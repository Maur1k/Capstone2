<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Setting;
use App\Models\StatusUpdate;
use App\Models\ChecklistItem;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfficerController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
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
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Convert checkbox value to boolean
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Log the data being updated for debugging
        \Log::info('Updating officer', [
            'officer_id' => $officer->id,
            'data' => $data,
            'request_data' => $request->all()
        ]);

        $officer->update($data);

        \Log::info('Officer updated successfully', [
            'officer_id' => $officer->id,
            'updated_data' => $officer->fresh()->toArray()
        ]);

        return redirect()->route('admin.officers.index')->with('status', 'Officer updated successfully.');
    }

    public function destroy(Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);

        try {
            $officer->delete();

            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Officer removed successfully!']);
            }

            return redirect()->route('admin.officers.index')->with('status', 'Officer removed.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error removing officer: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.officers.index')->with('error', 'Error removing officer: ' . $e->getMessage());
        }
    }

    public function toggle(Admin $officer)
    {
        abort_unless($officer->role === 'officer', 404);

        try {
            $officer->is_active = !$officer->is_active;
            $officer->save();

            $status = $officer->is_active ? 'activated' : 'deactivated';

            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => "Officer {$status} successfully!"]);
            }

            return redirect()->back()->with('status', 'Officer status updated.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating officer status: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error updating officer status: ' . $e->getMessage());
        }
    }

    // Officer login methods
    public function showLoginForm()
    {
        return view('admin.auth.officer-login');
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

        // Get requests for this officer's department
        if ($officer->department === 'Civil Registry Office') {
            // Civil Registry Office only manages Civil Services (BRAP and Regular)
            $civilRequests = CivilService::orderBy('created_at', 'desc')->get();
            $otherRequests = collect(); // Civil Registry doesn't manage Other Services
        } else {
            $civilRequests = CivilService::where('department', $officer->department)
                ->orderBy('created_at', 'desc')
                ->get();

            $otherRequests = OtherService::where('department', $officer->department)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Combine and sort all requests
        $allRequests = $civilRequests->concat($otherRequests)->sortByDesc('created_at');

        // Get statistics
        $stats = [
            'total' => $allRequests->count(),
            'pending' => $allRequests->where('status', 'inquiry')->count(),
            'processing' => $allRequests->whereIn('status', ['checklist_given', 'incomplete_requirements', 'accepted'])->count(),
            'completed' => $allRequests->whereIn('status', ['registered', 'positive_result', 'negative_result'])->count(),
        ];
        
        // Today's statistics
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $todayRequests = $allRequests->filter(function($request) use ($today) {
            return $request->created_at >= $today;
        })->count();
        $yesterdayRequests = $allRequests->filter(function($request) use ($yesterday, $today) {
            return $request->created_at >= $yesterday && $request->created_at < $today;
        })->count();
        $todayChange = $yesterdayRequests > 0 ? round((($todayRequests - $yesterdayRequests) / $yesterdayRequests) * 100) : ($todayRequests > 0 ? 100 : 0);
        
        // This week's statistics
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        
        $thisWeekRequests = $allRequests->filter(function($request) use ($startOfWeek, $endOfWeek) {
            return $request->created_at >= $startOfWeek && $request->created_at <= $endOfWeek;
        })->count();
        
        $lastWeekRequests = $allRequests->filter(function($request) use ($lastWeekStart, $lastWeekEnd) {
            return $request->created_at >= $lastWeekStart && $request->created_at <= $lastWeekEnd;
        })->count();
        
        $weekChange = $lastWeekRequests > 0 ? round((($thisWeekRequests - $lastWeekRequests) / $lastWeekRequests) * 100) : ($thisWeekRequests > 0 ? 100 : 0);
        
        // Weekly data for bar chart (Mon-Sun)
        $weeklyData = [];
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $dayStart->copy()->endOfDay();
            $dayCount = $allRequests->filter(function($request) use ($dayStart, $dayEnd) {
                return $request->created_at >= $dayStart && $request->created_at <= $dayEnd;
            })->count();
            $weeklyData[$dayStart->format('D')] = $dayCount;
        }
        
        $stats['today'] = $todayRequests;
        $stats['today_change'] = $todayChange;
        $stats['week'] = $thisWeekRequests;
        $stats['week_change'] = $weekChange;
        $stats['weekly'] = $weeklyData;

        // Add Delayed Regular Non-BRAP and Delayed BRAP statistics for Civil Registry officers
        if ($officer->department === 'Civil Registry Office') {
            // Get Delayed BRAP requests (exact service name match)
            $brapRequests = CivilService::where('service_name', 'Delayed BRAP Birth Registration Assistance Project')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get Delayed Regular Non-BRAP requests (exact service name match)
            $regularRequests = CivilService::where('service_name', 'Delayed Regular Non-BRAP Birth Registration Assistance Project')
                ->orderBy('created_at', 'desc')
                ->get();

            // Add BRAP and Regular statistics
            $stats['brap_total'] = $brapRequests->count();
            $stats['brap_pending'] = $brapRequests->where('status', 'inquiry')->count();
            $stats['brap_processing'] = $brapRequests->whereIn('status', ['checklist_given', 'incomplete_requirements', 'accepted'])->count();
            $stats['brap_completed'] = $brapRequests->whereIn('status', ['registered', 'positive_result', 'negative_result'])->count();

            $stats['regular_total'] = $regularRequests->count();
            $stats['regular_pending'] = $regularRequests->where('status', 'inquiry')->count();
            $stats['regular_processing'] = $regularRequests->whereIn('status', ['checklist_given', 'incomplete_requirements', 'accepted'])->count();
            $stats['regular_completed'] = $regularRequests->whereIn('status', ['registered'])->count();
        }

        return view('admin.officer-dashboard.dashboard', compact('officer', 'allRequests', 'stats'));
    }

    /**
     * Get weekly overview data for a specific week offset
     */
    public function weeklyOverview(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $weekOffset = (int) $request->get('week_offset', 0);
        
        // Limit to 12 weeks back and no future weeks
        $weekOffset = max(-12, min(0, $weekOffset));
        
        // Get requests for this officer's department
        if ($officer->department === 'Civil Registry Office') {
            $civilRequests = CivilService::orderBy('created_at', 'desc')->get();
            $otherRequests = collect();
        } else {
            $civilRequests = CivilService::where('department', $officer->department)
                ->orderBy('created_at', 'desc')
                ->get();
            $otherRequests = OtherService::where('department', $officer->department)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $allRequests = $civilRequests->concat($otherRequests);
        
        // Calculate the target week dates
        $targetWeekStart = now()->addWeeks($weekOffset)->startOfWeek();
        $targetWeekEnd = now()->addWeeks($weekOffset)->endOfWeek();
        $previousWeekStart = now()->addWeeks($weekOffset - 1)->startOfWeek();
        $previousWeekEnd = now()->addWeeks($weekOffset - 1)->endOfWeek();
        
        // Get week total
        $weekTotal = $allRequests->filter(function($request) use ($targetWeekStart, $targetWeekEnd) {
            return $request->created_at >= $targetWeekStart && $request->created_at <= $targetWeekEnd;
        })->count();
        
        // Get previous week total for comparison
        $previousWeekTotal = $allRequests->filter(function($request) use ($previousWeekStart, $previousWeekEnd) {
            return $request->created_at >= $previousWeekStart && $request->created_at <= $previousWeekEnd;
        })->count();
        
        $weekChange = $previousWeekTotal > 0 
            ? round((($weekTotal - $previousWeekTotal) / $previousWeekTotal) * 100) 
            : ($weekTotal > 0 ? 100 : 0);
        
        // Weekly data for bar chart (Mon-Sun)
        $weeklyData = [];
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $targetWeekStart->copy()->addDays($i)->startOfDay();
            $dayEnd = $dayStart->copy()->endOfDay();
            $dayCount = $allRequests->filter(function($request) use ($dayStart, $dayEnd) {
                return $request->created_at >= $dayStart && $request->created_at <= $dayEnd;
            })->count();
            $weeklyData[$dayStart->format('D')] = $dayCount;
        }
        
        return response()->json([
            'success' => true,
            'start_date' => $targetWeekStart->format('M j, Y'),
            'end_date' => $targetWeekEnd->format('M j, Y'),
            'week_total' => $weekTotal,
            'week_change' => $weekChange,
            'weekly_data' => $weeklyData,
            'week_offset' => $weekOffset
        ]);
    }

    public function viewRequestDetails($trackingNumber, Request $request)
    {
        $officer = Auth::guard('admin')->user();

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            // Log request viewed activity
            ActivityLogService::logRequestViewed($officer, 'App\Models\CivilService', $civilRequest->id, $trackingNumber, $request);

            // Get activity logs for this request
            $activityLogs = \App\Models\ActivityLog::where('target_type', 'App\Models\CivilService')
                ->where('target_id', $civilRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get SMS logs
            $smsLogs = $this->getSmsLogs($civilRequest);

            $statusUpdates = $civilRequest->statusUpdates()->get();
            $checklistItems = $civilRequest->checklistItems()->get();

            return view('admin.officer-dashboard.request-details', compact('civilRequest', 'officer', 'activityLogs', 'smsLogs', 'statusUpdates', 'checklistItems'));
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            // Log request viewed activity
            ActivityLogService::logRequestViewed($officer, 'App\Models\OtherService', $otherRequest->id, $trackingNumber, $request);

            // Get activity logs for this request
            $activityLogs = \App\Models\ActivityLog::where('target_type', 'App\Models\OtherService')
                ->where('target_id', $otherRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get SMS logs
            $smsLogs = $this->getSmsLogs($otherRequest);

            $statusUpdates = $otherRequest->statusUpdates()->get();
            $checklistItems = $otherRequest->checklistItems()->get();

            return view('admin.officer-dashboard.request-details', compact('otherRequest', 'officer', 'activityLogs', 'smsLogs', 'statusUpdates', 'checklistItems'));
        }

        return redirect()->back()->withErrors(['error' => 'Request not found.']);
    }

    public function updateRequestStatus(Request $request, $trackingNumber)
    {
        $officer = Auth::guard('admin')->user();

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            // Determine request type: check service_name first to override incorrect database values
            $serviceName = $civilRequest->service_name ?? '';
            $dbRequestType = $civilRequest->request_type;
            
            // Priority 1: Check if this is Delayed Regular Non-BRAP (has Delayed and Non-BRAP)
            if (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) {
                $requestType = 'delayed_regular';
            }
            // Priority 2: Check if this is Delayed BRAP (has Delayed and BRAP, but NOT Non-BRAP)
            elseif (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) {
                $requestType = 'delayed_brap';
            }
            // Priority 3: If service_name clearly indicates Non-BRAP (without Delayed), force it to 'regular'
            elseif (stripos($serviceName, 'Non-BRAP') !== false || stripos($serviceName, 'Non BRAP') !== false) {
                $requestType = 'regular';
            }
            // Priority 4: If service_name contains BRAP (but not Non-BRAP and not Delayed), it's regular BRAP
            elseif (stripos($serviceName, 'BRAP') !== false) {
                $requestType = 'brap';
            }
            // Priority 5: Use database value if set
            elseif ($dbRequestType && $dbRequestType !== '') {
                $requestType = $dbRequestType;
            }
            // Priority 6: Default to 'regular'
            else {
                $requestType = 'regular';
            }
            
            $allowedStatuses = CivilService::statusFlow($requestType, $serviceName);

            $request->validate([
                'status' => ['required', Rule::in($allowedStatuses)],
                'status_date' => ['nullable', 'date'],
                'remarks' => ['nullable', 'string', 'max:1000'],
            ]);

            if (!CivilService::canTransition($civilRequest->status, $request->status, $requestType, $serviceName)) {
                return redirect()->back()->withErrors(['status' => 'Invalid status transition.']);
            }

            // Validate 10-day posting period before allowing status to be set to "registered"
            if ($request->status === 'registered') {
                $postingValidation = $this->validatePostingPeriod($civilRequest);
                if (!$postingValidation['valid']) {
                    return redirect()->back()->withErrors(['status' => $postingValidation['message']]);
                }
            }

            $oldStatus = $civilRequest->status;
            $civilRequest->updateStatus($request->status, $officer->name, $request->remarks);

            $this->logStatusUpdate($civilRequest, $request, $officer);

            // Send notification to officer admin
            // Pass remarks to the request object temporarily so NotificationService can access it
            $civilRequest->remarks = $request->remarks;
            $this->notificationService->notifyStatusChange($civilRequest, $oldStatus, $request->status, $officer->id);

            // Send SMS notification to client (prepared for Semaphore API)
            $this->notificationService->notifyClientViaSMS($civilRequest, $request->status, $request->remarks);

            // Log status change activity
            ActivityLogService::logStatusChange(
                $officer,
                'App\Models\CivilService',
                $civilRequest->id,
                $trackingNumber,
                $oldStatus,
                $request->status,
                $request->remarks,
                $request
            );

            return redirect()->back()->with('status', 'Request status updated successfully.');
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            $request->validate([
                'status' => ['required', Rule::in(OtherService::statusFlow())],
                'status_date' => ['nullable', 'date'],
                'remarks' => ['nullable', 'string', 'max:1000'],
            ]);

            if (!OtherService::canTransition($otherRequest->status, $request->status)) {
                return redirect()->back()->withErrors(['status' => 'Invalid status transition.']);
            }

            $oldStatus = $otherRequest->status;
            $otherRequest->updateStatus($request->status, $officer->name, $request->remarks);

            $this->logStatusUpdate($otherRequest, $request, $officer);

            // Send notification to officer admin
            // Pass remarks to the request object temporarily so NotificationService can access it
            $otherRequest->remarks = $request->remarks;
            $this->notificationService->notifyStatusChange($otherRequest, $oldStatus, $request->status, $officer->id);

            // Send SMS notification to client (prepared for Semaphore API)
            $this->notificationService->notifyClientViaSMS($otherRequest, $request->status, $request->remarks);

            // Log status change activity
            ActivityLogService::logStatusChange(
                $officer,
                'App\Models\OtherService',
                $otherRequest->id,
                $trackingNumber,
                $oldStatus,
                $request->status,
                $request->remarks,
                $request
            );

            return redirect()->back()->with('status', 'Request status updated successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Request not found.']);
    }

    // Request Management Methods

    public function getBrapRequests(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        \Log::info('getBrapRequests called', [
            'officer_id' => $officer->id,
            'officer_department' => $officer->department
        ]);

        // Only Civil Registry officers can manage BRAP requests
        if ($officer->department !== 'Civil Registry Office') {
            \Log::info('Non-Civil Registry officer accessing BRAP requests, returning empty array');
            return response()->json([
                'requests' => []
            ]);
        }

        // Match BRAP requests - must be "Delayed Birth Registration" with BRAP
        // Exclude services with "Non-BRAP" and exclude Other Civil Registry Services
        $brapRequests = CivilService::where(function($query) {
                $query->where(function($q) {
                    // Match by request_type='brap' AND must be Birth Registration
                    $q->where('request_type', 'brap')
                      ->where(function($subQ) {
                          $subQ->where('service_name', 'like', '%Delayed Birth Registration%')
                               ->orWhere('service_name', 'like', '%Birth Registration%');
                      });
                })
                ->orWhere(function($q) {
                    // Match "Delayed Birth Registration" that IS BRAP
                    $q->where('service_name', 'like', '%Delayed Birth Registration%')
                      ->where('service_name', 'like', '%BRAP%')
                      ->where('service_name', 'not like', '%Non-BRAP%')
                      ->where('service_name', 'not like', '%Non-Brap%');
                })
                ->orWhere('service_name', 'Delayed BRAP Birth Registration Assistance Project');
            })
            // Exclude Other Civil Registry Services (Marriage, Death, Issuance)
            ->where(function($query) {
                $query->where('service_name', 'not like', '%Certificates of Marriage%')
                      ->where('service_name', 'not like', '%Municipal Form No. 97%')
                      ->where('service_name', 'not like', '%Certificates of Death%')
                      ->where('service_name', 'not like', '%Municipal Form No. 103%')
                      ->where('service_name', 'not like', '%Certified Photocopy%')
                      ->where('service_name', 'not like', '%Transcription of Civil Registry%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'requests' => $brapRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'queue_number' => $request->queue_number,
                    'full_name' => $request->full_name,
                    'contact_number' => $request->contact_number,
                    'service_type' => $request->service_type,
                    'service_name' => $request->service_name,
                    'status' => $request->status,
                    'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'model_type' => get_class($request),
                    'request_type' => 'delayed_brap', // Changed from 'brap' to 'delayed_brap'
                ];
            })
        ]);
    }

    public function getRegularRequests(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        \Log::info('getRegularRequests called', [
            'officer_id' => $officer->id,
            'officer_department' => $officer->department
        ]);

        if ($officer->department === 'Civil Registry Office') {
            // Civil Registry officers manage Delayed Regular Non-BRAP Birth Registration requests only
            // Must be "Delayed Birth Registration" and NOT BRAP, and NOT other services (Marriage, Death, Issuance)
            $regularRequests = CivilService::where(function($query) {
                    $query->where(function($q) {
                        // Match by request_type='regular' AND must be Birth Registration
                        $q->where('request_type', 'regular')
                          ->where(function($subQ) {
                              $subQ->where('service_name', 'like', '%Delayed Birth Registration%')
                                   ->orWhere('service_name', 'like', '%Birth Registration%');
                          });
                    })
                    ->orWhere(function($q) {
                        // Match "Delayed Birth Registration" that is NOT BRAP
                        $q->where('service_name', 'like', '%Delayed Birth Registration%')
                          ->where('service_name', 'not like', '%BRAP%')
                          ->where('service_name', 'not like', '%Brap%');
                    })
                    ->orWhere('service_name', 'Delayed Regular Non-BRAP Birth Registration Assistance Project');
                })
                // Exclude Other Civil Registry Services (Marriage, Death, Issuance)
                ->where(function($query) {
                    $query->where('service_name', 'not like', '%Certificates of Marriage%')
                          ->where('service_name', 'not like', '%Municipal Form No. 97%')
                          ->where('service_name', 'not like', '%Certificates of Death%')
                          ->where('service_name', 'not like', '%Municipal Form No. 103%')
                          ->where('service_name', 'not like', '%Certified Photocopy%')
                          ->where('service_name', 'not like', '%Transcription of Civil Registry%');
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Other department officers manage all their department's other services
            $regularRequests = OtherService::where('department', $officer->department)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        \Log::info('Regular requests found', [
            'count' => $regularRequests->count(),
            'department' => $officer->department
        ]);

        return response()->json([
            'requests' => $regularRequests->map(function ($request) use ($officer) {
                $serviceName = $request->service_name ?? '';
                
                // Determine request type for Civil Registry Office
                $requestType = 'other_service';
                if ($officer->department === 'Civil Registry Office') {
                    // Delayed Regular Non-BRAP should be 'delayed_regular' for proper flow detection
                    if (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) {
                        $requestType = 'delayed_regular';
                    } else {
                        $requestType = 'civil_service';
                    }
                }
                
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'queue_number' => $request->queue_number,
                    'full_name' => $request->full_name,
                    'contact_number' => $request->contact_number,
                    'service_type' => $request->service_type,
                    'service_name' => $serviceName,
                    'status' => $request->status,
                    'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'model_type' => get_class($request),
                    'request_type' => $requestType,
                    'admin_notes' => $request->admin_notes ?? '',
                ];
            })
        ]);
    }

    public function getOtherCivilRegistryRequests(Request $request)
    {
        $officer = Auth::guard('admin')->user();

        \Log::info('getOtherCivilRegistryRequests called', [
            'officer_id' => $officer->id,
            'officer_department' => $officer->department
        ]);

        // Only Civil Registry officers can manage other civil registry requests
        if ($officer->department !== 'Civil Registry Office') {
            \Log::info('Non-Civil Registry officer accessing other civil registry requests, returning empty array');
            return response()->json([
                'requests' => []
            ]);
        }

        // Define the service types to include (using partial matching for flexibility)
        // These are the 3 specific service types from service ID 24
        $serviceTypePatterns = [
            'Delayed Registration of Certificates of Marriage',
            'Municipal Form No. 97',
            'Delayed Registration of Certificates of Death',
            'Municipal Form No. 103',
            'Issuance of Certified Photocopy',
            'Transcription of Civil Registry Documents'
        ];

        // Get requests matching these service types
        // Exclude Delayed Birth Registration requests (those are in regular/brap tabs)
        $otherRequests = CivilService::where(function($query) use ($serviceTypePatterns) {
                $query->where(function($q) use ($serviceTypePatterns) {
                    foreach ($serviceTypePatterns as $index => $pattern) {
                        if ($index === 0) {
                            $q->where('service_name', 'like', '%' . $pattern . '%')
                              ->orWhere('service_type', 'like', '%' . $pattern . '%');
                        } else {
                            $q->orWhere('service_name', 'like', '%' . $pattern . '%')
                              ->orWhere('service_type', 'like', '%' . $pattern . '%');
                        }
                    }
                });
            })
            // Exclude Delayed Birth Registration requests (those belong to regular/brap tabs)
            ->where(function($query) {
                $query->where('service_name', 'not like', '%Delayed Birth Registration%')
                      ->where('service_name', 'not like', '%Birth Registration%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('Other civil registry requests found', [
            'count' => $otherRequests->count()
        ]);

        return response()->json([
            'requests' => $otherRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'queue_number' => $request->queue_number,
                    'full_name' => $request->full_name,
                    'contact_number' => $request->contact_number,
                    'service_type' => $request->service_type,
                    'service_name' => $request->service_name ?? '',
                    'status' => $request->status,
                    'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'model_type' => get_class($request),
                    'request_type' => 'other_service', // Use other_service to get the same status flow as Other Departments
                    'admin_notes' => $request->admin_notes ?? '',
                ];
            })
        ]);
    }

    public function updateRequestStatusAjax(Request $request)
    {
        \Log::info('Status update request received', [
            'tracking_number' => $request->tracking_number,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'all_data' => $request->all()
        ]);

        try {
            $validationRules = [
            'tracking_number' => 'required|string',
                'status' => 'required|string',
                'status_date' => ['nullable', 'date'],
                'remarks' => ['nullable', 'string', 'max:1000'],
                'checklist_items' => ['nullable', 'array'],
                'checklist_items.*' => ['string'],
                'checklist_remarks' => ['nullable', 'string', 'max:1000'],
                'send_checklist_sms' => ['nullable', 'boolean'],
                'missing_requirements' => ['nullable', 'array'],
                'missing_requirements.*' => ['string'],
                'incomplete_requirements_remarks' => ['nullable', 'string', 'max:1000'],
                'send_incomplete_requirements_sms' => ['nullable', 'boolean'],
                // For compliance requirements
                'compliance_requirements' => ['nullable', 'array'],
                'compliance_requirements.*' => ['string'],
                'compliance_remarks' => ['nullable', 'string', 'max:1000'],
                'send_compliance_sms' => ['nullable', 'boolean'],
            ];

            // If status is "checklist_given", require checklist items
            if ($request->status === 'checklist_given') {
                $validationRules['checklist_items'] = ['required', 'array', 'min:1'];
            }

            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for status update', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        $officer = Auth::guard('admin')->user();
        $notificationService = new NotificationService();

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $request->tracking_number)->first();
        if ($civilRequest) {
            // Determine request type: check service_name first to override incorrect database values
            $serviceName = $civilRequest->service_name ?? '';
            $dbRequestType = $civilRequest->request_type;
            
            // PRIORITY 0: Check if this is an "Other Civil Registry Service" (Marriage, Death, Issuance)
            // These should use OtherService validation logic, not CivilService validation
            // Check both service_name and service_type fields for better detection
            $serviceNameLower = strtolower($serviceName ?? '');
            $serviceType = strtolower($civilRequest->service_type ?? '');
            
            $isOtherCivilRegistryService = 
                // Marriage services
                stripos($serviceName, 'Certificates of Marriage') !== false ||
                stripos($serviceName, 'Certificate of Marriage') !== false ||
                stripos($serviceName, 'Municipal Form No. 97') !== false ||
                stripos($serviceName, 'Form No. 97') !== false ||
                stripos($serviceName, 'COM') !== false ||
                // Death services
                stripos($serviceName, 'Certificates of Death') !== false ||
                stripos($serviceName, 'Certificate of Death') !== false ||
                stripos($serviceName, 'Municipal Form No. 103') !== false ||
                stripos($serviceName, 'Form No. 103') !== false ||
                // Issuance services
                stripos($serviceName, 'Certified Photocopy') !== false ||
                stripos($serviceName, 'Transcription of Civil Registry') !== false ||
                stripos($serviceName, 'Transcription') !== false ||
                // Also check service_type field
                stripos($serviceType, 'marriage') !== false ||
                stripos($serviceType, 'death') !== false ||
                stripos($serviceType, 'photocopy') !== false ||
                stripos($serviceType, 'transcription') !== false;
            
            // Exclude Delayed Birth Registration services
            $isDelayedBirthRegistration = 
                stripos($serviceName, 'Delayed Birth Registration') !== false ||
                stripos($serviceName, 'Birth Registration') !== false;
            
            // If it's an Other Civil Registry Service (and NOT a Birth Registration), use OtherService validation
            if ($isOtherCivilRegistryService && !$isDelayedBirthRegistration) {
                \Log::info('Detected Other Civil Registry Service, using OtherService validation', [
                    'tracking_number' => $request->tracking_number,
                    'service_name' => $serviceName,
                    'service_type' => $serviceType,
                    'current_status' => $civilRequest->status,
                    'new_status' => $request->status,
                    'is_other_civil_registry' => true,
                    'is_delayed_birth_registration' => false
                ]);
                
                // Use OtherService validation logic
                $allowedStatuses = OtherService::statusFlow();
                \Log::info('OtherService allowed statuses', [
                    'allowed_statuses' => $allowedStatuses,
                    'requested_status' => $request->status,
                    'is_allowed' => in_array($request->status, $allowedStatuses)
                ]);
                
                if (!in_array($request->status, $allowedStatuses)) {
                    \Log::warning('Status not in OtherService flow', [
                        'requested_status' => $request->status,
                        'allowed_statuses' => $allowedStatuses
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Invalid Status Selection\n\nThe status '{$request->status}' is not valid for Other Civil Registry Services.\n\nAllowed statuses: " . implode(', ', $allowedStatuses),
                        'error_type' => 'invalid_status'
                    ], 422);
                }
                
                // Use OtherService transition validation
                $canTransition = OtherService::canTransition($civilRequest->status, $request->status);
                \Log::info('OtherService canTransition check', [
                    'from' => $civilRequest->status,
                    'to' => $request->status,
                    'can_transition' => $canTransition
                ]);
                
                if (!$canTransition) {
                    $currentLabel = ucwords(str_replace('_', ' ', $civilRequest->status));
                    $nextLabel = ucwords(str_replace('_', ' ', $request->status));
                    
                    \Log::warning('OtherService transition not allowed', [
                        'from' => $civilRequest->status,
                        'to' => $request->status
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Invalid Status Transition\n\nCannot transition from '{$currentLabel}' to '{$nextLabel}'.\n\nFor Other Civil Registry Services, allowed transitions:\n• Inquiry → Incomplete Requirements\n• Inquiry → Registered\n• Incomplete Requirements → Registered\n• Incomplete Requirements → Incomplete Requirements (loop)",
                        'error_type' => 'invalid_transition'
                    ], 422);
                }
                
                // Validation passed, proceed with status update
                // Use database transaction to ensure atomicity
                try {
                    return DB::transaction(function () use ($civilRequest, $request, $officer, $notificationService) {
                        $oldStatus = $civilRequest->status;
                        
                        // Manually update status (bypass CivilService validation since we already validated with OtherService)
                        $civilRequest->status = $request->status;
                        $civilRequest->processed_by = $officer->name;
                        $civilRequest->processed_at = now();
                        
                        // Update admin notes if provided
                        if ($request->has('remarks') && $request->remarks) {
                            $civilRequest->admin_notes = $request->remarks;
                        }
                        
                        $civilRequest->save();
                        
                        // Log status update
                        $this->logStatusUpdate($civilRequest, $request, $officer);
                        
                        // Handle missing requirements if status is "incomplete_requirements"
                        $missingRequirements = null;
                        $sendIncompleteRequirementsSMS = $request->boolean('send_incomplete_requirements_sms', false);
                        if ($request->status === 'incomplete_requirements' && $request->has('missing_requirements') && is_array($request->missing_requirements) && count($request->missing_requirements) > 0) {
                            $missingRequirements = $request->missing_requirements;
                            // Get incomplete requirements remarks (separate from main remarks)
                            $incompleteRemarks = $request->input('incomplete_requirements_remarks', '');
                            $existingRemarks = $request->remarks ?? '';
                            
                            // Store missing requirements in admin_notes
                            $missingItemsList = implode(', ', $missingRequirements);
                            $adminNotes = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $missingItemsList : 'Missing requirements: ' . $missingItemsList;
                            if ($incompleteRemarks) {
                                $adminNotes .= ' ' . $incompleteRemarks;
                            }
                            $civilRequest->admin_notes = $adminNotes;
                            
                            // Only append missing requirements to remarks if SMS is requested
                            if ($sendIncompleteRequirementsSMS) {
                                $smsRemarks = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $missingItemsList : 'Missing requirements: ' . $missingItemsList;
                                if ($incompleteRemarks) {
                                    $smsRemarks .= ' ' . $incompleteRemarks;
                                }
                                $request->remarks = $smsRemarks;
                            } else {
                                // Keep only original remarks for SMS (missing requirements stored in admin_notes only)
                                $request->remarks = $existingRemarks;
                            }
                            
                            $civilRequest->save();
                        }
                        
                        // Log activity
                        ActivityLogService::logStatusChange(
                            $officer,
                            'App\Models\CivilService',
                            $civilRequest->id,
                            $request->tracking_number,
                            $oldStatus,
                            $request->status,
                            $request->remarks,
                            $request
                        );
                        
                        // Send SMS notification
                        $smsSent = false;
                        $smsSkipped = false;
                        $smsSkipReason = null;
                        $smsMessageId = null;
                        $smsNetwork = null;
                        $smsError = null;
                        
                        try {
                            // For incomplete_requirements, only send SMS if checkbox is checked
                            if ($request->status === 'incomplete_requirements' && !$sendIncompleteRequirementsSMS) {
                                $smsSkipped = true;
                                $smsSkipReason = 'SMS not requested for incomplete requirements';
                                \Log::info('Skipping SMS for incomplete_requirements - SMS not requested', [
                                    'tracking_number' => $request->tracking_number
                                ]);
                            } else {
                                // Pass remarks to the request object temporarily so NotificationService can access it
                                $civilRequest->remarks = $request->remarks;
                                
                                // Send notification (which includes SMS)
                                $notification = $notificationService->notifyStatusChange($civilRequest, $oldStatus, $request->status, $officer->id);
                                
                                // Get SMS status from notification
                                if ($notification && isset($notification->data)) {
                                    $notificationData = $notification->data;
                                    $smsSent = $notificationData['sms_sent'] ?? false;
                                    $smsMessageId = $notificationData['sms_message_id'] ?? null;
                                    $smsNetwork = $notificationData['sms_network'] ?? null;
                                    if (!$smsSent && isset($notificationData['sms_error'])) {
                                        $smsError = $notificationData['sms_error'];
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error sending notification for Other Civil Registry Service', [
                                'error' => $e->getMessage(),
                                'tracking_number' => $request->tracking_number,
                                'trace' => $e->getTraceAsString()
                            ]);
                            $smsError = 'Failed to send notification: ' . $e->getMessage();
                            // Don't fail the request if notification fails
                        }
                        
                        // Get the latest status update for timestamp
                        $latestStatusUpdate = $civilRequest->statusUpdates()->latest()->first();
                        $statusUpdateTime = $latestStatusUpdate 
                            ? $latestStatusUpdate->created_at->format('M d, Y h:i A')
                            : now()->format('M d, Y h:i A');
                        
                        // Prepare response with SMS status and timestamp
                        $response = [
                            'success' => true, 
                            'message' => 'Status updated successfully.',
                            'status' => $request->status,
                            'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                            'updated_at' => $statusUpdateTime,
                            'updated_timestamp' => $latestStatusUpdate?->created_at?->toIso8601String() ?? now()->toIso8601String()
                        ];
                        
                        // Add SMS status to response
                        if ($smsSent) {
                            $response['sms_sent'] = true;
                            if ($smsMessageId) {
                                $response['sms_message_id'] = $smsMessageId;
                            }
                            if ($smsNetwork) {
                                $response['sms_network'] = $smsNetwork;
                            }
                        } else if ($smsSkipped) {
                            $response['sms_skipped'] = true;
                            $response['sms_skip_reason'] = $smsSkipReason;
                        } else {
                            $response['sms_sent'] = false;
                            if ($smsError) {
                                $response['sms_error'] = $smsError;
                            }
                        }
                        
                        return response()->json($response);
                    });
                } catch (\Exception $e) {
                    \Log::error('Error updating Other Civil Registry Service status', [
                        'error' => $e->getMessage(),
                        'tracking_number' => $request->tracking_number,
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update status: ' . $e->getMessage()
                    ], 500);
                }
            }
            
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
            
            \Log::info('Processing Civil Service status update', [
                'tracking_number' => $request->tracking_number,
                'current_status' => $civilRequest->status,
                'new_status' => $request->status,
                'db_request_type' => $dbRequestType,
                'detected_request_type' => $requestType,
                'service_name' => $serviceName,
                'override_reason' => stripos($serviceName, 'Non-BRAP') !== false ? 'service_name contains Non-BRAP' : 
                                    (stripos($serviceName, 'BRAP') !== false ? 'service_name contains BRAP' : 'using db value or default')
            ]);
            
            // Validate status is in allowed flow for this request type (pass serviceName for correct detection)
            $allowedStatuses = CivilService::statusFlow($requestType, $serviceName);
            if (!in_array($request->status, $allowedStatuses)) {
                \Log::warning('Invalid status for Civil Service', [
                    'tracking_number' => $request->tracking_number,
                    'request_type' => $requestType,
                    'requested_status' => $request->status,
                    'allowed_statuses' => $allowedStatuses,
                    'service_name' => $serviceName
                ]);

                $requestTypeLabel = match($requestType) {
                    'delayed_regular' => 'Delayed Birth Registration (Regular)',
                    'delayed_brap' => 'Delayed BRAP',
                    'brap' => 'BRAP',
                    'regular' => 'Regular Non-BRAP',
                    default => ucfirst(str_replace('_', ' ', $requestType))
                };

                $allowedStatusesFormatted = array_map(function($s) {
                    return ucwords(str_replace('_', ' ', $s));
                }, $allowedStatuses);

                return response()->json([
                    'success' => false,
                    'message' => "❌ Invalid Status Selection\n\n" .
                                "The status '{$request->status}' is not valid for {$requestTypeLabel} requests.\n\n" .
                                "📋 Allowed statuses for this request type:\n" .
                                "• " . implode("\n• ", $allowedStatusesFormatted) . "\n\n" .
                                "Please select a valid status from the dropdown.",
                    'error_type' => 'invalid_status',
                    'allowed_statuses' => $allowedStatusesFormatted
                ], 422);
            }

            // Validate 10-day posting period before allowing status to be set to "registered"
            if ($request->status === 'registered') {
                $postingValidation = $this->validatePostingPeriod($civilRequest);
                if (!$postingValidation['valid']) {
                    \Log::warning('Posting period validation failed for status update to registered', [
                        'tracking_number' => $request->tracking_number,
                        'message' => $postingValidation['message'],
                        'posting_started_at' => $civilRequest->posting_started_at,
                        'accepted_at' => $civilRequest->accepted_at,
                        'service_name' => $serviceName
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => $postingValidation['message'],
                        'error_type' => 'posting_period_not_completed',
                        'days_remaining' => $postingValidation['days_remaining'] ?? null,
                        'posting_started_at' => $civilRequest->posting_started_at ? $civilRequest->posting_started_at->format('Y-m-d H:i:s') : null,
                        'accepted_at' => $civilRequest->accepted_at ? $civilRequest->accepted_at->format('Y-m-d H:i:s') : null,
                    ], 422);
                }
            }

            // Check if transition is allowed (prevents backwards and skipping steps)
            // Pass serviceName to ensure correct flow detection
            $flow = CivilService::statusFlow($requestType, $serviceName);
            $currentIndex = array_search($civilRequest->status, $flow, true);
            $nextIndex = array_search($request->status, $flow, true);
            
            \Log::info('Status transition check', [
                'request_type' => $requestType,
                'service_name' => $serviceName,
                'current_status' => $civilRequest->status,
                'current_index' => $currentIndex,
                'new_status' => $request->status,
                'next_index' => $nextIndex,
                'flow' => $flow,
                'can_transition' => CivilService::canTransition($civilRequest->status, $request->status, $requestType, $serviceName)
            ]);

            // Special handling: If current status is not in the flow (e.g., wrong status was set previously),
            // allow transitioning to any valid status in the flow as a correction
            if ($currentIndex === false && $nextIndex !== false) {
                \Log::info('Current status not in flow, allowing correction transition', [
                    'tracking_number' => $request->tracking_number,
                    'current_status' => $civilRequest->status,
                    'new_status' => $request->status,
                    'request_type' => $requestType,
                    'service_name' => $serviceName
                ]);
                // Allow the transition as a correction - proceed to update (skip validation)
            } elseif (!CivilService::canTransition($civilRequest->status, $request->status, $requestType, $serviceName)) {
                $currentLabel = ucwords(str_replace('_', ' ', $civilRequest->status));
                $nextLabel = ucwords(str_replace('_', ' ', $request->status));
                
                if ($nextIndex === false) {
                    // This shouldn't happen since we already validated, but just in case
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Invalid Status\n\nThe requested status '{$nextLabel}' is not valid for this request type.",
                        'error_type' => 'invalid_status'
                    ], 422);
                } elseif ($nextIndex < $currentIndex) {
                    // Prevent backwards transition
                    $currentTime = $civilRequest->status_updates()->latest()->first()?->created_at 
                        ? $civilRequest->status_updates()->latest()->first()->created_at->format('M d, Y h:i A')
                        : 'Unknown';
                    
                    return response()->json([
                        'success' => false,
                        'message' => "⛔ Cannot Revert Status Backwards\n\n" .
                                    "Current Status: {$currentLabel}\n" .
                                    "Last Updated: {$currentTime}\n\n" .
                                    "⚠️ Status progression can only move forward, not backward.\n" .
                                    "You can only move to the next step in the workflow.\n\n" .
                                    "Next allowed status: " . ($currentIndex + 1 < count($flow) ? ucwords(str_replace('_', ' ', $flow[$currentIndex + 1])) : 'None'),
                        'error_type' => 'backwards_transition',
                        'current_status' => $currentLabel,
                        'current_index' => $currentIndex,
                        'next_allowed' => $currentIndex + 1 < count($flow) ? $flow[$currentIndex + 1] : null
                    ], 422);
                } elseif ($nextIndex > $currentIndex + 1) {
                    // Prevent skipping steps
                    $nextAllowed = $currentIndex + 1 < count($flow) ? ucwords(str_replace('_', ' ', $flow[$currentIndex + 1])) : 'None';
                    $skippedSteps = [];
                    for ($i = $currentIndex + 1; $i < $nextIndex; $i++) {
                        $skippedSteps[] = ucwords(str_replace('_', ' ', $flow[$i]));
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => "⛔ Cannot Skip Steps\n\n" .
                                    "Current Status: {$currentLabel}\n" .
                                    "Requested Status: {$nextLabel}\n\n" .
                                    "⚠️ You cannot skip workflow steps.\n" .
                                    "Skipped steps: " . implode(' → ', $skippedSteps) . "\n\n" .
                                    "Next allowed status: {$nextAllowed}",
                        'error_type' => 'skip_steps',
                        'current_status' => $currentLabel,
                        'skipped_steps' => $skippedSteps,
                        'next_allowed' => $nextAllowed
                    ], 422);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Invalid Status Transition\n\n" .
                                    "Cannot transition from '{$currentLabel}' to '{$nextLabel}'.\n\n" .
                                    "Please ensure you are following the correct workflow sequence.",
                        'error_type' => 'invalid_transition'
                    ], 422);
                }
            }

            try {
                // Use database transaction to ensure atomicity
                return DB::transaction(function () use ($civilRequest, $request, $officer, $notificationService) {
            $oldStatus = $civilRequest->status;
                    
                    // Update status
                    $civilRequest->updateStatus($request->status, $officer->name, $request->remarks);

                    $this->logStatusUpdate($civilRequest, $request, $officer);

                    // Handle checklist items if status is "checklist_given"
                    $checklistItem = null;
                    if ($request->status === 'checklist_given' && $request->has('checklist_items') && is_array($request->checklist_items) && count($request->checklist_items) > 0) {
                        $checklistItem = $this->createChecklistItem($civilRequest, $request, $officer);
                    }
                    
                    // Handle missing requirements if status is "incomplete_requirements"
                    $missingRequirements = null;
                    $sendIncompleteRequirementsSMS = $request->boolean('send_incomplete_requirements_sms', false);
                    if ($request->status === 'incomplete_requirements' && $request->has('missing_requirements') && is_array($request->missing_requirements) && count($request->missing_requirements) > 0) {
                        $missingRequirements = $request->missing_requirements;
                        // Get incomplete requirements remarks (separate from main remarks)
                        $incompleteRemarks = $request->input('incomplete_requirements_remarks', '');
                        $existingRemarks = $request->remarks ?? '';
                        
                        // Store missing requirements in admin_notes
                        $missingItemsList = implode(', ', $missingRequirements);
                        $adminNotes = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $missingItemsList : 'Missing requirements: ' . $missingItemsList;
                        if ($incompleteRemarks) {
                            $adminNotes .= ' ' . $incompleteRemarks;
                        }
                        $civilRequest->admin_notes = $adminNotes;
                        
                        // Only append missing requirements to remarks if SMS is requested
                        if ($sendIncompleteRequirementsSMS) {
                            $smsRemarks = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $missingItemsList : 'Missing requirements: ' . $missingItemsList;
                            if ($incompleteRemarks) {
                                $smsRemarks .= ' ' . $incompleteRemarks;
                            }
                            $request->remarks = $smsRemarks;
                        } else {
                            // Keep only original remarks for SMS (missing requirements stored in admin_notes only)
                            $request->remarks = $existingRemarks;
                        }
                    }
                    
                    // Handle compliance requirements if status is "for_compliance"
                    $complianceRequirements = null;
                    $sendComplianceSMS = $request->boolean('send_compliance_sms', false);
                    if ($request->status === 'for_compliance' && $request->has('compliance_requirements') && is_array($request->compliance_requirements) && count($request->compliance_requirements) > 0) {
                        $complianceRequirements = $request->compliance_requirements;
                        // Get compliance remarks (separate from main remarks)
                        $complianceRemarks = $request->input('compliance_remarks', '');
                        $existingRemarks = $request->remarks ?? '';
                        
                        // Store compliance requirements in admin_notes
                        $complianceItemsList = implode(', ', $complianceRequirements);
                        $adminNotes = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $complianceItemsList : 'Missing requirements: ' . $complianceItemsList;
                        if ($complianceRemarks) {
                            $adminNotes .= ' ' . $complianceRemarks;
                        }
                        $civilRequest->admin_notes = $adminNotes;
                        
                        // Only append compliance requirements to remarks if SMS is requested
                        if ($sendComplianceSMS) {
                            $smsRemarks = $existingRemarks ? $existingRemarks . ' Missing requirements: ' . $complianceItemsList : 'Missing requirements: ' . $complianceItemsList;
                            if ($complianceRemarks) {
                                $smsRemarks .= ' ' . $complianceRemarks;
                            }
                            $request->remarks = $smsRemarks;
                        } else {
                            // Keep only original remarks for SMS (compliance requirements stored in admin_notes only)
                            $request->remarks = $existingRemarks;
                        }
                    }

            ActivityLogService::logStatusChange(
                $officer,
                'App\Models\CivilService',
                $civilRequest->id,
                $request->tracking_number,
                $oldStatus,
                $request->status,
                        $request->remarks,
                $request
            );

                    // Send SMS notification
                    try {
                        // For incomplete_requirements and for_compliance, only send SMS if checkbox is checked
                        $shouldSendSMS = true;
                        if ($request->status === 'incomplete_requirements' && !$sendIncompleteRequirementsSMS) {
                            $shouldSendSMS = false;
                        }
                        if ($request->status === 'for_compliance' && !$sendComplianceSMS) {
                            $shouldSendSMS = false;
                        }
                        
                        // Pass remarks to the request object temporarily so NotificationService can access it
                        $civilRequest->remarks = $request->remarks;
                        
                        // Always create notification, but conditionally send SMS
                        $notification = $notificationService->notifyStatusChange($civilRequest, $oldStatus, $request->status, $officer->id);
                        
                        // If SMS should not be sent, skip the SMS sending part
                        if (!$shouldSendSMS) {
                            // Update notification to indicate SMS was not sent
                            $notificationData = $notification->data ?? [];
                            $notificationData['sms_sent'] = false;
                            $notificationData['sms_skipped'] = true;
                            $skipReason = $request->status === 'incomplete_requirements' 
                                ? 'SMS not requested for incomplete requirements' 
                                : ($request->status === 'for_compliance' 
                                    ? 'SMS not requested for compliance requirements' 
                                    : 'SMS not requested');
                            $notificationData['sms_skip_reason'] = $skipReason;
                            $notification->update(['data' => $notificationData]);
                        }

            // Get the latest status update for timestamp
            $latestStatusUpdate = $civilRequest->statusUpdates()->latest()->first();
            $statusUpdateTime = $latestStatusUpdate 
                ? $latestStatusUpdate->created_at->format('M d, Y h:i A')
                : now()->format('M d, Y h:i A');
            
            // Prepare response with SMS status and timestamp
            $response = [
                'success' => true, 
                'message' => 'Status updated successfully.',
                'status' => $request->status,
                'status_formatted' => ucwords(str_replace('_', ' ', $request->status)),
                'updated_at' => $statusUpdateTime,
                'updated_timestamp' => $latestStatusUpdate?->created_at?->toIso8601String() ?? now()->toIso8601String()
            ];

                        // Add checklist information if created
                        if ($checklistItem) {
                            $response['checklist_created'] = true;
                            $response['checklist_sms_sent'] = $checklistItem->sms_status === 'sent';
                            $response['checklist_sms_status'] = $checklistItem->sms_status;
                            if ($checklistItem->sms_status === 'sent') {
                                $response['message'] = 'Status updated and checklist sent successfully!';
                            } elseif ($checklistItem->sms_status === 'failed') {
                                $response['message'] = 'Status updated successfully, but checklist SMS failed to send.';
                            } else {
                                $response['message'] = 'Status updated and checklist saved successfully!';
                            }
                        }

            // Check if SMS was sent successfully
            if ($notification && $notification->data) {
                $smsData = $notification->data;
                if (isset($smsData['sms_sent']) && $smsData['sms_sent']) {
                    $response['sms_sent'] = true;
                    $response['sms_message_id'] = $smsData['sms_message_id'] ?? null;
                    $response['sms_network'] = $smsData['sms_network'] ?? null;
                    $response['message'] .= "\n\n📱 SMS notification sent to client successfully.";
                } elseif (isset($smsData['sms_skipped']) && $smsData['sms_skipped']) {
                    $response['sms_sent'] = false;
                    $response['sms_skipped'] = true;
                    $response['sms_skip_reason'] = $smsData['sms_skip_reason'] ?? 'SMS not requested';
                } else {
                    $response['sms_sent'] = false;
                    $response['sms_error'] = $smsData['sms_error'] ?? 'SMS sending failed';
                    $response['message'] .= "\n\n⚠️ SMS notification failed: " . ($smsData['sms_error'] ?? 'Unknown error');
                                // Note: Status is still updated even if SMS fails (non-critical)
                }
            } else {
                $response['sms_sent'] = false;
                $response['sms_error'] = 'SMS notification not sent';
                $response['message'] .= "\n\n⚠️ SMS notification was not sent.";
                            // Note: Status is still updated even if SMS fails (non-critical)
            }

            return response()->json($response);
                    } catch (\Exception $smsException) {
                        // Log SMS error but don't rollback status update (SMS is non-critical)
                        \Log::error('SMS sending failed after status update', [
                            'tracking_number' => $request->tracking_number,
                            'error' => $smsException->getMessage(),
                            'trace' => $smsException->getTraceAsString()
                        ]);
                        
                        // Status update succeeded, but SMS failed
                        return response()->json([
                            'success' => true,
                            'message' => 'Status updated successfully, but SMS notification failed.',
                            'sms_sent' => false,
                            'sms_error' => $smsException->getMessage()
                        ]);
                    }
                });
            } catch (\InvalidArgumentException $e) {
                \Log::error('Status transition validation failed', [
                    'tracking_number' => $request->tracking_number,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Error updating Civil Service status', [
                    'tracking_number' => $request->tracking_number,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the status: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $request->tracking_number)->first();
        if ($otherRequest) {
            \Log::info('Processing Other Service status update', [
                'tracking_number' => $request->tracking_number,
                'current_status' => $otherRequest->status,
                'new_status' => $request->status
            ]);

            // Validate status is in allowed flow
            $allowedStatuses = OtherService::statusFlow();
            if (!in_array($request->status, $allowedStatuses)) {
                \Log::warning('Invalid status for Other Service', [
                    'tracking_number' => $request->tracking_number,
                    'requested_status' => $request->status,
                    'allowed_statuses' => $allowedStatuses
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. Allowed statuses: ' . implode(', ', array_map(function($s) {
                        return ucwords(str_replace('_', ' ', $s));
                    }, $allowedStatuses)),
                ], 422);
            }

            // Check if transition is allowed (prevents backwards and skipping steps)
            $flow = OtherService::statusFlow();
            $currentIndex = array_search($otherRequest->status, $flow, true);
            $nextIndex = array_search($request->status, $flow, true);
            
            // Handle old statuses (like 'submitted') - allow transition to inquiry
            if ($currentIndex === false && $request->status === 'inquiry') {
                \Log::info('Allowing correction transition from old status to inquiry', [
                    'old_status' => $otherRequest->status,
                    'new_status' => $request->status
                ]);
                // Allow this transition - will proceed to update
            } elseif (!OtherService::canTransition($otherRequest->status, $request->status)) {
                $currentLabel = ucwords(str_replace('_', ' ', $otherRequest->status));
                $nextLabel = ucwords(str_replace('_', ' ', $request->status));
                
                \Log::info('Status transition check for Other Service', [
                    'current_status' => $otherRequest->status,
                    'current_index' => $currentIndex,
                    'new_status' => $request->status,
                    'next_index' => $nextIndex,
                    'can_transition' => false
                ]);
                
                if ($currentIndex === false) {
                    // Current status is not in the flow - this might be an old status
                    return response()->json([
                        'success' => false,
                        'message' => "The current status '{$currentLabel}' is not recognized in the workflow. You can only transition to 'Inquiry'.",
                    ], 422);
                } elseif ($nextIndex === false) {
                    // This shouldn't happen since we already validated, but just in case
                    return response()->json([
                        'success' => false,
                        'message' => "The requested status '{$nextLabel}' is not valid for Other Services.",
                    ], 422);
                } elseif ($otherRequest->status === 'registered') {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot update status. 'Registered' is the final status and cannot be changed.",
                    ], 422);
                } elseif ($nextIndex < $currentIndex) {
                    // Prevent backwards transitions
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot revert status backwards from '{$currentLabel}' to '{$nextLabel}'. Allowed transitions: " . 
                            ($otherRequest->status === 'inquiry' ? 'Incomplete Requirements or Registered' : 
                             ($otherRequest->status === 'incomplete_requirements' ? 'Incomplete Requirements (loop) or Registered' : 'None')),
                    ], 422);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid status transition from '{$currentLabel}' to '{$nextLabel}'. " .
                            ($otherRequest->status === 'inquiry' ? 'You can move to Incomplete Requirements or Registered.' :
                             ($otherRequest->status === 'incomplete_requirements' ? 'You can loop to Incomplete Requirements or move to Registered.' : 'No transitions allowed.')),
                    ], 422);
                }
            }

            try {
                $oldStatus = $otherRequest->status;
                $otherRequest->updateStatus($request->status, $officer->name, $request->remarks);

                $this->logStatusUpdate($otherRequest, $request, $officer);

                // Handle checklist items if status is "checklist_given"
                $checklistItem = null;
                if ($request->status === 'checklist_given' && $request->has('checklist_items') && is_array($request->checklist_items) && count($request->checklist_items) > 0) {
                    $checklistItem = $this->createChecklistItem($otherRequest, $request, $officer);
                }

                ActivityLogService::logStatusChange(
                    $officer,
                    'App\Models\OtherService',
                    $otherRequest->id,
                    $request->tracking_number,
                    $oldStatus,
                    $request->status,
                    $request->remarks,
                    $request
                );

                // Pass remarks to the request object temporarily so NotificationService can access it
                $otherRequest->remarks = $request->remarks;
                $notification = $notificationService->notifyStatusChange($otherRequest, $oldStatus, $request->status, $officer->id);

                // Prepare response with SMS status
                $response = ['success' => true, 'message' => 'Status updated successfully.'];

                // Add checklist information if created
                if ($checklistItem) {
                    $response['checklist_created'] = true;
                    $response['checklist_sms_sent'] = $checklistItem->sms_status === 'sent';
                    $response['checklist_sms_status'] = $checklistItem->sms_status;
                    if ($checklistItem->sms_status === 'sent') {
                        $response['message'] = 'Status updated and checklist sent successfully!';
                    } elseif ($checklistItem->sms_status === 'failed') {
                        $response['message'] = 'Status updated successfully, but checklist SMS failed to send.';
                    } else {
                        $response['message'] = 'Status updated and checklist saved successfully!';
                    }
                }

                // Check if SMS was sent successfully
                if ($notification && $notification->data) {
                    $smsData = $notification->data;
                    if (isset($smsData['sms_sent']) && $smsData['sms_sent']) {
                        $response['sms_sent'] = true;
                        $response['sms_message_id'] = $smsData['sms_message_id'] ?? null;
                        $response['sms_network'] = $smsData['sms_network'] ?? null;
                    } else {
                        $response['sms_sent'] = false;
                        $response['sms_error'] = $smsData['sms_error'] ?? 'SMS sending failed';
                    }
                } else {
                    $response['sms_sent'] = false;
                    $response['sms_error'] = 'SMS notification not sent';
                }

                return response()->json($response);
            } catch (\InvalidArgumentException $e) {
                \Log::error('Status transition validation failed for Other Service', [
                    'tracking_number' => $request->tracking_number,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Error updating Other Service status', [
                    'tracking_number' => $request->tracking_number,
                    'status' => $request->status,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the status: ' . $e->getMessage(),
                ], 500);
            }
        }

        \Log::warning('Request not found for status update', [
            'tracking_number' => $request->tracking_number,
            'officer_id' => $officer->id,
            'officer_department' => $officer->department
        ]);

        return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
    }

    public function viewRequest($trackingNumber)
    {
        $officer = Auth::guard('admin')->user();

        // Try to find in civil_services first
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            // Get activity logs for this request
            $activityLogs = \App\Models\ActivityLog::where('target_type', 'App\Models\CivilService')
                ->where('target_id', $civilRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get SMS logs (we'll create a simple log for now)
            $smsLogs = $this->getSmsLogs($civilRequest);

            $statusUpdates = $civilRequest->statusUpdates()->get();
            $checklistItems = $civilRequest->checklistItems()->get();

            return view('admin.officer-dashboard.request-details', compact('civilRequest', 'officer', 'activityLogs', 'smsLogs', 'statusUpdates', 'checklistItems'));
        }

        // Try to find in other_services
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            // Get activity logs for this request
            $activityLogs = \App\Models\ActivityLog::where('target_type', 'App\Models\OtherService')
                ->where('target_id', $otherRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get SMS logs
            $smsLogs = $this->getSmsLogs($otherRequest);

            $statusUpdates = $otherRequest->statusUpdates()->get();
            $checklistItems = $otherRequest->checklistItems()->get();

            return view('admin.officer-dashboard.request-details', compact('otherRequest', 'officer', 'activityLogs', 'smsLogs', 'statusUpdates', 'checklistItems'));
        }

        return redirect()->back()->withErrors(['error' => 'Request not found.']);
    }

    /**
     * Get SMS logs for a request from the database
     */
    private function getSmsLogs($request)
    {
        // Fetch SMS logs from the database for this tracking number
        $smsLogs = \App\Models\SmsLog::where('tracking_number', $request->tracking_number)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'phone_number' => $log->phone_number,
                    'message' => $log->message,
                    'status' => $log->status,
                    'status_type' => $log->status_type,
                    'status_type_formatted' => ucwords(str_replace('_', ' ', $log->status_type ?? '')),
                    'message_id' => $log->message_id,
                    'network' => $log->network,
                    'sender' => $log->sender,
                    'error' => $log->error,
                    'sent_at' => $log->sent_at,
                    'created_at' => $log->created_at,
                    'response' => $log->status === 'sent' ? 'Message delivered successfully' : ($log->error ?? 'Failed to send')
                ];
            });

        return $smsLogs;
    }

    private function logStatusUpdate($model, Request $request, $officer): void
    {
        // Use provided status_date or current date/time
        $statusDate = $request->status_date 
            ? \Carbon\Carbon::parse($request->status_date)->toDateString()
            : now()->toDateString();
        
        StatusUpdate::create([
            'statusable_id' => $model->id,
            'statusable_type' => get_class($model),
            'status' => $request->status,
            'status_date' => $statusDate,
            'remarks' => $request->remarks,
            'admin_id' => $officer->id,
        ]);
        
        \Log::info('Status update logged', [
            'tracking_number' => $model->tracking_number ?? 'N/A',
            'status' => $request->status,
            'status_date' => $statusDate,
            'officer' => $officer->name,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Create checklist item and send SMS if requested
     */
    private function createChecklistItem($model, Request $request, $officer)
    {
        try {
            $checklistItems = $request->checklist_items ?? [];
            $checklistRemarks = $request->checklist_remarks ?? null;
            $sendSms = $request->boolean('send_checklist_sms', false);

            // Validate at least one item is selected
            if (empty($checklistItems) || count($checklistItems) === 0) {
                throw new \Exception('At least one checklist item must be selected.');
            }

            // Get contact number
            $contactNumber = $model->contact_number ?? $model->phone ?? $model->phone_number ?? null;
            
            if ($sendSms && !$contactNumber) {
                throw new \Exception('Cannot send SMS: Client contact number is not available.');
            }

            // Create checklist item
            $checklistItem = ChecklistItem::create([
                'checklistable_id' => $model->id,
                'checklistable_type' => get_class($model),
                'items' => $checklistItems,
                'remarks' => $checklistRemarks,
                'admin_id' => $officer->id,
                'phone_number' => $contactNumber,
                'sms_status' => $sendSms ? 'pending' : null,
            ]);

            // Generate SMS message
            $smsMessage = $checklistItem->generateSMSMessage(
                $model->tracking_number,
                $model->full_name ?? null
            );
            $checklistItem->sms_message = $smsMessage;
            $checklistItem->save();

            // Send SMS if requested (with retry mechanism)
            if ($sendSms && $contactNumber) {
                $smsSent = false;
                $maxRetries = 3;
                $retryCount = 0;

                while (!$smsSent && $retryCount < $maxRetries) {
                    try {
                        $nodeSmsService = new \App\Services\NodeSmsService();
                        $result = $nodeSmsService->sendSMSDirect($contactNumber, $smsMessage);

                        if ($result['success'] ?? false) {
                            $checklistItem->sms_status = 'sent';
                            $checklistItem->sent_at = now();
                            $checklistItem->save();
                            $smsSent = true;

                            Log::info('Checklist SMS sent successfully', [
                                'checklist_item_id' => $checklistItem->id,
                                'tracking_number' => $model->tracking_number,
                                'phone_number' => $contactNumber,
                                'retry_count' => $retryCount + 1,
                            ]);
                        } else {
                            $retryCount++;
                            if ($retryCount < $maxRetries) {
                                sleep(1); // Wait 1 second before retry
                            }
                        }
                    } catch (\Exception $smsException) {
                        $retryCount++;
                        Log::warning('Checklist SMS sending attempt failed', [
                            'checklist_item_id' => $checklistItem->id,
                            'retry_count' => $retryCount,
                            'error' => $smsException->getMessage(),
                        ]);

                        if ($retryCount >= $maxRetries) {
                            $checklistItem->sms_status = 'failed';
                            $checklistItem->save();
                        } else {
                            sleep(1); // Wait 1 second before retry
                        }
                    }
                }

                if (!$smsSent) {
                    Log::error('Checklist SMS failed after all retries', [
                        'checklist_item_id' => $checklistItem->id,
                        'tracking_number' => $model->tracking_number,
                        'phone_number' => $contactNumber,
                        'max_retries' => $maxRetries,
                    ]);
                }
            }

            return $checklistItem;
        } catch (\Exception $e) {
            Log::error('Failed to create checklist item', [
                'error' => $e->getMessage(),
                'tracking_number' => $model->tracking_number ?? null,
            ]);
            throw $e;
        }
    }

    public function markNotificationAsRead(Request $request)
    {
        $notification = \App\Models\Notification::findOrFail($request->notification_id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        \App\Models\Notification::unread()->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function getNotifications(Request $request)
    {
        try {
            $officer = Auth::guard('admin')->user();

            // Get notifications for this officer
            $notifications = \App\Models\Notification::where('user_id', $officer->id)
                ->where('user_type', 'officer')
                ->orderBy('created_at', 'desc')
                ->get();

            $unreadCount = $notifications->whereNull('read_at')->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications', [
                'officer_id' => Auth::guard('admin')->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    private function getDepartmentServiceTypes($officer)
    {
        return ServiceType::whereHas('service', function ($query) use ($officer) {
            $query->where('department', $officer->department)
                ->where('is_active', true);
        })->pluck('name')
            ->toArray();
    }


    /**
     * Show approval requests page (now 10 Days Posting page)
     */
    public function showApprovalRequests()
    {
        $officer = Auth::guard('admin')->user();

        // Only Civil Registry officers can manage approval requests
        if ($officer->department !== 'Civil Registry Office') {
            return redirect()->back()->withErrors(['error' => 'Access denied. Only Civil Registry officers can manage this page.']);
        }

        return view('admin.officer-dashboard.approval-requests', compact('officer'));
    }

    /**
     * Get all requests currently in 10 days posting period
     * This includes both Delayed Regular and Delayed BRAP requests with 'posting' or 'accepted' status
     */
    public function getPostingRequests()
    {
        $officer = Auth::guard('admin')->user();

        // Only Civil Registry officers can view posting requests
        if ($officer->department !== 'Civil Registry Office') {
            return response()->json(['requests' => []]);
        }

        // Get all requests that are in posting period
        // Statuses that indicate posting: 'posting', 'accepted', 'positive_result' (all with posting_started_at)
        $postingRequests = CivilService::where(function ($query) {
                // Requests with 'posting' status
                $query->where('status', 'posting')
                    // OR requests in 'accepted' status that have posting_started_at (in posting phase)
                    ->orWhere(function ($q) {
                        $q->where('status', 'accepted')
                          ->whereNotNull('posting_started_at');
                    })
                    // OR requests in 'positive_result' status that have posting_started_at (in posting phase)
                    ->orWhere(function ($q) {
                        $q->where('status', 'positive_result')
                          ->whereNotNull('posting_started_at');
                    });
            })
            ->where(function ($query) {
                // Only include Delayed Birth Registration services (Regular and BRAP)
                $query->where('service_name', 'LIKE', '%Delayed%');
            })
            ->orderBy('posting_started_at', 'asc')
            ->get();

        $formattedRequests = $postingRequests->map(function ($request) {
            $serviceName = $request->service_name ?? '';
            
            // Determine if this is Delayed BRAP or Delayed Regular
            $isDelayedBrap = stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false;
            
            // Calculate days remaining - use stored posting_days if available, otherwise use current setting
            $postingDays = $request->posting_days ?? Setting::getPostingDays($request->department ?? 'Civil Registry Office', 10);
            $daysRemaining = $postingDays; // Default
            if ($request->posting_started_at) {
                $postingStart = \Carbon\Carbon::parse($request->posting_started_at);
                $postingEnd = $postingStart->copy()->addDays($postingDays);
                $now = \Carbon\Carbon::now();
                
                if ($now->gte($postingEnd)) {
                    $daysRemaining = 0;
                } else {
                    $daysRemaining = $now->diffInDays($postingEnd, false);
                    $daysRemaining = max(0, ceil($daysRemaining));
                }
            }
            
            return [
                'id' => $request->id,
                'tracking_number' => $request->tracking_number,
                'full_name' => $request->full_name,
                'contact_number' => $request->contact_number,
                'service_name' => $serviceName,
                'service_type' => $request->service_type,
                'status' => $request->status,
                'is_delayed_brap' => $isDelayedBrap,
                'posting_started_at' => $request->posting_started_at ? $request->posting_started_at->toISOString() : null,
                'accepted_at' => $request->accepted_at ? $request->accepted_at->toISOString() : null,
                'posting_days' => $postingDays,
                'days_remaining' => $daysRemaining,
                'created_at' => $request->created_at->toISOString(),
            ];
        });

        return response()->json([
            'requests' => $formattedRequests,
            'total' => $formattedRequests->count()
        ]);
    }

    /**
     * Check for requests ready for approval (10-day posting completed)
     */
    public function checkForApprovalRequests()
    {
        $officer = Auth::guard('admin')->user();

        // Only Civil Registry officers can manage approval requests
        if ($officer->department !== 'Civil Registry Office') {
            return response()->json(['requests' => []]);
        }

        // Get requests ready for approval
        // Regular BRAP: posting -> ready_for_approval -> registered flow
        // Delayed BRAP: accepted -> ready_for_approval -> registered flow (10 calendar days after accepted)
        $readyForApproval = CivilService::where(function ($query) {
                // Check for regular BRAP requests in posting status
                $query->where('status', 'posting')
                    ->orWhere(function ($q) {
                        // Or requests in accepted status that should be in posting (legacy data)
                        $q->where('status', 'accepted')
                          ->where('posting_started_at', '!=', null);
                    })
                    // OR Delayed BRAP requests in accepted status (waiting for 10 calendar days)
                    ->orWhere(function ($q) {
                        $q->where('status', 'accepted')
                          ->where('service_name', 'Delayed BRAP Birth Registration Assistance Project');
                    });
            })
            ->get()
            ->filter(function ($request) {
                $serviceName = $request->service_name ?? '';
                $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
                
                // For Delayed BRAP: Must be in accepted status and ready for approval (10 calendar days)
                if ($isDelayedBrap) {
                    return $request->status === 'accepted' && $request->isReadyForApproval();
                }
                
                // For regular BRAP: Must have posting_started_at and be ready for approval
                $isRegularBrap = (stripos($serviceName, 'Non-BRAP') === false && stripos($serviceName, 'BRAP') !== false) 
                               || $request->request_type === 'brap';
                
                if ($isRegularBrap) {
                    return $request->posting_started_at !== null && $request->isReadyForApproval();
                }
                
                return false;
            });

        // Mark new requests as ready for approval
        $newlyReady = $readyForApproval->filter(function ($request) {
            $serviceName = $request->service_name ?? '';
            $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
            
            // For Delayed BRAP: Must be in accepted status (not yet ready_for_approval)
            if ($isDelayedBrap) {
                return $request->status === 'accepted';
            }
            
            // For regular BRAP: Must not be ready_for_approval and posting_ended_at is null
            return $request->status !== 'ready_for_approval' && $request->posting_ended_at === null;
        });

        foreach ($newlyReady as $request) {
            $serviceName = $request->service_name ?? '';
            $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
            
            if ($isDelayedBrap) {
                // For Delayed BRAP: Mark as ready_for_approval (no posting period)
                $request->status = 'ready_for_approval';
                $request->posting_ended_at = now(); // Set posting_ended_at to track when it became ready
                $request->save();
                
                // Create status update
                \App\Models\StatusUpdate::create([
                    'statusable_id' => $request->id,
                    'statusable_type' => get_class($request),
                    'status' => 'ready_for_approval',
                    'status_date' => now()->toDateString(),
                    'remarks' => 'Request ready for approval after 10 calendar days from acceptance',
                    'admin_id' => $officer->id,
                ]);
            } else {
                // For regular BRAP: Use existing markForApproval method
                $request->markForApproval();
            }

            // Send notification to officer
            $notificationService = new NotificationService();
            $notificationService->notifyApprovalReady($request, $officer->id);
        }

        // Also get requests that have already been marked as ready_for_approval but not yet approved
        // (status will be 'registered' after approval, so these are still pending)
        $alreadyMarked = CivilService::where('status', 'ready_for_approval')
            ->get()
            ->filter(function ($request) {
                $serviceName = $request->service_name ?? '';
                $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
                $isRegularBrap = (stripos($serviceName, 'Non-BRAP') === false && stripos($serviceName, 'BRAP') !== false) 
                               || $request->request_type === 'brap';
                
                // Include both Delayed BRAP and regular BRAP requests
                return $isDelayedBrap || ($isRegularBrap && $request->posting_ended_at !== null);
            });

        // Combine both sets of requests
        $allReadyRequests = $readyForApproval->concat($alreadyMarked)->unique('id');

        return response()->json([
            'requests' => $allReadyRequests->map(function ($request) {
                $serviceName = $request->service_name ?? '';
                $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
                
                return [
                    'id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'full_name' => $request->full_name,
                    'service_name' => $request->service_name,
                    'service_type' => $request->service_type,
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'posting_started_at' => $request->posting_started_at ? $request->posting_started_at->format('Y-m-d H:i:s') : null,
                    'posting_ended_at' => $request->posting_ended_at ? $request->posting_ended_at->format('Y-m-d H:i:s') : null,
                    'accepted_at' => $request->accepted_at ? $request->accepted_at->format('Y-m-d H:i:s') : null,
                    'status' => $request->status,
                    'is_delayed_brap' => $isDelayedBrap,
                    'days_waiting' => $isDelayedBrap && $request->accepted_at 
                        ? $request->accepted_at->diffInDays(now()) 
                        : ($request->posting_started_at ? $request->posting_started_at->diffInDays(now()) : 0),
                ];
            })->values()->toArray()
        ]);
    }

    /**
     * Approve a request
     */
    public function approveRequest(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $officer = Auth::guard('admin')->user();
        $notificationService = new NotificationService();

        // Find the civil service request
        $civilRequest = CivilService::where('tracking_number', $request->tracking_number)->first();

        if (!$civilRequest) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        // Verify this is a BRAP request (Delayed BRAP or regular BRAP)
        $serviceName = $civilRequest->service_name ?? '';
        $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project';
        $isRegularBrap = (stripos($serviceName, 'Non-BRAP') === false && stripos($serviceName, 'BRAP') !== false) 
                      || $civilRequest->request_type === 'brap';
        
        if (!$isDelayedBrap && !$isRegularBrap) {
            return response()->json(['success' => false, 'message' => 'Only BRAP requests require approval.'], 400);
        }
        
        if ($civilRequest->status !== 'ready_for_approval') {
            return response()->json(['success' => false, 'message' => 'Request is not ready for approval. Current status: ' . ucwords(str_replace('_', ' ', $civilRequest->status))], 400);
        }

        // Validate 10-day posting period before allowing status to be set to "registered"
        $postingValidation = $this->validatePostingPeriod($civilRequest);
        if (!$postingValidation['valid']) {
            \Log::warning('Posting period validation failed for approval request', [
                'tracking_number' => $request->tracking_number,
                'message' => $postingValidation['message'],
                'days_remaining' => $postingValidation['days_remaining']
            ]);

            return response()->json([
                'success' => false,
                'message' => $postingValidation['message'],
                'error_type' => 'posting_period_not_completed',
                'days_remaining' => $postingValidation['days_remaining'] ?? null,
            ], 422);
        }

        try {
            // Use database transaction to ensure atomicity
            return DB::transaction(function () use ($civilRequest, $request, $officer, $notificationService) {
            $oldStatus = $civilRequest->status;
                
                // Update status
                $civilRequest->updateStatus('registered', $officer->name, 'Request approved and registered by officer');

                // Create StatusUpdate record
                StatusUpdate::create([
                    'statusable_id' => $civilRequest->id,
                    'statusable_type' => get_class($civilRequest),
                    'status' => 'registered',
                    'status_date' => now()->toDateString(),
                    'remarks' => 'Request approved and registered by officer',
                    'admin_id' => $officer->id,
                ]);

            // Log activity
            ActivityLogService::logStatusChange(
                $officer,
                'App\Models\CivilService',
                $civilRequest->id,
                $request->tracking_number,
                $oldStatus,
                'registered',
                'Request approved and registered by officer',
                $request
            );

                // Send notification to officer admin
                $notificationService->notifyStatusChange($civilRequest, $oldStatus, 'registered', $officer->id);

                // Send SMS notification to client (non-critical, catch errors)
                try {
                    $notificationService->notifyClientViaSMS($civilRequest, 'registered');
                    $smsStatus = 'sent';
                } catch (\Exception $smsError) {
                    Log::warning('SMS sending failed for approved request', [
                        'tracking_number' => $request->tracking_number,
                        'error' => $smsError->getMessage()
                    ]);
                    $smsStatus = 'failed';
                }

            return response()->json([
                'success' => true,
                    'message' => 'Request approved and registered successfully.' . ($smsStatus === 'failed' ? ' Note: SMS notification failed to send.' : ''),
            ]);
            });

        } catch (\Exception $e) {
            Log::error('Error approving request', [
                'tracking_number' => $request->tracking_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error approving request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show queue number toggle settings page for officers
     */
    public function showQueueNumberSettings()
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;
        
        // Get department-specific setting
        $queueNumberEnabled = Setting::getQueueNumberSettingForDepartment($department, false);
        
        // Get custom display names
        $officeDisplayName = Setting::getQueueDisplayOfficeName($department, $department);
        
        // Get windows configuration
        $windows = Setting::getQueueWindows($department);
        
        // Get column configurations
        $columns = Setting::getQueueColumns($department);
        
        // Get column names (for backward compatibility)
        $columnNames = Setting::getQueueColumnNames($department);
        
        // Get queue number prefix (only use default if not set in database)
        // Pass null as default so we can distinguish between "not set" and "set to empty"
        $defaultPrefix = $this->generateDefaultPrefix($department);
        $savedPrefix = Setting::getQueueNumberPrefix($department, null);
        // If no custom prefix is set (null), use default for display
        // If it's an empty string, that means user cleared it, so use empty
        $queuePrefix = $savedPrefix !== null ? $savedPrefix : $defaultPrefix;

        return view('admin.officer-dashboard.queue-number-settings', [
            'queueNumberEnabled' => $queueNumberEnabled,
            'officer' => $officer,
            'department' => $department,
            'officeDisplayName' => $officeDisplayName,
            'windows' => $windows,
            'columns' => $columns,
            'columnNames' => $columnNames,
            'queuePrefix' => $queuePrefix,
        ]);
    }

    /**
     * Update queue number toggle setting (department-specific)
     */
    public function updateQueueNumberSettings(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;
        
        // Checkbox sends '1' when checked, nothing when unchecked
        // So we check if 'enabled' exists in the request
        $enabled = $request->has('enabled') && $request->input('enabled') == '1';

        // Set department-specific setting
        Setting::setQueueNumberSettingForDepartment($department, $enabled);
        
        // Update office display name
        $officeDisplayName = $request->input('office_display_name');
        if ($officeDisplayName !== null) {
            // Trim and convert empty strings to null
            $officeDisplayName = trim($officeDisplayName);
            Setting::setQueueDisplayOfficeName($department, $officeDisplayName ?: null);
        }
        
        // Update windows configuration
        $windowsJson = $request->input('windows_json');
        if ($windowsJson !== null) {
            $windows = json_decode($windowsJson, true);
            if (is_array($windows)) {
                // Validate and clean windows data
                $validatedWindows = [];
                foreach ($windows as $window) {
                    if (isset($window['number']) && isset($window['name']) && isset($window['active'])) {
                        $validatedWindows[] = [
                            'number' => (int)$window['number'],
                            'name' => trim($window['name']) ?: "Window {$window['number']}",
                            'active' => (bool)$window['active']
                        ];
                    }
                }
                // Sort by window number
                usort($validatedWindows, function($a, $b) {
                    return $a['number'] <=> $b['number'];
                });
                Setting::setQueueWindows($department, $validatedWindows);
            }
        }
        
        // Update column configurations (dynamic columns)
        $columnsJson = $request->input('columns_json');
        if ($columnsJson !== null) {
            $columns = json_decode($columnsJson, true);
            if (is_array($columns)) {
                Setting::setQueueColumns($department, $columns);
            }
        } else {
            // Fallback: Update individual column names for backward compatibility
            $onQueueColumn = $request->input('column_on_queue');
            $toSubmitColumn = $request->input('column_to_submit');
            $processedColumn = $request->input('column_processed');
            
            if ($onQueueColumn !== null || $toSubmitColumn !== null || $processedColumn !== null) {
                // Convert old format to new format
                $existingColumns = Setting::getQueueColumns($department);
                foreach ($existingColumns as &$col) {
                    if ($col['key'] === 'on_queue' && $onQueueColumn !== null) {
                        $col['name'] = $onQueueColumn;
                    } elseif ($col['key'] === 'to_submit' && $toSubmitColumn !== null) {
                        $col['name'] = $toSubmitColumn;
                    } elseif ($col['key'] === 'processed' && $processedColumn !== null) {
                        $col['name'] = $processedColumn;
                    }
                }
                Setting::setQueueColumns($department, $existingColumns);
            }
        }
        
        // Update queue number prefix
        $queuePrefix = $request->input('queue_prefix');
        if ($queuePrefix !== null) {
            Setting::setQueueNumberPrefix($department, $queuePrefix);
        }

        // Log the change
        ActivityLogService::log(
            $officer,
            'setting_updated',
            'Queue number feature ' . ($enabled ? 'enabled' : 'disabled') . ' for ' . $department . ' by officer',
            'App\Models\Setting',
            null, // No specific target ID for settings
            [
                'feature' => 'feature.queuing_number.' . str_replace(' ', '_', strtolower($department)),
                'enabled' => $enabled,
                'department' => $department,
            ],
            $request
        );

        return redirect()
            ->route('officer.queue-number-settings')
            ->with('status', 'Queue settings updated successfully for ' . $department . '.');
    }

    /**
     * Get queue data for today's requests
     */
    public function getQueueData(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;

        // Check if queue is enabled for this department
        $queueEnabled = Setting::getQueueNumberSettingForDepartment($department, false);
        
        if (!$queueEnabled) {
            return response()->json([
                'success' => false,
                'message' => 'Queue number feature is not enabled for this department',
                'data' => []
            ]);
        }

        // Get window number from request or officer (if window is stored in admin table)
        // For now, we'll get it from request parameter or use a default
        $windowNumber = $request->input('window', null);
        
        // If no window specified in request, try to get from officer's window setting
        // This assumes a window_number field might be added to admins table
        // For now, we'll use a setting or request parameter
        if ($windowNumber === null) {
            $windowNumber = $request->input('window_number', null);
        }

        $today = now()->format('Y-m-d');
        $queueData = [];

        if ($department === 'Civil Registry Office') {
            // Get Civil Service requests with queue numbers for today
            // Cast queue_number to integer for proper ordering
            // Include ALL statuses - the frontend will categorize them
            $query = CivilService::whereDate('created_at', $today)
                ->whereNotNull('queue_number')
                ->where('queue_number', '!=', '');
            
            // Filter by window if specified
            if ($windowNumber !== null) {
                $query->where('window_number', $windowNumber);
            }
            
            $civilRequests = $query->orderByRaw('CAST(queue_number AS UNSIGNED) ASC')->get();

            \Log::info('Queue data fetched', [
                'department' => $department,
                'date' => $today,
                'window_number' => $windowNumber,
                'total_requests' => $civilRequests->count(),
                'requests' => $civilRequests->map(function($r) {
                    return [
                        'tracking_number' => $r->tracking_number,
                        'queue_number' => $r->queue_number,
                        'status' => $r->status,
                        'window_number' => $r->window_number ?? null,
                        'called_at' => $r->called_at ?? null,
                        'created_at' => $r->created_at->format('Y-m-d H:i:s')
                    ];
                })->toArray()
            ]);

            foreach ($civilRequests as $request) {
                // Prioritize service_name, but filter out generic department names
                $serviceDisplay = $request->service_name ?? $request->service_type ?? 'N/A';
                
                // If service_name contains generic department name, prefer service_type if it's more specific
                $genericNames = ['City Civil Registration Services', 'Civil Registry Office', 'Civil Registry'];
                if (in_array($serviceDisplay, $genericNames) && $request->service_type && !in_array($request->service_type, $genericNames)) {
                    $serviceDisplay = $request->service_type;
                }
                
                $queueData[] = [
                    'queue_number' => (int) $request->queue_number,
                    'tracking_number' => $request->tracking_number,
                    'client_name' => $request->full_name,
                    'service_type' => $serviceDisplay,
                    'status' => $request->status,
                    'time_submitted' => $request->created_at->format('H:i:s'),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'department' => $department,
                    'window_number' => $request->window_number ?? null,
                    'called_at' => $request->called_at ? $request->called_at->format('Y-m-d H:i:s') : null,
                    'is_called' => $request->called_at !== null,
                ];
            }
        } else {
            // Get Other Service requests with queue numbers for today
            // Cast queue_number to integer for proper ordering
            $query = OtherService::where('department', $department)
                ->whereDate('created_at', $today)
                ->whereNotNull('queue_number');
            
            // Filter by window if specified
            // Note: We filter by window_number even if window is inactive, to show existing queues
            // New queue assignments should only allow active windows, but existing queues remain accessible
            if ($windowNumber !== null) {
                $query->where('window_number', $windowNumber);
            }
            
            $otherRequests = $query->orderByRaw('CAST(queue_number AS UNSIGNED) ASC')->get();

            foreach ($otherRequests as $request) {
                // Prioritize service_name for OtherService as well
                $serviceDisplay = $request->service_name ?? $request->service_type ?? 'N/A';
                
                $queueData[] = [
                    'queue_number' => (int) $request->queue_number,
                    'tracking_number' => $request->tracking_number,
                    'client_name' => $request->full_name,
                    'service_type' => $serviceDisplay,
                    'status' => $request->status,
                    'time_submitted' => $request->created_at->format('H:i:s'),
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'department' => $department,
                    'window_number' => $request->window_number ?? null,
                    'called_at' => $request->called_at ? $request->called_at->format('Y-m-d H:i:s') : null,
                    'is_called' => $request->called_at !== null,
                ];
            }
        }

        // Always get display names (even if null, so frontend knows to use defaults)
        $officeDisplayName = Setting::getQueueDisplayOfficeName($department, $department);
        $windowDisplayName = null;
        if ($windowNumber !== null) {
            // Get window name using helper method (works for both active and inactive windows)
            $windowDisplayName = Setting::getWindowNameByNumber($department, (int)$windowNumber);
        }
        
        // Get column configurations
        $columns = Setting::getQueueColumns($department);
        $columnNames = Setting::getQueueColumnNames($department);
        
        // Get queue number prefix (generate default from department name if not set)
        $defaultPrefix = $this->generateDefaultPrefix($department);
        $queuePrefix = Setting::getQueueNumberPrefix($department, $defaultPrefix);

        return response()->json([
            'success' => true,
            'data' => $queueData,
            'total' => count($queueData),
            'date' => $today,
            'department' => $department,
            'window_number' => $windowNumber,
            'office_display_name' => ($officeDisplayName && trim($officeDisplayName) !== '') ? trim($officeDisplayName) : $department, // Always return a value
            'window_display_name' => ($windowDisplayName && trim($windowDisplayName) !== '') ? trim($windowDisplayName) : null, // Can be null if no window filter
            'columns' => $columns, // Full column configurations with statuses
            'column_names' => $columnNames, // Column display names (backward compatibility)
            'queue_prefix' => ($queuePrefix && trim($queuePrefix) !== '') ? trim($queuePrefix) : $defaultPrefix, // Queue number prefix
        ]);
    }

    /**
     * Show queue display screen for TV/monitor
     */
    public function showQueueDisplay()
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;

        // Check if queue is enabled for this department
        $queueEnabled = Setting::getQueueNumberSettingForDepartment($department, false);
        
        if (!$queueEnabled) {
            abort(404, 'Queue display is not available for this department');
        }

        // Get custom display names
        $officeDisplayName = Setting::getQueueDisplayOfficeName($department, $department);
        $windowNumber = request()->input('window', null);
        $windowDisplayName = null;
        if ($windowNumber) {
            // Get window name using helper method (works for both active and inactive windows)
            $windowDisplayName = Setting::getWindowNameByNumber($department, (int)$windowNumber);
        }

        return view('admin.officer-dashboard.queue-display', [
            'officer' => $officer,
            'department' => $department,
            'officeDisplayName' => ($officeDisplayName && trim($officeDisplayName) !== '') ? trim($officeDisplayName) : $department,
            'windowNumber' => $windowNumber,
            'windowDisplayName' => ($windowDisplayName && trim($windowDisplayName) !== '') ? trim($windowDisplayName) : null,
        ]);
    }

    /**
     * Generate default prefix from department name
     */
    private function generateDefaultPrefix(string $department): string
    {
        $departmentMap = [
            'Business Permit and Licensing Office' => 'BPLO',
            'City Transportation Regulation Unit' => 'CTRU',
            'City Engineering Office' => 'CEO',
            'Civil Registry Office' => 'CRO',
        ];
        
        if (isset($departmentMap[$department])) {
            return $departmentMap[$department];
        }
        
        // Fallback: Generate code from department name (first letters of each word)
        $words = explode(' ', trim($department));
        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 4));
        }
        return strtoupper(substr(implode('', array_map(function($word) {
            return $word[0] ?? '';
        }, $words)), 0, 4));
    }

    /**
     * Call a queue (mark as called)
     */
    public function callQueue(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $trackingNumber = $request->input('tracking_number');
        $windowNumber = $request->input('window_number', null);

        if (!$trackingNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking number is required'
            ], 400);
        }

        // Try to find in CivilService
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            $civilRequest->called_at = now();
            if ($windowNumber !== null) {
                $civilRequest->window_number = $windowNumber;
            }
            $civilRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue called successfully',
                'data' => [
                    'tracking_number' => $trackingNumber,
                    'called_at' => $civilRequest->called_at->format('Y-m-d H:i:s'),
                    'window_number' => $civilRequest->window_number
                ]
            ]);
        }

        // Try to find in OtherService
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            $otherRequest->called_at = now();
            if ($windowNumber !== null) {
                $otherRequest->window_number = $windowNumber;
            }
            $otherRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue called successfully',
                'data' => [
                    'tracking_number' => $trackingNumber,
                    'called_at' => $otherRequest->called_at->format('Y-m-d H:i:s'),
                    'window_number' => $otherRequest->window_number
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Request not found'
        ], 404);
    }

    /**
     * Printable requests list (per department, per-source)
     */
    public function printRequests(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;

        // Determine source model per department
        $isCivil = str_contains(strtolower($department), 'civil');
        $query = $isCivil ? CivilService::query() : OtherService::query();

        // Always scope to officer's department
        $query->where('department', $department);

        // Filters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Order newest first
        $requests = $query->orderBy('created_at', 'desc')->get()->map(function ($r) use ($department, $isCivil) {
            return [
                'tracking_number' => $r->tracking_number,
                'name' => $r->full_name ?? $r->client_name ?? 'N/A',
                'service_type' => $r->service_name ?? $r->service_type ?? 'N/A',
                'status' => $r->status ?? 'N/A',
                'submitted_at' => $r->created_at ? $r->created_at->format('Y-m-d H:i') : 'N/A',
                'window_number' => $r->window_number ?? null,
                'queue_number' => $r->queue_number ?? null,
            ];
        });

        // Distinct statuses for filter dropdown (from this source + dept)
        $statuses = $query->clone()->select('status')->distinct()->pluck('status')->filter()->values();

        return view('admin.officer-dashboard.reports.requests-print', [
            'officer' => $officer,
            'department' => $department,
            'requests' => $requests,
            'statuses' => $statuses,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status ?? 'all',
            ],
        ]);
    }

    /**
     * Remove a request from the queue (set queue_number to null)
     * This only affects the queue display, not the actual request data
     */
    public function removeFromQueue(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $trackingNumber = $request->input('tracking_number');

        if (!$trackingNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking number is required'
            ], 400);
        }

        // Try to find in CivilService
        $civilRequest = CivilService::where('tracking_number', $trackingNumber)->first();
        if ($civilRequest) {
            // Only remove if it belongs to the officer's department
            if ($civilRequest->department !== $officer->department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Request does not belong to your department'
                ], 403);
            }

            // Store original values before updating
            $previousQueueNumber = $civilRequest->queue_number;
            
            $civilRequest->queue_number = null;
            $civilRequest->window_number = null;
            $civilRequest->called_at = null;
            $civilRequest->save();

            // Log the action
            ActivityLogService::log(
                $officer,
                'queue_removed',
                "Removed request {$trackingNumber} from queue",
                'App\Models\CivilService',
                $civilRequest->id,
                [
                    'tracking_number' => $trackingNumber,
                    'previous_queue_number' => $previousQueueNumber,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Request removed from queue successfully',
                'data' => [
                    'tracking_number' => $trackingNumber,
                ]
            ]);
        }

        // Try to find in OtherService
        $otherRequest = OtherService::where('tracking_number', $trackingNumber)->first();
        if ($otherRequest) {
            // Only remove if it belongs to the officer's department
            if ($otherRequest->department !== $officer->department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Request does not belong to your department'
                ], 403);
            }

            // Store original values before updating
            $previousQueueNumber = $otherRequest->queue_number;
            
            $otherRequest->queue_number = null;
            $otherRequest->window_number = null;
            $otherRequest->called_at = null;
            $otherRequest->save();

            // Log the action
            ActivityLogService::log(
                $officer,
                'queue_removed',
                "Removed request {$trackingNumber} from queue",
                'App\Models\OtherService',
                $otherRequest->id,
                [
                    'tracking_number' => $trackingNumber,
                    'previous_queue_number' => $previousQueueNumber,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Request removed from queue successfully',
                'data' => [
                    'tracking_number' => $trackingNumber,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Request not found'
        ], 404);
    }

    /**
     * Validate that 10 calendar days have passed for posting period
     * Returns array with 'valid' (bool), 'message' (string), and 'days_remaining' (int|null)
     */
    private function validatePostingPeriod($civilRequest)
    {
        $serviceName = $civilRequest->service_name ?? '';
        $isDelayedBrap = stripos($serviceName, 'Delayed') !== false && 
                        stripos($serviceName, 'BRAP') !== false && 
                        stripos($serviceName, 'Non-BRAP') === false;
        $isDelayedRegular = stripos($serviceName, 'Delayed') !== false && 
                           stripos($serviceName, 'Non-BRAP') !== false;
        
        // Determine the start date for posting period
        $startDate = null;
        if ($isDelayedBrap) {
            // For Delayed BRAP: Use accepted_at as the start date
            $startDate = $civilRequest->accepted_at;
        } elseif ($isDelayedRegular) {
            // For Delayed Regular: Use posting_started_at or accepted_at
            $startDate = $civilRequest->posting_started_at ?? $civilRequest->accepted_at;
        } else {
            // For regular BRAP: Use posting_started_at
            $startDate = $civilRequest->posting_started_at;
        }

        // If no start date is found, cannot proceed
        if (!$startDate) {
            return [
                'valid' => false,
                'message' => 'Cannot update status to "Registered". Posting period has not started. Please ensure the request has been accepted or posted first.',
                'days_remaining' => null
            ];
        }

        // Get posting days - use stored value if available, otherwise use current setting
        $postingDays = $civilRequest->posting_days ?? Setting::getPostingDays($civilRequest->department ?? 'Civil Registry Office', 10);
        
        // Calculate posting days from start date
        // The posting period starts on the start date and ends after the configured posting days
        $startDateCarbon = \Carbon\Carbon::parse($startDate);
        $endDate = $startDateCarbon->copy()->addDays($postingDays);
        
        // If holiday exclusion is enabled, we need to add extra days for holidays
        if ($civilRequest->is_holiday_excluded) {
            $calendarDays = 0;
            $currentDate = $startDateCarbon->copy();
            $endDate = $startDateCarbon->copy();
            
            // Count configured posting days, skipping holidays
            while ($calendarDays < $postingDays) {
                $endDate->addDay();
                // Skip holidays
                if (!$this->isHoliday($endDate)) {
                    $calendarDays++;
                }
            }
        }

        $now = \Carbon\Carbon::now();
        $daysRemaining = max(0, $now->diffInDays($endDate, false));
        
        // Format days remaining as whole number (no decimals)
        $daysRemainingFormatted = (string) round($daysRemaining);

        // Check if 10 calendar days have passed
        if ($now->lt($endDate)) {
            $startDateFormatted = \Carbon\Carbon::parse($startDate)->format('M d, Y');
            $endDateFormatted = $endDate->format('M d, Y');
            
            return [
                'valid' => false,
                'message' => "Cannot Update Status to 'Registered'\n\n" .
                            "The {$postingDays}-day posting period has not been completed yet.\n\n" .
                            "Posting Started: {$startDateFormatted}\n" .
                            "Posting Ends: {$endDateFormatted}\n" .
                            "Days Remaining: {$daysRemainingFormatted} day(s)\n\n" .
                            "You must wait until the {$postingDays}-day posting period is complete before updating the status to 'Registered'.\n\n" .
                            "Please wait until {$endDateFormatted} to proceed.",
                'days_remaining' => $daysRemaining,
                'days_remaining_formatted' => $daysRemainingFormatted,
                'posting_started' => $startDateFormatted,
                'posting_ends' => $endDateFormatted
            ];
        }

        return [
            'valid' => true,
            'message' => 'Posting period completed.',
            'days_remaining' => 0
        ];
    }

    /**
     * Save posting days setting for the officer's department
     */
    public function savePostingDays(Request $request)
    {
        $officer = Auth::guard('admin')->user();
        $department = $officer->department;
        
        $request->validate([
            'posting_days' => 'required|integer|min:1|max:30'
        ]);
        
        Setting::setPostingDays($department, $request->posting_days);
        
        return response()->json([
            'success' => true,
            'message' => 'Posting days setting saved successfully',
            'posting_days' => $request->posting_days
        ]);
    }

    /**
     * Check if a date is a holiday (matches CivilService model logic)
     */
    private function isHoliday($date)
    {
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        $holidays = [
            '01-01', // New Year's Day
            '04-09', // Araw ng Kagitingan
            '05-01', // Labor Day
            '06-12', // Independence Day
            '08-30', // National Heroes Day
            '11-30', // Bonifacio Day
            '12-25', // Christmas Day
            '12-30', // Rizal Day
        ];

        $monthDay = $date->format('m-d');
        return in_array($monthDay, $holidays);
    }
}




