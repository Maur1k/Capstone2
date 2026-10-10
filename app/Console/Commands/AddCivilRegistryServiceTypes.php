<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;
use Illuminate\Console\Command;

class AddCivilRegistryServiceTypes extends Command
{
    protected $signature = 'services:add-civil-registry-types {service_id=24}';
    protected $description = 'Add 3 service types and their steps for Civil Registry Office service';

    public function handle()
    {
        $serviceId = $this->argument('service_id');
        $service = Service::find($serviceId);

        if (!$service) {
            $this->error("Service with ID {$serviceId} not found!");
            return 1;
        }

        $this->info("Adding service types to: {$service->name} (ID: {$serviceId})");

        // Get current max display_order
        $maxOrder = ServiceType::where('service_id', $serviceId)->max('display_order') ?? 0;

        // Service Type 1: Delayed Registration of Certificates of Marriage
        $type1 = ServiceType::create([
            'service_id' => $serviceId,
            'name' => 'Delayed Registration of Certificates of Marriage (COM) Municipal Form No. 97',
            'icon_path' => '/assets/icons/civil-services.png',
            'is_active' => true,
            'display_order' => ++$maxOrder,
        ]);

        $this->info("Created service type: {$type1->name}");

        // Steps for Type 1
        $steps1 = [
            [
                'title' => 'Submission and Assessment of Documents - Step 1.1',
                'content' => 'Signs the Visitor\'s Log Book and gets queuing number. Agency interviews and assists client. Processing Time: 3 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 1,
            ],
            [
                'title' => 'Submission and Assessment of Documents - Step 1.2',
                'content' => 'Submits queuing number, documents and requirements. Agency receives queuing number and reviews documents and requirements. Processing Time: 5 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 2,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.1',
                'content' => 'Payment of fees at City Treasurer\'s Office. Fees: Penalty Fee for Delayed Registration of Certificate of Marriage (PHP 30.00), Marriage Solemnization Fee if solemnized by the City Mayor (PHP 100.00), Sponsor\'s Fee if solemnized by the City Mayor (PHP 30.00 per sponsor), Documentary Stamp Tax (PHP 30.00 per document), ICT Fee (PHP 20.00 per O.R.). Person Responsible: City Treasurer\'s Office',
                'display_order' => 3,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.2',
                'content' => 'Presents Official Receipt and receives Verification. Agency receives receipt, processes application for Delayed Registration of Marriage Certificate, issues Verification Slip and gives instruction to client to return after the 10-day reglementary posting period. Processing Time: 20 minutes. Person Responsible: Lovely D. Milos / Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 4,
            ],
            [
                'title' => 'Releasing of Document - Step 3.1',
                'content' => 'Proceed to City Civil Registry Office (CCRO) after the 10-day reglementary posting period, signs Visitor\'s Log Book, presents Verification Slip and gets queuing number. Agency assists and receives queuing number and Verification Slip. Processing Time: 5 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 5,
            ],
            [
                'title' => 'Releasing of Document - Step 3.2',
                'content' => 'Receives copy of duly registered Certificate of Marriage and signs log book. Agency releases/issues copy of duly registered Certificate of Marriage and assists in signing log book. Processing Time: 5 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa. Total Processing Time: 38 minutes after 10-day posting period of Notice',
                'display_order' => 6,
            ],
        ];

        foreach ($steps1 as $step) {
            ServiceStep::create(array_merge($step, ['service_type_id' => $type1->id]));
        }
        $this->info("  Added " . count($steps1) . " steps");

        // Service Type 2: Delayed Registration of Certificates of Death
        $type2 = ServiceType::create([
            'service_id' => $serviceId,
            'name' => 'Delayed Registration of Certificates of Death Municipal Form No. 103 and Municipal Form No. 103 A',
            'icon_path' => '/assets/icons/civil-services.png',
            'is_active' => true,
            'display_order' => ++$maxOrder,
        ]);

        $this->info("Created service type: {$type2->name}");

        // Steps for Type 2
        $steps2 = [
            [
                'title' => 'Submission and Assessment of Documents - Step 1.1',
                'content' => 'Signs the Visitor\'s Log Book and gets queuing number. Agency interviews and assists client. Processing Time: 3 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 1,
            ],
            [
                'title' => 'Submission and Assessment of Documents - Step 1.2',
                'content' => 'Submits queuing number, documents and requirements. Agency receives queuing number and reviews documents and requirements. Processing Time: 5 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 2,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.1',
                'content' => 'Payment of fees at City Treasurer\'s Office. Fees: Penalty Fee for Delayed Registration of Certificate of Death (PHP 500.00), Burial Permit Fee (PHP 100.00), Transfer Permit Fee if applicable (PHP 200.00), Documentary Stamp Tax (PHP 30.00 per document), ICT Fee (PHP 15.00). Person Responsible: City Treasurer\'s Office',
                'display_order' => 3,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.2',
                'content' => 'Presents Official Receipt and receives Verification. Agency receives receipt, processes application for delayed registration of Death Certificate, issues Verification Slip and gives instruction to client to return after the 10-day reglementary posting period. Processing Time: 20 minutes. Person Responsible: Lovely D. Miles / Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 4,
            ],
            [
                'title' => 'Releasing of Document - Step 3.1',
                'content' => 'Proceed to City Civil Registry Office (CCRO) after the 10-day reglementary posting period, signs Visitor\'s Log Book, presents Verification Slip and gets queuing number. Agency assists and receives queuing number and Verification Slip. Processing Time: 3 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa',
                'display_order' => 5,
            ],
            [
                'title' => 'Releasing of Document - Step 3.2',
                'content' => 'Receives copy of duly registered Certificate of Death and signs log book. Agency releases/issues copy of duly registered Certificate of Death and assists in signing log book. Processing Time: 5 minutes. Person Responsible: Jonard R. Ochoa / Evita Christie B. Macasieb / Gemma R. Ocampo / Helen B. Mesa. Total Processing Time: 36 minutes and 10-day posting period of Notice',
                'display_order' => 6,
            ],
        ];

        foreach ($steps2 as $step) {
            ServiceStep::create(array_merge($step, ['service_type_id' => $type2->id]));
        }
        $this->info("  Added " . count($steps2) . " steps");

        // Service Type 3: Issuance of Certified Photocopy or Transcription
        $type3 = ServiceType::create([
            'service_id' => $serviceId,
            'name' => 'Issuance of Certified Photocopy or Transcription of Civil Registry Documents',
            'icon_path' => '/assets/icons/civil-services.png',
            'is_active' => true,
            'display_order' => ++$maxOrder,
        ]);

        $this->info("Created service type: {$type3->name}");

        // Steps for Type 3
        $steps3 = [
            [
                'title' => 'Submit Request - Step 1.1',
                'content' => 'Submits queuing number and request form. Agency receives queuing number and checks accuracy and completeness of request form. Processing Time: 5 minutes. Person Responsible: Gerome R. Ocampo / Maricar R. Nening / Jonalen B. Pascua / Evita Christie B. Macasaet / Jenard R. Ochoa / Maribeth C. Barbon / Helen B. Mesa',
                'display_order' => 1,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.1',
                'content' => 'Payment of fees at City Treasurer\'s Office. Fees: CTC/Cedula (PHP 30.00), Documentary Stamp Tax (PHP 30.00), ICT Fee (PHP 20.00). Person Responsible: City Treasurer\'s Office',
                'display_order' => 2,
            ],
            [
                'title' => 'Payment of Applicable Fees at the Treasurer\'s Office - Step 2.2',
                'content' => 'Presents Official Receipt. Agency receives receipt and processes request. Processing Time: 20 minutes. Person Responsible: Lovely D. Nilles / Gerome R. Ocampo / Maricar R. Nening / Jonalen B. Pascua / Evita Christie B. Macasaet / Jenard R. Ochoa / Maribeth C. Barbon / Helen B. Mesa',
                'display_order' => 3,
            ],
            [
                'title' => 'Releasing of Document - Step 3.1',
                'content' => 'Receive requested document and signs releasing log book. Agency releases requested document and assists client in signing Log Book. Processing Time: 5 minutes. Person Responsible: Gerome R. Ocampo / Maricar R. Nening / Jonalen B. Pascua / Evita Christie B. Macasaet / Jenard R. Ochoa / Maribeth C. Barbon / Helen B. Mesa. Total Processing Time: 35 minutes',
                'display_order' => 4,
            ],
        ];

        foreach ($steps3 as $step) {
            ServiceStep::create(array_merge($step, ['service_type_id' => $type3->id]));
        }
        $this->info("  Added " . count($steps3) . " steps");

        $this->info("\n✅ Successfully added 3 service types with their steps!");
        return 0;
    }
}









