<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CivilService;
use App\Models\OtherService;
use App\Models\Notification;

class CheckReadyForApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:check-ready-for-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for regular requests that are ready for approval after 10 business days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for requests ready for approval...');
        
        $notificationsCreated = 0;
        
        // Check Civil Services
        $civilRequests = CivilService::regular()
            ->where('status', 'accepted')
            ->whereNotNull('accepted_at')
            ->whereNull('notified_at')
            ->get();
            
        foreach ($civilRequests as $request) {
            if ($request->isReadyForNotification()) {
                Notification::createReadyForApproval(
                    $request->tracking_number,
                    $request->request_type,
                    $request->full_name
                );
                
                $request->markAsNotified();
                $notificationsCreated++;
                
                $this->line("Created notification for Civil Service request: {$request->tracking_number}");
            }
        }
        
        // Check Other Services
        $otherRequests = OtherService::regular()
            ->where('status', 'accepted')
            ->whereNotNull('accepted_at')
            ->whereNull('notified_at')
            ->get();
            
        foreach ($otherRequests as $request) {
            if ($request->isReadyForNotification()) {
                Notification::createReadyForApproval(
                    $request->tracking_number,
                    $request->request_type,
                    $request->full_name
                );
                
                $request->markAsNotified();
                $notificationsCreated++;
                
                $this->line("Created notification for Other Service request: {$request->tracking_number}");
            }
        }
        
        $this->info("Created {$notificationsCreated} notifications for requests ready for approval.");
        
        return Command::SUCCESS;
    }
}
