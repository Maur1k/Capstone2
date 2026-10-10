<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin account
        Admin::updateOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.test')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'change-me-local')),
                'role' => 'super_admin',
                'is_active' => true,
                'department' => null,
            ]
        );

    }
}
