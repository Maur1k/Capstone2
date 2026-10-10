<?php

use Illuminate\Support\Facades\Route;

// Home or landing page -> kiosk
Route::get('/', function () {
    return view('home.kiosk');
});

// 3D Map Kiosk (main route)
Route::get('/3dmap', function () {
    return view('kiosk.3dmap');
})->name('3dmap');

// Kiosk route (redirects to 3D Map)
Route::get('/kiosk', function () {
    return redirect('/3dmap');
});

