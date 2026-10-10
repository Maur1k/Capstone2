<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Admin;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--create-sample : Create sample requests for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system for status changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Testing Notification System...');
        
        $notificationService = new NotificationService();
        
        if ($this->option('create-sample')) {
            $this->createSampleRequests();
        }
        
        // Test with existing requests
        $this->testWithExistingRequests($notificationService);
        
        $this->info('✅ Notification system test completed!');
    }
    
    private function createSampleRequests()
    {
        $this->info('Creating sample requests...');
        
        // Create a sample Civil Service request
        $civilRequest = CivilService::create([
            'full_name' => 'John Doe Test',
            'contact_number' => '09123456789',
            'service_name' => 'Delayed Regular Non-BRAP Birth Registration Assistance Project',
            'department' => 'Civil Registry Office',
            'tracking_number' => 'TEST-CIVIL-' . time(),
            'queue_number' => '001',
            'request_type' => 'regular',
            'status' => 'inquiry',
            'service_type' => 'Regular Service',
        ]);
        
        // Create a sample Other Service request
        $otherRequest = OtherService::create([
            'full_name' => 'Jane Smith Test',
            'contact_number' => '09987654321',
            'service_name' => 'BPLO Business Registration',
            'department' => 'Business Permits and Licensing Office',
            'tracking_number' => 'TEST-OTHER-' . time(),
            'queue_number' => '002',
            'request_type' => 'regular',
            'status' => 'inquiry',
            'service_type' => 'Regular Service',
        ]);
        
        $this->info("Created sample requests:");
        $this->line("- Civil Service: {$civilRequest->tracking_number}");
        $this->line("- Other Service: {$otherRequest->tracking_number}");
    }
    
    private function testWithExistingRequests($notificationService)
    {
        $this->info('Testing with existing requests...');
        
        // Get a Civil Service request
        $civilRequest = CivilService::first();
        if ($civilRequest) {
            $this->info("Testing with Civil Service request: {$civilRequest->tracking_number}");
            
            // Test status change notification
            $oldStatus = $civilRequest->status;
            $newStatus = 'checklist_given';
            
            $result = $notificationService->notifyStatusChange($civilRequest, $oldStatus, $newStatus);
            
            if ($result) {
                $this->info("✅ Civil Service notification created successfully");
            } else {
                $this->error("❌ Failed to create Civil Service notification");
            }
            
            // Test SMS notification
            $smsResult = $notificationService->notifyClientViaSMS($civilRequest, $newStatus);
            if ($smsResult) {
                $this->info("✅ SMS notification prepared for Civil Service");
            } else {
                $this->warn("⚠️ SMS notification failed for Civil Service");
            }
        } else {
            $this->warn("No Civil Service requests found");
        }
        
        // Get an Other Service request
        $otherRequest = OtherService::first();
        if ($otherRequest) {
            $this->info("Testing with Other Service request: {$otherRequest->tracking_number}");
            
            // Test status change notification
            $oldStatus = $otherRequest->status;
            $newStatus = 'accepted';
            
            $result = $notificationService->notifyStatusChange($otherRequest, $oldStatus, $newStatus);
            
            if ($result) {
                $this->info("✅ Other Service notification created successfully");
            } else {
                $this->error("❌ Failed to create Other Service notification");
            }
            
            // Test SMS notification
            $smsResult = $notificationService->notifyClientViaSMS($otherRequest, $newStatus);
            if ($smsResult) {
                $this->info("✅ SMS notification prepared for Other Service");
            } else {
                $this->warn("⚠️ SMS notification failed for Other Service");
            }
        } else {
            $this->warn("No Other Service requests found");
        }
        
        // Show notification count
        $notificationCount = \App\Models\Notification::count();
        $this->info("Total notifications in database: {$notificationCount}");
    }
}