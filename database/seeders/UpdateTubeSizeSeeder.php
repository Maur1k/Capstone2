<?php

namespace Database\Seeders;

use App\Models\Poi;
use Illuminate\Database\Seeder;

class UpdateTubeSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find all POIs with tube properties
        $pois = Poi::where('name', 'Wayfinding Tube')->get();
        
        if ($pois->isEmpty()) {
            $this->command->error('No POIs with tube properties found.');
            return;
        }

        foreach ($pois as $poi) {
            $currentMeta = $poi->meta;
            
            if (isset($currentMeta['tube_properties'])) {
                // Reduce tube radius from 25 to 12 (50% smaller)
                $currentMeta['tube_properties']['radius'] = 12;
                
                // Reduce tube height from 200 to 120 (40% smaller)
                $currentMeta['tube_properties']['height'] = 120;
                
                $poi->meta = $currentMeta;
                $poi->save();
                
                $this->command->info("Updated {$poi->name} POI (ID: {$poi->id}) - radius: 12, height: 120");
            }
        }

        $this->command->info("Successfully updated tube sizes for " . $pois->count() . " POIs");
    }
}
