<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\AppointmentController;
use App\Http\Controllers\Website\AppointmentServiceController;
use App\Http\Controllers\Website\PublicDashboardController;
use App\Http\Controllers\Website\TrackingController;
use App\Http\Controllers\Website\WebsiteController;

// Website Mode Routes (from existing Laravel app)
Route::prefix('website')->name('website.')->group(function () {
    Route::get('/', [WebsiteController::class, 'homepage'])->name('homepage');
    Route::get('/processing', fn () => view('website.service-request.service_track_processing'))->name('processing');
    Route::get('/requested', fn () => view('website.service-request.service_track_requested'))->name('requested');
    Route::get('/completed', fn () => view('website.service-request.service_track_completed'))->name('completed');

    // Appointment routes
    Route::get('/tnd', fn () => view('website.appointment.tnd'))->name('appointment.tnd');
    Route::get('/service', fn () => view('website.appointment.service'))->name('appointment.service');
    Route::get('/details', fn () => view('website.appointment.details'))->name('appointment.details');
    Route::get('/done', fn () => view('website.appointment.done'))->name('appointment.done');

    // Steps and forms
    Route::get('/marriage-steps', fn () => view('website.steps.marriage_steps'))->name('steps');
    Route::get('/personal-details', fn () => view('website.marriage-form.personal-details'))->name('personal-details');
    Route::get('/marriage-form', fn () => view('website.marriage-form.form'))->name('marriage-form');
    Route::get('/track', fn () => view('website.reference_code'))->name('track');

    // Additional website pages
    Route::get('/about', fn () => view('website.about'))->name('about');
    Route::get('/services', fn () => view('website.services'))->name('services');
    Route::get('/contact', fn () => view('website.contact'))->name('contact');

    // Service detail pages
    Route::get('/services/civil-registry', fn () => view('website.services.civil-registry'))->name('services.civil-registry');
    Route::get('/services/business-permit', fn () => view('website.services.business-permit'))->name('services.business-permit');
    Route::get('/services/transportation', fn () => view('website.services.transportation'))->name('services.transportation');
    Route::get('/services/veterinary', fn () => view('website.services.social-welfare'))->name('services.veterinary');
    Route::get('/services/social-welfare', fn () => view('website.services.social-welfare'))->name('services.social-welfare'); // Keep for backward compatibility
    Route::get('/services/engineering', fn () => view('website.services.engineering'))->name('services.engineering');
    Route::get('/public-dashboard', [PublicDashboardController::class, 'index'])->name('public-dashboard');
});

// Direct website routes (without prefix for easier access)
Route::get('/homepage', fn () => view('website.homepage'));
Route::get('/processing', fn () => view('website.service-request.service_track_processing'));
Route::get('/requested', fn () => view('website.service-request.service_track_requested'));
Route::get('/completed', fn () => view('website.service-request.service_track_completed'));

Route::get('/tnd', fn () => view('website.appointment.tnd'))->name('appointment.tnd');
Route::get('/service', [AppointmentServiceController::class, 'index'])->name('appointment.service');
Route::post('/service/select', [AppointmentServiceController::class, 'selectService'])->name('website.appointment.select-service');
Route::get('/service/types', [AppointmentServiceController::class, 'getServiceTypes'])->name('website.appointment.service-types');

// Appointment data persistence routes
Route::post('/appointment/service', [AppointmentController::class, 'storeServiceSelection'])->name('website.appointment.store-service');
Route::post('/appointment/datetime', [AppointmentController::class, 'storeDateTime'])->name('website.appointment.store-datetime');
Route::post('/appointment/details', [AppointmentController::class, 'storePersonalDetails'])->name('website.appointment.store-details');
Route::post('/appointment/create', [AppointmentController::class, 'createAppointment'])->name('website.appointment.create');
Route::post('/appointment/documents', [AppointmentController::class, 'storeDocuments'])->name('website.appointment.store-documents'); // Deprecated - kept for backward compatibility
Route::get('/appointment/{id}', [AppointmentController::class, 'show'])->name('website.appointment.show');
Route::get('/appointment/number/{appointmentNumber}', [AppointmentController::class, 'getByAppointmentNumber'])->name('website.appointment.by-number');

// Get service data from session
Route::get('/website/appointment/service-data', [AppointmentController::class, 'getServiceData'])->name('website.appointment.service-data');

Route::get('/details', fn () => view('website.appointment.details'))->name('appointment.details');
Route::get('/done', fn () => view('website.appointment.done'))->name('appointment.done');
Route::get('/marriage-steps', fn () => view('website.steps.marriage_steps'))->name('steps');
Route::get('/personal-details', fn () => view('website.marriage-form.personal-details'))->name('personal-details');
Route::get('/marriage-form', fn () => view('website.marriage-form.form'));

Route::get('/track', [TrackingController::class, 'index'])->name('track');
Route::post('/track', [TrackingController::class, 'track'])->name('website.track');
Route::get('/website/tracking/data/{trackingNumber}', [TrackingController::class, 'getTrackingData'])->name('website.tracking.data');
Route::get('/website/tracking/status/{trackingNumber}', [TrackingController::class, 'getStatus'])->name('website.tracking.status');

Route::get('/public-dashboard', [PublicDashboardController::class, 'index'])->name('public-dashboard');

// Test route for upload validation
Route::get('/test-upload-validation', fn () => view('website.test-upload-validation'))->name('test.upload.validation');

