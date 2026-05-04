<?php

if (! function_exists('get_company_info')) {
    function get_company_info()
    {
        $path = base_path('jsonassets/company.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return [
            'name' => 'Technodec',
            'tagline' => 'Admin Panel',
            'description' => ''
        ];
    }
}

if (! function_exists('get_company_name')) {
    function get_company_name()
    {
        $company = get_company_info();
        return $company['name'] ?? 'Technodec';
    }
}

if (! function_exists('get_company_tagline')) {
    function get_company_tagline()
    {
        $company = get_company_info();
        return $company['tagline'] ?? 'Admin Panel';
    }
}

if (! function_exists('get_company_description')) {
    function get_company_description()
    {
        $company = get_company_info();
        return $company['description'] ?? '';
    }
}

