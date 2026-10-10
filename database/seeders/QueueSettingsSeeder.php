<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class QueueSettingsSeeder extends Seeder
{
    /**
     * Seed queue display settings for all departments.
     * 
     * Settings include:
     * - Queue number feature toggle per department
     * - Office display names
     * - Window configurations
     * - Column names
     * - Queue number prefixes
     */
    public function run(): void
    {
        $departments = [
            'Civil Registry Office' => [
                'enabled' => true,
                'office_name' => 'City Civil Registry Office',
                'prefix' => 'CRO',
                'columns' => [
                    'on_queue' => 'Queueing',
                    'to_submit' => 'To Submit',
                    'processed' => 'Processed',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - Birth Registration', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Marriage & Death', 'active' => true],
                    ['number' => 3, 'name' => 'Window 3 - CENOMAR', 'active' => true],
                    ['number' => 4, 'name' => 'Window 4', 'active' => false],
                    ['number' => 5, 'name' => 'Window 5', 'active' => false],
                ],
            ],
            'Business Permit and Licensing Office' => [
                'enabled' => true,
                'office_name' => 'Business Permit and Licensing Office',
                'prefix' => 'BPLO',
                'columns' => [
                    'on_queue' => 'Waiting',
                    'to_submit' => 'Processing',
                    'processed' => 'Completed',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - New Permits', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Renewals', 'active' => true],
                    ['number' => 3, 'name' => 'Window 3', 'active' => false],
                ],
            ],
            'City Transportation Regulation Unit' => [
                'enabled' => true,
                'office_name' => 'City Transportation Regulation Unit',
                'prefix' => 'CTRU',
                'columns' => [
                    'on_queue' => 'Queueing',
                    'to_submit' => 'Processing',
                    'processed' => 'Released',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - MTOP', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Boat Permits', 'active' => true],
                ],
            ],
            'City Engineering Office' => [
                'enabled' => true,
                'office_name' => 'City Engineering Office',
                'prefix' => 'CEO',
                'columns' => [
                    'on_queue' => 'Waiting',
                    'to_submit' => 'Under Review',
                    'processed' => 'Approved',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - Building Permits', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Locational Clearance', 'active' => true],
                ],
            ],
            'Social Welfare and Development Office' => [
                'enabled' => true,
                'office_name' => 'Social Welfare and Development Office',
                'prefix' => 'SWDO',
                'columns' => [
                    'on_queue' => 'Waiting',
                    'to_submit' => 'Interview',
                    'processed' => 'Completed',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - AICS', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Child Services', 'active' => true],
                ],
            ],
            'City Veterinary Office' => [
                'enabled' => true,
                'office_name' => 'City Veterinary Office',
                'prefix' => 'CVO',
                'columns' => [
                    'on_queue' => 'Waiting',
                    'to_submit' => 'Processing',
                    'processed' => 'Done',
                ],
                'windows' => [
                    ['number' => 1, 'name' => 'Window 1 - Pet Registration', 'active' => true],
                    ['number' => 2, 'name' => 'Window 2 - Vaccination', 'active' => true],
                ],
            ],
        ];

        foreach ($departments as $department => $config) {
            // Enable/disable queue number feature
            Setting::setQueueNumberSettingForDepartment($department, $config['enabled']);

            // Set office display name
            Setting::setQueueDisplayOfficeName($department, $config['office_name']);

            // Set queue number prefix
            Setting::setQueueNumberPrefix($department, $config['prefix']);

            // Set column names
            foreach ($config['columns'] as $columnKey => $columnName) {
                Setting::setQueueColumnName($department, $columnKey, $columnName);
            }

            // Set windows configuration
            Setting::setQueueWindows($department, $config['windows']);
        }

        // Global feature toggle (legacy support)
        Setting::setBoolean('feature.queuing_number', true);

        $this->command->info('Queue settings seeded successfully!');
        $this->command->table(
            ['Department', 'Enabled', 'Prefix', 'Windows'],
            array_map(function($dept, $config) {
                $activeWindows = count(array_filter($config['windows'], fn($w) => $w['active']));
                return [$dept, $config['enabled'] ? 'Yes' : 'No', $config['prefix'], $activeWindows . ' active'];
            }, array_keys($departments), $departments)
        );
    }
}


