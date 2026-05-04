<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get supported languages from our JSON system
        $supportedLanguages = array_keys(get_supported_languages());
        $defaultLang = get_default_language();
        
        // Get language from URL path segment (first segment after domain)
        $pathSegments = explode('/', trim($request->path(), '/'));
        $lang = !empty($pathSegments) && in_array($pathSegments[0], $supportedLanguages) 
            ? $pathSegments[0] 
            : null;
        
        // Also check route parameter (for named routes with {lang} parameter)
        if (!$lang && $request->route('lang')) {
            $lang = $request->route('lang');
        }
        
        // Fallback: check query parameter (for backward compatibility)
        if (!$lang && $request->has('lang')) {
            $queryLang = $request->query('lang');
            if ($queryLang && in_array($queryLang, $supportedLanguages)) {
                $lang = $queryLang;
            }
        }
        
        // Fallback: check session
        if (!$lang) {
            $sessionLang = session('locale');
            if ($sessionLang && in_array($sessionLang, $supportedLanguages)) {
                $lang = $sessionLang;
            }
        }
        
        // Final fallback: use default language
        if (!$lang || !in_array($lang, $supportedLanguages)) {
            $lang = $defaultLang;
        }
        
        // Set the locale
        app()->setLocale($lang);
        session(['locale' => $lang]);
        
        return $next($request);
    }
}

