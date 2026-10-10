<?php

namespace App\Http\Controllers;

use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    /**
     * Track a request by tracking number
     */
    public function track($trackingNumber)
    {
        try {
            // Try to find in CivilService first
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if ($civilService) {
                return response()->json([
                    'success' => true,
                    'data' => $civilService->getTrackingData(),
                    'message' => 'Tracking data retrieved successfully'
                ]);
            }

            // Try to find in OtherService
            $otherService = \App\Models\OtherService::byTracking($trackingNumber)->first();

            if ($otherService) {
                return response()->json([
                    'success' => true,
                    'data' => $otherService->getTrackingData(),
                    'message' => 'Tracking data retrieved successfully'
                ]);
            }

            // Try to find in Appointment (handle both APT formats)
            $appointment = Appointment::byTracking($trackingNumber)->first();

            if ($appointment) {
                return response()->json([
                    'success' => true,
                    'data' => $appointment->getTrackingData(),
                    'message' => 'Appointment tracking data retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tracking number not found',
            ], 404);

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
            // Try to find in CivilService first
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if ($civilService) {
                return response()->json([
                    'success' => true,
                    'status' => $civilService->status,
                    'updated_at' => $civilService->updated_at->toISOString(),
                    'requirements_checklist' => $civilService->requirements_checklist,
                    'remarks' => $civilService->admin_notes,
                    'message' => 'Status retrieved successfully'
                ]);
            }

            // Try to find in OtherService
            $otherService = \App\Models\OtherService::byTracking($trackingNumber)->first();

            if ($otherService) {
                return response()->json([
                    'success' => true,
                    'status' => $otherService->status,
                    'updated_at' => $otherService->updated_at->toISOString(),
                    'requirements_checklist' => null, // OtherService doesn't have requirements_checklist
                    'remarks' => $otherService->admin_notes,
                    'message' => 'Status retrieved successfully'
                ]);
            }

            // Try to find in Appointment
            $appointment = Appointment::byTracking($trackingNumber)->first();

            if ($appointment) {
                return response()->json([
                    'success' => true,
                    'status' => $appointment->status,
                    'updated_at' => $appointment->updated_at->toISOString(),
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'assigned_officer' => $appointment->assigned_officer,
                    'notes' => $appointment->notes,
                    'message' => 'Appointment status retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tracking number not found',
            ], 404);

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

            // Try to find in CivilService first
            $civilService = CivilService::byTracking($trackingNumber)->first();
            $serviceRequest = $civilService;

            // If not found in CivilService, try OtherService
            if (!$civilService) {
                $otherService = \App\Models\OtherService::byTracking($trackingNumber)->first();
                $serviceRequest = $otherService;
            }

            if (!$serviceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            // Use service_type (specific service) first, fall back to service_name (department) if not available
            $serviceForReceipt = $serviceRequest->service_type ?? $serviceRequest->service_name ?? 'Service Request';
            
            // Only include queue number if feature is enabled for the department and queue number exists
            $department = $serviceRequest->department ?? ($serviceRequest instanceof \App\Models\CivilService ? 'Civil Registry Office' : 'Other Services');
            $queueEnabled = \App\Models\Setting::getQueueNumberSettingForDepartment($department, false);
            $queueNumber = ($queueEnabled && $serviceRequest->queue_number) ? $serviceRequest->queue_number : null;
            
            $printData = [
                'name' => $serviceRequest->full_name,
                'service' => $serviceForReceipt,
                'tracking' => $serviceRequest->tracking_number,
                'queue' => $queueNumber,
                'date' => $serviceRequest->created_at->format('Y-m-d'),
                'time' => $serviceRequest->created_at->format('H:i:s'),
            ];

            // Call the print service
            $printResult = $this->printReceipt($printData);

            if ($printResult['success']) {
                // Update print status
                $serviceRequest->update(['print_status' => 'printed']);

                return response()->json([
                    'success' => true,
                    'message' => 'Receipt printed successfully',
                    'data' => $printData
                ]);
            } else {
                // Update print status
                $serviceRequest->update(['print_status' => 'failed']);

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
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if (!$civilService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found',
                ], 404);
            }

            // Determine request type: check service_name first to override incorrect database values
            $serviceName = $civilService->service_name ?? '';
            $dbRequestType = $civilService->request_type;
            
            // Priority 1: Check for "Delayed Birth Registration (Regular)" - SIMPLIFIED DETECTION
            if (stripos($serviceName, 'Delayed Birth Registration (Regular)') !== false || 
                stripos($serviceName, 'Delayed Birth Registration(Regular)') !== false) {
                $requestType = 'delayed_regular';
            }
            // Priority 2: Check if this is Delayed Regular Non-BRAP (has Delayed and Non-BRAP)
            elseif (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) {
                $requestType = 'delayed_regular';
            }
            // Priority 3: Check if this is Delayed BRAP (must have Delayed and BRAP, but NOT Non-BRAP)
            elseif (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) {
                $requestType = 'delayed_brap';
            }
            // Priority 4: If service_name clearly indicates Non-BRAP (without Delayed), force it to 'regular'
            elseif (stripos($serviceName, 'Non-BRAP') !== false || stripos($serviceName, 'Non BRAP') !== false) {
                $requestType = 'regular';
            }
            // Priority 5: If service_name contains BRAP (but not Non-BRAP), it's BRAP
            elseif (stripos($serviceName, 'BRAP') !== false) {
                $requestType = 'brap';
            }
            // Priority 6: Use database value if set
            elseif ($dbRequestType && $dbRequestType !== '') {
                $requestType = $dbRequestType;
            }
            // Priority 7: Default to 'regular'
            else {
                $requestType = 'regular';
            }
            
            $allowedStatuses = CivilService::statusFlow($requestType);

            $validated = $request->validate([
                'status' => ['required', 'string', Rule::in($allowedStatuses)],
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            if (!CivilService::canTransition($civilService->status, $validated['status'], $requestType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status transition.',
                ], 422);
            }

            $civilService->updateStatus(
                $validated['status'],
                'admin',
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

            // Generate queue number only if feature is enabled for Civil Registry Office
            $queueNumber = null;
            if (\App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false)) {
                $queueNumber = CivilService::getNextQueueNumber();
            }

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
            $payload = $data;

            if (Setting::getBoolean('feature.receipt_enhancements', true)) {
                $payload['fontFamily'] = config('services.print.font_family', 'Roboto');
            }

            // Only include queue number if feature is enabled for the department
            // For receipt printing, determine department from service context
            // Default to Civil Registry Office for most cases
            $department = $data['department'] ?? 'Civil Registry Office';
            if (!Setting::getQueueNumberSettingForDepartment($department, false)) {
                $payload['queue'] = null;
            }

            $response = Http::timeout(10)->post('http://localhost:4000/print', $payload);

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

            // Generate queue number only if feature is enabled for Civil Registry Office
            $queueNumber = null;
            if (\App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false)) {
                $queueNumber = CivilService::getNextQueueNumber();
            }

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

    /**
     * Show service types selection page
     */
    public function showServiceTypes(Request $request)
    {
        $serviceName = $request->get('service', 'Service Request');
        $department = $request->get('department', 'Municipal Office');

        // Get the service and its types from database
        $service = \App\Models\Service::where('name', $serviceName)
            ->where('is_active', true)
            ->first();

        $serviceTypes = collect();
        if ($service) {
            $serviceTypes = $service->types()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        }

        return view('services.types', compact('serviceName', 'department', 'service', 'serviceTypes'));
    }

    public function showRequirements(Request $request)
    {
        $serviceName = $request->get('service', 'Service Request');
        $department = $request->get('department', 'Municipal Office');
        $selectedType = $request->get('selected_type', '');


        // Get the service type and its steps from database
        $service = \App\Models\Service::where('name', $serviceName)
            ->where('is_active', true)
            ->first();

        $serviceType = null;
        $steps = collect();

        if ($service && $selectedType) {
            $serviceType = $service->types()
                ->where('name', $selectedType)
                ->where('is_active', true)
                ->first();

            if ($serviceType) {
                $steps = $serviceType->steps()
                    ->orderBy('display_order')
                    ->get();
            }
        }

        // For prioritized services, if no steps found, create default steps
        if ($steps->isEmpty() && $service && $service->name === 'Prioritized Civil Services') {
            $steps = $this->getDefaultBrapSteps($selectedType);
        }

        // Additional fallback: if still no steps, try to find by service type name directly
        if ($steps->isEmpty()) {
            $serviceType = \App\Models\ServiceType::where('name', $selectedType)
                ->where('is_active', true)
                ->first();

            if ($serviceType) {
                $steps = $serviceType->steps()
                    ->orderBy('display_order')
                    ->get();
            }
        }

        // Final fallback: create default steps based on selected type
        if ($steps->isEmpty()) {
            $steps = $this->getDefaultBrapSteps($selectedType);
        }


        return view('services.requirements', compact(
            'serviceName',
            'department',
            'service',
            'serviceType',
            'steps'
        ))->with('serviceTypeName', $selectedType);
    }

    /**
     * Get default steps for BRAP services if none exist in database
     */
    private function getDefaultBrapSteps($selectedType)
    {
        if (strpos($selectedType, 'Delayed Regular Non-BRAP') !== false) {
            return collect([
                (object) [
                    'title' => 'Step 1: Fill out basic request information',
                    'content' => 'Provide your full name and contact number to start the process.',
                    'display_order' => 1
                ],
                (object) [
                    'title' => 'Step 2: Submit request and receive tracking number',
                    'content' => 'Your request will be submitted and you will receive a tracking number for monitoring.',
                    'display_order' => 2
                ],
                (object) [
                    'title' => 'Step 3: Wait for officer to provide checklist of requirements',
                    'content' => 'An officer will review your request and provide a detailed checklist of required documents.',
                    'display_order' => 3
                ],
                (object) [
                    'title' => 'Step 4: Submit required documents for processing',
                    'content' => 'Gather and submit all required documents as specified in the checklist.',
                    'display_order' => 4
                ],
                (object) [
                    'title' => 'Step 5: Request will be accepted after 10 business days',
                    'content' => 'Your request will be processed and accepted after the standard processing period.',
                    'display_order' => 5
                ]
            ]);
        } elseif (strpos($selectedType, 'Delayed BRAP') !== false) {
            return collect([
                (object) [
                    'title' => 'Step 1: Fill out basic request information',
                    'content' => 'Provide your full name and contact number to start the BRAP process.',
                    'display_order' => 1
                ],
                (object) [
                    'title' => 'Step 2: Submit request and receive tracking number',
                    'content' => 'Your BRAP request will be submitted and you will receive a tracking number.',
                    'display_order' => 2
                ],
                (object) [
                    'title' => 'Step 3: Request will be encoded to BRAP Request List',
                    'content' => 'Your request will be added to the official BRAP request tracking system.',
                    'display_order' => 3
                ],
                (object) [
                    'title' => 'Step 4: Officer will provide checklist of requirements',
                    'content' => 'A BRAP officer will provide you with specific requirements for the assistance project.',
                    'display_order' => 4
                ],
                (object) [
                    'title' => 'Step 5: Submit documents and wait for BRAP processing result',
                    'content' => 'Submit all required documents and wait for the BRAP processing to complete.',
                    'display_order' => 5
                ]
            ]);
        }

        return collect();
    }

    /**
     * Show the simplified kiosk form
     */
    public function showKioskForm(Request $request)
    {
        $serviceName = $request->get('service', 'Service Request');
        $department = $request->get('department', 'Municipal Office');
        $serviceType = $request->get('type', 'regular');
        $selectedType = $request->get('selected_type', '');

        return view('services.kiosk', compact('serviceName', 'department', 'serviceType', 'selectedType'));
    }

    /**
     * Handle simplified kiosk form submission
     */
    public function kioskSubmit(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Kiosk submit request received', [
                'all_data' => $request->all(),
                'department' => $request->input('department'),
                'service_name' => $request->input('service_name'),
                'service_type' => $request->input('service_type')
            ]);

            // Validate the simplified request
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'service_name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'service_type' => 'required|string',
                'selected_type' => 'nullable|string|max:255',
            ]);

            // Generate tracking number
            $trackingNumber = $this->generateTrackingNumber();

            // Determine which model to use based on department and service type
            $isCivilRegistry = $validated['department'] === 'Civil Registry Office';
            
            // Check service_name to determine if it's Delayed Regular or Delayed BRAP
            $serviceName = $validated['service_name'] ?? '';
            $hasDelayed = stripos($serviceName, 'Delayed') !== false;
            $hasNonBrap = stripos($serviceName, 'Non-BRAP') !== false || stripos($serviceName, 'Non BRAP') !== false;
            $hasBrap = stripos($serviceName, 'BRAP') !== false;
            
            // Determine request type
            $isDelayedRegular = $hasDelayed && $hasNonBrap;
            $isDelayedBrap = $hasDelayed && $hasBrap && !$hasNonBrap;
            $isBrapService = $validated['service_type'] === 'brap' || $isDelayedBrap || ($hasBrap && !$hasNonBrap);

            \Log::info('Kiosk routing decision', [
                'isCivilRegistry' => $isCivilRegistry,
                'isBrapService' => $isBrapService,
                'isDelayedRegular' => $isDelayedRegular,
                'isDelayedBrap' => $isDelayedBrap,
                'department' => $validated['department'],
                'service_name' => $serviceName,
                'service_type' => $validated['service_type']
            ]);

            // Use database transaction to ensure request is saved even if SMS/print fails
            $serviceRequest = \DB::transaction(function () use ($validated, $trackingNumber, $isCivilRegistry, $isBrapService, $isDelayedBrap, $isDelayedRegular, $serviceName) {
                if ($isCivilRegistry || $isBrapService) {
                // For Civil Registry services (both regular and BRAP), use CivilService model
                // Queue number is only generated if feature is enabled for Civil Registry Office
                $queueNumber = null;
                if (\App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false)) {
                    $queueNumber = CivilService::getNextQueueNumber();
                }

                // Determine correct request_type based on service name
                $requestType = 'regular';
                if ($isDelayedBrap) {
                    $requestType = 'brap'; // Delayed BRAP uses 'brap' request_type
                } elseif ($isDelayedRegular) {
                    $requestType = 'regular'; // Delayed Regular uses 'regular' request_type
                } elseif ($isBrapService) {
                    $requestType = 'brap';
                }

                $requestData = [
                    'full_name' => $validated['full_name'],
                    'contact_number' => $validated['contact_number'],
                    'service_name' => $validated['service_name'],
                    'department' => $validated['department'],
                    'tracking_number' => $trackingNumber,
                    'queue_number' => $queueNumber,
                    'request_type' => $requestType,
                    'status' => 'submitted', // Initial status - first status in the flow
                    'service_type' => $validated['selected_type'] ?? ($isBrapService ? 'BRAP Service' : 'Regular Service'),
                ];

                // Validate status flow before creating - use service_name for correct flow detection
                $flowRequestType = $isDelayedBrap ? 'delayed_brap' : ($isDelayedRegular ? 'delayed_regular' : $requestType);
                if (!CivilService::canTransition(null, 'submitted', $flowRequestType, $serviceName)) {
                    throw new \Exception('Invalid initial status for this request type');
                }

                $serviceRequest = CivilService::create($requestData);
                
                // Automatically transition to 'inquiry' status after creation
                if (CivilService::canTransition('submitted', 'inquiry', $flowRequestType, $serviceName)) {
                    $serviceRequest->update(['status' => 'inquiry']);
                    
                    // Create initial status update for 'inquiry' status
                    \App\Models\StatusUpdate::create([
                        'statusable_id' => $serviceRequest->id,
                        'statusable_type' => \App\Models\CivilService::class,
                        'status' => 'inquiry',
                        'status_date' => now()->toDateString(),
                        'remarks' => 'Request submitted and received',
                    ]);
                }
                
                \Log::info('Created CivilService request', ['id' => $serviceRequest->id, 'tracking_number' => $trackingNumber]);
                return $serviceRequest;
            } else {
                // For other departments, use OtherService model - WITH QUEUE NUMBER (if feature enabled for that department)
                // Queue number is only generated if feature is enabled for the specific department
                $queueNumber = null;
                $department = $validated['department'] ?? 'Other Services';
                if (\App\Models\Setting::getQueueNumberSettingForDepartment($department, false)) {
                    $queueNumber = $this->generateQueueNumber();
                }

                $requestData = [
                    'full_name' => $validated['full_name'],
                    'contact_number' => $validated['contact_number'],
                    'service_name' => $validated['service_name'],
                    'department' => $validated['department'],
                    'tracking_number' => $trackingNumber,
                    'queue_number' => $queueNumber,
                    'request_type' => 'regular',
                    'status' => 'inquiry', // Initial status - first status in the flow for OtherService
                    'service_type' => !empty($validated['selected_type']) ? $validated['selected_type'] : ($validated['service_name'] ?? 'Regular Service'),
                ];

                // Validate status flow before creating
                if (!OtherService::canTransition(null, 'inquiry')) {
                    throw new \Exception('Invalid initial status for this request type');
                }

                $serviceRequest = OtherService::create($requestData);
                
                // Create initial status update for 'inquiry' status
                \App\Models\StatusUpdate::create([
                    'statusable_id' => $serviceRequest->id,
                    'statusable_type' => \App\Models\OtherService::class,
                    'status' => 'inquiry',
                    'status_date' => now()->toDateString(),
                    'remarks' => 'Request submitted and received',
                ]);
                
                \Log::info('Created OtherService request', ['id' => $serviceRequest->id, 'tracking_number' => $trackingNumber]);
                return $serviceRequest;
            }
            });

            // Refresh the model to ensure we have the latest data
            $serviceRequest->refresh();

            // Send SMS inquiry message automatically if status is 'inquiry'
            if ($serviceRequest->status === 'inquiry') {
                try {
                    $notificationService = new \App\Services\NotificationService();
                    $smsResult = $notificationService->notifyClientViaSMS($serviceRequest, 'inquiry');
                    \Log::info('SMS inquiry message sent', [
                        'tracking_number' => $trackingNumber,
                        'status' => $serviceRequest->status,
                        'contact_number' => $serviceRequest->contact_number,
                        'sms_result' => $smsResult
                    ]);
                } catch (\Exception $e) {
                    // Log SMS failure but don't fail the request
                    \Log::warning('Failed to send SMS inquiry message', [
                        'tracking_number' => $trackingNumber,
                        'status' => $serviceRequest->status,
                        'contact_number' => $serviceRequest->contact_number,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::warning('SMS inquiry not sent - status is not inquiry', [
                    'tracking_number' => $trackingNumber,
                    'status' => $serviceRequest->status
                ]);
            }

            // Get the final queue number from the service request (may have been set for Civil Services too)
            $finalQueueNumber = $serviceRequest->queue_number ?? $queueNumber ?? null;

            // Print receipt using existing print service
            // Use service_type from the database (specific service like "Pet Registration") instead of service_name (department name)
            // The service_type is stored in the database when the request is created, so use that
            // Check if selected_type is not empty, otherwise use the stored service_type from database
            $selectedType = !empty($validated['selected_type']) ? $validated['selected_type'] : null;
            $serviceTypeForReceipt = $serviceRequest->service_type ?? $selectedType ?? $validated['service_name'] ?? 'Service Request';
            
            // Get department from service request or validated data
            $departmentForPrint = $serviceRequest->department ?? $validated['department'] ?? 'Other Services';
            $this->printKioskReceipt($trackingNumber, $validated['full_name'], $finalQueueNumber, $serviceTypeForReceipt, $departmentForPrint);

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully',
                'data' => [
                    'tracking_number' => $trackingNumber,
                    'queue_number' => $finalQueueNumber,
                    'full_name' => $validated['full_name'],
                    'contact_number' => $validated['contact_number'],
                    'service_name' => $validated['service_name'],
                    'service_type' => $validated['selected_type'] ?? $validated['service_type'] ?? 'Regular Service',
                    'department' => $validated['department'],
                    'status' => $serviceRequest->status // Use the actual status (should be 'inquiry' after auto-transition)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Kiosk submit failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique tracking number
     */
    private function generateTrackingNumber()
    {
        do {
            $trackingNumber = 'C2S-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (
            CivilService::where('tracking_number', $trackingNumber)->exists() ||
            \App\Models\OtherService::where('tracking_number', $trackingNumber)->exists()
        );

        return $trackingNumber;
    }

    /**
     * Generate a unique queue number (for Other Services only)
     */
    private function generateQueueNumber()
    {
        $today = date('Y-m-d');
        // Only consider Other Services for queue numbering
        $otherQueue = OtherService::whereDate('created_at', $today)->max('queue_number') ?? 0;

        return (int) $otherQueue + 1;
    }

    /**
     * Print receipt using the print service
     */
    private function printKioskReceipt($trackingNumber, $fullName, $queueNumber, $serviceName, $department = null)
    {
        try {
            // Determine department - use provided department or default to Civil Registry Office
            $department = $department ?? 'Civil Registry Office';
            
            // Only include queue number if feature is enabled for the department and queue number exists
            $queueEnabled = \App\Models\Setting::getQueueNumberSettingForDepartment($department, false);
            
            $printData = [
                'name' => $fullName,
                'service' => $serviceName,
                'tracking' => $trackingNumber,
                'queue' => ($queueEnabled && $queueNumber) ? $queueNumber : null,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ];

            // Call the print service
            Http::timeout(10)->post('http://localhost:4000/print', $printData);

            Log::info('Receipt printed successfully', [
                'tracking_number' => $trackingNumber,
                'full_name' => $fullName,
                'queue_number' => $queueNumber,
                'department' => $department,
                'queue_enabled' => $queueEnabled
            ]);

        } catch (\Exception $e) {
            Log::error('Print service failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage()
            ]);
        }
    }
}
