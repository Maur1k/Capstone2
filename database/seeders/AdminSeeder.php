<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Admin::query()->exists()) {
            return; // Already initialized
        }

        $name = env('ADMIN_NAME', 'Super Admin');
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD');

        if (!$password) {
            $password = 'SuperAdmin123!';
        }

        Admin::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'is_active' => true,
            'department' => null,
        ]);
    }
}




