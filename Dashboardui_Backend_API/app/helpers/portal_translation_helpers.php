<?php

use App\Services\TranslationPublishService;

if (! function_exists('portal_resolve_path')) {
    /**
     * @param  array<string, mixed>  $data
     */
    function portal_resolve_path(array $data, string $dotKey): mixed
    {
        $segments = explode('.', $dotKey);
        $cursor = $data;
        foreach ($segments as $segment) {
            if (! is_array($cursor) || ! array_key_exists($segment, $cursor)) {
                return null;
            }
            $cursor = $cursor[$segment];
        }

        return $cursor;
    }
}

if (! function_exists('portal_t')) {
    /**
     * Translate a key from the current portal's published JSON bundle (dot notation).
     * Requires middleware that sets app('translation_portal') to a valid domain id.
     */
    function portal_t(string $key, ?string $locale = null): string
    {
        if (! app()->bound('translation_portal')) {
            return $key;
        }

        $domain = app('translation_portal');
        if (! is_string($domain) || $domain === '') {
            return $key;
        }

        $locale = $locale ?? app()->getLocale();
        $request = request();
        $cacheKey = '_portal_bundle_'.$domain.'_'.$locale;

        if (! $request->attributes->has($cacheKey)) {
            $request->attributes->set(
                $cacheKey,
                app(TranslationPublishService::class)->readBundle($domain, $locale)
            );
        }

        /** @var array<string, mixed> $bundle */
        $bundle = $request->attributes->get($cacheKey, []);
        $value = is_array($bundle) ? portal_resolve_path($bundle, $key) : null;

        if (is_string($value) && $value !== '') {
            return $value;
        }

        if ($locale !== 'en') {
            $enKey = '_portal_bundle_'.$domain.'_en';
            if (! $request->attributes->has($enKey)) {
                $request->attributes->set(
                    $enKey,
                    app(TranslationPublishService::class)->readBundle($domain, 'en')
                );
            }
            $enBundle = $request->attributes->get($enKey, []);
            $fallback = is_array($enBundle) ? portal_resolve_path($enBundle, $key) : null;
            if (is_string($fallback) && $fallback !== '') {
                return $fallback;
            }
        }

        return $key;
    }
}
