<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CivilService extends Model
{
    use HasFactory;

    // Full status sequence including all possible statuses
    public const STATUS_SEQUENCE = [
        'submitted',
        'inquiry',
        'to_be_encoded_brap',
        'checklist_given',
        'incomplete_requirements',
        'accepted',
        'psa_processing',
        'posting',
        'ready_for_approval',
        'registered',
    ];

    // BRAP-specific status flow (includes to_be_encoded_brap)
    public const BRAP_STATUS_FLOW = [
        'submitted',
        'inquiry',
        'to_be_encoded_brap',
        'checklist_given',
        'incomplete_requirements',
        'accepted',
        'psa_processing',
        'posting',
        'ready_for_approval',
        'registered',
    ];

    // Regular/Non-BRAP status flow (excludes to_be_encoded_brap, psa_processing, posting, and ready_for_approval)
    public const REGULAR_STATUS_FLOW = [
        'submitted',
        'inquiry',
        'checklist_given',
        'incomplete_requirements',
        'accepted',
        'registered',
    ];

    // Delayed Regular Non-BRAP status flow (specific flow for Delayed Birth Registration - Regular)
    // Flow: INQUIRY -> WAITING FOR PSA NEGATIVE CERTIFICATE -> 
    //       POSITIVE RESULT -> COMPLETED (auto-posting starts, after 10 days -> completed)
    //       OR -> INCOMPLETE REQUIREMENTS (loop) -> ACCEPTED/ACCEPTED (OCTOGENARIAN)
    //       ACCEPTED (OCTOGENARIAN) -> VERIFIED WITH PROBLEM -> FOR COMPLIANCE (loop) -> ACCEPTED
    //       ACCEPTED -> REGISTERED (auto-posting starts, after 10 days -> registered)
    // Note: 'posting' is not in the flow as it's automatic when 'accepted' or 'positive_result' is set
    public const DELAYED_REGULAR_STATUS_FLOW = [
        'inquiry',
        'waiting_for_psa_negative_certificate',
        'positive_result',
        'incomplete_requirements',
        'accepted',
        'accepted_octogenarian',
        'verified_with_problem',
        'for_compliance',
        'registered',
    ];

    // Delayed BRAP status flow (strict flow for Delayed BRAP requests)
    // Flow: INQUIRY -> TO BE ENCODED TO BRAP REQUEST LIST -> POSITIVE RESULT (end via 10 days posting)
    //       OR -> NEGATIVE RESULT -> INCOMPLETE REQUIREMENTS (loop) / ACCEPTED / ACCEPTED (OCTOGENARIAN)
    //       ACCEPTED (OCTOGENARIAN) -> VERIFIED WITH PROBLEM -> FOR COMPLIANCE (loop) -> ACCEPTED
    //       ACCEPTED -> POSTING (10 days) -> REGISTERED (completed)
    public const DELAYED_BRAP_STATUS_FLOW = [
        'inquiry',
        'to_be_encoded_brap',
        'positive_result',
        'negative_result',
        'incomplete_requirements',
        'accepted',
        'accepted_octogenarian',
        'verified_with_problem',
        'for_compliance',
        'posting',
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
        'additional_info',
        'child_info',
        'mother_info',
        'father_info',
        'contact_info',
        'processed_by',
        'processed_at',
        'accepted_at',
        'notified_at',
        'admin_notes',
        'print_status',
        'posting_started_at',
        'posting_ended_at',
        'posting_days',
        'approved_at',
        'requirements_checklist',
        'checklist_sent_at',
        'is_holiday_excluded',
        'called_at',
    ];

    protected $casts = [
        'child_info' => 'array',
        'mother_info' => 'array',
        'father_info' => 'array',
        'contact_info' => 'array',
        'processed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'notified_at' => 'datetime',
        'posting_started_at' => 'datetime',
        'posting_ended_at' => 'datetime',
        'approved_at' => 'datetime',
        'checklist_sent_at' => 'datetime',
        'is_holiday_excluded' => 'boolean',
        'called_at' => 'datetime',
    ];

    /**
     * Get the status with proper formatting
     */
    protected function statusFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => ucwords(str_replace('_', ' ', $this->status))
        );
    }

    /**
     * Get the service type display name
     */
    protected function serviceTypeDisplay(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->service_type
        );
    }

    /**
     * Scope for tracking number
     * Handles multiple formats:
     * - C2S-YYYYMMDD-XXXX (with dashes) and C2SYYYYMMDDXXXX (without dashes)
     * - APT-YYYYMMDD-XXXXXX (with dashes) and APTYYYYMMDDXXXX (without dashes)
     * - APT format variations for appointment numbers
     */
    public function scopeByTracking($query, $trackingNumber)
    {
        $numbersToTry = [$trackingNumber];
        
        // Handle C2S format: C2S-YYYYMMDD-XXXX (with dashes) and C2SYYYYMMDDXXXX (without dashes)
        if (preg_match('/^C2S-(\d{8})-(\d{4})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'C2S' . $matches[1] . $matches[2];
        }
        
        if (preg_match('/^C2S(\d{8})(\d{4})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'C2S-' . $matches[1] . '-' . $matches[2];
        }
        
        // Handle APT format: APT-YYYYMMDD-XXXXXX (with dashes) and APTYYYYMMDDXXXX (without dashes)
        // Format: APTYYYYMMDDXXXX (e.g., APT202512011794)
        if (preg_match('/^APT-(\d{8})-(\d{4,6})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'APT' . $matches[1] . $matches[2];
        }
        
        if (preg_match('/^APT(\d{8})(\d{4,6})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'APT-' . $matches[1] . '-' . $matches[2];
        }
        
        // Remove duplicates
        $numbersToTry = array_unique($numbersToTry);
        
        // Try all possible formats
        return $query->whereIn('tracking_number', $numbersToTry);
    }

    /**
     * Scope for status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
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
     * Get the next queue number for today
     */
    public static function getNextQueueNumber()
    {
        $today = now()->format('Y-m-d');
        $lastQueue = self::whereDate('created_at', $today)
            ->orderBy('queue_number', 'desc')
            ->value('queue_number');

        return $lastQueue ? (int) $lastQueue + 1 : 1;
    }

    /**
     * Generate a unique tracking number
     */
    public static function generateTrackingNumber()
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
     * Update status with timestamp
     */
    public function updateStatus($status, $processedBy = null, $notes = null)
    {
        // Determine request type: check service_name first to override incorrect database values
        $serviceName = $this->service_name ?? '';
        $dbRequestType = $this->request_type;
        
        // Priority 1: Check for "Delayed Birth Registration (Regular)" - SIMPLIFIED DETECTION
        if (stripos($serviceName, 'Delayed Birth Registration (Regular)') !== false || 
            stripos($serviceName, 'Delayed Birth Registration(Regular)') !== false) {
            $requestType = 'delayed_regular';
        }
        // Priority 2: Check if this is Delayed Regular Non-BRAP (has Delayed and Non-BRAP)
        elseif (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) {
            $requestType = 'delayed_regular';
        }
        // Priority 3: Check if this is Delayed BRAP (must have Delayed and BRAP, but NOT Non-BRAP)
        elseif (stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) {
            $requestType = 'delayed_brap';
        }
        // Priority 4: If service_name clearly indicates Non-BRAP (without Delayed), force it to 'regular'
        elseif (stripos($serviceName, 'Non-BRAP') !== false || stripos($serviceName, 'Non BRAP') !== false) {
            $requestType = 'regular';
        }
        // Priority 5: If service_name contains BRAP (but not Non-BRAP and not Delayed), it's regular BRAP
        elseif (stripos($serviceName, 'BRAP') !== false) {
            $requestType = 'brap';
        }
        // Priority 6: Use database value if set
        elseif ($dbRequestType && $dbRequestType !== '') {
            $requestType = $dbRequestType;
        }
        // Priority 7: Default to 'regular'
        else {
            $requestType = 'regular';
        }
        
        // Pass serviceName to canTransition for proper Delayed BRAP detection
        if (!static::canTransition($this->status, $status, $requestType, $serviceName)) {
            throw new \InvalidArgumentException("Invalid status transition from {$this->status} to {$status}");
        }

        $oldStatus = $this->status;
        $this->status = $status;
        $this->processed_by = $processedBy;
        $this->processed_at = now();

        // Set accepted_at when status changes to 'accepted'
        if ($status === 'accepted') {
            $this->accepted_at = now();
            // Automatically start posting for ALL request types when accepted
            // This includes: Delayed Regular, Delayed BRAP, and regular BRAP
            if (!$this->posting_started_at) {
                $this->posting_started_at = now();
                // Store the current posting days setting when posting starts
                $this->posting_days = \App\Models\Setting::getPostingDays($this->department ?? 'Civil Registry Office', 10);
            }
        }
        
        // Set posting_started_at when status changes to 'positive_result' (auto-posting starts)
        if ($status === 'positive_result' && !$this->posting_started_at) {
            $this->posting_started_at = now();
            // Store the current posting days setting when posting starts
            $this->posting_days = \App\Models\Setting::getPostingDays($this->department ?? 'Civil Registry Office', 10);
        }
        
        // Set posting_started_at when status changes to 'posting' (if not already set - backward compatibility)
        if ($status === 'posting' && !$this->posting_started_at) {
            $this->posting_started_at = now();
            // Store the current posting days setting when posting starts
            $this->posting_days = \App\Models\Setting::getPostingDays($this->department ?? 'Civil Registry Office', 10);
        }

        // Set called_at when queue moves from "On Queue" (submitted/inquiry) to "To Submit" statuses
        // This triggers the sound chime to notify the client it's their turn
        $onQueueStatuses = ['submitted', 'inquiry'];
        $toSubmitStatuses = ['accepted', 'checklist_given', 'incomplete_requirements', 'to_be_encoded_brap', 
                            'negative_result', 'accepted_octogenarian', 'verified_with_problem', 'for_compliance'];
        
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
     * Check if request is ready for approval (10 days after posting started)
     * Only applies to BRAP requests that are in 'posting' or 'accepted' status
     * For Delayed BRAP: 10 calendar days after accepted (excluding holidays)
     */
    public function isReadyForApproval()
    {
        $serviceName = $this->service_name ?? '';
        // Strictly check for "Delayed BRAP Birth Registration Assistance Project" only
        $isDelayedBrap = $serviceName === 'Delayed BRAP Birth Registration Assistance Project' 
                      || $this->request_type === 'delayed_brap';
        
        // For Delayed BRAP: Check 10 calendar days after accepted (excluding holidays)
        if ($isDelayedBrap) {
            if (!$this->accepted_at || $this->status === 'registered') {
                return false;
            }
            
            // Must be in accepted status
            if ($this->status !== 'accepted') {
                return false;
            }
            
            // Check if 10 calendar days have passed (excluding holidays)
            $calendarDays = 0;
            $currentDate = $this->accepted_at->copy();
            
            while ($calendarDays < 10) {
                $currentDate->addDay();
                // Skip holidays if holiday exclusion is enabled
                if ($this->is_holiday_excluded && $this->isHoliday($currentDate)) {
                    continue;
                }
                $calendarDays++;
            }
            
            return now()->gte($currentDate);
        }
        
        // For regular BRAP: Must have posting_started_at and not be registered
        if (!$this->posting_started_at || $this->status === 'registered') {
            return false;
        }
        
        // Only BRAP requests go through the posting -> ready_for_approval flow
        $isBrap = (stripos($serviceName, 'Non-BRAP') === false && stripos($serviceName, 'BRAP') !== false) 
               || $this->request_type === 'brap';
        
        if (!$isBrap) {
            return false; // Non-BRAP requests don't go through approval process
        }
        
        // Must be in posting or accepted status (accepted is legacy, should be posting)
        if ($this->status !== 'posting' && $this->status !== 'accepted' && $this->status !== 'ready_for_approval') {
            return false;
        }

        // Get posting days - use stored value if available, otherwise use current setting
        $postingDays = $this->posting_days ?? \App\Models\Setting::getPostingDays($this->department ?? 'Civil Registry Office', 10);
        
        // Check if configured business days have passed (excluding weekends and holidays)
        $businessDays = 0;
        $currentDate = $this->posting_started_at->copy();

        while ($businessDays < $postingDays) {
            $currentDate->addDay();
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                // Skip holidays if holiday exclusion is enabled
                if ($this->is_holiday_excluded && $this->isHoliday($currentDate)) {
                    continue;
                }
                $businessDays++;
            }
        }

        return now()->gte($currentDate);
    }

    /**
     * Check if a date is a holiday (basic implementation)
     */
    public function isHoliday($date)
    {
        // Basic holiday checking - can be expanded with a holidays table
        $holidays = [
            '01-01', // New Year's Day
            '04-09', // Araw ng Kagitingan
            '05-01', // Labor Day
            '06-12', // Independence Day
            '08-30', // National Heroes Day
            '11-30', // Bonifacio Day
            '12-25', // Christmas Day
            '12-30', // Rizal Day
        ];

        $monthDay = $date->format('m-d');
        return in_array($monthDay, $holidays);
    }

    /**
     * Mark as ready for approval after 10-day posting
     */
    public function markForApproval()
    {
        // Update status to ready_for_approval (only for BRAP requests)
        // Determine request type to ensure this is a BRAP request
        $serviceName = $this->service_name ?? '';
        $dbRequestType = $this->request_type;
        
        // Check if this is a BRAP request
        $isBrap = false;
        if (stripos($serviceName, 'Non-BRAP') === false && stripos($serviceName, 'BRAP') !== false) {
            $isBrap = true;
        } elseif ($dbRequestType === 'brap') {
            $isBrap = true;
        }
        
        if ($isBrap) {
            $this->status = 'ready_for_approval';
        } else {
            // For non-BRAP, just mark posting as ended but keep status as accepted
            // (non-BRAP requests don't have ready_for_approval status)
            $this->status = 'accepted';
        }
        
        $this->posting_ended_at = now();
        $this->processed_at = now();
        $this->save();
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
     * Get the appointments for this civil service
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the document uploads for this civil service
     */
    public function documentUploads(): HasMany
    {
        return $this->hasMany(DocumentUpload::class);
    }

    /**
     * Status update history (officer/admin timeline)
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

    /**
     * Returns the configured status flow based on request type.
     * 
     * @param string|null $requestType 'brap', 'delayed_brap', 'delayed_regular', or 'regular'. If null, returns full sequence.
     * @param string|null $serviceName Optional service name to detect Delayed BRAP or Delayed Regular
     * @return array
     */
    public static function statusFlow(?string $requestType = null, ?string $serviceName = null): array
    {
        // Check if this is a Delayed BRAP request (must have Delayed and BRAP, but NOT Non-BRAP)
        if ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) {
            return self::DELAYED_BRAP_STATUS_FLOW;
        }
        
        // Check if this is Delayed Regular Non-BRAP (Delayed Birth Registration - Regular)
        if ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) {
            return self::DELAYED_REGULAR_STATUS_FLOW;
        }
        
        if ($requestType === 'delayed_brap') {
            return self::DELAYED_BRAP_STATUS_FLOW;
        } elseif ($requestType === 'delayed_regular') {
            return self::DELAYED_REGULAR_STATUS_FLOW;
        } elseif ($requestType === 'brap') {
            return self::BRAP_STATUS_FLOW;
        } elseif ($requestType === 'regular') {
            return self::REGULAR_STATUS_FLOW;
        }
        
        // Default to full sequence for backwards compatibility
        return self::STATUS_SEQUENCE;
    }

    /**
     * Check if a status transition is allowed based on request type.
     * 
     * @param string|null $from Current status
     * @param string $to Target status
     * @param string|null $requestType 'brap', 'delayed_brap', or 'regular'
     * @param string|null $serviceName Optional service name to detect Delayed BRAP
     * @return bool
     */
    public static function canTransition(?string $from, string $to, ?string $requestType = null, ?string $serviceName = null): bool
    {
        // Allow 'submitted' as initial status for all request types (it's a temporary status before inquiry)
        // This is used when creating new requests
        if ($from === null && $to === 'submitted') {
            return true;
        }

        $flow = self::statusFlow($requestType, $serviceName);
        $nextIndex = array_search($to, $flow, true);

        if ($nextIndex === false) {
            \Log::warning('Target status not found in flow', [
                'to' => $to,
                'flow' => $flow,
                'requestType' => $requestType,
                'serviceName' => $serviceName,
            ]);
            return false;
        }

        if ($from === null) {
            // The target must be the first status in the flow
            return $nextIndex === 0;
        }

        $currentIndex = array_search($from, $flow, true);

        // If current status is not in the flow, allow transitioning to any valid status in the flow
        // This handles cases where a request was incorrectly set to a status not in its flow
        if ($currentIndex === false) {
            // Allow transitioning to any status in the flow as a correction
            return $nextIndex !== false;
        }

        // Detect Delayed Regular Non-BRAP (Delayed Birth Registration - Regular) early for loop check
        $isDelayedRegularEarly = ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false) 
                               || $requestType === 'delayed_regular';
        
        // Detect Delayed BRAP early for loop check
        $isDelayedBrapEarly = ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) 
                            || $requestType === 'delayed_brap';
        
        // Allow looping for specific statuses (incomplete_requirements, for_compliance)
        // These statuses can repeat to allow officers to send multiple SMS notifications for missing requirements
        $loopableStatuses = ['incomplete_requirements', 'for_compliance'];
        $allowLoop = ($isDelayedRegularEarly || $isDelayedBrapEarly) && in_array($from, $loopableStatuses) && $from === $to;
        
        // Prevent updating to the same status (no repeats) - except for loopable statuses
        if ($from === $to && !$allowLoop) {
            return false;
        }
        
        // If it's an allowed loop, return true immediately
        if ($allowLoop) {
            \Log::info('Allowing loop transition', [
                'from' => $from,
                'to' => $to,
                'requestType' => $requestType,
                'serviceName' => $serviceName,
            ]);
            return true;
        }
        
        // Log flow information for debugging
        \Log::info('canTransition check', [
            'from' => $from,
            'to' => $to,
            'currentIndex' => $currentIndex,
            'nextIndex' => $nextIndex,
            'requestType' => $requestType,
            'serviceName' => $serviceName,
            'flow' => $flow,
            'isDelayedBrapFlow' => $flow === self::DELAYED_BRAP_STATUS_FLOW,
        ]);

        // Detect Delayed Regular Non-BRAP (Delayed Birth Registration - Regular)
        // Priority 1: Check for "Delayed Birth Registration (Regular)" - SIMPLIFIED DETECTION
        $isDelayedRegular = ($serviceName && (stripos($serviceName, 'Delayed Birth Registration (Regular)') !== false || 
                                               stripos($serviceName, 'Delayed Birth Registration(Regular)') !== false))
                         || ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'Non-BRAP') !== false)
                         || $requestType === 'delayed_regular';
        
        // Special case for Delayed Regular Non-BRAP status transitions
        if ($isDelayedRegular) {
            // From inquiry -> waiting_for_psa_negative_certificate
            if ($from === 'inquiry' && $to === 'waiting_for_psa_negative_certificate') {
                return true;
            }
            
            // From waiting_for_psa_negative_certificate -> positive_result OR incomplete_requirements OR accepted OR accepted_octogenarian
            if ($from === 'waiting_for_psa_negative_certificate') {
                if (in_array($to, ['positive_result', 'incomplete_requirements', 'accepted', 'accepted_octogenarian'])) {
                    return true;
                }
            }
            
            // From positive_result -> registered (auto-posting starts, after 10 days -> registered)
            if ($from === 'positive_result' && $to === 'registered') {
                return true;
            }
            
            // From incomplete_requirements -> can loop (incomplete_requirements) OR go to accepted OR accepted_octogenarian
            if ($from === 'incomplete_requirements') {
                if (in_array($to, ['incomplete_requirements', 'accepted', 'accepted_octogenarian'])) {
                    return true;
                }
            }
            
            // From accepted -> registered (after 10 days posting, automatic posting starts on acceptance)
            if ($from === 'accepted' && $to === 'registered') {
                return true;
            }
            
            // From accepted_octogenarian -> verified_with_problem OR accepted (80+ can go directly to posting)
            if ($from === 'accepted_octogenarian') {
                if (in_array($to, ['verified_with_problem', 'accepted'])) {
                    return true;
                }
            }
            
            // From verified_with_problem -> for_compliance OR accepted
            if ($from === 'verified_with_problem') {
                if (in_array($to, ['for_compliance', 'accepted'])) {
                    return true;
                }
            }
            
            // From for_compliance -> can loop (for_compliance) OR go to accepted
            if ($from === 'for_compliance') {
                if (in_array($to, ['for_compliance', 'accepted'])) {
                    return true;
                }
            }
        }
        
        // Special case for Delayed BRAP status transitions
        $isDelayedBrap = ($serviceName && stripos($serviceName, 'Delayed') !== false && stripos($serviceName, 'BRAP') !== false && stripos($serviceName, 'Non-BRAP') === false) 
                      || $requestType === 'delayed_brap';
        
        if ($isDelayedBrap) {
            // From submitted -> inquiry (initial transition)
            if ($from === 'submitted' && $to === 'inquiry') {
                return true;
            }
            
            // From inquiry -> to_be_encoded_brap (only option)
            if ($from === 'inquiry' && $to === 'to_be_encoded_brap') {
                return true;
            }
            
            // From to_be_encoded_brap -> positive_result OR negative_result
            if ($from === 'to_be_encoded_brap') {
                if (in_array($to, ['positive_result', 'negative_result'])) {
                    return true;
                }
            }
            
            // From positive_result -> registered (auto-posting starts, after 10 days -> registered)
            if ($from === 'positive_result' && $to === 'registered') {
                return true;
            }
            
            // From negative_result -> incomplete_requirements OR accepted OR accepted_octogenarian
            if ($from === 'negative_result') {
                if (in_array($to, ['incomplete_requirements', 'accepted', 'accepted_octogenarian'])) {
                    return true;
                }
            }
            
            // From incomplete_requirements -> can loop OR accepted OR accepted_octogenarian
            if ($from === 'incomplete_requirements') {
                if (in_array($to, ['incomplete_requirements', 'accepted', 'accepted_octogenarian'])) {
                    return true;
                }
            }
            
            // From accepted -> registered (after 10 days posting, automatic posting starts on acceptance)
            if ($from === 'accepted' && $to === 'registered') {
                return true;
            }
            
            // From accepted_octogenarian -> verified_with_problem OR accepted (80+ can go directly to posting)
            if ($from === 'accepted_octogenarian') {
                if (in_array($to, ['verified_with_problem', 'accepted'])) {
                    return true;
                }
            }
            
            // From verified_with_problem -> for_compliance OR accepted
            if ($from === 'verified_with_problem') {
                if (in_array($to, ['for_compliance', 'accepted'])) {
                    return true;
                }
            }
            
            // From for_compliance -> can loop OR accepted
            if ($from === 'for_compliance') {
                if (in_array($to, ['for_compliance', 'accepted'])) {
                    return true;
                }
            }
        }

        // Special case: Allow skipping "incomplete_requirements" when going from "checklist_given" to "accepted"
        // This allows officers to mark requests as accepted directly if requirements are complete
        if ($from === 'checklist_given' && $to === 'accepted') {
            $checklistIndex = array_search('checklist_given', $flow, true);
            $incompleteIndex = array_search('incomplete_requirements', $flow, true);
            $acceptedIndex = array_search('accepted', $flow, true);
            
            // Allow if accepted comes after checklist_given (skipping incomplete_requirements)
            if ($checklistIndex !== false && $acceptedIndex !== false && $acceptedIndex > $checklistIndex) {
                return true;
            }
        }

        // Default validation: only allow same status or moving forward by 1 step
        // This ensures statuses follow proper order and cannot skip steps
        // For Delayed BRAP, special cases above should have handled the transitions
        // But we still apply default validation as fallback
        $isValidTransition = $nextIndex >= $currentIndex && $nextIndex <= $currentIndex + 1;
        
        // Log for debugging if transition fails
        if (!$isValidTransition && $from === 'checklist_given' && ($to === 'negative_result' || $to === 'positive_result')) {
            \Log::warning('Default validation failed for Delayed BRAP transition', [
                'from' => $from,
                'to' => $to,
                'currentIndex' => $currentIndex,
                'nextIndex' => $nextIndex,
                'isDelayedBrap' => $isDelayedBrap,
                'serviceName' => $serviceName,
                'requestType' => $requestType,
                'flow' => $flow,
            ]);
        }
        
        return $isValidTransition;
    }

    /**
     * Get the latest appointment for this civil service
     */
    public function latestAppointment()
    {
        return $this->hasOne(Appointment::class)->latest();
    }

    /**
     * Check if this civil service has an appointment
     */
    public function hasAppointment()
    {
        return $this->appointments()->exists();
    }

    /**
     * Check if this civil service has pending documents
     */
    public function hasPendingDocuments()
    {
        return $this->documentUploads()->pending()->exists();
    }

    /**
     * Get tracking data for the tracker page
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
            'document_type' => $this->service_type, // For compatibility
            'tracking_number' => $this->tracking_number,
            'queue_number' => \App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false) ? $this->queue_number : null,
            'status' => $this->status,
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('H:i:s'),
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'print_status' => $this->print_status,
            'request_type' => $this->request_type,
            'child_info' => $this->child_info,
            'mother_info' => $this->mother_info,
            'father_info' => $this->father_info,
            'contact_info' => $this->contact_info,
            'requirements_checklist' => $this->requirements_checklist,
            'remarks' => $latestStatusUpdate?->remarks ?? $this->admin_notes,
            'status_date' => $latestStatusUpdate?->status_date?->format('Y-m-d'),
            'status_updates' => $this->statusUpdates()->get()->map(function ($update) {
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


