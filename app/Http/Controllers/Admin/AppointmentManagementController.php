<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CivilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentManagementController extends Controller
{
    /**
     * Display appointments for officers
     */
    public function index(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        \Log::info('AppointmentManagementController::index called', [
            'officer' => $officer->name,
            'department' => $officer->department,
            'role' => $officer->role
        ]);

        $query = Appointment::with('civilService');

        // Filter by date if provided
        if ($request->has('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('appointment_date', $date);
        }
        // Remove default date filter to show all appointments

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by officer if super admin
        if ($officer->role === 'super_admin') {
            if ($request->has('officer')) {
                $query->where('assigned_officer', $request->officer);
            }
        } else {
            // For Civil Registry officers, show all appointments (not just assigned ones)
            // since appointments are created without assigned_officer initially
            if ($officer->department !== 'Civil Registry Office') {
                $query->where('assigned_officer', $officer->name);
            }
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(20);

        \Log::info('AppointmentManagementController::index results', [
            'total_appointments' => $appointments->total(),
            'appointments_count' => $appointments->count(),
            'appointments_data' => $appointments->items()
        ]);

        return response()->json([
            'appointments' => $appointments,
            'officer' => $officer,
        ]);
    }

    /**
     * Get appointment statistics
     */
    public function statistics()
    {
        $officer = Auth::guard('admin')->user();

        $query = Appointment::query();

        // Filter by officer if not super admin
        if ($officer->role !== 'super_admin') {
            // For Civil Registry officers, show all appointments (not just assigned ones)
            if ($officer->department !== 'Civil Registry Office') {
                $query->where('assigned_officer', $officer->name);
            }
        }

        $stats = [
            'total_appointments' => $query->count(),
            'pending_appointments' => $query->where('status', 'pending')->count(),
            'confirmed_appointments' => $query->where('status', 'confirmed')->count(),
            'completed_appointments' => $query->where('status', 'completed')->count(),
            'cancelled_appointments' => $query->where('status', 'cancelled')->count(),
            'today_appointments' => $query->whereDate('appointment_date', now())->count(),
            'upcoming_appointments' => $query->where('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Confirm an appointment
     */
    public function confirm(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $officer = Auth::guard('admin')->user();

        if ($appointment->status !== 'pending') {
            return response()->json(['error' => 'Only pending appointments can be confirmed'], 400);
        }

        $appointment->confirm($officer->name);

        return response()->json([
            'success' => true,
            'message' => 'Appointment confirmed successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Complete an appointment
     */
    public function complete(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $officer = Auth::guard('admin')->user();

        if ($appointment->status !== 'confirmed') {
            return response()->json(['error' => 'Only confirmed appointments can be completed'], 400);
        }

        $appointment->complete();

        // Also update the civil service status if needed
        $civilService = $appointment->civilService;
        if ($civilService->status === 'submitted') {
            $civilService->updateStatus('processing', $officer->name, 'Appointment completed');
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment completed successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        $validator = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $officer = Auth::guard('admin')->user();

        if ($appointment->isCompleted()) {
            return response()->json(['error' => 'Cannot cancel a completed appointment'], 400);
        }

        if ($appointment->isCancelled()) {
            return response()->json(['error' => 'Appointment is already cancelled'], 400);
        }

        $appointment->cancel($validator['cancellation_reason']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Assign appointment to officer
     */
    public function assign(Request $request, $appointmentId)
    {
        $validator = $request->validate([
            'officer_name' => 'required|string|max:255',
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $officer = Auth::guard('admin')->user();

        // Only super admins can assign appointments
        if ($officer->role !== 'super_admin') {
            return response()->json(['error' => 'Only super admins can assign appointments'], 403);
        }

        $appointment->update([
            'assigned_officer' => $validator['officer_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment assigned successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Get appointment details
     */
    public function show($appointmentId)
    {
        $appointment = Appointment::with('civilService')->findOrFail($appointmentId);

        return response()->json([
            'appointment' => $appointment,
            'civil_service' => $appointment->civilService,
        ]);
    }

    /**
     * Get appointments calendar data
     */
    public function calendar(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        $query = Appointment::with('civilService');

        // Filter by officer if not super admin
        if ($officer->role !== 'super_admin') {
            // For Civil Registry officers, show all appointments (not just assigned ones)
            if ($officer->department !== 'Civil Registry Office') {
                $query->where('assigned_officer', $officer->name);
            }
        }

        // Filter by date range
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);
            $query->whereBetween('appointment_date', [$start, $end]);
        }

        $appointments = $query->get();

        $calendarData = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->full_name . ' - ' . $appointment->service_type,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time->format('H:i:s'),
                'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time->addMinutes(30)->format('H:i:s'),
                'status' => $appointment->status,
                'appointment_number' => $appointment->appointment_number,
                'contact_number' => $appointment->contact_number,
                'notes' => $appointment->notes,
                'assigned_officer' => $appointment->assigned_officer,
            ];
        });

        return response()->json($calendarData);
    }
}