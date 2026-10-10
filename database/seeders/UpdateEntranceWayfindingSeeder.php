<?php

namespace Database\Seeders;

use App\Models\Poi;
use Illuminate\Database\Seeder;

class UpdateEntranceWayfindingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the wayfinding tube POI
        $poi = Poi::where('name', 'Wayfinding Tube')->first();
        
        if (!$poi) {
            $this->command->error('Wayfinding Tube POI not found.');
            return;
        }

        // Update to entrance position
        $poi->x = 100;
        $poi->y = 200;
        $poi->name = 'Main Entrance Wayfinding';

        // Update metadata for entrance positioning
        $currentMeta = $poi->meta;
        $currentMeta['modelPosition'] = [
            'x' => -200,  // Convert image coords (100, 200) to model coords
            'y' => 0.5,   // Ground level + small offset
            'z' => 100,   // Convert Y to Z for 3D space
            'generated' => true,
            'timestamp' => now()->toISOString(),
            'notes' => 'Positioned at main entrance for user starting location'
        ];
        
        $currentMeta['description'] = 'Main entrance wayfinding tube - user starting location';
        $currentMeta['entrance'] = true;
        $currentMeta['user_start_location'] = true;

        $poi->meta = $currentMeta;
        $poi->save();

        $this->command->info("Updated Wayfinding Tube POI (ID: {$poi->id}) to entrance position (100, 200)");
        
        // Also create an entrance POI if it doesn't exist
        $entrancePoi = Poi::where('name', 'Main Entrance')->first();
        if (!$entrancePoi) {
            $entrancePoi = Poi::create([
                'floor_id' => $poi->floor_id,
                'name' => 'Main Entrance',
                'category' => 'Entrance',
                'x' => 100,
                'y' => 200,
                'meta' => [
                    'description' => 'Main building entrance',
                    'entrance' => true,
                    'user_start_location' => true,
                    'modelPosition' => [
                        'x' => -200,
                        'y' => 0.5,
                        'z' => 100,
                        'generated' => true,
                        'timestamp' => now()->toISOString()
                    ]
                ]
            ]);
            
            $this->command->info("Created Main Entrance POI (ID: {$entrancePoi->id}) at position (100, 200)");
        }
    }
}
