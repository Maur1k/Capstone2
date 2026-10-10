<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;

class BPLOServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::find(9); // Business Permit and Licensing Office (BPLO)
        
        if (!$service) {
            $this->command->error('Service ID 9 not found!');
            return;
        }

        $serviceTypes = [
            [
                'name' => 'On-site Application for New Business Permit',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Proceed to Business Permit & Licensing Office (BPLO) for submission and verification of documents. Required documents: Community Tax Certificate (CTC), Barangay Clearance, Proof of enrollment in Personal Information System (PIS), Contract of Lease/Tax Declaration of Property, Inspection and Recommendation slip, Special Power of Attorney (if applicable), Licenses from National Government Agencies, Certificate of Registration.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Proceed to City Treasurer\'s Office (CTO) for payment of Mayor\'s Permit and other regulatory fees. Required clearances: Zoning Clearance, Real Property Tax Clearance, Sanitary Permit, Fire Safety Inspection Certificate.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Permit',
                        'content' => 'Proceed to BPLO for releasing of Mayor\'s Permit. Processing time: 25 minutes per step.',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'On-site Application for Renewal of Business Permit',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Proceed to Business Permit & Licensing Office (BPLO) for submission and verification of documents. Required documents: Community Tax Certificate (CTC), Barangay Clearance, Proof of enrollment in Personal Information System (PIS), Contract of Lease/Tax Declaration of Property, Inspection and Recommendation slip, Special Power of Attorney (if applicable), Licenses from National Government Agencies, Certificate of Registration.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Proceed to City Treasurer\'s Office (CTO) for payment. Fees include: Account Clearance (Php 50.00), Building Clearance (Php 20.00), Sanitary Permit (Php 100.00), Documentary stamp (Php 60.00), ICT Fee (Php 25.00), Sanitary Inspection fees (based on area). Processing time: Step 1 (10 minutes), Step 2 (30 minutes), Step 3 (20 minutes).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Permit',
                        'content' => 'Proceed to BPLO for releasing of renewed Mayor\'s Permit. Additional fees may apply based on Mayor\'s Permit Fee, Business Tax, and 15% of total fees and charges.',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'Online Filing of Application for New Business Permit (eBOSS)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Request Application Form',
                        'content' => 'Message the Business Permit & Licensing Office (BPLO) via Facebook Messenger to request application forms. Required documents: Community Tax Certificate (CTC), Barangay Clearance, Proof of enrollment in Personal Information System (PIS), Contract of Lease, Certifications from City Offices (Tourism, Market, Cemetery), Special Power of Attorney (SPA), Licenses/Certificates from National Government Agencies (BSP, FDA, DENR, DTI, SEC, etc.).',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Submit Requirements via Email',
                        'content' => 'Submit all required documents via email to BPLO. Processing time: 15 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Pay Fees via Bank Transfer',
                        'content' => 'Pay fees via bank transfer. Fees include: Business Plate (Php 200.00), Fire Safety Inspection Certificate (15% of total fees), Zoning Clearance (Php 20.00), Real Property Tax Clearance (Php 50.00), Sanitary Permit (Php 100.00), Documentary stamp (Php 60.00), ICT Fee (Php 25.00). Total fees depend on capitalization/gross sales/receipts. Processing time: 20 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Print and Post Permit',
                        'content' => 'Print and post the Mayor\'s Permit. Processing time: 35 minutes. Total processing time: 60 minutes.',
                        'display_order' => 4
                    ]
                ]
            ],
            [
                'name' => 'Online Filing of Application for Renewal of Business Permit (eBOSS)',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Request Application Form',
                        'content' => 'Message the Business Permit & Licensing Office (BPLO) via Facebook Messenger to request application forms. Required documents: Community Tax Certificate (CTC), Barangay Clearance, Proof of enrollment in Personal Information System (PIS), Contract of Lease, Certifications from City Offices (Tourism, Market, Cemetery), Special Power of Attorney (SPA), Licenses/Certificates from National Government Agencies.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Submit Requirements via Email',
                        'content' => 'Submit all required documents via email to BPLO. Processing time: 15 minutes.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Pay Fees via Bank Transfer',
                        'content' => 'Pay fees via bank transfer. Fees include: Business Plate (Php 200.00), Fire Safety Inspection Certificate (15% of total fees), Zoning Clearance (Php 20.00), Real Property Tax Clearance (Php 50.00), Sanitary Permit (Php 100.00), Documentary stamp (Php 60.00), ICT Fee (Php 25.00). Additional fees may apply based on Mayor\'s Permit Fee, Business Tax, and 15% of total fees and charges. Processing time: 20 minutes.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Print and Post Permit',
                        'content' => 'Print and post the renewed Mayor\'s Permit. Processing time: 35 minutes. Total processing time: 60 minutes.',
                        'display_order' => 4
                    ]
                ]
            ],
            [
                'name' => 'Issuance of General Permit',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Proceed to Business Permit & Licensing Office (BPLO) for submission, verification of documents and affix signature in the encoded application form. Required documents vary by category: For Cooperative - PIS, CTC, Barangay Clearance, Letter of Approval by City Market and Cemetery Section Head. For Firecrackers Stallholders - Authenticated photocopy of Dealers/Manufacturer\'s License from PNP Camp Crame, Authorization/Certification of Dealers/Licensee, Fireworks Retailers Seminar Certificate, Fire Safety Inspection Certificate (FSIC). For Promotions and Exhibitors - PIS of requesting party, Request letter approved by City Administrator, Letter of Approval by City Market and Cemetery Section Head.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Proceed to City Treasurer\'s Office for Payment of Mayor\'s Permit and other regulatory fees and comply required clearances. Fees vary by category: For Cooperative - Mayor\'s Permit (Php 100.00), Business Plate (Php 200.00), Sticker Fee (Php 65.00), RPT Clearance (Php 50.00), Account Clearance (Php 50.00), ICT Fee (Php 25.00). Total: Php 490.00. For Firecrackers Stallholders - Mayor\'s Permit (Php 100.00), Inspection Fee (Php 100.00), Garbage Fee (Php 200.00), Space Rental Fee (Php 450.00), ICT Fee (Php 25.00). Total: Php 875.00. For Promotions and Exhibitors - Special Permit (Php 1000.00), Inspection Fee (Php 100.00), Electrical Consumption (Php 375.00), ICT Fee (Php 25.00). Total: Php 1,500.00.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Permit',
                        'content' => 'Proceed to BPLO for releasing of Mayor\'s Permit. Processing time: 5-25 minutes depending on category.',
                        'display_order' => 3
                    ]
                ]
            ],
            [
                'name' => 'Motorized Tricycle Operator\'s Permit Service',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Prepare Requirements',
                        'content' => 'Prepare all required documents: Community Tax Certificate (CTC), Barangay Clearance/Certification, Official Receipt (OR), Certificate of Registration (CR), TODA Certification, Professional Driver\'s License, Picture (2x2), Stencil of Motor and Chassis. These can be obtained from: Barangay of residence, Land Transportation Office, and Driver.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Submit Application',
                        'content' => 'Fill out the application form and submit all requirements to City Transportation Regulation Unit (CTRU). CTRU staff will inspect the tricycle, fill out POF, and register the application.',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Proceed to City Treasurer\'s Office for payment. Fees include: ICT Fee (Php 25.00), MTOP Fee (Php 100.00), Registration/Filing Fee (Php 150.00), Franchise Fee (Php 200.00), Sticker Fee (Php 65.00), Account Clearance (Php 50.00), POSO Clearance (Php 50.00). Total: Php 590.00. Penalties may apply for late renewal.',
                        'display_order' => 3
                    ],
                    [
                        'title' => 'Release of Permit',
                        'content' => 'CTRU staff will print Motorized Tricycle Operator\'s Permit (MTOP) and release documents. Processing time: 2 minutes per step.',
                        'display_order' => 4
                    ]
                ]
            ],
            [
                'name' => 'Fishtrap/Fishpen Permit',
                'icon_path' => '/assets/icons/civil-services.png',
                'steps' => [
                    [
                        'title' => 'Submit Requirements',
                        'content' => 'Proceed to Business Permit & Licensing Office (BPLO) for submission and verification of documents. Required documents: Personal Information System (PIS), Community Tax Certificate (CTC), Barangay Clearance, Certification from Brgy. Captain and duly noted by CFARMC Chairman, Certification from City Agriculturist, Contract of Lease (for New). These can be obtained from: Management Information Office (MIS), Barangay Hall or City Treasurer\'s Office, City Agriculture Office.',
                        'display_order' => 1
                    ],
                    [
                        'title' => 'Payment of Fees',
                        'content' => 'Proceed to City Treasurer\'s Office for payment. Fees include: Application Fee (Php 50.00), Mayor\'s Permit (Php 100.00), Inspection Fee (Php 200.00), Bond Deposit (Fishtrap: Php 300.00, Fishpen: Php 1,000.00), Clearing Bond (Fishtrap: Php 1,000.00, Fishpen: Php 1,000.00), Rental Fee (Fishtrap: Php 600.00/unit, Fishpen: Php 5.00/m2), Business Plate (Php 200.00), Sticker Fee (Php 65.00), ICT Fee (Php 25.00).',
                        'display_order' => 2
                    ],
                    [
                        'title' => 'Release of Permit',
                        'content' => 'Proceed to BPLO for releasing of Mayor\'s Permit. Processing time: 5-25 minutes.',
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

        $this->command->info('Successfully seeded ' . count($serviceTypes) . ' BPLO service types with steps!');
    }
}

