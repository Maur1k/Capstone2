<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'author',
        'published_at',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('assets/images/placeholder-blog.jpg');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }
}








