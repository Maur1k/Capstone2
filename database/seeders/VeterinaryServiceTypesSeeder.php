<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;

class VeterinaryServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::find(15); // City Veterinary Office
        
        if (!$service) {
            $this->command->error('Service ID 15 not found!');
            return;
        }

        $serviceTypes = [
            [
                'name' => 'Anti-Rabies Vaccination for Walk-in Clients',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Bring Pet to Office',
                        'content' => 'Pet Owners shall bring their pets (dogs and cats three months and above) to the City Veterinary Office. Administrative Assistant III (Computer Operator II) will assist and interview the client. Processing time: 2 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Inquire for Requirements',
                        'content' => 'Pet Owners shall inquire for requirements. Administrative Assistant III (Computer Operator II) will assist and interview the client. City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will prepare vaccine and perform inoculation. Post-vaccination examination will be conducted. Processing time: Step 2.1 (3 minutes), Step 2.2 (1 minute), Step 2.3 (3 minutes).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Check Personal Information System (PIS)',
                        'content' => 'Check at Personal Information System if the client was already enrolled at PIS so he/she can avail the services and can pay the fees and charges. If not yet enrolled at PIS, advise the client to proceed at MIS to enroll his/her datum. Processing time: 2 minutes. Person Responsible: Administrative Assistant III (Computer Operator II).',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Sign Logbook and Receive Form',
                        'content' => 'Sign the logbook and receive form. Administrative Assistant III (Computer Operator II) will print Registration Form and release the animal. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Applicants shall pay the following fees. Ticket Checker will issue Official Receipt. Fees: Registration Fee (Php 75.00), ICT Fee (Php 25.00), Anti-Rabies Fee (Php 300.00). Note: If pet is not yet registered, the Anti-Rabies Fee is Php 300.00. Total fee for Registered Pet: Php 300.00/pet. Total fee for Not Registered Pet: Php 400.00/pet. Processing time: 3 minutes. Total processing time: 15 minutes.',
                        'display_order' => 5
                    ]
                ]
            ],
            [
                'name' => 'Walk-in Veterinary Services',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Bring Pet to Office',
                        'content' => 'Clients shall bring their pets to the City Veterinary Office. Administrative Assistant III (Computer Operator II) will interview and log the Client\'s and pet\'s information. Processing time: 5 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Sign Consent Form and Treatment',
                        'content' => 'Client shall sign the consent form. City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will check and diagnose the animal, treat the animal, perform post-vaccination examination, fill-up and file the Veterinary Medical Record, and prescribe medication and observation to animal. Processing time: Step 2.1 (10 minutes), Step 2.2 (30 minutes), Step 2.3 (3 minutes), Step 2.4 (3 minutes), Step 2.5 (5 minutes).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Animal',
                        'content' => 'Administrative Assistant III (Computer Operator II) will release the animal. Processing time: 1 minute. Total processing time: 54 minutes.',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'Pet Registration',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Bring Pet to Office',
                        'content' => 'Pet Owners shall bring their pets to the City Veterinary Office. Administrative Assistant III (Computer Operator II) will assist and interview the client. Processing time: 2 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Inquire for Requirements',
                        'content' => 'Pet Owners shall inquire for requirements. Administrative Assistant III (Computer Operator II) will assist and interview the client. Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Fill Up Pre-Registration Form',
                        'content' => 'Client shall fill up Pre-Registration Form. Administrative Assistant III (Computer Operator II) will encode the information into the PetRis System (database) and print Pet Registration Form. Processing time: 5 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Applicants shall pay the following fees. Ticket Checker will issue Official Receipt. Fees: Registration Fee (Php 75.00), ICT Fee (Php 25.00). Total: Php 100.00. Processing time: 3 minutes.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Sign Logbook and Receive Form',
                        'content' => 'Sign the logbook and receive Pet Registration Form. Administrative Assistant III (Computer Operator II) will release the animal. Processing time: 1 minute. Total processing time: 12 minutes.',
                        'display_order' => 5
                    ]
                ]
            ],
            [
                'name' => 'Deworming of Small and Large Animals',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Inquire Requirements',
                        'content' => 'Client/animal owner inquire requirements. Administrative Assistant III (Computer Operator II) will interview clients and log Client/Animal owner\'s complete address and availability. Processing time: 5 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Preparation and Travel',
                        'content' => 'City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will prepare and travel to client\'s place for service. Processing time: 5 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Assess Animal Condition',
                        'content' => 'City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will assess animal condition. Processing time: 10 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Perform Deworming',
                        'content' => 'If animal is suitable for deworming, City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will drench deworming. All Ruminants (Goat, Sheep, Cattle and Carabao) must be drench/orally given dewormer for economical considerations. Processing time: 2 minutes.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Post Deworming Advice and Recording',
                        'content' => 'City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will provide post deworming advice and recording. Processing time: 5 minutes. Total processing time: 27 minutes.',
                        'display_order' => 5
                    ]
                ]
            ],
            [
                'name' => 'Issuance of Veterinary Health Certificate',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Bring Animals for Inspection',
                        'content' => 'Stakeholder shall bring their animals for inspection. Required document: Brgy. Certification (obtain from Brgy. Hall). Slaughterhouse Master II or Livestock Inspector I will inspect and pass the animals. City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will issue Veterinary Health Certificate. Processing time: Step 1.1 (2 minutes), Step 1.2 (2 minutes).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Stakeholder shall pay the Veterinary Health Certificate fees. Slaughterhouse Master III or Ticket Checker (bonded Collector) shall collect the payment and issue Official Receipt. Fees: VHC (Php 20.00), ICT Fee (Php 25.00). Total: Php 45.00 per transaction. Processing time: 2 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Sign Logbook and Release Certificate',
                        'content' => 'Stakeholder shall sign the logbook. Administrative Assistant III (Computer Operator II) will log to the logbook and release the Veterinary Health Certificate and release the animal for slaughter. Processing time: 1 minute. Total processing time: 7 minutes.',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'Animal Health Consultation',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Bring Animal to Office',
                        'content' => 'Pet owners shall bring their animals to the City Veterinary Office for consultation. Administrative Assistant III (Computer Operator II) will assist and log client and animal information. Processing time: 3 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Health Examination',
                        'content' => 'City Veterinarian, Veterinarian III, Veterinarian II or Administrative Officer V will conduct health examination, diagnose condition, and provide treatment recommendations. Processing time: 15 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Receive Consultation Report',
                        'content' => 'Administrative Assistant III (Computer Operator II) will prepare and release consultation report. Processing time: 2 minutes. Total processing time: 20 minutes.',
                        'display_order' => 3
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

        $this->command->info('Successfully seeded ' . count($serviceTypes) . ' City Veterinary Office service types with steps!');
    }
}

