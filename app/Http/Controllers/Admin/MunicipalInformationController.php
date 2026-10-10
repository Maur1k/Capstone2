<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MunicipalInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MunicipalInformationController extends Controller
{
    public function edit()
    {
        $municipalInfo = MunicipalInformation::first();
        if (!$municipalInfo) {
            $municipalInfo = MunicipalInformation::create([
                'city_name' => 'Alaminos City',
                'tagline' => 'HOME OF THE Hundred Islands National Park',
                'mayor_name' => 'ARTH BRYAN G. CELESTE',
                'mayor_title' => 'CITY MAYOR',
                'email' => 'cso.alaminos@yahoo.com',
                'phone' => '(075) 201-2345'
            ]);
        }
        return view('admin.municipal-information.edit', compact('municipalInfo'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_name' => 'required|string|max:255',
            'tagline' => 'required|string|max:255',
            'mayor_name' => 'required|string|max:255',
            'mayor_title' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'announcements' => 'nullable|string',
            'mayor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'city_seal' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'iso_certification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $municipalInfo = MunicipalInformation::first();
        if (!$municipalInfo) {
            $municipalInfo = MunicipalInformation::create([
                'city_name' => $request->city_name,
                'tagline' => $request->tagline,
                'mayor_name' => $request->mayor_name,
                'mayor_title' => $request->mayor_title,
                'email' => $request->email,
                'phone' => $request->phone
            ]);
        }

        $data = [
            'city_name' => $request->city_name,
            'tagline' => $request->tagline,
            'mayor_name' => $request->mayor_name,
            'mayor_title' => $request->mayor_title,
            'email' => $request->email,
            'phone' => $request->phone,
            'announcements' => $request->announcements,
        ];

        // Handle image uploads
        $imageFields = ['mayor_image', 'banner_image', 'city_seal', 'iso_certification'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if exists
                if ($municipalInfo->$field && file_exists(public_path($municipalInfo->$field))) {
                    unlink(public_path($municipalInfo->$field));
                }

                $image = $request->file($field);
                $directory = 'assets/images/municipal';
                $publicPath = public_path($directory);
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $directory . '/' . $filename;

                $image->move($publicPath, $filename);
                $data[$field] = $path;
            }
        }

        $municipalInfo->update($data);

        return redirect()->route('admin.municipal-information.edit')
            ->with('success', 'Municipal information updated successfully!');
    }
}
