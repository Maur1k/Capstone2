<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentUpload;
use App\Models\CivilService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\NodeSmsService;

class DocumentManagementController extends Controller
{
    /**
     * Display documents for officers
     */
    public function index(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        \Log::info('DocumentManagementController::index called', [
            'officer' => $officer->name,
            'department' => $officer->department,
            'role' => $officer->role
        ]);

        $query = DocumentUpload::with(['civilService', 'appointment']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by requirement if provided
        if ($request->has('requirement')) {
            $query->where('requirement_name', 'like', '%' . $request->requirement . '%');
        }

        // Filter by civil service if provided
        if ($request->has('civil_service_id')) {
            $query->where('civil_service_id', $request->civil_service_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        \Log::info('DocumentManagementController::index results', [
            'total_documents' => $documents->total(),
            'documents_count' => $documents->count(),
            'documents_data' => $documents->items()
        ]);

        return response()->json([
            'documents' => $documents,
            'officer' => $officer,
        ]);
    }

    /**
     * Get document statistics
     */
    public function statistics()
    {
        $officer = Auth::guard('admin')->user();

        $query = DocumentUpload::query();

        $stats = [
            'total_documents' => $query->count(),
            'pending_documents' => $query->where('status', 'pending')->count(),
            'approved_documents' => $query->where('status', 'approved')->count(),
            'rejected_documents' => $query->where('status', 'rejected')->count(),
            'today_uploads' => $query->whereDate('created_at', now())->count(),
            'pending_review' => $query->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Approve a document
     */
    public function approve(Request $request, $documentId)
    {
        $document = DocumentUpload::findOrFail($documentId);
        $officer = Auth::guard('admin')->user();

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Only pending documents can be approved'], 400);
        }

        $document->approve($officer->name);

        return response()->json([
            'success' => true,
            'message' => 'Document approved successfully',
            'document' => $document,
        ]);
    }

    /**
     * Reject a document
     */
    public function reject(Request $request, $documentId)
    {
        $validator = $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'send_sms' => 'nullable|boolean',
        ]);

        $document = DocumentUpload::findOrFail($documentId);
        $officer = Auth::guard('admin')->user();

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Only pending documents can be rejected'], 400);
        }

        $document->reject($validator['rejection_reason'], $officer->name);

        // Prepare response
        $response = [
            'success' => true,
            'message' => 'Document rejected successfully',
            'document' => $document,
        ];

        // Send SMS if requested
        $sendSMS = $request->boolean('send_sms', false);
        if ($sendSMS) {
            try {
                // Get the related appointment or civil service to get contact number
                $contactNumber = null;
                $clientName = null;
                
                if ($document->appointment_id) {
                    $appointment = $document->appointment;
                    if ($appointment) {
                        $contactNumber = $appointment->contact_number;
                        $clientName = $appointment->full_name;
                    }
                } elseif ($document->civil_service_id) {
                    $civilService = $document->civilService;
                    if ($civilService) {
                        $contactNumber = $civilService->contact_number;
                        $clientName = $civilService->full_name;
                    }
                }

                if ($contactNumber) {
                    // Create SMS message
                    $smsMessage = "Dear {$clientName},\n\n";
                    $smsMessage .= "Your document '{$document->file_name}' has been rejected.\n\n";
                    $smsMessage .= "Reason: {$validator['rejection_reason']}\n\n";
                    $smsMessage .= "Please upload a new document or contact us for assistance.\n\n";
                    $smsMessage .= "Thank you.";

                    // Send SMS using NodeSmsService
                    $nodeSmsService = new NodeSmsService();
                    $smsResult = $nodeSmsService->sendSMSDirect($contactNumber, $smsMessage);
                    
                    if ($smsResult['success'] ?? false) {
                        $response['sms_sent'] = true;
                        $response['sms_message_id'] = $smsResult['message_id'] ?? null;
                        $response['sms_network'] = $smsResult['network'] ?? null;
                        $response['message'] = 'Document rejected and SMS notification sent successfully.';
                        
                        Log::info('Document rejection SMS sent successfully', [
                            'document_id' => $documentId,
                            'contact_number' => $contactNumber,
                            'message_id' => $smsResult['message_id'] ?? null
                        ]);
                    } else {
                        $response['sms_sent'] = false;
                        $response['sms_error'] = $smsResult['error'] ?? $smsResult['message'] ?? 'SMS sending failed';
                        $response['message'] = 'Document rejected successfully, but SMS notification failed.';
                        
                        Log::warning('Document rejection SMS failed', [
                            'document_id' => $documentId,
                            'contact_number' => $contactNumber,
                            'error' => $smsResult['error'] ?? $smsResult['message'] ?? 'Unknown error'
                        ]);
                    }
                } else {
                    $response['sms_sent'] = false;
                    $response['sms_error'] = 'Client contact number not available';
                    $response['message'] = 'Document rejected successfully, but SMS could not be sent (no contact number).';
                    
                    Log::warning('Document rejection SMS skipped - no contact number', [
                        'document_id' => $documentId,
                        'appointment_id' => $document->appointment_id,
                        'civil_service_id' => $document->civil_service_id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending SMS for document rejection', [
                    'document_id' => $documentId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $response['sms_sent'] = false;
                $response['sms_error'] = $e->getMessage();
                $response['message'] = 'Document rejected successfully, but SMS notification failed.';
            }
        } else {
            $response['sms_sent'] = false;
            $response['sms_skipped'] = true;
        }

        return response()->json($response);
    }

    /**
     * Download a document
     */
    public function download($documentId)
    {
        $document = DocumentUpload::findOrFail($documentId);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * View a document
     */
    public function view($documentId)
    {
        $document = DocumentUpload::findOrFail($documentId);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => $document->file_type,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }

    /**
     * Get documents for a specific civil service
     */
    public function getCivilServiceDocuments($civilServiceId)
    {
        $civilService = CivilService::findOrFail($civilServiceId);
        $documents = $civilService->documentUploads()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'civil_service' => $civilService,
            'documents' => $documents,
        ]);
    }

    /**
     * Bulk approve documents
     */
    public function bulkApprove(Request $request)
    {
        $validator = $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:document_uploads,id',
        ]);

        $officer = Auth::guard('admin')->user();
        $approvedCount = 0;

        foreach ($validator['document_ids'] as $documentId) {
            $document = DocumentUpload::findOrFail($documentId);

            if ($document->status === 'pending') {
                $document->approve($officer->name);
                $approvedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$approvedCount} documents",
            'approved_count' => $approvedCount,
        ]);
    }

    /**
     * Bulk reject documents
     */
    public function bulkReject(Request $request)
    {
        $validator = $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:document_uploads,id',
            'rejection_reason' => 'required|string|max:500',
        ]);

        $officer = Auth::guard('admin')->user();
        $rejectedCount = 0;

        foreach ($validator['document_ids'] as $documentId) {
            $document = DocumentUpload::findOrFail($documentId);

            if ($document->status === 'pending') {
                $document->reject($validator['rejection_reason'], $officer->name);
                $rejectedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully rejected {$rejectedCount} documents",
            'rejected_count' => $rejectedCount,
        ]);
    }

    /**
     * Get document details
     */
    public function show($documentId)
    {
        $document = DocumentUpload::with('civilService')->findOrFail($documentId);

        return response()->json([
            'document' => $document,
            'civil_service' => $document->civilService,
        ]);
    }

    /**
     * Manually upload a supporting document for a request
     */
    public function upload(Request $request)
    {
        if (!Setting::getBoolean('feature.manual_document_uploads', false)) {
            abort(403, 'Manual document uploads are currently disabled.');
        }

        $validated = $request->validate([
            'tracking_number' => ['required', 'string'],
            'requirement_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $civilService = CivilService::where('tracking_number', $validated['tracking_number'])->first();

        if (!$civilService) {
            return back()->withErrors([
                'tracking_number' => 'Tracking number not found or not eligible for manual uploads.',
            ]);
        }

        $file = $request->file('document');
        $path = $file->store('documents/manual', 'public');

        DocumentUpload::create([
            'civil_service_id' => $civilService->id,
            'requirement_name' => $validated['requirement_name'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return back()->with('status', 'Supporting document uploaded and queued for review.');
    }

    /**
     * Get pending documents count for notifications
     */
    public function pendingCount()
    {
        $count = DocumentUpload::where('status', 'pending')->count();

        return response()->json([
            'pending_count' => $count,
        ]);
    }
}