<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Api\WayfindingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes (no CSRF protection)
Route::post('/request', [RequestController::class, 'store']);
Route::get('/request/track/{trackingNumber}', [RequestController::class, 'track']);
Route::get('/request/status/{trackingNumber}', [RequestController::class, 'getStatus']);
Route::post('/request/print-receipt', [RequestController::class, 'printReceiptByTracking']);
Route::put('/request/{trackingNumber}/status', [RequestController::class, 'updateStatus']);
Route::post('/request/kiosk-submit', [RequestController::class, 'kioskSubmit']);

// Feedback API routes
Route::post('/feedback/submit', [\App\Http\Controllers\Api\FeedbackController::class, 'submit']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Wayfinding API Routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    // Public read-only routes
    Route::get('/floors', [WayfindingController::class, 'floors']);
    Route::get('/floor/{id}', [WayfindingController::class, 'floor']);
    Route::get('/floor/{id}/pois', [WayfindingController::class, 'poisByFloor']);
    Route::get('/poi/{id}', [WayfindingController::class, 'poi']);
    Route::get('/route', [WayfindingController::class, 'route']); // ?from=1&to=5
});

// Admin routes with stricter rate limiting
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/admin/cache/clear', [WayfindingController::class, 'clearCache']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'services' => [
            'database' => 'connected',
            'cache' => 'available'
        ]
    ]);
});