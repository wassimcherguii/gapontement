<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect(route_with_lang('admin.dashboard'));
        }
        
        return view('admin.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect(route_with_lang('admin.dashboard'));
        }
        
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min_length'),
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Check if user exists and has admin privileges
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => __('messages.no_account_found'),
            ])->withInput($request->except('password'));
        }

        // Check if user has admin privileges
        if (!$user->hasAdminPrivileges()) {
            return back()->withErrors([
                'email' => __('messages.no_admin_permission'),
            ])->withInput($request->except('password'));
        }

        // Attempt to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Redirect to admin dashboard with language
            $locale = app()->getLocale();
            return redirect()->intended(route_with_lang('admin.dashboard'));
        }

        // Authentication failed
        return back()->withErrors([
            'password' => __('messages.password_incorrect'),
        ])->withInput($request->except('password'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Preserve language preference before clearing session
        $currentLocale = app()->getLocale();
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Restore language preference after session regeneration
        app()->setLocale($currentLocale);
        session(['locale' => $currentLocale]);
        
        return redirect(route_with_lang('admin.login', [], $currentLocale));
    }
}