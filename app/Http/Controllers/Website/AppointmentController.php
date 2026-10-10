<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CivilService;
use App\Models\DocumentUpload;
use App\Services\NodeSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Store appointment data from service selection
     */
    public function storeServiceSelection(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        // Store in session for now, will be saved to database later
        session([
            'appointment_data.service_name' => $request->service_name,
            'appointment_data.department' => $request->department,
            'appointment_data.service_type' => $request->service_type,
            'appointment_data.description' => $request->description,
        ]);

        // Log for debugging
        \Log::info('Service selection saved:', $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service selection saved successfully'
        ]);
    }

    /**
     * Store appointment date and time
     */
    public function storeDateTime(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string'
        ]);

        $appointmentDate = \Carbon\Carbon::parse($request->appointment_date);
        $now = \Carbon\Carbon::now();
        
        // Check if it's a weekend
        if ($appointmentDate->isWeekend()) {
            return response()->json([
                'success' => false,
                'message' => 'Appointments are not available on weekends. Please select a weekday.'
            ], 422);
        }

        // Parse appointment time and create full datetime
        $appointmentTime = \Carbon\Carbon::createFromFormat('H:i', $request->appointment_time);
        $appointmentDateTime = $appointmentDate->copy()
            ->setTime($appointmentTime->hour, $appointmentTime->minute, 0);

        // Check if appointment time is in the past
        if ($appointmentDateTime->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot book an appointment in the past. Please select a future date and time.'
            ], 422);
        }

        // Check if time is within working hours (8:00 AM - 5:00 PM)
        $hour = $appointmentTime->hour;
        if ($hour < 8 || $hour >= 17) {
            return response()->json([
                'success' => false,
                'message' => 'Appointments are only available during working hours (8:00 AM - 5:00 PM). Please select a different time.'
            ], 422);
        }

        // Check if appointment is at least 30 minutes from now
        $minutesUntilAppointment = $now->diffInMinutes($appointmentDateTime, false);
        if ($minutesUntilAppointment < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Appointments must be booked at least 30 minutes in advance. Please select a different time.'
            ], 422);
        }

        // Store in session
        session([
            'appointment_data.appointment_date' => $request->appointment_date,
            'appointment_data.appointment_time' => $request->appointment_time,
        ]);

        // Log for debugging
        \Log::info('Date/time saved:', $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Date and time saved successfully'
        ]);
    }

    /**
     * Store personal details
     */
    public function storePersonalDetails(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'age' => 'required|integer|min:1|max:150'
        ]);

        // Store in session
        session([
            'appointment_data.full_name' => $request->full_name,
            'appointment_data.contact_number' => $request->contact_number,
            'appointment_data.age' => $request->age,
        ]);

        // Log for debugging
        \Log::info('Personal details saved:', $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Personal details saved successfully'
        ]);
    }

    /**
     * Create final appointment without documents
     */
    public function createAppointment(Request $request)
    {
        try {
            // Get appointment data from session
            $appointmentData = session('appointment_data', []);

            // Log session data for debugging
            \Log::info('Appointment creation started (no documents)');
            \Log::info('Appointment session data:', $appointmentData);

            if (empty($appointmentData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment data not found. Please start over.'
                ], 422);
            }

            // Validate required fields and provide specific error messages
            $missingFields = [];
            if (!isset($appointmentData['service_name'])) {
                $missingFields[] = 'service selection';
            }
            if (!isset($appointmentData['appointment_date'])) {
                $missingFields[] = 'appointment date';
            }
            if (!isset($appointmentData['appointment_time'])) {
                $missingFields[] = 'appointment time';
            }
            if (!isset($appointmentData['full_name'])) {
                $missingFields[] = 'full name';
            }
            if (!isset($appointmentData['contact_number'])) {
                $missingFields[] = 'contact number';
            }
            if (!isset($appointmentData['age'])) {
                $missingFields[] = 'age';
            }
            
            if (!empty($missingFields)) {
                $message = 'Missing required appointment information: ' . implode(', ', $missingFields) . '. Please complete all steps.';
                \Log::warning('Appointment creation failed - missing fields', [
                    'missing_fields' => $missingFields,
                    'session_data_keys' => array_keys($appointmentData)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            // Find or create civil service
            $civilService = CivilService::where('service_name', $appointmentData['service_name'])->first();
            if (!$civilService) {
                $civilService = CivilService::create([
                    'service_name' => $appointmentData['service_name'],
                    'department' => $appointmentData['department'] ?? 'Civil Registry',
                    'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                    'status' => 'submitted',
                    'tracking_number' => 'CR-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6)),
                    'queue_number' => '1',
                    'full_name' => $appointmentData['full_name'] ?? 'Service Request',
                    'contact_number' => $appointmentData['contact_number'] ?? 'N/A',
                    'email' => 'service@alaminoscity.gov.ph',
                    'address' => 'Civil Registry Office, Alaminos City'
                ]);
            }

            // Create appointment
            \Log::info('Creating appointment with data:', [
                'civil_service_id' => $civilService->id,
                'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                'full_name' => $appointmentData['full_name'],
                'contact_number' => $appointmentData['contact_number'],
                'appointment_date' => $appointmentData['appointment_date'],
                'appointment_time' => $appointmentData['appointment_time']
            ]);

            $appointment = Appointment::create([
                'appointment_number' => 'APT' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'civil_service_id' => $civilService->id,
                'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                'full_name' => $appointmentData['full_name'],
                'contact_number' => $appointmentData['contact_number'],
                'email' => $appointmentData['email'] ?? 'service@alaminoscity.gov.ph',
                'address' => $appointmentData['address'] ?? 'Civil Registry Office, Alaminos City',
                'appointment_date' => $appointmentData['appointment_date'],
                'appointment_time' => $appointmentData['appointment_time'],
                'status' => 'pending',
                'notes' => 'Appointment created via website'
            ]);

            \Log::info('Appointment created successfully:', ['id' => $appointment->id]);

            // Clear session data
            session()->forget('appointment_data');

            // Send SMS notification to the applicant
            $smsResult = $this->sendAppointmentConfirmationSms(
                $appointment,
                $appointmentData['contact_number'] ?? null,
                $appointmentData['full_name'] ?? null,
                $appointmentData['service_name'] ?? 'Civil Service',
                $appointmentData['appointment_date'] ?? null,
                $appointmentData['appointment_time'] ?? null,
                $appointmentData['age'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'sms_sent' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['success'] ? 'SMS confirmation sent' : ($smsResult['error'] ?? 'SMS not sent'),
                'appointment' => [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'status' => $appointment->status,
                    'service_name' => $appointmentData['service_name'] ?? 'Service Request',
                    'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                    'appointment_date' => $appointmentData['appointment_date'] ?? null,
                    'appointment_time' => $appointmentData['appointment_time'] ?? null,
                    'full_name' => $appointmentData['full_name'] ?? 'N/A',
                    'contact_number' => $appointmentData['contact_number'] ?? 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            // Log the full error for debugging
            \Log::error('Appointment creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $appointmentData ?? 'No session data',
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating appointment. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store uploaded documents and create final appointment (DEPRECATED - kept for backward compatibility)
     */
    public function storeDocuments(Request $request)
    {
        // Validate document uploads
        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240' // 10MB max
        ], [
            'documents.required' => 'Please upload at least one document.',
            'documents.min' => 'Please upload at least one document.',
            'documents.*.file' => 'One or more files are invalid.',
            'documents.*.mimes' => 'Only PDF, JPG, JPEG, PNG, DOC, and DOCX files are allowed.',
            'documents.*.max' => 'Each file must not exceed 10MB in size.'
        ]);

        try {
            // Get appointment data from session
            $appointmentData = session('appointment_data', []);

            // Log session data for debugging
            \Log::info('Appointment creation started');
            \Log::info('Appointment session data:', $appointmentData);
            \Log::info('Request files:', $request->allFiles());
            \Log::info('Request data:', $request->all());

            if (empty($appointmentData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment data not found. Please start over.'
                ], 422);
            }

            // Find or create civil service
            $civilService = CivilService::where('service_name', $appointmentData['service_name'])->first();
            if (!$civilService) {
                $civilService = CivilService::create([
                    'service_name' => $appointmentData['service_name'],
                    'department' => $appointmentData['department'] ?? 'Civil Registry',
                    'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                    'status' => 'submitted',
                    'tracking_number' => 'CR-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6)),
                    'queue_number' => '1',
                    'full_name' => $appointmentData['full_name'] ?? 'Service Request',
                    'contact_number' => $appointmentData['contact_number'] ?? 'N/A',
                    'email' => 'service@alaminoscity.gov.ph',
                    'address' => 'Civil Registry Office, Alaminos City'
                ]);
            }

            // Create appointment
            \Log::info('Creating appointment with data:', [
                'civil_service_id' => $civilService->id,
                'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                'full_name' => $appointmentData['full_name'],
                'contact_number' => $appointmentData['contact_number'],
                'email' => $appointmentData['email'] ?? 'service@alaminoscity.gov.ph',
                'address' => $appointmentData['address'] ?? 'Civil Registry Office, Alaminos City',
                'appointment_date' => $appointmentData['appointment_date'],
                'appointment_time' => $appointmentData['appointment_time']
            ]);

            $appointment = Appointment::create([
                'appointment_number' => 'APT' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'civil_service_id' => $civilService->id,
                'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                'full_name' => $appointmentData['full_name'],
                'contact_number' => $appointmentData['contact_number'],
                'email' => $appointmentData['email'] ?? 'service@alaminoscity.gov.ph',
                'address' => $appointmentData['address'] ?? 'Civil Registry Office, Alaminos City',
                'appointment_date' => $appointmentData['appointment_date'],
                'appointment_time' => $appointmentData['appointment_time'],
                'status' => 'pending',
                'notes' => 'Appointment created via website'
            ]);

            \Log::info('Appointment created successfully:', ['id' => $appointment->id]);

            // Handle document uploads
            $uploadedDocuments = [];
            $files = $request->file('documents');

            \Log::info('Files received:', [
                'files' => $files,
                'files_count' => $files ? count($files) : 0,
                'all_files' => $request->allFiles()
            ]);

            // Handle both array and single file uploads
            if ($files) {
                // Ensure files is an array
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('appointment_documents', $fileName, 'public');

                        \Log::info('Creating document upload:', [
                            'civil_service_id' => $civilService->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath
                        ]);

                        $document = DocumentUpload::create([
                            'civil_service_id' => $civilService->id,
                            'requirement_name' => $this->determineRequirementName($file->getClientOriginalName()),
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'status' => 'pending'
                        ]);

                        \Log::info('Document created successfully:', ['document_id' => $document->id]);
                        $uploadedDocuments[] = $document;
                    }
                }
            } else {
                \Log::warning('No files received');
            }

            // Clear session data
            session()->forget('appointment_data');

            // Send SMS notification to the applicant
            $smsResult = $this->sendAppointmentConfirmationSms(
                $appointment,
                $appointmentData['contact_number'] ?? null,
                $appointmentData['full_name'] ?? null,
                $appointmentData['service_name'] ?? 'Civil Service',
                $appointmentData['appointment_date'] ?? null,
                $appointmentData['appointment_time'] ?? null,
                $appointmentData['age'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'sms_sent' => $smsResult['success'] ?? false,
                'sms_message' => $smsResult['success'] ? 'SMS confirmation sent' : ($smsResult['error'] ?? 'SMS not sent'),
                'appointment' => [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'status' => $appointment->status,
                    'documents_count' => count($uploadedDocuments),
                    'service_name' => $appointmentData['service_name'] ?? 'Service Request',
                    'service_type' => $appointmentData['service_type'] ?? 'Civil Service',
                    'appointment_date' => $appointmentData['appointment_date'] ?? null,
                    'appointment_time' => $appointmentData['appointment_time'] ?? null,
                    'full_name' => $appointmentData['full_name'] ?? 'N/A',
                    'contact_number' => $appointmentData['contact_number'] ?? 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            // Log the full error for debugging
            \Log::error('Appointment creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $appointmentData ?? 'No session data',
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating appointment. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send appointment confirmation SMS
     */
    private function sendAppointmentConfirmationSms($appointment, $phoneNumber, $fullName, $serviceName, $appointmentDate, $appointmentTime, $age = null)
    {
        if (!$phoneNumber) {
            \Log::warning('No phone number provided for appointment confirmation SMS', [
                'appointment_number' => $appointment->appointment_number
            ]);
            return ['success' => false, 'error' => 'No phone number provided'];
        }

        try {
            $smsService = new NodeSmsService();

            // Format date for display
            $formattedDate = $appointmentDate;
            if ($appointmentDate) {
                try {
                    $dateObj = \Carbon\Carbon::parse($appointmentDate);
                    $formattedDate = $dateObj->format('F j, Y'); // e.g., "November 25, 2025"
                } catch (\Exception $e) {
                    // Keep original format if parsing fails
                }
            }

            // Format time for display
            $formattedTime = $appointmentTime;
            if ($appointmentTime && strpos($appointmentTime, ':') !== false) {
                $timeParts = explode(':', $appointmentTime);
                $hours = (int) $timeParts[0];
                $minutes = $timeParts[1] ?? '00';
                $ampm = $hours >= 12 ? 'PM' : 'AM';
                $displayHour = $hours % 12 ?: 12;
                $formattedTime = "{$displayHour}:{$minutes} {$ampm}";
            }

            // Format service name for display
            $displayServiceName = $serviceName;
            if ($serviceName && stripos($serviceName, 'Delayed Regular Non-BRAP') !== false) {
                $displayServiceName = 'Delayed Birth Registration (Regular)';
            } elseif ($serviceName && stripos($serviceName, 'Delayed BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) {
                $displayServiceName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
            }

            // Create the SMS message
            $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
            $message = "{$greeting}\n\n";
            $message .= "Your appointment has been booked successfully!\n\n";
            $message .= "REFERENCE CODE: {$appointment->appointment_number}\n\n";
            $message .= "Service: {$displayServiceName}\n";
            $message .= "Date: {$formattedDate}\n";
            $message .= "Time: {$formattedTime}\n\n";
            $message .= "IMPORTANT REMINDER:\n";
            $message .= "Paalala: Kung ma-late ka, makiki-pila ka na o maghihintay ka sa queue onsite.\n";
            $message .= "If you arrive late, you will need to join the queue or wait in the onsite queue.\n\n";
            $message .= "Visit our office on your scheduled date with required documents\n\n";
            $message .= "Track your Appointment {$appointment->appointment_number} at Click2Serve Tracking feature.\n\n";
            $message .= "Thank you for using Click2Serve Website!";

            \Log::info('Sending appointment confirmation SMS', [
                'appointment_number' => $appointment->appointment_number,
                'phone_number' => $phoneNumber,
                'message_length' => strlen($message)
            ]);

            // Send SMS using direct method (more reliable)
            $result = $smsService->sendSMSDirect($phoneNumber, $message);

            \Log::info('Appointment confirmation SMS result', [
                'appointment_number' => $appointment->appointment_number,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null
            ]);

            // Send follow-up SMS messages based on service type
            if ($result['success'] ?? false) {
                // Use original service name for detection and pass age
                $this->sendFollowUpSmsForRegularService($appointment, $phoneNumber, $fullName, $serviceName, $age);
                $this->sendFollowUpSmsForBrapService($appointment, $phoneNumber, $fullName, $serviceName, $age);
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Failed to send appointment confirmation SMS', [
                'appointment_number' => $appointment->appointment_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send follow-up SMS messages for Regular Delayed Birth Registration service
     */
    private function sendFollowUpSmsForRegularService($appointment, $phoneNumber, $fullName, $serviceName, $age = null)
    {
        // Check if this is a Regular service (not BRAP)
        $isRegular = (stripos($serviceName, 'Delayed Regular Non-BRAP') !== false ||
            stripos($serviceName, 'Delayed Birth Registration (Regular)') !== false);

        if (!$isRegular) {
            return; // Only send for Regular service
        }

        try {
            $smsService = new NodeSmsService();
            $isOctogenarian = $age !== null && $age >= 80;

            // Wait a bit before sending first follow-up (2 seconds)
            sleep(2);

            if ($isOctogenarian) {
                // For 80 years old and above: Send Octogenarian checklist only
                $this->sendRegularServiceOctogenarianChecklistSms($smsService, $appointment, $phoneNumber, $fullName);
            } else {
                // For 79 and below: Send key requirements and complete checklist
                // Send first SMS: Key requirements (9 items)
                $this->sendRegularServiceKeyRequirementsSms($smsService, $appointment, $phoneNumber, $fullName);

                // Wait a bit before sending second follow-up (3 seconds)
                sleep(3);

                // Send second SMS: Complete checklist Part 1 (items 1-14)
                $this->sendRegularServiceCompleteChecklistPart1Sms($smsService, $appointment, $phoneNumber, $fullName);

                // Wait a bit before sending third follow-up (3 seconds)
                sleep(3);

                // Send third SMS: Complete checklist Part 2 (items 15-29)
                $this->sendRegularServiceCompleteChecklistPart2Sms($smsService, $appointment, $phoneNumber, $fullName);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send follow-up SMS for Regular service', [
                'appointment_number' => $appointment->appointment_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send follow-up SMS messages for BRAP Delayed Birth Registration service
     */
    private function sendFollowUpSmsForBrapService($appointment, $phoneNumber, $fullName, $serviceName, $age = null)
    {
        // Check if this is a BRAP service
        $isBrap = (stripos($serviceName, 'Delayed BRAP') !== false &&
            stripos($serviceName, 'Non-BRAP') === false) ||
            stripos($serviceName, 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)') !== false;

        if (!$isBrap) {
            return; // Only send for BRAP service
        }

        try {
            $smsService = new NodeSmsService();
            $isOctogenarian = $age !== null && $age >= 80;

            // Wait a bit before sending first follow-up (2 seconds)
            sleep(2);

            if ($isOctogenarian) {
                // For 80 years old and above: Send Octogenarian checklist only
                $this->sendBrapServiceOctogenarianChecklistSms($smsService, $appointment, $phoneNumber, $fullName);
            } else {
                // For 79 and below: Send key requirements and complete checklist
                // Send first SMS: Key requirements for BRAP (79 and below)
                $this->sendBrapServiceKeyRequirementsSms($smsService, $appointment, $phoneNumber, $fullName);

                // Wait a bit before sending second follow-up (3 seconds)
                sleep(3);

                // Send second SMS: Complete BRAP checklist Part 1 (items 1-9)
                $this->sendBrapServiceCompleteChecklistPart1Sms($smsService, $appointment, $phoneNumber, $fullName);

                // Wait a bit before sending third follow-up (3 seconds)
                sleep(3);

                // Send third SMS: Complete BRAP checklist Part 2 (items 10-18)
                $this->sendBrapServiceCompleteChecklistPart2Sms($smsService, $appointment, $phoneNumber, $fullName);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send follow-up SMS for BRAP service', [
                'appointment_number' => $appointment->appointment_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send key requirements SMS (9 items) for Regular Delayed Birth Registration
     */
    private function sendRegularServiceKeyRequirementsSms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "KEY REQUIREMENTS for Delayed Birth Registration (Regular):\n";
        $message .= "1. Duly Accomplished Municipal Form No. 102 (4 Copies)\n";
        $message .= "2. Negative Certification from PSA\n";
        $message .= "3. Any 2 documentary evidences (Baptismal, School Record, Voter's Record, etc.)\n";
        $message .= "4. National ID of document owner (1 year old and above)\n";
        $message .= "5. Any Government Issued ID of owner and applicant\n";
        $message .= "6. Certificate of Marriage, if married (PSA Copy)\n";
        $message .= "7. Certificate of Residency from Barangay\n";
        $message .= "8. Unedited 2x2 photo (white background, 4 copies)\n";
        $message .= "9. Personal Appearance required\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "Complete checklist will follow...";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('Regular service key requirements SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send complete checklist SMS Part 1 (items 1-14) for Regular Delayed Birth Registration
     */
    private function sendRegularServiceCompleteChecklistPart1Sms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "COMPLETE CHECKLIST Part 1/2 - Delayed Birth Registration (Regular):\n\n";
        $message .= "1. Municipal Form No. 102 (4 Copies)\n";
        $message .= "2. Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)\n";
        $message .= "3. Civil Registry Form 1B or 1C\n";
        $message .= "4. Negative Certification from PSA\n";
        $message .= "5. Affidavit for Delayed Registration\n";
        $message .= "6. Any 2 documentary evidences:\n";
        $message .= "   a) Baptismal Certificate\n";
        $message .= "   b) School Record\n";
        $message .= "   c) Voter's Record/Certification\n";
        $message .= "   d) Medical/Immunization Record\n";
        $message .= "   e) PhilHealth/SSS/GSIS Record\n";
        $message .= "   f) Barangay Captain's Certification\n";
        $message .= "   g) Other valid documents\n";
        $message .= "7. National ID (1 year old and above)\n";
        $message .= "8. Government Issued ID of owner and applicant\n";
        $message .= "9. Updated CEDULA\n";
        $message .= "10. Certificate of Marriage (if married, PSA Copy)\n";
        $message .= "11. Certificate of Residency\n";
        $message .= "12. Death Certificate (if owner deceased, PSA Copy)\n";
        $message .= "13. Affidavit of Undertaking (if owner deceased)\n";
        $message .= "14. Any 2 evidences of Parents' Identity:\n";
        $message .= "   a) Parents' Birth Certificate\n";
        $message .= "   b) Parents' Government ID\n";
        $message .= "   c) Parents' Marriage Certificate\n";
        $message .= "   d) Parents' Death Certificate\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "Part 2 will follow...";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('Regular service complete checklist Part 1 SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send complete checklist SMS Part 2 (items 15-29) for Regular Delayed Birth Registration
     */
    private function sendRegularServiceCompleteChecklistPart2Sms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "COMPLETE CHECKLIST Part 2/2 - Delayed Birth Registration (Regular):\n\n";
        $message .= "15. Personal Appearance (18 years old and above)\n";
        $message .= "16. Personal Appearance of Parents (Marital Minor)\n";
        $message .= "17. Personal Appearance of Mother (Non-marital minor)\n";
        $message .= "18. Affidavit/Sworn Statement (Mother's whereabouts)\n";
        $message .= "19. 2x2 Photo (white background, 4 copies, within 3 months)\n";
        $message .= "20. Affidavit of Admission of Paternity (if applicable)\n";
        $message .= "21. AUSF under RA 9255 (if applicable, register within 20 days)\n";
        $message .= "22. Sworn Attestation (for child 7-17 years old)\n";
        $message .= "23. Affidavit of Acknowledgement (non-marital child before Aug 3, 1988)\n";
        $message .= "24. Birth Certificate of Siblings (if applicable)\n";
        $message .= "25. Affidavit of Two Disinterested Persons\n";
        $message .= "26. For Foreigner Parent (if applicable):\n";
        $message .= "   a) Parents' Marriage Certificate\n";
        $message .= "   b) Parents' Birth Certificate\n";
        $message .= "   c) Valid Passport/BI Clearance/ACR I-Card\n";
        $message .= "27. Certification from MCR (PSA Form No.1, 2024) - Out of Town\n";
        $message .= "28. Special Power of Attorney/Authorization Letter\n";
        $message .= "29. Valid ID of Authorizing person and authorized person\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "City Civil Registry Office\n";
        $message .= "Contact: 09457449761";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('Regular service complete checklist Part 2 SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send Octogenarian checklist SMS (13 items for 80 years old and above) for Regular service
     */
    private function sendRegularServiceOctogenarianChecklistSms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "OCTOGENARIAN CHECKLIST (80 years old and above)\n";
        $message .= "Delayed Birth Registration (Regular)\n\n";
        $message .= "If you are 80 years old and above, please use this simplified checklist:\n";
        $message .= "1. Municipal Form No. 102 (4 Copies) - prepared at LCRO\n";
        $message .= "2. Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)\n";
        $message .= "3. Civil Registry Form 1C/Birth Destroyed (issued by LCRO)\n";
        $message .= "4. Negative Omnibus Certification/Negative Certification from PSA (processed at LCRO)\n";
        $message .= "5. Affidavit for Delayed Registration (at back of COLB)\n";
        $message .= "6. Affidavit of Two (2) Disinterested Persons\n";
        $message .= "7. Certificate of Indigency (issued by Punong Barangay)\n";
        $message .= "8. Any proof of identity:\n";
        $message .= "   a) Barangay Certification\n";
        $message .= "   b) Voter's Record/Certification\n";
        $message .= "   c) Member's Data Record (Senior Citizen, PhilHealth, SSS, GSIS)\n";
        $message .= "   d) Any Government Issued ID\n";
        $message .= "9. Special Power of Attorney/Authorization Letter (if applicable)\n";
        $message .= "10. Valid ID of Authorizing person and authorized person (if applicable)\n";
        $message .= "11. Others (if applicable)\n";
        $message .= "12. National ID\n";
        $message .= "13. 2x2 picture\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "City Civil Registry Office\n";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('Regular service Octogenarian checklist SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send key requirements SMS for BRAP Delayed Birth Registration (79 and below)
     */
    private function sendBrapServiceKeyRequirementsSms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "KEY REQUIREMENTS for BRAP (79 years old and below):\n\n";
        $message .= "1. Municipal Form No. 102 (4 Copies)\n";
        $message .= "2. Negative Certification from PSA\n";
        $message .= "3. Affidavit of Two Disinterested Persons\n";
        $message .= "4. Certificate of Indigency from Barangay\n";
        $message .= "5. Any proof of identity (Barangay Cert, Medical Record, Baptismal, etc.)\n";
        $message .= "6. Personal Appearance required\n";
        $message .= "7. Affidavit for Delayed Registration\n";
        $message .= "8. Civil Registry Form 1B or 1C\n";
        $message .= "9. National ID (if applicable)\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "Complete BRAP checklist will follow...";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('BRAP service key requirements SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send complete BRAP checklist SMS Part 1 (items 1-9) for 79 and below
     */
    private function sendBrapServiceCompleteChecklistPart1Sms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "COMPLETE CHECKLIST Part 1/2 - BRAP (79 years old and below):\n\n";
        $message .= "1. Municipal Form No. 102 (4 Copies)\n";
        $message .= "   Note: For Home Birth, prepared at LCRO\n";
        $message .= "2. Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)\n";
        $message .= "3. Civil Registry Form 1B or 1C\n";
        $message .= "4. Negative Omnibus Certification/Negative Certification from PSA (processed at LCRO)\n";
        $message .= "5. Affidavit for Delayed Registration (at back of COLB)\n";
        $message .= "6. Affidavit of Two (2) Disinterested Persons\n";
        $message .= "7. Certificate of Indigency (issued by Punong Barangay)\n";
        $message .= "8. Any proof of identity:\n";
        $message .= "   a) Barangay Certification\n";
        $message .= "   b) Medical/Immunization Record\n";
        $message .= "   c) Baptismal Certificate (Original from Church)\n";
        $message .= "   d) School Record (Certified Copy)\n";
        $message .= "   e) Voter's Record/Certification\n";
        $message .= "   f) Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)\n";
        $message .= "   g) Any Government Issued ID\n";
        $message .= "   h) Others (if applicable)\n";
        $message .= "9. Personal Appearance (18 years old and above)\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "Part 2 will follow...";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('BRAP service complete checklist Part 1 SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send complete BRAP checklist SMS Part 2 (items 10-18) for 79 and below
     */
    private function sendBrapServiceCompleteChecklistPart2Sms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "COMPLETE CHECKLIST Part 2/2 - BRAP (79 years old and below):\n\n";
        $message .= "10. Personal Appearance of Parents (Marital Minor)\n";
        $message .= "    In default: Grandparents, Oldest Sibling (21+), or Actual Custodian (21+)\n";
        $message .= "11. Personal Appearance of Mother (Non-marital minor)\n";
        $message .= "12. Affidavit/Sworn Statement (Mother's whereabouts if non-appearance)\n";
        $message .= "13. Affidavit of Admission of Paternity (if applicable, at back of COLB/PHI)\n";
        $message .= "14. AUSF under RA 9255 (if applicable, register within 20 days)\n";
        $message .= "15. Sworn Attestation (for child 7-17 years old)\n";
        $message .= "16. Affidavit of Acknowledgement (non-marital child before Aug 3, 1988)\n";
        $message .= "17. Special Power of Attorney/Authorization Letter (if applicable)\n";
        $message .= "18. Valid ID of Authorizing person and authorized person (if applicable)\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "City Civil Registry Office\n";

        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('BRAP service complete checklist Part 2 SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Send Octogenarian checklist SMS (13 items for 80 years old and above) for BRAP service
     */
    private function sendBrapServiceOctogenarianChecklistSms($smsService, $appointment, $phoneNumber, $fullName)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $message = "{$greeting}\n\n";
        $message .= "OCTOGENARIAN CHECKLIST (80 years old and above)\n";
        $message .= "BRAP - Delayed Birth Registration\n\n";
        $message .= "If you are 80 years old and above, please use this simplified checklist:\n";
        $message .= "1. Municipal Form No. 102 (4 Copies) - prepared at LCRO\n";
        $message .= "2. Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)\n";
        $message .= "3. Civil Registry Form 1C/Birth Destroyed (issued by LCRO)\n";
        $message .= "4. Negative Omnibus Certification/Negative Certification from PSA (processed at LCRO)\n";
        $message .= "5. Affidavit for Delayed Registration (at back of COLB)\n";
        $message .= "6. Affidavit of Two (2) Disinterested Persons\n";
        $message .= "7. Certificate of Indigency (issued by Punong Barangay)\n";
        $message .= "8. Any proof of identity:\n";
        $message .= "   a) Barangay Certification\n";
        $message .= "   b) Voter's Record/Certification\n";
        $message .= "   c) Member's Data Record (Senior Citizen, PhilHealth, SSS, GSIS)\n";
        $message .= "   d) Any Government Issued ID\n";
        $message .= "9. Special Power of Attorney/Authorization Letter (if applicable)\n";
        $message .= "10. Valid ID of Authorizing person and authorized person (if applicable)\n";
        $message .= "11. Others (if applicable)\n";
        $message .= "12. National ID\n";
        $message .= "13. 2x2 picture\n\n";
        $message .= "REF: {$appointment->appointment_number}\n";
        $message .= "City Civil Registry Office\n";


        $result = $smsService->sendSMSDirect($phoneNumber, $message);

        \Log::info('BRAP service Octogenarian checklist SMS sent', [
            'appointment_number' => $appointment->appointment_number,
            'success' => $result['success'] ?? false
        ]);
    }

    /**
     * Get appointment details
     */
    public function show($id)
    {
        $appointment = Appointment::with(['civilService', 'documentUploads'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'appointment' => $appointment
        ]);
    }

    /**
     * Get appointment by appointment number
     */
    public function getByAppointmentNumber($appointmentNumber)
    {
        try {
            // Use the byTracking scope which handles format normalization
            $appointment = Appointment::byTracking($appointmentNumber)
                ->with('civilService')
                ->first();

            if (!$appointment) {
                \Log::info('Appointment not found for number: ' . $appointmentNumber);
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // Get document count from civil service if available
            $documentsCount = 0;
            if ($appointment->civil_service_id) {
                $documentsCount = DocumentUpload::where('civil_service_id', $appointment->civil_service_id)->count();
            }

            // Get service name
            $serviceName = $appointment->service_type;
            if ($appointment->civilService && $appointment->civilService->service_name) {
                $serviceName = $appointment->civilService->service_name;
            }

            return response()->json([
                'success' => true,
                'appointment' => [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'service_name' => $serviceName,
                    'service_type' => $appointment->service_type,
                    'appointment_date' => $appointment->appointment_date ? (method_exists($appointment->appointment_date, 'format') ? $appointment->appointment_date->format('Y-m-d') : (string) $appointment->appointment_date) : null,
                    'appointment_time' => $appointment->appointment_time,
                    'full_name' => $appointment->full_name,
                    'contact_number' => $appointment->contact_number,
                    'email' => $appointment->email,
                    'address' => $appointment->address,
                    'status' => $appointment->status,
                    'documents_count' => $documentsCount,
                    'created_at' => $appointment->created_at ? $appointment->created_at->format('Y-m-d H:i:s') : null,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Get appointment by number error:', [
                'appointment_number' => $appointmentNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all appointments for officer dashboard
     */
    public function getAppointments(Request $request)
    {
        $query = Appointment::with(['civilService', 'documentUploads'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->paginate(15);

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'assigned_officer' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($id);

        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes
        ];

        if ($request->status === 'confirmed') {
            $updateData['confirmed_at'] = now();
        } elseif ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($request->status === 'cancelled') {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $request->notes;
        }

        if ($request->assigned_officer) {
            $updateData['assigned_officer'] = $request->assigned_officer;
        }

        $appointment->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully',
            'appointment' => $appointment->fresh()
        ]);
    }

    /**
     * Get appointment statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'today' => Appointment::whereDate('appointment_date', today())->count(),
            'this_week' => Appointment::whereBetween('appointment_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => Appointment::whereMonth('appointment_date', now()->month)->count()
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Determine requirement name based on filename
     */
    private function determineRequirementName($filename)
    {
        $filename = strtolower($filename);

        if (strpos($filename, 'id') !== false || strpos($filename, 'license') !== false || strpos($filename, 'passport') !== false) {
            return 'Valid Government ID';
        } elseif (strpos($filename, 'photo') !== false || strpos($filename, '2x2') !== false) {
            return '2x2 Photos';
        } elseif (strpos($filename, 'birth') !== false || strpos($filename, 'certificate') !== false || strpos($filename, 'colb') !== false) {
            return 'Certificate of Live Birth';
        } elseif (strpos($filename, 'affidavit') !== false || strpos($filename, 'witness') !== false) {
            return 'Affidavit of Two Witnesses';
        } elseif (strpos($filename, 'marriage') !== false) {
            return 'Marriage Certificate of Parents';
        } elseif (strpos($filename, 'form') !== false && (strpos($filename, '102') !== false || strpos($filename, '1b') !== false || strpos($filename, '1c') !== false)) {
            return 'Municipal Form No.102';
        } else {
            return 'Supporting Document';
        }
    }

    /**
     * Get service data from session
     */
    public function getServiceData()
    {
        $appointmentData = session('appointment_data', []);

        if (empty($appointmentData) || !isset($appointmentData['service_name'])) {
            return response()->json([
                'success' => false,
                'message' => 'No service data found'
            ]);
        }

        return response()->json([
            'success' => true,
            'service' => [
                'serviceName' => $appointmentData['service_name'],
                'department' => $appointmentData['department'] ?? 'Civil Registry',
                'serviceType' => $appointmentData['service_type'] ?? 'Civil Service',
                'description' => $appointmentData['description'] ?? ''
            ]
        ]);
    }

    /**
     * Approve an appointment
     * Creates CivilService record, sets status to inquiry, sends inquiry SMS, and marks appointment as approved
     */
    public function approve(Request $request, $id)
    {
        try {
            $appointment = Appointment::with('civilService')->findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'error' => 'Only pending appointments can be approved'
                ], 422);
            }

            // Determine request type and service name based on service type
            $serviceType = $appointment->service_type ?? '';
            $isBrap = (stripos($serviceType, 'BRAP') !== false && stripos($serviceType, 'Non-BRAP') === false);
            $requestType = $isBrap ? 'delayed_brap' : 'delayed_regular';

            // Map service_type to proper service_name for database
            // Service names must match the pattern used in the system for proper routing
            // These must match exactly what OfficerController uses to query requests
            if ($isBrap) {
                // Delayed BRAP - must contain "Delayed" and "BRAP" but NOT "Non-BRAP"
                $serviceName = 'Delayed BRAP Birth Registration Assistance Project';
            } else {
                // Delayed Regular - must contain both "Delayed" and "Non-BRAP"
                // Must match: 'Delayed Regular Non-BRAP Birth Registration Assistance Project'
                $serviceName = 'Delayed Regular Non-BRAP Birth Registration Assistance Project';
            }

            // Use appointment number as tracking number (Solution 2: Single reference number)
            // This ensures the client keeps the same reference number throughout the process
            $trackingNumber = $appointment->appointment_number;

            // Verify the appointment number doesn't already exist as a tracking number
            // (shouldn't happen, but safety check)
            if (CivilService::where('tracking_number', $trackingNumber)->exists()) {
                \Log::warning('Appointment number already exists as tracking number', [
                    'appointment_number' => $trackingNumber,
                    'appointment_id' => $appointment->id
                ]);
                // Fallback: generate new tracking number if conflict exists
                $trackingNumber = CivilService::generateTrackingNumber();
            }

            // Generate queue number only if feature is enabled for Civil Registry Office
            $queueNumber = null;
            if (\App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false)) {
                $queueNumber = CivilService::getNextQueueNumber();
            }

            // Create new CivilService record (don't reuse existing if any)
            $civilService = CivilService::create([
                'service_name' => $serviceName,
                'department' => 'Civil Registry Office',
                'service_type' => $appointment->service_type ?? 'Civil Service',
                'status' => 'inquiry',
                'request_type' => $requestType,
                'tracking_number' => $trackingNumber, // Use appointment number as tracking number
                'queue_number' => $queueNumber ?? '1',
                'full_name' => $appointment->full_name,
                'contact_number' => $appointment->contact_number,
                'email' => $appointment->email ?? null,
                'address' => $appointment->address ?? null
            ]);

            // Create initial status update for 'inquiry' status
            \App\Models\StatusUpdate::create([
                'statusable_id' => $civilService->id,
                'statusable_type' => CivilService::class,
                'status' => 'inquiry',
                'status_date' => now()->toDateString(),
                'remarks' => 'Appointment approved - Request moved to inquiry status',
            ]);

            // Update appointment with civil_service_id and mark as approved
            $appointment->civil_service_id = $civilService->id;
            $appointment->status = 'approved';
            $appointment->confirmed_at = now();
            $appointment->save();

            // Send inquiry SMS automatically
            try {
                $notificationService = new \App\Services\NotificationService();
                $smsResult = $notificationService->notifyClientViaSMS($civilService, 'inquiry');
                \Log::info('Inquiry SMS sent after appointment approval', [
                    'tracking_number' => $trackingNumber,
                    'appointment_number' => $appointment->appointment_number,
                    'sms_result' => $smsResult
                ]);
            } catch (\Exception $e) {
                // Log SMS failure but don't fail the approval
                \Log::warning('Failed to send inquiry SMS after appointment approval', [
                    'tracking_number' => $trackingNumber,
                    'appointment_number' => $appointment->appointment_number,
                    'error' => $e->getMessage()
                ]);
            }

            \Log::info('Appointment approved and moved to CivilService', [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'civil_service_id' => $civilService->id,
                'tracking_number' => $trackingNumber, // Same as appointment_number (Solution 2)
                'request_type' => $requestType,
                'service_name' => $serviceName,
                'note' => 'Appointment number used as tracking number for single reference number system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment approved successfully. Request has been moved to ' . ($isBrap ? 'BRAP' : 'Regular') . ' table with inquiry status.',
                'appointment' => $appointment->fresh(),
                'civil_service' => $civilService
            ]);

        } catch (\Exception $e) {
            \Log::error('Error approving appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to approve appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decline an appointment
     * Updates status to cancelled and sends SMS notification
     * Decline means client was late or did not attend the appointment
     */
    public function decline(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'error' => 'Only pending appointments can be declined'
                ], 422);
            }

            $reason = $request->input('reason');

            // Update appointment status
            $appointment->status = 'declined';
            $appointment->cancelled_at = now();
            $appointment->cancellation_reason = $reason;
            $appointment->notes = $reason ? 'Declined: ' . $reason : 'Declined - Client was late or did not attend';
            $appointment->save();

            // Send SMS notification
            $this->sendAppointmentDeclineSms($appointment, $reason);

            \Log::info('Appointment declined', [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'reason' => $reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment declined successfully',
                'appointment' => $appointment->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error declining appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to decline appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send SMS notification when appointment is approved
     */
    private function sendAppointmentApprovalSms($appointment)
    {
        try {
            $smsService = new NodeSmsService();
            $phoneNumber = $appointment->contact_number;
            $fullName = $appointment->full_name;

            if (!$phoneNumber) {
                \Log::warning('No phone number for appointment approval SMS', [
                    'appointment_id' => $appointment->id
                ]);
                return ['success' => false, 'error' => 'No phone number provided'];
            }

            // Format date and time
            $formattedDate = $appointment->appointment_date ? $appointment->appointment_date->format('F d, Y') : 'N/A';
            $timeStr = $appointment->appointment_time ?? 'N/A';
            $formattedTime = $timeStr;
            if ($timeStr && strpos($timeStr, ':') !== false) {
                $timeParts = explode(':', $timeStr);
                $hours = (int) $timeParts[0];
                $minutes = $timeParts[1] ?? '00';
                $ampm = $hours >= 12 ? 'PM' : 'AM';
                $displayHour = $hours % 12 ?: 12;
                $formattedTime = "{$displayHour}:{$minutes} {$ampm}";
            }

            $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
            $message = "{$greeting}\n\n";
            $message .= "Your appointment has been APPROVED!\n\n";
            $message .= "REFERENCE NO: {$appointment->appointment_number}\n\n";
            $message .= "Service: {$appointment->service_type}\n";
            $message .= "Date: {$formattedDate}\n";
            $message .= "Time: {$formattedTime}\n\n";
            $message .= "IMPORTANT: Please arrive on time. If you do not arrive on time, your appointment will be automatically cancelled.\n\n";
            $message .= "Track your Appointment {$appointment->appointment_number} at Click2Serve Tracking feature.\n\n";
            $message .= "Thank you for using Click2Serve!";

            \Log::info('Sending appointment approval SMS', [
                'appointment_number' => $appointment->appointment_number,
                'phone_number' => $phoneNumber,
                'message_length' => strlen($message)
            ]);

            $result = $smsService->sendSMSDirect($phoneNumber, $message);

            \Log::info('Appointment approval SMS result', [
                'appointment_number' => $appointment->appointment_number,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Failed to send appointment approval SMS', [
                'appointment_number' => $appointment->appointment_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send SMS notification when appointment is declined
     */
    private function sendAppointmentDeclineSms($appointment, $reason = null)
    {
        try {
            $smsService = new NodeSmsService();
            $phoneNumber = $appointment->contact_number;
            $fullName = $appointment->full_name;

            if (!$phoneNumber) {
                \Log::warning('No phone number for appointment decline SMS', [
                    'appointment_id' => $appointment->id
                ]);
                return ['success' => false, 'error' => 'No phone number provided'];
            }

            $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
            $message = "{$greeting}\n\n";
            $message .= "Your appointment {$appointment->appointment_number} has been cancelled.\n\n";
            $message .= "You may visit the office and join the regular queue during business hours.\n\n";

            // Add optional remarks if provided by officer
            if ($reason && trim($reason) !== '') {
                $message .= "Remarks: {$reason}\n\n";
            }

            $message .= "Thank you for using Click2Serve!";

            \Log::info('Sending appointment decline SMS', [
                'appointment_number' => $appointment->appointment_number,
                'phone_number' => $phoneNumber,
                'message_length' => strlen($message)
            ]);

            $result = $smsService->sendSMSDirect($phoneNumber, $message);

            \Log::info('Appointment decline SMS result', [
                'appointment_number' => $appointment->appointment_number,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Failed to send appointment decline SMS', [
                'appointment_number' => $appointment->appointment_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}