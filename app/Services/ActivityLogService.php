<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log a user action.
     */
    public function log(
        string $action,
        ?int $userId = null,
        ?string $entityType = null,
        ?int $entityId = null,
        array $properties = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => $userId,
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'properties'   => $properties,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }

    /**
     * Get activity logs for a specific entity.
     */
    public function getForEntity(string $entityType, int $entityId)
    {
        return ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get activity logs for a specific user.
     */
    public function getForUser(int $userId, int $limit = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
