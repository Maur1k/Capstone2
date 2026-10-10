<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TrackingController extends Controller
{
    /**
     * Show the tracking form
     */
    public function index()
    {
        return view('website.reference_code');
    }

    /**
     * Track a request by tracking number
     */
    public function track(Request $request)
    {
        $trackingNumber = $request->input('reference_code');

        if (!$trackingNumber) {
            return redirect()->back()->with('error', 'Please enter a tracking number.');
        }

        try {
            // Try to find in CivilService first
            $civilService = CivilService::byTracking($trackingNumber)->first();

            if ($civilService) {
                $data = $civilService->getTrackingData();
                return view('website.tracking-result', compact('data', 'trackingNumber'));
            }

            // Try to find in OtherService
            $otherService = OtherService::byTracking($trackingNumber)->first();

            if ($otherService) {
                $data = $otherService->getTrackingData();
                return view('website.tracking-result', compact('data', 'trackingNumber'));
            }

            // Try to find in Appointment (handle both APT formats)
            $appointment = Appointment::byTracking($trackingNumber)->first();

            if ($appointment) {
                $data = $appointment->getTrackingData();
                return view('website.tracking-result', compact('data', 'trackingNumber'));
            }

            return redirect()->back()->with('error', 'Tracking number not found.');

        } catch (\Exception $e) {
            Log::error('Track request failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Unable to retrieve tracking information.');
        }
    }

    /**
     * Get tracking data via AJAX
     */
    public function getTrackingData($trackingNumber)
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
            $otherService = OtherService::byTracking($trackingNumber)->first();

            if ($otherService) {
                return response()->json([
                    'success' => true,
                    'data' => $otherService->getTrackingData(),
                    'message' => 'Tracking data retrieved successfully'
                ]);
            }

            // Try to find in Appointment
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
                    'updated_at' => $civilService->updated_at,
                    'requirements_checklist' => $civilService->requirements_checklist
                ]);
            }

            // Try to find in OtherService
            $otherService = OtherService::byTracking($trackingNumber)->first();

            if ($otherService) {
                return response()->json([
                    'success' => true,
                    'status' => $otherService->status,
                    'updated_at' => $otherService->updated_at,
                    'requirements_checklist' => $otherService->requirements_checklist
                ]);
            }

            // Try to find in Appointment
            $appointment = Appointment::byTracking($trackingNumber)->first();

            if ($appointment) {
                return response()->json([
                    'success' => true,
                    'status' => $appointment->status,
                    'updated_at' => $appointment->updated_at,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'assigned_officer' => $appointment->assigned_officer,
                    'notes' => $appointment->notes
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
                'message' => 'Unable to retrieve status information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print receipt for a tracked request
     */
    public function printReceipt(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'name' => 'required|string',
            'service' => 'required|string',
            'date' => 'nullable|string',
            'time' => 'nullable|string',
            'queue' => 'nullable|string',
        ]);

        try {
            $payload = [
                'name' => $request->name,
                'service' => $request->service,
                'tracking' => $request->tracking_number,
                'date' => $request->date,
                'time' => $request->time,
                'queue' => $request->queue,
            ];

            // Add font family if receipt enhancements are enabled
            if (Setting::getBoolean('feature.receipt_enhancements', true)) {
                $payload['fontFamily'] = config('services.print.font_family', 'Roboto');
            }

            // Only include queue number if feature is enabled for the department
            // Determine department from tracking number by finding the service request
            $department = 'Civil Registry Office'; // Default fallback
            $serviceRequest = null;
            
            // Try to find in CivilService first
            $civilService = CivilService::byTracking($request->tracking_number)->first();
            if ($civilService) {
                $serviceRequest = $civilService;
                $department = 'Civil Registry Office';
            } else {
                // Try to find in OtherService
                $otherService = OtherService::byTracking($request->tracking_number)->first();
                if ($otherService) {
                    $serviceRequest = $otherService;
                    // Get department from OtherService, fallback to 'Other Services' if not set
                    $department = $otherService->department ?? 'Other Services';
                }
            }
            
            // Only include queue number if feature is enabled for the department and queue number exists
            if ($serviceRequest && $serviceRequest->queue_number) {
                $queueEnabled = Setting::getQueueNumberSettingForDepartment($department, false);
                if (!$queueEnabled) {
                    $payload['queue'] = null;
                }
            } else {
                $payload['queue'] = null;
            }

            // Use service_type (specific service) from database if available, otherwise use the provided service value
            // This ensures we print the actual service type (e.g., "Fishtrap/Fishpen Permit") 
            // instead of the department name (e.g., "BPLO")
            if ($serviceRequest && $serviceRequest->service_type) {
                $payload['service'] = $serviceRequest->service_type;
            }

            // Send to print service
            $response = Http::timeout(10)->post('http://localhost:4000/print', $payload);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Receipt sent to printer successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Print service returned error',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Print receipt failed', [
                'tracking_number' => $request->tracking_number,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Print service unavailable',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
