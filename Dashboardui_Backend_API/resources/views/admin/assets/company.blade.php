@extends('layouts.admin')

@section('title', get_translation('company_settings'))
@section('description', get_translation('company_settings'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('company_settings') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('company_settings_description') }}
            </p>
        </div>
    </div>

    <!-- Company Settings Form -->
    <div class="admin-card p-6">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-6">
            {!! lucide_icon('building', 'w-6 h-6', 'var(--primary-color)') !!}
            <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('company_information') }}
            </h2>
        </div>

        <form id="companyForm" class="space-y-6">
            @csrf
            @php
                $company = get_company_info();
            @endphp

            <!-- Company Name -->
            <div>
                <label for="companyName" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('company_name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="companyName" 
                       name="name" 
                       value="{{ old('name', $company['name'] ?? 'Technodec') }}"
                       required
                       class="admin-input w-full" 
                       style="color: var(--text-color); background: var(--surface-color);"
                       placeholder="{{ get_translation('company_name_placeholder') }}">
                <p class="mt-1 text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('company_name_help') }}
                </p>
            </div>

            <!-- Company Tagline -->
            <div>
                <label for="companyTagline" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('company_tagline') }}
                </label>
                <input type="text" 
                       id="companyTagline" 
                       name="tagline" 
                       value="{{ old('tagline', $company['tagline'] ?? 'Admin Panel') }}"
                       class="admin-input w-full" 
                       style="color: var(--text-color); background: var(--surface-color);"
                       placeholder="{{ get_translation('company_tagline_placeholder') }}">
                <p class="mt-1 text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('company_tagline_help') }}
                </p>
            </div>

            <!-- Company Description -->
            <div>
                <label for="companyDescription" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('company_description') }}
                </label>
                <textarea id="companyDescription" 
                          name="description" 
                          rows="4"
                          class="admin-input w-full" 
                          style="color: var(--text-color); background: var(--surface-color);"
                          placeholder="{{ get_translation('company_description_placeholder') }}">{{ old('description', $company['description'] ?? '') }}</textarea>
                <p class="mt-1 text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('company_description_help') }}
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                <button type="submit" 
                        class="px-6 py-2 text-white rounded-lg transition-colors duration-200 hover:opacity-90 flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}"
                        style="background: var(--primary-color);">
                    {!! lucide_icon('save', 'w-4 h-4', 'currentColor') !!}
                    <span>{{ get_translation('save_changes') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Current Information -->
    <div class="admin-card p-6">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
            {!! lucide_icon('info', 'w-6 h-6', 'var(--primary-color)') !!}
            <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('current_company_info') }}
            </h3>
        </div>

        <div class="space-y-3">
            <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                    <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                        {{ get_translation('company_name') }}:
                    </span>
                    <span class="text-sm font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_company_name() }}
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                    <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                        {{ get_translation('company_tagline') }}:
                    </span>
                    <span class="text-sm font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_company_tagline() }}
                    </span>
                </div>
            </div>

            @if(get_company_description())
            <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                    <span class="text-sm font-medium" style="color: var(--text-secondary-color);">
                        {{ get_translation('company_description') }}:
                    </span>
                    <p class="mt-1 text-sm" style="color: var(--text-color);">
                        {{ get_company_description() }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2 {{ is_rtl_language(app()->getLocale()) ? 'right-auto left-4' : '' }}"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('companyForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        fetch('{{ route_with_lang("admin.assets.company.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || '{{ get_translation("company_updated_success") }}', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(data.message || '{{ get_translation("company_update_failed") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ get_translation("company_update_failed") }}', 'error');
        });
    });

    function showToast(message, type) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' 
            ? 'bg-green-500' 
            : 'bg-red-500';
        
        toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0 opacity-100`;
        toast.textContent = message;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100px)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
});
</script>
@endsection

