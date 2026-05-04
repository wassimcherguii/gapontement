@extends('layouts.admin')

@section('title', get_translation('brand_management'))
@section('description', get_translation('brand_management'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('brand_management') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('brand_management') }}
            </p>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Logo Upload -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('upload', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('upload_logo') }}
                </h3>
            </div>
            
            <form id="logoUploadForm" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('logo_file') }}
                    </label>
                    <input type="file" name="logo" id="logoFile" accept="image/png,image/jpg,image/jpeg" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('alt_text') }}
                    </label>
                    <input type="text" name="alt_text" id="logoAlt" placeholder="{{ get_company_name() }} Logo" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('description') }}
                    </label>
                    <textarea name="description" id="logoDescription" rows="2" 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                              style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                              placeholder="Main company logo used in headers, sidebars, and branding"></textarea>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 text-white rounded-lg transition-colors duration-200" 
                        style="background: var(--primary-color);">
                    {{ get_translation('upload_logo') }}
                </button>
            </form>
        </div>

        <!-- Favicon Upload -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('upload', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('upload_favicon') }}
                </h3>
            </div>
            
            <form id="faviconUploadForm" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('favicon_file') }}
                    </label>
                    <input type="file" name="favicon" id="faviconFile" accept="image/png,image/ico" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('alt_text') }}
                    </label>
                    <input type="text" name="alt_text" id="faviconAlt" placeholder="{{ get_company_name() }} Favicon" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('description') }}
                    </label>
                    <textarea name="description" id="faviconDescription" rows="2" 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                              style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                              placeholder="Browser tab icon and favicon"></textarea>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 text-white rounded-lg transition-colors duration-200" 
                        style="background: var(--primary-color);">
                    {{ get_translation('upload_favicon') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Current vs Pending Changes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Current JSON Data -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('file-text', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('current_assets') }}
                </h3>
            </div>
            
            <div class="space-y-4" id="currentAssets">
                @php
                    $jsonAssets = get_brand_assets();
                @endphp
                
                <!-- Current Logo -->
                <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                    <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset($jsonAssets['logo']['path']) }}" alt="{{ $jsonAssets['logo']['alt'] }}" class="w-8 h-8 object-contain">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                                {{ $jsonAssets['logo']['alt'] }}
                            </p>
                            <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                                {{ $jsonAssets['logo']['filename'] }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Current Favicon -->
                <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                    <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset($jsonAssets['favicon']['path']) }}" alt="{{ $jsonAssets['favicon']['alt'] }}" class="w-6 h-6 object-contain">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                                {{ $jsonAssets['favicon']['alt'] }}
                            </p>
                            <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                                {{ $jsonAssets['favicon']['filename'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Database Changes -->
        <div class="admin-card p-6">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                {!! lucide_icon('database', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('pending_changes') }}
                </h3>
            </div>
            
            <div class="space-y-4" id="pendingChanges">
                @php
                    $dbLogos = get_all_logos_from_db();
                @endphp
                
                @if($dbLogos->count() > 0)
                    @foreach($dbLogos as $logo)
                        <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                            <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    @php
                                        $isIco = \Illuminate\Support\Str::endsWith(strtolower($logo->filename), '.ico');
                                        $storageUrl = asset('storage/' . $logo->path);
                                        $publicUrl = asset($logo->path);
                                    @endphp
                                    @if($isIco)
                                        <div class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center text-[10px] text-gray-600">ICO</div>
                                    @else
                                        <img src="{{ $storageUrl }}" alt="{{ $logo->alt }}" class="w-8 h-8 object-contain"
                                             onerror="this.onerror=null;this.src='{{ $publicUrl }}'">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                                        {{ $logo->name }}
                                    </p>
                                    <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                                        {{ $logo->filename }}
                                    </p>
                                    @if($logo->description)
                                        <p class="text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                                            {{ $logo->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
                        <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                            {{ get_translation('no_pending_changes') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sync Section -->
    <div class="admin-card p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                {!! lucide_icon('git-compare', 'w-6 h-6', 'var(--primary-color)') !!}
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('sync_changes') }}
                </h3>
            </div>
            <button id="syncButton" class="px-6 py-2 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" 
                    style="background: var(--primary-color);" disabled>
                {!! lucide_icon('sync', 'w-4 h-4', 'currentColor') !!}
                <span class="ml-2">{{ get_translation('sync_to_json') }}</span>
            </button>
        </div>
        
        <div class="p-4 rounded-lg border" style="border-color: var(--border-color); background: var(--surface-color);">
            <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('sync_description') }}
            </p>
        </div>
    </div>

    <!-- Brand Importance -->
    <div class="admin-card p-6">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
            {!! lucide_icon('award', 'w-6 h-6', 'var(--primary-color)') !!}
            <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('brand_importance') }}
            </h3>
        </div>
        
        <div class="space-y-4">
            <div class="p-4 rounded-lg border-l-4" style="background: var(--surface-color); border-left-color: var(--primary-color);">
                <h4 class="font-semibold mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('brand_identity') }}
                </h4>
                <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('brand_identity_description') }}
                </p>
            </div>
            
            <div class="p-4 rounded-lg border-l-4" style="background: var(--surface-color); border-left-color: var(--primary-color);">
                <h4 class="font-semibold mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('brand_consistency') }}
                </h4>
                <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('brand_consistency_description') }}
                </p>
            </div>
            
            <div class="p-4 rounded-lg border-l-4" style="background: var(--surface-color); border-left-color: var(--primary-color);">
                <h4 class="font-semibold mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('brand_trust') }}
                </h4>
                <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    {{ get_translation('brand_trust_description') }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload forms
    const logoForm = document.getElementById('logoUploadForm');
    const faviconForm = document.getElementById('faviconUploadForm');
    const syncButton = document.getElementById('syncButton');
    
    // Logo upload
    logoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        uploadAsset('logo', this);
    });
    
    // Favicon upload
    faviconForm.addEventListener('submit', function(e) {
        e.preventDefault();
        uploadAsset('favicon', this);
    });
    
    // Sync button
    syncButton.addEventListener('click', function() {
        syncToJson();
    });
    
    // Check for changes on page load
    checkForChanges();
    
    function uploadAsset(type, form) {
        const formData = new FormData(form);
        const url = type === 'logo' ? '{{ route_with_lang("admin.assets.brand.upload-logo") }}' : '{{ route_with_lang("admin.assets.brand.upload-favicon") }}';
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('{{ get_translation("upload_success") }}', 'success');
                form.reset();
                checkForChanges();
                // Reload the page to show updated pending changes
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || '{{ get_translation("upload_failed") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('{{ get_translation("upload_failed") }}', 'error');
        });
    }
    
    function syncToJson() {
        fetch('{{ route_with_lang("admin.assets.brand.sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('{{ get_translation("sync_success") }}', 'success');
                syncButton.disabled = true;
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification(data.message || '{{ get_translation("sync_failed") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('{{ get_translation("sync_failed") }}', 'error');
        });
    }
    
    function checkForChanges() {
        fetch('{{ route_with_lang("admin.assets.brand.comparison") }}')
        .then(response => response.json())
        .then(data => {
            if (data.has_changes) {
                syncButton.disabled = false;
                syncButton.style.background = 'var(--primary-color)';
            } else {
                syncButton.disabled = true;
                syncButton.style.background = 'var(--text-secondary-color)';
            }
        })
        .catch(error => {
            console.error('Error checking changes:', error);
        });
    }
    
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection
