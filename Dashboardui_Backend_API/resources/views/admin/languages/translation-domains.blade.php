@extends('layouts.admin')

@section('title', get_translation('translation_domains_heading'))
@section('description', get_translation('translation_domains_description'))

@section('content')
<div class="space-y-6">
    <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
        <h1 class="text-2xl font-bold" style="color: var(--text-color);">{{ get_translation('translation_domains_heading') }}</h1>
        <p class="mt-1 text-sm" style="color: var(--text-secondary-color);">{{ get_translation('translation_domains_description') }}</p>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card p-5">
        <h2 class="text-lg font-semibold mb-4" style="color: var(--text-color);">{{ get_translation('translation_domains_create') }}</h2>
        <form method="POST" action="{{ route_with_lang('admin.assets.translation-domains.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('translation_domains_slug') }}</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required
                       class="w-full rounded border px-3 py-2 text-sm font-mono"
                       style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                       placeholder="my_portal">
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: var(--text-color);">{{ get_translation('translation_domains_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded border px-3 py-2 text-sm"
                       style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                       placeholder="My portal">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 rounded text-sm font-medium w-full md:w-auto"
                        style="background: var(--primary-color); color: #fff;">
                    {{ get_translation('translation_domains_submit') }}
                </button>
            </div>
        </form>
    </div>

    <div class="admin-card p-5">
        <h2 class="text-lg font-semibold mb-4" style="color: var(--text-color);">{{ get_translation('translation_domains_list') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm" style="border-color: var(--border-color);">
                <thead style="background: var(--hover-bg);">
                    <tr>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('translation_domains_slug') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('translation_domains_name') }}</th>
                        <th class="px-3 py-2 text-xs uppercase {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('translation_domains_keys_link') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    @foreach($domains as $d)
                        <tr>
                            <td class="px-3 py-2 font-mono" style="color: var(--text-color);">{{ $d->slug }}</td>
                            <td class="px-3 py-2" style="color: var(--text-color);">{{ $d->name }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route_with_lang('admin.assets.client-translations.index', ['domain' => $d->slug]) }}"
                                   class="text-sm underline" style="color: var(--primary-color);">
                                    {{ get_translation('translation_domains_open_keys') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-xs" style="color: var(--text-secondary-color);">{{ get_translation('translation_domains_hint') }}</p>
    </div>
</div>
@endsection
