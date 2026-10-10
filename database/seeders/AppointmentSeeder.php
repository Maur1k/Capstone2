<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CivilService;
use App\Models\Appointment;
use App\Models\DocumentUpload;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find existing civil services or create minimal ones
        $existingCivilService = CivilService::first();

        if (!$existingCivilService) {
            // Create a minimal civil service for testing
            $existingCivilService = CivilService::create([
                'tracking_number' => 'CR-' . date('Ymd') . '-TEST01',
                'queue_number' => '1',
                'service_type' => 'Civil Service',
                'status' => 'submitted',
                'full_name' => 'Test Service',
                'contact_number' => '09123456789',
                'email' => 'test@alaminoscity.gov.ph',
                'address' => 'Civil Registry Office, Alaminos City'
            ]);
        }

        // Create sample appointments
        $appointment1 = Appointment::create([
            'appointment_number' => 'APT' . date('Ymd') . '0001',
            'civil_service_id' => $existingCivilService->id,
            'service_type' => 'Civil Service',
            'full_name' => 'Juan Dela Cruz',
            'contact_number' => '09123456789',
            'email' => 'juan.delacruz@email.com',
            'address' => '123 Main Street, Alaminos City',
            'appointment_date' => now()->addDays(3)->format('Y-m-d'),
            'appointment_time' => '09:00',
            'status' => 'pending',
            'notes' => 'Sample appointment for testing'
        ]);

        $appointment2 = Appointment::create([
            'appointment_number' => 'APT' . date('Ymd') . '0002',
            'civil_service_id' => $existingCivilService->id,
            'service_type' => 'Civil Service',
            'full_name' => 'Maria Santos',
            'contact_number' => '09123456790',
            'email' => 'maria.santos@email.com',
            'address' => '456 Oak Avenue, Alaminos City',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '14:00',
            'status' => 'confirmed',
            'notes' => 'Sample confirmed appointment'
        ]);

        // Create sample document uploads
        DocumentUpload::create([
            'civil_service_id' => $existingCivilService->id,
            'requirement_name' => 'Certificate of Live Birth',
            'file_name' => 'sample_colb.pdf',
            'file_path' => 'appointment_documents/sample_colb.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
            'status' => 'pending'
        ]);

        DocumentUpload::create([
            'civil_service_id' => $existingCivilService->id,
            'requirement_name' => 'Valid Government ID',
            'file_name' => 'sample_id.jpg',
            'file_path' => 'appointment_documents/sample_id.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 512000,
            'status' => 'approved'
        ]);
    }
}
