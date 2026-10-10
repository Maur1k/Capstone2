<?php

namespace App\Http\Controllers;

use App\Models\MunicipalInformation;
use App\Models\CityOfficial;
use App\Models\TouristSpot;
use Illuminate\Http\Request;

class MunicipalInformationController extends Controller
{
    public function index()
    {
        $municipalInfo = MunicipalInformation::first();
        $cityOfficials = CityOfficial::active()->ordered()->get();
        $officialsByDepartment = $cityOfficials->groupBy('department');
        $touristSpots = TouristSpot::active()->ordered()->get();

        return view('municipal-information', compact('municipalInfo', 'cityOfficials', 'officialsByDepartment', 'touristSpots'));
    }
}
