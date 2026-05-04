@props([
    'title' => '',
    'value' => '0',
    'icon' => '',
    'color' => 'primary',
    'trend' => null,
    'trendValue' => '0%',
    'description' => ''
])

<div class="admin-card p-6 rounded-xl fade-in">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="flex-1">
            <p class="text-sm font-medium" style="color: var(--text-secondary-color);">{{ $title }}</p>
            <p class="text-3xl font-bold mt-2" style="color: var(--text-color);">{{ $value }}</p>
            @if($description)
                <p class="text-xs mt-1" style="color: var(--text-secondary-color);">{{ $description }}</p>
            @endif
        </div>
        
        @if($icon)
        <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
             style="background: var(--{{ $color }}-color)20; color: var(--{{ $color }}-color);">
            {!! $icon !!}
        </div>
        @endif
    </div>
    
    @if($trend)
    <div class="mt-4 flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="flex items-center {{ $trend === 'up' ? 'text-green-600' : 'text-red-600' }}">
            @if($trend === 'up')
                {!! lucide_icon('trending-up', 'w-4 h-4', 'currentColor') !!}
            @else
                {!! lucide_icon('trending-down', 'w-4 h-4', 'currentColor') !!}
            @endif
            <span class="text-sm font-medium">{{ $trendValue }}</span>
        </div>
        <span class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'mr-2' : 'ml-2' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('vs_last_month') }}
        </span>
    </div>
    @endif
</div>

