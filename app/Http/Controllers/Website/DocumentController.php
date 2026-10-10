<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DocumentUpload;
use App\Models\CivilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Upload documents for a civil service request
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'civil_service_id' => 'required|exists:civil_services,id',
            'documents' => 'required|array|min:1',
            'documents.*.requirement_name' => 'required|string|max:255',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,bmp,doc,docx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $civilService = CivilService::findOrFail($request->civil_service_id);
        $uploadedDocuments = [];

        foreach ($request->documents as $documentData) {
            $file = $documentData['file'];
            $requirementName = $documentData['requirement_name'];

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;

            // Store file in storage/app/public/documents
            $filePath = $file->storeAs('documents', $filename, 'public');

            // Create document upload record
            $documentUpload = DocumentUpload::create([
                'civil_service_id' => $civilService->id,
                'requirement_name' => $requirementName,
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'pending',
            ]);

            $uploadedDocuments[] = $documentUpload;
        }

        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded successfully',
            'documents' => $uploadedDocuments,
        ]);
    }

    /**
     * Get documents for a civil service request
     */
    public function index(Request $request)
    {
        $civilServiceId = $request->get('civil_service_id');

        if (!$civilServiceId) {
            return response()->json(['error' => 'Civil service ID is required'], 400);
        }

        $civilService = CivilService::findOrFail($civilServiceId);
        $documents = $civilService->documentUploads()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'civil_service' => $civilService,
            'documents' => $documents,
        ]);
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
     * View a document (for images and PDFs)
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
     * Delete a document
     */
    public function destroy($documentId)
    {
        $document = DocumentUpload::findOrFail($documentId);

        // Check if document is already approved (cannot delete approved documents)
        if ($document->isApproved()) {
            return response()->json(['error' => 'Cannot delete approved documents'], 400);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete database record
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Get document status for a civil service request
     */
    public function status(Request $request)
    {
        $civilServiceId = $request->get('civil_service_id');

        if (!$civilServiceId) {
            return response()->json(['error' => 'Civil service ID is required'], 400);
        }

        $civilService = CivilService::findOrFail($civilServiceId);
        $documents = $civilService->documentUploads;

        $status = [
            'total_documents' => $documents->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'approved' => $documents->where('status', 'approved')->count(),
            'rejected' => $documents->where('status', 'rejected')->count(),
            'documents' => $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'requirement_name' => $doc->requirement_name,
                    'file_name' => $doc->file_name,
                    'status' => $doc->status,
                    'rejection_reason' => $doc->rejection_reason,
                    'reviewed_at' => $doc->reviewed_at,
                    'reviewed_by' => $doc->reviewed_by,
                ];
            }),
        ];

        return response()->json($status);
    }

    /**
     * Get required documents for a service type
     */
    public function getRequiredDocuments(Request $request)
    {
        $serviceType = $request->get('service_type');

        if (!$serviceType) {
            return response()->json(['error' => 'Service type is required'], 400);
        }

        // Define required documents based on service type
        $requiredDocuments = $this->getServiceRequirements($serviceType);

        return response()->json([
            'service_type' => $serviceType,
            'required_documents' => $requiredDocuments,
        ]);
    }

    /**
     * Get required documents for a specific service type
     */
    private function getServiceRequirements($serviceType)
    {
        $requirements = [
            'Certificate of Live Birth (On-time)' => [
                'Birth Certificate (Original)',
                'Valid ID of Requester',
                'Proof of Relationship',
            ],
            'Certificate of Live Birth (Late Registration)' => [
                'Birth Certificate (Original)',
                'Valid ID of Requester',
                'Proof of Relationship',
                'Affidavit of Late Registration',
                'Supporting Documents',
            ],
            'Certificate of Marriage' => [
                'Marriage Certificate (Original)',
                'Valid ID of Requester',
                'Proof of Relationship',
            ],
            'Certificate of Death' => [
                'Death Certificate (Original)',
                'Valid ID of Requester',
                'Proof of Relationship',
            ],
        ];

        return $requirements[$serviceType] ?? [
            'Valid ID',
            'Supporting Documents',
        ];
    }
}