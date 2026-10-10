<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CivilService;
use App\Services\NotificationService;

class CheckApprovalRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:check-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for requests ready for approval after 10-day posting period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for requests ready for approval...');

        // Get all accepted requests that have been posted for 10+ business days
        $readyForApproval = CivilService::where('status', 'accepted')
            ->where('posting_started_at', '!=', null)
            ->get()
            ->filter(function ($request) {
                return $request->isReadyForApproval();
            });

        if ($readyForApproval->isEmpty()) {
            $this->info('No requests are ready for approval.');
            return 0;
        }

        $this->info("Found {$readyForApproval->count()} requests ready for approval.");

        $notificationService = new NotificationService();
        $processedCount = 0;

        foreach ($readyForApproval as $request) {
            try {
                // Mark as ready for approval
                $request->markForApproval();
                
                // Get the officer for this department
                $officer = \App\Models\Admin::where('department', $request->department)
                    ->where('role', 'officer')
                    ->first();
                
                if ($officer) {
                    // Send notification to officer
                    $notificationService->notifyApprovalReady($request, $officer->id);
                    $this->line("Notified officer for request: {$request->tracking_number}");
                } else {
                    $this->warn("No officer found for department: {$request->department}");
                }
                
                $processedCount++;
                
            } catch (\Exception $e) {
                $this->error("Error processing request {$request->tracking_number}: " . $e->getMessage());
            }
        }

        $this->info("Successfully processed {$processedCount} requests for approval.");
        return 0;
    }
}