<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $officerCount = Admin::where('role', 'officer')->count();
        $activeOfficerCount = Admin::where('role', 'officer')->where('is_active', true)->count();
        $inactiveOfficerCount = Admin::where('role', 'officer')->where('is_active', false)->count();
        $departmentCount = Admin::where('role', 'officer')->whereNotNull('department')->distinct('department')->count('department');
        $recentOfficers = Admin::where('role', 'officer')->latest()->limit(5)->get(['id', 'name', 'department', 'is_active']);

        return view('admin.dashboard.index', compact(
            'admin',
            'officerCount',
            'activeOfficerCount',
            'inactiveOfficerCount',
            'departmentCount',
            'recentOfficers'
        ));
    }

    public function filteredLogs(Request $request)
    {
        $type = $request->get('type');
        $logs = collect();

        switch ($type) {
            case 'total_officers':
                // Show all officers created (not activities)
                $officers = Admin::where('role', 'officer')
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
                    ->get();

                $logs = $officers->map(function ($officer) {
                    return [
                        'id' => $officer->id,
                        'officer_name' => $officer->name,
                        'officer_department' => $officer->department ?? 'N/A',
                        'activity_type' => 'create',
                        'description' => "Officer account created",
                        'created_at' => $officer->created_at->toISOString()
                    ];
                });
                break;

            case 'departments':
                // Show all departments (not activities)
                $departments = Admin::where('role', 'officer')
                    ->whereNotNull('department')
                    ->distinct('department')
                    ->pluck('department');

                $logs = $departments->map(function ($department, $index) {
                    return [
                        'id' => $index + 1,
                        'officer_name' => 'System',
                        'officer_department' => $department,
                        'activity_type' => 'create',
                        'description' => "Department: {$department}",
                        'created_at' => now()->toISOString()
                    ];
                });
                break;

            default:
                $logs = collect();
        }

        return response()->json(['logs' => $logs]);
    }

    public function recentActivity()
    {
        // Get the 3 most recent officer activities
        $activities = ActivityLog::with('admin')
            ->whereHas('admin', function ($query) {
                $query->where('role', 'officer');
            })
            ->whereIn('activity_type', ['login', 'logout', 'status_change', 'request_viewed'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'officer_name' => $log->admin ? $log->admin->name : 'Unknown',
                    'officer_department' => $log->admin ? $log->admin->department : 'N/A',
                    'activity_type' => $log->activity_type,
                    'description' => $log->description,
                    'created_at' => $log->created_at->toISOString()
                ];
            });

        return response()->json(['activities' => $activities]);
    }
}


