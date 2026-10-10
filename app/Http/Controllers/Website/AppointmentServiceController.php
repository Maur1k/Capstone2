<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class AppointmentServiceController extends Controller
{
    /**
     * Show the appointment service selection page
     */
    public function index()
    {
        try {
            // Get prioritized civil services
            $prioritizedService = Service::where('name', 'Prioritized Civil Services')
                ->where('is_active', true)
                ->first();

            $prioritizedServiceTypes = $prioritizedService ?
                $prioritizedService->types()->where('is_active', true)->get() :
                collect();

            // Get other municipal services
            $otherServices = Service::where('is_active', true)
                ->where('name', '!=', 'Prioritized Civil Services')
                ->orderBy('name')
                ->get();

            return view('website.appointment.service', compact(
                'prioritizedService',
                'prioritizedServiceTypes',
                'otherServices'
            ));
        } catch (\Exception $e) {
            // Fallback to empty collections if database error
            $prioritizedService = null;
            $prioritizedServiceTypes = collect();
            $otherServices = collect();

            return view('website.appointment.service', compact(
                'prioritizedService',
                'prioritizedServiceTypes',
                'otherServices'
            ));
        }
    }

    /**
     * Get service types for a specific service
     */
    public function getServiceTypes(Request $request)
    {
        $serviceId = $request->input('service_id');

        try {
            $serviceTypes = ServiceType::where('service_id', $serviceId)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'service_types' => $serviceTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load service types'
            ], 500);
        }
    }

    /**
     * Handle service selection
     */
    public function selectService(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
            'department' => 'required|string',
            'service_type' => 'nullable|string',
        ]);

        // Store selection in session for next step
        session([
            'appointment.service_name' => $request->service_name,
            'appointment.department' => $request->department,
            'appointment.service_type' => $request->service_type,
        ]);

        return redirect()->route('appointment.tnd');
    }
}

