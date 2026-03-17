<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLog
    ) {}

    /**
     * Get all activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type)
                  ->where('entity_id', $request->entity_id);
        }

        return $query->paginate(50);
    }

    /**
     * Get activity logs for a specific user.
     */
    public function byUser(int $userId)
    {
        return response()->json([
            'data' => $this->activityLog->getForUser($userId)
        ]);
    }

    /**
     * Get activity logs for a specific entity.
     */
    public function byEntity(string $entityType, int $entityId)
    {
        return response()->json([
            'data' => $this->activityLog->getForEntity($entityType, $entityId)
        ]);
    }
}
