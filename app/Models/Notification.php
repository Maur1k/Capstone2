<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'type',
        'title',
        'message',
        'data',
        'tracking_number',
        'request_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Create a ready for approval notification
     */
    public static function createReadyForApproval($trackingNumber, $requestType, $fullName)
    {
        return self::create([
            'type' => 'ready_for_approval',
            'title' => 'Request Ready for Approval',
            'message' => "Request {$trackingNumber} for {$fullName} is ready for approval after 10 business days.",
            'tracking_number' => $trackingNumber,
            'request_type' => $requestType,
        ]);
    }
}
