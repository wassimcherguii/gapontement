<?php

if (! function_exists('get_colors')) {
    function get_colors($theme = 'light')
    {
        $path = base_path('jsonassets/colors.json');
        if (file_exists($path)) {
            $colors = json_decode(file_get_contents($path), true);
            return $colors[$theme] ?? $colors['light'] ?? [];
        }
        return [];
    }
}

if (! function_exists('get_light_colors')) {
    function get_light_colors()
    {
        return get_colors('light');
    }
}

if (! function_exists('get_dark_colors')) {
    function get_dark_colors()
    {
        return get_colors('dark');
    }
}

if (! function_exists('get_current_theme_colors')) {
    function get_current_theme_colors()
    {
        $theme = session('theme', 'light');
        return get_colors($theme);
    }
}

if (! function_exists('get_color_by_path')) {
    function get_color_by_path($path, $theme = 'light')
    {
        $colors = get_colors($theme);
        $keys = explode('.', $path);
        $value = $colors;
        
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        
        return $value;
    }
}
