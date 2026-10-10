<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('assets/images/placeholder-about.jpg');
    }
}








