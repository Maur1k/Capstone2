<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MunicipalInformationController;
use App\Http\Controllers\OtherServiceController;

// Municipal Information
Route::get('/municipal-information', [MunicipalInformationController::class, 'index']);

// Services pages
Route::get('/services', fn () => view('services.index'));
Route::get('/services/types', [RequestController::class, 'showServiceTypes']);
Route::get('/services/requirements', [RequestController::class, 'showRequirements']);
Route::get('/services/kiosk', [RequestController::class, 'showKioskForm']);
Route::get('/services/tracker', fn () => view('services.tracker.index'));
Route::post('/services/tracker/print-receipt', [\App\Http\Controllers\Website\TrackingController::class, 'printReceipt']);

// Chatbot UI and message endpoint
Route::get('/chatbot', [ChatbotController::class, 'view']);
Route::post('/chatbot/message', [ChatbotController::class, 'message']);

// Knowledge base scanning endpoints
Route::get('/chatbot/knowledge-base/scan', [ChatbotController::class, 'scanKnowledgeBase']);
Route::get('/chatbot/knowledge-base/questions', [ChatbotController::class, 'getQuestions']);
Route::get('/chatbot/knowledge-base/document/{documentId}', [ChatbotController::class, 'getDocument']);
Route::post('/chatbot/knowledge-base/document', [ChatbotController::class, 'createDocument']);
Route::post('/chatbot/knowledge-base/import-csv', [ChatbotController::class, 'importCsv']);
Route::delete('/chatbot/knowledge-base/document/{documentId}', [ChatbotController::class, 'deleteDocument']);

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

// Debug route for tracking
Route::get('/debug/track/{trackingNumber}', function ($trackingNumber) {
    $civil = \App\Models\CivilService::where('tracking_number', $trackingNumber)->first();
    $other = \App\Models\OtherService::where('tracking_number', $trackingNumber)->first();

    return response()->json([
        'tracking_number' => $trackingNumber,
        'civil_service' => $civil ? [
            'id' => $civil->id,
            'full_name' => $civil->full_name,
            'status' => $civil->status,
            'service_name' => $civil->service_name,
            'service_type' => $civil->service_type,
            'created_at' => $civil->created_at,
        ] : null,
        'other_service' => $other ? [
            'id' => $other->id,
            'full_name' => $other->full_name,
            'status' => $other->status,
            'service_name' => $other->service_name,
            'service_type' => $other->service_type,
            'created_at' => $other->created_at,
        ] : null,
    ]);
});

// Other services (kept in web routes for CSRF protection)
Route::post('/other-services', [OtherServiceController::class, 'store']);

