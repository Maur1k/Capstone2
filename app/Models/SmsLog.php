<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'request_type',
        'request_id',
        'phone_number',
        'message',
        'status',
        'status_type',
        'message_id',
        'network',
        'sender',
        'error',
        'sent_at',
        'response_data',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response_data' => 'array',
    ];

    /**
     * Scope for successful SMS
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed SMS
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for a specific tracking number
     */
    public function scopeForTracking($query, $trackingNumber)
    {
        return $query->where('tracking_number', $trackingNumber);
    }

    /**
     * Get formatted status
     */
    public function getStatusFormattedAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get formatted status type
     */
    public function getStatusTypeFormattedAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status_type));
    }
}

