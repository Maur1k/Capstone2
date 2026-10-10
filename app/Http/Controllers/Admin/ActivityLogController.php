<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('admin')
            ->whereHas('admin', function ($q) {
                $q->where('role', 'officer');
            })
            ->orderBy('created_at', 'desc');

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by officer
        if ($request->filled('officer_id')) {
            $query->where('admin_id', $request->officer_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activityLogs = $query->paginate(20);

        // Get officers for filter dropdown
        $officers = Admin::where('role', 'officer')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get activity types for filter dropdown
        $activityTypes = ActivityLog::whereHas('admin', function ($q) {
            $q->where('role', 'officer');
        })
            ->distinct()
            ->pluck('activity_type')
            ->sort()
            ->values();

        // Get statistics
        $stats = [
            'total_activities' => ActivityLog::whereHas('admin', function ($q) {
                $q->where('role', 'officer');
            })->count(),
            'logins_today' => ActivityLog::whereHas('admin', function ($q) {
                $q->where('role', 'officer');
            })->where('activity_type', 'login')
                ->whereDate('created_at', today())
                ->count(),
            'status_changes_today' => ActivityLog::whereHas('admin', function ($q) {
                $q->where('role', 'officer');
            })->where('activity_type', 'status_change')
                ->whereDate('created_at', today())
                ->count(),
            'active_officers' => Admin::where('role', 'officer')
                ->where('is_active', true)
                ->count(),
        ];

        return view('admin.activity-logs.index', compact(
            'activityLogs',
            'officers',
            'activityTypes',
            'stats'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('admin');

        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function filtered(Request $request)
    {
        $type = $request->get('type');
        $query = ActivityLog::with('admin')
            ->whereHas('admin', function ($q) {
                $q->where('role', 'officer');
            })
            ->orderBy('created_at', 'desc');

        switch ($type) {
            case 'login':
                $query->where('activity_type', 'login')
                    ->whereDate('created_at', today());
                break;
            case 'status_change':
                $query->where('activity_type', 'status_change')
                    ->whereDate('created_at', today());
                break;
            case 'active_officers':
                // Get activities from active officers in the last 24 hours
                $query->whereHas('admin', function ($q) {
                    $q->where('role', 'officer')
                        ->where('is_active', true);
                })->where('created_at', '>=', now()->subDay());
                break;
            default:
                // Return recent activities (last 50)
                $query->limit(50);
                break;
        }

        $logs = $query->limit(20)->get();

        return response()->json([
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activity_type' => $log->activity_type,
                    'description' => $log->description,
                    'formatted_time' => $log->formatted_time,
                    'admin' => [
                        'name' => $log->admin->name,
                        'department' => $log->admin->department
                    ]
                ];
            })
        ]);
    }
}