<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;
use App\Services\NodeSmsService;

class NotificationService
{
    /**
     * Send notification to officer admin when request status changes
     */
    public function notifyStatusChange($request, $oldStatus, $newStatus, $officerId = null)
    {
        try {
            // Determine the request type and get the appropriate model
            $modelType = get_class($request);
            $isCivilService = $modelType === CivilService::class;

            // Get the officer ID if not provided
            if (!$officerId) {
                $officerId = $this->getOfficerIdForDepartment($request->department);
            }

            if (!$officerId) {
                Log::warning('No officer found for department: ' . $request->department);
                // For testing purposes, use the first available officer
                $firstOfficer = \App\Models\Admin::where('role', 'officer')->first();
                if ($firstOfficer) {
                    $officerId = $firstOfficer->id;
                    Log::info('Using first available officer for testing: ' . $officerId);
                } else {
                    return false;
                }
            }

            // Create notification message
            $message = $this->createStatusChangeMessage($request, $oldStatus, $newStatus, $isCivilService);

            // Create notification
            $notificationData = [
                'user_id' => $officerId,
                'user_type' => 'officer',
                'title' => 'Request Status Updated',
                'message' => $message,
                'type' => 'status_change',
                'data' => [
                    'request_id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'model_type' => $modelType,
                    'request_type' => $isCivilService ? 'civil' : 'other'
                ]
            ];

            Log::info('Creating notification with data:', $notificationData);

            $notification = Notification::create($notificationData);

            Log::info('Status change notification created', [
                'notification_id' => $notification->id,
                'officer_id' => $officerId,
                'tracking_number' => $request->tracking_number,
                'status_change' => $oldStatus . ' → ' . $newStatus
            ]);

            // Get remarks from request if available (passed from controller)
            $remarks = $request->remarks ?? null;

            // Check if SMS should be sent (for incomplete_requirements, check if SMS was requested)
            $shouldSendSMS = true;
            if ($newStatus === 'incomplete_requirements') {
                // Check if remarks contain missing requirements (indicates SMS was requested)
                // If remarks don't contain missing requirements but admin_notes does, SMS was not requested
                if ($remarks && stripos($remarks, 'Missing requirements:') === false) {
                    // Check admin_notes to see if missing requirements exist but weren't included in remarks
                    $adminNotes = $request->admin_notes ?? '';
                    if ($adminNotes && stripos($adminNotes, 'Missing requirements:') !== false) {
                        $shouldSendSMS = false; // Missing requirements in admin_notes but not in remarks = SMS not requested
                    }
                }
            }

            // Send SMS notification to client and capture result (only if shouldSendSMS is true)
            $smsResult = null;
            if ($shouldSendSMS) {
                $smsResult = $this->notifyClientViaSMS($request, $newStatus, $remarks);
            } else {
                Log::info('SMS skipped for incomplete_requirements - SMS not requested', [
                    'tracking_number' => $request->tracking_number,
                    'status' => $newStatus
                ]);
            }

            // Update notification data with SMS status
            $notificationData = $notification->data ?? [];
            if ($smsResult) {
                $notificationData['sms_sent'] = true;
                $notificationData['sms_message_id'] = $smsResult['message_id'] ?? null;
                $notificationData['sms_network'] = $smsResult['network'] ?? null;
            } else {
                $notificationData['sms_sent'] = false;
                $notificationData['sms_error'] = 'SMS sending failed';
            }

            // Update notification with SMS data
            $notification->update(['data' => $notificationData]);

            return $notification;

        } catch (\Exception $e) {
            Log::error('Failed to create status change notification', [
                'error' => $e->getMessage(),
                'request_id' => $request->id ?? null,
                'tracking_number' => $request->tracking_number ?? null
            ]);

            return false;
        }
    }

    /**
     * Send SMS notification to client via Python SMS service
     */
    public function notifyClientViaSMS($request, $newStatus, $remarks = null)
    {
        try {
            // Get contact number - check multiple possible field names
            $contactNumber = $request->contact_number ?? $request->phone ?? $request->phone_number ?? null;

            // Check if client has a valid phone number
            if (!$contactNumber || empty(trim($contactNumber))) {
                Log::warning('No contact number available for SMS notification', [
                    'tracking_number' => $request->tracking_number,
                    'request_type' => get_class($request),
                    'available_fields' => array_keys($request->getAttributes() ?? [])
                ]);
                return [
                    'success' => false,
                    'error' => 'No contact number available',
                    'message' => 'Client has no valid phone number'
                ];
            }

            Log::info('Preparing to send SMS notification', [
                'tracking_number' => $request->tracking_number,
                'contact_number' => $contactNumber,
                'status' => $newStatus,
                'full_name' => $request->full_name ?? 'N/A',
                'remarks' => $remarks ?? 'N/A'
            ]);

            // Use Node.js SMS service for status updates
            $nodeSmsService = new NodeSmsService();

            // If remarks not provided, try to get from latest status update or admin notes
            if (!$remarks) {
                if (method_exists($request, 'statusUpdates')) {
                    // Refresh the relationship to get the latest status update
                    $request->load('statusUpdates');
                    $latestStatusUpdate = $request->statusUpdates()->latest()->first();
                    $remarks = $latestStatusUpdate?->remarks;
                }
                // Fallback to admin_notes if no status update remarks
                if (!$remarks) {
                    $remarks = $request->admin_notes ?? null;
                }
            }

            // Generate the actual SMS message that will be sent
            $smsMessage = $nodeSmsService->generateStatusMessage(
                $request->tracking_number,
                $newStatus,
                $request->full_name ?? null,
                $remarks
            );

            // Send status update SMS (using direct HTTP client to avoid DNS issues)
            $result = $nodeSmsService->sendStatusUpdate(
                $request->tracking_number,
                $newStatus,
                $request->full_name ?? null,
                $contactNumber,
                $remarks
            );

            // Determine request type
            $modelType = get_class($request);
            $requestType = $modelType === CivilService::class ? 'civil' : 'other';

            // Log the SMS to the database
            $smsLogData = [
                'tracking_number' => $request->tracking_number,
                'request_type' => $requestType,
                'request_id' => $request->id,
                'phone_number' => $contactNumber,
                'message' => $smsMessage,
                'status' => $result['success'] ? 'sent' : 'failed',
                'status_type' => $newStatus,
                'message_id' => $result['message_id'] ?? null,
                'network' => $result['network'] ?? null,
                'sender' => $result['sender'] ?? null,
                'error' => $result['success'] ? null : ($result['error'] ?? $result['message'] ?? 'Unknown error'),
                'sent_at' => $result['success'] ? now() : null,
                'response_data' => $result['data'] ?? null,
            ];

            try {
                SmsLog::create($smsLogData);
                Log::info('SMS log created', ['tracking_number' => $request->tracking_number, 'status' => $newStatus]);
            } catch (\Exception $logException) {
                Log::error('Failed to create SMS log', [
                    'error' => $logException->getMessage(),
                    'tracking_number' => $request->tracking_number
                ]);
            }

            if ($result['success']) {
                Log::info('SMS notification sent successfully via Node.js service', [
                    'phone_number' => $contactNumber,
                    'tracking_number' => $request->tracking_number,
                    'status' => $newStatus,
                    'message_id' => $result['message_id'] ?? 'N/A',
                    'network' => $result['network'] ?? 'N/A',
                    'sender' => $result['sender'] ?? 'N/A'
                ]);
                return $result; // Return full result instead of just true
            } else {
                Log::warning('SMS notification failed via Node.js service', [
                    'phone_number' => $contactNumber,
                    'tracking_number' => $request->tracking_number,
                    'status' => $newStatus,
                    'error' => $result['error'] ?? 'Unknown error',
                    'message' => $result['message'] ?? 'Unknown error',
                    'exit_code' => $result['exit_code'] ?? null,
                    'result_data' => $result
                ]);
                return $result; // Return full result instead of just false
            }

        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification via Node.js service', [
                'error' => $e->getMessage(),
                'tracking_number' => $request->tracking_number ?? null
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'SMS service exception'
            ];
        }
    }

    /**
     * Get officer ID for a specific department
     */
    private function getOfficerIdForDepartment($department)
    {
        $officer = \App\Models\Admin::where('department', $department)
            ->where('role', 'officer')
            ->first();

        return $officer ? $officer->id : null;
    }

    /**
     * Create status change message for officer notification
     */
    private function createStatusChangeMessage($request, $oldStatus, $newStatus, $isCivilService)
    {
        if ($isCivilService) {
            $requestType = 'Civil Service';
        } else {
            // For Other Services, use the actual service type name instead of "Other Service"
            $requestType = $request->service_type ?? 'Other Service';
        }

        $statusLabels = $this->getStatusLabels();

        $oldStatusLabel = $statusLabels[$oldStatus] ?? ucwords(str_replace('_', ' ', $oldStatus));
        $newStatusLabel = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        return "Request #{$request->tracking_number} ({$requestType}) status changed from '{$oldStatusLabel}' to '{$newStatusLabel}'. Client: {$request->full_name}";
    }

    /**
     * Create SMS message for client
     */
    private function createSMSMessage($request, $newStatus)
    {
        $statusLabels = $this->getStatusLabels();
        $statusLabel = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        return "Your request #{$request->tracking_number} status has been updated to: {$statusLabel}. Thank you for using our services!";
    }

    /**
     * Format phone number for Semaphore API
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Add country code if not present (assuming Philippines +63)
        if (strlen($cleaned) === 10 && substr($cleaned, 0, 1) === '9') {
            $cleaned = '63' . $cleaned;
        } elseif (strlen($cleaned) === 11 && substr($cleaned, 0, 2) === '09') {
            $cleaned = '63' . substr($cleaned, 1);
        }

        return '+' . $cleaned;
    }

    /**
     * Get status labels for display
     */
    private function getStatusLabels()
    {
        return [
            'submitted' => 'Submitted',
            'inquiry' => 'Inquiry',
            'to_be_encoded_brap' => 'To Be Encoded to BRAP Request List',
            'checklist_given' => 'Checklist of Requirements Given',
            'incomplete_requirements' => 'Incomplete Requirements',
            'accepted' => 'Accepted',
            'psa_processing' => 'PSA Processing',
            'posting' => 'Posting',
            'ready_for_approval' => 'Ready for Approval',
            'registered' => 'Registered',
            'encoded_brap' => 'Encoded to BRAP',
            'negative_result' => 'Negative Result',
            'positive_result' => 'Positive Result',
            'rejected' => 'Rejected'
        ];
    }

    /**
     * Send SMS via Semaphore API using SemaphoreClient
     */
    private function sendSMSViaSemaphore($phoneNumber, $message)
    {
        try {
            $apiKey = config('services.semaphore.api_key');
            $senderName = config('services.semaphore.sender_name', 'SEMAPHORE');
            $enabled = config('services.semaphore.enabled', false);

            if (!$enabled) {
                Log::info('SMS sending is disabled in configuration');
                return false;
            }

            if (!$apiKey) {
                Log::error('Semaphore API key not configured');
                return false;
            }

            // Use our custom SemaphoreClient
            $client = new SemaphoreClient($apiKey, $senderName);

            // Send SMS
            $result = $client->send($phoneNumber, $message);

            // Check if there was an error
            if (isset($result['error'])) {
                Log::error('SMS sending failed via SemaphoreClient', [
                    'phone_number' => $phoneNumber,
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Unknown error'
                ]);
                return false;
            }

            // Check if we got a valid response array
            if (is_array($result) && !empty($result)) {
                $messageData = $result[0] ?? $result;

                Log::info('SMS sent successfully via SemaphoreClient', [
                    'phone_number' => $phoneNumber,
                    'message_length' => strlen($message),
                    'message_id' => $messageData['message_id'] ?? 'N/A',
                    'status' => $messageData['status'] ?? 'N/A',
                    'network' => $messageData['network'] ?? 'N/A'
                ]);

                return true;
            }

            Log::warning('Unexpected response from SemaphoreClient', [
                'phone_number' => $phoneNumber,
                'response' => $result
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('SMS sending exception via SemaphoreClient', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }


    /**
     * Notify officer that request is ready for approval
     */
    public function notifyApprovalReady($request, $officerId)
    {
        try {
            $message = "Request #{$request->tracking_number} is ready for approval after 10-day posting period. Client: {$request->full_name}";

            $notificationData = [
                'user_id' => $officerId,
                'user_type' => 'officer',
                'title' => 'Request Ready for Approval',
                'message' => $message,
                'type' => 'approval_ready',
                'data' => [
                    'request_id' => $request->id,
                    'tracking_number' => $request->tracking_number,
                    'posting_started_at' => $request->posting_started_at,
                    'model_type' => get_class($request)
                ]
            ];

            Log::info('Creating approval ready notification with data:', $notificationData);

            $notification = Notification::create($notificationData);

            Log::info('Approval ready notification created', [
                'notification_id' => $notification->id,
                'officer_id' => $officerId,
                'tracking_number' => $request->tracking_number
            ]);

            return $notification;

        } catch (\Exception $e) {
            Log::error('Failed to create approval ready notification', [
                'error' => $e->getMessage(),
                'request_id' => $request->id ?? null,
                'tracking_number' => $request->tracking_number ?? null
            ]);

            return false;
        }
    }
}
