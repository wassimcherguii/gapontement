<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SyncLandingThemeFromCookie::class,
        ]);

        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'superadmin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'portal' => \App\Http\Middleware\SetTranslationPortal::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        $middleware->redirectTo(
            guests: function ($request) {
                $path = trim((string) $request->path(), '/');
                $segments = $path === '' ? [] : explode('/', $path);
                $section = $segments[1] ?? null;

                if ($section === 'patient') {
                    return route_with_lang('patient.login');
                }

                if ($section === 'doctor') {
                    return route_with_lang('doctor.login');
                }

                return route_with_lang('admin.login');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom 404 handler for all not found routes
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            // If API request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => get_translation('page_not_found') ?? 'Page not found',
                    'error' => '404',
                ], 404);
            }

            // Return custom 404 page for web requests
            return response()->view('errors.404', [], 404);
        });
    })->create();
