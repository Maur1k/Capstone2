<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class FeatureToggleSeeder extends Seeder
{
    /**
     * Seed default feature toggle values.
     * 
     * Feature toggles control various system behaviors:
     * - Queue number display per department
     * - SMS notifications
     * - Other feature flags
     */
    public function run(): void
    {
        // Global feature toggles
        $globalToggles = [
            'feature.queuing_number' => true,      // Global queue number feature
            'feature.sms_notifications' => false,  // SMS notifications (requires API setup)
            'feature.email_notifications' => true, // Email notifications
            'feature.appointment_booking' => true, // Allow appointment booking
            'feature.document_upload' => true,     // Allow document uploads
        ];

        foreach ($globalToggles as $key => $value) {
            Setting::setBoolean($key, $value);
        }

        // Department-specific queue number toggles
        $departments = [
            'Civil Registry Office' => true,
            'Business Permit and Licensing Office' => true,
            'City Transportation Regulation Unit' => true,
            'City Engineering Office' => true,
            'Social Welfare and Development Office' => true,
            'City Veterinary Office' => true,
        ];

        foreach ($departments as $department => $enabled) {
            Setting::setQueueNumberSettingForDepartment($department, $enabled);
        }

        // Kiosk settings
        Setting::set('kiosk.idle_timeout', '60');           // Seconds before returning to home
        Setting::set('kiosk.print_timeout', '30');          // Seconds to wait for print
        Setting::set('kiosk.refresh_interval', '10');       // Seconds between data refresh
        Setting::set('kiosk.queue_display_columns', '3');   // Number of columns in queue display

        $this->command->info('Feature toggles seeded successfully!');
        $this->command->table(
            ['Feature', 'Status'],
            [
                ['Queue Number (Global)', 'Enabled'],
                ['SMS Notifications', 'Disabled'],
                ['Email Notifications', 'Enabled'],
                ['Appointment Booking', 'Enabled'],
                ['Document Upload', 'Enabled'],
            ]
        );
    }
}
