<?php

namespace App\Observers;

use App\Models\Application;
use App\Services\ActivityLogService;

class ApplicationObserver
{
    public function __construct(
        private ActivityLogService $activityLog
    ) {}

    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        $this->activityLog->log(
            'application_created_trigger',
            $application->user_id,
            Application::class,
            $application->id,
            [
                'internship_id' => $application->internship_id,
                'group_id' => $application->group_id,
                'company_id' => $application->company_id,
                'status' => $application->status,
            ]
        );
    }
}
