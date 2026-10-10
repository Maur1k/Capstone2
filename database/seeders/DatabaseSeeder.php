<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Run all seeders:
     *   php artisan db:seed
     * 
     * Run specific seeders:
     *   php artisan db:seed --class=AdminSeeder
     *   php artisan db:seed --class=TestQueueSeeder
     *   php artisan db:seed --class=QueueSettingsSeeder
     * 
     * Fresh migration with seeding:
     *   php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        // Core seeders (required for basic functionality)
        $this->call([
            AdminSeeder::class,           // Admin and Officer accounts
            ServiceSeeder::class,         // Services and Service Types
            FloorPoiSeeder::class,        // Floor plans and Points of Interest
            FeatureToggleSeeder::class,   // Feature toggles
            QueueSettingsSeeder::class,   // Queue display settings
        ]);

        // Content seeders (website content)
        $this->call([
            MunicipalDataSeeder::class,   // Municipal info, officials, tourist spots
            WebsiteContentSeeder::class,  // About, Blog posts, Announcements
        ]);

        // Certificate seeders (sample data - optional for development)
        $this->call([
            MarriageCertificateSeeder::class,
            CenomarCertificateSeeder::class,
            DeathCertificateSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('======================================');
        $this->command->info('  Database seeding completed!');
        $this->command->info('======================================');
        $this->command->newLine();
        $this->command->info('You can now login with:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@gmail.com', 'admin123'],
                ['Civil Officer', 'civil@gmail.com', 'civil123'],
                ['BPLO Officer', 'bplo@gmail.com', 'bplo123'],
            ]
        );
        $this->command->newLine();
        $this->command->info('To add test queue data, run:');
        $this->command->info('  php artisan db:seed --class=TestQueueSeeder');
    }
}
