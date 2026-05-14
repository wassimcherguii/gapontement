@php
    $lp = landing_page_payload('home');
    $webCatalog = app(\App\Services\TranslationPublishService::class)->readLanguages('web', false);
    $webLocaleCodes = array_keys($webCatalog['supported'] ?? []);
    $isRtl = is_rtl_language(app()->getLocale());
    $theme = session('theme', 'light');
    $paletteLight = get_colors('light');
    $paletteDark = get_colors('dark');
    $brandLight = $paletteLight['brand'] ?? [];
    $usageLight = $paletteLight['usage'] ?? [];
    $brandDark = $paletteDark['brand'] ?? [];
    $usageDark = $paletteDark['usage'] ?? [];
    $companyName = get_company_name();
    $companyTagline = get_company_tagline();
    $logoUrl = asset_logo();
    $faviconPath = get_favicon_path();
    $contactPhone = filled(data_get($lp, 'topBar.phone')) ? (string) data_get($lp, 'topBar.phone') : '';
    $telHref = $contactPhone !== '' ? preg_replace('/\s+/', '', $contactPhone) : '';
    $txt = function (?string $fromJson, string $webKey, ?string $fallback = null) {
        if (is_string($fromJson) && $fromJson !== '') {
            return $fromJson;
        }

        return web_client_t($webKey, $fallback);
    };
    $landingBlogPosts = \App\Models\BlogPost::query()
        ->published()
        ->featuredLanding()
        ->orderBy('sort_order')
        ->orderByDesc('published_at')
        ->with('translations')
        ->get();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth" data-landing-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ filled(data_get($lp, 'meta.title')) ? data_get($lp, 'meta.title') : web_client_t('landing.brand.site_name', $companyName).' — '.web_client_t('landing.meta.title_suffix') }}</title>
    @php
        $metaDesc = data_get($lp, 'meta.description');
        $metaDesc = is_string($metaDesc) && $metaDesc !== '' ? $metaDesc : web_client_t('landing.meta.description');
    @endphp
    <meta name="description" content="{{ Str::limit(strip_tags($metaDesc), 160) }}">
    <link rel="icon" href="{{ asset($faviconPath) }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html[data-landing-theme="light"] {
            --lp-primary: {{ $brandLight['primary'] ?? '#0F4C81' }};
            --lp-primary-dark: {{ $brandLight['primary-dark'] ?? '#0B355A' }};
            --lp-primary-light: {{ $brandLight['primary-light'] ?? '#3A7CA5' }};
            --lp-primary-hover: {{ $brandLight['primary-hover'] ?? '#0D3F6B' }};
            --lp-secondary: {{ $brandLight['secondary'] ?? '#14B8A6' }};
            --lp-accent: {{ $brandLight['accent'] ?? '#22D3EE' }};
            --lp-bg: {{ $usageLight['background'] ?? '#F8FAFC' }};
            --lp-surface: {{ $usageLight['surface'] ?? '#ffffff' }};
            --lp-text: {{ $usageLight['text'] ?? '#0f172a' }};
            --lp-muted: {{ $usageLight['text-secondary'] ?? '#475569' }};
            --lp-border: {{ $usageLight['border'] ?? '#e2e8f0' }};
        }
        html[data-landing-theme="dark"] {
            --lp-primary: {{ $brandDark['primary'] ?? '#3A7CA5' }};
            --lp-primary-dark: {{ $brandDark['primary-dark'] ?? '#0F4C81' }};
            --lp-primary-light: {{ $brandDark['primary-light'] ?? '#60A5FA' }};
            --lp-primary-hover: {{ $brandDark['primary-hover'] ?? '#2F6690' }};
            --lp-secondary: {{ $brandDark['secondary'] ?? '#2DD4BF' }};
            --lp-accent: {{ $brandDark['accent'] ?? '#67E8F9' }};
            --lp-bg: {{ $usageDark['background'] ?? '#020617' }};
            --lp-surface: {{ $usageDark['surface'] ?? '#0f172a' }};
            --lp-text: {{ $usageDark['text'] ?? '#f8fafc' }};
            --lp-muted: {{ $usageDark['text-secondary'] ?? '#cbd5e1' }};
            --lp-border: {{ $usageDark['border'] ?? '#1e293b' }};
        }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .landing-settings-fab {
            position: fixed;
            z-index: 120;
            bottom: 1.5rem;
            inset-inline-end: 1.5rem;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            border: none;
            background: linear-gradient(145deg, var(--lp-primary), color-mix(in srgb, var(--lp-primary) 70%, var(--lp-secondary)));
            box-shadow:
                0 0 0 1px color-mix(in srgb, #fff 22%, transparent),
                0 4px 6px color-mix(in srgb, var(--lp-text) 12%, transparent),
                0 18px 40px color-mix(in srgb, var(--lp-primary) 45%, transparent);
            transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
        }
        .landing-settings-fab:hover {
            transform: translateY(-2px) scale(1.04);
            filter: brightness(1.06);
            box-shadow:
                0 0 0 1px color-mix(in srgb, #fff 30%, transparent),
                0 8px 16px color-mix(in srgb, var(--lp-text) 14%, transparent),
                0 22px 48px color-mix(in srgb, var(--lp-primary) 50%, transparent);
        }
        .landing-settings-fab:focus-visible {
            outline: 2px solid var(--lp-accent);
            outline-offset: 3px;
        }
        .landing-settings-fab .landing-settings-gear {
            width: 1.45rem;
            height: 1.45rem;
            transition: transform 0.5s cubic-bezier(0.34, 1.3, 0.64, 1);
            transform-origin: center;
            filter: drop-shadow(0 1px 1px color-mix(in srgb, #000 25%, transparent));
        }
        .landing-settings-fab.is-open .landing-settings-gear {
            transform: rotate(225deg) scale(0.92);
        }
        .landing-settings-dialog {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .landing-settings-dialog.is-open { display: flex; }
        .landing-settings-dialog-backdrop {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 120% 80% at 50% 100%, color-mix(in srgb, var(--lp-primary) 18%, transparent), color-mix(in srgb, var(--lp-text) 55%, transparent));
            backdrop-filter: blur(10px) saturate(1.2);
            -webkit-backdrop-filter: blur(10px) saturate(1.2);
        }
        .landing-settings-panel {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 24rem;
            border-radius: 1.25rem;
            overflow: hidden;
            color: var(--lp-text);
            border: 1px solid color-mix(in srgb, var(--lp-border) 85%, var(--lp-primary));
            background: color-mix(in srgb, var(--lp-surface) 88%, transparent);
            backdrop-filter: blur(16px) saturate(1.35);
            -webkit-backdrop-filter: blur(16px) saturate(1.35);
            box-shadow:
                0 0 0 1px color-mix(in srgb, #fff 12%, transparent) inset,
                0 28px 60px color-mix(in srgb, var(--lp-text) 28%, transparent),
                0 12px 24px color-mix(in srgb, var(--lp-primary) 15%, transparent);
            animation: landing-settings-pop 0.38s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .landing-settings-panel-accent {
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, var(--lp-primary), var(--lp-secondary), var(--lp-accent));
            opacity: 0.95;
        }
        .landing-settings-panel-inner {
            padding: 1.35rem 1.35rem 1.25rem;
        }
        .landing-settings-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.15rem;
        }
        .landing-settings-kicker {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin: 0 0 0.35rem;
            color: var(--lp-primary);
        }
        .landing-settings-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.25;
            color: var(--lp-text);
        }
        .landing-settings-sub {
            margin: 0.4rem 0 0;
            font-size: 0.8125rem;
            line-height: 1.45;
            color: var(--lp-muted);
        }
        .landing-settings-close {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            border: 1px solid color-mix(in srgb, var(--lp-border) 90%, transparent);
            background: color-mix(in srgb, var(--lp-bg) 65%, transparent);
            color: var(--lp-muted);
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.2s;
        }
        .landing-settings-close:hover {
            background: color-mix(in srgb, var(--lp-primary) 12%, var(--lp-surface));
            color: var(--lp-text);
            transform: rotate(90deg);
        }
        .landing-settings-body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .landing-settings-block {
            padding: 1rem 1rem 1.05rem;
            border-radius: 0.875rem;
            background: color-mix(in srgb, var(--lp-bg) 55%, var(--lp-surface));
            border: 1px solid color-mix(in srgb, var(--lp-border) 70%, transparent);
            box-shadow: 0 1px 0 color-mix(in srgb, #fff 6%, transparent) inset;
        }
        .landing-settings-block-title {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0 0 0.65rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--lp-muted);
        }
        .landing-settings-block-title svg {
            width: 0.95rem;
            height: 0.95rem;
            opacity: 0.85;
            color: var(--lp-primary);
        }
        .landing-settings-lang-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }
        .landing-settings-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid color-mix(in srgb, var(--lp-border) 85%, transparent);
            color: var(--lp-text);
            background: color-mix(in srgb, var(--lp-surface) 90%, transparent);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background 0.15s ease;
        }
        .landing-settings-chip:hover {
            transform: translateY(-1px);
            border-color: color-mix(in srgb, var(--lp-primary) 45%, var(--lp-border));
            box-shadow: 0 6px 14px color-mix(in srgb, var(--lp-text) 8%, transparent);
        }
        .landing-settings-chip.is-current {
            background: var(--lp-primary);
            border-color: color-mix(in srgb, var(--lp-primary) 80%, #000);
            color: #fff;
            box-shadow: 0 6px 18px color-mix(in srgb, var(--lp-primary) 42%, transparent);
        }
        .landing-settings-segmented {
            display: flex;
            width: 100%;
            border-radius: 0.875rem;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--lp-border) 80%, transparent);
            background: color-mix(in srgb, var(--lp-bg) 70%, var(--lp-surface));
        }
        .landing-settings-segmented .landing-settings-theme-option {
            flex: 1;
            margin: 0;
            padding: 0.7rem 0.75rem;
            border: none;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            color: var(--lp-muted);
            background: transparent;
            transition: background 0.2s ease, color 0.2s ease, opacity 0.2s ease;
        }
        .landing-settings-segmented .landing-settings-theme-option:first-of-type {
            border-inline-end: 1px solid color-mix(in srgb, var(--lp-border) 75%, transparent);
        }
        .landing-settings-segmented .landing-settings-theme-option.is-active {
            background: linear-gradient(180deg, color-mix(in srgb, var(--lp-primary) 92%, #fff), var(--lp-primary));
            color: #fff;
        }
        .landing-settings-segmented .landing-settings-theme-option.is-pending {
            opacity: 0.65;
            pointer-events: none;
        }
        .landing-settings-theme-hint {
            margin: 0.55rem 0 0;
            font-size: 0.7rem;
            line-height: 1.4;
            color: var(--lp-muted);
        }
        .landing-settings-ltr {
            direction: ltr;
            unicode-bidi: isolate;
            text-align: start;
        }
        .landing-settings-phone-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            background: color-mix(in srgb, var(--lp-primary) 10%, var(--lp-bg));
            border: 1px solid color-mix(in srgb, var(--lp-primary) 22%, var(--lp-border));
        }
        .landing-settings-phone-card svg {
            flex-shrink: 0;
            width: 1.35rem;
            height: 1.35rem;
            color: var(--lp-primary);
        }
        .landing-settings-phone-link {
            font-size: 1.125rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.02em;
            text-decoration: none;
            color: var(--lp-text);
            transition: color 0.15s ease;
        }
        .landing-settings-phone-link:hover {
            color: var(--lp-primary);
        }
        .landing-settings-phone-empty {
            margin: 0;
            font-size: 0.8125rem;
            line-height: 1.45;
            color: var(--lp-muted);
        }
        .landing-topbar-phone {
            direction: ltr;
            unicode-bidi: isolate;
            display: inline-block;
        }
        @keyframes landing-spin {
            to { transform: rotate(360deg); }
        }
        .landing-lang-nav-spinner {
            display: none;
            width: 0.85em;
            height: 0.85em;
            flex-shrink: 0;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: landing-spin 0.65s linear infinite;
            opacity: 0.85;
        }
        .landing-settings-chip.is-navigating .landing-lang-nav-spinner {
            display: inline-block;
        }
        .landing-settings-chip.is-navigating {
            pointer-events: none;
            opacity: 0.92;
        }
        @keyframes landing-settings-pop {
            from { opacity: 0; transform: translateY(14px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-[var(--lp-bg)] text-[var(--lp-text)]" style="color: var(--lp-text);">

    @if ($lp && (data_get($lp, 'topBar.phone') || data_get($lp, 'topBar.emergency') || data_get($lp, 'topBar.hours')))
        <div class="text-xs sm:text-sm border-b px-4 py-2 flex flex-wrap gap-x-6 gap-y-1 justify-center sm:justify-between max-w-screen-xl mx-auto"
             style="border-color: var(--lp-border); background: var(--lp-surface); color: var(--lp-muted);">
            @if (data_get($lp, 'topBar.phone'))
                <span><span class="font-semibold" style="color: var(--lp-text);">{{ web_client_t('landing.contact.clinic_prefix') }}</span> <span class="landing-topbar-phone" dir="ltr">{{ data_get($lp, 'topBar.phone') }}</span></span>
            @endif
            @if (data_get($lp, 'topBar.emergency'))
                <span>{{ data_get($lp, 'topBar.emergency') }}</span>
            @endif
            @if (data_get($lp, 'topBar.hours'))
                <span>{{ data_get($lp, 'topBar.hours') }}</span>
            @endif
        </div>
    @endif

    <nav class="sticky top-0 z-50 border-b shadow-sm" style="background: color-mix(in srgb, var(--lp-surface) 92%, transparent); border-color: var(--lp-border); backdrop-filter: blur(8px);" aria-label="{{ web_client_t('landing.nav.aria_label') }}">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ route('welcome', ['lang' => app()->getLocale()]) }}" class="flex items-center gap-3 rtl:flex-row-reverse shrink-0">
                <img src="{{ $logoUrl }}" alt="{{ e(web_client_t('landing.brand.logo_alt', get_logo_alt())) }}" class="h-9 w-auto object-contain max-w-[10rem]" width="160" height="36" onerror="this.style.display='none'">
                <div class="flex flex-col {{ $isRtl ? 'text-right' : 'text-left' }} min-w-0">
                    <span class="self-center text-lg font-semibold whitespace-nowrap truncate" style="color: var(--lp-text);">{{ web_client_t('landing.brand.site_name', $companyName) }}</span>
                    <span class="text-xs font-medium truncate max-w-[14rem] sm:max-w-xs" style="color: var(--lp-muted);">{{ web_client_t('landing.brand.site_tagline', $companyTagline) }}</span>
                </div>
            </a>

            <button data-collapse-toggle="landing-navbar" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" style="color: var(--lp-muted);" aria-controls="landing-navbar" aria-expanded="false">
                <span class="sr-only">{{ web_client_t('landing.nav.menu_toggle') }}</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/></svg>
            </button>

            <div class="hidden w-full md:block md:w-auto" id="landing-navbar">
                <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border rounded-lg md:flex-row md:gap-1 md:mt-0 md:border-0 rtl:space-x-reverse md:rtl:space-x-reverse {{ $isRtl ? 'md:flex-row-reverse' : '' }}"
                    style="border-color: var(--lp-border); background: var(--lp-surface);">
                    @if ($lp && ! empty($lp['nav']))
                        @foreach ($lp['nav'] as $item)
                            @if (array_key_exists('is_visible', $item) && ! $item['is_visible'])
                                @continue
                            @endif
                            <li>
                                @if (! empty($item['is_cta']))
                                    <a href="{{ $item['href'] ?? '#' }}"
                                       class="inline-flex justify-center items-center text-white font-semibold rounded-lg text-sm px-4 py-2.5 w-full md:w-auto transition hover:brightness-105 focus:ring-4 focus:outline-none"
                                       style="background: var(--lp-primary); --tw-ring-color: color-mix(in srgb, var(--lp-primary) 35%, transparent);">
                                        {{ $item['label'] ?? '' }}
                                    </a>
                                @else
                                    <a href="{{ $item['href'] ?? '#' }}" class="block py-2 px-3 rounded md:p-2 hover:opacity-90 transition" style="color: var(--lp-muted);">{{ $item['label'] ?? '' }}</a>
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li>
                            <a href="#about" class="block py-2 px-3 rounded md:p-2 hover:opacity-90 transition" style="color: var(--lp-muted);">{{ web_client_t('landing.nav.about') }}</a>
                        </li>
                        <li>
                            <a href="#features" class="block py-2 px-3 rounded md:p-2 hover:opacity-90 transition" style="color: var(--lp-muted);">{{ web_client_t('landing.nav.features') }}</a>
                        </li>
                        <li>
                            <a href="#contact" class="block py-2 px-3 rounded md:p-2 hover:opacity-90 transition" style="color: var(--lp-muted);">{{ web_client_t('landing.nav.contact') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <section class="relative overflow-hidden border-b" style="border-color: var(--lp-border);">
            <div class="absolute inset-0 pointer-events-none opacity-40" aria-hidden="true"
                 style="background:
                    radial-gradient(ellipse 80% 55% at 20% 0%, color-mix(in srgb, var(--lp-primary) 28%, transparent), transparent 55%),
                    radial-gradient(ellipse 60% 50% at 85% 10%, color-mix(in srgb, var(--lp-secondary) 22%, transparent), transparent 50%);"></div>
            <div class="relative max-w-screen-xl mx-auto px-4 py-16 sm:py-24 lg:py-28">
                <div class="max-w-3xl {{ $isRtl ? 'text-right ms-auto' : 'text-left' }}">
                    <p class="text-sm font-semibold tracking-wide uppercase mb-3" style="color: var(--lp-primary);">{{ $txt(data_get($lp, 'hero.tagline'), 'landing.hero.tagline', $companyTagline) }}</p>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight mb-6" style="color: var(--lp-text);">
                        {{ $txt(data_get($lp, 'hero.headline'), 'landing.hero.headline') }}
                    </h1>
                    <p class="text-lg sm:text-xl leading-relaxed mb-10 max-w-2xl {{ $isRtl ? 'ms-auto' : '' }}" style="color: var(--lp-muted);">
                        {{ $txt(data_get($lp, 'hero.subhead'), 'landing.hero.subhead') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 {{ $isRtl ? 'sm:flex-row-reverse' : '' }}">
                        <a href="{{ data_get($lp, 'hero.cta_primary_href') ?: '#features' }}"
                           class="inline-flex justify-center items-center px-6 py-3.5 text-base font-semibold rounded-lg text-white shadow-md transition hover:brightness-105 focus:ring-4 focus:outline-none"
                           style="background: var(--lp-primary); --tw-ring-color: color-mix(in srgb, var(--lp-primary) 40%, transparent);">
                            {{ $txt(data_get($lp, 'hero.cta_primary'), 'landing.hero.cta_primary') }}
                        </a>
                        <a href="{{ data_get($lp, 'hero.cta_secondary_href') ?: '#about' }}"
                           class="inline-flex justify-center items-center px-6 py-3.5 text-base font-semibold rounded-lg border-2 transition hover:bg-black/5 focus:ring-4 focus:outline-none"
                           style="border-color: var(--lp-border); color: var(--lp-text); --tw-ring-color: color-mix(in srgb, var(--lp-border) 80%, transparent);">
                            {{ $txt(data_get($lp, 'hero.cta_secondary'), 'landing.hero.cta_secondary') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="py-16 sm:py-20 border-b" style="background: var(--lp-surface); border-color: var(--lp-border);">
            <div class="max-w-screen-lg mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                <h2 class="text-3xl font-bold mb-3" style="color: var(--lp-text);">{{ $txt(data_get($lp, 'about.title'), 'landing.about.title') }}</h2>
                <p class="text-lg font-medium mb-6" style="color: var(--lp-primary);">{{ $txt(data_get($lp, 'about.lead'), 'landing.about.lead') }}</p>
                <p class="text-base leading-relaxed max-w-3xl {{ $isRtl ? 'ms-auto' : '' }}" style="color: var(--lp-muted);">{{ $txt(data_get($lp, 'about.body'), 'landing.about.body') }}</p>
            </div>
        </section>

        @if ($lp && ! empty($lp['departments']))
            <section id="departments" class="py-16 sm:py-20 border-b" style="border-color: var(--lp-border); background: var(--lp-bg);">
                <div class="max-w-screen-xl mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <h2 class="text-3xl font-bold mb-8" style="color: var(--lp-text);">{{ web_client_t('landing.features.title') }}</h2>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($lp['departments'] as $card)
                            <a href="{{ $card['href'] ?? '#' }}" class="block max-w-sm p-6 rounded-xl border shadow-sm mx-auto md:mx-0 w-full transition hover:shadow-md" style="background: var(--lp-surface); border-color: var(--lp-border);">
                                @if (! empty($card['image']))
                                    <img src="{{ $card['image'] }}" alt="" class="w-full h-32 object-cover rounded-lg mb-4">
                                @endif
                                <h3 class="mb-2 text-xl font-semibold" style="color: var(--lp-text);">{{ $card['title'] ?? '' }}</h3>
                                <p class="text-sm" style="color: var(--lp-muted);">{{ $card['subtitle'] ?? '' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($lp && ! empty($lp['featuredDoctors']))
            <section id="doctors" class="py-16 sm:py-20 border-b" style="background: var(--lp-surface); border-color: var(--lp-border);">
                <div class="max-w-screen-xl mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <h2 class="text-3xl font-bold mb-8" style="color: var(--lp-text);">{{ web_client_t('landing.nav.features') }}</h2>
                    <div class="grid gap-8 md:grid-cols-2">
                        @foreach ($lp['featuredDoctors'] as $doc)
                            <div class="flex gap-4 p-6 rounded-xl border" style="border-color: var(--lp-border); background: var(--lp-bg);">
                                @if (! empty($doc['image']))
                                    <img src="{{ $doc['image'] }}" alt="" class="w-20 h-20 rounded-full object-cover shrink-0">
                                @endif
                                <div>
                                    <h3 class="text-lg font-semibold" style="color: var(--lp-text);">{{ $doc['title'] ?? '' }}</h3>
                                    <p class="text-sm mb-2" style="color: var(--lp-primary);">{{ $doc['subtitle'] ?? '' }}</p>
                                    @if (! empty($doc['href']))
                                        <a href="{{ $doc['href'] }}" class="text-sm font-semibold" style="color: var(--lp-primary);">{{ $doc['cta_label'] ?? 'Book' }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($lp && ! empty(data_get($lp, 'quickBooking.enabled')))
            <section id="appointments" class="py-12 border-b" style="border-color: var(--lp-border); background: var(--lp-bg);">
                <div class="max-w-screen-md mx-auto px-4 rounded-xl border p-8 text-center" style="border-color: var(--lp-border); background: var(--lp-surface);">
                    <h2 class="text-xl font-bold mb-2" style="color: var(--lp-text);">{{ data_get($lp, 'quickBooking.copy.title') }}</h2>
                    <p class="text-sm" style="color: var(--lp-muted);">{{ data_get($lp, 'quickBooking.copy.hint') }}</p>
                </div>
            </section>
        @endif

        <section id="features" class="py-16 sm:py-20 border-b" style="border-color: var(--lp-border); background: var(--lp-bg);">
            <div class="max-w-screen-xl mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-center' }}">
                <h2 class="text-3xl font-bold mb-2" style="color: var(--lp-text);">{{ $txt(data_get($lp, 'whyUs.title'), 'landing.features.title') }}</h2>
                <p class="text-lg mb-12 max-w-2xl {{ $isRtl ? 'ms-auto me-0' : 'mx-auto' }}" style="color: var(--lp-muted);">{{ $txt(data_get($lp, 'whyUs.subtitle'), 'landing.features.subtitle') }}</p>
                <div class="grid gap-8 md:grid-cols-3 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    @if ($lp && ! empty(data_get($lp, 'whyUs.items')))
                        @foreach (data_get($lp, 'whyUs.items', []) as $card)
                            <div class="max-w-sm p-6 rounded-xl border shadow-sm mx-auto md:mx-0 w-full transition hover:shadow-md" style="background: var(--lp-surface); border-color: var(--lp-border);">
                                <div class="w-12 h-12 rounded-lg mb-4 flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, var(--lp-primary), var(--lp-secondary));">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="mb-2 text-xl font-semibold tracking-tight" style="color: var(--lp-text);">{{ $card['title'] ?? '' }}</h3>
                                <p class="font-normal leading-relaxed" style="color: var(--lp-muted);">{{ $card['body'] ?? '' }}</p>
                            </div>
                        @endforeach
                    @else
                        @foreach (['card1', 'card2', 'card3'] as $card)
                            <div class="max-w-sm p-6 rounded-xl border shadow-sm mx-auto md:mx-0 w-full transition hover:shadow-md" style="background: var(--lp-surface); border-color: var(--lp-border);">
                                <div class="w-12 h-12 rounded-lg mb-4 flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, var(--lp-primary), var(--lp-secondary));">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="mb-2 text-xl font-semibold tracking-tight" style="color: var(--lp-text);">{{ web_client_t('landing.features.'.$card.'_title') }}</h3>
                                <p class="font-normal leading-relaxed" style="color: var(--lp-muted);">{{ web_client_t('landing.features.'.$card.'_body') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        @if ($lp && ! empty($lp['testimonials']))
            <section class="py-16 sm:py-20 border-b" style="border-color: var(--lp-border); background: var(--lp-surface);">
                <div class="max-w-screen-lg mx-auto px-4 space-y-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    @foreach ($lp['testimonials'] as $quote)
                        <blockquote class="border-s-4 ps-4" style="border-color: var(--lp-primary);">
                            <p class="text-lg italic mb-2" style="color: var(--lp-text);">{{ $quote['body'] ?? '' }}</p>
                            <cite class="text-sm not-italic font-semibold" style="color: var(--lp-muted);">{{ $quote['title'] ?? '' }}</cite>
                        </blockquote>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($landingBlogPosts->isNotEmpty())
            <section id="blog" class="py-16 sm:py-20 border-b" style="border-color: var(--lp-border); background: var(--lp-bg);">
                <div class="max-w-screen-xl mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <h2 class="text-2xl font-bold mb-6" style="color: var(--lp-text);">{{ get_translation('website_blog') }}</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($landingBlogPosts as $post)
                            @php $btr = $post->translation(); @endphp
                            @if ($btr && $btr->title !== '')
                            <a href="{{ route('blog.show', ['lang' => app()->getLocale(), 'slug' => $post->slug]) }}" class="block p-6 rounded-xl border transition hover:shadow-md overflow-hidden" style="border-color: var(--lp-border); background: var(--lp-surface);">
                                @if (is_array($post->images) && isset($post->images[0]) && $post->images[0] !== '')
                                    <div class="mb-4 -mx-6 -mt-6">
                                        <img src="{{ Str::startsWith($post->images[0], ['http://', 'https://']) ? $post->images[0] : asset($post->images[0]) }}" alt="" class="w-full h-44 object-cover" loading="lazy">
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold mb-2" style="color: var(--lp-text);">{{ $btr->title }}</h3>
                                <p class="text-sm mb-3" style="color: var(--lp-muted);">{{ Str::limit(strip_tags($btr->excerpt ?: $btr->body ?? ''), 160) }}</p>
                                <span class="text-sm font-semibold" style="color: var(--lp-primary);">{{ get_translation('blog_public_read_more') }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="py-16 sm:py-20" style="background: linear-gradient(135deg, color-mix(in srgb, var(--lp-primary) 12%, var(--lp-bg)), color-mix(in srgb, var(--lp-secondary) 10%, var(--lp-bg)));">
            <div class="max-w-screen-md mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-4" style="color: var(--lp-text);">{{ $txt(data_get($lp, 'cta.title'), 'landing.cta.title') }}</h2>
                <p class="text-lg mb-8" style="color: var(--lp-muted);">{{ $txt(data_get($lp, 'cta.body'), 'landing.cta.body') }}</p>
                @if (Route::has('admin.login'))
                    <a href="{{ route_with_lang('admin.login') }}"
                       class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold rounded-lg text-white shadow-lg transition hover:brightness-105 focus:ring-4 focus:outline-none"
                       style="background: var(--lp-primary); --tw-ring-color: color-mix(in srgb, var(--lp-primary) 40%, transparent);">
                        {{ $txt(data_get($lp, 'cta.button'), 'landing.cta.button') }}
                    </a>
                @endif
            </div>
        </section>

        <section id="contact" class="py-12 border-t" style="background: var(--lp-surface); border-color: var(--lp-border);">
            <div class="max-w-screen-lg mx-auto px-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 {{ $isRtl ? 'sm:flex-row-reverse text-right' : 'text-left' }}">
                <div>
                    <h2 class="text-xl font-bold mb-1" style="color: var(--lp-text);">{{ $txt(data_get($lp, 'contact.title'), 'landing.contact.title') }}</h2>
                    <p class="text-sm" style="color: var(--lp-muted);">{{ $txt(data_get($lp, 'contact.body'), 'landing.contact.body') }}</p>
                </div>
                <div class="flex flex-wrap gap-3 {{ $isRtl ? 'justify-end' : 'justify-start' }}">
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border" style="border-color: var(--lp-border); color: var(--lp-muted);">
                        <span class="font-semibold" style="color: var(--lp-text);">{{ $txt(data_get($lp, 'contact.cta'), 'landing.contact.clinic_prefix') }}</span>
                        <span class="mx-1.5 opacity-50" aria-hidden="true">·</span>
                        <span>{{ web_client_t('landing.brand.site_name', $companyName) }}</span>
                    </span>
                </div>
            </div>
        </section>
    </main>

    <footer class="mt-auto py-6 border-t text-center text-xs px-4" style="border-color: var(--lp-border); color: var(--lp-muted); background: var(--lp-bg);">
        {{ $txt(data_get($lp, 'footer.line'), 'app.hint') }}
    </footer>

    <div id="landing-settings-dialog" class="landing-settings-dialog" role="dialog" aria-modal="true" aria-labelledby="landing-settings-title" aria-hidden="true">
        <div class="landing-settings-dialog-backdrop" data-landing-settings-close></div>
        <div class="landing-settings-panel {{ $isRtl ? 'text-right' : 'text-left' }}">
            <div class="landing-settings-panel-accent" aria-hidden="true"></div>
            <div class="landing-settings-panel-inner">
                <div class="landing-settings-header">
                    <div>
                        <p class="landing-settings-kicker">{{ get_translation('landing_fp_kicker') }}</p>
                        <h2 id="landing-settings-title" class="landing-settings-title">{{ get_translation('landing_fp_title') }}</h2>
                        <p class="landing-settings-sub">{{ get_translation('landing_fp_subtitle') }}</p>
                    </div>
                    <button type="button" class="landing-settings-close" data-landing-settings-close aria-label="{{ get_translation('landing_fp_close') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="landing-settings-body">
                    <div class="landing-settings-block">
                        <p class="landing-settings-block-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            {{ get_translation('landing_fp_language') }}
                        </p>
                        @if (count($webLocaleCodes) > 1)
                            <div class="landing-settings-lang-row {{ $isRtl ? 'justify-end' : '' }}">
                                @foreach ($webLocaleCodes as $code)
                                    @php
                                        $info = get_language_info($code) ?? [];
                                        $label = $info['native'] ?? $info['name'] ?? strtoupper($code);
                                    @endphp
                                    <a href="{{ route('welcome', ['lang' => $code]) }}"
                                       hreflang="{{ $code }}"
                                       class="landing-settings-chip landing-lang-welcome {{ app()->getLocale() === $code ? 'is-current' : '' }}"
                                       @if (app()->getLocale() !== $code) title="{{ get_translation('landing_fp_lang_loading') }}" @endif>
                                        <span>{{ $label }}</span>
                                        <span class="landing-lang-nav-spinner" aria-hidden="true"></span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="landing-settings-phone-empty">{{ strtoupper(app()->getLocale()) }}</p>
                        @endif
                    </div>

                    <div class="landing-settings-block">
                        <p class="landing-settings-block-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                            </svg>
                            {{ get_translation('landing_fp_theme') }}
                        </p>
                        <div class="landing-settings-segmented"
                             role="group"
                             aria-label="{{ get_translation('landing_fp_theme') }}"
                             data-landing-theme-url="{{ route('welcome.theme', ['lang' => app()->getLocale()]) }}">
                            <button type="button"
                                    class="landing-settings-theme-option {{ $theme === 'light' ? 'is-active' : '' }}"
                                    data-landing-set-theme="light">
                                {{ get_translation('landing_fp_light') }}
                            </button>
                            <button type="button"
                                    class="landing-settings-theme-option {{ $theme === 'dark' ? 'is-active' : '' }}"
                                    data-landing-set-theme="dark">
                                {{ get_translation('landing_fp_dark') }}
                            </button>
                        </div>
                        <p class="landing-settings-theme-hint">{{ get_translation('landing_fp_theme_saved_hint') }}</p>
                    </div>

                    <div class="landing-settings-block">
                        <p class="landing-settings-block-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            {{ get_translation('landing_fp_contact') }}
                        </p>
                        @if ($contactPhone !== '')
                            <div class="landing-settings-phone-card landing-settings-ltr" dir="ltr">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <a href="tel:{{ e($telHref) }}" class="landing-settings-phone-link">{{ e($contactPhone) }}</a>
                            </div>
                        @else
                            <p class="landing-settings-phone-empty">{{ get_translation('landing_fp_phone_unavailable') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" id="landing-settings-fab" class="landing-settings-fab" aria-expanded="false" aria-controls="landing-settings-dialog" title="{{ get_translation('landing_fp_open') }}">
        <span class="sr-only">{{ get_translation('landing_fp_open') }}</span>
        <svg class="landing-settings-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
    </button>

    <script>
    (function () {
        var fab = document.getElementById('landing-settings-fab');
        var dlg = document.getElementById('landing-settings-dialog');

        if (fab && dlg) {
            function isOpen() {
                return dlg.classList.contains('is-open');
            }

            function openDialog() {
                dlg.classList.add('is-open');
                dlg.setAttribute('aria-hidden', 'false');
                fab.classList.add('is-open');
                fab.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeDialog() {
                dlg.classList.remove('is-open');
                dlg.setAttribute('aria-hidden', 'true');
                fab.classList.remove('is-open');
                fab.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            fab.addEventListener('click', function () {
                if (isOpen()) {
                    closeDialog();
                } else {
                    openDialog();
                }
            });

            dlg.querySelectorAll('[data-landing-settings-close]').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeDialog();
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isOpen()) {
                    closeDialog();
                }
            });
        }

        document.querySelectorAll('a.landing-lang-welcome[hreflang]').forEach(function (a) {
            a.addEventListener('click', function () {
                if (a.classList.contains('is-current')) {
                    return;
                }
                a.classList.add('is-navigating');
                a.setAttribute('aria-busy', 'true');
            });
        });

        var themeSeg = document.querySelector('[data-landing-theme-url]');
        if (themeSeg) {
            var themeUrl = themeSeg.getAttribute('data-landing-theme-url');
            var csrfEl = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfEl ? csrfEl.getAttribute('content') : '';

            themeSeg.querySelectorAll('[data-landing-set-theme]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var next = btn.getAttribute('data-landing-set-theme');
                    var html = document.documentElement;
                    var prev = html.getAttribute('data-landing-theme');
                    if (!next || next === prev) {
                        return;
                    }

                    html.setAttribute('data-landing-theme', next);
                    themeSeg.querySelectorAll('[data-landing-set-theme]').forEach(function (b) {
                        b.classList.toggle('is-active', b.getAttribute('data-landing-set-theme') === next);
                    });

                    btn.classList.add('is-pending');
                    fetch(themeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ theme: next })
                    }).then(function (r) {
                        if (!r.ok) {
                            throw new Error('theme');
                        }
                        return r.json();
                    }).catch(function () {
                        html.setAttribute('data-landing-theme', prev);
                        themeSeg.querySelectorAll('[data-landing-set-theme]').forEach(function (b) {
                            b.classList.toggle('is-active', b.getAttribute('data-landing-set-theme') === prev);
                        });
                    }).finally(function () {
                        btn.classList.remove('is-pending');
                    });
                });
            });
        }
    })();
    </script>
</body>
</html>
