<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function editMunicipal()
    {
        $name = Setting::get('municipal.name', 'Alaminos City');
        $tagline = Setting::get('municipal.tagline', 'Home of the Hundred Islands National Park');
        return view('admin.settings.municipal', compact('name', 'tagline'));
    }

    public function updateMunicipal(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('municipal.name', $data['name']);
        Setting::set('municipal.tagline', $data['tagline'] ?? '');

        return back()->with('status', 'Municipal information updated.');
    }

    public function editHero()
    {
        $hero = Setting::get('dashboard.hero', '/assets/images/slide1.jpg');
        return view('admin.settings.hero', compact('hero'));
    }

    public function updateHero(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('image')->store('public/hero');
        $publicPath = str_replace('public', '/storage', $path);
        Setting::set('dashboard.hero', $publicPath);

        return back()->with('status', 'Dashboard photo updated.');
    }
}




