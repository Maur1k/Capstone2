<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OfficerController;
use App\Http\Controllers\Admin\DocumentManagementController;
use App\Http\Controllers\Admin\AppointmentManagementController;
use App\Http\Controllers\Website\AppointmentController as WebsiteAppointmentController;

// Officer dashboard routes (legacy views/shared endpoints)
Route::prefix('officer')->name('officer.api.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.officer-dashboard.dashboard'))->name('dashboard');

    // Appointment management (shared APIs used by officer dashboard widgets)
    Route::get('/appointments', [WebsiteAppointmentController::class, 'getAppointments'])->name('appointments');
    Route::get('/appointments/statistics', [WebsiteAppointmentController::class, 'getStatistics'])->name('appointments.statistics');
    Route::put('/appointments/{id}/status', [WebsiteAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{id}/approve', [WebsiteAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{id}/decline', [WebsiteAppointmentController::class, 'decline'])->name('appointments.decline');

    // Document management widgets
    Route::get('/documents', [DocumentManagementController::class, 'index'])->name('documents');
    Route::get('/documents/statistics', [DocumentManagementController::class, 'statistics'])->name('documents.statistics');
    Route::put('/documents/{id}/approve', [DocumentManagementController::class, 'approve'])->name('documents.approve');
    Route::put('/documents/{id}/reject', [DocumentManagementController::class, 'reject'])->name('documents.reject');
});

// Officer authentication + protected workflows
Route::prefix('officer')->name('officer.')->group(function () {
    // Officer login routes (no middleware to allow access)
    Route::get('/login', [OfficerController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [OfficerController::class, 'login'])->name('login.attempt');

    // Authenticated officer routes
    Route::middleware(['auth:admin', 'officer'])->group(function () {
        Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
        Route::get('/weekly-overview', [OfficerController::class, 'weeklyOverview'])->name('weekly-overview');
        Route::get('/approval-requests', [OfficerController::class, 'showApprovalRequests'])->name('approval-requests');
        Route::post('/logout', [OfficerController::class, 'logout'])->name('logout');
        Route::get('/requests/{trackingNumber}/details', [OfficerController::class, 'viewRequestDetails'])->name('requests.details');
        Route::post('/requests/{trackingNumber}/status', [OfficerController::class, 'updateRequestStatus'])->name('requests.update-status');

        // Officer request management routes (AJAX only)
        Route::get('/requests/brap', [OfficerController::class, 'getBrapRequests'])->name('requests.brap');
        Route::get('/requests/regular', [OfficerController::class, 'getRegularRequests'])->name('requests.regular');
        Route::get('/requests/other', [OfficerController::class, 'getOtherCivilRegistryRequests'])->name('requests.other');
        Route::post('/requests/status', [OfficerController::class, 'updateRequestStatusAjax'])->name('requests.update-status-ajax');
        Route::get('/requests/{trackingNumber}/view', [OfficerController::class, 'viewRequest'])->name('requests.view');
        Route::get('/notifications', [OfficerController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/mark-read', [OfficerController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [OfficerController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

        // Approval workflow routes
        Route::get('/check-approval-requests', [OfficerController::class, 'checkForApprovalRequests'])->name('check-approval-requests');
        Route::post('/approve-request', [OfficerController::class, 'approveRequest'])->name('approve-request');
        
        // 10 Days Posting routes
        Route::get('/posting-requests', [OfficerController::class, 'getPostingRequests'])->name('posting-requests');
        Route::post('/save-posting-days', [OfficerController::class, 'savePostingDays'])->name('save-posting-days');

        // Queue number settings
        Route::get('/queue-number-settings', [OfficerController::class, 'showQueueNumberSettings'])->name('queue-number-settings');
        Route::post('/queue-number-settings', [OfficerController::class, 'updateQueueNumberSettings'])->name('queue-number-settings.update');
        Route::get('/queue-data', [OfficerController::class, 'getQueueData'])->name('queue-data');
        Route::get('/queue-display', [OfficerController::class, 'showQueueDisplay'])->name('queue-display');
        Route::post('/queue/call', [OfficerController::class, 'callQueue'])->name('queue.call');
        Route::get('/reports/requests', [OfficerController::class, 'printRequests'])->name('reports.requests');
        Route::post('/queue/remove', [OfficerController::class, 'removeFromQueue'])->name('queue.remove');

        // Officer Appointment Management (internal tooling)
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [AppointmentManagementController::class, 'index'])->name('index');
            Route::get('/statistics', [AppointmentManagementController::class, 'statistics'])->name('statistics');
            Route::get('/calendar', [AppointmentManagementController::class, 'calendar'])->name('calendar');
            Route::get('/{appointmentId}', [AppointmentManagementController::class, 'show'])->name('show');
            Route::post('/{appointmentId}/confirm', [AppointmentManagementController::class, 'confirm'])->name('confirm');
            Route::post('/{appointmentId}/complete', [AppointmentManagementController::class, 'complete'])->name('complete');
            Route::post('/{appointmentId}/cancel', [AppointmentManagementController::class, 'cancel'])->name('cancel');
        });

        // Officer Document Management
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentManagementController::class, 'index'])->name('index');
            Route::get('/statistics', [DocumentManagementController::class, 'statistics'])->name('statistics');
            Route::get('/pending-count', [DocumentManagementController::class, 'pendingCount'])->name('pending-count');
            Route::get('/civil-service/{civilServiceId}', [DocumentManagementController::class, 'getCivilServiceDocuments'])->name('civil-service');
            Route::get('/{documentId}', [DocumentManagementController::class, 'show'])->name('show');
            Route::get('/{documentId}/download', [DocumentManagementController::class, 'download'])->name('download');
            Route::get('/{documentId}/view', [DocumentManagementController::class, 'view'])->name('view');
            Route::post('/{documentId}/approve', [DocumentManagementController::class, 'approve'])->name('approve');
            Route::post('/{documentId}/reject', [DocumentManagementController::class, 'reject'])->name('reject');
            Route::post('/upload', [DocumentManagementController::class, 'upload'])->name('upload');
            Route::post('/bulk-approve', [DocumentManagementController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [DocumentManagementController::class, 'bulkReject'])->name('bulk-reject');
        });
    });
});

