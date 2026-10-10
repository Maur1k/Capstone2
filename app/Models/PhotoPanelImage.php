<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoPanelImage extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'alt_text',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Scope to get only active images
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute()
    {
        // Check if it's an assets path (new structure)
        if (str_starts_with($this->image_path, 'assets/')) {
            if (file_exists(public_path($this->image_path))) {
                return asset($this->image_path);
            }
        }

        // Check if it's a storage path (old structure)
        if (str_starts_with($this->image_path, 'photo-panel/')) {
            $fullPath = 'public/' . $this->image_path;
            if (Storage::exists($fullPath)) {
                return asset('storage/' . $this->image_path);
            }
        }

        // Fallback to existing slide images
        $fallbackImages = ['slide1.jpg', 'slide2.jpg', 'slide3.jpg'];
        $index = ($this->id - 1) % 3;
        return asset('assets/images/' . $fallbackImages[$index]);
    }
}
