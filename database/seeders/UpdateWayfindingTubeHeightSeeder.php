<?php

namespace Database\Seeders;

use App\Models\Poi;
use Illuminate\Database\Seeder;

class UpdateWayfindingTubeHeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find all wayfinding tube POIs
        $pois = Poi::where('category', 'Navigation')
            ->orWhere('name', 'LIKE', '%Wayfinding%')
            ->orWhere('name', 'LIKE', '%Tube%')
            ->get();
        
        if ($pois->isEmpty()) {
            $this->command->warn('No wayfinding tube POIs found to update.');
            return;
        }

        foreach ($pois as $poi) {
            $meta = $poi->meta ?? [];
            
            // Update tube height in meta
            if (isset($meta['tube_properties'])) {
                $meta['tube_properties']['height'] = 200; // Increased height
                $meta['tube_properties']['segments'] = 32; // More segments for smoother curve
            } else {
                $meta['tube_properties'] = [
                    'height' => 200,
                    'segments' => 32,
                    'radius' => 20,
                ];
            }
            
            // Add height update timestamp
            $meta['height_updated'] = now()->toISOString();
            $meta['height_update_notes'] = 'Updated to 200 units for better visibility';
            
            $poi->meta = $meta;
            $poi->save();
            
            $this->command->info("Updated height for {$poi->name} POI (ID: {$poi->id})");
        }
        
        $this->command->info("Updated wayfinding tube heights for {$pois->count()} POI(s)");
    }
}
