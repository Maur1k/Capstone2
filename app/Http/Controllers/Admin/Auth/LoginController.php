<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If the app has no super admin yet, redirect to first-run setup
        if (!Admin::where('role', 'super_admin')->exists()) {
            return redirect()->route('admin.setup.show');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        $guard = Auth::guard('admin');

        // Check "active" flag before attempt
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin && !$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account is deactivated.',
            ]);
        }

        if ($guard->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($admin) {
                $admin->forceFill(['last_login_at' => now()])->save();
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}




