@extends('layouts.admin')

@section('title', get_translation('client_translations_catalog_title'))
@section('description', get_translation('client_translations_catalog_description'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
            <h1 class="text-2xl font-bold" style="color: var(--text-color);">{{ get_translation('client_translations_catalog_title') }}</h1>
            <p class="mt-1 text-sm" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_catalog_description') }}</p>
            <p class="mt-2 text-xs font-mono" style="color: var(--text-secondary-color);">{{ strtoupper($domain) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($domains as $item)
                <a href="{{ route_with_lang('admin.assets.client-translations.languages-catalog', ['domain' => $item->slug]) }}"
                   class="px-3 py-1.5 rounded border text-sm {{ $domain === $item->slug ? 'font-semibold' : '' }}"
                   style="border-color: var(--border-color); color: var(--text-color); background: {{ $domain === $item->slug ? 'var(--hover-bg)' : 'transparent' }};"
                   title="{{ $item->name }}">
                    {{ strtoupper($item->slug) }}
                </a>
            @endforeach
            <a href="{{ route_with_lang('admin.assets.client-translations.index', ['domain' => $domain]) }}"
               class="px-3 py-1.5 rounded border text-sm"
               style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('client_translations_back_to_keys') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route_with_lang('admin.assets.client-translations.languages-catalog.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="domain" value="{{ $domain }}">

        <div class="admin-card p-5">
            <label class="block text-sm font-medium mb-2" style="color: var(--text-color);">{{ get_translation('client_translations_default_locale') }}</label>
            <select name="default" class="w-full max-w-md rounded border px-3 py-2 text-sm"
                    style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                @foreach(array_keys($languages['supported'] ?? []) as $code)
                    <option value="{{ $code }}" {{ ($languages['default'] ?? '') === $code ? 'selected' : '' }}>{{ strtoupper($code) }}</option>
                @endforeach
            </select>
        </div>

        <div class="admin-card p-5">
            <h3 class="text-lg font-semibold mb-4 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_catalog_locales') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y text-sm" style="border-color: var(--border-color);">
                    <thead style="background: var(--hover-bg);">
                        <tr>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">Code</th>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_name') }}</th>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">Native</th>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">Dir</th>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">Flag</th>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_active') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-color);">
                        @foreach($languages['supported'] ?? [] as $code => $meta)
                            @if(is_array($meta))
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="hidden" name="locale_codes[]" value="{{ $code }}">
                                        <span class="font-mono" style="color: var(--text-color);">{{ $code }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="locale_name[{{ $code }}]" value="{{ old('locale_name.'.$code, $meta['name'] ?? '') }}" required
                                               class="w-full min-w-[8rem] rounded border px-2 py-1 text-sm"
                                               style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="locale_native[{{ $code }}]" value="{{ old('locale_native.'.$code, $meta['native'] ?? '') }}" required
                                               class="w-full min-w-[8rem] rounded border px-2 py-1 text-sm"
                                               style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select name="locale_direction[{{ $code }}]" class="rounded border px-2 py-1 text-sm"
                                                style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                                            <option value="ltr" {{ ($meta['direction'] ?? 'ltr') === 'ltr' ? 'selected' : '' }}>LTR</option>
                                            <option value="rtl" {{ ($meta['direction'] ?? '') === 'rtl' ? 'selected' : '' }}>RTL</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="locale_flag[{{ $code }}]" value="{{ old('locale_flag.'.$code, $meta['flag'] ?? '') }}"
                                               class="w-20 rounded border px-2 py-1 text-sm"
                                               style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="checkbox" name="locale_active[{{ $code }}]" value="1"
                                               {{ old('locale_active.'.$code, (! array_key_exists('active', $meta) || $meta['active'] !== false)) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card p-5">
            <h3 class="text-lg font-semibold mb-4 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_catalog_add_locale') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">Code</label>
                    <input type="text" name="new_locale_code" value="{{ old('new_locale_code') }}"
                           class="w-full rounded border px-3 py-2 text-sm"
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                           placeholder="de">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_locale_name') }}</label>
                    <input type="text" name="new_locale_name" value="{{ old('new_locale_name') }}"
                           class="w-full rounded border px-3 py-2 text-sm"
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">Native</label>
                    <input type="text" name="new_locale_native" value="{{ old('new_locale_native') }}"
                           class="w-full rounded border px-3 py-2 text-sm"
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">Dir</label>
                    <select name="new_locale_direction" class="w-full rounded border px-3 py-2 text-sm"
                            style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                        <option value="ltr" {{ old('new_locale_direction', 'ltr') === 'ltr' ? 'selected' : '' }}>LTR</option>
                        <option value="rtl" {{ old('new_locale_direction') === 'rtl' ? 'selected' : '' }}>RTL</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 max-w-xs">
                <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">Flag</label>
                <input type="text" name="new_locale_flag" value="{{ old('new_locale_flag') }}"
                       class="w-full rounded border px-3 py-2 text-sm"
                       style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
            </div>
        </div>

        <button type="submit" class="px-4 py-2 rounded text-sm font-medium"
                style="background: var(--primary-color); color: #fff;">
            {{ get_translation('client_translations_catalog_save') }}
        </button>
    </form>
</div>
@endsection
