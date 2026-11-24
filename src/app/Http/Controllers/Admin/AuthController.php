<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = 'admin-login:'.strtolower($credentials['email']).'|'.$request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->with('error', 'Too many attempts. Please try again in a minute.');
        }

        if (! Auth::attempt(['email' => strtolower($credentials['email']), 'password' => $credentials['password']])) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

            return back()->withInput(['email' => $credentials['email']])->with('error', 'Invalid credentials.');
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        if (! Auth::user()->isStaff() || ! Auth::user()->is_active) {
            Auth::logout();

            return back()->with('error', 'This account does not have staff access.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
