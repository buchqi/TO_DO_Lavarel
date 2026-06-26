<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// This controller handles account creation.
// It demonstrates Laravel's MVC pattern: the route receives the request,
// the controller validates it, the User model saves it, and a redirect
// sends the browser to the next page.
class RegisterController extends Controller
{
    // Called by GET /register.
    // It receives a browser request and returns the Blade registration form.
    public function create()
    {
        return view('auth.register');
    }

    // Called by POST /register after the form is submitted.
    // It receives name/email/password fields, uses the User model, logs in
    // the new account, and returns a redirect to the task list.
    public function store(Request $request)
    {
        // These rules protect the users table from incomplete or duplicate
        // accounts. The confirmed rule expects password_confirmation from Blade.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // User::create uses Eloquent mass assignment. The User model decides
        // which fields are fillable and automatically hashes the password cast.
        $user = User::create($validated);

        // Logging in immediately stores the new user's id in the session, so
        // they do not need to visit the login page after registering.
        Auth::login($user);

        // The redirect ends the POST request and starts a fresh GET request,
        // which avoids duplicate form submissions on browser refresh.
        return redirect()->route('tasks.index')->with('success', 'Account created successfully.');
    }
}
