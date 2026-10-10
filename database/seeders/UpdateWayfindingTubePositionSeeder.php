<?php

namespace Database\Seeders;

use App\Models\Poi;
use Illuminate\Database\Seeder;

class UpdateWayfindingTubePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the wayfinding tube POI by ID 9
        $poi = Poi::find(9);
        
        if (!$poi) {
            $this->command->error('Wayfinding Tube POI (ID: 9) not found.');
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
        $currentMeta['kioskLocation'] = true;

        $poi->meta = $currentMeta;
        $poi->save();

        $this->command->info("Updated Wayfinding Tube POI (ID: {$poi->id}) to entrance position (100, 200)");
        $this->command->info("Name: {$poi->name}");
        $this->command->info("Coordinates: ({$poi->x}, {$poi->y})");
    }
}
