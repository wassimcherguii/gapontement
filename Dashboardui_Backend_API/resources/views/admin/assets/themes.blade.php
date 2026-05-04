@extends('layouts.admin')

@section('title', get_translation('theme_management'))
@section('description', get_translation('theme_management'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('theme_management') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('theme_management') }}
            </p>
        </div>
    </div>

    <!-- Theme Selection -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Light Theme -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('sun', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('light') }}
                </h3>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 rounded-lg border" style="background: var(--background-color); border-color: var(--border-color);">
                    <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-3">
                        <div class="w-8 h-8 rounded-lg" style="background: var(--primary-color);"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium" style="color: var(--text-color);">{{ get_translation('sample_card') }}</p>
                            <p class="text-xs" style="color: var(--text-secondary-color);">{{ get_translation('light_theme_preview') }}</p>
                        </div>
                    </div>
                </div>
                
                <button class="w-full px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-200" style="background: var(--primary-color);">
                    {{ get_translation('activate') }}
                </button>
            </div>
        </div>

        <!-- Dark Theme -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('moon', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('dark') }}
                </h3>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 rounded-lg border bg-gray-800 border-gray-700">
                    <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-3">
                        <div class="w-8 h-8 rounded-lg" style="background: var(--primary-color);"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white">{{ get_translation('sample_card') }}</p>
                            <p class="text-xs text-gray-400">{{ get_translation('dark_theme_preview') }}</p>
                        </div>
                    </div>
                </div>
                
                <button class="w-full px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-200 bg-gray-700 hover:bg-gray-600">
                    {{ get_translation('activate') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
