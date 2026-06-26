<?php

use App\Http\Controllers\Api\PublicGroupController;
use App\Http\Controllers\Api\PublicTaskController;
use Illuminate\Support\Facades\Route;

// API routes return JSON instead of Blade HTML.
// These endpoints are intentionally public so external clients can read
// summarized task/group data without using the browser UI.
Route::get('/groups/public', PublicGroupController::class)->name('api.groups.public');
Route::get('/tasks/public', PublicTaskController::class)->name('api.tasks.public');
