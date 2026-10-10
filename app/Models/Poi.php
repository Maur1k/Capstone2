<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Poi extends Model
{
    protected $fillable = ['floor_id', 'name', 'category', 'x', 'y', 'meta'];
    
    protected $casts = ['meta' => 'array'];
    
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Automatically clear wayfinding caches whenever POIs change.
     * This keeps the kiosk destination list in sync without needing artisan commands.
     */
    protected static function booted()
    {
        $clear = function (Poi $poi) {
            Cache::forget('floors_with_pois');
            if ($poi->floor_id) {
                Cache::forget("floor_{$poi->floor_id}_with_pois");
            }
        };

        static::saved($clear);
        static::deleted($clear);
    }
}
