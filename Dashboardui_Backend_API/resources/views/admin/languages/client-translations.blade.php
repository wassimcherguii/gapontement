@extends('layouts.admin')

@section('title', get_translation('client_translations_title'))
@section('description', get_translation('client_translations_description'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
            <h1 class="text-2xl font-bold" style="color: var(--text-color);">{{ get_translation('client_translations_title') }}</h1>
            <p class="mt-1 text-sm" style="color: var(--text-secondary-color);">
                {{ get_translation('client_translations_description') }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card p-5">
        <div class="flex flex-wrap items-center justify-between gap-4 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <div class="flex flex-wrap items-center gap-2">
                @foreach($domains as $item)
                    <a href="{{ route_with_lang('admin.assets.client-translations.index', ['domain' => $item->slug]) }}"
                       class="px-3 py-1.5 rounded border text-sm {{ $domain === $item->slug ? 'font-semibold' : '' }}"
                       style="border-color: var(--border-color); color: var(--text-color); background: {{ $domain === $item->slug ? 'var(--hover-bg)' : 'transparent' }};"
                       title="{{ $item->name }}">
                        {{ strtoupper($item->slug) }}
                    </a>
                @endforeach
                <a href="{{ route_with_lang('admin.assets.translation-domains.index') }}"
                   class="px-3 py-1.5 rounded border text-xs"
                   style="border-color: var(--border-color); color: var(--text-secondary-color);">
                    {{ get_translation('client_translations_manage_domains') }}
                </a>
            </div>

            <div class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                <div>{{ get_translation('client_translations_last_generated') }}: {{ $syncMeta['generated_at'] ?? '—' }}</div>
                <div>{{ get_translation('client_translations_checksum') }}: {{ $syncMeta['checksum'] ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="admin-card p-5">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                <h3 class="text-lg font-semibold" style="color: var(--text-color);">{{ get_translation('client_translations_locale_overview') }}</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary-color);">
                    {{ get_translation('client_translations_locale_count', ['count' => $supportedLanguageCount]) }}
                </p>
                <p class="text-xs mt-2" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_coverage_help') }}</p>
            </div>
            <a href="{{ route_with_lang('admin.assets.client-translations.languages-catalog', ['domain' => $domain]) }}"
               class="px-4 py-2 rounded text-sm font-medium border whitespace-nowrap"
               style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('client_translations_edit_languages') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm" style="border-color: var(--border-color);">
                <thead style="background: var(--hover-bg);">
                    <tr>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_code') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_name') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_direction') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_coverage_percent') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_filled_total') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_locale_active') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    @foreach($localeCoverage as $row)
                        <tr>
                            <td class="px-3 py-2 font-mono" style="color: var(--text-color);">{{ $row['code'] }}</td>
                            <td class="px-3 py-2" style="color: var(--text-color);">{{ $row['meta']['native'] ?? $row['meta']['name'] ?? '—' }}</td>
                            <td class="px-3 py-2" style="color: var(--text-secondary-color);">{{ strtoupper($row['meta']['direction'] ?? 'ltr') }}</td>
                            <td class="px-3 py-2" style="color: var(--text-color);">
                                @if($row['percent'] === null)
                                    —
                                @else
                                    <span class="font-semibold">{{ $row['percent'] }}%</span>
                                    <div class="w-32 h-2 rounded mt-1" style="background: var(--border-color);">
                                        <div class="h-2 rounded" style="width: {{ min(100, $row['percent']) }}%; background: var(--primary-color);"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs" style="color: var(--text-secondary-color);">{{ $row['filled'] }} / {{ $row['total'] }}</td>
                            <td class="px-3 py-2 text-xs" style="color: var(--text-secondary-color);">
                                @if(array_key_exists('active', $row['meta']) && $row['meta']['active'] === false)
                                    {{ get_translation('client_translations_inactive') }}
                                @else
                                    {{ get_translation('client_translations_active') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card p-5">
        <h3 class="text-lg font-semibold mb-4 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_add_key') }}</h3>
        <form method="POST" action="{{ route_with_lang('admin.assets.client-translations.store') }}">
            @csrf
            <input type="hidden" name="domain" value="{{ $domain }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('client_translations_key') }}</label>
                    <input type="text" name="key" required class="w-full rounded border px-3 py-2 text-sm"
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                           placeholder="common.example.title">
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('client_translations_status') }}</label>
                    <select name="status" class="w-full rounded border px-3 py-2 text-sm"
                            style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                        <option value="draft">{{ get_translation('client_translations_status_draft') }}</option>
                        <option value="published">{{ get_translation('client_translations_status_published_label') }}</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('client_translations_description_label') }}</label>
                <textarea name="description" rows="2" class="w-full rounded border px-3 py-2 text-sm"
                          style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                @foreach($locales as $locale)
                    <div>
                        <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('client_translations_value') }} ({{ strtoupper($locale) }})</label>
                        <textarea name="values[{{ $locale }}]" rows="2" class="w-full rounded border px-3 py-2 text-sm"
                                  style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"></textarea>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="mt-4 px-4 py-2 rounded text-sm font-medium"
                    style="background: var(--primary-color); color: #fff;">
                {{ get_translation('client_translations_save_key') }}
            </button>
        </form>
    </div>

    <div class="admin-card p-5" id="clientSyncActionsCard"
         data-sync-diff-url="{{ route_with_lang('admin.assets.client-translations.sync-diff', ['domain' => $domain]) }}"
         data-domain="{{ $domain }}">
        <h3 class="text-lg font-semibold mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_sync_actions_title') }}</h3>
        <p class="text-xs mb-3 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_sync_actions_help') }}</p>

        <div class="flex flex-wrap items-center gap-2 mb-3 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <button type="button" id="btnSyncDiffCompare" class="px-4 py-2 rounded text-sm font-medium border"
                    style="border-color: var(--border-color); color: var(--text-color); background: var(--hover-bg);">
                {{ get_translation('client_translations_sync_compare_button') }}
            </button>
            <button type="button" id="btnSyncDiffClear" class="hidden px-3 py-2 rounded text-xs border"
                    style="border-color: var(--border-color); color: var(--text-secondary-color);">
                {{ get_translation('client_translations_sync_clear_report') }}
            </button>
        </div>

        <div id="syncDiffRecap" class="hidden mb-4 text-sm rounded-lg p-3 max-h-72 overflow-y-auto {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
             style="border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-color);"></div>

        <p id="syncDiffStatus" class="hidden text-xs mb-3" style="color: var(--text-secondary-color);"></p>

        <div class="flex flex-wrap items-center gap-3 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <form method="POST" action="{{ route_with_lang('admin.assets.client-translations.sync-to-json') }}">
                @csrf
                <input type="hidden" name="domain" value="{{ $domain }}">
                <button type="submit" id="btnClientSyncPublish" disabled class="px-4 py-2 rounded text-sm font-medium opacity-50 cursor-not-allowed"
                        style="background: var(--primary-color); color: #fff;">
                    {{ get_translation('client_translations_publish') }}
                </button>
            </form>

            <form method="POST" action="{{ route_with_lang('admin.assets.client-translations.sync-from-json') }}">
                @csrf
                <input type="hidden" name="domain" value="{{ $domain }}">
                <button type="submit" id="btnClientSyncImport" disabled class="px-4 py-2 rounded text-sm font-medium border opacity-50 cursor-not-allowed"
                        style="border-color: var(--border-color); color: var(--text-color);">
                    {{ get_translation('client_translations_import') }}
                </button>
            </form>
        </div>
    </div>

    <div class="admin-card p-5">
        <h3 class="text-lg font-semibold mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_sync_status') }}</h3>
        <div class="text-sm rounded-lg px-3 py-2 mb-4 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
             style="background: var(--hover-bg); color: var(--text-secondary-color); border: 1px solid var(--border-color);">
            @if(! $syncState['has_meta_checksum'])
                {{ get_translation('client_translations_sync_no_meta') }}
            @elseif($syncState['published_in_sync'] && ! $syncState['has_draft_keys'])
                {{ get_translation('client_translations_sync_in_sync') }}
            @elseif($syncState['published_in_sync'] && $syncState['has_draft_keys'])
                <span>{{ get_translation('client_translations_sync_in_sync') }}</span>
                <span class="block mt-1">{{ get_translation('client_translations_sync_drafts') }}</span>
            @elseif($syncState['has_draft_keys'])
                {{ get_translation('client_translations_sync_drafts') }}
                <span class="block mt-1">{{ get_translation('client_translations_sync_drift') }}</span>
            @else
                {{ get_translation('client_translations_sync_drift') }}
            @endif
        </div>

        <div class="flex flex-wrap gap-2 mb-4 border-b pb-3" style="border-color: var(--border-color);">
            <button type="button" id="tabBtnDb" class="px-4 py-2 rounded text-sm font-medium tab-btn-active"
                    style="background: var(--primary-color); color: #fff;">
                {{ get_translation('client_translations_tab_db') }}
            </button>
            <button type="button" id="tabBtnJson" class="px-4 py-2 rounded text-sm font-medium border"
                    style="border-color: var(--border-color); color: var(--text-color); background: transparent;">
                {{ get_translation('client_translations_tab_json') }}
            </button>
        </div>

        <div id="panelDb">
            <h3 class="text-lg font-semibold mb-3 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_keys_heading') }} — {{ get_translation('client_translations_tab_db') }} ({{ strtoupper($domain) }})</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: var(--border-color);">
                    <thead style="background: var(--hover-bg);">
                        <tr>
                            <th class="px-3 py-2 text-xs uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_key') }}</th>
                            <th class="px-3 py-2 text-xs uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_status') }}</th>
                            @foreach($locales as $locale)
                                <th class="px-3 py-2 text-xs uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ strtoupper($locale) }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-color);">
                        @forelse($translationKeys as $item)
                            <tr>
                                <td class="px-3 py-2 text-sm font-mono" style="color: var(--text-color);">{{ $item->key }}</td>
                                <td class="px-3 py-2 text-sm" style="color: var(--text-secondary-color);">{{ $item->status }}</td>
                                @foreach($locales as $locale)
                                    <td class="px-3 py-2 text-xs" style="color: var(--text-color);">
                                        {{ \Illuminate\Support\Str::limit(optional($item->values->firstWhere('locale', $locale))->value ?: '—', 80) }}
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <button type="button" class="text-xs px-2 py-1 rounded border edit-db-key"
                                            style="border-color: var(--border-color); color: var(--text-color);"
                                            data-payload="{{ base64_encode(json_encode([
                                                'id' => $item->id,
                                                'key' => $item->key,
                                                'description' => $item->description,
                                                'status' => $item->status,
                                                'values' => collect($locales)->mapWithKeys(fn ($loc) => [$loc => optional($item->values->firstWhere('locale', $loc))->value ?? ''])->all(),
                                            ], JSON_UNESCAPED_UNICODE)) }}">
                                        {{ get_translation('client_translations_edit_db') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($locales) }}" class="px-3 py-6 text-sm text-center" style="color: var(--text-secondary-color);">
                                    {{ get_translation('client_translations_no_keys') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="panelJson" class="hidden">
            <h3 class="text-lg font-semibold mb-3 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_keys_heading') }} — {{ get_translation('client_translations_tab_json') }} ({{ strtoupper($domain) }})</h3>
            <p class="text-xs mb-3" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_modal_json_title') }}</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: var(--border-color);">
                    <thead style="background: var(--hover-bg);">
                        <tr>
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('client_translations_key') }}</th>
                            @foreach($locales as $locale)
                                <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ strtoupper($locale) }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-color);">
                        @forelse($jsonMatrix as $jrow)
                            <tr>
                                <td class="px-3 py-2 text-sm font-mono" style="color: var(--text-color);">{{ $jrow['key'] }}</td>
                                @foreach($locales as $locale)
                                    <td class="px-3 py-2 text-xs" style="color: var(--text-color);">
                                        {{ \Illuminate\Support\Str::limit($jrow['locales'][$locale] ?? '', 80) ?: '—' }}
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <button type="button" class="text-xs px-2 py-1 rounded border edit-json-key"
                                            style="border-color: var(--border-color); color: var(--text-color);"
                                            data-payload="{{ base64_encode(json_encode([
                                                'key' => $jrow['key'],
                                                'values' => $jrow['locales'],
                                            ], JSON_UNESCAPED_UNICODE)) }}">
                                        {{ get_translation('client_translations_edit_json') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + count($locales) }}" class="px-3 py-6 text-sm text-center" style="color: var(--text-secondary-color);">
                                    {{ get_translation('client_translations_no_keys') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $translationKeys->links() }}
        </div>
    </div>

    {{-- Modal: edit DB key --}}
    <div id="modalEditDb" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.45);">
        <div class="max-w-2xl w-full rounded-lg shadow-lg p-5 max-h-[90vh] overflow-y-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h4 class="text-lg font-semibold mb-4" style="color: var(--text-color);">{{ get_translation('client_translations_modal_db_title') }}</h4>
            <form id="formEditDb" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="domain" value="{{ $domain }}">
                <input type="hidden" name="page" value="{{ request('page') }}">
                <div class="mb-3">
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_key') }}</label>
                    <input type="text" id="dbModalKey" readonly class="w-full rounded border px-3 py-2 text-sm font-mono" style="border-color: var(--border-color); background: var(--hover-bg); color: var(--text-color);">
                </div>
                <div class="mb-3">
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_description_label') }}</label>
                    <textarea name="description" id="dbModalDescription" rows="2" class="w-full rounded border px-3 py-2 text-sm" style="border-color: var(--border-color); color: var(--text-color);"></textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_status') }}</label>
                    <select name="status" id="dbModalStatus" class="w-full rounded border px-3 py-2 text-sm" style="border-color: var(--border-color); color: var(--text-color);">
                        <option value="draft">{{ get_translation('client_translations_status_draft') }}</option>
                        <option value="published">{{ get_translation('client_translations_status_published_label') }}</option>
                    </select>
                </div>
                <div id="dbModalLocaleFields" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4"></div>
                <div class="flex flex-wrap gap-2 justify-end">
                    <button type="button" onclick="closeDbModal()" class="px-4 py-2 rounded text-sm border" style="border-color: var(--border-color); color: var(--text-color);">{{ get_translation('client_translations_close') }}</button>
                    <button type="submit" class="px-4 py-2 rounded text-sm font-medium" style="background: var(--primary-color); color: #fff;">{{ get_translation('client_translations_save_key') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: edit JSON key --}}
    <div id="modalEditJson" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.45);">
        <div class="max-w-2xl w-full rounded-lg shadow-lg p-5 max-h-[90vh] overflow-y-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h4 class="text-lg font-semibold mb-4" style="color: var(--text-color);">{{ get_translation('client_translations_modal_json_title') }}</h4>
            <form id="formEditJson" method="POST" action="{{ route_with_lang('admin.assets.client-translations.json-key.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="domain" value="{{ $domain }}">
                <input type="hidden" name="page" value="{{ request('page') }}">
                <input type="hidden" name="key" id="jsonModalKeyInput">
                <div class="mb-3">
                    <label class="block text-xs mb-1" style="color: var(--text-secondary-color);">{{ get_translation('client_translations_key') }}</label>
                    <input type="text" id="jsonModalKeyReadonly" readonly class="w-full rounded border px-3 py-2 text-sm font-mono" style="border-color: var(--border-color); background: var(--hover-bg); color: var(--text-color);">
                </div>
                <div id="jsonModalLocaleFields" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4"></div>
                <div class="flex flex-wrap gap-2 justify-end">
                    <button type="button" onclick="closeJsonModal()" class="px-4 py-2 rounded text-sm border" style="border-color: var(--border-color); color: var(--text-color);">{{ get_translation('client_translations_close') }}</button>
                    <button type="submit" class="px-4 py-2 rounded text-sm font-medium" style="background: var(--primary-color); color: #fff;">{{ get_translation('client_translations_save_key') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@php
    $syncDiffLabels = [
        'loading' => get_translation('client_translations_sync_diff_loading'),
        'error' => get_translation('client_translations_sync_diff_error'),
        'noDiff' => get_translation('client_translations_sync_no_diff_lock'),
        'publishHeading' => get_translation('client_translations_sync_diff_publish_heading'),
        'importHeading' => get_translation('client_translations_sync_diff_import_heading'),
        'colLocale' => get_translation('client_translations_sync_diff_col_locale'),
        'colKey' => get_translation('client_translations_sync_diff_col_key'),
        'colDb' => get_translation('client_translations_sync_diff_col_database'),
        'colJson' => get_translation('client_translations_sync_diff_col_json'),
        'more' => get_translation('client_translations_sync_diff_more'),
        'checksumLine' => get_translation('client_translations_sync_diff_checksum_line'),
        'checksumMatch' => get_translation('client_translations_sync_diff_checksum_match'),
        'checksumMismatch' => get_translation('client_translations_sync_diff_checksum_mismatch'),
        'checksumNoMeta' => get_translation('client_translations_sync_diff_checksum_no_meta'),
    ];
@endphp
<script>
(function () {
    const LANG = @json(app()->getLocale());
    const LOCALES = @json($locales);
    const SYNC_DIFF_LABELS = @json($syncDiffLabels);
    const keysUpdateUrl = function (id) {
        return '/' + LANG + '/admin/assets/client-translations/keys/' + id;
    };

    const tabBtnDb = document.getElementById('tabBtnDb');
    const tabBtnJson = document.getElementById('tabBtnJson');
    const panelDb = document.getElementById('panelDb');
    const panelJson = document.getElementById('panelJson');

    function setTabDb() {
        panelDb.classList.remove('hidden');
        panelJson.classList.add('hidden');
        tabBtnDb.style.background = 'var(--primary-color)';
        tabBtnDb.style.color = '#fff';
        tabBtnJson.style.background = 'transparent';
        tabBtnJson.style.color = 'var(--text-color)';
    }
    function setTabJson() {
        panelJson.classList.remove('hidden');
        panelDb.classList.add('hidden');
        tabBtnJson.style.background = 'var(--primary-color)';
        tabBtnJson.style.color = '#fff';
        tabBtnDb.style.background = 'transparent';
        tabBtnDb.style.color = 'var(--text-color)';
    }
    if (tabBtnDb) tabBtnDb.addEventListener('click', setTabDb);
    if (tabBtnJson) tabBtnJson.addEventListener('click', setTabJson);

    const modalEditDb = document.getElementById('modalEditDb');
    const formEditDb = document.getElementById('formEditDb');
    const dbModalLocaleFields = document.getElementById('dbModalLocaleFields');

    window.closeDbModal = function () {
        if (modalEditDb) modalEditDb.classList.add('hidden');
    };
    document.querySelectorAll('.edit-db-key').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let data;
            try {
                data = JSON.parse(atob(this.getAttribute('data-payload')));
            } catch (e) { return; }
            formEditDb.action = keysUpdateUrl(data.id);
            document.getElementById('dbModalKey').value = data.key || '';
            document.getElementById('dbModalDescription').value = data.description || '';
            document.getElementById('dbModalStatus').value = data.status || 'draft';
            dbModalLocaleFields.innerHTML = '';
            LOCALES.forEach(function (loc) {
                const v = (data.values && data.values[loc]) ? data.values[loc] : '';
                const wrap = document.createElement('div');
                wrap.innerHTML = '<label class="block text-xs mb-1" style="color:var(--text-secondary-color);">{{ get_translation('client_translations_value') }} (' + loc.toUpperCase() + ')</label>' +
                    '<textarea name="values[' + loc + ']" rows="3" class="w-full rounded border px-3 py-2 text-sm" style="border-color:var(--border-color);color:var(--text-color);"></textarea>';
                wrap.querySelector('textarea').value = v;
                dbModalLocaleFields.appendChild(wrap);
            });
            modalEditDb.classList.remove('hidden');
        });
    });
    if (modalEditDb) {
        modalEditDb.addEventListener('click', function (e) {
            if (e.target === modalEditDb) closeDbModal();
        });
    }

    const modalEditJson = document.getElementById('modalEditJson');
    const jsonModalLocaleFields = document.getElementById('jsonModalLocaleFields');

    window.closeJsonModal = function () {
        if (modalEditJson) modalEditJson.classList.add('hidden');
    };
    document.querySelectorAll('.edit-json-key').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let data;
            try {
                data = JSON.parse(atob(this.getAttribute('data-payload')));
            } catch (e) { return; }
            document.getElementById('jsonModalKeyInput').value = data.key || '';
            document.getElementById('jsonModalKeyReadonly').value = data.key || '';
            jsonModalLocaleFields.innerHTML = '';
            LOCALES.forEach(function (loc) {
                const v = (data.values && data.values[loc]) ? data.values[loc] : '';
                const wrap = document.createElement('div');
                wrap.innerHTML = '<label class="block text-xs mb-1" style="color:var(--text-secondary-color);">{{ get_translation('client_translations_value') }} (' + loc.toUpperCase() + ')</label>' +
                    '<textarea name="values[' + loc + ']" rows="3" class="w-full rounded border px-3 py-2 text-sm" style="border-color:var(--border-color);color:var(--text-color);"></textarea>';
                wrap.querySelector('textarea').value = v;
                jsonModalLocaleFields.appendChild(wrap);
            });
            modalEditJson.classList.remove('hidden');
        });
    });
    if (modalEditJson) {
        modalEditJson.addEventListener('click', function (e) {
            if (e.target === modalEditJson) closeJsonModal();
        });
    }

    const syncCard = document.getElementById('clientSyncActionsCard');
    if (syncCard) {
        let syncDiffUrl = syncCard.getAttribute('data-sync-diff-url') || '';
        const syncDomain = syncCard.getAttribute('data-domain') || '';
        if (syncDomain && syncDiffUrl.indexOf('domain=') === -1) {
            syncDiffUrl += (syncDiffUrl.indexOf('?') === -1 ? '?' : '&') + 'domain=' + encodeURIComponent(syncDomain);
        }
        const btnSyncDiffCompare = document.getElementById('btnSyncDiffCompare');
        const btnSyncDiffClear = document.getElementById('btnSyncDiffClear');
        const syncDiffRecap = document.getElementById('syncDiffRecap');
        const syncDiffStatus = document.getElementById('syncDiffStatus');
        const btnClientSyncPublish = document.getElementById('btnClientSyncPublish');
        const btnClientSyncImport = document.getElementById('btnClientSyncImport');

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function setSubmitDisabled(btn, disabled) {
            if (!btn) return;
            btn.disabled = disabled;
            if (disabled) {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function clearRecapAndDisableSync() {
            if (syncDiffRecap) {
                syncDiffRecap.innerHTML = '';
                syncDiffRecap.classList.add('hidden');
            }
            if (syncDiffStatus) {
                syncDiffStatus.textContent = '';
                syncDiffStatus.classList.add('hidden');
            }
            if (btnSyncDiffClear) btnSyncDiffClear.classList.add('hidden');
            setSubmitDisabled(btnClientSyncPublish, true);
            setSubmitDisabled(btnClientSyncImport, true);
        }

        function buildSampleTable(samples, colDbLabel, colJsonLabel) {
            if (!samples || samples.length === 0) {
                return '';
            }
            let rows = '';
            samples.forEach(function (row) {
                rows += '<tr><td class="px-2 py-1 align-top font-mono text-xs">' + escapeHtml(row.locale) + '</td>' +
                    '<td class="px-2 py-1 align-top font-mono text-xs break-all">' + escapeHtml(row.key) + '</td>' +
                    '<td class="px-2 py-1 align-top text-xs break-all">' + escapeHtml(row.database) + '</td>' +
                    '<td class="px-2 py-1 align-top text-xs break-all">' + escapeHtml(row.json) + '</td></tr>';
            });
            return '<table class="w-full text-left border-collapse mt-2" style="border-color: var(--border-color);"><thead><tr>' +
                '<th class="px-2 py-1 text-xs uppercase" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.colLocale) + '</th>' +
                '<th class="px-2 py-1 text-xs uppercase" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.colKey) + '</th>' +
                '<th class="px-2 py-1 text-xs uppercase" style="color: var(--text-secondary-color);">' + escapeHtml(colDbLabel) + '</th>' +
                '<th class="px-2 py-1 text-xs uppercase" style="color: var(--text-secondary-color);">' + escapeHtml(colJsonLabel) + '</th>' +
                '</tr></thead><tbody>' + rows + '</tbody></table>';
        }

        async function runSyncDiff() {
            if (!btnSyncDiffCompare || !syncDiffRecap) return;
            btnSyncDiffCompare.disabled = true;
            syncDiffRecap.classList.remove('hidden');
            syncDiffRecap.innerHTML = '<p class="text-xs" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.loading) + '</p>';
            if (syncDiffStatus) syncDiffStatus.classList.add('hidden');
            setSubmitDisabled(btnClientSyncPublish, true);
            setSubmitDisabled(btnClientSyncImport, true);

            try {
                const res = await fetch(syncDiffUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                const data = await res.json();
                const pubCount = data.publish && typeof data.publish.count === 'number' ? data.publish.count : 0;
                const impCount = data.import && typeof data.import.count === 'number' ? data.import.count : 0;
                const pubSamples = (data.publish && data.publish.samples) ? data.publish.samples : [];
                const impSamples = (data.import && data.import.samples) ? data.import.samples : [];

                let html = '';
                if (data.has_meta_checksum) {
                    html += '<p class="text-xs mb-2" style="color: var(--text-secondary-color);">' +
                        escapeHtml(SYNC_DIFF_LABELS.checksumLine) + ' ' +
                        (data.meta_checksum_match ? escapeHtml(SYNC_DIFF_LABELS.checksumMatch) : escapeHtml(SYNC_DIFF_LABELS.checksumMismatch)) +
                        '</p>';
                } else {
                    html += '<p class="text-xs mb-2" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.checksumNoMeta) + '</p>';
                }

                html += '<div class="mt-3"><strong class="text-sm">' + escapeHtml(SYNC_DIFF_LABELS.publishHeading) + '</strong> (' + pubCount + ')' +
                    buildSampleTable(pubSamples, SYNC_DIFF_LABELS.colDb, SYNC_DIFF_LABELS.colJson);
                if (pubCount > pubSamples.length) {
                    html += '<p class="text-xs mt-1" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.more).replace(':count', String(pubCount - pubSamples.length)) + '</p>';
                }
                html += '</div>';

                html += '<div class="mt-4"><strong class="text-sm">' + escapeHtml(SYNC_DIFF_LABELS.importHeading) + '</strong> (' + impCount + ')' +
                    buildSampleTable(impSamples, SYNC_DIFF_LABELS.colDb, SYNC_DIFF_LABELS.colJson);
                if (impCount > impSamples.length) {
                    html += '<p class="text-xs mt-1" style="color: var(--text-secondary-color);">' + escapeHtml(SYNC_DIFF_LABELS.more).replace(':count', String(impCount - impSamples.length)) + '</p>';
                }
                html += '</div>';

                syncDiffRecap.innerHTML = html;
                if (btnSyncDiffClear) btnSyncDiffClear.classList.remove('hidden');

                if (pubCount === 0 && impCount === 0) {
                    setSubmitDisabled(btnClientSyncPublish, true);
                    setSubmitDisabled(btnClientSyncImport, true);
                    if (syncDiffStatus) {
                        syncDiffStatus.textContent = SYNC_DIFF_LABELS.noDiff;
                        syncDiffStatus.classList.remove('hidden');
                    }
                } else {
                    setSubmitDisabled(btnClientSyncPublish, pubCount === 0);
                    setSubmitDisabled(btnClientSyncImport, impCount === 0);
                    if (syncDiffStatus) syncDiffStatus.classList.add('hidden');
                }
            } catch (e) {
                syncDiffRecap.innerHTML = '<p class="text-xs" style="color: #b91c1c;">' + escapeHtml(SYNC_DIFF_LABELS.error) + '</p>';
                if (btnSyncDiffClear) btnSyncDiffClear.classList.remove('hidden');
                setSubmitDisabled(btnClientSyncPublish, true);
                setSubmitDisabled(btnClientSyncImport, true);
            } finally {
                btnSyncDiffCompare.disabled = false;
            }
        }

        if (btnSyncDiffCompare) {
            btnSyncDiffCompare.addEventListener('click', function () {
                runSyncDiff();
            });
        }
        if (btnSyncDiffClear) {
            btnSyncDiffClear.addEventListener('click', function () {
                clearRecapAndDisableSync();
            });
        }
    }
</script>
@endpush
@endsection