<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;

class CityEngineeringServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::find(5); // City Engineering Office
        
        if (!$service) {
            $this->command->error('Service ID 5 not found!');
            return;
        }

        $serviceTypes = [
            [
                'name' => 'Securing Locational Clearance for Building Permit',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Application Form and Documents',
                        'content' => 'Submit accomplished and notarized Unified Application Form for Building Permit (1 copy) at City Engineering Office. Administrative Assistant III will receive application form and check for submitted required documents. Zoning Officer II will verify Land Use (Zoning). Processing time: 1 Day. Person Responsible: Arsenio Q. Basila (Zoning Officer II), Carmela P. De Leon (Administrative Assistant III).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Assessment and Payment Order',
                        'content' => 'City Engineering Office will assess and print payment order of fees. Fees: Documentary Stamp (PHP 30), ICT (PHP 25). Processing time: 2 Minutes. Person Responsible: Shera A. Basila, Genalyn R. Casillo.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Payment at City Treasury Office',
                        'content' => 'Proceed to City Treasury Office for the payment of fees. City Treasury Office will accept payment based on the Order of Payment. Fees: Documentary Stamp (PHP 30), ICT (PHP 25). Processing time: 2 Minutes. Person Responsible: City Treasury Office staff.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Submission of Official Receipt and Claiming',
                        'content' => 'Return to City Engineering Office for the submission of Official Receipt and claiming of approved Locational Clearance. Zoning Officer II will approve the Locational Clearance. Administrative Assistant III will release the approved Locational Clearance to client. Processing time: 1 Day. Person Responsible: Arsenio Q. Basila (Zoning Officer II), Eloise Mina (Administrative Assistant III).',
                        'display_order' => 4
                    ]
                ]
            ],
            [
                'name' => 'Securing Building Permit (Simple Application)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Application and Required Documents',
                        'content' => 'Submit accomplished application form and required documents at City Engineering Office assistance desk. Administrative Assistant III will receive application form and verify the submitted required documents. Processing time: 1 Hour. Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Evaluation of Plans and Documents',
                        'content' => 'Engineers and Architects will evaluate plans and documents (attach evaluation checklist). Processing time: 1 day for Simple Application. Person Responsible: Engr. Jeremias V. Curameng, Jr. (Engr. III), Engr. Allan F. Alcantara (Engr. II), Arch. Lloyd P. Santos (Architect III), Arch. Albert C. Carillo (Architect II), Engr. Roland D. Ranches (CGADH I), Engr. Jonathan P. Castro (Engr. II), Engr. Wilson C. Rosario (Engr. III), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Inspection and Verification',
                        'content' => 'Inspection & Verification will be conducted. Fee: PHP 200 (will be included in the assessment of fees for Building permit). Person Responsible: Engr. Wilson C. Rosario (Engr. III), Jesusito M. Ancheta (CMGF).',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Assessment and Payment Order',
                        'content' => 'Architects and Engineers will assess, print and issue payment order of fees. Bases of assessment for fees: a. Character of Occupancy or use of building structure; b. floor area; and c. fixtures that will be installed in the building. Processing time: 1 Hour. Person Responsible: Arch. Lloyd P. Santos (Architect III), Engr. Allan F. Alcantara (Engr. II), Engr. Jonathan P. Castro (Engr. II), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Fire Endorsement',
                        'content' => 'Administrative Assistant III will prepare, print and issue fire endorsement. Note: Advice client to proceed to the Bureau of Fire Protection Office. Processing time: 5 minutes. Person Responsible: Carmela F. De Leon (Administrative Assistant III).',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Payment at City Treasury Office',
                        'content' => 'Proceed to City Treasury Office for the payment of fees. Make sure to secure Official Receipt that will be issued upon payment. Fees: Documentary Stamp (PHP 30), ICT (PHP 25), Signboard (PHP 200), Inspection & Verification (PHP 200), Building permit fees based on the assessment of fees. Processing time: 2 minutes. Person Responsible: Emelle R. Barrozo (Admin. Assist. III), Sheila A. Bacallo (RCC II), Genalyn R. Castillo (RCC I), Eloisa Mina (RCC I).',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Approval and Release of Permit',
                        'content' => 'Client get the approved permit and signboard and sign in the Building Permit Logbook. Administrative Assistant III will prepare the necessary documents of permit for the approval of Building Official. Building Official will approve the permit. Administrative Assistant III will release the approved permit to the client. Processing time: 1 Day. Total processing time: 2 days, 2 hours, 7 minutes (Simple Application). Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM), Engr. Roel N. Camba (Building Official).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Securing Building Permit (Complex Application)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Application and Required Documents',
                        'content' => 'Submit accomplished application form and required documents at City Engineering Office assistance desk. Administrative Assistant III will receive application form and verify the submitted required documents. Processing time: 1 Hour. Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Evaluation of Plans and Documents',
                        'content' => 'Engineers and Architects will evaluate plans and documents (attach evaluation checklist). Processing time: 4 days for Complex Application. Person Responsible: Engr. Jeremias V. Curameng, Jr. (Engr. III), Engr. Allan F. Alcantara (Engr. II), Arch. Lloyd P. Santos (Architect III), Arch. Albert C. Carillo (Architect II), Engr. Roland D. Ranches (CGADH I), Engr. Jonathan P. Castro (Engr. II), Engr. Wilson C. Rosario (Engr. III), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Inspection and Verification',
                        'content' => 'Inspection & Verification will be conducted. Fee: PHP 200 (will be included in the assessment of fees for Building permit). Person Responsible: Engr. Wilson C. Rosario (Engr. III), Jesusito M. Ancheta (CMGF).',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Assessment and Payment Order',
                        'content' => 'Architects and Engineers will assess, print and issue payment order of fees. Bases of assessment for fees: a. Character of Occupancy or use of building structure; b. floor area; and c. fixtures that will be installed in the building. Processing time: 1 Hour. Person Responsible: Arch. Lloyd P. Santos (Architect III), Engr. Allan F. Alcantara (Engr. II), Engr. Jonathan P. Castro (Engr. II), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Fire Endorsement',
                        'content' => 'Administrative Assistant III will prepare, print and issue fire endorsement. Note: Advice client to proceed to the Bureau of Fire Protection Office. Processing time: 5 minutes. Person Responsible: Carmela F. De Leon (Administrative Assistant III).',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Payment at City Treasury Office',
                        'content' => 'Proceed to City Treasury Office for the payment of fees. Make sure to secure Official Receipt that will be issued upon payment. Fees: Documentary Stamp (PHP 30), ICT (PHP 25), Signboard (PHP 200), Inspection & Verification (PHP 200), Building permit fees based on the assessment of fees. Processing time: 2 minutes. Person Responsible: Emelle R. Barrozo (Admin. Assist. III), Sheila A. Bacallo (RCC II), Genalyn R. Castillo (RCC I), Eloisa Mina (RCC I).',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Approval and Release of Permit',
                        'content' => 'Client get the approved permit and signboard and sign in the Building Permit Logbook. Administrative Assistant III will prepare the necessary documents of permit for the approval of Building Official. Building Official will approve the permit. Administrative Assistant III will release the approved permit to the client. Processing time: 1 Day. Total processing time: 5 days, 2 hours, 7 minutes (Complex Application). Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM), Engr. Roel N. Camba (Building Official).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Securing Building Permit (Highly Technical Application)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Application and Required Documents',
                        'content' => 'Submit accomplished application form and required documents at City Engineering Office assistance desk. Administrative Assistant III will receive application form and verify the submitted required documents. Processing time: 1 Hour. Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Evaluation of Plans and Documents',
                        'content' => 'Engineers and Architects will evaluate plans and documents (attach evaluation checklist). Processing time: 12 days for Highly Technical Application. Person Responsible: Engr. Jeremias V. Curameng, Jr. (Engr. III), Engr. Allan F. Alcantara (Engr. II), Arch. Lloyd P. Santos (Architect III), Arch. Albert C. Carillo (Architect II), Engr. Roland D. Ranches (CGADH I), Engr. Jonathan P. Castro (Engr. II), Engr. Wilson C. Rosario (Engr. III), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Inspection and Verification',
                        'content' => 'Inspection & Verification will be conducted. Fee: PHP 200 (will be included in the assessment of fees for Building permit). Person Responsible: Engr. Wilson C. Rosario (Engr. III), Jesusito M. Ancheta (CMGF).',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Assessment and Payment Order',
                        'content' => 'Architects and Engineers will assess, print and issue payment order of fees. Bases of assessment for fees: a. Character of Occupancy or use of building structure; b. floor area; and c. fixtures that will be installed in the building. Processing time: 1 Hour. Person Responsible: Arch. Lloyd P. Santos (Architect III), Engr. Allan F. Alcantara (Engr. II), Engr. Jonathan P. Castro (Engr. II), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Fire Endorsement',
                        'content' => 'Administrative Assistant III will prepare, print and issue fire endorsement. Note: Advice client to proceed to the Bureau of Fire Protection Office. Processing time: 5 minutes. Person Responsible: Carmela F. De Leon (Administrative Assistant III).',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Payment at City Treasury Office',
                        'content' => 'Proceed to City Treasury Office for the payment of fees. Make sure to secure Official Receipt that will be issued upon payment. Fees: Documentary Stamp (PHP 30), ICT (PHP 25), Signboard (PHP 200), Inspection & Verification (PHP 200), Building permit fees based on the assessment of fees. Processing time: 2 minutes. Person Responsible: Emelle R. Barrozo (Admin. Assist. III), Sheila A. Bacallo (RCC II), Genalyn R. Castillo (RCC I), Eloisa Mina (RCC I).',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Approval and Release of Permit',
                        'content' => 'Client get the approved permit and signboard and sign in the Building Permit Logbook. Administrative Assistant III will prepare the necessary documents of permit for the approval of Building Official. Building Official will approve the permit. Administrative Assistant III will release the approved permit to the client. Processing time: 1 Day. Total processing time: 13 days, 2 hours, 7 minutes (Highly Technical Application). Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM), Engr. Roel N. Camba (Building Official).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Securing Certificate of Occupancy',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Application and Required Documents',
                        'content' => 'Submit accomplished application form and required documents at City Engineering Office assistance desk. Required documents: Notarized Unified Application Form for Certificate of Occupancy (4 copies), Notarized Certificate of Completion (NBC Form No. B-10) signed by the owner/applicant together with the Construction Logbook with Certification signed and sealed by the duly licensed Architect or Civil Engineer in-charge of the construction. Administrative Assistant III will receive application form and verify the submitted required documents. Processing time: 1 Hour. Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Evaluation of Plans and Documents',
                        'content' => 'Engineers and Architects will evaluate plans and documents (attach evaluation checklist). Processing time: 1 day (Simple Application); 4 days (Complex Application); 12 days (Highly Technical Application). Person Responsible: Engr. Jeremias V. Curameng, Jr. (Engr. III), Engr. Allan F. Alcantara (Engr. II), Arch. Lloyd P. Santos (Architect III), Arch. Albert C. Carillo (Architect II), Engr. Roland D. Ranches (CGADH I), Engr. Jonathan P. Castro (Engr. II), Engr. Wilson C. Rosario (Engr. III), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Final Inspection',
                        'content' => 'Final inspection of building/structure will be conducted. Fee will be included in the assessment of fees. Person Responsible: Engr. Wilson C. Rosario (Engr. III), Jesusito M. Ancheta (CMGF).',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Assessment and Payment Order',
                        'content' => 'Architects and Engineers will assess, print and issue payment order of fees. Bases of assessment for fees: a. Character of Occupancy or use of building structure; b. actual cost of the building; and c. fixtures installed in building. Processing time: 1 Hour. Person Responsible: Arch. Lloyd P. Santos (Architect III), Engr. Allan F. Alcantara (Engr. II), Engr. Jonathan P. Castro (Engr. II), Engr. Joey A. Tugade (Engr. I).',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Payment at City Treasury Office',
                        'content' => 'Proceed to City Treasury Office for the payment of fees. Make sure to secure Official Receipt that will be issued upon payment. Fees: Documentary Stamp (PHP 30), ICT (PHP 25), Occupancy permit fees based on the assessment of fees. Processing time: 2 minutes. Person Responsible: Emelle R. Barrozo (Admin. Assist. III), Sheila A. Bacallo (RCC II), Genalyn R. Castillo (RCC I), Eloisa Mina (RCC I).',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Approval and Release of Certificate',
                        'content' => 'Client get the approved Certificate of Occupancy and sign in the Occupancy Permit Logbook. Administrative Assistant III will prepare the necessary documents of permit for the approval of Building Official. Building Official will approve the permit. Administrative Assistant III will release the approved Certificate of Occupancy to the client. Processing time: 1 Day. Total processing time: 2 days, 2 hours, 7 minutes (Simple Application); 5 days, 2 hours, 7 minutes (Complex Application); 13 days, 2 hours, 7 minutes (Highly Technical Application). Person Responsible: Karen R. Galla (Administrative Assistant III), Roshiela F. Ruiz (CMM), Engr. Roel N. Camba (Building Official).',
                        'display_order' => 6
                    ]
                ]
            ]
        ];

        foreach ($serviceTypes as $typeData) {
            $type = ServiceType::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'name' => $typeData['name']
                ],
                [
                    'icon_path' => $typeData['icon_path'],
                    'is_active' => true
                ]
            );

            $this->command->info("Created/Updated service type: {$type->name}");

            // Add steps
            foreach ($typeData['steps'] as $stepData) {
                ServiceStep::updateOrCreate(
                    [
                        'service_type_id' => $type->id,
                        'title' => $stepData['title']
                    ],
                    [
                        'content' => $stepData['content'],
                        'display_order' => $stepData['display_order']
                    ]
                );
            }

            $this->command->info("  - Added " . count($typeData['steps']) . " steps");
        }

        $this->command->info('Successfully seeded ' . count($serviceTypes) . ' City Engineering Office service types with steps!');
    }
}

