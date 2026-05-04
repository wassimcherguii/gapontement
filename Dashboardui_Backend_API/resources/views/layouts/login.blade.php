<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}" style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', get_translation('admin_login')) - {{ get_company_name() }}</title>
    <meta name="description" content="@yield('description', get_translation('login_subtitle'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset_favicon() }}">
    
    <!-- Tailwind CSS & Flowbite (Local Installation) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Arabic Font for RTL -->
    @if(is_rtl_language(app()->getLocale()))
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endif
    
    <!-- Critical CSS -->
    @include('components.critical-css')
    
    <!-- Page-specific CSS -->
    @stack('styles')
</head>
<body class="min-h-screen" style="background: var(--background-color); color: var(--text-color);">
    @yield('content')
    
    <!-- Page-specific JavaScript -->
    @stack('scripts')
    
    <!-- Language Persistence System -->
    @include('components.language-persistence')
</body>
</html>
