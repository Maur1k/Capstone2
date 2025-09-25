<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'activity_type',
        'description',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function getTargetAttribute()
    {
        if (!$this->target_type || !$this->target_id) {
            return null;
        }

        return $this->target_type::find($this->target_id);
    }

    public function getActivityIconAttribute(): string
    {
        return match ($this->activity_type) {
            'login' => '🔑',
            'logout' => '🚪',
            'status_change' => '🔄',
            'request_viewed' => '👁️',
            'request_created' => '➕',
            'request_updated' => '✏️',
            default => '📝',
        };
    }

    public function getActivityColorAttribute(): string
    {
        return match ($this->activity_type) {
            'login' => 'text-green-600',
            'logout' => 'text-gray-600',
            'status_change' => 'text-blue-600',
            'request_viewed' => 'text-purple-600',
            'request_created' => 'text-green-600',
            'request_updated' => 'text-yellow-600',
            default => 'text-gray-600',
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}