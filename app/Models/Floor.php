<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Floor extends Model
{
    protected $fillable = [
        'building',
        'name',
        'image_path',
        'width',
        'height',
        'glb_path',
        'stairs_model_x',
        'stairs_model_y',
        'stairs_model_z',
        'use_stairs_as_start',
        'show_you_are_here',
        'you_are_here_model_x',
        'you_are_here_model_y',
        'you_are_here_model_z',
    ];

    public function pois()
    {
        return $this->hasMany(Poi::class);
    }

    /**
     * Automatically clear wayfinding caches whenever floors change.
     */
    protected static function booted()
    {
        $clear = function (Floor $floor) {
            Cache::forget('floors_with_pois');
            if ($floor->id) {
                Cache::forget("floor_{$floor->id}_with_pois");
            }
        };

        static::saved($clear);
        static::deleted($clear);
    }
}
