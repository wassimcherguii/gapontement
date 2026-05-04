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
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
            {{ get_translation($titleKey) }}
        </h1>
        <p class="mt-2 text-sm max-w-2xl {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('website_section_intro') }}
        </p>
    </div>
    <form method="post" action="{{ route_with_lang('admin.website.home.publish') }}" class="shrink-0">
        @csrf
        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105"
                style="background: var(--primary-color, #0F4C81);">
            {{ get_translation('publish') ?? 'Publish homepage' }}
        </button>
    </form>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
        {{ session('status') }}
    </div>
@endif
@if (session('landing_publish_checksum'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm font-mono break-all" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-secondary-color);">
        Published. Checksum: {{ session('landing_publish_checksum') }}
    </div>
@endif

<form method="post" action="{{ route_with_lang('admin.website.home.save') }}" class="space-y-8">
    @csrf
    <input type="hidden" name="quick_booking_enabled" value="0">
    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">{{ get_translation('seo') ?? 'SEO / meta' }}</h2>
        @foreach ($locales as $loc)
            @php $pl = $page->locales->firstWhere('locale', $loc); @endphp
            <div class="grid gap-3 sm:grid-cols-2 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium sm:col-span-2" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                <label class="block text-sm">
                    <span class="block mb-1" style="color: var(--text-secondary-color);">meta_title</span>
                    <input type="text" name="page_meta[{{ $loc }}][meta_title]" value="{{ old('page_meta.'.$loc.'.meta_title', $pl?->meta_title) }}"
                           class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </label>
                <label class="block text-sm sm:col-span-2">
                    <span class="block mb-1" style="color: var(--text-secondary-color);">meta_description</span>
                    <textarea name="page_meta[{{ $loc }}][meta_description]" rows="2"
                              class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">{{ old('page_meta.'.$loc.'.meta_description', $pl?->meta_description) }}</textarea>
                </label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">Top bar</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('top_bar'), $loc); @endphp
            <div class="grid gap-3 sm:grid-cols-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium sm:col-span-3" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80">phone</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][phone]" value="{{ old('content.top_bar.'.$loc.'.phone', $c['phone'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80">emergency</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][emergency]" value="{{ old('content.top_bar.'.$loc.'.emergency', $c['emergency'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80">hours</span>
                    <input type="text" name="content[top_bar][{{ $loc }}][hours]" value="{{ old('content.top_bar.'.$loc.'.hours', $c['hours'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">Hero</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('hero'), $loc); @endphp
            <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                @foreach (['tagline', 'headline', 'subhead', 'cta_primary', 'cta_primary_href', 'cta_secondary', 'cta_secondary_href'] as $field)
                    <label class="block text-sm">
                        <span class="block mb-1 opacity-80">{{ $field }}</span>
                        <input type="text" name="content[hero][{{ $loc }}][{{ $field }}]" value="{{ old('content.hero.'.$loc.'.'.$field, $c[$field] ?? '') }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>

    @foreach (['about' => ['title', 'lead', 'body'], 'contact' => ['title', 'body', 'cta'], 'cta' => ['title', 'body', 'button'], 'footer' => ['line']] as $sk => $fields)
        <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h2 class="text-lg font-semibold capitalize" style="color: var(--text-color);">{{ str_replace('_', ' ', $sk) }}</h2>
            @foreach ($locales as $loc)
                @php $c = $trContent($section($sk), $loc); @endphp
                <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                    <p class="text-sm font-medium" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                    @foreach ($fields as $field)
                        <label class="block text-sm">
                            <span class="block mb-1 opacity-80">{{ $field }}</span>
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
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">Why us (section title)</h2>
        @foreach ($locales as $loc)
            @php $c = $trContent($section('why_us'), $loc); @endphp
            <div class="grid gap-3 border-t pt-4 first:border-t-0 first:pt-0" style="border-color: var(--border-color);">
                <p class="text-sm font-medium" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80">title</span>
                    <input type="text" name="content[why_us][{{ $loc }}][title]" value="{{ old('content.why_us.'.$loc.'.title', $c['title'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80">subtitle</span>
                    <input type="text" name="content[why_us][{{ $loc }}][subtitle]" value="{{ old('content.why_us.'.$loc.'.subtitle', $c['subtitle'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">Quick booking</h2>
        <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="quick_booking_enabled" value="1" class="rounded border" @checked(old('quick_booking_enabled', $qbEnabled))>
            <span style="color: var(--text-color);">{{ get_translation('enabled') ?? 'Enabled' }}</span>
        </label>
        @foreach ($locales as $loc)
            @php $c = $trContent($qbSection, $loc); @endphp
            <div class="grid gap-3 border-t pt-4" style="border-color: var(--border-color);">
                <p class="text-sm font-medium" style="color: var(--text-secondary-color);">{{ strtoupper($loc) }}</p>
                <label class="block text-sm"><span class="block mb-1 opacity-80">title</span>
                    <input type="text" name="content[quick_booking][{{ $loc }}][title]" value="{{ old('content.quick_booking.'.$loc.'.title', $c['title'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                <label class="block text-sm"><span class="block mb-1 opacity-80">hint</span>
                    <input type="text" name="content[quick_booking][{{ $loc }}][hint]" value="{{ old('content.quick_booking.'.$loc.'.hint', $c['hint'] ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
            </div>
        @endforeach
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-6 overflow-x-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold" style="color: var(--text-color);">Navigation</h2>
        @foreach ($page->navItems as $nav)
            <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                <p class="text-xs font-mono" style="color: var(--text-secondary-color);">#{{ $nav->id }}</p>
                <input type="hidden" name="nav[{{ $nav->id }}][id]" value="{{ $nav->id }}">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80">href</span>
                        <input type="text" name="nav[{{ $nav->id }}][href]" value="{{ old('nav.'.$nav->id.'.href', $nav->href) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="nav[{{ $nav->id }}][is_visible]" value="0"><input type="checkbox" name="nav[{{ $nav->id }}][is_visible]" value="1" @checked(old('nav.'.$nav->id.'.is_visible', $nav->is_visible))> visible</label>
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="nav[{{ $nav->id }}][is_cta]" value="0"><input type="checkbox" name="nav[{{ $nav->id }}][is_cta]" value="1" @checked(old('nav.'.$nav->id.'.is_cta', $nav->is_cta))> CTA</label>
                </div>
                @foreach ($locales as $loc)
                    @php $nl = $nav->translations->firstWhere('locale', $loc); @endphp
                    <label class="block text-sm">
                        <span class="block mb-1 opacity-80">label ({{ $loc }})</span>
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
            <h2 class="text-lg font-semibold capitalize" style="color: var(--text-color);">Entities — {{ str_replace('_', ' ', $sec->section_key) }}</h2>
            @foreach ($sec->entities as $ent)
                <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                    <p class="text-xs font-mono">{{ $ent->type }} #{{ $ent->id }}</p>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="block text-sm"><span class="block mb-1 opacity-80">sort_order</span>
                            <input type="number" name="entities[{{ $ent->id }}][sort_order]" value="{{ old('entities.'.$ent->id.'.sort_order', $ent->sort_order) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm"><span class="block mb-1 opacity-80">slug</span>
                            <input type="text" name="entities[{{ $ent->id }}][slug]" value="{{ old('entities.'.$ent->id.'.slug', $ent->slug) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80">image_path</span>
                            <input type="text" name="entities[{{ $ent->id }}][image_path]" value="{{ old('entities.'.$ent->id.'.image_path', $ent->image_path) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                        <label class="block text-sm sm:col-span-2"><span class="block mb-1 opacity-80">href</span>
                            <input type="text" name="entities[{{ $ent->id }}][href]" value="{{ old('entities.'.$ent->id.'.href', $ent->href) }}" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"></label>
                    </div>
                    <p class="text-xs" style="color: var(--text-secondary-color);">user_id (Phase 2): {{ $ent->user_id ?? '—' }}</p>
                    @foreach ($locales as $loc)
                        @php $et = $ent->translations->firstWhere('locale', $loc); @endphp
                        <div class="grid gap-2 border-t pt-3" style="border-color: var(--border-color);">
                            <p class="text-xs font-medium">{{ strtoupper($loc) }}</p>
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][title]" placeholder="title" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.title', $et?->title) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][subtitle]" placeholder="subtitle" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.subtitle', $et?->subtitle) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                            <textarea name="entities[{{ $ent->id }}][t][{{ $loc }}][body]" rows="2" placeholder="body" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">{{ old('entities.'.$ent->id.'.t.'.$loc.'.body', $et?->body) }}</textarea>
                            <input type="text" name="entities[{{ $ent->id }}][t][{{ $loc }}][cta_label]" placeholder="cta_label" value="{{ old('entities.'.$ent->id.'.t.'.$loc.'.cta_label', $et?->cta_label) }}" class="w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);">
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105" style="background: var(--primary-color, #0F4C81);">
            {{ get_translation('save') ?? 'Save' }}
        </button>
    </div>
</form>
@endsection
