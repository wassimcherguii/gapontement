@extends('layouts.admin')

@section('title', get_translation('dashboard'))
@section('description', get_translation('admin_dashboard_description'))

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
            <h1 class="text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('welcome_back') }}, {{ Auth::user()->name }}!
            </h1>
            <p class="mt-2 text-lg {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('admin_dashboard_subtitle') }}
            </p>
        </div>
        <div class="hidden md:flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
            <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}">
                <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}" style="color: var(--text-color);">{{ get_translation('role') }}</p>
                <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}" style="color: var(--text-secondary-color);">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: var(--primary-color);">
                <span class="text-white text-lg font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
    @include('admin.components.stats-card', [
        'title' => get_translation('total_users'),
        'value' => '2,345',
        'icon' => lucide_icon('users', 'w-6 h-6'),
        'color' => 'primary',
        'trend' => 'up',
        'trendValue' => '+12%',
        'description' => get_translation('active_users')
    ])
    
    @include('admin.components.stats-card', [
        'title' => get_translation('total_orders'),
        'value' => '1,234',
        'icon' => lucide_icon('shopping-bag', 'w-6 h-6'),
        'color' => 'success',
        'trend' => 'up',
        'trendValue' => '+8%',
        'description' => get_translation('this_month')
    ])
    
    @include('admin.components.stats-card', [
        'title' => get_translation('revenue'),
        'value' => '$45,678',
        'icon' => lucide_icon('dollar-sign', 'w-6 h-6'),
        'color' => 'info',
        'trend' => 'up',
        'trendValue' => '+15%',
        'description' => get_translation('this_month')
    ])
    
    @include('admin.components.stats-card', [
        'title' => get_translation('conversion_rate'),
        'value' => '3.24%',
        'icon' => lucide_icon('chart-pie', 'w-6 h-6'),
        'color' => 'warning',
        'trend' => 'down',
        'trendValue' => '-2%',
        'description' => get_translation('vs_last_month')
    ])
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
            <!-- Recent Activity -->
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-6 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                    <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('recent_activity') }}
                    </h3>
                    <button class="text-sm font-medium transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}" style="color: var(--primary-color);">
                        {{ get_translation('view_all') }}
                    </button>
                </div>
                
                <div class="space-y-4">
                    @for($i = 0; $i < 5; $i++)
                    <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse flex-row-reverse' : '' }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: var(--primary-color)20;">
                            {!! lucide_icon('users', 'w-5 h-5', 'var(--primary-color)') !!}
                        </div>
                        <div class="flex-1 min-w-0 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                            <p class="text-sm font-medium" style="color: var(--text-color);">
                                {{ get_translation('new_user_registered') }}
                            </p>
                            <p class="text-xs" style="color: var(--text-secondary-color);">
                                {{ get_translation_with_params('minutes_ago', ['count' => rand(1, 60)]) }}
                            </p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
    
    <!-- Quick Actions -->
    <div class="admin-card p-6">
        <h3 class="text-lg font-semibold mb-6 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
            {{ get_translation('quick_actions') }}
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button class="p-4 rounded-lg border-2 border-dashed transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-col' : 'flex-col' }}"
                    style="border-color: var(--border-color);">
                {!! lucide_icon('user-plus', 'w-8 h-8', 'var(--primary-color)') !!}
                <span class="text-sm font-medium" style="color: var(--text-color);">{{ get_translation('add_user') }}</span>
            </button>
            
            <button class="p-4 rounded-lg border-2 border-dashed transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-col' : 'flex-col' }}"
                    style="border-color: var(--border-color);">
                {!! lucide_icon('file-text', 'w-8 h-8', 'var(--success-color)') !!}
                <span class="text-sm font-medium" style="color: var(--text-color);">{{ get_translation('create_post') }}</span>
            </button>
            
            <button class="p-4 rounded-lg border-2 border-dashed transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-col' : 'flex-col' }}"
                    style="border-color: var(--border-color);">
                {!! lucide_icon('bar-chart', 'w-8 h-8', 'var(--info-color)') !!}
                <span class="text-sm font-medium" style="color: var(--text-color);">{{ get_translation('view_reports') }}</span>
            </button>
            
            <button class="p-4 rounded-lg border-2 border-dashed transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-col' : 'flex-col' }}"
                    style="border-color: var(--border-color);">
                {!! lucide_icon('settings', 'w-8 h-8', 'var(--warning-color)') !!}
                <span class="text-sm font-medium" style="color: var(--text-color);">{{ get_translation('settings') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Recent Users Table -->
<div class="admin-card p-6">
    <div class="flex items-center justify-between mb-6 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
            {{ get_translation('recent_users') }}
        </h3>
        <button class="text-sm font-medium transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}" style="color: var(--primary-color);">
            {{ get_translation('view_all') }}
        </button>
    </div>
    
    @include('admin.components.data-table', [
        'headers' => [get_translation('name'), get_translation('email'), get_translation('role'), get_translation('created_at')],
        'data' => [
            ['John Doe', 'john@example.com', 'Admin', '2024-01-15'],
            ['Jane Smith', 'jane@example.com', 'Manager', '2024-01-14'],
            ['Bob Johnson', 'bob@example.com', 'User', '2024-01-13'],
            ['Alice Brown', 'alice@example.com', 'User', '2024-01-12'],
            ['Charlie Wilson', 'charlie@example.com', 'Manager', '2024-01-11'],
        ],
        'actions' => true,
        'searchable' => true,
        'pagination' => true
    ])
</div>

@endsection

