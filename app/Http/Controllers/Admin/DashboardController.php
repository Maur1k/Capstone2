<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

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
}


