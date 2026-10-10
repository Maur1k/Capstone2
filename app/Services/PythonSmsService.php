<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class NodeSmsService
{
    private $nodeScriptPath;
    private $nodeExecutable;

    public function __construct()
    {
        $this->nodeScriptPath = base_path('sms-service/send_sms.js');
        $this->nodeExecutable = config('services.node_sms.node_path', 'node');
    }

    /**
     * Send SMS via Node.js script
     */
    public function sendSMS($phoneNumber, $message, $senderName = null)
    {
        try {
            // Escape the message and phone number for command line
            $escapedMessage = escapeshellarg($message);
            $escapedNumber = escapeshellarg($phoneNumber);
            
            // Build the command
            $command = "{$this->nodeExecutable} {$this->nodeScriptPath} {$escapedMessage} {$escapedNumber}";
            
            Log::info('Executing Node.js SMS command', [
                'command' => $command,
                'phone_number' => $phoneNumber,
                'message_length' => strlen($message)
            ]);
            
            // Execute the Node.js script
            $result = Process::run($command);
            
            $exitCode = $result->exitCode();
            $output = $result->output();
            $errorOutput = $result->errorOutput();
            
            Log::info('Node.js SMS command executed', [
                'exit_code' => $exitCode,
                'output' => $output,
                'error_output' => $errorOutput
            ]);
            
            if ($exitCode === 0) {
                // Parse the output to extract message details
                $messageData = $this->parseNodeOutput($output);
                
                Log::info('SMS sent successfully via Node.js script', [
                    'phone_number' => $phoneNumber,
                    'message_length' => strlen($message),
                    'message_id' => $messageData['message_id'] ?? 'N/A',
                    'status' => $messageData['status'] ?? 'N/A',
                    'network' => $messageData['network'] ?? 'N/A'
                ]);
                
                return [
                    'success' => true,
                    'message_id' => $messageData['message_id'] ?? null,
                    'status' => $messageData['status'] ?? null,
                    'network' => $messageData['network'] ?? null,
                    'data' => $messageData
                ];
            } else {
                Log::error('Node.js SMS command failed', [
                    'phone_number' => $phoneNumber,
                    'exit_code' => $exitCode,
                    'output' => $output,
                    'error_output' => $errorOutput
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Node.js script failed',
                    'message' => $errorOutput ?: $output,
                    'exit_code' => $exitCode
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
     * Send status update SMS
     */
    public function sendStatusUpdate($phoneNumber, $trackingNumber, $status, $fullName = null)
    {
        try {
            // Create status update message
            $message = $this->createStatusUpdateMessage($trackingNumber, $status, $fullName);
            
            // Send SMS
            return $this->sendSMS($phoneNumber, $message);
            
        } catch (\Exception $e) {
            Log::error('Status update SMS exception via Node.js', [
                'phone_number' => $phoneNumber,
                'tracking_number' => $trackingNumber,
                'status' => $status,
                'error' => $e->getMessage()
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
        
        // Look for specific patterns in the output
        if (preg_match('/Message ID: (\d+)/', $output, $matches)) {
            $data['message_id'] = $matches[1];
        }
        
        if (preg_match('/Status: ([^\n]+)/', $output, $matches)) {
            $data['status'] = trim($matches[1]);
        }
        
        if (preg_match('/Network: ([^\n]+)/', $output, $matches)) {
            $data['network'] = trim($matches[1]);
        }
        
        if (preg_match('/Sender: ([^\n]+)/', $output, $matches)) {
            $data['sender'] = trim($matches[1]);
        }
        
        return $data;
    }


    /**
     * Create status update message
     */
    private function createStatusUpdateMessage($trackingNumber, $status, $fullName = null)
    {
        $statusLabels = [
            'inquiry' => 'Inquiry',
            'to_be_encoded_brap' => 'To Be Encoded to BRAP Request List',
            'checklist_given' => 'Checklist of Requirements Given',
            'incomplete_requirements' => 'Incomplete Requirements',
            'accepted' => 'Accepted',
            'registered' => 'Registered'
        ];
        
        $statusLabel = $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status));
        
        // Special message for registered status
        if ($status === 'registered') {
            return "Your request #{$trackingNumber} is now ready to receive! Please visit our office to collect your document. Thank you for using our services!";
        }
        
        return "Your request #{$trackingNumber} status has been updated to: {$statusLabel}. Thank you for using our services!";
    }

}
