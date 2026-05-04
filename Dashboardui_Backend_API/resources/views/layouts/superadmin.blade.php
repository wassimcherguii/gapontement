<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}" style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', get_translation('superadmin_dashboard')) - {{ get_company_name() }} {{ get_company_tagline() }}</title>
    <meta name="description" content="@yield('description', get_translation('superadmin_dashboard_description'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset_favicon() }}">
    
    <!-- Tailwind CSS & Flowbite (Local Installation) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Arabic Font for RTL -->
    @if(is_rtl_language(app()->getLocale()))
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif
    
    <!-- Critical CSS -->
    @include('superadmin.components.critical-css')
    
    <!-- Page-specific CSS -->
    @stack('styles')
</head>
<body class="min-h-screen" style="background: var(--background-color); color: var(--text-color);">
    <!-- SuperAdmin Layout Container -->
    <div class="flex h-screen overflow-hidden {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}" 
         dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}">
        <!-- Mobile Sidebar Overlay -->
        <div id="superadminSidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
        
        <!-- Sidebar -->
        @include('superadmin.components.sidebar')
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden {{ is_rtl_language(app()->getLocale()) ? 'mr-0' : 'ml-0' }}">
            <!-- Header -->
            @include('superadmin.components.header')
            
            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto {{ is_rtl_language(app()->getLocale()) ? 'lg:mr-64' : 'lg:ml-64' }}" style="background: var(--background-color);">
                <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" 
                     style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Page-specific JavaScript -->
    @stack('scripts')
    
    <!-- Settings Modal -->
    @include('superadmin.components.settings-modal')
    
    <!-- Language Persistence System -->
    @include('components.language-persistence')
    
    <!-- SuperAdmin JavaScript -->
    @include('superadmin.components.superadmin-scripts')
</body>
</html>
