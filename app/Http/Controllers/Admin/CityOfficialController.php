<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityOfficial;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityOfficialController extends Controller
{
    public function index()
    {
        $officials = CityOfficial::ordered()->get();
        $officialsByDepartment = $officials->groupBy('department');
        $departments = Service::where('is_active', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();
        
        return view('admin.city-officials.index', compact('officials', 'officialsByDepartment', 'departments'));
    }

    public function create()
    {
        $departments = Service::where('is_active', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();
        
        return view('admin.city-officials.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'display_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = 'assets/images/officials';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);

            CityOfficial::create([
                'name' => $request->name,
                'position' => $request->position,
                'department' => $request->department,
                'image' => $path,
                'display_order' => $request->display_order ?: CityOfficial::max('display_order') + 1,
                'is_active' => true
            ]);

            return redirect()->route('admin.city-officials.index')
                ->with('success', 'City official added successfully!');
        }

        return redirect()->back()->with('error', 'No image file provided.');
    }

    public function edit($id)
    {
        $official = CityOfficial::findOrFail($id);
        $departments = Service::where('is_active', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();
        
        return view('admin.city-officials.edit', compact('official', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $official = CityOfficial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'position' => $request->position,
            'department' => $request->department,
            'display_order' => $request->display_order ?: $official->display_order,
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('image')) {
            if (file_exists(public_path($official->image))) {
                unlink(public_path($official->image));
            }

            $image = $request->file('image');
            $directory = 'assets/images/officials';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['image'] = $path;
        }

        $official->update($data);

        return redirect()->route('admin.city-officials.index')
            ->with('success', 'City official updated successfully!');
    }

    public function destroy($id)
    {
        $official = CityOfficial::findOrFail($id);

        if (file_exists(public_path($official->image))) {
            unlink(public_path($official->image));
        }

        $official->delete();

        return redirect()->route('admin.city-officials.index')
            ->with('success', 'City official deleted successfully!');
    }
}
