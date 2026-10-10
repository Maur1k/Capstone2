<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Poi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FloorPoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a ground floor with floorplan image
        $floor = Floor::create([
            'building' => 'Main Building',
            'name' => 'Ground Floor',
            'image_path' => 'storage/floors/ground.svg',
            'width' => 1200,
            'height' => 800,
        ]);

        $pois = [
            [
                'name' => 'Information Desk',
                'category' => 'Service',
                'x' => 200,
                'y' => 150,
                'meta' => [
                    'modelPosition' => ['x' => 2, 'y' => -20, 'z' => 180],
                ],
            ],
            [
                'name' => 'Civil Registry Office',
                'category' => 'Service',
                'x' => 300,
                'y' => 200,
                'meta' => [
                    'modelPosition' => ['x' => -190, 'y' => -20, 'z' => -125],
                ],
            ],
            [
                'name' => 'Office of the Supply Officer',
                'category' => 'Service',
                'x' => 400,
                'y' => 100,
                'meta' => [
                    'modelPosition' => ['x' => -190, 'y' => -20, 'z' => -18],
                ],
            ],
            [
                'name' => 'Office of the City Agriculture and Agri Biosystem',
                'category' => 'Service',
                'x' => 150,
                'y' => 300,
                'meta' => [
                    'modelPosition' => ['x' => -200, 'y' => -20, 'z' => 124],
                ],
            ],
            [
                'name' => 'City Transportation Regulation Unit',
                'category' => 'Service',
                'x' => 250,
                'y' => 250,
                'meta' => [
                    'modelPosition' => ['x' => -137, 'y' => -20, 'z' => 165],
                ],
            ],
            [
                'name' => 'Negosyo Center',
                'category' => 'Service',
                'x' => 350,
                'y' => 180,
                'meta' => [
                    'modelPosition' => ['x' => -137, 'y' => -20, 'z' => 200],
                ],
            ],
            [
                'name' => 'City Assesor Office',
                'category' => 'Service',
                'x' => 100,
                'y' => 100,
                'meta' => [
                    'modelPosition' => ['x' => 60, 'y' => -20, 'z' => 78],
                ],
            ],
            [
                'name' => 'Business Licensing Office',
                'category' => 'Service',
                'x' => 300,
                'y' => 200,
                'meta' => [
                    'modelPosition' => ['x' => 85, 'y' => -20, 'z' => 200],
                ],
            ],
            [
                'name' => 'Office of the City Treasurer',
                'category' => 'Service',
                'x' => 400,
                'y' => 100,
                'meta' => [
                    'modelPosition' => ['x' => 75, 'y' => -20, 'z' => -15],
                ],
            ],
            [
                'name' => 'Restroom',
                'category' => 'Restroom',
                'x' => 150,
                'y' => 300,
                'meta' => [
                    'modelPosition' => ['x' => 50, 'y' => -20, 'z' => -115],
                    'waypoints' => [
                        ['x' => 50, 'y' => -20, 'z' => -115],
                        ['x' => 50, 'y' => -6, 'z' => -175],
                    ],
                ],
            ],
            [
                'name' => 'BOSS',
                'category' => 'Service',
                'x' => 250,
                'y' => 250,
                'meta' => [
                    'modelPosition' => ['x' => 230, 'y' => -20, 'z' => -115],
                ],
            ],
        ];

        foreach ($pois as $poi) {
            $poi['floor_id'] = $floor->id;
            Poi::create($poi);
        }
    }
}
