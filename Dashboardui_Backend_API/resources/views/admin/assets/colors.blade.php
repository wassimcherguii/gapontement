@extends('layouts.admin')

@section('title', get_translation('color_management'))
@section('description', get_translation('color_management'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('color_management') }}
            </h1>
            <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                {{ get_translation('color_management_description') }}
            </p>
        </div>
        
        <!-- Sync Buttons -->
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
            <button onclick="checkForChanges()" class="admin-button-secondary flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                {!! lucide_icon('search', 'w-4 h-4') !!}
                <span>{{ get_translation('check_changes') }}</span>
            </button>
            <button id="syncAllBtn" onclick="syncAllToJson()" class="hidden admin-button-primary flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                {!! lucide_icon('sync', 'w-4 h-4') !!}
                <span>Sync All to JSON</span>
            </button>
            <button id="revertBtn" onclick="revertFromJson()" class="hidden admin-button-secondary flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                {!! lucide_icon('rotate-ccw', 'w-4 h-4') !!}
                <span>{{ get_translation('revert_changes') }}</span>
            </button>
        </div>
    </div>

    <!-- Save Buttons (top and bottom, hidden by default) -->
    <div id="saveButtonTop" class="mb-6 sticky top-0 z-10">
        <div class="admin-card p-4 flex items-center justify-between">
            <span class="text-sm" style="color: var(--text-color);">
                Unsaved changes detected
            </span>
            <button id="saveButtonTopBtn" onclick="saveAllChanges()" disabled class="admin-button-primary flex items-center space-x-2 opacity-50 cursor-not-allowed {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <span id="saveCountTop" class="bg-white text-primary rounded-full px-2 py-1 text-sm font-bold">0</span>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Compare Section (hidden by default) -->
    <div id="compareSection" class="admin-card p-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('compare_db_json') }}
            </h3>
            <button onclick="closeCompareSection()" class="text-sm admin-button-secondary">
                {{ get_translation('close') }}
            </button>
        </div>
        
        <div id="compareContent" class="space-y-4">
            <!-- Comparison results will be loaded here -->
        </div>
    </div>

    <!-- Color Display by Theme -->
    @foreach(['light' => 'Light Theme', 'dark' => 'Dark Theme'] as $theme => $themeLabel)
    <div class="space-y-4">
        <h2 class="text-xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
            {{ $themeLabel }}
        </h2>
        
        @php
            $categories = ['brand', 'complementary', 'neutral', 'shadows', 'semantic', 'usage'];
            $themeColors = isset($dbColors[$theme]) ? $dbColors[$theme] : [];
            $themeJsonColors = isset($jsonColors[$theme]) ? $jsonColors[$theme] : [];
        @endphp
        
        @foreach($categories as $category)
            @php
                $dbCategoryColors = isset($themeColors[$category]) ? $themeColors[$category] : collect();
                $jsonCategoryColors = isset($themeJsonColors[$category]) ? $themeJsonColors[$category] : [];
            @endphp
            
            @if($dbCategoryColors->count() > 0 || count($jsonCategoryColors) > 0)
            <div class="admin-card p-6">
                <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} mb-4" style="color: var(--text-color);">
                    {{ ucfirst(str_replace('-', ' ', $category)) }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($dbCategoryColors as $dbColor)
                        @php
                            $jsonValue = null;
                            if ($category === 'shadows' && isset($jsonCategoryColors['primary'][$dbColor->name])) {
                                $jsonValue = $jsonCategoryColors['primary'][$dbColor->name];
                            } elseif (isset($jsonCategoryColors[$dbColor->name])) {
                                $jsonValue = $jsonCategoryColors[$dbColor->name];
                            }
                            $isDifferent = $jsonValue !== null && strtolower($jsonValue) !== strtolower($dbColor->hex_value);
                        @endphp
                        
                        <div class="border rounded-lg p-4 {{ $isDifferent ? 'border-orange-500' : 'border-gray-300' }}">
                            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-3">
                                <div class="w-12 h-12 rounded-lg shadow-md" style="background: {{ $dbColor->hex_value }};"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                                        {{ $dbColor->name }}
                                    </p>
                                    @if($isDifferent)
                                        <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--warning-color);">
                                            {{ get_translation('different_from_json') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- JSON Value (Read-only) -->
                            @if($jsonValue)
                            <div class="mb-3">
                                <label class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                                    {{ get_translation('json_value') }}
                                </label>
                                <div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mt-1">
                                    <input type="text" value="{{ $jsonValue }}" readonly class="admin-input flex-1 bg-gray-50 dark:bg-gray-800" style="color: var(--text-color);">
                                    <div class="w-8 h-8 rounded border" style="background: {{ $jsonValue }};"></div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- DB Value (Editable) -->
                            <div>
                                <label class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                                    {{ get_translation('database_value') }}
                                </label>
                                <div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mt-1">
                                    <input 
                                        type="color" 
                                        value="{{ substr($dbColor->hex_value, 0, 7) }}" 
                                        id="colorpicker_{{ $dbColor->id }}"
                                        data-color-id="{{ $dbColor->id }}"
                                        data-has-alpha="{{ strlen($dbColor->hex_value) > 7 ? 'true' : 'false' }}"
                                        class="h-8 w-12 rounded border cursor-pointer"
                                        onchange="onColorPickerChange({{ $dbColor->id }})"
                                    >
                                    <div class="relative flex-1">
                                        <input 
                                            type="text" 
                                            value="{{ $dbColor->hex_value }}" 
                                            id="color_{{ $dbColor->id }}"
                                            data-color-id="{{ $dbColor->id }}"
                                            data-original-value="{{ $dbColor->hex_value }}"
                                            class="admin-input w-full color-input pl-8"
                                            style="color: var(--text-color); background: var(--surface-color);"
                                            onkeypress="if(event.key === 'Enter') { event.preventDefault(); }"
                                            oninput="onTextInputChange({{ $dbColor->id }})"
                                        >
                                        <span id="check_{{ $dbColor->id }}" class="hidden absolute left-2 top-1/2 transform -translate-y-1/2 text-green-600 dark:text-green-400">
                                            {!! lucide_icon('check-circle', 'w-4 h-4') !!}
                                        </span>
                                        <span id="error_{{ $dbColor->id }}" class="hidden absolute left-2 top-1/2 transform -translate-y-1/2 text-red-600 dark:text-red-400">
                                            {!! lucide_icon('alert-circle', 'w-4 h-4') !!}
                                        </span>
                                    </div>
                                    <div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" id="preview_{{ $dbColor->id }}" style="background: {{ $dbColor->hex_value }};"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endforeach
    
    <!-- Bottom Save Button (at end of colors) -->
    <div id="saveButtonBottom" class="mt-6">
        <div class="admin-card p-4 flex items-center justify-between opacity-50">
            <span class="text-sm" style="color: var(--text-color);">
                Unsaved changes detected
            </span>
            <button id="saveButtonBottomBtn" onclick="saveAllChanges()" disabled class="admin-button-primary flex items-center space-x-2 opacity-50 cursor-not-allowed {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <span id="saveCountBottom" class="bg-white text-primary rounded-full px-2 py-1 text-sm font-bold">0</span>
                <span>Save Changes</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 transform translate-x-full transition-transform duration-300 z-50">
    <div class="admin-card p-4 flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
        <div id="toastIcon"></div>
        <div>
            <p id="toastMessage" class="text-sm font-medium"></p>
        </div>
    </div>
</div>

<!-- Confirm Save Modal -->
<div id="confirmSaveModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="relative p-4 w-full max-w-md">
        <div class="admin-card p-6">
            <h3 class="mb-4 text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('confirm_save') }}
            </h3>
            <p class="mb-6 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                Save <span id="confirmCount" class="font-bold" style="color: var(--primary-color);">0</span> color change(s)?
            </p>
            <div class="flex justify-end space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <button type="button" onclick="closeConfirmModal()" class="admin-button-secondary px-6 py-3 text-base min-w-[100px] border-2 rounded-lg transition-colors duration-200 hover:bg-opacity-80 hover:scale-105">
                    {{ get_translation('cancel') }}
                </button>
                <button type="button" onclick="confirmSave()" class="admin-button-primary px-6 py-3 text-base min-w-[100px] border-2 rounded-lg transition-colors duration-200 hover:opacity-90 hover:scale-105">
                    {{ get_translation('save') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Revert Modal -->
<div id="confirmRevertModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="relative p-4 w-full max-w-md">
        <div class="admin-card p-6">
            <h3 class="mb-4 text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('revert_changes') }}
            </h3>
            <p class="mb-6 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                {{ get_translation('confirm_revert_changes') }}
            </p>
            <div class="flex justify-end space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <button type="button" onclick="closeConfirmRevertModal()" class="admin-button-secondary px-6 py-3 text-base min-w-[100px] border-2 rounded-lg transition-colors duration-200 hover:bg-opacity-80 hover:scale-105">
                    {{ get_translation('cancel') }}
                </button>
                <button type="button" onclick="confirmRevert()" class="admin-button-primary px-6 py-3 text-base min-w-[100px] border-2 rounded-lg transition-colors duration-200 hover:opacity-90 hover:scale-105">
                    {{ get_translation('revert_changes') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let isUpdating = {};
let changedColors = new Set();
let invalidColors = new Set();
let pendingColors = [];

// Handle color picker change
function onColorPickerChange(colorId) {
    console.log('onColorPickerChange called with colorId:', colorId);
    const picker = document.getElementById(`colorpicker_${colorId}`);
    const input = document.getElementById(`color_${colorId}`);
    
    if (!picker || !input) {
        console.error('Picker or input not found for colorId:', colorId);
        return;
    }
    
    const hexValue = picker.value.toUpperCase();
    console.log('Picker hex value:', hexValue);
    
    // Check if original had alpha channel
    const originalValue = input.dataset.originalValue;
    let finalHexValue = hexValue;
    
    // If original had alpha, preserve it
    if (originalValue && originalValue.length > 7) {
        finalHexValue = hexValue + originalValue.substring(7);
    }
    
    console.log('Final hex value:', finalHexValue);
    
    // Update text input with full value
    input.value = finalHexValue;
    
    // Update preview
    updatePreview(colorId, finalHexValue);
    
    // Check if changed
    console.log('Original value:', originalValue);
    if (finalHexValue !== originalValue) {
        changedColors.add(colorId);
        showCheckIcon(colorId, true);
        console.log('Color changed, added to set. Total:', changedColors.size);
    } else {
        changedColors.delete(colorId);
        showCheckIcon(colorId, false);
        console.log('Color not changed, removed from set. Total:', changedColors.size);
    }
    
    updateSaveButton();
}

// Handle text input change
function onTextInputChange(colorId) {
    console.log('onTextInputChange called with colorId:', colorId);
    const input = document.getElementById(`color_${colorId}`);
    const picker = document.getElementById(`colorpicker_${colorId}`);
    
    if (!input) {
        console.error('Input not found for colorId:', colorId);
        return;
    }
    
    const hexValue = input.value;
    console.log('Hex value:', hexValue);
    
    // Don't validate while typing - allow intermediate states
    console.log('Processing hex value:', hexValue);
    
    // For alpha colors, only use the 6-character part for picker
    const hex6 = hexValue.substring(0, 7);
    
    // Update color picker - only 6 character colors supported
    if (picker) {
        picker.value = hex6;
    }
    
    // Validate hex format
    const isValidHex = /^#[A-Fa-f0-9]{6,8}$/.test(hexValue);
    
    if (!isValidHex && hexValue.length > 1) {
        // Show error if invalid
        invalidColors.add(colorId);
        showErrorIcon(colorId, true);
        showCheckIcon(colorId, false);
        console.log('Invalid hex format:', hexValue);
    } else {
        // Clear error if valid
        invalidColors.delete(colorId);
        showErrorIcon(colorId, false);
        
        // Check if changed - compare full value with original
        const originalValue = input.dataset.originalValue;
        console.log('Original value:', originalValue);
        
        // For comparison, use original hex but normalize case
        if (hexValue.toUpperCase() !== originalValue.toUpperCase()) {
            changedColors.add(colorId);
            showCheckIcon(colorId, true);
            console.log('Color changed, added to set. Total:', changedColors.size);
        } else {
            changedColors.delete(colorId);
            showCheckIcon(colorId, false);
            console.log('Color not changed, removed from set. Total:', changedColors.size);
        }
    }
    
    // Update preview only if valid hex
    if (isValidHex) {
        updatePreview(colorId, hexValue);
    }
    
    updateSaveButton();
}

function updatePreview(colorId, hexValue) {
    document.getElementById(`preview_${colorId}`).style.background = hexValue;
}

function showCheckIcon(colorId, show) {
    const checkIcon = document.getElementById(`check_${colorId}`);
    if (checkIcon) {
        if (show) {
            checkIcon.classList.remove('hidden');
        } else {
            checkIcon.classList.add('hidden');
        }
    }
}

function showErrorIcon(colorId, show) {
    const errorIcon = document.getElementById(`error_${colorId}`);
    const input = document.getElementById(`color_${colorId}`);
    
    if (errorIcon) {
        if (show) {
            errorIcon.classList.remove('hidden');
        } else {
            errorIcon.classList.add('hidden');
        }
    }
    
    if (input) {
        if (show) {
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    }
}

function updateSaveButton() {
    const saveButtonTop = document.getElementById('saveButtonTop');
    const saveButtonBottom = document.getElementById('saveButtonBottom');
    const saveCountTop = document.getElementById('saveCountTop');
    const saveCountBottom = document.getElementById('saveCountBottom');
    const saveButtons = [
        document.getElementById('saveButtonTopBtn'),
        document.getElementById('saveButtonBottomBtn')
    ].filter(btn => btn !== null);
    
    saveCountTop.textContent = changedColors.size;
    saveCountBottom.textContent = changedColors.size;
    
    // Disable save if there are invalid colors
    const canSave = changedColors.size > 0 && invalidColors.size === 0;
    
    if (canSave) {
        saveButtonTop.classList.remove('opacity-50');
        saveButtonBottom.classList.remove('opacity-50');
        saveButtons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    } else {
        saveButtonTop.classList.add('opacity-50');
        saveButtonBottom.classList.add('opacity-50');
        saveButtons.forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }
}

// Save all changed colors
function saveAllChanges() {
    if (changedColors.size === 0) {
        showToast('No changes to save', 'error');
        return;
    }
    
    if (invalidColors.size > 0) {
        showToast(`Cannot save: ${invalidColors.size} invalid color format(s)`, 'error');
        return;
    }
    
    // Show modal instead of confirm
    document.getElementById('confirmCount').textContent = changedColors.size;
    document.getElementById('confirmSaveModal').classList.remove('hidden');
    
    // Store colors to save
    pendingColors = Array.from(changedColors);
}

function confirmSave() {
    if (pendingColors.length === 0) {
        closeConfirmModal();
        return;
    }
    
    const promises = [];
    pendingColors.forEach(colorId => {
        const input = document.getElementById(`color_${colorId}`);
        const hexValue = input.value;
        promises.push(saveColor(colorId, hexValue));
    });
    
    Promise.allSettled(promises).then(results => {
        console.log('Save results:', results);
        
        let actualSuccess = 0;
        let actualFailed = 0;
        
        results.forEach((result, index) => {
            if (result.status === 'fulfilled' && result.value && result.value.success) {
                actualSuccess++;
            } else {
                actualFailed++;
                if (result.status === 'fulfilled') {
                    console.log('Failed result:', result.value);
                } else {
                    console.log('Rejected error:', result.reason);
                }
            }
        });
        
        changedColors.clear();
        updateSaveButton();
        closeConfirmModal();
        
        if (actualFailed === 0) {
            showToast(`Successfully saved ${actualSuccess} color(s)`, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(`Saved ${actualSuccess} but ${actualFailed} failed`, 'error');
            setTimeout(() => location.reload(), 2000);
        }
    });
}

function closeConfirmModal() {
    document.getElementById('confirmSaveModal').classList.add('hidden');
    pendingColors = [];
}

// Save individual color
function saveColor(colorId, hexValue) {
    // Get current language from URL
    const currentLang = window.location.pathname.split('/')[1] || 'en';
    const url = `/${currentLang}/admin/assets/colors/update/${colorId}`;
    
    console.log('Saving color:', colorId, hexValue);
    console.log('URL:', url);
    
    return fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hex_value: hexValue })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Save response:', data);
        if (!data.success) {
            throw new Error(data.message || 'Save failed');
        }
        return data;
    })
    .catch(error => {
        console.error('Error saving color:', error);
        throw error;
    });
}

function updateColor(colorId) {
    console.log('updateColor called for:', colorId);
    
    if (isUpdating[colorId]) {
        console.log('Already updating');
        return;
    }
    
    const input = document.getElementById(`color_${colorId}`);
    const hexValue = input.value.trim();
    
    console.log('Hex value:', hexValue);
    
    // Validate hex color
    if (!/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/.test(hexValue)) {
        showToast('Invalid hex color format', 'error');
        return;
    }
    
    isUpdating[colorId] = true;
    input.disabled = true;
    
    // Get current language from URL
    const currentLang = window.location.pathname.split('/')[1] || 'en';
    const url = `/${currentLang}/admin/assets/colors/update/${colorId}`;
    console.log('URL:', url);
    
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hex_value: hexValue })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            document.getElementById(`preview_${colorId}`).style.background = hexValue;
            showToast(data.message || 'Color updated successfully', 'success');
        } else {
            input.value = input.defaultValue;
            showToast(data.message || 'Failed to update color', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        input.value = input.defaultValue;
        showToast('An error occurred while updating the color', 'error');
    })
    .finally(() => {
        isUpdating[colorId] = false;
        input.disabled = false;
    });
}

function checkForChanges() {
    const compareSection = document.getElementById('compareSection');
    const compareContent = document.getElementById('compareContent');
    
    compareSection.classList.remove('hidden');
    compareContent.innerHTML = '<p class="text-center py-4">{{ get_translation("loading") }}...</p>';
    
    fetch('{{ route_with_lang("admin.assets.colors.comparison") }}')
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.different.length > 0) {
                // Show "Sync All" and "Revert" buttons
                document.getElementById('syncAllBtn').classList.remove('hidden');
                document.getElementById('revertBtn').classList.remove('hidden');
                
                html += '<div class="mb-4">';
                html += '<div class="flex justify-between items-center mb-3">';
                html += '<h4 class="font-semibold" style="color: var(--warning-color);">{{ get_translation("different_colors") }} (' + data.different.length + ')</h4>';
                html += '<div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                html += '<button onclick="revertFromJson()" class="admin-button-secondary text-sm px-4 py-2">{{ get_translation("revert_changes") }}</button>';
                html += '<button onclick="syncAllToJson()" class="admin-button-primary text-sm px-4 py-2">{{ get_translation("sync_all") }}</button>';
                html += '</div>';
                html += '</div>';
                html += '<div class="space-y-2 max-h-64 overflow-y-auto">';
                data.different.forEach(color => {
                    html += '<div class="p-3 border border-orange-300 rounded bg-orange-50">';
                    html += '<div class="flex items-center justify-between">';
                    html += '<div>';
                    html += '<p class="text-sm font-medium">' + color.name + '</p>';
                    html += '<p class="text-xs text-gray-600">' + color.category + ' - ' + color.theme + '</p>';
                    html += '</div>';
                    html += '<button onclick="syncToJson()" class="admin-button-secondary text-xs">{{ get_translation("sync") }}</button>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
                html += '</div>';
            } else {
                // Hide "Sync All" and "Revert" buttons if no differences
                document.getElementById('syncAllBtn').classList.add('hidden');
                document.getElementById('revertBtn').classList.add('hidden');
            }
            
            if (data.same.length > 0) {
                html += '<div>';
                html += '<h4 class="font-semibold mb-2" style="color: var(--success-color);">{{ get_translation("identical_colors") }} (' + data.same.length + ')</h4>';
                html += '<p class="text-sm text-gray-600">{{ get_translation("no_changes_needed") }}</p>';
                html += '</div>';
            }
            
            if (data.different.length === 0 && data.same.length === 0) {
                html = '<p class="text-center py-4 text-gray-600">{{ get_translation("no_colors_found") }}</p>';
            }
            
            compareContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            compareContent.innerHTML = '<p class="text-center py-4 text-red-600">{{ get_translation("error_loading_comparison") }}</p>';
        });
}

function closeCompareSection() {
    document.getElementById('compareSection').classList.add('hidden');
}

function syncToJson() {
    syncAllColors();
}

function syncAllToJson() {
    syncAllColors();
}

function syncAllColors() {
    fetch('{{ route_with_lang("admin.assets.colors.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while syncing colors', 'error');
    });
}

function revertFromJson() {
    // Show confirmation modal
    document.getElementById('confirmRevertModal').classList.remove('hidden');
}

function confirmRevert() {
    closeConfirmRevertModal();
    
    // Get current language from URL
    const currentLang = window.location.pathname.split('/')[1] || 'en';
    const url = `/${currentLang}/admin/assets/colors/revert`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while reverting colors', 'error');
    });
}

function closeConfirmRevertModal() {
    document.getElementById('confirmRevertModal').classList.add('hidden');
}

function showToast(message, type) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastMessage.textContent = message;
    
    if (type === 'success') {
        toastIcon.innerHTML = '{!! lucide_icon("check-circle", "w-5 h-5", "#10b981") !!}';
        toast.firstElementChild.classList.add('bg-green-50', 'border-green-200');
    } else {
        toastIcon.innerHTML = '{!! lucide_icon("x-circle", "w-5 h-5", "#ef4444") !!}';
        toast.firstElementChild.classList.add('bg-red-50', 'border-red-200');
    }
    
    toast.classList.remove('translate-x-full');
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}

// Initialize - hide all check icons on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkIcons = document.querySelectorAll('[id^="check_"]');
    checkIcons.forEach(icon => {
        icon.classList.add('hidden');
    });
});
</script>
    <!-- JSON to DB Comparison Section -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                    {{ get_translation('json_to_db_comparison') }}
                </h2>
                <p class="mt-1 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary);">
                    {{ get_translation('json_to_db_comparison_description') }}
                </p>
            </div>
            <button onclick="checkJsonChanges()" class="admin-button-secondary flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                {!! lucide_icon('search', 'w-4 h-4') !!}
                <span>{{ get_translation('check_json_changes') }}</span>
            </button>
        </div>
        
        <!-- JSON Comparison Results -->
        <div id="jsonCompareSection" class="hidden">
            <div id="jsonCompareContent" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function checkJsonChanges() {
    const compareSection = document.getElementById('jsonCompareSection');
    const compareContent = document.getElementById('jsonCompareContent');
    
    compareSection.classList.remove('hidden');
    compareContent.innerHTML = '<p class="text-center py-4">{{ get_translation("loading") }}...</p>';
    
    // Get current language from URL
    const currentLang = window.location.pathname.split('/')[1] || 'en';
    const url = `/${currentLang}/admin/assets/colors/json-comparison`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.different && data.different.length > 0) {
                html += '<div class="mb-4">';
                html += '<div class="flex justify-between items-center mb-3">';
                html += '<h4 class="font-semibold" style="color: var(--warning-color);">{{ get_translation("different_colors") }} (' + data.different.length + ')</h4>';
                html += '<div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                html += '<button onclick="revertFromJson()" class="admin-button-primary text-sm px-4 py-2">{{ get_translation("sync_from_json") }}</button>';
                html += '</div>';
                html += '</div>';
                html += '<div class="space-y-2 max-h-64 overflow-y-auto">';
                data.different.forEach(color => {
                    html += '<div class="p-3 border border-orange-300 rounded bg-orange-50 dark:bg-orange-900/20">';
                    html += '<div class="flex items-center justify-between">';
                    html += '<div class="flex-1">';
                    html += '<p class="text-sm font-medium">' + color.name + '</p>';
                    html += '<p class="text-xs text-gray-600 dark:text-gray-400">' + color.category + ' - ' + color.theme + '</p>';
                    html += '<div class="mt-2 flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                    html += '<div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                    html += '<span class="text-xs" style="color: var(--text-secondary);">{{ get_translation("json_value") ?? "JSON" }}:</span>';
                    html += '<div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background: ' + color.json_value + ';"></div>';
                    html += '<span class="text-xs font-mono">' + color.json_value + '</span>';
                    html += '</div>';
                    html += '<div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                    html += '<span class="text-xs" style="color: var(--text-secondary);">{{ get_translation("database_value") ?? "DB" }}:</span>';
                    html += '<div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background: ' + color.db_value + ';"></div>';
                    html += '<span class="text-xs font-mono">' + color.db_value + '</span>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
                html += '</div>';
            }
            
            if (data.missing && data.missing.length > 0) {
                html += '<div class="mb-4">';
                html += '<h4 class="font-semibold mb-3" style="color: var(--error-color);">{{ get_translation("missing_in_db") }} (' + data.missing.length + ')</h4>';
                html += '<div class="space-y-2 max-h-64 overflow-y-auto">';
                data.missing.forEach(color => {
                    html += '<div class="p-3 border border-red-300 rounded bg-red-50 dark:bg-red-900/20">';
                    html += '<div class="flex items-center justify-between">';
                    html += '<div class="flex-1">';
                    html += '<p class="text-sm font-medium">' + color.name + '</p>';
                    html += '<p class="text-xs text-gray-600 dark:text-gray-400">' + color.category + ' - ' + color.theme + '</p>';
                    html += '<div class="mt-2 flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? "space-x-reverse" : "" }}">';
                    html += '<span class="text-xs" style="color: var(--text-secondary);">{{ get_translation("json_value") ?? "JSON" }}:</span>';
                    html += '<div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background: ' + color.json_value + ';"></div>';
                    html += '<span class="text-xs font-mono">' + color.json_value + '</span>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
                html += '</div>';
            }
            
            if (data.same && data.same.length > 0 && (!data.different || data.different.length === 0) && (!data.missing || data.missing.length === 0)) {
                html += '<div>';
                html += '<h4 class="font-semibold mb-2" style="color: var(--success-color);">{{ get_translation("identical_colors") }} (' + data.same.length + ')</h4>';
                html += '<p class="text-sm text-gray-600 dark:text-gray-400">{{ get_translation("no_changes_needed") }}</p>';
                html += '</div>';
            }
            
            if ((!data.different || data.different.length === 0) && (!data.missing || data.missing.length === 0) && (!data.same || data.same.length === 0)) {
                html = '<p class="text-center py-4 text-gray-600 dark:text-gray-400">{{ get_translation("no_colors_found") ?? "No colors found" }}</p>';
            }
            
            compareContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            compareContent.innerHTML = '<p class="text-center py-4 text-red-600">{{ get_translation("error_loading_comparison") ?? "Error loading comparison" }}</p>';
        });
}

function closeJsonCompareSection() {
    document.getElementById('jsonCompareSection').classList.add('hidden');
}
</script>

@endsection
