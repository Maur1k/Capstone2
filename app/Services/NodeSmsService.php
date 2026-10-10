<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Http;

class NodeSmsService
{
    private $nodeScriptPath;
    private $nodeExecutable;

    public function __construct()
    {
        $this->nodeScriptPath = base_path('sms-service/send_sms.cjs');
        $this->nodeExecutable = config('services.node_sms.node_path', 'C:\\nvm4w\\nodejs\\node.exe');
    }

    /**
     * Send SMS directly via Semaphore API using Laravel HTTP client (bypasses Node.js DNS issues)
     */
    public function sendSMSDirect($phoneNumber, $message, $senderName = null)
    {
        try {
            // Get environment variables from Laravel config
            $apiKey = config('services.semaphore.api_key');
            $senderName = $senderName ?? config('services.semaphore.sender_name', 'Click2Serve');
            $apiUrl = config('services.semaphore.api_url', 'https://api.semaphore.co/api/v4/messages');

            // Validate API key is set
            if (empty($apiKey)) {
                Log::error('Semaphore API key is not configured');
                return [
                    'success' => false,
                    'error' => 'API key not configured',
                    'message' => 'SEMAPHORE_API_KEY is not set in .env file'
                ];
            }

            // Normalize phone number
            $normalizedNumber = $this->normalizePhoneNumber($phoneNumber);

            Log::info('Sending SMS via direct HTTP client', [
                'phone_number' => $normalizedNumber,
                'message_length' => strlen($message),
                'sender_name' => $senderName
            ]);

            // Check if SSL verification should be disabled (for local/testing environments)
            $verifySsl = config('services.semaphore.verify_ssl', null);
            $shouldVerifySsl = $verifySsl !== false; // Default to true unless explicitly set to false
            
            // Build HTTP client with conditional SSL verification
            $httpClient = Http::timeout(30)->asForm();
            
            // Disable SSL verification if configured or if we're in a local environment
            if (!$shouldVerifySsl || app()->environment(['local', 'testing'])) {
                $httpClient = $httpClient->withoutVerifying();
                Log::info('SSL verification disabled for SMS sending', [
                    'environment' => app()->environment(),
                    'verify_ssl_config' => $verifySsl
                ]);
            }
            
            // Send SMS via Semaphore API using Laravel HTTP client
            try {
                $response = $httpClient->post($apiUrl . '?apikey=' . urlencode($apiKey), [
                    'message' => $message,
                    'number' => $normalizedNumber,
                    'sendername' => $senderName
                ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // If SSL error occurs, retry without verification
                if (stripos($e->getMessage(), 'SSL certificate') !== false || 
                    stripos($e->getMessage(), 'cURL error 60') !== false) {
                    Log::warning('SSL certificate error detected, retrying without verification', [
                        'error' => $e->getMessage()
                    ]);
                    
                    $response = Http::timeout(30)
                        ->asForm()
                        ->withoutVerifying()
                        ->post($apiUrl . '?apikey=' . urlencode($apiKey), [
                            'message' => $message,
                            'number' => $normalizedNumber,
                            'sendername' => $senderName
                        ]);
                } else {
                    throw $e;
                }
            }

            if ($response->successful()) {
                $responseData = $response->json();

                // Handle array or object response
                $messageData = is_array($responseData) && isset($responseData[0]) ? $responseData[0] : $responseData;

                $messageId = $messageData['message_id'] ?? $messageData['id'] ?? null;
                $status = $messageData['status'] ?? 'queued';
                $network = $messageData['network'] ?? $messageData['carrier'] ?? null;

                Log::info('SMS sent successfully via direct HTTP client', [
                    'phone_number' => $normalizedNumber,
                    'message_id' => $messageId,
                    'status' => $status,
                    'network' => $network
                ]);

                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'status' => $status,
                    'network' => $network,
                    'sender' => $senderName,
                    'data' => $responseData
                ];
            } else {
                $errorMessage = $response->json()['message'] ?? $response->body() ?? 'HTTP ' . $response->status();

                Log::error('SMS sending failed via direct HTTP client', [
                    'phone_number' => $normalizedNumber,
                    'status_code' => $response->status(),
                    'error' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'status_code' => $response->status(),
                    'data' => $response->json() ?? []
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception sending SMS via direct HTTP client', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'SMS service exception'
            ];
        }
    }

    /**
     * Normalize phone number to international format
     */
    private function normalizePhoneNumber($number)
    {
        // Remove spaces, dashes, plus signs
        $normalized = preg_replace('/[\s\-\+]/', '', $number);

        // If number doesn't start with country code, assume it's a local number and add 63
        if (strpos($normalized, '63') !== 0 && strlen($normalized) === 10) {
            $normalized = '63' . $normalized;
        } elseif (strpos($normalized, '0') === 0 && strlen($normalized) === 11) {
            $normalized = '63' . substr($normalized, 1);
        }

        return $normalized;
    }

    /**
     * Send status update SMS
     */
    public function sendStatusUpdate($trackingNumber, $status, $fullName = null, $phoneNumber = null, $remarks = null)
    {
        $message = $this->createStatusUpdateMessage($trackingNumber, $status, $fullName, $remarks);

        if (!$phoneNumber) {
            Log::error('No phone number provided for status update SMS', [
                'tracking_number' => $trackingNumber
            ]);
            return [
                'success' => false,
                'error' => 'No phone number provided'
            ];
        }

        return $this->sendSMSDirect($phoneNumber, $message);
    }

    /**
     * Generate status update message without sending (for logging purposes)
     */
    public function generateStatusMessage($trackingNumber, $status, $fullName = null, $remarks = null)
    {
        return $this->createStatusUpdateMessage($trackingNumber, $status, $fullName, $remarks);
    }

    /**
     * Send SMS via Node.js script
     */
    public function sendSMS($phoneNumber, $message, $senderName = null)
    {
        try {
            // Get environment variables from Laravel config
            $apiKey = config('services.semaphore.api_key');
            $senderName = $senderName ?? config('services.semaphore.sender_name', 'Click2Serve');
            $apiUrl = config('services.semaphore.api_url', 'https://api.semaphore.co/api/v4/messages');
            $testNumber = config('services.semaphore.test_number', '639219580125');
            $useTestNumber = config('services.semaphore.use_test_number', false);

            // Validate API key is set
            if (empty($apiKey)) {
                Log::error('Semaphore API key is not configured', [
                    'phone_number' => $phoneNumber,
                    'config_services_semaphore' => config('services.semaphore')
                ]);
                return [
                    'success' => false,
                    'error' => 'API key not configured',
                    'message' => 'SEMAPHORE_API_KEY is not set in .env file'
                ];
            }

            // Escape the message and phone number for command line
            $escapedMessage = escapeshellarg($message);
            $escapedNumber = escapeshellarg($phoneNumber);

            // Build the command with API key and sender name as arguments (fallback if env vars don't work)
            // This ensures the API key is passed even if Process::env() doesn't work on Windows
            $escapedApiKey = escapeshellarg($apiKey);
            $escapedSenderName = escapeshellarg($senderName);
            $command = "{$this->nodeExecutable} {$this->nodeScriptPath} {$escapedMessage} {$escapedNumber} {$escapedApiKey} {$escapedSenderName}";

            // Verify script exists
            if (!file_exists($this->nodeScriptPath)) {
                Log::error('Node.js SMS script not found', [
                    'script_path' => $this->nodeScriptPath,
                    'base_path' => base_path()
                ]);
                return [
                    'success' => false,
                    'error' => 'SMS script not found',
                    'message' => "Script not found at: {$this->nodeScriptPath}"
                ];
            }

            Log::info('Executing Node.js SMS command', [
                'command' => $command,
                'script_path' => $this->nodeScriptPath,
                'script_exists' => file_exists($this->nodeScriptPath),
                'node_executable' => $this->nodeExecutable,
                'phone_number' => $phoneNumber,
                'message_length' => strlen($message),
                'api_key_set' => !empty($apiKey),
                'api_key_preview' => $apiKey ? substr($apiKey, 0, 8) . '...' : 'NOT SET',
                'sender_name' => $senderName
            ]);

            // Execute the Node.js script with environment variables
            // Use Process::env() method for cross-platform support
            try {
                $result = Process::env([
                    'SEMAPHORE_API_KEY' => $apiKey,
                    'SEMAPHORE_SENDER_NAME' => $senderName,
                    'SEMAPHORE_API_URL' => $apiUrl,
                    'SEMAPHORE_TEST_NUMBER' => $testNumber,
                    'SEMAPHORE_USE_TEST_NUMBER' => $useTestNumber ? 'true' : 'false',
                ])->run($command);
            } catch (\Exception $e) {
                Log::error('Failed to execute Node.js SMS command', [
                    'error' => $e->getMessage(),
                    'command' => $command,
                    'trace' => $e->getTraceAsString()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to execute command',
                    'message' => $e->getMessage()
                ];
            }

            $exitCode = $result->exitCode();
            $output = $result->output();
            $errorOutput = $result->errorOutput();

            Log::info('Node.js SMS command executed', [
                'exit_code' => $exitCode,
                'output' => $output,
                'error_output' => $errorOutput
            ]);

            // Parse the output to extract message details first
            $messageData = $this->parseNodeOutput($output);

            // Check if we got a valid message_id from Semaphore API (this is the real indicator of success)
            $hasMessageId = !empty($messageData['message_id']);

            // Check if output contains "SUCCESS" message (indicates script executed successfully)
            $isSuccess = stripos($output, 'SUCCESS:') !== false || stripos($output, 'Message sent successfully') !== false;

            // Check if output contains "ERROR" message
            $hasError = stripos($output, 'ERROR:') !== false || stripos($errorOutput, 'ERROR:') !== false;

            // Only consider it successful if:
            // 1. Exit code is 0 AND
            // 2. We have a message_id from Semaphore API (proves SMS was actually sent) AND
            // 3. No error messages in output
            if ($exitCode === 0 && $hasMessageId && !$hasError) {
                Log::info('SMS sent successfully via Node.js script', [
                    'phone_number' => $phoneNumber,
                    'message_length' => strlen($message),
                    'message_id' => $messageData['message_id'],
                    'status' => $messageData['status'] ?? 'N/A',
                    'network' => $messageData['network'] ?? 'N/A',
                    'sender' => $messageData['sender'] ?? 'N/A'
                ]);

                return [
                    'success' => true,
                    'message_id' => $messageData['message_id'],
                    'status' => $messageData['status'] ?? null,
                    'network' => $messageData['network'] ?? null,
                    'sender' => $messageData['sender'] ?? null,
                    'data' => $messageData
                ];
            } else {
                // Failed - log the reason
                $failureReason = [];
                if ($exitCode !== 0)
                    $failureReason[] = "exit_code: {$exitCode}";
                if (!$hasMessageId)
                    $failureReason[] = "no_message_id";
                if ($hasError)
                    $failureReason[] = "error_in_output";

                Log::error('Node.js SMS command failed or did not send SMS', [
                    'phone_number' => $phoneNumber,
                    'exit_code' => $exitCode,
                    'has_message_id' => $hasMessageId,
                    'has_error' => $hasError,
                    'failure_reasons' => $failureReason,
                    'output' => $output,
                    'error_output' => $errorOutput,
                    'parsed_message_data' => $messageData
                ]);

                // Try to extract error message from output
                $errorMessage = 'SMS sending failed';
                if ($hasError) {
                    if (preg_match('/ERROR:\s*(.+)/i', $output . "\n" . $errorOutput, $matches)) {
                        $errorMessage = trim($matches[1]);
                    }
                } elseif (!$hasMessageId) {
                    $errorMessage = 'No message ID received from Semaphore API. SMS may not have been sent.';
                } elseif ($exitCode !== 0) {
                    $errorMessage = "Script exited with code {$exitCode}";
                }

                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'message' => $errorMessage,
                    'exit_code' => $exitCode,
                    'has_message_id' => $hasMessageId,
                    'data' => [
                        'output' => $output,
                        'error_output' => $errorOutput,
                        'parsed_data' => $messageData
                    ]
                ];
            }

        } catch (\Exception $e) {
            Log::error('Node.js SMS service exception', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Exception',
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * Test SMS service
     */
    public function testSMS($testNumber = null)
    {
        $testNumber = $testNumber ?: '639219580125';
        $testMessage = 'Node.js SMS Service Test - ' . date('Y-m-d H:i:s') . ' - Please confirm receipt';

        return $this->sendSMS($testNumber, $testMessage);
    }

    /**
     * Check if Node.js is available
     */
    public function checkNodeAvailability()
    {
        try {
            $result = Process::run("{$this->nodeExecutable} --version");

            if ($result->successful()) {
                return [
                    'success' => true,
                    'version' => trim($result->output()),
                    'node_path' => $this->nodeExecutable
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Node.js not found',
                    'message' => $result->errorOutput()
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Exception',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if Node.js script exists and is readable
     */
    public function checkScriptAvailability()
    {
        if (!file_exists($this->nodeScriptPath)) {
            return [
                'success' => false,
                'error' => 'Script not found',
                'message' => "Node.js script not found at: {$this->nodeScriptPath}"
            ];
        }

        if (!is_readable($this->nodeScriptPath)) {
            return [
                'success' => false,
                'error' => 'Script not readable',
                'message' => "Node.js script is not readable: {$this->nodeScriptPath}"
            ];
        }

        return [
            'success' => true,
            'script_path' => $this->nodeScriptPath,
            'message' => 'Node.js script is available and readable'
        ];
    }

    /**
     * Parse Node.js script output to extract message details
     */
    private function parseNodeOutput($output)
    {
        $data = [
            'message_id' => null,
            'status' => null,
            'network' => null,
            'sender' => null
        ];

        // First, try to extract JSON from the "Raw Response:" line (most reliable)
        // Match JSON that may span multiple lines - use non-greedy with proper boundaries
        if (preg_match('/Raw Response:\s*(\[[\s\S]*?\]|\{[\s\S]*?\})/s', $output, $jsonMatches)) {
            try {
                $jsonString = trim($jsonMatches[1]);
                $jsonData = json_decode($jsonString, true);

                if (json_last_error() === JSON_ERROR_NONE && $jsonData !== null) {
                    if (is_array($jsonData) && !empty($jsonData)) {
                        // Handle array response (Semaphore returns array)
                        $messageData = is_array($jsonData[0]) ? $jsonData[0] : $jsonData;
                        $data['message_id'] = $messageData['message_id'] ?? $messageData['id'] ?? null;
                        $data['status'] = $messageData['status'] ?? null;
                        $data['network'] = $messageData['network'] ?? $messageData['carrier'] ?? null;
                        $data['sender'] = $messageData['sender_name'] ?? $messageData['sendername'] ?? null;

                        // Log successful parsing
                        Log::debug('Successfully parsed JSON from Raw Response', [
                            'message_id' => $data['message_id'],
                            'status' => $data['status']
                        ]);

                        return $data;
                    } elseif (is_array($jsonData)) {
                        // Handle object response
                        $data['message_id'] = $jsonData['message_id'] ?? $jsonData['id'] ?? null;
                        $data['status'] = $jsonData['status'] ?? null;
                        $data['network'] = $jsonData['network'] ?? $jsonData['carrier'] ?? null;
                        $data['sender'] = $jsonData['sender_name'] ?? $jsonData['sendername'] ?? null;
                        return $data;
                    }
                } else {
                    Log::warning('JSON decode error in Raw Response', [
                        'error' => json_last_error_msg(),
                        'json_string' => substr($jsonString, 0, 200)
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to parse JSON from Raw Response', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Second, try to extract JSON from the "Response Data:" line
        if (preg_match('/Response Data:\s*(\[[\s\S]*?\]|\{[\s\S]*?\})/s', $output, $jsonMatches)) {
            try {
                $jsonData = json_decode($jsonMatches[1], true);
                if (is_array($jsonData) && !empty($jsonData)) {
                    // Handle array response
                    $messageData = is_array($jsonData[0]) ? $jsonData[0] : $jsonData;
                    $data['message_id'] = $messageData['message_id'] ?? $messageData['id'] ?? null;
                    $data['status'] = $messageData['status'] ?? null;
                    $data['network'] = $messageData['network'] ?? $messageData['carrier'] ?? null;
                    $data['sender'] = $messageData['sender_name'] ?? $messageData['sendername'] ?? null;
                    return $data;
                } elseif (is_array($jsonData)) {
                    // Handle object response
                    $data['message_id'] = $jsonData['message_id'] ?? $jsonData['id'] ?? null;
                    $data['status'] = $jsonData['status'] ?? null;
                    $data['network'] = $jsonData['network'] ?? $jsonData['carrier'] ?? null;
                    $data['sender'] = $jsonData['sender_name'] ?? $jsonData['sendername'] ?? null;
                    return $data;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to parse JSON from Response Data', ['error' => $e->getMessage()]);
            }
        }

        // Third, try to find any JSON array or object in the output (fallback)
        // Look for JSON patterns that might be anywhere in the output
        if (preg_match('/(\[[\s\S]*?"message_id"[\s\S]*?\])/s', $output, $jsonMatches)) {
            try {
                $jsonString = trim($jsonMatches[1]);
                $jsonData = json_decode($jsonString, true);

                if (json_last_error() === JSON_ERROR_NONE && $jsonData !== null && is_array($jsonData)) {
                    $messageData = is_array($jsonData[0]) ? $jsonData[0] : $jsonData;
                    $data['message_id'] = $messageData['message_id'] ?? $messageData['id'] ?? null;
                    $data['status'] = $messageData['status'] ?? null;
                    $data['network'] = $messageData['network'] ?? $messageData['carrier'] ?? null;
                    $data['sender'] = $messageData['sender_name'] ?? $messageData['sendername'] ?? null;

                    if ($data['message_id']) {
                        Log::debug('Successfully parsed JSON from fallback pattern', [
                            'message_id' => $data['message_id']
                        ]);
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                // Continue to next fallback
            }
        }

        // Fallback: Look for specific patterns in the output (with timestamp prefix support)
        if (preg_match('/\[.*?\]\s*Message ID:\s*(\d+)/', $output, $matches)) {
            $data['message_id'] = $matches[1];
        } elseif (preg_match('/Message ID:\s*(\d+)/', $output, $matches)) {
            $data['message_id'] = $matches[1];
        }

        if (preg_match('/\[.*?\]\s*Status:\s*([^\n]+)/', $output, $matches)) {
            $data['status'] = trim($matches[1]);
        } elseif (preg_match('/Status:\s*([^\n]+)/', $output, $matches)) {
            $data['status'] = trim($matches[1]);
        }

        if (preg_match('/\[.*?\]\s*Network:\s*([^\n]+)/', $output, $matches)) {
            $data['network'] = trim($matches[1]);
        } elseif (preg_match('/Network:\s*([^\n]+)/', $output, $matches)) {
            $data['network'] = trim($matches[1]);
        }

        if (preg_match('/\[.*?\]\s*Sender:\s*([^\n]+)/', $output, $matches)) {
            $data['sender'] = trim($matches[1]);
        } elseif (preg_match('/Sender:\s*([^\n]+)/', $output, $matches)) {
            $data['sender'] = trim($matches[1]);
        }

        return $data;
    }


    /**
     * Create status update message with clear next steps
     */
    private function createStatusUpdateMessage($trackingNumber, $status, $fullName = null, $remarks = null)
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";

        // Define messages for each status with clear next steps
        $messages = [
            'submitted' => "{$greeting} Your request #{$trackingNumber} has been submitted successfully. We will review your request and contact you soon. Please wait for further updates. Thank you!",

            // Inquiry SMS is automatically sent - no remarks needed
            'inquiry' => "{$greeting} Your request #{$trackingNumber} is now under inquiry. Our office is reviewing your application. Please wait for our staff to contact you for any concerns regarding your application. Thank you!",

            'to_be_encoded_brap' => "{$greeting} Your request #{$trackingNumber} has been encoded to the BRAP (Birth Registration Assistance Project) request list. This is part of the standard processing for delayed birth registration under BRAP. We will notify you of the next steps. Thank you!",

            'checklist_given' => "{$greeting} Your request #{$trackingNumber} - A checklist of required documents has been provided. Please submit all required documents to our office to proceed with your application. Visit us during office hours (Mon-Fri, 8AM-5PM). Thank you!",

            'incomplete_requirements' => function ($trackingNumber, $fullName, $remarks) use ($greeting) {
                $baseMessage = "{$greeting} Your request #{$trackingNumber} - Some required documents are missing or incomplete.";

                // Check if remarks contain missing requirements
                if ($remarks && stripos($remarks, 'Missing requirements:') !== false) {
                    // Extract missing requirements from remarks - match until "Remarks:" or end of string
                    if (preg_match('/Missing requirements:\s*(.+?)(?:\s*Remarks:|$)/is', $remarks, $matches)) {
                        $missingItems = trim($matches[1]);
                        // Remove trailing period if present
                        $missingItems = rtrim($missingItems, '.');
                        $baseMessage .= " Missing requirements: {$missingItems}.";
                    }
                    
                    // Extract remaining remarks after "Missing requirements:" section
                    // Match everything after "Remarks:" if it exists, otherwise get what's after the missing requirements section
                    if (preg_match('/Remarks:\s*(.+)$/is', $remarks, $remarksMatches)) {
                        $otherRemarks = trim($remarksMatches[1]);
                        // Remove any duplicate missing requirements content that might be in remarks
                        $otherRemarks = preg_replace('/\s*Missing requirements:.*?$/is', '', $otherRemarks);
                        $otherRemarks = trim($otherRemarks);
                        
                        // Only add remarks if there's meaningful content left (not just the missing requirements repeated)
                        if (!empty($otherRemarks) && stripos($otherRemarks, $missingItems ?? '') === false) {
                            $baseMessage .= " Remarks: {$otherRemarks}.";
                        }
                    }
                } else {
                    // No missing requirements section, just add remarks if present
                    if ($remarks && !empty(trim($remarks))) {
                        $baseMessage .= " Remarks: " . trim($remarks) . ".";
                    }
                }

                $baseMessage .= " Please submit the missing documents to our office. Visit us during office hours (Mon-Fri, 8AM-5PM) to complete your application. Thank you!";

                return $baseMessage;
            },

            'accepted' => "{$greeting} Great news! Your request #{$trackingNumber} has been accepted. Your application is now posted for 10 calendar days. We will notify you once it's registered and ready for pick up. Thank you!",

            'psa_processing' => "{$greeting} Your request #{$trackingNumber} is currently being processed by PSA (Philippine Statistics Authority). This may take several days. We will notify you once the processing is complete. Please wait for further updates. Thank you!",

            'posting' => "{$greeting} Your request #{$trackingNumber} is now posted for public viewing. This is a required 10-day posting period for BRAP requests. After this period, your request will be ready for final approval. We will notify you of the next steps. Thank you!",

            'ready_for_approval' => "{$greeting} Your request #{$trackingNumber} has completed the posting period and is now ready for final approval. Our office will review and approve your request shortly. We will notify you once approved. Thank you!",

            'registered' => "{$greeting} Congratulations! Your request #{$trackingNumber} has been registered and is now ready for pickup. Please visit our office during office hours (Mon-Fri, 8AM-5PM) to collect your document. Bring a valid ID for verification. Thank you for using our services!",

            'encoded_brap' => "{$greeting} Your request #{$trackingNumber} will be encoded to the BRAP (Birth Registry Application Program) request list. For delayed registration under BRAP, you will be notified as soon as the result is available. Thank you!",

            'negative_result' => "{$greeting} Your request #{$trackingNumber} - The result of your application is already available, kindly comply the needed requirements. Thank you!",

            'positive_result' => "{$greeting} Great news! Your request #{$trackingNumber} has received a positive result from PSA (Philippine Statistics Authority). Your application will now proceed to the 10-day posting period. We will notify you once it's registered and ready for pick up. Thank you!",

            'rejected' => "{$greeting} Your request #{$trackingNumber} has been rejected. Please visit our office to discuss the reason and possible next steps. Our staff will assist you with any questions. Thank you!",

            // Delayed Birth Registration (Regular) specific statuses
            'waiting_for_psa_negative_certificate' => "{$greeting} Your request #{$trackingNumber} is now waiting for you to submit the negative certification from PSA (Philippine Statistics Authority) together with the requirements. Please visit our office during office hours (Mon-Fri, 8AM-5PM) to submit your documents. Thank you!",

            'accepted_octogenarian' => "{$greeting} Your request #{$trackingNumber} has been sent to PSA for evaluation. We will notify you once the evaluation is completed. Please wait for further updates. Thank you!",

            'verified_with_problem' => "{$greeting} Your request #{$trackingNumber} has been verified by PSA but with a problem. Please report to the City Civil Registry Office during office hours for further instructions regarding your application. Bring valid ID for verification. Thank you!",

            'for_compliance' => function ($trackingNumber, $fullName, $remarks) use ($greeting) {
                $baseMessage = "{$greeting} Your request #{$trackingNumber} requires compliance with additional requirements.";

                // Check if remarks contain missing requirements
                if ($remarks && stripos($remarks, 'Missing requirements:') !== false) {
                    // Extract missing requirements from remarks - match until "Remarks:" or end of string
                    if (preg_match('/Missing requirements:\s*(.+?)(?:\s*Remarks:|$)/is', $remarks, $matches)) {
                        $missingItems = trim($matches[1]);
                        // Remove trailing period if present
                        $missingItems = rtrim($missingItems, '.');
                        $baseMessage .= " Required documents: {$missingItems}.";
                    }
                    
                    // Extract remaining remarks after "Missing requirements:" section
                    // Match everything after "Remarks:" if it exists, otherwise get what's after the missing requirements section
                    if (preg_match('/Remarks:\s*(.+)$/is', $remarks, $remarksMatches)) {
                        $otherRemarks = trim($remarksMatches[1]);
                        // Remove any duplicate missing requirements content that might be in remarks
                        $otherRemarks = preg_replace('/\s*Missing requirements:.*?$/is', '', $otherRemarks);
                        $otherRemarks = trim($otherRemarks);
                        
                        // Only add remarks if there's meaningful content left (not just the missing requirements repeated)
                        if (!empty($otherRemarks) && stripos($otherRemarks, $missingItems ?? '') === false) {
                            $baseMessage .= " Remarks: {$otherRemarks}.";
                        }
                    }
                } else {
                    // No missing requirements section, just add remarks if present
                    if ($remarks && !empty(trim($remarks))) {
                        $baseMessage .= " Remarks: " . trim($remarks) . ".";
                    }
                }

                $baseMessage .= " Please submit the required documents to our office. Visit us during office hours (Mon-Fri, 8AM-5PM) to complete your compliance. Thank you!";

                return $baseMessage;
            },

            'completed' => "{$greeting} Congratulations! Your request #{$trackingNumber} has been completed after the required posting period. Your document is now ready for pickup. Please visit our office during office hours (Mon-Fri, 8AM-5PM) to collect your document. Bring a valid ID for verification. Thank you for using our services!",
        ];

        // Get base message for the status
        $baseMessage = $messages[$status] ?? null;

        if (!$baseMessage) {
            // Fallback for unknown statuses
            $statusLabel = ucwords(str_replace('_', ' ', $status));
            $baseMessage = "{$greeting} Your request #{$trackingNumber} status has been updated to: {$statusLabel}. We will notify you of the next steps. Thank you for using our services!";
        }

        // If message is a callable (function), call it with parameters
        if (is_callable($baseMessage)) {
            $baseMessage = $baseMessage($trackingNumber, $fullName, $remarks);
        }

        // Statuses that should NOT have remarks appended automatically
        // - inquiry: automatically sent, no officer input for remarks
        // - callable statuses: already handle remarks internally
        $noRemarksStatuses = ['inquiry'];

        // For other statuses, append remarks ONLY if officer explicitly provided them
        // Remarks are optional - only append if there's actual content typed by the officer
        if (!is_callable($messages[$status] ?? null) && !in_array($status, $noRemarksStatuses) && $remarks && !empty(trim($remarks))) {
            $baseMessage .= " Remarks: " . trim($remarks) . ".";
        }

        return $baseMessage;
    }

    /**
     * Check if Node.js is available in PATH
     */
    private function isNodeAvailable()
    {
        try {
            $result = Process::run("node --version");
            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find Node.js executable in common locations
     */
    private function findNodeExecutable()
    {
        $commonPaths = [
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Roaming\\npm\\node.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Local\\Programs\\nodejs\\node.exe',
            'C:\\nodejs\\node.exe',
            'node' // fallback to PATH
        ];

        foreach ($commonPaths as $path) {
            if ($path === 'node') {
                return $path; // fallback
            }

            if (file_exists($path)) {
                Log::info('Found Node.js at: ' . $path);
                return $path;
            }
        }

        Log::warning('Node.js not found in common locations, using fallback');
        return 'node';
    }

}
