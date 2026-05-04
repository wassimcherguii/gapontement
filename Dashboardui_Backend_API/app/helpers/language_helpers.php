<?php

if (! function_exists('get_languages')) {
    function get_languages()
    {
        $path = base_path('jsonassets/languages.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }

        return [];
    }
}

if (! function_exists('get_default_language')) {
    function get_default_language()
    {
        $languages = get_languages();

        return $languages['default'] ?? 'en';
    }
}

if (! function_exists('get_supported_languages')) {
    function get_supported_languages()
    {
        $languages = get_languages();

        return $languages['supported'] ?? [];
    }
}

if (! function_exists('get_translation')) {
    /**
     * @param  string  $key  Message key under the messages group (without messages. prefix)
     * @param  array|string|null  $replace  Replacement parameters for :placeholders, or legacy: locale string when only two args are used
     * @param  string|null  $locale
     */
    function get_translation($key, $replace = [], $locale = null)
    {
        // Backward compatible: get_translation('key', 'fr') meant a target locale, not replacements
        if (func_num_args() === 2 && is_string($replace)) {
            $locale = $replace;
            $replace = [];
        }

        $replace = is_array($replace) ? $replace : [];

        return __('messages.'.$key, $replace, $locale ?? app()->getLocale());
    }
}

if (! function_exists('get_translation_with_params')) {
    function get_translation_with_params($key, $params = [], $locale = null)
    {
        $translation = get_translation($key, $locale);

        // Replace parameters in the translation
        foreach ($params as $param => $value) {
            $translation = str_replace('{{ '.$param.' }}', $value, $translation);
        }

        return $translation;
    }
}

if (! function_exists('get_language_info')) {
    function get_language_info($code)
    {
        $languages = get_supported_languages();

        return $languages[$code] ?? null;
    }
}

if (! function_exists('route_with_lang')) {
    /**
     * Generate a route URL with language prefix in path
     *
     * @param  string  $name
     * @param  array  $parameters
     * @param  string|null  $locale
     * @return string
     */
    function route_with_lang($name, $parameters = [], $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLang = get_default_language();

        // Always include language in route parameters
        $parameters['lang'] = $locale;

        return route($name, $parameters);
    }
}

if (! function_exists('localized_route')) {
    /**
     * Generate a localized route URL (alias for route_with_lang)
     *
     * @param  string  $name
     * @param  array  $parameters
     * @param  string|null  $locale
     * @return string
     */
    function localized_route($name, $parameters = [], $locale = null)
    {
        return route_with_lang($name, $parameters, $locale);
    }
}

if (! function_exists('is_rtl_language')) {
    function is_rtl_language($code = null)
    {
        $code = $code ?? app()->getLocale();
        $info = get_language_info($code);

        return $info && $info['direction'] === 'rtl';
    }
}

if (! function_exists('web_client_resolve_path')) {
    /**
     * @param  array<string, mixed>  $data
     */
    function web_client_resolve_path(array $data, string $dotKey): mixed
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

if (! function_exists('web_client_t')) {
    /**
     * String from the published WEB domain JSON bundle (jsonassets/i18n/web/{locale}.json), dot notation.
     * Falls back to English bundle, then to $default or the key string.
     */
    function web_client_t(string $dotKey, ?string $default = null, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $request = request();
        $cacheKey = '_web_client_bundle_'.$locale;

        if (! $request->attributes->has($cacheKey)) {
            $request->attributes->set(
                $cacheKey,
                app(\App\Services\TranslationPublishService::class)->readBundle('web', $locale)
            );
        }

        /** @var array<string, mixed> $bundle */
        $bundle = $request->attributes->get($cacheKey, []);
        $value = is_array($bundle) ? web_client_resolve_path($bundle, $dotKey) : null;

        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if ($locale !== 'en') {
            $enKey = '_web_client_bundle_en';
            if (! $request->attributes->has($enKey)) {
                $request->attributes->set(
                    $enKey,
                    app(\App\Services\TranslationPublishService::class)->readBundle('web', 'en')
                );
            }
            /** @var array<string, mixed> $enBundle */
            $enBundle = $request->attributes->get($enKey, []);
            $fallback = is_array($enBundle) ? web_client_resolve_path($enBundle, $dotKey) : null;
            if (is_string($fallback) && $fallback !== '') {
                return $fallback;
            }
            if (is_int($fallback) || is_float($fallback)) {
                return (string) $fallback;
            }
        }

        return $default ?? $dotKey;
    }
}
