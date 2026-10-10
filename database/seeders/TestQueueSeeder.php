<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CivilService;
use App\Models\OtherService;
use Carbon\Carbon;

class TestQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample queue data for testing the queue display and sound chimes.
     * 
     * Queue Columns:
     * - Column 1 (Queueing/On Queue): status = 'inquiry' or 'submitted'
     * - Column 2 (To Submit): status = 'accepted', 'checklist_given', 'incomplete_requirements', etc.
     * - Column 3 (Processed): status = 'registered', 'completed', 'positive_result', etc.
     */
    public function run(): void
    {
        // Delete any existing test queue data from today
        CivilService::whereDate('created_at', Carbon::today())
            ->where('full_name', 'like', 'Test Queue%')
            ->delete();

        OtherService::whereDate('created_at', Carbon::today())
            ->where('full_name', 'like', 'Test Queue%')
            ->delete();

        // === CIVIL REGISTRY OFFICE TEST DATA ===
        $civilSamples = [
            // On Queue (inquiry status) - Column 1 "Queueing"
            [
                'queue_number' => '101', 
                'window_number' => 1,
                'status' => 'inquiry', 
                'full_name' => 'Test Queue - Juan Dela Cruz', 
                'service_name' => 'Delayed BRAP Birth Registration Assistance Project',
                'request_type' => 'brap',
            ],
            [
                'queue_number' => '102', 
                'window_number' => 1,
                'status' => 'inquiry', 
                'full_name' => 'Test Queue - Maria Santos', 
                'service_name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project',
                'request_type' => 'regular',
            ],
            [
                'queue_number' => '103', 
                'window_number' => 2,
                'status' => 'inquiry', 
                'full_name' => 'Test Queue - Pedro Reyes', 
                'service_name' => 'Delayed BRAP Birth Registration Assistance Project',
                'request_type' => 'brap',
            ],
            
            // To Submit (accepted/checklist_given status) - Column 2 "To Submit"
            [
                'queue_number' => '104', 
                'window_number' => 1,
                'status' => 'checklist_given', 
                'full_name' => 'Test Queue - Ana Garcia', 
                'service_name' => 'Delayed BRAP Birth Registration Assistance Project',
                'request_type' => 'brap',
                'called_at' => now()->subMinutes(30),
            ],
            [
                'queue_number' => '105', 
                'window_number' => 2,
                'status' => 'accepted', 
                'full_name' => 'Test Queue - Roberto Cruz', 
                'service_name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project',
                'request_type' => 'regular',
                'called_at' => now()->subMinutes(20),
            ],
            [
                'queue_number' => '106', 
                'window_number' => 1,
                'status' => 'incomplete_requirements', 
                'full_name' => 'Test Queue - Elena Fernandez', 
                'service_name' => 'Delayed BRAP Birth Registration Assistance Project',
                'request_type' => 'brap',
                'called_at' => now()->subMinutes(15),
            ],
            
            // Processed (registered status) - Column 3 "Processed"
            [
                'queue_number' => '107', 
                'window_number' => 1,
                'status' => 'registered', 
                'full_name' => 'Test Queue - Carmen Lopez', 
                'service_name' => 'Delayed BRAP Birth Registration Assistance Project',
                'request_type' => 'brap',
                'called_at' => now()->subHours(1),
            ],
            [
                'queue_number' => '108', 
                'window_number' => 2,
                'status' => 'positive_result', 
                'full_name' => 'Test Queue - Miguel Torres', 
                'service_name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project',
                'request_type' => 'regular',
                'called_at' => now()->subHours(2),
            ],
        ];

        foreach ($civilSamples as $sample) {
            CivilService::create([
                'tracking_number' => 'C2S-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'queue_number' => $sample['queue_number'],
                'window_number' => $sample['window_number'] ?? null,
                'service_type' => 'Civil Service',
                'request_type' => $sample['request_type'] ?? 'regular',
                'service_name' => $sample['service_name'],
                'department' => 'Civil Registry Office',
                'status' => $sample['status'],
                'full_name' => $sample['full_name'],
                'contact_number' => '09' . rand(100000000, 999999999),
                'email' => strtolower(str_replace(' ', '.', explode(' - ', $sample['full_name'])[1] ?? 'test')) . '@test.com',
                'address' => 'Alaminos City, Pangasinan',
                'called_at' => $sample['called_at'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // === BPLO TEST DATA ===
        $bploSamples = [
            // On Queue
            [
                'queue_number' => '201', 
                'window_number' => 1,
                'status' => 'inquiry', 
                'full_name' => 'Test Queue - ABC Trading Corp', 
                'service_name' => 'Application for New Business Permit',
                'department' => 'Business Permit and Licensing Office',
            ],
            [
                'queue_number' => '202', 
                'window_number' => 1,
                'status' => 'inquiry', 
                'full_name' => 'Test Queue - XYZ Restaurant', 
                'service_name' => 'Renewal of Business Permit',
                'department' => 'Business Permit and Licensing Office',
            ],
            // To Submit
            [
                'queue_number' => '203', 
                'window_number' => 1,
                'status' => 'incomplete_requirements', 
                'full_name' => 'Test Queue - Sunshine Store', 
                'service_name' => 'Application for New Business Permit',
                'department' => 'Business Permit and Licensing Office',
                'called_at' => now()->subMinutes(10),
            ],
            // Processed
            [
                'queue_number' => '204', 
                'window_number' => 1,
                'status' => 'registered', 
                'full_name' => 'Test Queue - Golden Enterprise', 
                'service_name' => 'Renewal of Business Permit',
                'department' => 'Business Permit and Licensing Office',
                'called_at' => now()->subHours(1),
            ],
        ];

        foreach ($bploSamples as $sample) {
            OtherService::create([
                'tracking_number' => 'C2S-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'queue_number' => $sample['queue_number'],
                'window_number' => $sample['window_number'] ?? null,
                'service_type' => 'Business Permit',
                'request_type' => 'regular',
                'service_name' => $sample['service_name'],
                'department' => $sample['department'],
                'status' => $sample['status'],
                'full_name' => $sample['full_name'],
                'contact_number' => '09' . rand(100000000, 999999999),
                'email' => strtolower(str_replace(' ', '.', str_replace('Test Queue - ', '', $sample['full_name']))) . '@test.com',
                'address' => 'Alaminos City, Pangasinan',
                'called_at' => $sample['called_at'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created test queue items:');
        $this->command->info('  - Civil Registry Office: ' . count($civilSamples) . ' items');
        $this->command->info('  - BPLO: ' . count($bploSamples) . ' items');
        $this->command->newLine();
        $this->command->info('Queue Column Distribution:');
        $this->command->table(
            ['Department', 'Queueing', 'To Submit', 'Processed'],
            [
                ['Civil Registry Office', '3 items (101-103)', '3 items (104-106)', '2 items (107-108)'],
                ['BPLO', '2 items (201-202)', '1 item (203)', '1 item (204)'],
            ]
        );
    }
}
