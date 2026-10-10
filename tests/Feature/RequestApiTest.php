<?php

namespace Tests\Feature;

use App\Models\CivilService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_submit_civil_service_request()
    {
        Http::fake([
            'http://localhost:4000/print' => Http::response(['success' => true], 200),
            'http://localhost:4000/test-print' => Http::response(['success' => true], 200),
        ]);

        $payload = [
            'full_name' => 'Juan Dela Cruz',
            'document_type' => 'Certificate of Live Birth (On-time)',
            'email' => 'juan@example.com',
            'phone' => '09123456789',
        ];

        $response = $this->postJson('/api/request', $payload);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Request submitted successfully!',
            ]);

        $this->assertDatabaseCount('civil_services', 1);
        $this->assertDatabaseHas('civil_services', [
            'full_name' => 'Juan Dela Cruz',
            'service_type' => 'Certificate of Live Birth (On-time)',
            'print_status' => 'printed',
        ]);
    }

    public function test_can_track_existing_request()
    {
        $civil = CivilService::create([
            'tracking_number' => 'CR-20250101-ABC123',
            'queue_number' => 1,
            'service_type' => 'Certificate of Marriage',
            'service_name' => 'Marriage Certificate',
            'department' => 'Civil Registry Office',
            'status' => 'submitted',
            'full_name' => 'Maria Clara',
            'contact_number' => '09999999999',
            'request_type' => 'regular',
        ]);

        $response = $this->getJson('/api/request/track/' . $civil->tracking_number);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'tracking_number' => 'CR-20250101-ABC123',
                    'full_name' => 'Maria Clara',
                ],
            ]);
    }

    public function test_status_endpoint_returns_latest_status()
    {
        $civil = CivilService::create([
            'tracking_number' => 'CR-20250101-XYZ987',
            'queue_number' => 2,
            'service_type' => 'Regular Service',
            'service_name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project',
            'department' => 'Civil Registry Office',
            'status' => 'inquiry',
            'full_name' => 'Pedro Penduko',
            'contact_number' => '09171111111',
            'request_type' => 'regular',
        ]);

        $response = $this->getJson('/api/request/status/' . $civil->tracking_number);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'inquiry',
            ]);
    }
}

