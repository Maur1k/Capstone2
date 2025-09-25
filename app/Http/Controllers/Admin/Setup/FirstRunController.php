<?php

namespace App\Http\Controllers\Admin\Setup;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstRunController extends Controller
{
    public function show()
    {
        if (Admin::where('role', 'super_admin')->exists()) {
            return redirect()->route('admin.login');
        }

        return view('admin.setup.index');
    }

    public function store(Request $request)
    {
        if (Admin::where('role', 'super_admin')->exists()) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        return redirect()->route('admin.login')->with('status', 'Super Admin created. You can now sign in.');
    }
}




