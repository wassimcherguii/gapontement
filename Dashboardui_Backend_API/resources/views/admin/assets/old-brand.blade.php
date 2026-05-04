@extends('layouts.admin')

@section('title', get_translation('old_brand_management'))
@section('description', get_translation('old_brand_management_description'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('old_brand_management') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('old_brand_management_description') }}
            </p>
        </div>
    </div>

    <!-- Old Logos Section -->
    <div class="admin-card p-6">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-6">
            {!! lucide_icon('image', 'w-6 h-6', 'var(--primary-color)') !!}
            <h2 class="text-xl font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('old_logos') }}
            </h2>
        </div>
        
        <div id="logosGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Logos will be loaded here via JavaScript -->
        </div>
        
        <div id="logosLoading" class="text-center py-8">
            <div class="inline-flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2" style="border-color: var(--primary-color);"></div>
                <span class="text-sm" style="color: var(--text-secondary-color);">{{ get_translation('loading') }}...</span>
            </div>
        </div>
    </div>

    <!-- Old Favicons Section -->
    <div class="admin-card p-6">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-6">
            {!! lucide_icon('star', 'w-6 h-6', 'var(--primary-color)') !!}
            <h2 class="text-xl font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('old_favicons') }}
            </h2>
        </div>
        
        <div id="faviconsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Favicons will be loaded here via JavaScript -->
        </div>
        
        <div id="faviconsLoading" class="text-center py-8">
            <div class="inline-flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2" style="border-color: var(--primary-color);"></div>
                <span class="text-sm" style="color: var(--text-secondary-color);">{{ get_translation('loading') }}...</span>
            </div>
        </div>
    </div>
    </div>

    <!-- Sync Section -->
    <div class="admin-card p-6">
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

    <!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="relative max-w-4xl max-h-[90vh] w-full mx-4">
        <button onclick="closeImagePreview()" 
                class="absolute top-4 right-4 z-10 p-2 rounded-full shadow-lg transition-colors"
                style="background: var(--surface-color); color: var(--text-color);">
            {!! lucide_icon('x', 'w-6 h-6', 'currentColor') !!}
        </button>
        
        <div class="rounded-lg overflow-hidden" style="background: var(--surface-color);">
            <div class="p-4 border-b" style="border-color: var(--border-color); background: var(--surface-color);">
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('image_preview') }}
                </h3>
                <p id="previewFilename" class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                    <!-- Filename will be inserted here -->
                </p>
            </div>
            
            <div class="p-4 flex items-center justify-center bg-gray-50" style="min-height: 400px;">
                <img id="previewImage" src="" alt="" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-lg">
            </div>
            
            <div class="p-4 border-t" style="border-color: var(--border-color);">
                <div class="flex space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                    <button onclick="closeImagePreview()" 
                            class="flex-1 px-4 py-2 border rounded-lg transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700" 
                            style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                        {{ get_translation('close') }}
                    </button>
                    <button onclick="downloadImage()" 
                            class="flex-1 px-4 py-2 text-white rounded-lg transition-colors duration-200 flex items-center justify-center" 
                            style="background: var(--primary-color);">
                        {!! lucide_icon('download', 'w-4 h-4', 'currentColor') !!}
                        <span class="ml-2">{{ get_translation('download') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="relative max-w-md w-full mx-4">
        <div class="rounded-lg overflow-hidden" style="background: var(--surface-color);">
            <div class="p-6">
                <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" style="background: #fef2f2;">
                        {!! lucide_icon('trash-2', 'w-6 h-6', '#dc2626') !!}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                            {{ get_translation('confirm_delete') }}
                        </h3>
                        <p class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                            {{ get_translation('delete_warning') }}
                        </p>
                    </div>
                </div>
                
                <div class="mb-6 p-4 rounded-lg" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: #dc2626;">
                        {{ get_translation('delete_warning_text') }}
                    </p>
                </div>
                
                <div class="flex space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                    <button onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2 border rounded-lg transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700" 
                            style="border-color: var(--border-color); color: var(--text-color); background: var(--surface-color);">
                        {{ get_translation('cancel') }}
                    </button>
                    <button onclick="confirmDelete()" 
                            class="flex-1 px-4 py-2 text-white rounded-lg transition-colors duration-200 flex items-center justify-center" 
                            style="background: #dc2626;">
                        {!! lucide_icon('trash-2', 'w-4 h-4', 'currentColor') !!}
                        <span class="ml-2">{{ get_translation('delete') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Restore Modal -->
<div id="restoreModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="admin-card p-6 max-w-md w-full mx-4" style="background: var(--surface-color);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('restore_image') }}
            </h3>
            <button onclick="closeRestoreModal()" class="text-gray-500 hover:text-gray-700">
                {!! lucide_icon('x', 'w-6 h-6', 'currentColor') !!}
            </button>
        </div>
        
        <form id="restoreForm">
            @csrf
            <input type="hidden" id="restoreType" name="type">
            <input type="hidden" id="restoreFilename" name="filename">
            
            <div class="mb-4">
                <img id="restorePreview" src="" alt="" class="w-full h-32 object-contain rounded-lg border" style="border-color: var(--border-color);">
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('name') }}
                    </label>
                    <input type="text" name="name" id="restoreName" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('alt_text') }}
                    </label>
                    <input type="text" name="alt" id="restoreAlt" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                           style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('description') }}
                    </label>
                    <textarea name="description" id="restoreDescription" rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2" 
                              style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"></textarea>
                </div>
            </div>
            
            <div class="flex space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mt-6">
                <button type="button" onclick="closeRestoreModal()" 
                        class="flex-1 px-4 py-2 border rounded-lg transition-colors duration-200" 
                        style="border-color: var(--border-color); color: var(--text-color);">
                    {{ get_translation('cancel') }}
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 text-white rounded-lg transition-colors duration-200" 
                        style="background: var(--primary-color);">
                    {{ get_translation('restore') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadLogos();
    loadFavicons();
    
    // Restore form submission
    document.getElementById('restoreForm').addEventListener('submit', function(e) {
        e.preventDefault();
        restoreImage();
    });
    
    // Sync button functionality
    document.getElementById('syncButton').addEventListener('click', function() {
        syncToJson();
    });
    
    // Check for changes on page load
    checkForChanges();
});

function loadLogos() {
    fetch('{{ route_with_lang("admin.assets.old-brand.logos") }}')
        .then(response => response.json())
        .then(data => {
            displayImages(data.logos, 'logosGrid', 'logo');
            document.getElementById('logosLoading').style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading logos:', error);
            document.getElementById('logosLoading').innerHTML = '<p class="text-red-500">{{ get_translation("error_loading_images") }}</p>';
        });
}

function loadFavicons() {
    fetch('{{ route_with_lang("admin.assets.old-brand.favicons") }}')
        .then(response => response.json())
        .then(data => {
            displayImages(data.favicons, 'faviconsGrid', 'favicon');
            document.getElementById('faviconsLoading').style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading favicons:', error);
            document.getElementById('faviconsLoading').innerHTML = '<p class="text-red-500">{{ get_translation("error_loading_images") }}</p>';
        });
}

function displayImages(images, containerId, type) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    images.forEach(image => {
        const imageCard = createImageCard(image, type);
        container.appendChild(imageCard);
    });
}

function createImageCard(image, type) {
    const card = document.createElement('div');
    card.className = 'relative group bg-white rounded-lg border overflow-hidden transition-all duration-200 hover:shadow-lg';
    card.style.borderColor = 'var(--border-color)';
    
    const isCurrent = image.is_current;
    const currentBadge = isCurrent ? `
        <div class="absolute top-2 left-2 z-10">
            <span class="px-2 py-1 text-xs font-semibold text-white rounded-full" style="background: var(--primary-color);">
                {{ get_translation('current') }}
            </span>
        </div>
    ` : '';
    
    card.innerHTML = `
        ${currentBadge}
        <div class="aspect-square p-4 flex items-center justify-center">
            <img src="${image.url}" alt="${image.filename}" class="max-w-full max-h-full object-contain">
        </div>
        <div class="p-3 border-t" style="border-color: var(--border-color);">
            <p class="text-sm font-medium truncate" style="color: var(--text-color);">${image.filename}</p>
            <p class="text-xs" style="color: var(--text-secondary-color);">${formatFileSize(image.size)}</p>
            <p class="text-xs" style="color: var(--text-secondary-color);">${formatDate(image.modified)}</p>
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <div class="flex space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <button onclick="viewImage('${image.url}', '${image.filename}')" 
                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition-colors">
                    {!! lucide_icon('eye', 'w-4 h-4', '#374151') !!}
                </button>
                ${!isCurrent ? `
                    <button onclick="openRestoreModal('${type}', '${image.filename}', '${image.url}')" 
                            class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition-colors">
                        {!! lucide_icon('refresh-cw', 'w-4 h-4', 'var(--primary-color)') !!}
                    </button>
                    <button onclick="deleteImage('${type}', '${image.filename}')" 
                            class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition-colors">
                        {!! lucide_icon('trash-2', 'w-4 h-4', 'var(--error-color)') !!}
                    </button>
                ` : ''}
            </div>
        </div>
    `;
    
    return card;
}

function viewImage(url, filename) {
    // Show image in modal
    document.getElementById('previewImage').src = url;
    document.getElementById('previewImage').alt = filename;
    document.getElementById('previewFilename').textContent = filename;
    
    // Store current image data for download
    window.currentPreviewImage = { url: url, filename: filename };
    
    // Show modal
    document.getElementById('imagePreviewModal').classList.remove('hidden');
    document.getElementById('imagePreviewModal').classList.add('flex');
}

function openRestoreModal(type, filename, url) {
    document.getElementById('restoreType').value = type;
    document.getElementById('restoreFilename').value = filename;
    document.getElementById('restorePreview').src = url;
    document.getElementById('restoreName').value = filename.replace(/\.(jpg|jpeg|png|gif)$/i, '');
    document.getElementById('restoreAlt').value = filename.replace(/\.(jpg|jpeg|png|gif)$/i, '');
    document.getElementById('restoreDescription').value = '';
    
    document.getElementById('restoreModal').classList.remove('hidden');
    document.getElementById('restoreModal').classList.add('flex');
}

function closeImagePreview() {
    document.getElementById('imagePreviewModal').classList.add('hidden');
    document.getElementById('imagePreviewModal').classList.remove('flex');
}

function downloadImage() {
    if (window.currentPreviewImage) {
        const link = document.createElement('a');
        link.href = window.currentPreviewImage.url;
        link.download = window.currentPreviewImage.filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function closeRestoreModal() {
    document.getElementById('restoreModal').classList.add('hidden');
    document.getElementById('restoreModal').classList.remove('flex');
}

function restoreImage() {
    // Prevent multiple clicks
    if (window.isRestoring) {
        return;
    }
    
    window.isRestoring = true;
    const restoreButton = document.querySelector('#restoreModal button[type="submit"]');
    const originalText = restoreButton.innerHTML;
    
    // Disable button and show loading
    restoreButton.disabled = true;
    restoreButton.innerHTML = `
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
        <span class="ml-2">{{ get_translation('restoring') }}...</span>
    `;
    
    const formData = new FormData(document.getElementById('restoreForm'));
    
    fetch('{{ route_with_lang("admin.assets.old-brand.restore") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeRestoreModal();
            // Reload the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || '{{ get_translation("restore_failed") }}', 'error');
            // Re-enable button on error
            restoreButton.disabled = false;
            restoreButton.innerHTML = originalText;
            window.isRestoring = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ get_translation("restore_failed") }}', 'error');
        // Re-enable button on error
        restoreButton.disabled = false;
        restoreButton.innerHTML = originalText;
        window.isRestoring = false;
    });
}

function deleteImage(type, filename) {
    // Store the delete data for confirmation
    window.pendingDelete = { type: type, filename: filename };
    
    // Show the confirmation modal
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
    document.getElementById('deleteConfirmModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
    document.getElementById('deleteConfirmModal').classList.remove('flex');
    window.pendingDelete = null;
    window.isDeleting = false;
}

function confirmDelete() {
    if (!window.pendingDelete) {
        return;
    }
    
    // Prevent multiple clicks
    if (window.isDeleting) {
        return;
    }
    
    window.isDeleting = true;
    const deleteButton = document.querySelector('#deleteConfirmModal button[onclick="confirmDelete()"]');
    const originalText = deleteButton.innerHTML;
    
    // Disable button and show loading
    deleteButton.disabled = true;
    deleteButton.innerHTML = `
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
        <span class="ml-2">{{ get_translation('deleting') }}...</span>
    `;
    
    const { type, filename } = window.pendingDelete;
    
    fetch('{{ route_with_lang("admin.assets.old-brand.delete") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type,
            filename: filename
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeDeleteModal();
            // Reload the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || '{{ get_translation("delete_failed") }}', 'error');
            // Re-enable button on error
            deleteButton.disabled = false;
            deleteButton.innerHTML = originalText;
            window.isDeleting = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ get_translation("delete_failed") }}', 'error');
        // Re-enable button on error
        deleteButton.disabled = false;
        deleteButton.innerHTML = originalText;
        window.isDeleting = false;
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDate(timestamp) {
    return new Date(timestamp * 1000).toLocaleDateString();
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
            document.getElementById('syncButton').disabled = true;
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
        const syncButton = document.getElementById('syncButton');
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
</script>
@endsection
