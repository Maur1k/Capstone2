<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with BRAP and Regular requests
     */
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get BRAP requests
        $brapRequests = collect();
        $brapRequests = $brapRequests->merge(CivilService::brap()->get());
        $brapRequests = $brapRequests->merge(OtherService::brap()->get());
        $brapRequests = $brapRequests->sortByDesc('created_at');
        
        // Get Regular requests
        $regularRequests = collect();
        $regularRequests = $regularRequests->merge(CivilService::regular()->get());
        $regularRequests = $regularRequests->merge(OtherService::regular()->get());
        $regularRequests = $regularRequests->sortByDesc('created_at');
        
        // Get notifications
        $notifications = Notification::unread()->orderBy('created_at', 'desc')->get();
        
        // Statistics
        $stats = [
            'brap_total' => $brapRequests->count(),
            'brap_pending' => $brapRequests->where('status', 'inquiry')->count(),
            'brap_processing' => $brapRequests->whereIn('status', ['checklist_given', 'incomplete_requirements', 'accepted'])->count(),
            'brap_completed' => $brapRequests->whereIn('status', ['registered', 'positive_result', 'negative_result'])->count(),
            'regular_total' => $regularRequests->count(),
            'regular_pending' => $regularRequests->where('status', 'inquiry')->count(),
            'regular_processing' => $regularRequests->whereIn('status', ['checklist_given', 'incomplete_requirements', 'accepted'])->count(),
            'regular_completed' => $regularRequests->whereIn('status', ['registered'])->count(),
            'unread_notifications' => $notifications->count(),
        ];
        
        return view('admin.dashboard.requests', compact(
            'admin',
            'brapRequests',
            'regularRequests',
            'notifications',
            'stats'
        ));
    }
    
    /**
     * Get BRAP requests (AJAX)
     */
    public function getBrapRequests(Request $request)
    {
        $brapRequests = collect();
        $brapRequests = $brapRequests->merge(CivilService::brap()->get());
        $brapRequests = $brapRequests->merge(OtherService::brap()->get());
        $brapRequests = $brapRequests->sortByDesc('created_at');
        
        return response()->json([
            'requests' => $brapRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'queue_number' => $request->queue_number,
                    'full_name' => $request->full_name,
                    'service_type' => $request->service_type,
                    'status' => $request->status,
                    'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'model_type' => get_class($request),
                ];
            })
        ]);
    }
    
    /**
     * Get Regular requests (AJAX)
     */
    public function getRegularRequests(Request $request)
    {
        $regularRequests = collect();
        $regularRequests = $regularRequests->merge(CivilService::regular()->get());
        $regularRequests = $regularRequests->merge(OtherService::regular()->get());
        $regularRequests = $regularRequests->sortByDesc('created_at');
        
        return response()->json([
            'requests' => $regularRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'queue_number' => $request->queue_number,
                    'full_name' => $request->full_name,
                    'service_type' => $request->service_type,
                    'status' => $request->status,
                    'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'model_type' => get_class($request),
                ];
            })
        ]);
    }
    
    /**
     * Update request status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'status' => 'required|string|in:inquiry,checklist_given,incomplete_requirements,accepted,registered,encoded_brap,negative_result,positive_result,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $admin = Auth::guard('admin')->user();
        
        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $request->tracking_number)->first();
        if ($civilRequest) {
            $oldStatus = $civilRequest->status;
            $civilRequest->updateStatus($request->status, $admin->name, $request->admin_notes);
            
            // Create notification for status change
            Notification::create([
                'type' => 'status_update',
                'title' => 'Status Updated',
                'message' => "Request {$request->tracking_number} status changed from " . ucwords(str_replace('_', ' ', $oldStatus)) . " to " . ucwords(str_replace('_', ' ', $request->status)),
                'tracking_number' => $request->tracking_number,
                'request_type' => $civilRequest->request_type,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }
        
        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $request->tracking_number)->first();
        if ($otherRequest) {
            $oldStatus = $otherRequest->status;
            $otherRequest->updateStatus($request->status, $admin->name, $request->admin_notes);
            
            // Create notification for status change
            Notification::create([
                'type' => 'status_update',
                'title' => 'Status Updated',
                'message' => "Request {$request->tracking_number} status changed from " . ucwords(str_replace('_', ' ', $oldStatus)) . " to " . ucwords(str_replace('_', ' ', $request->status)),
                'tracking_number' => $request->tracking_number,
                'request_type' => $otherRequest->request_type,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }
        
        return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
    }
    
    
    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        Notification::unread()->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
}
