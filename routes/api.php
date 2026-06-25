<?php

use App\Http\Controllers\Api\PublicGroupController;
use App\Http\Controllers\Api\PublicTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/groups/public', PublicGroupController::class)->name('api.groups.public');
Route::get('/tasks/public', PublicTaskController::class)->name('api.tasks.public');
