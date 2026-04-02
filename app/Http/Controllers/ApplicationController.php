<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Internship;
use App\Models\Application;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLog
    ) {}

    /**
     * Store a new internship application using a transaction.
     */
    public function store(Request $request)
    {
        $userId = $request->user_id;
        $internshipId = $request->internship_id;

        // Pre-validation before transaction
        $user = User::find($userId);
        if (!$user) {
            $this->activityLog->log(
                'application_failed',
                null,
                Application::class,
                null,
                ['reason' => 'User not found', 'user_id' => $userId, 'internship_id' => $internshipId]
            );
            return response()->json(['error' => 'User not found'], 404);
        }

        $internship = Internship::find($internshipId);
        if (!$internship) {
            $this->activityLog->log(
                'application_failed',
                $user->id,
                Application::class,
                null,
                ['reason' => 'Internship not found', 'user_id' => $user->id, 'internship_id' => $internshipId]
            );
            return response()->json(['error' => 'Internship not found'], 404);
        }

        $isAllowed = DB::table('group_internships')
            ->where('group_id', $user->group_id)
            ->where('internship_id', $internship->id)
            ->exists();

        if ($user->role !== 'student' || !$isAllowed) {
            $this->activityLog->log(
                'application_failed',
                $user->id,
                Application::class,
                null,
                ['reason' => 'User is not allowed to apply for this internship', 'user_id' => $user->id, 'internship_id' => $internshipId, 'group_id' => $user->group_id]
            );
            return response()->json(['error' => 'User is not allowed to apply for this internship'], 403);
        }

        // Create application within transaction
        return DB::transaction(function () use ($request, $user, $internship) {
            $application = Application::create([
                'user_id'           => $user->id,
                'group_id'          => $user->group_id,
                'internship_id'     => $internship->id,
                'company_id'        => $request->company_id,
                'motivation_letter' => $request->motivation_letter,
                'status'            => 'pending'
            ]);

            $this->activityLog->log(
                'application_created',
                $user->id,
                Application::class,
                $application->id,
                ['internship_id' => $internship->id]
            );

            return response()->json([
                'message' => 'Application created successfully',
                'data' => $application
            ], 201);
        });
    }

    /**
     * Apply for internship using stored procedure.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'user_id'           => 'required|integer',
            'internship_id'     => 'required|integer',
            'group_id'          => 'required|integer',
            'company_id'        => 'nullable|integer',
            'motivation_letter' => 'required|string',
        ]);

        DB::statement('CALL apply_for_internship(?, ?, ?, ?, ?, @result)', [
            $request->user_id,
            $request->internship_id,
            $request->group_id,
            $request->company_id,
            $request->motivation_letter,
        ]);

        $result = DB::select('SELECT @result')[0]->{'@result'};

        if ($result === 'OK') {
            $application = Application::where('user_id', $request->user_id)
                ->where('internship_id', $request->internship_id)
                ->latest('id')
                ->first();

            $this->activityLog->log(
                'application_submitted',
                $request->user_id,
                Application::class,
                $application->id,
                ['internship_id' => $request->internship_id]
            );

            return response()->json([
                'message' => 'Application created successfully',
                'data' => $application
            ], 201);
        }

        $this->activityLog->log(
            'application_failed',
            $request->user_id,
            null,
            null,
            ['reason' => $result, 'internship_id' => $request->internship_id]
        );

        $status = match ($result) {
            'User not found'     => 404,
            'Invalid internship' => 404,
            'Already applied'    => 409,
            default              => 400,
        };

        return response()->json(['error' => $result], $status);
    }
}