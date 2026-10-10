<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceStep;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Prioritized Civil Services
        $prioritizedCivilService = Service::firstOrCreate(
            ['name' => 'Prioritized Civil Services'],
            [
                'icon_path' => '/assets/icons/civil-services.png',
                'is_active' => true,
                'department' => 'Civil Registry Office'
            ]
        );

        // Service Types for Prioritized Civil Services
        $delayedRegularNonBrap = ServiceType::firstOrCreate([
            'service_id' => $prioritizedCivilService->id,
            'name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project'
        ], [
            'is_active' => true
        ]);

        $delayedBrap = ServiceType::firstOrCreate([
            'service_id' => $prioritizedCivilService->id,
            'name' => 'Delayed BRAP Birth Registration Assistance Project'
        ], [
            'is_active' => true
        ]);

        // Add service steps for Delayed Regular Non-BRAP
        $this->createSteps($delayedRegularNonBrap->id, [
            'Step 1: Fill out basic request information (Name, Contact Number)',
            'Step 2: Submit request and receive tracking number',
            'Step 3: Wait for officer to provide checklist of requirements',
            'Step 4: Submit required documents for processing',
            'Step 5: Request will be accepted after 10 business days'
        ]);

        // Add service steps for Delayed BRAP
        $this->createSteps($delayedBrap->id, [
            'Step 1: Fill out basic request information (Name, Contact Number)',
            'Step 2: Submit request and receive tracking number',
            'Step 3: Request will be encoded to BRAP Request List',
            'Step 4: Officer will provide checklist of requirements',
            'Step 5: Submit documents and wait for BRAP processing result'
        ]);

        // Business Permit and Licensing Office (BPLO) Services
        $bploService = Service::firstOrCreate(
            ['name' => 'Business Permit and Licensing Office (BPLO)'],
            [
                'icon_path' => '/assets/icons/business-permit.png',
                'is_active' => true,
                'department' => 'Business Permit and Licensing Office'
            ]
        );

        // Service Types for BPLO
        $newPermit = ServiceType::firstOrCreate([
            'service_id' => $bploService->id,
            'name' => 'Application for New Business Permit'
        ], [
            'is_active' => true
        ]);

        $renewalPermit = ServiceType::firstOrCreate([
            'service_id' => $bploService->id,
            'name' => 'Renewal of Business Permit'
        ], [
            'is_active' => true
        ]);

        // Add service steps for New Business Permit
        $this->createSteps($newPermit->id, [
            'Step 1: Proceed to BOSS - Submit pre-requirements and application form (25 minutes)',
            'Step 2: Payment of Mayor\'s Permit and other regulatory fees (10 minutes)',
            'Step 3: Secure Mayor\'s Permit and Business Plate (25 minutes)',
            'Submit application request and receive tracking number'
        ]);

        // Add service steps for Business Permit Renewal
        $this->createSteps($renewalPermit->id, [
            'Step 1: Proceed to BOSS - Submit pre-requirements (10 minutes)',
            'Step 2: Assessment and payment at CTO (30 minutes)',
            'Step 3: Secure Mayor\'s Permit and sticker (20 minutes)',
            'Submit renewal request and receive tracking number'
        ]);

        // City Transportation Regulation Unit (CTRU) Services
        $ctruService = Service::firstOrCreate(
            ['name' => 'City Transportation Regulation Unit (CTRU)'],
            [
                'icon_path' => '/assets/icons/transportation.png',
                'is_active' => true,
                'department' => 'City Transportation Regulation Unit'
            ]
        );

        // Service Types for CTRU
        $mtopService = ServiceType::firstOrCreate([
            'service_id' => $ctruService->id,
            'name' => 'Motorized Tricycle Operator\'s Permit Service (MTOP)'
        ], [
            'is_active' => true
        ]);

        $boatService = ServiceType::firstOrCreate([
            'service_id' => $ctruService->id,
            'name' => 'Mayor\'s Permit for Passenger Boat Service'
        ], [
            'is_active' => true
        ]);

        // Add service steps for MTOP
        $this->createSteps($mtopService->id, [
            'Step 1: Fill up PMTSRF and submit initial requirements (2-5 minutes)',
            'Step 2: Payment at City Treasurer\'s Office (3 minutes)',
            'Step 3: CTRU processing and document preparation (1-2 minutes)',
            'Step 4: CTRU Head and City Administrator approval (2 minutes)',
            'Step 5: Release of MTOP and sticker (2 minutes)',
            'Submit MTOP request and receive tracking number'
        ]);

        // Add service steps for Passenger Boat Service
        $this->createSteps($boatService->id, [
            'Step 1: Submit requirements and fill up MPPBAF (3 minutes)',
            'Step 2: Payment at City Treasurer\'s Office (3 minutes)',
            'Step 3: CTRU processing and permit preparation (3 minutes)',
            'Step 4: CTRU Head and City Administrator approval (2 minutes)',
            'Step 5: Release of Mayor\'s Permit and sticker (2 minutes)',
            'Submit boat permit request and receive tracking number'
        ]);

        // Engineering Office Services
        $engineeringService = Service::firstOrCreate(
            ['name' => 'Engineering Office'],
            [
                'icon_path' => '/assets/icons/engineering.png',
                'is_active' => true,
                'department' => 'Engineering Office'
            ]
        );

        // Service Types for Engineering Office
        $locationalService = ServiceType::firstOrCreate([
            'service_id' => $engineeringService->id,
            'name' => 'Securing Locational Clearance for Building Permit'
        ], [
            'is_active' => true
        ]);

        $buildingService = ServiceType::firstOrCreate([
            'service_id' => $engineeringService->id,
            'name' => 'Securing a Building Permit & Other Building Related Permits'
        ], [
            'is_active' => true
        ]);

        // Add service steps for Locational Clearance
        $this->createSteps($locationalService->id, [
            'Step 1: Submit application form and required documents (15 minutes)',
            'Step 2: Verification of Land Use (Zoning) - 1 day',
            'Step 3: Assessment and payment order of fees (1 day)',
            'Step 4: Payment at City Treasury Office (2 minutes)',
            'Step 5: Approval and release of Locational Clearance (1 day, 5 minutes)',
            'Submit locational clearance request and receive tracking number'
        ]);

        // Add service steps for Building Permit
        $this->createSteps($buildingService->id, [
            'Step 1: Submit application form and required documents (30 minutes)',
            'Step 2: Evaluation of plans and documents (1-5 days)',
            'Step 3: Inspection and verification (18 days for highly technical)',
            'Step 4: Assessment and payment order of fees (1 hour)',
            'Step 5: Payment at City Treasury Office (2 minutes)',
            'Step 6: Fire endorsement preparation (5 minutes)',
            'Step 7: Permit approval and release (1 day, 5 minutes)',
            'Submit building permit request and receive tracking number'
        ]);

        // Social Welfare and Development Office Services
        $socialWelfareService = Service::firstOrCreate(
            ['name' => 'Social Welfare and Development Office'],
            [
                'icon_path' => '/assets/icons/social-welfare.png',
                'is_active' => true,
                'department' => 'Social Welfare and Development Office'
            ]
        );

        // Service Types for Social Welfare and Development Office
        $aicsService = ServiceType::firstOrCreate([
            'service_id' => $socialWelfareService->id,
            'name' => 'Assistance to Individual in Crisis Situation (AICS)'
        ], [
            'is_active' => true
        ]);

        $childDevService = ServiceType::firstOrCreate([
            'service_id' => $socialWelfareService->id,
            'name' => 'Child Development Services'
        ], [
            'is_active' => true
        ]);

        // Add service steps for AICS
        $this->createSteps($aicsService->id, [
            'Step 1: Submit requirements and sign in logbook (2 minutes)',
            'Step 2: Interview and gather data for AICS form (10 minutes)',
            'Step 3: Review and recommendation by CSWD Officer (5 minutes)',
            'Step 4: Endorsement to City Mayor\'s Office for final approval',
            'Submit AICS request and receive tracking number'
        ]);

        // Add service steps for Child Development Services
        $this->createSteps($childDevService->id, [
            'Step 1: Registration of children with required documents (5 minutes)',
            'Step 2: Attend parents\' orientation (2 hours)',
            'Step 3: Attend child development sessions (2.5 hours per session)',
            'Step 4: Monthly sessions for 10 months (June to March)',
            'Submit child development services request and receive tracking number'
        ]);

        // City Veterinary Office Services
        $veterinaryService = Service::firstOrCreate(
            ['name' => 'City Veterinary Office'],
            [
                'icon_path' => '/assets/icons/veterinary.png',
                'is_active' => true,
                'department' => 'City Veterinary Office'
            ]
        );

        // Service Types for City Veterinary Office
        $petRegService = ServiceType::firstOrCreate([
            'service_id' => $veterinaryService->id,
            'name' => 'Pet Registration'
        ], [
            'is_active' => true
        ]);

        $antiRabiesService = ServiceType::firstOrCreate([
            'service_id' => $veterinaryService->id,
            'name' => 'Anti-Rabies Vaccination for Walk-in Clients'
        ], [
            'is_active' => true
        ]);

        // Add service steps for Pet Registration
        $this->createSteps($petRegService->id, [
            'Step 1: Bring pet to office and inquire about requirements (5 minutes)',
            'Step 2: Fill up Pre-Registration Form (5 minutes)',
            'Step 3: Encode information into PetRis System and print form (5 minutes)',
            'Step 4: Pay registration and ICT fees (3 minutes)',
            'Step 5: Sign logbook and receive Pet Registration Form (1 minute)',
            'Submit pet registration request and receive tracking number'
        ]);

        // Add service steps for Anti-Rabies Vaccination
        $this->createSteps($antiRabiesService->id, [
            'Step 1: Bring pet and inquire about requirements (2 minutes)',
            'Step 2: Check Personal Information System (PIS) enrollment (3 minutes)',
            'Step 3: Sign logbook and pay vaccination fees (5 minutes)',
            'Step 4: Vaccine preparation and inoculation (3 minutes)',
            'Step 5: Post-vaccination examination and registration (2 minutes)',
            'Submit anti-rabies vaccination request and receive tracking number'
        ]);

    }

    private function createSteps($serviceTypeId, $stepDescriptions)
    {
        foreach ($stepDescriptions as $index => $description) {
            ServiceStep::firstOrCreate([
                'service_type_id' => $serviceTypeId,
                'display_order' => $index + 1
            ], [
                'title' => 'Step ' . ($index + 1),
                'content' => $description
            ]);
        }
    }
}


