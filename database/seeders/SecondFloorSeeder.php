<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Poi;
use Illuminate\Database\Seeder;

class SecondFloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get or create 2nd Floor
        $secondFloor = Floor::where('name', '2nd Floor')->first();
        
        if (!$secondFloor) {
            $secondFloor = Floor::create([
                'building' => 'Main Building',
                'name' => '2nd Floor',
                'image_path' => 'storage/floors/second-floor.svg',
                'width' => 1200,
                'height' => 800,
                'glb_path' => '/kiosk/assets/1764478770_map_2nd_floor.glb',
                'show_you_are_here' => false,
            ]);
            $this->command->info("Created 2nd Floor");
        } else {
            // Update GLB path if not set
            if (!$secondFloor->glb_path) {
                $secondFloor->glb_path = '/kiosk/assets/1764478770_map_2nd_floor.glb';
                $secondFloor->save();
            }
            $this->command->info("2nd Floor already exists");
        }

        // Only add POIs if floor has less than 5 POIs
        if ($secondFloor->pois()->count() < 5) {
            $pois = [
                [
                    'name' => 'Mayor\'s Office',
                    'category' => 'Office',
                    'x' => 200,
                    'y' => 150,
                    'meta' => [
                        'modelPosition' => ['x' => -100, 'y' => 20, 'z' => 75],
                        'description' => 'Office of the Municipal Mayor'
                    ],
                ],
                [
                    'name' => 'Treasurer\'s Office',
                    'category' => 'Office',
                    'x' => 500,
                    'y' => 150,
                    'meta' => [
                        'modelPosition' => ['x' => 0, 'y' => 20, 'z' => 75],
                        'description' => 'Municipal Treasurer\'s Office'
                    ],
                ],
                [
                    'name' => 'Engineering Office',
                    'category' => 'Office',
                    'x' => 800,
                    'y' => 150,
                    'meta' => [
                        'modelPosition' => ['x' => 100, 'y' => 20, 'z' => 75],
                        'description' => 'Municipal Engineering Office'
                    ],
                ],
                [
                    'name' => 'Social Welfare Office',
                    'category' => 'Office',
                    'x' => 300,
                    'y' => 400,
                    'meta' => [
                        'modelPosition' => ['x' => -50, 'y' => 20, 'z' => 0],
                        'description' => 'Social Welfare and Development Office'
                    ],
                ],
                [
                    'name' => 'Conference Room A',
                    'category' => 'Meeting',
                    'x' => 600,
                    'y' => 400,
                    'meta' => [
                        'modelPosition' => ['x' => 50, 'y' => 20, 'z' => 0],
                        'description' => 'Large conference room for meetings'
                    ],
                ],
                [
                    'name' => 'Stairs',
                    'category' => 'Transport',
                    'x' => 600,
                    'y' => 500,
                    'meta' => [
                        'modelPosition' => ['x' => 0, 'y' => 20, 'z' => -50],
                        'description' => 'Stairway to other floors'
                    ],
                ],
            ];

            foreach ($pois as $poiData) {
                // Check if POI already exists
                $existing = Poi::where('floor_id', $secondFloor->id)
                    ->where('name', $poiData['name'])
                    ->first();
                
                if (!$existing) {
                    $poiData['floor_id'] = $secondFloor->id;
                    Poi::create($poiData);
                    $this->command->info("Created POI: {$poiData['name']}");
                }
            }
        } else {
            $this->command->info("2nd Floor already has POIs");
        }
    }
}











