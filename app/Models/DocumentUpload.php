<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'civil_service_id',
        'requirement_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'rejection_reason',
        'remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the civil service that owns the document
     */
    public function civilService(): BelongsTo
    {
        return $this->belongsTo(CivilService::class);
    }

    /**
     * Get the appointment associated with this document
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'civil_service_id', 'civil_service_id');
    }

    /**
     * Scope for documents by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for documents by requirement
     */
    public function scopeByRequirement($query, $requirement)
    {
        return $query->where('requirement_name', $requirement);
    }

    /**
     * Scope for pending documents
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved documents
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected documents
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if document is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if document is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if document is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Approve the document
     */
    public function approve($reviewedBy)
    {
        $this->status = 'approved';
        $this->reviewed_by = $reviewedBy;
        $this->reviewed_at = now();
        $this->rejection_reason = null; // Clear any previous rejection reason
        $this->save();
    }

    /**
     * Reject the document
     */
    public function reject($reason, $reviewedBy)
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->reviewed_by = $reviewedBy;
        $this->reviewed_at = now();
        $this->save();
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file URL for download/viewing
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf()
    {
        return strtolower($this->file_extension) === 'pdf';
    }
}