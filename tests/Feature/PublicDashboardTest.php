<?php

namespace Tests\Feature;

use App\Models\CivilService;
use App\Models\DocumentUpload;
use App\Models\Setting;
use App\Models\StatusUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_disabled_message_when_feature_off(): void
    {
        Setting::setBoolean('feature.public_dashboard', false);

        $response = $this->get('/public-dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Temporarily Hidden');
    }

    public function test_dashboard_renders_metrics_when_enabled(): void
    {
        Setting::setBoolean('feature.public_dashboard', true);

        $civil = CivilService::create([
            'tracking_number' => 'CR-TEST-001',
            'queue_number' => '1',
            'service_type' => 'Marriage Certificate',
            'service_name' => 'Marriage Certificate',
            'department' => 'Civil Registry Office',
            'status' => 'inquiry',
            'full_name' => 'Juan Dela Cruz',
            'contact_number' => '09171234567',
        ]);

        DocumentUpload::create([
            'civil_service_id' => $civil->id,
            'requirement_name' => 'Valid ID',
            'file_name' => 'id.jpg',
            'file_path' => 'documents/id.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 12000,
            'status' => 'pending',
        ]);

        StatusUpdate::create([
            'statusable_id' => $civil->id,
            'statusable_type' => CivilService::class,
            'status' => 'inquiry',
            'status_date' => now()->toDateString(),
        ]);

        $response = $this->get('/public-dashboard');

        $response->assertStatus(200);
        $response->assertSee('Click2Serve Public Dashboard');
        $response->assertSee('Total Requests');
        $response->assertSee('1');
    }
}

