<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhotoPanelImage;

class PhotoPanelImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing images first
        PhotoPanelImage::truncate();

        // Create sample photo panel images
        PhotoPanelImage::create([
            'title' => 'Municipal Services',
            'description' => 'Welcome to our municipal services kiosk',
            'image_path' => 'assets/images/slide1.jpg',
            'alt_text' => 'Municipal Services',
            'display_order' => 1,
            'is_active' => true
        ]);

        PhotoPanelImage::create([
            'title' => 'Community Information',
            'description' => 'Stay informed about community events and updates',
            'image_path' => 'assets/images/slide2.jpg',
            'alt_text' => 'Community Information',
            'display_order' => 2,
            'is_active' => true
        ]);

        PhotoPanelImage::create([
            'title' => 'Digital Services',
            'description' => 'Access government services digitally',
            'image_path' => 'assets/images/slide3.jpg',
            'alt_text' => 'Digital Services',
            'display_order' => 3,
            'is_active' => true
        ]);
    }
}