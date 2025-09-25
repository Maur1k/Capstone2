<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherService extends Model
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
        'purpose',
        'additional_info',
        'processed_by',
        'processed_at',
        'admin_notes',
        'print_status',
    ];

    protected $casts = [
        'additional_info' => 'array',
        'processed_at' => 'datetime',
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
            $prefix = 'OS-' . now()->format('Ymd');
            $suffix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
            $trackingNumber = $prefix . '-' . $suffix;
        } while (self::where('tracking_number', $trackingNumber)->exists());
        return $trackingNumber;
    }
}


