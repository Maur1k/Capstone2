<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Log;

class OtherService extends Model
{
    use HasFactory;

    public const STATUS_SEQUENCE = [
        'inquiry',
        'incomplete_requirements',
        'registered',
    ];

    protected $fillable = [
        'tracking_number',
        'queue_number',
        'window_number',
        'service_type',
        'request_type',
        'service_name',
        'department',
        'status',
        'full_name',
        'contact_number',
        'email',
        'address',
        'purpose',
        'additional_info',
        'processed_by',
        'processed_at',
        'accepted_at',
        'notified_at',
        'admin_notes',
        'print_status',
        'called_at',
    ];

    protected $casts = [
        'additional_info' => 'array',
        'processed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'notified_at' => 'datetime',
        'called_at' => 'datetime',
    ];

    public static function getNextQueueNumber(): int
    {
        $today = now()->format('Y-m-d');
        $lastQueue = self::whereDate('created_at', $today)->orderBy('queue_number', 'desc')->value('queue_number');
        return $lastQueue ? (int) $lastQueue + 1 : 1;
    }

    public static function generateTrackingNumber(): string
    {
        do {
            $prefix = 'C2S-' . now()->format('Ymd');
            // Generate 4-digit random number (0001-9999)
            $suffix = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $trackingNumber = $prefix . '-' . $suffix;
        } while (self::where('tracking_number', $trackingNumber)->exists());
        return $trackingNumber;
    }

    /**
     * Scope for request type (BRAP or Regular)
     */
    public function scopeByRequestType($query, $requestType)
    {
        return $query->where('request_type', $requestType);
    }

    /**
     * Scope for BRAP requests
     */
    public function scopeBrap($query)
    {
        return $query->where('request_type', 'brap');
    }

    /**
     * Scope for Regular requests
     */
    public function scopeRegular($query)
    {
        return $query->where('request_type', 'regular');
    }

    /**
     * Timeline of status updates recorded by officers/admins.
     */
    public function statusUpdates(): MorphMany
    {
        return $this
            ->morphMany(StatusUpdate::class, 'statusable')
            ->orderByDesc('status_date')
            ->orderByDesc('created_at');
    }

    /**
     * Get all checklist items for this request
     */
    public function checklistItems(): MorphMany
    {
        return $this->morphMany(ChecklistItem::class, 'checklistable');
    }

    /**
     * Get the latest checklist item
     */
    public function latestChecklistItem()
    {
        return $this->morphOne(ChecklistItem::class, 'checklistable')->latestOfMany();
    }

    public static function statusFlow(): array
    {
        return self::STATUS_SEQUENCE;
    }

    public static function canTransition(?string $from, string $to): bool
    {
        $flow = self::statusFlow();
        $nextIndex = array_search($to, $flow, true);

        if ($nextIndex === false) {
            return false;
        }

        if ($from === null) {
            return $nextIndex === 0; // Only allow starting with 'inquiry'
        }

        $currentIndex = array_search($from, $flow, true);

        // If current status is not in the flow (old status like 'submitted'), allow transition to any valid status
        // This handles cases where requests might have been created with a different initial status
        if ($currentIndex === false) {
            \Log::info('OtherService: Current status not in flow, allowing transition to valid status', [
                'from' => $from,
                'to' => $to,
                'flow' => $flow
            ]);
            // Allow transition to any status in the flow as a correction
            return $nextIndex !== false;
        }

        // Prevent backwards from registered (final status)
        if ($from === 'registered') {
            return false;
        }

        // Allow self-loop: incomplete_requirements -> incomplete_requirements
        if ($from === 'incomplete_requirements' && $to === 'incomplete_requirements') {
            return true;
        }

        // Allow forward: inquiry -> incomplete_requirements
        if ($from === 'inquiry' && $to === 'incomplete_requirements') {
            \Log::info('OtherService: Allowing inquiry -> incomplete_requirements');
            return true;
        }

        // Allow direct forward: inquiry -> registered
        if ($from === 'inquiry' && $to === 'registered') {
            \Log::info('OtherService: Allowing inquiry -> registered');
            return true;
        }

        // Allow forward: incomplete_requirements -> registered
        if ($from === 'incomplete_requirements' && $to === 'registered') {
            \Log::info('OtherService: Allowing incomplete_requirements -> registered');
            return true;
        }

        // Prevent all other transitions (including backwards from inquiry, and updating to same status for other statuses)
        if ($from === $to) {
            return false; // Prevent same status updates for inquiry and registered
        }

        \Log::warning('OtherService: Transition not allowed', [
            'from' => $from,
            'to' => $to,
            'current_index' => $currentIndex,
            'next_index' => $nextIndex,
            'flow' => $flow
        ]);

        return false;
    }

    /**
     * Scope for tracking number
     */
    public function scopeByTracking($query, $trackingNumber)
    {
        return $query->where('tracking_number', $trackingNumber);
    }

    /**
     * Update status with timestamp
     */
    public function updateStatus($status, $processedBy = null, $notes = null)
    {
        if (!static::canTransition($this->status, $status)) {
            throw new \InvalidArgumentException("Invalid status transition from {$this->status} to {$status}");
        }

        $oldStatus = $this->status;
        $this->status = $status;
        $this->processed_by = $processedBy;
        $this->processed_at = now();
        
        // Set accepted_at when status changes to 'accepted'
        if ($status === 'accepted') {
            $this->accepted_at = now();
        }
        
        // Set called_at when queue moves from "On Queue" (inquiry/submitted) to "To Submit" statuses
        // This triggers the sound chime to notify the client it's their turn
        $onQueueStatuses = ['submitted', 'inquiry'];
        $toSubmitStatuses = ['incomplete_requirements'];
        
        if (in_array($oldStatus, $onQueueStatuses) && in_array($status, $toSubmitStatuses) && !$this->called_at) {
            $this->called_at = now();
        }
        
        if ($notes !== null) {
            $this->admin_notes = $notes;
        }
        $this->save();
    }

    /**
     * Check if request is ready for notification (10 days after accepted)
     */
    public function isReadyForNotification()
    {
        if (!$this->accepted_at || $this->notified_at) {
            return false;
        }
        
        // Check if 10 business days have passed (excluding weekends)
        $businessDays = 0;
        $currentDate = $this->accepted_at->copy();
        
        while ($businessDays < 10) {
            $currentDate->addDay();
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $businessDays++;
            }
        }
        
        return now()->gte($currentDate);
    }

    /**
     * Mark as notified
     */
    public function markAsNotified()
    {
        $this->notified_at = now();
        $this->save();
    }

    /**
     * Get tracking data for display
     */
    public function getTrackingData()
    {
        // Get latest status update with remarks
        $latestStatusUpdate = $this->statusUpdates()->latest()->first();
        
        return [
            'full_name' => $this->full_name,
            'service_name' => $this->service_name,
            'service_type' => $this->service_type,
            'department' => $this->department,
            'document_type' => $this->service_type, // For compatibility with tracker
            'tracking_number' => $this->tracking_number,
            'queue_number' => \App\Models\Setting::getBoolean('feature.queuing_number', false) ? $this->queue_number : null,
            'status' => $this->status,
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('H:i:s'),
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'purpose' => $this->purpose,
            'additional_info' => $this->additional_info,
            'print_status' => $this->print_status,
            'request_type' => $this->request_type,
            'remarks' => $latestStatusUpdate?->remarks ?? $this->admin_notes,
            'admin_notes' => $this->admin_notes, // Include admin_notes for compatibility
            'status_date' => $latestStatusUpdate?->status_date?->format('Y-m-d'),
            'status_updates' => $this->statusUpdates()->orderBy('created_at', 'asc')->get()->map(function ($update) {
                return [
                    'status' => $update->status,
                    'status_date' => $update->status_date?->format('Y-m-d'),
                    'remarks' => $update->remarks,
                    'created_at' => $update->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),
        ];
    }
}


