<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class UpdateServiceTypeIconsSeeder extends Seeder
{
    public function run(): void
    {
        // Update Delayed Regular Non-BRAP icon
        $delayedRegularNonBrap = ServiceType::where('name', 'Delayed Regular Non-BRAP Birth Registration Assistance Project')->first();
        if ($delayedRegularNonBrap) {
            $delayedRegularNonBrap->update([
                'icon_path' => '/assets/icons/non-brap.png'
            ]);
        }

        // Update Delayed BRAP icon
        $delayedBrap = ServiceType::where('name', 'Delayed BRAP Birth Registration Assistance Project')->first();
        if ($delayedBrap) {
            $delayedBrap->update([
                'icon_path' => '/assets/icons/brap-services.png'
            ]);
        }
    }
}
