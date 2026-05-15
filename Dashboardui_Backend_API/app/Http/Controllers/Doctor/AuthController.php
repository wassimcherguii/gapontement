<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isDoctor()) {
            return redirect(route_with_lang('doctor.dashboard'));
        }

        return view('doctor.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => get_translation('email_required'),
            'email.email' => get_translation('email_invalid'),
            'password.required' => get_translation('password_required'),
            'password.min' => get_translation('password_min_length'),
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => get_translation('no_account_found'),
            ])->withInput($request->except('password'));
        }

        if (! $user->isDoctor()) {
            return back()->withErrors([
                'email' => get_translation('no_doctor_permission'),
            ])->withInput($request->except('password'));
        }

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'password' => get_translation('password_incorrect'),
            ])->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route_with_lang('doctor.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route_with_lang('doctor.login'));
    }
}
