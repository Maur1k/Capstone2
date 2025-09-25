<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    // RefreshDatabase will handle migrating and wrapping tests in transactions

    public function test_guest_can_view_login(): void
    {
        // Ensure there is at least one admin to avoid first-run redirect
        Admin::create([
            'name' => 'Seed',
            'email' => 'seed@example.com',
            'password' => Hash::make('secret1234'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->get('/admin/login');
        $response->assertOk();
    }

    public function test_super_admin_can_login_and_see_dashboard(): void
    {
        $admin = Admin::create([
            'name' => 'SA',
            'email' => 'sa@example.com',
            'password' => Hash::make('secret1234'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'sa@example.com',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, guard: 'admin');
    }

    public function test_officer_forbidden_to_manage_officers(): void
    {
        $officer = Admin::create([
            'name' => 'OF',
            'email' => 'of@example.com',
            'password' => Hash::make('secret1234'),
            'role' => 'officer',
            'department' => 'Civil Registry',
            'is_active' => true,
        ]);

        $this->post('/admin/login', [
            'email' => 'of@example.com',
            'password' => 'secret1234',
        ]);

        $response = $this->get('/admin/officers');
        $response->assertStatus(403);
    }
}


