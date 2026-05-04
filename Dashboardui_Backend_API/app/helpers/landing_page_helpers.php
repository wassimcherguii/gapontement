<?php

use Illuminate\Support\Facades\File;

if (! function_exists('landing_page_payload')) {
    /**
     * Published landing page JSON (no DB). Cached per request.
     *
     * @return array<string, mixed>|null
     */
    function landing_page_payload(string $slug = 'home', ?string $locale = null): ?array
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = '_landing_payload_'.$slug.'_'.$locale;

        if (request()->attributes->has($cacheKey)) {
            /** @var array<string, mixed>|null $cached */
            $cached = request()->attributes->get($cacheKey);

            return $cached;
        }

        $path = base_path("jsonassets/page-cache/{$slug}.{$locale}.json");
        if (! File::exists($path)) {
            request()->attributes->set($cacheKey, null);

            return null;
        }

        $json = File::get($path);
        $data = json_decode($json, true);
        if (! is_array($data)) {
            request()->attributes->set($cacheKey, null);

            return null;
        }

        request()->attributes->set($cacheKey, $data);

        return $data;
    }
}
