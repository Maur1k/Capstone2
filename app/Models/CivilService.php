<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CivilService extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'queue_number',
        'service_type',
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
        'admin_notes',
        'print_status',
    ];

    protected $casts = [
        'child_info' => 'array',
        'mother_info' => 'array',
        'father_info' => 'array',
        'contact_info' => 'array',
        'processed_at' => 'datetime',
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
     */
    public function scopeByTracking($query, $trackingNumber)
    {
        return $query->where('tracking_number', $trackingNumber);
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
            $prefix = 'CR-' . now()->format('Ymd');
            $suffix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
            $trackingNumber = $prefix . '-' . $suffix;
        } while (self::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    /**
     * Update status with timestamp
     */
    public function updateStatus($status, $processedBy = null, $notes = null)
    {
        $this->status = $status;
        $this->processed_by = $processedBy;
        $this->processed_at = now();
        if ($notes) {
            $this->admin_notes = $notes;
        }
        $this->save();
    }

    /**
     * Get tracking data for the tracker page
     */
    public function getTrackingData()
    {
        return [
            'full_name' => $this->full_name,
            'document_type' => $this->service_type,
            'tracking_number' => $this->tracking_number,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('H:i:s'),
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'print_status' => $this->print_status,
            'child_info' => $this->child_info,
            'mother_info' => $this->mother_info,
            'father_info' => $this->father_info,
            'contact_info' => $this->contact_info,
        ];
    }
}

