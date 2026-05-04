<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}" style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>404 - {{ get_translation('page_not_found') ?? 'Page Not Found' }} | {{ get_company_name() }}</title>
    <meta name="description" content="{{ get_translation('page_not_found_description') ?? 'The page you are looking for could not be found.' }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset_favicon() }}">
    
    <!-- Tailwind CSS & Flowbite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
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
    @include('components.critical-css')
    
    <style>
        body {
            background: var(--background-color);
            color: var(--text-color);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" 
         dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}"
         style="background: var(--background-color); color: var(--text-color);">
        
        <!-- 404 Content -->
        <div class="max-w-2xl w-full text-center space-y-6">
            <!-- Error Code -->
            <div class="space-y-4">
                <h1 class="text-8xl md:text-9xl font-bold" style="color: var(--primary-color);">
                    404
                </h1>
                <h2 class="text-2xl md:text-3xl font-semibold" style="color: var(--text-color);">
                    {{ get_translation('page_not_found') ?? 'Page Not Found' }}
                </h2>
                <p class="text-base md:text-lg" style="color: var(--text-secondary-color);">
                    {{ get_translation('page_not_found_description') ?? 'The page you are looking for could not be found. It may have been moved, deleted, or you may have entered an incorrect URL.' }}
                </p>
            </div>

            <!-- Home Button -->
            <div class="pt-4">
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors duration-200 hover:opacity-90 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-2' : 'space-x-2' }}"
                   style="background: var(--primary-color);">
                    @if(function_exists('lucide_icon'))
                        {!! lucide_icon('home', 'w-5 h-5', 'currentColor') !!}
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    @endif
                    <span>{{ get_translation('go_home') ?? 'Go to Homepage' }}</span>
                </a>
            </div>

            <!-- Company Logo (Optional) -->
            @if(function_exists('asset_logo'))
            <div class="pt-8">
                <img src="{{ asset_logo() }}" 
                     alt="{{ get_logo_alt() ?? get_company_name() }} Logo" 
                     class="h-12 mx-auto opacity-50">
            </div>
            @endif
        </div>
    </div>

    <!-- Footer with Company Name -->
    <div class="absolute bottom-4 left-0 right-0 text-center">
        <p class="text-sm" style="color: var(--text-secondary-color);">
            &copy; {{ date('Y') }} {{ get_company_name() }}. {{ get_translation('all_rights_reserved') ?? 'All rights reserved.' }}
        </p>
    </div>
</body>
</html>


