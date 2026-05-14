<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncLandingThemeFromCookie
{
    /**
     * Keep session('theme') aligned with the landing_theme cookie so light/dark persists across pages and visits.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fromCookie = $request->cookie('landing_theme');
        if (in_array($fromCookie, ['light', 'dark'], true)) {
            session(['theme' => $fromCookie]);
        } elseif (! in_array(session('theme'), ['light', 'dark'], true)) {
            session(['theme' => 'light']);
        }

        return $next($request);
    }
}
