@extends('layouts.admin')

@section('title', get_translation($titleKey))
@section('description', get_translation('website_section_intro'))

@section('content')
@php
    $locales = $locales ?? ['en', 'fr', 'ar'];
    $section = fn (string $key) => $page->sections->firstWhere('section_key', $key);
    $trContent = function (?\App\Models\LandingSection $sec, string $loc): array {
        if (! $sec) {
            return [];
        }
        $t = $sec->translations->firstWhere('locale', $loc);

        return is_array($t?->content) ? $t->content : [];
    };
    $qbSection = $section('quick_booking');
    $qbEnabled = (bool) data_get($qbSection?->settings, 'enabled', false);
    $localeLabel = function (string $loc): string {
        $info = get_language_info($loc) ?? [];

        return (string) ($info['native'] ?? $info['name'] ?? strtoupper($loc));
    };
    $cmsField = fn (string $key): string => get_translation('landing_cms_field_'.$key);
    $cmsSection = fn (string $slug): string => get_translation('landing_cms_section_'.$slug);
    $cmsBlock = function (string $sectionKey): string {
        $k = 'landing_cms_block_'.$sectionKey;
        $fullKey = 'messages.'.$k;
        $t = __($fullKey);

        return $t === $fullKey ? str_replace('_', ' ', $sectionKey) : $t;
    };
    $entityTypeLabel = function (string $type): string {
        $k = 'landing_cms_entity_type_'.$type;
        $fullKey = 'messages.'.$k;
        $t = __($fullKey);

        return $t === $fullKey ? $type : $t;
    };
    $jsonByLocale = $jsonByLocale ?? [];
    $jsonBundlesByLocale = $jsonBundlesByLocale ?? [];
@endphp

<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
        {{ get_translation($titleKey) }}
    </h1>
    <p class="mt-2 text-sm max-w-2xl {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
        {{ get_translation('website_section_intro') }}
    </p>
    <div class="mt-4 flex flex-wrap gap-2 border-b pb-2" style="border-color: var(--border-color);" role="tablist">
        <button type="button" id="landing-tab-btn-db" class="landing-tab-btn rounded-lg px-4 py-2 text-sm font-semibold transition"
                style="background: var(--primary-color, #0F4C81); color: #fff;" aria-selected="true">
            {{ get_translation('landing_cms_tab_database') }}
        </button>
        <button type="button" id="landing-tab-btn-json" class="landing-tab-btn rounded-lg px-4 py-2 text-sm font-semibold border transition"
                style="border-color: var(--border-color); color: var(--text-color); background: transparent;" aria-selected="false">
            {{ get_translation('landing_cms_tab_json') }}
        </button>
    </div>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
        {{ session('status') }}
    </div>
@endif
@if (session('landing_publish_checksum'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm font-mono break-all" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-secondary-color);">
        {{ get_translation('landing_cms_checksum', ['checksum' => session('landing_publish_checksum')]) }}
    </div>
@endif

<div id="landing-panel-db" class="space-y-8">
<form id="landing-db-save-form" method="post" action="{{ route_with_lang('admin.website.landing.save') }}" class="space-y-8">
    @csrf
    <div class="admin-card rounded-xl p-4 sm:p-5 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <p class="text-sm max-w-3xl {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('landing_cms_db_save_reminder') }}
        </p>
        <div class="flex flex-wrap gap-2 {{ is_rtl_language(app()->getLocale()) ? 'justify-end' : 'justify-start' }}">
            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105" style="background: var(--primary-color, #0F4C81);">
                {{ get_translation('save') }}
            </button>
            <button type="submit"
                    formaction="{{ route_with_lang('admin.website.landing.sync_db_to_json') }}?return_tab=db"
                    formmethod="POST"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                    style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                {{ get_translation('landing_cms_sync_db_to_json') }}
            </button>
            <button type="submit"
                    formaction="{{ route_with_lang('admin.website.landing.import_json_files') }}?return_tab=db"
                    formmethod="POST"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                    style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                {{ get_translation('landing_cms_btn_import_json_files') }}
            </button>
        </div>
    </div>
    <input type="hidden" name="quick_booking_enabled" value="0">
    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_seo_section') }}</h2>
        @foreach ($locales as $loc)
            @php $pl = $page->locales->firstWhere('locale', $loc); @endphp
            <div class="grid gap-3 sm:grid-cols-2 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium sm:col-span-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                <label class="block text-sm">
                    <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $cmsField('meta_title') }}</span>
                    <input type="text" name="page_meta[{{ $loc }}][meta_title]" value="{{ old('page_meta.'.$loc.'.meta_title', $pl?->meta_title) }}"
                           class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </label>
                <label class="block text-sm sm:col-span-2">
                    <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $cmsField('meta_description') }}</span>
                    <textarea name="page_meta[{{ $loc }}][meta_description]" rows="2"
                              class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">{{ old('page_meta.'.$loc.'.meta_description', $pl?->meta_description) }}</textarea>
                </label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_top_bar_section') }}</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('top_bar'), $loc); @endphp
            <div class="grid gap-3 sm:grid-cols-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium sm:col-span-3 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('phone') }}</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][phone]" value="{{ old('content.top_bar.'.$loc.'.phone', $c['phone'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('emergency') }}</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][emergency]" value="{{ old('content.top_bar.'.$loc.'.emergency', $c['emergency'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('hours') }}</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][hours]" value="{{ old('content.top_bar.'.$loc.'.hours', $c['hours'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_hero_section') }}</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('hero'), $loc); @endphp
            <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                @foreach (['tagline', 'headline', 'subhead', 'cta_primary', 'cta_primary_href', 'cta_secondary', 'cta_secondary_href'] as $field)
                    <label class="block text-sm">
                        <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField($field) }}</span>
                        <input type="text" name="content[hero][{{ $loc }}][{{ $field }}]" value="{{ old('content.hero.'.$loc.'.'.$field, $c[$field] ?? '') }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>

    @foreach (['about' => ['title', 'lead', 'body'], 'contact' => ['title', 'body', 'cta'], 'cta' => ['title', 'body', 'button'], 'footer' => ['line']] as $sk => $fields)
        <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h2 class="text-lg font-semibold capitalize {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ $cmsSection($sk) }}</h2>
            @foreach ($locales as $loc)
                @php $c = $trContent($section($sk), $loc); @endphp
                <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                    @foreach ($fields as $field)
                        <label class="block text-sm">
                            <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField($field) }}</span>
                            @if ($field === 'body' || $field === 'lead')
                                <textarea name="content[{{ $sk }}][{{ $loc }}][{{ $field }}]" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">{{ old('content.'.$sk.'.'.$loc.'.'.$field, $c[$field] ?? '') }}</textarea>
                            @else
                                <input type="text" name="content[{{ $sk }}][{{ $loc }}][{{ $field }}]" value="{{ old('content.'.$sk.'.'.$loc.'.'.$field, $c[$field] ?? '') }}"
                                       class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">
                            @endif
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_why_us_section') }}</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('why_us'), $loc); @endphp
            <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('title') }}</span>
                    <input type="text" name="content[why_us][{{ $loc }}][title]" value="{{ old('content.why_us.'.$loc.'.title', $c['title'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('subtitle') }}</span>
                    <input type="text" name="content[why_us][{{ $loc }}][subtitle]" value="{{ old('content.why_us.'.$loc.'.subtitle', $c['subtitle'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_quick_booking_section') }}</h2>
        <label class="inline-flex items-center gap-2 text-sm cursor-pointer {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <input type="checkbox" name="quick_booking_enabled" value="1" class="rounded border" @checked(old('quick_booking_enabled', $qbEnabled))>
            <span style="color: var(--text-color);">{{ get_translation('enabled') }}</span>
        </label>
        @foreach ($locales as $loc)
            @php $c = $trContent($qbSection, $loc); @endphp
            <div class="grid gap-3 border-t pt-4" style="border-color: var(--border-color);">
                <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $localeLabel($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('title') }}</span>
                    <input type="text" name="content[quick_booking][{{ $loc }}][title]" value="{{ old('content.quick_booking.'.$loc.'.title', $c['title'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('hint') }}</span>
                    <input type="text" name="content[quick_booking][{{ $loc }}][hint]" value="{{ old('content.quick_booking.'.$loc.'.hint', $c['hint'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-6 overflow-x-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_navigation_section') }}</h2>
        @foreach ($page->navItems as $nav)
            <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                <p class="text-xs font-mono {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">#{{ $nav->id }}</p>
                <input type="hidden" name="nav[{{ $nav->id }}][id]" value="{{ $nav->id }}">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('href') }}</span>
                        <input type="text" name="nav[{{ $nav->id }}][href]" value="{{ old('nav.'.$nav->id.'.href', $nav->href) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                    <label class="inline-flex items-center gap-2 text-sm {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}"><input type="hidden" name="nav[{{ $nav->id }}][is_visible]" value="0"><input type="checkbox" name="nav[{{ $nav->id }}][is_visible]" value="1" @checked(old('nav.'.$nav->id.'.is_visible', $nav->is_visible))> {{ get_translation('landing_cms_nav_visible') }}</label>
                    <label class="inline-flex items-center gap-2 text-sm {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}"><input type="hidden" name="nav[{{ $nav->id }}][is_cta]" value="0"><input type="checkbox" name="nav[{{ $nav->id }}][is_cta]" value="1" @checked(old('nav.'.$nav->id.'.is_cta', $nav->is_cta))> {{ get_translation('landing_cms_nav_cta') }}</label>
                </div>
                @foreach ($locales as $loc)
                    @php $nl = $nav->translations->firstWhere('locale', $loc); @endphp
                    <label class="block text-sm">
                        <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('landing_cms_nav_label', ['locale' => $localeLabel($loc)]) }}</span>
                        <input type="text" name="nav[{{ $nav->id }}][labels][{{ $loc }}]" value="{{ old('nav.'.$nav->id.'.labels.'.$loc, $nl?->label) }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>

    @foreach ($page->sections as $sec)
        @if ($sec->entities->isEmpty())
            @continue
        @endif
        <div class="admin-card rounded-xl p-6 sm:p-8 space-y-6 overflow-x-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_entities_heading', ['section' => $cmsBlock($sec->section_key)]) }}</h2>
            @foreach ($sec->entities as $ent)
                <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                    <p class="text-xs font-mono {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $entityTypeLabel($ent->type) }} #{{ $ent->id }}</p>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('sort_order') }}</span>
                            <input type="number" name="entities[{{ $ent->id }}][sort_order]" value="{{ old('entities.'.$ent->id.'.sort_order', $ent->sort_order) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('slug') }}</span>
                            <input type="text" name="entities[{{ $ent->id }}][slug]" value="{{ old('entities.'.$ent->id.'.slug', $ent->slug) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('image_path') }}</span>
                            <input type="text" name="entities[{{ $ent->id }}][image_path]" value="{{ old('entities.'.$ent->id.'.image_path', $ent->image_path) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('href') }}</span>
                            <input type="text" name="entities[{{ $ent->id }}][href]" value="{{ old('entities.'.$ent->id.'.href', $ent->href) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                    </div>
                    <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('landing_cms_phase2_user_id') }}: {{ $ent->user_id ?? '—' }}</p>
                    @foreach ($locales as $loc)
                        @php $et = $ent->translations->firstWhere('locale', $loc); @endphp
                        <div class="grid gap-2 border-t pt-3" style="border-color: var(--border-color);">
                            <p class="text-xs font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $localeLabel($loc) }}</p>
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][title]" placeholder="{{ get_translation('landing_cms_placeholder_title') }}" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.title', $et?->title) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][subtitle]" placeholder="{{ get_translation('landing_cms_placeholder_subtitle') }}" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.subtitle', $et?->subtitle) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                            <textarea name="entities[{{ $ent->id }}][t][{{ $loc }}][body]" rows="2" placeholder="{{ get_translation('landing_cms_placeholder_body') }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">{{ old('entities.'.$ent->id.'.t.'.$loc.'.body', $et?->body) }}</textarea>
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][cta_label]" placeholder="{{ get_translation('landing_cms_placeholder_cta_label') }}" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.cta_label', $et?->cta_label) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="flex flex-wrap gap-2 {{ is_rtl_language(app()->getLocale()) ? 'justify-end' : 'justify-start' }}">
        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105" style="background: var(--primary-color, #0F4C81);">
            {{ get_translation('save') }}
        </button>
        <button type="submit"
                formaction="{{ route_with_lang('admin.website.landing.sync_db_to_json') }}?return_tab=db"
                formmethod="POST"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
            {{ get_translation('landing_cms_sync_db_to_json') }}
        </button>
        <button type="submit"
                formaction="{{ route_with_lang('admin.website.landing.import_json_files') }}?return_tab=db"
                formmethod="POST"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
            {{ get_translation('landing_cms_btn_import_json_files') }}
        </button>
    </div>
</form>
</div>

<div id="landing-panel-json" class="hidden space-y-6">
    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-3" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        @if ($errors->any())
            <div class="rounded-lg border px-3 py-2 text-sm text-red-700" style="border-color: var(--border-color); background: color-mix(in srgb, red 8%, transparent);">
                <ul class="list-disc ms-4 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('landing_cms_json_intro') }}
        </p>
        <form id="landing-json-save-disk-form" method="post" action="{{ route_with_lang('admin.website.landing.save_json_cache') }}" class="hidden">
            @csrf
            <input type="hidden" name="locale" id="landing-json-save-locale" value="">
            <textarea name="json_payload" id="landing-json-save-payload" class="hidden" rows="1" cols="1" aria-hidden="true"></textarea>
        </form>
        <form id="landing-json-import-editor-form" method="post" action="{{ route_with_lang('admin.website.landing.import_json') }}?return_tab=json" class="hidden">
            @csrf
            <input type="hidden" name="locale" id="landing-json-import-locale" value="">
            <textarea name="json_payload" id="landing-json-import-payload" class="hidden" rows="1" cols="1" aria-hidden="true"></textarea>
        </form>
        <form id="landing-json-sync-db-form" method="post" action="{{ route_with_lang('admin.website.landing.sync_db_to_json') }}?return_tab=json" class="hidden">
            @csrf
        </form>
        <div class="flex flex-wrap gap-2 {{ is_rtl_language(app()->getLocale()) ? 'justify-end' : 'justify-start' }}">
            <button type="button" id="landing-json-btn-save-file"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105"
                    style="background: var(--primary-color, #0F4C81);">
                {{ get_translation('landing_cms_btn_save_json_file') }}
            </button>
            <button type="button" id="landing-json-btn-import-db"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                    style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                {{ get_translation('landing_cms_btn_import_editor_to_db') }}
            </button>
            <button type="submit" form="landing-json-sync-db-form"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold border transition hover:opacity-95"
                    style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                {{ get_translation('landing_cms_sync_db_to_json') }}
            </button>
        </div>
        <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('landing_cms_json_toolbar_hint') }}
        </p>
        <div class="flex flex-wrap gap-2" role="tablist" aria-label="{{ get_translation('landing_cms_json_locale_tabs') }}">
            @foreach ($locales as $loc)
                <button type="button" class="landing-json-locale-btn rounded-lg px-3 py-1.5 text-xs font-semibold border transition {{ $loop->first ? '' : 'opacity-70' }}"
                        style="border-color: var(--border-color); color: var(--text-color);"
                        data-json-locale="{{ $loc }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $localeLabel($loc) }}
                </button>
            @endforeach
        </div>
        @foreach ($locales as $loc)
            <div class="landing-json-pane space-y-4 {{ $loop->first ? '' : 'hidden' }}" data-json-pane="{{ $loc }}">
                <div class="space-y-4">
                    @include('admin.website.partials.landing-json-form', [
                        'loc' => $loc,
                        'bundle' => $jsonBundlesByLocale[$loc] ?? [],
                        'localeLabel' => $localeLabel,
                        'cmsField' => $cmsField,
                        'cmsSection' => $cmsSection,
                        'cmsBlock' => $cmsBlock,
                    ])
                    <details class="rounded-lg border px-3 py-2" style="border-color: var(--border-color);">
                        <summary class="cursor-pointer text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                            {{ get_translation('landing_cms_json_raw_toggle') }}
                        </summary>
                        <label class="mt-3 block text-xs font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                            {{ get_translation('landing_cms_json_editor_label', ['locale' => $localeLabel($loc)]) }}
                        </label>
                        <textarea name="json_payload_display_only" rows="18" dir="ltr"
                                  class="landing-json-textarea mt-1 w-full rounded-lg border p-3 font-mono text-xs leading-relaxed"
                                  style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">{{ old('locale') === $loc ? old('json_payload', $jsonByLocale[$loc] ?? '{}') : ($jsonByLocale[$loc] ?? '{}') }}</textarea>
                    </details>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
(function () {
    var dbBtn = document.getElementById('landing-tab-btn-db');
    var jsonBtn = document.getElementById('landing-tab-btn-json');
    var panelDb = document.getElementById('landing-panel-db');
    var panelJson = document.getElementById('landing-panel-json');
    if (!dbBtn || !jsonBtn || !panelDb || !panelJson) return;
    function styleActive(btn) {
        btn.style.background = 'var(--primary-color, #0F4C81)';
        btn.style.color = '#fff';
        btn.style.border = '1px solid transparent';
    }
    function styleInactive(btn) {
        btn.style.background = 'transparent';
        btn.style.color = 'var(--text-color)';
        btn.style.border = '1px solid var(--border-color, #e2e8f0)';
    }
    function showDb() {
        panelDb.classList.remove('hidden');
        panelJson.classList.add('hidden');
        dbBtn.setAttribute('aria-selected', 'true');
        jsonBtn.setAttribute('aria-selected', 'false');
        styleActive(dbBtn);
        styleInactive(jsonBtn);
    }
    function showJson() {
        panelDb.classList.add('hidden');
        panelJson.classList.remove('hidden');
        jsonBtn.setAttribute('aria-selected', 'true');
        dbBtn.setAttribute('aria-selected', 'false');
        styleActive(jsonBtn);
        styleInactive(dbBtn);
    }
    dbBtn.addEventListener('click', showDb);
    jsonBtn.addEventListener('click', showJson);
    document.querySelectorAll('.landing-json-locale-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var loc = btn.getAttribute('data-json-locale');
            document.querySelectorAll('.landing-json-locale-btn').forEach(function (b) {
                b.classList.toggle('opacity-70', b.getAttribute('data-json-locale') !== loc);
                b.setAttribute('aria-selected', b.getAttribute('data-json-locale') === loc ? 'true' : 'false');
            });
            document.querySelectorAll('.landing-json-pane').forEach(function (pane) {
                pane.classList.toggle('hidden', pane.getAttribute('data-json-pane') !== loc);
            });
        });
    });
    if (window.location.search.indexOf('tab=json') !== -1) {
        showJson();
    }

    window.__landingBundles = window.__landingBundles || {};
    document.querySelectorAll('.landing-json-initial').forEach(function (sc) {
        var loc = sc.getAttribute('data-locale');
        try {
            window.__landingBundles[loc] = JSON.parse(sc.textContent || '{}');
        } catch (e) {
            window.__landingBundles[loc] = {};
        }
    });

    function setPath(obj, path, value) {
        var keys = path.split('.');
        var cur = obj;
        for (var i = 0; i < keys.length - 1; i++) {
            var k = keys[i];
            var nk = keys[i + 1];
            if (cur[k] == null) {
                cur[k] = String(parseInt(nk, 10)) === String(nk) && String(Number(nk)) === String(nk) ? [] : {};
            }
            cur = cur[k];
        }
        cur[keys[keys.length - 1]] = value;
    }

    function getPath(obj, path) {
        return path.split('.').reduce(function (o, k) {
            return o == null ? o : o[k];
        }, obj);
    }

    function collectBundleFromPane(pane) {
        var loc = pane.getAttribute('data-json-pane');
        var base = JSON.parse(JSON.stringify(window.__landingBundles[loc] || {}));
        pane.querySelectorAll('[data-json-path]').forEach(function (el) {
            var path = el.getAttribute('data-json-path');
            if (!path) {
                return;
            }
            var v = el.type === 'checkbox' ? el.checked : el.value;
            if (el.type !== 'checkbox' && path.endsWith('.user_id')) {
                if (v === '') {
                    v = null;
                } else if (/^\d+$/.test(v)) {
                    v = parseInt(v, 10);
                }
            }
            setPath(base, path, v);
        });
        return base;
    }

    function applyBundleToFields(pane, bundle) {
        pane.querySelectorAll('[data-json-path]').forEach(function (el) {
            var path = el.getAttribute('data-json-path');
            if (!path) {
                return;
            }
            var v = getPath(bundle, path);
            if (el.type === 'checkbox') {
                el.checked = !!v;
            } else if (v === undefined || v === null) {
                el.value = '';
            } else {
                el.value = String(v);
            }
        });
    }

    function syncPaneFromFields(pane) {
        var loc = pane.getAttribute('data-json-pane');
        var b = collectBundleFromPane(pane);
        window.__landingBundles[loc] = b;
        var ta = pane.querySelector('.landing-json-textarea');
        if (ta) {
            ta.value = JSON.stringify(b, null, 2);
        }
    }

    document.querySelectorAll('.landing-json-pane').forEach(function (pane) {
        pane.addEventListener('input', function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('landing-json-field')) {
                syncPaneFromFields(pane);
            }
        });
        pane.addEventListener('change', function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('landing-json-field')) {
                syncPaneFromFields(pane);
            }
        });
        var ta = pane.querySelector('.landing-json-textarea');
        if (ta) {
            function syncTaToFields() {
                try {
                    var loc = pane.getAttribute('data-json-pane');
                    var b = JSON.parse(ta.value || '{}');
                    window.__landingBundles[loc] = b;
                    applyBundleToFields(pane, b);
                } catch (err) {
                    // keep previous bundle
                }
            }
            ta.addEventListener('change', syncTaToFields);
            ta.addEventListener('input', syncTaToFields);
        }
        syncPaneFromFields(pane);
    });

    function getActiveJsonPane() {
        return document.querySelector('.landing-json-pane:not(.hidden)');
    }

    function prepareActiveLocaleJson() {
        var pane = getActiveJsonPane();
        if (!pane) {
            return null;
        }
        syncPaneFromFields(pane);
        var loc = pane.getAttribute('data-json-pane');
        var ta = pane.querySelector('.landing-json-textarea');
        var payload = ta ? ta.value : '{}';
        return { loc: loc, payload: payload };
    }

    var btnSaveFile = document.getElementById('landing-json-btn-save-file');
    var btnImportDb = document.getElementById('landing-json-btn-import-db');
    var formSaveDisk = document.getElementById('landing-json-save-disk-form');
    var formImportEditor = document.getElementById('landing-json-import-editor-form');

    if (btnSaveFile && formSaveDisk) {
        btnSaveFile.addEventListener('click', function () {
            var p = prepareActiveLocaleJson();
            if (!p) {
                return;
            }
            document.getElementById('landing-json-save-locale').value = p.loc;
            document.getElementById('landing-json-save-payload').value = p.payload;
            formSaveDisk.submit();
        });
    }
    if (btnImportDb && formImportEditor) {
        btnImportDb.addEventListener('click', function () {
            var p = prepareActiveLocaleJson();
            if (!p) {
                return;
            }
            document.getElementById('landing-json-import-locale').value = p.loc;
            document.getElementById('landing-json-import-payload').value = p.payload;
            formImportEditor.submit();
        });
    }
})();
</script>
@endpush
@endsection
