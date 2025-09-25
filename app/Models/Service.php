<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'icon_path',
        'is_active',
    ];

    public function types()
    {
        return $this->hasMany(ServiceType::class);
    }
}




