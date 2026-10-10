<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;

class CTRUServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::find(13); // City Transportation Regulation Unit (CTRU)
        
        if (!$service) {
            $this->command->error('Service ID 13 not found!');
            return;
        }

        $serviceTypes = [
            [
                'name' => 'Motorized Tricycle Operator\'s Permit Service (MTOP)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements for Renewal/New Application',
                        'content' => 'For renewal applications: Fill up the Public Motorized Tricycle Service Request Form (PMTSRF) attached with 1 photocopy each of OR, CR and Driver\'s License, original TODA Certification, original CTC and Barangay Clearance/Certification. For approved new applications: Fill up the PMTSRF attached with 1 photocopy each of OR, CR and Driver\'s License, original TODA Certification, original CTC and Barangay Clearance/Certification. Client shall comply with sidecar requirements: standard backrest, body number, lights in the body number, prescribed body paint. CTRU Staff shall fill up the Payment Order Form (POF) and review submitted documents. For new applications, CTRU Staff shall inspect the tricycle if it complies with sidecar requirements. Processing time: 2 minutes (renewal), 5 minutes (new).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at the City Treasurer\'s Office. For applicants renewing only their tricycle franchise, secure Account Clearance. City Treasurer\'s Office staff shall accept the payment based on the POF and issue Official Receipt. Fees: ICT (Php 25.00), MTOP Fee (Php 100.00), Registration/Filing Fee (Php 150.00), Franchise Fee (Php 200.00), Sticker Fee (Php 65.00), Account Clearance (Php 50.00), POSO Clearance (Php 50.00). Total: Php 590.00 (NEW), Php 640.00 (1 YEAR). Penalty: 25% of total fees (P450) + 50% of Franchise Fee = P212.50. Interest: P2 per day after 2 months grace period. Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Further Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff shall detach the PMTSRF along with the CTC, Barangay Clearance/Certification, TODA Certification, and photocopy of OR and CR. CTRU shall advise the client to secure POSO Clearance. Processing time: 1 minute.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Secure POSO Clearance and Register Application',
                        'content' => 'Secure POSO Clearance. CTRU Staff shall register the application in the log book. CTRU Staff shall print the Motorized Tricycle Operator\'s Permit (MTOP). Processing time: 1 minute (registration), 2 minutes (printing).',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Present Complete Documents and Sign MTOP',
                        'content' => 'Present complete documents and clearances. CTRU Staff shall assist the client in signing the MTOP. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval of application. CTRU Head shall sign the MTOP to recommend its approval to the City Mayor. Processing time: 1 minute.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval of application. City Administrator shall sign the MTOP for final approval. Processing time: 2 minutes.',
                        'display_order' => 7
                    ],
                    [
                        'title' => 'Release of Documents and Sticker',
                        'content' => 'Return to CTRU for the release of documents and/or sticker. For renewal applications: CTRU Staff shall release the MTOP (owner\'s copy), documentary requirements, and sticker. For new applications: CTRU Staff shall release the MTOP (owner\'s copy) only, and advise the client to complete registration at LTO. Processing time: 2 minutes.',
                        'display_order' => 8
                    ],
                    [
                        'title' => 'LTO Registration (New Applications Only)',
                        'content' => 'Register application at LTO and submit 1 photocopy of the updated OR, CR, and Authorization (if necessary) to CTRU. CTRU Staff shall release the documentary requirements and sticker to the client. Processing time: 2 minutes. Total processing time: Renewal = 20 minutes, New = 25 minutes.',
                        'display_order' => 9
                    ]
                ]
            ],
            [
                'name' => 'Mayor\'s Permit for Passenger Boat Service',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Present Required Documents',
                        'content' => 'For renewal/new applications: Present original CTC, Barangay Clearance/Certification, City Tourism Office Certification, and Barangay Business Clearance (for Lucap Operators only). For new applications: Present approval from the Office of the City Mayor. CTRU Staff fills up the Mayor\'s Permit for Passenger Boat Application Form (MPPBAF) and checks submitted documents. Processing time: 3 minutes.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment and Clearances',
                        'content' => 'Settle payment at City Treasurer\'s Office and secure required clearances (Land Tax, Account). City Treasurer\'s Office staff accepts payment based on the Order of Payment and issues Official Receipt. Fees (for 1 boat, 1 year): ICT (Php 25.00), Mayor\'s Permit (Php 100.00), Inspection Fee (Php 200.00), Land Tax (Php 50.00), Clearance (Php 50.00), Account Clearance (Php 50.00), Sticker Fee (Php 65.00). Total: Php 490.00. Penalty: 25% of Mayor\'s Permit (P100) = P25. Interest: 2% of Mayor\'s Permit and Penalty Fees for every month of delay = P2.50 per month per boat. Processing time: 1 minute.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff records in the log book. CTRU Staff prepares the Mayor\'s Permit for Passenger Boat. CTRU Head signs the permit to recommend its approval. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Final Approval',
                        'content' => 'Seek the final approval of application. City Administrator signs the Mayor\'s Permit for Passenger Boat for final approval. Processing time: 2 minutes.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Release of Documents and Sticker',
                        'content' => 'Return to CTRU for the release of documents and sticker. CTRU Staff releases the Mayor\'s Permit (owner\'s copy), documentary requirements, and sticker. Processing time: 1 minute. Total processing time: 9 minutes (for 1 boat).',
                        'display_order' => 5
                    ]
                ]
            ],
            [
                'name' => 'PUV Registration Service',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Present Required Documents',
                        'content' => 'For new/renewal applications: Present photocopy of OR, CR, and PA (Provisional Authority) or Certificate of Public Conveyance or equivalent. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment based on the Order of Payment and issues Official Receipt. Fees (for 1 year, 1 unit): ICT (Php 25.00), Sticker fees vary by vehicle type: Jeepney/UV (Php 750.00), Mini Bus (Php 1,000.00), Ordinary Bus (Php 1,000.00), AC Bus (Php 1,500.00), Taxi (Php 500.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Sticker',
                        'content' => 'Return to CTRU Office for the release of sticker. CTRU Staff encodes data in the log book and releases the sticker to the client. Processing time: 1 minute. Total processing time: 5 minutes (for 1 vehicle).',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'Certificate of Transfer of Ownership (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP, 1 original or photocopy of Deed of Sale. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fees: Order for Dropping (Php 100.00), Substitution of Motor/Unit (Php 100.00), Fee for issuance of amended MTOP (Php 100.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Certificate of Transfer of Ownership. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Certificate of Transfer of Ownership. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Order for Dropping (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fee: Order for Dropping (Php 100.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Order for Dropping. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Order for Dropping. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Permit to Travel (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fee: Special Permit (Php 100.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Permit to Travel. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Permit to Travel. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Certification for Cancellation of Franchise (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fee: Certification for Cancellation of Franchise (Php 50.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Certification for Cancellation of Franchise. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Certification for Cancellation of Franchise. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Special Permit for Out-of-Line Operation (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fee: Special Permit for Out-of-Line Operation (Php 50.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Special Permit for Out-of-Line Operation. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Special Permit for Out-of-Line Operation. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
                    ]
                ]
            ],
            [
                'name' => 'Fare Matrix Fee (Tricycle)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Fill up the request form and submit required documents: 1 original or photocopy of Community Tax Certificate, 1 original or photocopy of Official Receipt (OR), 1 original or photocopy of Certificate of Registration (CR), 1 original or photocopy of MTOP. CTRU Staff fills up the Payment Order Form (POF). Processing time: 1 minute.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment at City Treasurer\'s Office',
                        'content' => 'Settle payment at City Treasurer\'s Office. City Treasurer\'s Office staff accepts payment and issues Official Receipt. Fee: Fare Matrix Fee (Php 150.00). Processing time: 3 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Return to CTRU for Processing',
                        'content' => 'Return to CTRU Office for further processing. CTRU Staff prepares the Fare Matrix document. Processing time: 2 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Affix Signatures',
                        'content' => 'CTRU Staff assists the client in signing the document. Processing time: 1 minute.',
                        'display_order' => 4
                    ],
                    [
                        'title' => 'Seek Recommending Approval',
                        'content' => 'Seek the recommending approval. CTRU Head signs the document to recommend its approval. Processing time: 1 minute.',
                        'display_order' => 5
                    ],
                    [
                        'title' => 'Seek Final Approval',
                        'content' => 'Seek the final approval. City Administrator signs the document for final approval. Processing time: 2 minutes.',
                        'display_order' => 6
                    ],
                    [
                        'title' => 'Release of Document',
                        'content' => 'Return to CTRU for the release of document. CTRU Staff releases the Fare Matrix document. Processing time: 1 minute. Total processing time: 8 minutes (for 1 document).',
                        'display_order' => 7
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

        $this->command->info('Successfully seeded ' . count($serviceTypes) . ' CTRU service types with steps!');
    }
}

