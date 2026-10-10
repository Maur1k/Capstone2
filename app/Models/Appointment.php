<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'civil_service_id',
        'service_type',
        'full_name',
        'contact_number',
        'email',
        'address',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'assigned_officer',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        // Note: appointment_time is stored as a string (e.g., "09:00:00")
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the civil service that owns the appointment
     */
    public function civilService(): BelongsTo
    {
        return $this->belongsTo(CivilService::class);
    }

    /**
     * Get the document uploads for this appointment (via civil service)
     */
    public function documentUploads()
    {
        return $this->hasMany(\App\Models\DocumentUpload::class, 'civil_service_id', 'civil_service_id');
    }

    /**
     * Generate a unique appointment number
     */
    public static function generateAppointmentNumber()
    {
        do {
            $prefix = 'APT-' . now()->format('Ymd');
            $suffix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
            $appointmentNumber = $prefix . '-' . $suffix;
        } while (self::where('appointment_number', $appointmentNumber)->exists());

        return $appointmentNumber;
    }

    /**
     * Scope for appointments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for appointments by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    /**
     * Scope for appointments by officer
     */
    public function scopeByOfficer($query, $officer)
    {
        return $query->where('assigned_officer', $officer);
    }

    /**
     * Scope for tracking appointments by appointment number
     */
    public function scopeByTracking($query, $trackingNumber)
    {
        // Normalize appointment number (handle both formats: APT202511253258 and APT-20251125-XXXXXX)
        // Format in DB: APTYYYYMMDDXXXX (e.g., APT202511253258)
        // Format with dashes: APT-YYYYMMDD-XXXXXX
        
        $numbersToTry = [$trackingNumber];
        
        // If format is APTYYYYMMDDXXXX (no dashes), also try with dashes
        if (preg_match('/^APT(\d{8})(\d{4,6})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'APT-' . $matches[1] . '-' . $matches[2];
        }
        
        // If format is APT-YYYYMMDD-XXXXXX (with dashes), also try without dashes
        if (preg_match('/^APT-(\d{8})-(\d{4,6})$/', $trackingNumber, $matches)) {
            $numbersToTry[] = 'APT' . $matches[1] . $matches[2];
        }
        
        // Remove duplicates
        $numbersToTry = array_unique($numbersToTry);
        
        // Try all possible formats
        return $query->whereIn('appointment_number', $numbersToTry);
    }

    /**
     * Scope for upcoming appointments
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Check if appointment is confirmed
     */
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if appointment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if appointment is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Confirm the appointment
     */
    public function confirm($officer = null)
    {
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        if ($officer) {
            $this->assigned_officer = $officer;
        }
        $this->save();
    }

    /**
     * Complete the appointment
     */
    public function complete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Cancel the appointment
     */
    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->cancellation_reason = $reason;
        }
        $this->save();
    }

    /**
     * Get formatted appointment date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        $date = $this->appointment_date ? $this->appointment_date->format('M d, Y') : 'N/A';
        $time = $this->appointment_time ?? 'N/A';
        
        // Format time if it's in HH:MM format
        if ($time && strpos($time, ':') !== false) {
            $timeParts = explode(':', $time);
            $hours = (int)$timeParts[0];
            $minutes = $timeParts[1] ?? '00';
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $displayHour = $hours % 12 ?: 12;
            $time = "{$displayHour}:{$minutes} {$ampm}";
        }
        
        return "{$date} at {$time}";
    }

    /**
     * Get tracking data for the appointment
     */
    public function getTrackingData()
    {
        // Format time for display
        $timeFormatted = $this->appointment_time;
        if ($this->appointment_time && strpos($this->appointment_time, ':') !== false) {
            $timeParts = explode(':', $this->appointment_time);
            $hours = (int)$timeParts[0];
            $minutes = $timeParts[1] ?? '00';
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $displayHour = $hours % 12 ?: 12;
            $timeFormatted = "{$displayHour}:{$minutes} {$ampm}";
        }

        return [
            'tracking_number' => $this->appointment_number,
            'full_name' => $this->full_name,
            'service_name' => $this->service_type,
            'service_type' => 'Appointment',
            'status' => $this->status,
            // Use created_at for submission date/time (when appointment was booked)
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('H:i:s'),
            'appointment_date' => $this->appointment_date ? $this->appointment_date->format('M d, Y') : null,
            'appointment_time' => $timeFormatted,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'assigned_officer' => $this->assigned_officer,
            'notes' => $this->notes,
            'confirmed_at' => $this->confirmed_at ? $this->confirmed_at->format('Y-m-d H:i:s') : null,
            'completed_at' => $this->completed_at ? $this->completed_at->format('Y-m-d H:i:s') : null,
            'cancelled_at' => $this->cancelled_at ? $this->cancelled_at->format('Y-m-d H:i:s') : null,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}