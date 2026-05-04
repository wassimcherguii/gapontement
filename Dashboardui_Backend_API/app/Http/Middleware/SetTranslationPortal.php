<?php

namespace App\Http\Middleware;

use App\Services\TranslationDomainRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetTranslationPortal
{
    /**
     * Bind the translation domain (portal) for this request (portal_t(), Blade).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $registry = app(TranslationDomainRegistry::class);
        if (! in_array($portal, $registry->allowedSlugs(), true)) {
            abort(404);
        }

        app()->instance('translation_portal', $portal);
        View::share('translationPortal', $portal);

        return $next($request);
    }
}
