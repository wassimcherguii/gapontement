@php
    $theme = session('theme', 'light');
    $palette = get_colors($theme);
    $brand = $palette['brand'] ?? [];
    $usage = $palette['usage'] ?? [];
    $isRtl = is_rtl_language(app()->getLocale());
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title') — {{ web_client_t('landing.brand.site_name', get_company_name()) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --lp-primary: {{ $brand['primary'] ?? '#0F4C81' }};
            --lp-secondary: {{ $brand['secondary'] ?? '#14B8A6' }};
            --lp-accent: {{ $brand['accent'] ?? '#22D3EE' }};
            --lp-bg: {{ $usage['background'] ?? '#F8FAFC' }};
            --lp-surface: {{ $usage['surface'] ?? '#ffffff' }};
            --lp-text: {{ $usage['text'] ?? '#0f172a' }};
            --lp-muted: {{ $usage['text-secondary'] ?? '#475569' }};
            --lp-border: {{ $usage['border'] ?? '#e2e8f0' }};
        }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .blog-prose { line-height: 1.7; }
        .blog-prose img { max-width: 100%; height: auto; border-radius: 0.5rem; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen flex flex-col antialiased" style="background: var(--lp-bg); color: var(--lp-text);">
    <header class="border-b px-4 py-4" style="border-color: var(--lp-border); background: var(--lp-surface);">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-4">
            <a href="{{ route('welcome', ['lang' => app()->getLocale()]) }}" class="text-sm font-semibold hover:underline" style="color: var(--lp-primary);">
                {{ get_translation('blog_public_back_home') }}
            </a>
        </div>
    </header>
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-10 {{ $isRtl ? 'text-right' : 'text-left' }}">
        @yield('content')
    </main>
</body>
</html>
