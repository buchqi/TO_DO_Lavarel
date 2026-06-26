<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// This controller owns the login/logout part of the request lifecycle.
// It is called from routes/web.php when the browser shows the login form,
// submits credentials, or logs the current user out.
class LoginController extends Controller
{
    // Called by GET /login.
    // It receives a normal browser request, uses no models, and returns
    // the Blade login view so the user can enter credentials.
    public function create()
    {
        return view('auth.login');
    }

    // Called by POST /login when the login form is submitted.
    // It receives email/password fields, uses Laravel's Auth service instead
    // of querying User manually, and returns either a redirect to the app or
    // a redirect back with validation-style errors.
    public function store(Request $request)
    {
        // Laravel validation protects the authentication attempt from malformed
        // input. If a rule fails, Laravel redirects back automatically and puts
        // error messages in the session for the Blade view.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt checks the credentials against the users table and,
        // when valid, stores the user's id in the session. The remember flag
        // lets Laravel create a long-lived login cookie if requested.
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerating the session id prevents session fixation attacks.
            // Without this, an attacker could try to reuse a known session id.
            $request->session()->regenerate();

            // redirect()->intended sends the user to the page they originally
            // tried to access before auth middleware sent them to login.
            // with('success', ...) is a flash message stored for one request.
            return redirect()->intended(route('tasks.index'))->with('success', 'Welcome back.');
        }

        // Failed login returns to the form with an error message and keeps the
        // email field. The password is intentionally not preserved.
        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    // Called by POST /logout.
    // It receives the current authenticated session, uses the Auth facade,
    // and returns a redirect to the login route with a flash message.
    public function destroy(Request $request)
    {
        // Auth::logout removes the authenticated user from Laravel's session.
        Auth::logout();

        // Invalidating and regenerating the CSRF token fully closes the old
        // session context so later requests cannot reuse stale session data.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // The success message is flashed to the session and displayed once.
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
