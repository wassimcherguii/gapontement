<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', get_translation('dashboard')) - {{ get_company_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route_with_lang('welcome') }}" class="font-semibold">{{ get_company_name() }}</a>
            <div class="flex items-center gap-3">
                @yield('header_links')
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 sm:p-6">
        @if(session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
