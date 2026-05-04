@extends('layouts.admin')

@section('title', get_translation($titleKey))
@section('description', get_translation('website_section_intro'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
        {{ get_translation($titleKey) }}
    </h1>
    <p class="mt-3 text-base max-w-3xl {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
        {{ get_translation('website_section_intro') }}
    </p>
</div>

<div class="admin-card rounded-xl p-6 sm:p-8" style="background: var(--surface-color); border: 1px solid var(--border-color);">
    <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
        {{ get_translation('website_editor_placeholder') }}
    </p>
</div>
@endsection
