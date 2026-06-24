<?php

use App\Http\Controllers\Api\PublicTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/tasks/public', PublicTaskController::class)->name('api.tasks.public');
