<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunicipalInformation extends Model
{
    protected $fillable = [
        'city_name',
        'tagline',
        'mayor_name',
        'mayor_title',
        'mayor_image',
        'banner_image',
        'city_seal',
        'iso_certification',
        'email',
        'phone',
        'announcements',
        'announcements_image'
    ];

    public function getMayorImageUrlAttribute()
    {
        if ($this->mayor_image && file_exists(public_path($this->mayor_image))) {
            return asset($this->mayor_image);
        }
        return asset('assets/images/placeholder-mayor.jpg');
    }

    public function getBannerImageUrlAttribute()
    {
        if ($this->banner_image && file_exists(public_path($this->banner_image))) {
            return asset($this->banner_image);
        }
        return asset('assets/images/hundred-islands-banner.jpg');
    }

    public function getCitySealUrlAttribute()
    {
        if ($this->city_seal && file_exists(public_path($this->city_seal))) {
            return asset($this->city_seal);
        }
        return asset('assets/images/city-seal.png');
    }

}
