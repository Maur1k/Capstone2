<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'statusable_id',
        'statusable_type',
        'status',
        'status_date',
        'remarks',
        'admin_id',
    ];

    protected $casts = [
        'status_date' => 'date',
    ];

    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}

