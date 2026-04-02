<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::post('/applications', [ApplicationController::class, 'store']);
Route::post('/applications/apply', [ApplicationController::class, 'apply']);

Route::get('/activity-log', [ActivityLogController::class, 'index']);
Route::get('/activity-log/user/{userId}', [ActivityLogController::class, 'byUser']);
Route::get('/activity-log/entity/{entityType}/{entityId}', [ActivityLogController::class, 'byEntity']);

// Comments routes (polymorphic)
Route::get('/comments/{type}/{id}', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);
Route::put('/comments/{id}', [CommentController::class, 'update']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
