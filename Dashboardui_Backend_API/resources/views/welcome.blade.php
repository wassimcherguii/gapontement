@php
    $lp = landing_page_payload('home');
    $webCatalog = app(\App\Services\TranslationPublishService::class)->readLanguages('web', false);
    $webLocaleCodes = array_keys($webCatalog['supported'] ?? []);
    $isRtl = is_rtl_language(app()->getLocale());
    $theme = session('theme', 'light');
    $palette = get_colors($theme);
    $brand = $palette['brand'] ?? [];
    $usage = $palette['usage'] ?? [];
    $companyName = get_company_name();
    $companyTagline = get_company_tagline();
    $logoUrl = asset_logo();
    $faviconPath = get_favicon_path();
    $txt = function (?string $fromJson, string $webKey, ?string $fallback = null) {
        if (is_string($fromJson) && $fromJson !== '') {
            return $fromJson;
        }

        return web_client_t($webKey, $fallback);
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        :root {
            --lp-primary: {{ $brand['primary'] ?? '#0F4C81' }};
            --lp-primary-dark: {{ $brand['primary-dark'] ?? '#0B355A' }};
            --lp-primary-light: {{ $brand['primary-light'] ?? '#3A7CA5' }};
            --lp-primary-hover: {{ $brand['primary-hover'] ?? '#0D3F6B' }};
            --lp-secondary: {{ $brand['secondary'] ?? '#14B8A6' }};
            --lp-accent: {{ $brand['accent'] ?? '#22D3EE' }};
            --lp-bg: {{ $usage['background'] ?? '#F8FAFC' }};
            --lp-surface: {{ $usage['surface'] ?? '#ffffff' }};
            --lp-text: {{ $usage['text'] ?? '#0f172a' }};
            --lp-muted: {{ $usage['text-secondary'] ?? '#475569' }};
            --lp-border: {{ $usage['border'] ?? '#e2e8f0' }};
        }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .landing-lang-link:hover { background: color-mix(in srgb, var(--lp-primary) 8%, var(--lp-surface)) !important; }
        .landing-lang-link.is-current { background: color-mix(in srgb, var(--lp-primary) 12%, var(--lp-surface)) !important; font-weight: 600; }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-[var(--lp-bg)] text-[var(--lp-text)]" style="color: var(--lp-text);">

    @if ($lp && (data_get($lp, 'topBar.phone') || data_get($lp, 'topBar.emergency') || data_get($lp, 'topBar.hours')))
        <div class="text-xs sm:text-sm border-b px-4 py-2 flex flex-wrap gap-x-6 gap-y-1 justify-center sm:justify-between max-w-screen-xl mx-auto"
             style="border-color: var(--lp-border); background: var(--lp-surface); color: var(--lp-muted);">
            @if (data_get($lp, 'topBar.phone'))
                <span><span class="font-semibold" style="color: var(--lp-text);">{{ web_client_t('landing.contact.clinic_prefix') }}</span> {{ data_get($lp, 'topBar.phone') }}</span>
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
                    @if (count($webLocaleCodes) > 1)
                        <li class="relative py-2 md:py-0 md:px-1 w-full md:w-auto">
                            <button id="landing-lang-dropdown-btn"
                                    data-dropdown-toggle="landing-lang-dropdown"
                                    data-dropdown-placement="{{ $isRtl ? 'bottom-end' : 'bottom-start' }}"
                                    type="button"
                                    class="inline-flex items-center justify-center gap-2 w-full md:w-auto py-2.5 px-3 text-sm font-semibold rounded-lg border transition hover:opacity-95 focus:ring-4 focus:outline-none"
                                    style="border-color: var(--lp-border); color: var(--lp-text); background: var(--lp-surface); --tw-ring-color: color-mix(in srgb, var(--lp-primary) 30%, transparent);"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    aria-label="{{ web_client_t('landing.nav.language_menu') }}">
                                @php
                                    $cur = get_language_info(app()->getLocale()) ?? [];
                                    $curLabel = $cur['native'] ?? $cur['name'] ?? strtoupper(app()->getLocale());
                                @endphp
                                <span>{{ $curLabel }}</span>
                                <svg class="w-4 h-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <div id="landing-lang-dropdown"
                                 class="z-50 hidden w-52 divide-y rounded-lg border shadow-lg"
                                 style="background: var(--lp-surface); border-color: var(--lp-border); divide-color: var(--lp-border);">
                                <ul class="py-1 text-sm" role="listbox" aria-label="{{ web_client_t('landing.nav.language_menu') }}">
                                    @foreach ($webLocaleCodes as $code)
                                        @php
                                            $info = get_language_info($code) ?? [];
                                            $label = $info['native'] ?? $info['name'] ?? strtoupper($code);
                                        @endphp
                                        <li role="option" aria-selected="{{ app()->getLocale() === $code ? 'true' : 'false' }}">
                                            <a href="{{ route('welcome', ['lang' => $code]) }}"
                                               hreflang="{{ $code }}"
                                               class="landing-lang-link block px-4 py-2.5 transition rounded-md mx-1 {{ app()->getLocale() === $code ? 'is-current' : '' }}"
                                               style="color: var(--lp-text);">
                                                {{ $label }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Route::has('admin.login'))
                        <li class="md:ms-1">
                            <a href="{{ route_with_lang('admin.login') }}"
                               class="inline-flex items-center justify-center text-white font-semibold rounded-lg text-sm px-4 py-2.5 w-full md:w-auto transition hover:brightness-105 focus:ring-4 focus:outline-none"
                               style="background: var(--lp-primary); --tw-ring-color: color-mix(in srgb, var(--lp-primary) 35%, transparent);">
                                {{ web_client_t('landing.nav.login') }}
                            </a>
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

        @if ($lp && ! empty($lp['blog']))
            <section id="blog" class="py-16 sm:py-20 border-b" style="border-color: var(--lp-border); background: var(--lp-bg);">
                <div class="max-w-screen-xl mx-auto px-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <h2 class="text-2xl font-bold mb-6" style="color: var(--lp-text);">{{ get_translation('website_blog') }}</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($lp['blog'] as $post)
                            <a href="{{ $post['href'] ?? '#' }}" class="block p-6 rounded-xl border transition hover:shadow-md" style="border-color: var(--lp-border); background: var(--lp-surface);">
                                <h3 class="text-lg font-semibold mb-2" style="color: var(--lp-text);">{{ $post['title'] ?? '' }}</h3>
                                <p class="text-sm mb-3" style="color: var(--lp-muted);">{{ Str::limit(strip_tags($post['body'] ?? ''), 140) }}</p>
                                <span class="text-sm font-semibold" style="color: var(--lp-primary);">{{ $post['cta_label'] ?? '' }}</span>
                            </a>
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
</body>
</html>
