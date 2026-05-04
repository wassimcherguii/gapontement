@extends('layouts.admin')

@section('title', get_translation('language_management'))
@section('description', get_translation('language_management'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('language_management') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('language_management') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route_with_lang('admin.assets.client-translations.index', ['domain' => 'mobile']) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
               style="background: var(--primary-color); color: #fff;">
                {{ get_translation('client_translations_link') }}
            </a>
            <a href="{{ route_with_lang('admin.assets.translation-domains.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border transition-colors duration-200"
               style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('translation_domains_heading') }}
            </a>
        </div>
    </div>

    <!-- Default Language -->
    <div class="admin-card p-6 max-w-2xl">
        <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} mb-4" style="color: var(--text-color);">
            {{ get_translation('default_language') }}
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <span class="text-2xl">{{ get_supported_languages()[get_default_language()]['flag'] }}</span>
                <div class="flex-1">
                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_supported_languages()[get_default_language()]['native'] }}
                    </p>
                    <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                        {{ get_translation('current_default') }}
                    </p>
                </div>
            </div>
            
            <select id="defaultLanguageSelect" class="w-full px-3 py-2 text-sm rounded border" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);" onchange="updateDefaultLanguage()">
                @foreach(get_supported_languages() as $code => $language)
                    <option value="{{ $code }}" {{ $code === get_default_language() ? 'selected' : '' }}>
                        {{ $language['native'] }} ({{ $language['name'] }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="languageToast" class="hidden fixed bottom-4 right-4 z-50 max-w-md">
    <div class="admin-card p-4 flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} shadow-lg border-l-4" style="border-color: var(--success-color);">
        <span id="languageToastIcon">{!! lucide_icon('check-circle', 'w-5 h-5') !!}</span>
        <p id="languageToastMessage" class="text-sm flex-1"></p>
    </div>
</div>

<script>
let isUpdatingLanguage = false;

function updateDefaultLanguage() {
    if (isUpdatingLanguage) {
        return;
    }
    
    const select = document.getElementById('defaultLanguageSelect');
    const newLanguage = select.value;
    const oldLanguage = '{{ get_default_language() }}';
    
    if (newLanguage === oldLanguage) {
        return;
    }
    
    // Show confirmation
    if (!confirm('{{ __("messages.confirm_change_default_language") }}')) {
        // Reset select to old value
        select.value = oldLanguage;
        return;
    }
    
    isUpdatingLanguage = true;
    
    // Show loading state
    showLanguageToast('{{ __("messages.updating") }}...', 'loading');
    select.disabled = true;
    
    // Make request
    fetch('{{ route_with_lang("admin.assets.languages.update-default") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            default_language: newLanguage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showLanguageToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showLanguageToast(data.message || '{{ __("messages.error_updating_language") }}', 'error');
            select.value = oldLanguage;
            select.disabled = false;
            isUpdatingLanguage = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showLanguageToast('{{ __("messages.error_occurred") }}', 'error');
        select.value = oldLanguage;
        select.disabled = false;
        isUpdatingLanguage = false;
    });
}

function showLanguageToast(message, type) {
    const toast = document.getElementById('languageToast');
    const toastMessage = document.getElementById('languageToastMessage');
    const toastIcon = document.getElementById('languageToastIcon');
    
    if (type === 'success') {
        toastIcon.innerHTML = '{!! lucide_icon('check-circle', 'w-5 h-5') !!}';
        toast.style.borderColor = 'var(--success-color)';
    } else if (type === 'error') {
        toastIcon.innerHTML = '{!! lucide_icon('alert-circle', 'w-5 h-5') !!}';
        toast.style.borderColor = 'var(--error-color)';
    } else if (type === 'loading') {
        toastIcon.innerHTML = '<svg class="w-5 h-5 animate-spin" style="color: var(--primary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        toast.style.borderColor = 'var(--primary-color)';
    }
    
    toastMessage.textContent = message;
    toast.classList.remove('hidden');
    
    if (type === 'success' || type === 'loading') {
        // Don't auto-hide for success/loading
        return;
    }
    
    // Auto hide after 5 seconds for errors
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}
</script>
@endsection

