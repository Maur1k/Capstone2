<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OfficerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $officers = Admin::query()
            ->where('role', 'officer')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('department', 'like', "%$q%")
                    ;
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.officers.index', compact('officers', 'q'));
    }

    public function create()
    {
        // Get unique departments from services table
        $departments = Service::where('is_active', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('admin.officers.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'department' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:3'],
        ]);

        $password = $data['password'] ?? str()->random(12);

        Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'role' => 'officer',
            'department' => $data['department'],
            'is_active' => true,
            'created_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.officers.index')->with('status', 'Officer created.');
    }

    public function edit(Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);

        // Get unique departments from services table
        $departments = Service::where('is_active', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('admin.officers.edit', compact('officer', 'departments'));
    }

    public function update(Request $request, Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $officer->id],
            'department' => ['required', 'string', 'max:255'],
        ]);

        $officer->update($data);

        return redirect()->route('admin.officers.index')->with('status', 'Officer updated.');
    }

    public function destroy(Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);
        $officer->delete();
        return redirect()->route('admin.officers.index')->with('status', 'Officer removed.');
    }

    public function toggle(Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);
        $officer->is_active = !$officer->is_active;
        $officer->save();
        return redirect()->back()->with('status', 'Officer status updated.');
    }

    // Officer login methods
    public function showLoginForm()
    {
        return view('admin.officers.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();

            // Check if user is an officer
            if ($admin->role === 'officer' && $admin->is_active) {
                $admin->update(['last_login_at' => now()]);
                $request->session()->regenerate();

                // Log officer login activity
                ActivityLogService::logLogin($admin, $request);

                return redirect()->route('officer.dashboard');
            } else {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Access denied. Only active officers can login.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Log officer logout activity
        if ($admin) {
            ActivityLogService::logLogout($admin, $request);
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('officer.login');
    }

    // Officer dashboard methods
    public function dashboard()
    {
        $officer = Auth::guard('admin')->user();

        // Get service types for this officer's department
        $departmentServiceTypes = \App\Models\ServiceType::whereHas('service', function ($query) use ($officer) {
            $query->where('department', $officer->department)
                ->where('is_active', true);
        })->pluck('name')
            ->toArray();

        // Get requests for this officer's department by matching service types
        $civilRequests = CivilService::whereIn('service_type', $departmentServiceTypes)
            ->orderBy('created_at', 'desc')
            ->get();

        $otherRequests = OtherService::whereIn('service_type', $departmentServiceTypes)
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine and sort all requests
        $allRequests = $civilRequests->concat($otherRequests)->sortByDesc('created_at');

        // Get statistics
        $stats = [
            'total' => $allRequests->count(),
            'pending' => $allRequests->where('status', 'submitted')->count(),
            'processing' => $allRequests->where('status', 'processing')->count(),
            'completed' => $allRequests->where('status', 'completed')->count(),
        ];

        return view('admin.officers.dashboard', compact('officer', 'allRequests', 'stats'));
    }

    public function viewRequestDetails($trackingNumber, Request $request)
    {
        $officer = Auth::guard('admin')->user();

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            // Log request viewed activity
            ActivityLogService::logRequestViewed($officer, 'CivilService', $civilRequest->id, $trackingNumber, $request);

            return view('admin.officers.request-details', compact('civilRequest', 'officer'));
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            // Log request viewed activity
            ActivityLogService::logRequestViewed($officer, 'OtherService', $otherRequest->id, $trackingNumber, $request);

            return view('admin.officers.request-details', compact('otherRequest', 'officer'));
        }

        return redirect()->back()->withErrors(['error' => 'Request not found.']);
    }

    public function updateRequestStatus(Request $request, $trackingNumber)
    {
        $officer = Auth::guard('admin')->user();

        $request->validate([
            'status' => ['required', 'in:submitted,processing,completed,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            $oldStatus = $civilRequest->status;
            $civilRequest->update([
                'status' => $request->status,
                'processed_by' => $officer->name,
                'processed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Log status change activity
            ActivityLogService::logStatusChange(
                $officer,
                'CivilService',
                $civilRequest->id,
                $trackingNumber,
                $oldStatus,
                $request->status,
                $request->admin_notes,
                $request
            );

            return redirect()->back()->with('status', 'Request status updated successfully.');
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            $oldStatus = $otherRequest->status;
            $otherRequest->update([
                'status' => $request->status,
                'processed_by' => $officer->name,
                'processed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Log status change activity
            ActivityLogService::logStatusChange(
                $officer,
                'OtherService',
                $otherRequest->id,
                $trackingNumber,
                $oldStatus,
                $request->status,
                $request->admin_notes,
                $request
            );

            return redirect()->back()->with('status', 'Request status updated successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Request not found.']);
    }
}




