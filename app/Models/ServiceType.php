<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'icon_path',
        'is_active',
        'display_order',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function steps()
    {
        return $this->hasMany(ServiceStep::class);
    }
}







