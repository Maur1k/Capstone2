<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;
use Illuminate\Database\Seeder;

class CenomarCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::firstOrCreate(
            ['name' => 'Civil Registry Certificates'],
            [
                'department' => 'Civil Registry Office',
                'icon_path' => 'assets/icons/brap-services.png',
                'is_active' => true,
            ]
        );

        $cenomarType = ServiceType::firstOrCreate(
            [
                'service_id' => $service->id,
                'name' => 'CENOMAR (Certificate of No Marriage)',
            ],
            [
                'icon_path' => 'assets/icons/brap-birth.png',
                'is_active' => true,
            ]
        );

        $steps = [
            ['title' => 'Inquiry', 'content' => 'Submit request via kiosk or information desk.'],
            ['title' => 'Checklist Given', 'content' => 'Receive the list of required IDs/documents.'],
            ['title' => 'PSA Processing', 'content' => 'CRO coordinates with PSA for verification.'],
            ['title' => 'Registered', 'content' => 'Certificate released at CRO window.'],
        ];

        foreach ($steps as $order => $step) {
            ServiceStep::firstOrCreate(
                [
                    'service_type_id' => $cenomarType->id,
                    'title' => $step['title'],
                ],
                [
                    'content' => $step['content'],
                    'display_order' => $order + 1,
                ]
            );
        }
    }
}

