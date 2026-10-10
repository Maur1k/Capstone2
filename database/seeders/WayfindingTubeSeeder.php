<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Poi;
use Illuminate\Database\Seeder;

class WayfindingTubeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the first floor
        $floor = Floor::first();
        
        if (!$floor) {
            $this->command->error('No floors found. Please run the main seeder first.');
            return;
        }

        // Create wayfinding tube POI
        $poi = Poi::create([
            'floor_id' => $floor->id,
            'name' => 'Wayfinding Tube',
            'category' => 'Navigation',
            'x' => 600,
            'y' => 400,
            'meta' => [
                'description' => 'Interactive wayfinding tube for navigation assistance',
                'geometry_type' => 'tube',
                'tube_properties' => [
                    'radius' => 20,
                    'height' => 100,
                    'segments' => 16,
                    'color' => '#00ff00',
                    'opacity' => 0.8,
                    'animated' => true,
                    'pulse_speed' => 2.0,
                    'material' => 'emissive'
                ],
                'interactive' => true,
                'wayfinding_features' => [
                    'direction_arrows',
                    'distance_display',
                    'route_highlighting',
                    'voice_guidance',
                    'touch_interaction'
                ],
                'accessibility' => [
                    'wheelchair_accessible' => true,
                    'audio_guidance' => true,
                    'visual_indicators' => true
                ],
                'technical_specs' => [
                    'power_requirements' => '12V DC',
                    'connectivity' => 'WiFi, Bluetooth',
                    'sensors' => ['proximity', 'touch', 'motion'],
                    'display_type' => 'LED strip with directional indicators'
                ]
            ]
        ]);

        $this->command->info("Created Wayfinding Tube POI with ID: {$poi->id}");
        
        // Create additional navigation POIs
        $navigationPois = [
            [
                'name' => 'Digital Wayfinding Kiosk',
                'category' => 'Navigation',
                'x' => 200,
                'y' => 300,
                'meta' => [
                    'description' => 'Interactive digital kiosk for building navigation',
                    'geometry_type' => 'kiosk',
                    'features' => ['touch_screen', 'voice_output', 'print_directions'],
                    'accessibility' => ['wheelchair_accessible', 'audio_guidance']
                ]
            ],
            [
                'name' => 'Directional Signage',
                'category' => 'Navigation',
                'x' => 800,
                'y' => 200,
                'meta' => [
                    'description' => 'Smart directional signage with LED indicators',
                    'geometry_type' => 'signage',
                    'features' => ['led_arrows', 'distance_display', 'room_numbers'],
                    'power_source' => 'solar_powered'
                ]
            ],
            [
                'name' => 'Audio Guidance Point',
                'category' => 'Navigation',
                'x' => 400,
                'y' => 600,
                'meta' => [
                    'description' => 'Audio guidance system for visually impaired visitors',
                    'geometry_type' => 'audio_beacon',
                    'features' => ['voice_guidance', 'proximity_activation', 'multilingual'],
                    'accessibility' => ['visually_impaired_friendly', 'audio_only']
                ]
            ]
        ];

        foreach ($navigationPois as $poiData) {
            $poiData['floor_id'] = $floor->id;
            $poi = Poi::create($poiData);
            $this->command->info("Created {$poi->name} POI with ID: {$poi->id}");
        }
    }
}
