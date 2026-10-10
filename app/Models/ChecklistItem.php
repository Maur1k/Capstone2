<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklistable_id',
        'checklistable_type',
        'items',
        'remarks',
        'admin_id',
        'sent_at',
        'sms_status',
        'sms_message',
        'phone_number',
    ];

    protected $casts = [
        'items' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the parent checklistable model (CivilService or OtherService).
     */
    public function checklistable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the admin who created this checklist.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Format items for SMS display
     */
    public function formatItemsForSMS(): string
    {
        if (empty($this->items) || !is_array($this->items)) {
            return '';
        }

        return implode(', ', $this->items);
    }

    /**
     * Generate SMS message
     */
    public function generateSMSMessage($trackingNumber, $fullName = null): string
    {
        $greeting = $fullName ? "Hello {$fullName}," : "Hello,";
        $itemsList = $this->formatItemsForSMS();
        $remarks = $this->remarks ? " Remarks: {$this->remarks}." : '';
        
        return "CLICK2SERVE: {$greeting} Your inquiry #{$trackingNumber} requires the following documents: {$itemsList}.{$remarks}";
    }
}
