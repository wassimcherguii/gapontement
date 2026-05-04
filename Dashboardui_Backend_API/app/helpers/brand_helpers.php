<?php

if (!function_exists('get_brand_assets')) {
    /**
     * Get brand assets configuration from JSON file
     *
     * @return array
     */
    function get_brand_assets()
    {
        $jsonPath = base_path('jsonassets/brand-assets.json');
        
        if (!file_exists($jsonPath)) {
            return [
                'logo' => [
                    'filename' => 'ClientLogo.png',
                    'path' => 'assets/logos/ClientLogo.png',
                    'alt' => 'Technodec Logo'
                ],
                'favicon' => [
                    'filename' => 'favicon.png',
                    'path' => 'favicon.png',
                    'alt' => 'Technodec Favicon'
                ]
            ];
        }
        
        $jsonContent = file_get_contents($jsonPath);
        $assets = json_decode($jsonContent, true);
        
        return $assets ?: [];
    }
}

if (!function_exists('get_logo_path')) {
    /**
     * Get the logo path
     *
     * @return string
     */
    function get_logo_path()
    {
        $assets = get_brand_assets();
        return $assets['logo']['path'] ?? 'assets/logos/ClientLogo.png';
    }
}

if (!function_exists('get_logo_alt')) {
    /**
     * Get the logo alt text
     *
     * @return string
     */
    function get_logo_alt()
    {
        $assets = get_brand_assets();
        return $assets['logo']['alt'] ?? 'Technodec Logo';
    }
}

if (!function_exists('get_favicon_path')) {
    /**
     * Get the favicon path
     *
     * @return string
     */
    function get_favicon_path()
    {
        $assets = get_brand_assets();
        return $assets['favicon']['path'] ?? 'favicon.png';
    }
}

if (!function_exists('get_favicon_alt')) {
    /**
     * Get the favicon alt text
     *
     * @return string
     */
    function get_favicon_alt()
    {
        $assets = get_brand_assets();
        return $assets['favicon']['alt'] ?? 'Technodec Favicon';
    }
}

if (!function_exists('asset_logo')) {
    /**
     * Get the full asset URL for the logo
     *
     * @return string
     */
    function asset_logo()
    {
        return asset(get_logo_path());
    }
}

if (!function_exists('asset_favicon')) {
    /**
     * Get the full asset URL for the favicon
     *
     * @return string
     */
    function asset_favicon()
    {
        return asset(get_favicon_path());
    }
}


