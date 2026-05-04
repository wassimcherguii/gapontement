<?php

if (!function_exists('get_logo_from_db')) {
    /**
     * Get logo data from database
     *
     * @param string $name
     * @return \App\Models\Logo|null
     */
    function get_logo_from_db($name = 'Main Logo')
    {
        return \App\Models\Logo::where('name', $name)->first();
    }
}

if (!function_exists('get_favicon_from_db')) {
    /**
     * Get favicon data from database
     *
     * @return \App\Models\Logo|null
     */
    function get_favicon_from_db()
    {
        return \App\Models\Logo::where('name', 'Favicon')->first();
    }
}

if (!function_exists('get_logo_path_from_db')) {
    /**
     * Get the logo path from database
     *
     * @param string $name
     * @return string
     */
    function get_logo_path_from_db($name = 'Main Logo')
    {
        $logo = get_logo_from_db($name);
        return $logo ? $logo->path : 'assets/logos/ClientLogo.png';
    }
}

if (!function_exists('get_logo_alt_from_db')) {
    /**
     * Get the logo alt text from database
     *
     * @param string $name
     * @return string
     */
    function get_logo_alt_from_db($name = 'Main Logo')
    {
        $logo = get_logo_from_db($name);
        return $logo ? $logo->alt : 'Technodec Logo';
    }
}

if (!function_exists('get_favicon_path_from_db')) {
    /**
     * Get the favicon path from database
     *
     * @return string
     */
    function get_favicon_path_from_db()
    {
        $favicon = get_favicon_from_db();
        return $favicon ? $favicon->path : 'favicon.png';
    }
}

if (!function_exists('get_favicon_alt_from_db')) {
    /**
     * Get the favicon alt text from database
     *
     * @return string
     */
    function get_favicon_alt_from_db()
    {
        $favicon = get_favicon_from_db();
        return $favicon ? $favicon->alt : 'Technodec Favicon';
    }
}

if (!function_exists('asset_logo_from_db')) {
    /**
     * Get the full asset URL for the logo from database
     *
     * @param string $name
     * @return string
     */
    function asset_logo_from_db($name = 'Main Logo')
    {
        return asset(get_logo_path_from_db($name));
    }
}

if (!function_exists('asset_favicon_from_db')) {
    /**
     * Get the full asset URL for the favicon from database
     *
     * @return string
     */
    function asset_favicon_from_db()
    {
        return asset(get_favicon_path_from_db());
    }
}

if (!function_exists('get_all_logos_from_db')) {
    /**
     * Get all logos from database
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_all_logos_from_db()
    {
        return \App\Models\Logo::all();
    }
}




