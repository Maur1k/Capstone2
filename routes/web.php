<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Setup\FirstRunController as AdminFirstRunController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Home or landing page -> kiosk
Route::get('/', function () {
    return view('home.kiosk');
});

// Services pages
Route::get('/services', fn() => view('services.index'));
Route::get('/services/civil', fn() => view('services.civil.index'));
Route::get('/services/civil/colb/on-time/process', fn() => view('services.civil.colb.on_time_process'));
Route::get('/services/civil/colb/on-time/check', fn() => view('services.civil.colb.on_time_check'));
Route::get('/services/civil/colb/on-time', fn() => view('services.civil.colb.on_time_tabbed'));
Route::get('/services/civil/colb/on-time/step2', fn() => view('services.civil.colb.on_time_step2'));
Route::get('/services/civil/colb/delayed', fn() => view('services.civil.colb.delayed_tabbed'));
Route::get('/services/civil/colb/delayed/step2', fn() => view('services.civil.colb.delayed_step2'));
Route::get('/services/civil/colb/delayed/process', fn() => view('services.civil.colb.delayed_process'));
Route::get('/services/civil/colb/delayed/check', fn() => view('services.civil.colb.delayed_check'));
Route::get('/services/civil/com/on-time/process', fn() => view('services.civil.com.on_time_process'));
Route::get('/services/civil/com/on-time/check', fn() => view('services.civil.com.on_time_check'));
Route::get('/services/civil/com/on-time', fn() => view('services.civil.com.on_time'));
Route::get('/services/civil/com/on-time/step2', fn() => view('services.civil.com.on_time_step2'));
Route::get('/services/civil/com/delayed/process', fn() => view('services.civil.com.delayed_process'));
Route::get('/services/civil/com/delayed/check', fn() => view('services.civil.com.delayed_check'));
Route::get('/services/civil/com/delayed', fn() => view('services.civil.com.delayed'));
Route::get('/services/civil/com/delayed/step2', fn() => view('services.civil.com.delayed_step2'));
Route::get('/services/other/bplo', fn() => view('services.other.bplo.index'));
Route::get('/services/other/bplo/application/process', fn() => view('services.other.bplo.application_process'));
Route::get('/services/other/bplo/application/check', fn() => view('services.other.bplo.application_check'));
Route::get('/services/other/bplo/application/request', fn() => view('services.other.bplo.application_request'));
Route::get('/services/other/bplo/renewal/process', fn() => view('services.other.bplo.renewal_process'));
Route::get('/services/other/bplo/renewal/check', fn() => view('services.other.bplo.renewal_check'));
Route::get('/services/other/bplo/renewal/request', fn() => view('services.other.bplo.renewal_request'));

// CTRU Services
Route::get('/services/other/ctru', fn() => view('services.other.ctru.index'));
Route::get('/services/other/ctru/mtop/process', fn() => view('services.other.ctru.mtop_process'));
Route::get('/services/other/ctru/mtop/check', fn() => view('services.other.ctru.mtop_check'));
Route::get('/services/other/ctru/mtop/request', fn() => view('services.other.ctru.mtop_request'));
Route::get('/services/other/ctru/boat/process', fn() => view('services.other.ctru.boat_process'));
Route::get('/services/other/ctru/boat/check', fn() => view('services.other.ctru.boat_check'));
Route::get('/services/other/ctru/boat/request', fn() => view('services.other.ctru.boat_request'));

// Engineering Office Services
Route::get('/services/other/engineering', fn() => view('services.other.engineering.index'));
Route::get('/services/other/engineering/locational/process', fn() => view('services.other.engineering.locational_process'));
Route::get('/services/other/engineering/locational/check', fn() => view('services.other.engineering.locational_check'));
Route::get('/services/other/engineering/locational/request', fn() => view('services.other.engineering.locational_request'));
Route::get('/services/other/engineering/building/process', fn() => view('services.other.engineering.building_process'));
Route::get('/services/other/engineering/building/check', fn() => view('services.other.engineering.building_check'));
Route::get('/services/other/engineering/building/request', fn() => view('services.other.engineering.building_request'));

// Social Welfare and Development Office Services
Route::get('/services/other/social-welfare', fn() => view('services.other.social-welfare.index'));
Route::get('/services/other/social-welfare/aics/process', fn() => view('services.other.social-welfare.aics_process'));
Route::get('/services/other/social-welfare/aics/check', fn() => view('services.other.social-welfare.aics_check'));
Route::get('/services/other/social-welfare/aics/request', fn() => view('services.other.social-welfare.aics_request'));
Route::get('/services/other/social-welfare/child-dev/process', fn() => view('services.other.social-welfare.child_dev_process'));
Route::get('/services/other/social-welfare/child-dev/check', fn() => view('services.other.social-welfare.child_dev_check'));
Route::get('/services/other/social-welfare/child-dev/request', fn() => view('services.other.social-welfare.child_dev_request'));

// City Veterinary Office Services
Route::get('/services/other/veterinary', fn() => view('services.other.veterinary.index'));
Route::get('/services/other/veterinary/pet-registration/process', fn() => view('services.other.veterinary.pet_registration_process'));
Route::get('/services/other/veterinary/pet-registration/check', fn() => view('services.other.veterinary.pet_registration_check'));
Route::get('/services/other/veterinary/pet-registration/request', fn() => view('services.other.veterinary.pet_registration_request'));
Route::get('/services/other/veterinary/anti-rabies/process', fn() => view('services.other.veterinary.anti_rabies_process'));
Route::get('/services/other/veterinary/anti-rabies/check', fn() => view('services.other.veterinary.anti_rabies_check'));
Route::get('/services/other/veterinary/anti-rabies/request', fn() => view('services.other.veterinary.anti_rabies_request'));

Route::get('/services/tracker', fn() => view('services.tracker.index'));

// Chatbot UI and message endpoint
Route::get('/chatbot', [ChatbotController::class, 'view']);
Route::post('/chatbot/message', [ChatbotController::class, 'message']);

// Civil service request endpoints
Route::post('/requests', [RequestController::class, 'store']);
Route::post('/requests/detailed', [RequestController::class, 'storeDetailed']);
Route::get('/requests/{trackingNumber}', [RequestController::class, 'track']);
Route::get('/requests/{trackingNumber}/status', [RequestController::class, 'getStatus']);
Route::put('/requests/{trackingNumber}/status', [RequestController::class, 'updateStatus']);
Route::post('/requests/print', [RequestController::class, 'printReceiptByTracking']);

// Print service diagnostics
Route::get('/print-service/test', [RequestController::class, 'testPrint']);
Route::get('/print-service/health', [RequestController::class, 'printServiceHealth']);

// API endpoints used by the frontend JS
Route::prefix('api')->group(function () {
    Route::post('/request', [RequestController::class, 'store']);
    Route::get('/request/track/{trackingNumber}', [RequestController::class, 'track']);
    Route::get('/request/status/{trackingNumber}', [RequestController::class, 'getStatus']);
    Route::post('/request/print-receipt', [RequestController::class, 'printReceiptByTracking']);
    Route::put('/request/{trackingNumber}/status', [RequestController::class, 'updateStatus']);
    // Other services
    Route::post('/other-services', [\App\Http\Controllers\OtherServiceController::class, 'store']);
});


// Admin portal
Route::prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin to dashboard or login as appropriate
    Route::get('/', function () {
        return Auth::guard('admin')->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    })->name('index');

    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:admin-login')->name('login.attempt');

        // First-run setup when no admins exist
        Route::get('/setup', [AdminFirstRunController::class, 'show'])->name('setup.show');
        Route::post('/setup', [AdminFirstRunController::class, 'store'])->name('setup.store');
    });

    // Authenticated routes (Super Admin only)
    Route::middleware(['auth:admin', 'superadmin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Officers (Super Admin only)
        Route::middleware('superadmin')->group(function () {
            Route::get('/officers', [\App\Http\Controllers\Admin\OfficerController::class, 'index'])->name('officers.index');
            Route::get('/officers/create', [\App\Http\Controllers\Admin\OfficerController::class, 'create'])->name('officers.create');
            Route::post('/officers', [\App\Http\Controllers\Admin\OfficerController::class, 'store'])->name('officers.store');
            Route::get('/officers/{officer}/edit', [\App\Http\Controllers\Admin\OfficerController::class, 'edit'])->name('officers.edit');
            Route::put('/officers/{officer}', [\App\Http\Controllers\Admin\OfficerController::class, 'update'])->name('officers.update');
            Route::delete('/officers/{officer}', [\App\Http\Controllers\Admin\OfficerController::class, 'destroy'])->name('officers.destroy');
            Route::put('/officers/{officer}/toggle', [\App\Http\Controllers\Admin\OfficerController::class, 'toggle'])->name('officers.toggle');

            // Activity Logs
            Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');

            // Services CRUD
            Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
            Route::get('/services/create', [\App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
            Route::post('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
            Route::get('/services/{service}/edit', [\App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
            Route::put('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
            Route::delete('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');

            // Types
            Route::get('/services/{service}/types', [\App\Http\Controllers\Admin\ServiceController::class, 'types'])->name('services.types');
            Route::post('/services/{service}/types', [\App\Http\Controllers\Admin\ServiceController::class, 'typeStore'])->name('services.types.store');
            Route::put('/services/{service}/types/{type}', [\App\Http\Controllers\Admin\ServiceController::class, 'typeUpdate'])->name('services.types.update');
            Route::delete('/services/{service}/types/{type}', [\App\Http\Controllers\Admin\ServiceController::class, 'typeDestroy'])->name('services.types.destroy');

            // Steps
            Route::get('/services/{service}/types/{type}/steps', [\App\Http\Controllers\Admin\ServiceController::class, 'steps'])->name('services.steps');
            Route::post('/services/{service}/types/{type}/steps', [\App\Http\Controllers\Admin\ServiceController::class, 'stepStore'])->name('services.steps.store');
            Route::put('/services/{service}/types/{type}/steps/{step}', [\App\Http\Controllers\Admin\ServiceController::class, 'stepUpdate'])->name('services.steps.update');
            Route::delete('/services/{service}/types/{type}/steps/{step}', [\App\Http\Controllers\Admin\ServiceController::class, 'stepDestroy'])->name('services.steps.destroy');
        });
    });
});

// Placeholder admin utilities for quick actions
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/services/manage', function () {
        return redirect()->route('admin.services.index');
    })->middleware('superadmin')->name('services.manage');

    Route::get('/dashboard/edit', function () {
        return view('admin.dashboard.edit');
    })->middleware('superadmin')->name('dashboard.edit');

    // Settings
    Route::middleware('superadmin')->group(function () {
        Route::get('/settings/municipal', [\App\Http\Controllers\Admin\SettingsController::class, 'editMunicipal'])->name('settings.municipal.edit');
        Route::put('/settings/municipal', [\App\Http\Controllers\Admin\SettingsController::class, 'updateMunicipal'])->name('settings.municipal.update');
        Route::get('/settings/hero', [\App\Http\Controllers\Admin\SettingsController::class, 'editHero'])->name('settings.hero.edit');
        Route::put('/settings/hero', [\App\Http\Controllers\Admin\SettingsController::class, 'updateHero'])->name('settings.hero.update');
    });
});

// Officer routes
Route::prefix('officer')->name('officer.')->group(function () {
    // Officer login routes (no middleware to allow access)
    Route::get('/login', [\App\Http\Controllers\Admin\OfficerController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\OfficerController::class, 'login'])->name('login.attempt');

    // Authenticated officer routes
    Route::middleware(['auth:admin', 'officer'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\OfficerController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [\App\Http\Controllers\Admin\OfficerController::class, 'logout'])->name('logout');
        Route::get('/requests/{trackingNumber}/details', [\App\Http\Controllers\Admin\OfficerController::class, 'viewRequestDetails'])->name('requests.details');
        Route::post('/requests/{trackingNumber}/status', [\App\Http\Controllers\Admin\OfficerController::class, 'updateRequestStatus'])->name('requests.update-status');
    });
});


