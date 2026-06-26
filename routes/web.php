<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// This is the first web route a browser hits at the site root.
// It demonstrates the request lifecycle: the browser requests "/",
// Laravel matches this route, then the closure decides where the user belongs.
Route::get('/', function () {
    // auth()->check() asks Laravel's authentication system whether a user
    // is stored in the current session. If this line were removed, guests
    // and logged-in users would not be separated at the landing page.
    return auth()->check()
        ? redirect()->route('tasks.index')
        : redirect()->route('login');
});

// The guest middleware allows only unauthenticated visitors to enter this group.
// It protects login/register pages from users who are already signed in.
Route::middleware('guest')->group(function () {
    // GET routes show forms because browsers request pages with GET.
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    // POST routes receive submitted form data and are allowed to change state.
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Logout is protected by auth middleware because only a logged-in user has
// a session that can be destroyed. POST is used because logout changes state.
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// The auth middleware protects the main application area.
// Every route inside this group requires a valid logged-in session first.
Route::middleware('auth')->group(function () {
    // This small route keeps the common dashboard URL while reusing the task list.
    Route::get('/dashboard', fn () => redirect()->route('tasks.index'))->name('dashboard');
    // These custom member routes sit beside the resource controller because
    // adding/removing group members is not one of the standard CRUD actions.
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.members.store');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.members.destroy');
    // Route::resource creates the conventional CRUD route set for groups.
    // Laravel maps URLs like /groups/{group}/edit to controller methods by name.
    Route::resource('groups', GroupController::class);
    // Toggle is a custom action because it changes only the task status,
    // not the full task record.
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    // Resource routes reduce repetition and demonstrate the MVC convention:
    // routes describe HTTP entry points, controllers handle application logic.
    Route::resource('tasks', TaskController::class)->except(['show']);
});
