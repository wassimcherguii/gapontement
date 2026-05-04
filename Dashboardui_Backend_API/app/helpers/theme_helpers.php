<?php

if (! function_exists('get_theme_config')) {
    function get_theme_config()
    {
        $path = base_path('jsonassets/theme.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return [];
    }
}

if (! function_exists('get_default_theme')) {
    function get_default_theme()
    {
        $config = get_theme_config();
        return $config['default'] ?? 'light';
    }
}

if (! function_exists('get_fallback_theme')) {
    function get_fallback_theme()
    {
        $config = get_theme_config();
        return $config['fallback'] ?? 'light';
    }
}

if (! function_exists('get_supported_themes')) {
    function get_supported_themes()
    {
        $config = get_theme_config();
        return $config['supported'] ?? [];
    }
}

if (! function_exists('get_theme_info')) {
    function get_theme_info($code)
    {
        $themes = get_supported_themes();
        return $themes[$code] ?? null;
    }
}

if (! function_exists('is_valid_theme')) {
    function is_valid_theme($theme)
    {
        $themes = get_supported_themes();
        return isset($themes[$theme]);
    }
}

if (! function_exists('get_theme_settings')) {
    function get_theme_settings()
    {
        $config = get_theme_config();
        return $config['settings'] ?? [];
    }
}



