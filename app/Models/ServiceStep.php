<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'title',
        'content',
        'display_order',
    ];

    public function type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}







