<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Setup\FirstRunController as AdminFirstRunController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OfficerController as AdminOfficerController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PhotoPanelController;
use App\Http\Controllers\Admin\MunicipalInformationController as AdminMunicipalInformationController;
use App\Http\Controllers\Admin\WebsiteMunicipalInformationController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CityOfficialController;
use App\Http\Controllers\Admin\TouristSpotController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\FloorAdminController;
use App\Http\Controllers\PoiAdminController;
use App\Http\Controllers\ChatbotController;

// Admin portal
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Auth::guard('admin')->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    })->name('index');

    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])
            ->middleware('throttle:admin-login')
            ->name('login.attempt');

        // First-run setup when no admins exist
        Route::get('/setup', [AdminFirstRunController::class, 'show'])->name('setup.show');
        Route::post('/setup', [AdminFirstRunController::class, 'store'])->name('setup.store');
    });

    // Authenticated routes (Super Admin only)
    Route::middleware(['auth:admin', 'superadmin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/filtered-logs', [AdminDashboardController::class, 'filteredLogs'])->name('dashboard.filtered-logs');
        Route::get('/dashboard/recent-activity', [AdminDashboardController::class, 'recentActivity'])->name('dashboard.recent-activity');
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Officers (Super Admin only)
        Route::get('/officers', [AdminOfficerController::class, 'index'])->name('officers.index');
        Route::get('/officers/create', [AdminOfficerController::class, 'create'])->name('officers.create');
        Route::post('/officers', [AdminOfficerController::class, 'store'])->name('officers.store');
        Route::get('/officers/{officer}/edit', [AdminOfficerController::class, 'edit'])->name('officers.edit');
        Route::put('/officers/{officer}', [AdminOfficerController::class, 'update'])->name('officers.update');
        Route::delete('/officers/{officer}', [AdminOfficerController::class, 'destroy'])->name('officers.destroy');
        Route::put('/officers/{officer}/toggle', [AdminOfficerController::class, 'toggle'])->name('officers.toggle');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/filtered', [ActivityLogController::class, 'filtered'])->name('activity-logs.filtered');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

        // Feedbacks
        Route::get('/feedbacks', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedbacks.index');
        Route::get('/feedbacks/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('feedbacks.show');
        Route::delete('/feedbacks/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'destroy'])->name('feedbacks.destroy');

        // Services CRUD
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        // Types
        Route::get('/services/{service}/types', [ServiceController::class, 'types'])->name('services.types');
        Route::post('/services/{service}/types', [ServiceController::class, 'typeStore'])->name('services.types.store');
        Route::put('/services/{service}/types/{type}', [ServiceController::class, 'typeUpdate'])->name('services.types.update');
        Route::post('/services/{service}/types/reorder', [ServiceController::class, 'typesReorder'])->name('services.types.reorder');
        Route::delete('/services/{service}/types/{type}', [ServiceController::class, 'typeDestroy'])->name('services.types.destroy');

        // Steps
        Route::get('/services/{service}/types/{type}/steps', [ServiceController::class, 'steps'])->name('services.steps');
        Route::post('/services/{service}/types/{type}/steps', [ServiceController::class, 'stepStore'])->name('services.steps.store');
        Route::put('/services/{service}/types/{type}/steps/{step}', [ServiceController::class, 'stepUpdate'])->name('services.steps.update');
        Route::delete('/services/{service}/types/{type}/steps/{step}', [ServiceController::class, 'stepDestroy'])->name('services.steps.destroy');

        // Quick admin utilities
        Route::get('/services/manage', fn () => redirect()->route('admin.services.index'))->name('services.manage');
        Route::get('/dashboard/edit', fn () => view('admin.dashboard.edit'))->name('dashboard.edit');
        Route::get('/process-flow', [SettingsController::class, 'processFlow'])->name('process-flow');
        
        // Workflow Steps CRUD
        Route::post('/workflow-steps', [SettingsController::class, 'createWorkflowStep'])->name('workflow-steps.create');
        Route::get('/workflow-steps/{id}', [SettingsController::class, 'getWorkflowStep'])->name('workflow-steps.get');
        Route::put('/workflow-steps/{id}', [SettingsController::class, 'updateWorkflowStep'])->name('workflow-steps.update');
        Route::delete('/workflow-steps/{id}', [SettingsController::class, 'deleteWorkflowStep'])->name('workflow-steps.delete');

        // Photo Panel Management
        Route::prefix('photo-panel')->name('photo-panel.')->group(function () {
            Route::get('/', [PhotoPanelController::class, 'index'])->name('index');
            Route::get('/create', [PhotoPanelController::class, 'create'])->name('create');
            Route::post('/', [PhotoPanelController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PhotoPanelController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PhotoPanelController::class, 'update'])->name('update');
            Route::delete('/{id}', [PhotoPanelController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [PhotoPanelController::class, 'reorder'])->name('reorder');
        });

        // Municipal Information Management (Kiosk)
        Route::prefix('municipal-information')->name('municipal-information.')->group(function () {
            Route::get('/edit', [AdminMunicipalInformationController::class, 'edit'])->name('edit');
            Route::put('/', [AdminMunicipalInformationController::class, 'update'])->name('update');
        });

        // Website Municipal Information Management
        Route::prefix('website/municipal-information')->name('website.municipal-information.')->group(function () {
            Route::get('/', [WebsiteMunicipalInformationController::class, 'index'])->name('index');
            Route::get('/create', [WebsiteMunicipalInformationController::class, 'create'])->name('create');
            Route::post('/', [WebsiteMunicipalInformationController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [WebsiteMunicipalInformationController::class, 'edit'])->name('edit');
            Route::put('/{id}', [WebsiteMunicipalInformationController::class, 'update'])->name('update');
            Route::delete('/{id}', [WebsiteMunicipalInformationController::class, 'destroy'])->name('destroy');
        });

        // Website Announcements Management
        Route::prefix('website/announcements')->name('website.announcements.')->group(function () {
            Route::get('/', [WebsiteMunicipalInformationController::class, 'announcements'])->name('index');
            Route::post('/', [WebsiteMunicipalInformationController::class, 'storeAnnouncement'])->name('store');
            Route::put('/{announcementId}', [WebsiteMunicipalInformationController::class, 'updateAnnouncement'])->name('update');
            Route::delete('/{announcementId}', [WebsiteMunicipalInformationController::class, 'deleteAnnouncement'])->name('delete');
        });

        // About Content Management
        Route::prefix('about')->name('about.')->group(function () {
            Route::get('/', [AboutController::class, 'index'])->name('index');
            Route::get('/create', [AboutController::class, 'create'])->name('create');
            Route::post('/', [AboutController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AboutController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AboutController::class, 'update'])->name('update');
            Route::delete('/{id}', [AboutController::class, 'destroy'])->name('destroy');
        });

        // Blog Management
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/create', [BlogController::class, 'create'])->name('create');
            Route::post('/', [BlogController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('edit');
            Route::put('/{id}', [BlogController::class, 'update'])->name('update');
            Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
        });

        Route::resource('city-officials', CityOfficialController::class);
        Route::resource('tourist-spots', TouristSpotController::class);

        // 3D Map Wayfinding Management
        // Floors Management
        Route::get('/floors', [FloorAdminController::class, 'index'])->name('floors.index');
        Route::post('/floors', [FloorAdminController::class, 'store'])->name('floors.store');
        Route::put('/floors/{floor}', [FloorAdminController::class, 'update'])->name('floors.update');
        Route::delete('/floors/{floor}', [FloorAdminController::class, 'destroy'])->name('floors.destroy');

        // POIs Management
        Route::get('/pois', [PoiAdminController::class, 'index'])->name('pois.index');
        Route::post('/pois', [PoiAdminController::class, 'store'])->name('pois.store');
        Route::put('/pois/{poi}', [PoiAdminController::class, 'update'])->name('pois.update');
        Route::delete('/pois/{poi}', [PoiAdminController::class, 'destroy'])->name('pois.destroy');
        Route::post('/kiosk/refresh', [PoiAdminController::class, 'refreshKiosk'])->name('kiosk.refresh');

        // Chatbot Knowledge Base Management
        Route::prefix('chatbot')->name('chatbot.')->group(function () {
            Route::get('/knowledge-base', [ChatbotController::class, 'viewKnowledgeBase'])->name('knowledge-base.index');
            Route::post('/knowledge-base/document', [ChatbotController::class, 'createDocument'])->name('knowledge-base.document.create');
            Route::post('/knowledge-base/import-csv', [ChatbotController::class, 'importCsv'])->name('knowledge-base.import-csv');
            Route::delete('/knowledge-base/document/{documentId}', [ChatbotController::class, 'deleteDocument'])->name('knowledge-base.document.delete');
        });
    });
});

