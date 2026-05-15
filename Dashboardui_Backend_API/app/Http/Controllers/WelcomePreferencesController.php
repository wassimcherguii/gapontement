<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class WelcomePreferencesController extends Controller
{
    public function updateTheme(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|string|in:light,dark',
        ]);

        $theme = $validated['theme'];
        session(['theme' => $theme]);

        Cookie::queue(Cookie::make(
            'landing_theme',
            $theme,
            60 * 24 * 365,
            '/',
            null,
            $request->secure(),
            true,
            false,
            'lax'
        ));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back();
    }
}
