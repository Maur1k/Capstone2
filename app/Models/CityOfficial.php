<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityOfficial extends Model
{
    protected $fillable = [
        'name',
        'position',
        'department',
        'image',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw('CASE WHEN department IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('department', 'asc')
                    ->orderBy('display_order', 'asc');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('assets/images/placeholder-official.jpg');
    }
}
