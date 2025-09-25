<?php

namespace App\Http\Controllers;

use App\Models\CivilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    /**
     * Track a request by tracking number
     */
    public function track($trackingNumber)
    {
        try {
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if (!$civilService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $civilService->getTrackingData(),
                'message' => 'Tracking data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Track request failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve tracking information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get current status of a request
     */
    public function getStatus($trackingNumber)
    {
        try {
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if (!$civilService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $civilService->status,
                'updated_at' => $civilService->updated_at->toISOString(),
                'message' => 'Status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Get status failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Print receipt for a specific tracking number
     */
    public function printReceiptByTracking(Request $request)
    {
        try {
            $validated = $request->validate([
                'tracking_number' => 'required|string'
            ]);

            $trackingNumber = $validated['tracking_number'];
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if (!$civilService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            $printData = [
                'name' => $civilService->full_name,
                'service' => $civilService->service_type,
                'tracking' => $civilService->tracking_number,
                'queue_no' => $civilService->queue_number,
                'date' => $civilService->created_at->format('Y-m-d'),
                'time' => $civilService->created_at->format('H:i:s'),
            ];

            // Call the print service
            $printResult = $this->printReceipt($printData);

            if ($printResult['success']) {
                // Update print status
                $civilService->update(['print_status' => 'printed']);

                return response()->json([
                    'success' => true,
                    'message' => 'Receipt printed successfully',
                    'data' => $printData
                ]);
            } else {
                // Update print status
                $civilService->update(['print_status' => 'failed']);

                return response()->json([
                    'success' => false,
                    'message' => 'Print failed: ' . ($printResult['error'] ?? 'Unknown error'),
                    'data' => $printData
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Print receipt failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to print receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update request status (for admin use)
     */
    public function updateStatus(Request $request, $trackingNumber)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:submitted,under_review,processing,ready_for_pickup,completed,rejected',
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            $civilService = CivilService::byTracking($trackingNumber)->first();

            if (!$civilService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            // Update status with admin notes
            $civilService->updateStatus(
                $validated['status'],
                'admin', // In real app, get from authenticated user
                $validated['admin_notes'] ?? null
            );

            Log::info('Status updated', [
                'tracking_number' => $trackingNumber,
                'new_status' => $validated['status'],
                'updated_by' => 'admin',
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'tracking_number' => $trackingNumber,
                'status' => $validated['status'],
                'updated_at' => $civilService->updated_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Update status failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store a new service request and print receipt
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'document_type' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'contact_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'additional_info' => 'nullable|string|max:1000',
                'child_info' => 'nullable|array',
                'mother_info' => 'nullable|array',
                'father_info' => 'nullable|array',
                'contact_info' => 'nullable|array',
            ]);

            // Generate tracking number
            $tracking = CivilService::generateTrackingNumber();

            // Generate queue number
            $queueNumber = CivilService::getNextQueueNumber();

            // Create civil service record
            $civilService = CivilService::create([
                'tracking_number' => $tracking,
                'queue_number' => $queueNumber,
                'service_type' => $validated['document_type'],
                'status' => 'submitted',
                'full_name' => $validated['full_name'],
                'contact_number' => $validated['contact_number'] ?? $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'additional_info' => $validated['additional_info'],
                'child_info' => $validated['child_info'] ?? null,
                'mother_info' => $validated['mother_info'] ?? null,
                'father_info' => $validated['father_info'] ?? null,
                'contact_info' => $validated['contact_info'] ?? null,
                'print_status' => 'pending',
            ]);

            // Prepare data for printing
            $printData = [
                'name' => $validated['full_name'],
                'service' => $validated['document_type'],
                'tracking' => $tracking,
                'queue_no' => $queueNumber,
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s'),
            ];

            // Call the print service
            $printResult = $this->printReceipt($printData);

            if ($printResult['success']) {
                // Update print status
                $civilService->update(['print_status' => 'printed']);

                // Log successful request
                Log::info('Service request processed successfully', [
                    'tracking' => $tracking,
                    'queue_no' => $queueNumber,
                    'name' => $validated['full_name'],
                    'service' => $validated['document_type'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Request submitted successfully!',
                    'tracking_number' => $tracking,
                    'queue_number' => $queueNumber,
                    'print_status' => 'Printed successfully',
                    'data' => $printData
                ]);
            } else {
                // Update print status
                $civilService->update(['print_status' => 'failed']);

                // Log print failure but still process the request
                Log::warning('Print service failed but request processed', [
                    'tracking' => $tracking,
                    'queue_no' => $queueNumber,
                    'print_error' => $printResult['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Request submitted successfully! (Print service unavailable)',
                    'tracking_number' => $tracking,
                    'queue_number' => $queueNumber,
                    'print_status' => 'Print failed: ' . ($printResult['error'] ?? 'Service unavailable'),
                    'data' => $printData
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Service request failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process request. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Print receipt using the thermal printer service
     */
    private function printReceipt(array $data)
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:4000/print', $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                return [
                    'success' => false,
                    'error' => 'Print service returned error: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Print service unavailable: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Test the print service connection
     */
    public function testPrint()
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:4000/test-print');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Print service is working correctly',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Print service returned error',
                    'error' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Print service is not available',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check print service health
     */
    public function printServiceHealth()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:4000/health');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Print service is healthy',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Print service health check failed',
                    'error' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Print service is not responding',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle detailed COLB form submission
     */
    public function storeDetailed(Request $request)
    {
        try {
            // Validate the detailed request
            $validated = $request->validate([
                'child_first_name' => 'required|string|max:255',
                'child_last_name' => 'required|string|max:255',
                'child_middle_name' => 'nullable|string|max:255',
                'child_birth_date' => 'required|date',
                'place_of_birth' => 'required|string|max:255',
                'child_sex' => 'required|string|in:Male,Female',
                'mother_first_name' => 'required|string|max:255',
                'mother_last_name' => 'required|string|max:255',
                'mother_middle_name' => 'nullable|string|max:255',
                'father_first_name' => 'nullable|string|max:255',
                'father_last_name' => 'nullable|string|max:255',
                'father_middle_name' => 'nullable|string|max:255',
                'contact_person' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'contact_address' => 'required|string|max:500',
            ]);

            // Generate tracking number
            $tracking = CivilService::generateTrackingNumber();

            // Generate queue number
            $queueNumber = CivilService::getNextQueueNumber();

            // Combine names for full name
            $childName = trim($validated['child_first_name'] . ' ' . $validated['child_middle_name'] . ' ' . $validated['child_last_name']);

            // Prepare detailed information arrays
            $childInfo = [
                'name' => $childName,
                'birth_date' => $validated['child_birth_date'],
                'place_of_birth' => $validated['place_of_birth'],
                'sex' => $validated['child_sex'],
            ];

            $motherInfo = [
                'name' => trim($validated['mother_first_name'] . ' ' . $validated['mother_middle_name'] . ' ' . $validated['mother_last_name']),
            ];

            $fatherInfo = [
                'name' => trim($validated['father_first_name'] . ' ' . $validated['father_middle_name'] . ' ' . $validated['father_last_name']),
            ];

            $contactInfo = [
                'person' => $validated['contact_person'],
                'number' => $validated['contact_number'],
                'email' => $validated['contact_email'],
                'address' => $validated['contact_address'],
            ];

            // Create civil service record
            $civilService = CivilService::create([
                'tracking_number' => $tracking,
                'queue_number' => $queueNumber,
                'service_type' => 'Certificate of Live Birth (On-time)',
                'status' => 'submitted',
                'full_name' => $childName,
                'contact_number' => $validated['contact_number'],
                'email' => $validated['contact_email'],
                'address' => $validated['contact_address'],
                'child_info' => $childInfo,
                'mother_info' => $motherInfo,
                'father_info' => $fatherInfo,
                'contact_info' => $contactInfo,
                'print_status' => 'pending',
            ]);

            // Prepare data for printing
            $printData = [
                'name' => $childName,
                'service' => 'Certificate of Live Birth (On-time)',
                'tracking' => $tracking,
                'queue_no' => $queueNumber,
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s'),
            ];

            // Call the print service
            $printResult = $this->printReceipt($printData);

            if ($printResult['success']) {
                // Update print status
                $civilService->update(['print_status' => 'printed']);

                // Log successful request
                Log::info('Detailed COLB request processed successfully', [
                    'tracking' => $tracking,
                    'queue_no' => $queueNumber,
                    'child_name' => $childName,
                    'service' => 'Certificate of Live Birth (On-time)',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Detailed request submitted successfully!',
                    'tracking_number' => $tracking,
                    'queue_number' => $queueNumber,
                    'print_status' => 'Printed successfully',
                    'data' => $printData
                ]);
            } else {
                // Update print status
                $civilService->update(['print_status' => 'failed']);

                // Log print failure but still process the request
                Log::warning('Print service failed but detailed request processed', [
                    'tracking' => $tracking,
                    'queue_no' => $queueNumber,
                    'print_error' => $printResult['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Detailed request submitted successfully! (Print service unavailable)',
                    'tracking_number' => $tracking,
                    'queue_number' => $queueNumber,
                    'print_status' => 'Print failed: ' . ($printResult['error'] ?? 'Service unavailable'),
                    'data' => $printData
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Detailed COLB request failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process detailed request. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
