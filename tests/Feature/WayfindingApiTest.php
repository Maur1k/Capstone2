<?php

namespace Tests\Feature;

use App\Models\Poi;
use Database\Seeders\FloorPoiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WayfindingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FloorPoiSeeder::class);
    }

    public function test_can_fetch_floors_with_pois()
    {
        $response = $this->getJson('/api/floors');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'pois',
                    ],
                ],
                'meta' => ['total'],
            ]);
    }

    public function test_can_calculate_route_between_two_pois()
    {
        $from = Poi::first();
        $to = Poi::where('id', '!=', $from->id)->first();

        $response = $this->getJson('/api/route?from=' . $from->id . '&to=' . $to->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'points',
                    'distance',
                    'estimated_duration',
                    'from_poi',
                    'to_poi',
                ],
            ]);
    }
}

