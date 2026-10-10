<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        Admin $admin,
        string $activityType,
        string $description,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $metadata = null,
        ?Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'admin_id' => $admin->id,
            'activity_type' => $activityType,
            'description' => $description,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public static function logLogin(Admin $admin, Request $request): ActivityLog
    {
        return self::log(
            $admin,
            'login',
            "Logged in to " . ($admin->role === 'super_admin' ? 'Admin' : 'Officer') . " Dashboard",
            null,
            null,
            ['department' => $admin->department],
            $request
        );
    }

    public static function logLogout(Admin $admin, Request $request): ActivityLog
    {
        return self::log(
            $admin,
            'logout',
            "Logged out from " . ($admin->role === 'super_admin' ? 'Admin' : 'Officer') . " Dashboard",
            null,
            null,
            ['department' => $admin->department],
            $request
        );
    }

    public static function logStatusChange(
        Admin $admin,
        string $targetType,
        int $targetId,
        string $trackingNumber,
        string $oldStatus,
        string $newStatus,
        ?string $adminNotes = null,
        Request $request = null
    ): ActivityLog {
        return self::log(
            $admin,
            'status_change',
            "Changed request status from '{$oldStatus}' to '{$newStatus}'",
            $targetType,
            $targetId,
            [
                'tracking_number' => $trackingNumber,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'admin_notes' => $adminNotes,
                'department' => $admin->department,
            ],
            $request
        );
    }

    public static function logRequestViewed(
        Admin $admin,
        string $targetType,
        int $targetId,
        string $trackingNumber,
        Request $request = null
    ): ActivityLog {
        return self::log(
            $admin,
            'request_viewed',
            "Viewed request details",
            $targetType,
            $targetId,
            [
                'tracking_number' => $trackingNumber,
                'department' => $admin->department,
            ],
            $request
        );
    }

    public static function logRequirementsUpdated(
        Admin $admin,
        string $targetType,
        int $targetId,
        string $trackingNumber,
        string $requirements,
        Request $request = null
    ): ActivityLog {
        return self::log(
            $admin,
            'requirements_updated',
            "Updated requirements checklist",
            $targetType,
            $targetId,
            [
                'tracking_number' => $trackingNumber,
                'requirements' => $requirements,
                'department' => $admin->department,
            ],
            $request
        );
    }
}
