<?php

namespace Database\Seeders;

use App\Models\Poi;
use Illuminate\Database\Seeder;

class UpdateWayfindingTubeSeeder extends Seeder
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

        // Update with modelPosition for GLB integration
        $currentMeta = $poi->meta;
        $currentMeta['modelPosition'] = [
            'x' => 0,
            'y' => 0.5,
            'z' => 0,
            'generated' => true,
            'timestamp' => now()->toISOString(),
            'notes' => 'Positioned at model center for GLB integration'
        ];

        $poi->meta = $currentMeta;
        $poi->save();

        $this->command->info("Updated Wayfinding Tube POI (ID: {$poi->id}) with modelPosition for GLB integration");
        
        // Also update other navigation POIs with modelPosition
        $navigationPois = Poi::where('category', 'Navigation')
            ->where('name', '!=', 'Wayfinding Tube')
            ->get();

        foreach ($navigationPois as $navPoi) {
            $meta = $navPoi->meta;
            $meta['modelPosition'] = [
                'x' => ($navPoi->x - 600) * 0.5, // Convert image coords to model coords
                'y' => 0.5,
                'z' => ($navPoi->y - 400) * 0.5,
                'generated' => true,
                'timestamp' => now()->toISOString()
            ];
            
            $navPoi->meta = $meta;
            $navPoi->save();
            
            $this->command->info("Updated {$navPoi->name} POI (ID: {$navPoi->id}) with modelPosition");
        }
    }
}
