<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CivilService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class OfficerRequestActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_guest_is_redirected_when_updating_request_status()
    {
        $civil = CivilService::create([
            'tracking_number' => 'CR-20250101-GUEST',
            'queue_number' => 10,
            'service_type' => 'Regular Service',
            'service_name' => 'Regular Service',
            'department' => 'Civil Registry Office',
            'status' => 'inquiry',
            'full_name' => 'Guest User',
            'contact_number' => '09999999999',
            'request_type' => 'regular',
        ]);

        $response = $this->post('/officer/requests/' . $civil->tracking_number . '/status', [
            'status' => 'accepted',
            'status_date' => now()->format('Y-m-d'),
            'remarks' => 'Reviewed',
        ]);

        $response->assertRedirect('/officer/login');
    }

    public function test_officer_can_update_civil_request_status()
    {
        $officer = Admin::create([
            'name' => 'Officer One',
            'email' => 'officer@example.com',
            'password' => Hash::make('secret1234'),
            'role' => 'officer',
            'department' => 'Civil Registry Office',
            'is_active' => true,
        ]);

        $civil = CivilService::create([
            'tracking_number' => 'CR-20250101-OFFC',
            'queue_number' => 5,
            'service_type' => 'Regular Service',
            'service_name' => 'Regular Service',
            'department' => 'Civil Registry Office',
            'status' => 'inquiry',
            'full_name' => 'Client One',
            'contact_number' => '09171234567',
            'request_type' => 'regular',
        ]);

        $notificationMock = Mockery::mock(NotificationService::class);
        $notificationMock->shouldReceive('notifyStatusChange')->once()->andReturnTrue();
        $notificationMock->shouldReceive('notifyClientViaSMS')->once()->andReturn(['success' => true]);
        $this->app->instance(NotificationService::class, $notificationMock);

        $response = $this
            ->actingAs($officer, 'admin')
            ->post('/officer/requests/' . $civil->tracking_number . '/status', [
                'status' => 'checklist_given',
                'status_date' => now()->format('Y-m-d'),
                'remarks' => 'Initial review complete',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('civil_services', [
            'id' => $civil->id,
            'status' => 'checklist_given',
            'admin_notes' => 'Initial review complete',
        ]);
    }
}

