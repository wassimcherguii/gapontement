@extends('layouts.superadmin')

@section('title', get_translation('users_management') ?? 'Users Management')
@section('description', get_translation('users_management_description') ?? 'Manage all system users')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="direction: {{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }};">
            <h1 class="text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('users_management') ?? 'Users Management' }}
            </h1>
            <p class="mt-2 text-lg {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('users_management_description') ?? 'Manage all system users, roles, and permissions' }}
            </p>
        </div>
        <div>
            <button type="button" 
                    id="addUserBtn" 
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white transition-all duration-200 hover:scale-105 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-2' : 'space-x-2' }}"
                    style="background: var(--primary-color);"
                    onmouseover="this.style.background='var(--primary-hover)';"
                    onmouseout="this.style.background='var(--primary-color)';">
                {!! lucide_icon('user-plus', 'w-5 h-5', 'currentColor') !!}
                <span>{{ get_translation('add_user') ?? 'Add User' }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div id="successNotification" class="mb-6 p-4 rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 transition-all duration-300" style="border-color: var(--success-color)40; background: var(--success-color)10;">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('check-circle', 'w-5 h-5', 'var(--success-color)') !!}
            <p class="text-sm font-medium flex-1" style="color: var(--success-color);">{{ session('success') }}</p>
        </div>
        <button type="button" 
                onclick="closeNotification('successNotification')"
                class="{{ is_rtl_language(app()->getLocale()) ? 'mr-3' : 'ml-3' }} flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-200 hover:scale-110"
                style="color: var(--success-color); background: var(--success-color)20;"
                onmouseover="this.style.background='var(--success-color)30';"
                onmouseout="this.style.background='var(--success-color)20';"
                aria-label="{{ get_translation('close') ?? 'Close' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div id="errorNotification" class="mb-6 p-4 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 transition-all duration-300" style="border-color: var(--error-color)40; background: var(--error-color)10;">
    <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('alert-circle', 'w-5 h-5', 'var(--error-color)') !!}
            <p class="text-sm font-medium flex-1" style="color: var(--error-color);">{{ session('error') }}</p>
        </div>
        <button type="button" 
                onclick="closeNotification('errorNotification')"
                class="{{ is_rtl_language(app()->getLocale()) ? 'mr-3' : 'ml-3' }} flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-200 hover:scale-110"
                style="color: var(--error-color); background: var(--error-color)20;"
                onmouseover="this.style.background='var(--error-color)30';"
                onmouseout="this.style.background='var(--error-color)20';"
                aria-label="{{ get_translation('close') ?? 'Close' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
@endif


<!-- Search Card -->
<div class="superadmin-card p-6 mb-6">
    <form method="GET" action="{{ route_with_lang('superadmin.users.index') }}" 
          class="flex items-center gap-4 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <!-- Search -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 {{ is_rtl_language(app()->getLocale()) ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                <svg class="h-5 w-5" style="color: var(--text-secondary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" 
                   name="search" 
                   value="{{ $search }}"
                   placeholder="{{ get_translation('search') ?? 'Search users...' }}"
                   class="w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left pl-10 pr-4' }} py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                   style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                   onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20';"
                   onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';"
                   onkeyup="this.form.submit()">
        </div>
        
        @if($search)
        <a href="{{ route_with_lang('superadmin.users.index', ['tab' => $activeTab]) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors duration-200 superadmin-hover"
           style="border-color: var(--border-color); color: var(--text-color);">
            {{ get_translation('reset') ?? 'Reset' }}
        </a>
        @endif
    </form>
</div>

<!-- Users Table with Tabs -->
<div class="superadmin-card p-0 overflow-hidden">
    <!-- Role Tabs -->
    <div class="border-b {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}" style="border-color: var(--border-color);">
        <div class="flex overflow-x-auto {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}" style="scrollbar-width: thin;">
            <!-- All Users Tab -->
            <a href="{{ route_with_lang('superadmin.users.index', array_merge(request()->only('search'), ['tab' => 'all', 'page' => 1])) }}" 
               data-ajax-tab="all"
               class="ajax-tab-link flex items-center px-6 py-4 text-sm font-medium whitespace-nowrap transition-all duration-200 border-b-2 {{ $activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent' }} {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} superadmin-hover"
               style="color: {{ $activeTab === 'all' ? 'var(--primary-color)' : 'var(--text-secondary-color)' }}; border-bottom-color: {{ $activeTab === 'all' ? 'var(--primary-color)' : 'transparent' }};">
                {{ get_translation('all_users') ?? 'All Users' }}
                <span class="{{ is_rtl_language(app()->getLocale()) ? 'mr-2' : 'ml-2' }} px-2 py-0.5 rounded-full text-xs font-medium" 
                      style="background: {{ $activeTab === 'all' ? 'var(--primary-color)20' : 'var(--background-color)' }}; color: {{ $activeTab === 'all' ? 'var(--primary-color)' : 'var(--text-secondary-color)' }};">
                    <span class="tab-count-all">{{ $roleCounts['all'] ?? 0 }}</span>
                </span>
            </a>
            
            <!-- Role Tabs -->
            @foreach($availableRoles as $role)
            <a href="{{ route_with_lang('superadmin.users.index', array_merge(request()->only('search'), ['tab' => $role, 'page' => 1])) }}" 
               data-ajax-tab="{{ $role }}"
               class="ajax-tab-link flex items-center px-6 py-4 text-sm font-medium whitespace-nowrap transition-all duration-200 border-b-2 {{ $activeTab === $role ? 'border-primary text-primary' : 'border-transparent' }} {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} superadmin-hover"
               style="color: {{ $activeTab === $role ? 'var(--primary-color)' : 'var(--text-secondary-color)' }}; border-bottom-color: {{ $activeTab === $role ? 'var(--primary-color)' : 'transparent' }};">
                {{ ucfirst($role) }}
                <span class="{{ is_rtl_language(app()->getLocale()) ? 'mr-2' : 'ml-2' }} px-2 py-0.5 rounded-full text-xs font-medium" 
                      style="background: {{ $activeTab === $role ? 'var(--primary-color)20' : 'var(--background-color)' }}; color: {{ $activeTab === $role ? 'var(--primary-color)' : 'var(--text-secondary-color)' }};">
                    <span class="tab-count-{{ $role }}">{{ $roleCounts[$role] ?? 0 }}</span>
                </span>
            </a>
            @endforeach
        </div>
    </div>
    
    <!-- Tab Content -->
    <div class="p-6">
        <div class="flex items-center justify-between mb-6 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <h3 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ $activeTab === 'all' ? (get_translation('all_users') ?? 'All Users') : ucfirst($activeTab) . ' ' . (get_translation('users') ?? 'Users') }}
                <span class="text-sm font-normal" style="color: var(--text-secondary-color);">
                    (<span id="total-users-count">{{ $users->total() }}</span> {{ get_translation('total') ?? 'total' }})
                </span>
            </h3>
        </div>
    
        <!-- Table Container (Will be updated via AJAX) -->
        <div id="users-table-container">
            @include('superadmin.users.partials.table', ['users' => $users])
        </div>
        
        <!-- Pagination Container (Will be updated via AJAX) -->
        <div id="users-pagination-container">
            @include('superadmin.users.partials.pagination', ['users' => $users, 'activeTab' => $activeTab, 'search' => $search])
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" 
     style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    
    <!-- Modal Content -->
    <div id="addUserModalContent" 
         class="relative w-full max-w-lg superadmin-card rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-y-auto"
         style="background: var(--surface-color); border: 1px solid var(--border-color);">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b sticky top-0 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}" style="border-color: var(--border-color); background: var(--surface-color);">
            <h3 class="text-xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                {{ get_translation('add_user') ?? 'Add User' }}
            </h3>
            <button id="closeAddUserBtn" 
                    type="button"
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-200 hover:scale-110"
                    style="color: var(--text-secondary-color); background: var(--background-color);"
                    onmouseover="this.style.background='var(--primary-color)20'; this.style.color='var(--primary-color)';"
                    onmouseout="this.style.background='var(--background-color)'; this.style.color='var(--text-secondary-color)';">
                {!! lucide_icon('x', 'w-6 h-6', 'currentColor') !!}
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="addUserForm" method="POST" action="{{ route_with_lang('superadmin.users.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="{{ $activeTab }}" id="modal_tab_param">
                
                <!-- Error Messages -->
                <div id="addUserErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                                {{ get_translation('validation_errors') ?? 'Validation Errors' }}
                            </h3>
                            <div id="addUserErrorsList" class="mt-2 text-sm text-red-700 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Name Field -->
                <div>
                    <label for="modal_user_name" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('name') ?? 'Name' }} <span class="text-red-500">*</span>
                    </label>
                    <input id="modal_user_name" 
                           name="name" 
                           type="text" 
                           required 
                           value="{{ old('name') }}"
                           class="form-field w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} py-3 px-4 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                           style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                           placeholder="{{ get_translation('enter_name') ?? 'Enter user name' }}"
                           data-validation="required|min:2|max:255"
                           data-error-message="{{ get_translation('name_required') ?? 'Name is required (min 2 characters)' }}"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20'; clearFieldError(this);"
                           onblur="validateField(this); this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                    <span class="field-error hidden text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--error-color);"></span>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="modal_user_email" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('email') ?? 'Email' }} <span class="text-red-500">*</span>
                    </label>
                    <input id="modal_user_email" 
                           name="email" 
                           type="email" 
                           required 
                           value="{{ old('email') }}"
                           class="form-field w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} py-3 px-4 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                           style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                           placeholder="{{ get_translation('enter_email') ?? 'Enter user email' }}"
                           data-validation="required|email"
                           data-error-message="{{ get_translation('email_invalid') ?? 'Please enter a valid email address' }}"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20'; clearFieldError(this);"
                           onblur="validateEmailField(this); this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                    <span class="field-error hidden text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--error-color);"></span>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="modal_user_password" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('password') ?? 'Password' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="modal_user_password" 
                               name="password" 
                               type="password" 
                               required 
                               minlength="6"
                               class="form-field w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left pl-10 pr-4' }} py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                               style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                               placeholder="{{ get_translation('enter_password') ?? 'Enter password (min 6 characters)' }}"
                               data-validation="required|min:6"
                               data-error-message="{{ get_translation('password_min_length') ?? 'Password must be at least 6 characters' }}"
                               onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20'; clearFieldError(this);"
                               onblur="validatePasswordField(this); this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                        <!-- Toggle Password Button -->
                        <button type="button" 
                                id="togglePasswordBtn" 
                                class="absolute inset-y-0 flex items-center transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'left-0 pl-3' : 'right-0 pr-3' }}"
                                style="color: var(--text-secondary-color);"
                                onmouseover="this.style.color='var(--primary-color)';"
                                onmouseout="this.style.color='var(--text-secondary-color)';">
                            <svg id="togglePasswordIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <span class="field-error hidden text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--error-color);"></span>
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label for="modal_user_password_confirmation" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('confirm_password') ?? 'Confirm Password' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="modal_user_password_confirmation" 
                               name="password_confirmation" 
                               type="password" 
                               required 
                               minlength="6"
                               class="form-field w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left pl-10 pr-4' }} py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                               style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                               placeholder="{{ get_translation('confirm_password') ?? 'Confirm password' }}"
                               data-validation="required|min:6"
                               data-error-message="{{ get_translation('password_confirmation_mismatch') ?? 'Passwords do not match' }}"
                               onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20'; clearFieldError(this);"
                               onblur="validatePasswordConfirmationField(this); this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                        <!-- Toggle Password Confirmation Button -->
                        <button type="button" 
                                id="togglePasswordConfirmationBtn" 
                                class="absolute inset-y-0 flex items-center transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'left-0 pl-3' : 'right-0 pr-3' }}"
                                style="color: var(--text-secondary-color);"
                                onmouseover="this.style.color='var(--primary-color)';"
                                onmouseout="this.style.color='var(--text-secondary-color)';">
                            <svg id="togglePasswordConfirmationIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <span class="field-error hidden text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--error-color);"></span>
                </div>

                <!-- Role Field -->
                <div>
                    <label for="modal_user_role" class="block text-sm font-medium mb-2 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
                        {{ get_translation('role') ?? 'Role' }} <span class="text-red-500">*</span>
                    </label>
                    <select id="modal_user_role" 
                            name="role" 
                            required
                            class="form-field w-full {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }} py-3 px-4 rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 sm:text-sm"
                            style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                            data-validation="required"
                            data-error-message="{{ get_translation('role_required') ?? 'Please select a role' }}"
                            onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20'; clearFieldError(this);"
                            onchange="validateField(this);"
                            onblur="validateField(this); this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                            <option value="">{{ get_translation('select_role') ?? 'Select Role' }}</option>
                        @foreach($availableRoles as $role)
                            <option value="{{ $role }}" {{ ($activeTab === $role && $activeTab !== 'all') ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <span class="field-error hidden text-xs mt-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--error-color);"></span>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse' : '' }}" style="border-color: var(--border-color);">
                    <button type="button" 
                            id="cancelAddUserBtn"
                            class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors duration-200 superadmin-hover"
                            style="border-color: var(--border-color); color: var(--text-color);">
                        {{ get_translation('cancel') ?? 'Cancel' }}
                    </button>
                    <button type="submit" 
                            id="submitAddUserBtn"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-all duration-200 hover:scale-105"
                            style="background: var(--primary-color);"
                            onmouseover="this.style.background='var(--primary-hover)';"
                            onmouseout="this.style.background='var(--primary-color)';">
                        {{ get_translation('create_user') ?? 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Function to close notifications
function closeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        notification.style.transition = 'all 0.3s ease-out';
        notification.style.maxHeight = notification.offsetHeight + 'px';
        
        setTimeout(() => {
            notification.style.maxHeight = '0';
            notification.style.marginBottom = '0';
            notification.style.padding = '0';
            notification.style.overflow = 'hidden';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide notifications after delay (optional)
    const successNotification = document.getElementById('successNotification');
    const errorNotification = document.getElementById('errorNotification');
    
    if (successNotification) {
        setTimeout(() => {
            if (successNotification && successNotification.style.display !== 'none') {
                closeNotification('successNotification');
            }
        }, 5000); // Auto-hide success after 5 seconds
    }
    
    if (errorNotification) {
        setTimeout(() => {
            if (errorNotification && errorNotification.style.display !== 'none') {
                closeNotification('errorNotification');
            }
        }, 7000); // Auto-hide error after 7 seconds (longer for errors)
    }
    
    // AJAX Pagination Implementation
    const containerId = 'users-table-container';
    const paginationContainerId = 'users-pagination-container';
    const loadingId = 'pagination-loading';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Show loading function
    function showLoading() {
        let loading = document.getElementById(loadingId);
        if (!loading) {
            loading = document.createElement('div');
            loading.id = loadingId;
            loading.className = 'fixed inset-0 z-50 flex items-center justify-center';
            loading.style.cssText = 'background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px);';
            loading.innerHTML = `
                <div class="superadmin-card p-6 rounded-xl" style="background: var(--surface-color); border: 1px solid var(--border-color);">
                    <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: var(--primary-color); border-top-color: transparent;"></div>
                        <span style="color: var(--text-color);">{{ get_translation('loading') ?? 'Loading...' }}</span>
                    </div>
                </div>
            `;
            document.body.appendChild(loading);
        }
        loading.style.display = 'flex';
    }
    
    // Hide loading function
    function hideLoading() {
        const loading = document.getElementById(loadingId);
        if (loading) {
            loading.style.display = 'none';
        }
    }
    
    // Load page via AJAX
    async function loadPageAjax(url, updateUrl = true) {
        try {
            showLoading();
            
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            const response = await fetch(url, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin',
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.html) {
                // Update table with fade animation
                const container = document.getElementById(containerId);
                if (container) {
                    container.style.opacity = '0';
                    container.style.transition = 'opacity 0.2s';
                    setTimeout(() => {
                        container.innerHTML = data.html;
                        container.style.opacity = '1';
                    }, 200);
                }
                
                // Update pagination
                if (data.pagination) {
                    const paginationContainer = document.getElementById(paginationContainerId);
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
                
                // Update total count
                const totalElement = document.getElementById('total-users-count');
                if (totalElement && data.total !== undefined) {
                    totalElement.textContent = data.total;
                }
                
                // Update browser URL without reload
                if (updateUrl) {
                    const urlObj = new URL(url, window.location.origin);
                    window.history.pushState(
                        { 
                            page: urlObj.searchParams.get('page') || 1, 
                            tab: urlObj.searchParams.get('tab') || 'all',
                            search: urlObj.searchParams.get('search') || ''
                        },
                        '',
                        url
                    );
                }
                
                // Update active tab highlighting
                updateActiveTab();
                
                // Scroll to top of table smoothly
                const tableContainer = document.getElementById(containerId);
                if (tableContainer) {
                    setTimeout(() => {
                        tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
        } catch (error) {
            console.error('AJAX Pagination Error:', error);
            hideLoading();
            alert('{{ get_translation("error_occurred") ?? "An error occurred while loading the page. Please refresh." }}');
        } finally {
            setTimeout(() => {
                hideLoading();
            }, 300);
        }
    }
    
    // Update active tab highlighting
    function updateActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'all';
        
        document.querySelectorAll('.ajax-tab-link').forEach(link => {
            const linkTab = link.getAttribute('data-ajax-tab');
            if (linkTab === activeTab) {
                link.style.color = 'var(--primary-color)';
                link.style.borderBottomColor = 'var(--primary-color)';
            } else {
                link.style.color = 'var(--text-secondary-color)';
                link.style.borderBottomColor = 'transparent';
            }
        });
    }
    
    // Handle pagination link clicks
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.ajax-pagination-link');
        if (paginationLink) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');
            if (url) {
                loadPageAjax(url);
            }
        }
        
        // Handle tab link clicks with AJAX
        const tabLink = e.target.closest('.ajax-tab-link');
        if (tabLink) {
            e.preventDefault();
            const url = tabLink.getAttribute('href');
            if (url) {
                loadPageAjax(url);
            }
        }
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        if (event.state) {
            const url = new URL(window.location.href);
            // Ensure page parameter is set
            if (!url.searchParams.has('page')) {
                url.searchParams.set('page', event.state.page || '1');
            }
            loadPageAjax(url.toString(), false);
        } else {
            // Fallback: reload current page
            window.location.reload();
        }
    });
    
    // Store reference globally for use in other functions
    window.usersAjaxPagination = {
        loadPage: loadPageAjax,
        showLoading: showLoading,
        hideLoading: hideLoading
    };
    
    // Password Toggle Functionality
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const togglePasswordConfirmationBtn = document.getElementById('togglePasswordConfirmationBtn');
    const modalUserPassword = document.getElementById('modal_user_password');
    const modalUserPasswordConfirmation = document.getElementById('modal_user_password_confirmation');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const togglePasswordConfirmationIcon = document.getElementById('togglePasswordConfirmationIcon');
    
    // Toggle password visibility
    if (togglePasswordBtn && modalUserPassword && togglePasswordIcon) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = modalUserPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            modalUserPassword.setAttribute('type', type);
            
            // Toggle eye icon (eye open = show, eye slashed = hide)
            if (type === 'text') {
                togglePasswordIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                togglePasswordIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    }
    
    // Toggle password confirmation visibility
    if (togglePasswordConfirmationBtn && modalUserPasswordConfirmation && togglePasswordConfirmationIcon) {
        togglePasswordConfirmationBtn.addEventListener('click', function() {
            const type = modalUserPasswordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            modalUserPasswordConfirmation.setAttribute('type', type);
            
            // Toggle eye icon (eye open = show, eye slashed = hide)
            if (type === 'text') {
                togglePasswordConfirmationIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                togglePasswordConfirmationIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    }
    
    // Modal functionality
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const addUserModalContent = document.getElementById('addUserModalContent');
    const closeAddUserBtn = document.getElementById('closeAddUserBtn');
    const cancelAddUserBtn = document.getElementById('cancelAddUserBtn');
    const addUserForm = document.getElementById('addUserForm');
    const addUserErrors = document.getElementById('addUserErrors');
    const addUserErrorsList = document.getElementById('addUserErrorsList');
    const modalUserRole = document.getElementById('modal_user_role');
    
    // Function to open modal
    function openAddUserModal() {
        // Get current active tab from URL
        const urlParams = new URLSearchParams(window.location.search);
        const currentActiveTab = urlParams.get('tab') || 'all';
        
        // Pre-select role based on active tab
        if (modalUserRole && currentActiveTab && currentActiveTab !== 'all') {
            modalUserRole.value = currentActiveTab;
        } else if (modalUserRole) {
            modalUserRole.value = '';
        }
        
        // Show modal
        addUserModal.classList.remove('hidden');
        addUserModal.classList.add('modal-show');
        
        // Trigger content animation
        setTimeout(() => {
            addUserModalContent.classList.remove('scale-95', 'opacity-0');
            addUserModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Focus on first input
        document.getElementById('modal_user_name').focus();
    }
    
    // Function to close modal
    function closeAddUserModal() {
        // Hide modal with CSS transitions
        addUserModalContent.classList.remove('scale-100', 'opacity-100');
        addUserModalContent.classList.add('scale-95', 'opacity-0');
        
        // Hide modal completely after animation
        setTimeout(() => {
            addUserModal.classList.remove('modal-show');
            addUserModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form
            addUserForm.reset();
            addUserErrors.classList.add('hidden');
            addUserErrorsList.innerHTML = '';
            
            // Reset role to active tab if not 'all'
            const urlParamsReset = new URLSearchParams(window.location.search);
            const currentActiveTabReset = urlParamsReset.get('tab') || 'all';
            if (modalUserRole && currentActiveTabReset && currentActiveTabReset !== 'all') {
                modalUserRole.value = currentActiveTabReset;
            } else if (modalUserRole) {
                modalUserRole.value = '';
            }
        }, 300);
    }
    
    // Event listeners
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openAddUserModal();
        });
    }
    
    if (closeAddUserBtn) {
        closeAddUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeAddUserModal();
        });
    }
    
    if (cancelAddUserBtn) {
        cancelAddUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeAddUserModal();
        });
    }
    
    // Close modal when clicking outside
    if (addUserModal) {
        addUserModal.addEventListener('click', function(e) {
            if (e.target === addUserModal) {
                closeAddUserModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && addUserModal && addUserModal.classList.contains('modal-show')) {
            closeAddUserModal();
        }
    });
    
    // Frontend Validation Functions
    function clearFieldError(field) {
        const errorSpan = field.closest('div').querySelector('.field-error');
        if (errorSpan) {
            errorSpan.classList.add('hidden');
            errorSpan.textContent = '';
        }
        field.style.borderColor = 'var(--border-color)';
    }
    
    function showFieldError(field, message) {
        const errorSpan = field.closest('div').querySelector('.field-error');
        if (errorSpan) {
            errorSpan.textContent = message;
            errorSpan.classList.remove('hidden');
        }
        field.style.borderColor = 'var(--error-color)';
    }
    
    function validateField(field) {
        const validation = field.getAttribute('data-validation');
        const value = field.value.trim();
        const errorMessage = field.getAttribute('data-error-message');
        
        if (!validation) return true;
        
        const rules = validation.split('|');
        let isValid = true;
        let message = errorMessage || 'This field is invalid';
        
        for (const rule of rules) {
            if (rule === 'required' && !value) {
                isValid = false;
                message = errorMessage || 'This field is required';
                break;
            } else if (rule.startsWith('min:')) {
                const minLength = parseInt(rule.split(':')[1]);
                if (value.length < minLength) {
                    isValid = false;
                    message = errorMessage || `Must be at least ${minLength} characters`;
                    break;
                }
            } else if (rule.startsWith('max:')) {
                const maxLength = parseInt(rule.split(':')[1]);
                if (value.length > maxLength) {
                    isValid = false;
                    message = errorMessage || `Must be at most ${maxLength} characters`;
                    break;
                }
            } else if (rule === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (value && !emailRegex.test(value)) {
                    isValid = false;
                    message = errorMessage || 'Please enter a valid email address';
                    break;
                }
            }
        }
        
        if (!isValid) {
            showFieldError(field, message);
        } else {
            clearFieldError(field);
        }
        
        return isValid;
    }
    
    function validateEmailField(field) {
        const isValid = validateField(field);
        return isValid;
    }
    
    function validatePasswordField(field) {
        const isValid = validateField(field);
        // Also check password confirmation if password is valid
        if (isValid) {
            const confirmField = document.getElementById('modal_user_password_confirmation');
            if (confirmField && confirmField.value) {
                validatePasswordConfirmationField(confirmField);
            }
        }
        return isValid;
    }
    
    function validatePasswordConfirmationField(field) {
        const password = document.getElementById('modal_user_password').value;
        const confirmation = field.value;
        const errorMessage = field.getAttribute('data-error-message');
        
        if (!confirmation) {
            showFieldError(field, errorMessage || 'Please confirm your password');
            return false;
        }
        
        if (password !== confirmation) {
            showFieldError(field, '{{ get_translation("password_confirmation_mismatch") ?? "Passwords do not match" }}');
            return false;
        }
        
        clearFieldError(field);
        return true;
    }
    
    function validateAllFields() {
        const fields = addUserForm.querySelectorAll('.form-field[required]');
        let isValid = true;
        
        fields.forEach(field => {
            let fieldValid = false;
            
            if (field.id === 'modal_user_name') {
                fieldValid = validateField(field);
            } else if (field.id === 'modal_user_email') {
                fieldValid = validateEmailField(field);
            } else if (field.id === 'modal_user_password') {
                fieldValid = validatePasswordField(field);
            } else if (field.id === 'modal_user_password_confirmation') {
                fieldValid = validatePasswordConfirmationField(field);
            } else if (field.id === 'modal_user_role') {
                fieldValid = validateField(field);
            }
            
            if (!fieldValid) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Handle AJAX form submission
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Hide previous errors
            addUserErrors.classList.add('hidden');
            addUserErrorsList.innerHTML = '';
            
            // Clear all field errors
            addUserForm.querySelectorAll('.field-error').forEach(span => {
                span.classList.add('hidden');
                span.textContent = '';
            });
            
            // Frontend validation
            if (!validateAllFields()) {
                addUserErrors.classList.remove('hidden');
                addUserErrorsList.innerHTML = '<p>{{ get_translation("please_fix_errors") ?? "Please fix the errors below" }}</p>';
                return;
            }
            
            // Update hidden tab input with current active tab
            const modalTabParam = document.getElementById('modal_tab_param');
            if (modalTabParam) {
                const urlParams = new URLSearchParams(window.location.search);
                const currentTab = urlParams.get('tab') || 'all';
                modalTabParam.value = currentTab;
            }
            
            // Disable submit button
            const submitBtn = document.getElementById('submitAddUserBtn');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '{{ get_translation("creating") ?? "Creating..." }}';
            }
            
            // Get form data
            const formData = new FormData(addUserForm);
            
            try {
                // Send AJAX request
                const response = await fetch(addUserForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Success - update table and pagination
                    if (data.html) {
                        const tableContainer = document.getElementById('users-table-container');
                        if (tableContainer) {
                            tableContainer.style.opacity = '0';
                            setTimeout(() => {
                                tableContainer.innerHTML = data.html;
                                tableContainer.style.opacity = '1';
                            }, 200);
                        }
                    }
                    
                    if (data.pagination) {
                        const paginationContainer = document.getElementById('users-pagination-container');
                        if (paginationContainer) {
                            paginationContainer.innerHTML = data.pagination;
                        }
                    }
                    
                    // Update total count
                    if (data.total !== undefined) {
                        const totalElement = document.getElementById('total-users-count');
                        if (totalElement) {
                            totalElement.textContent = data.total;
                        }
                    }
                    
                    // Update role counts in tabs if provided
                    if (data.roleCounts) {
                        Object.keys(data.roleCounts).forEach(role => {
                            const countElement = document.querySelector(`.tab-count-${role}`);
                            if (countElement) {
                                countElement.textContent = data.roleCounts[role];
                            }
                        });
                    }
                    
                    // Show success notification dynamically
                    showDynamicNotification(data.message || 'User created successfully!', 'success');
                    
                    // Close modal
                    closeAddUserModal();
                    
                    // Reset form
                    addUserForm.reset();
                    
                    // Reset role to active tab
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentTab = urlParams.get('tab') || 'all';
                    if (modalUserRole && currentTab && currentTab !== 'all') {
                        modalUserRole.value = currentTab;
                    }
                } else {
                    // Validation errors from server
                    if (data.errors) {
                        addUserErrors.classList.remove('hidden');
                        let errorsHtml = '<ul class="list-disc {{ is_rtl_language(app()->getLocale()) ? "list-right" : "list-inside" }} space-y-1">';
                        Object.keys(data.errors).forEach(field => {
                            data.errors[field].forEach(error => {
                                errorsHtml += `<li>${error}</li>`;
                                
                                // Show field-specific errors
                                let fieldId = field.replace('.', '_');
                                if (fieldId === 'password_confirmation') {
                                    fieldId = 'modal_user_password_confirmation';
                                } else {
                                    fieldId = `modal_user_${fieldId}`;
                                }
                                const fieldElement = document.getElementById(fieldId);
                                if (fieldElement) {
                                    showFieldError(fieldElement, error);
                                }
                            });
                        });
                        errorsHtml += '</ul>';
                        addUserErrorsList.innerHTML = errorsHtml;
                    } else {
                        addUserErrors.classList.remove('hidden');
                        addUserErrorsList.innerHTML = `<p>${data.message || '{{ get_translation("error_occurred") ?? "An error occurred" }}'}</p>`;
                    }
                }
            } catch (error) {
                console.error('Form submission error:', error);
                addUserErrors.classList.remove('hidden');
                addUserErrorsList.innerHTML = '<p>{{ get_translation("error_occurred") ?? "An error occurred while creating the user. Please try again." }}</p>';
            } finally {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    }
    
    // Function to show dynamic notification
    function showDynamicNotification(message, type = 'success') {
        const notificationId = type === 'success' ? 'dynamicSuccessNotification' : 'dynamicErrorNotification';
        let notification = document.getElementById(notificationId);
        const isRtl = {{ is_rtl_language(app()->getLocale()) ? 'true' : 'false' }};
        
        // Remove existing notification if any
        if (notification) {
            notification.remove();
        }
        
        notification = document.createElement('div');
        notification.id = notificationId;
        notification.className = 'mb-6 p-4 rounded-lg border transition-all duration-300';
        notification.style.cssText = type === 'success' 
            ? 'border-color: var(--success-color)40; background: var(--success-color)10;'
            : 'border-color: var(--error-color)40; background: var(--error-color)10;';
        
        const color = type === 'success' ? 'var(--success-color)' : 'var(--error-color)';
        const iconSvg = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ' + color + ';"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ' + color + ';"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
        notification.innerHTML = `
            <div class="flex items-center justify-between ${isRtl ? 'flex-row-reverse' : ''}">
                <div class="flex items-center ${isRtl ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3'}">
                    ${iconSvg}
                    <p class="text-sm font-medium flex-1" style="color: ${color};">${message}</p>
                </div>
                <button type="button" 
                        onclick="closeNotification('${notificationId}')"
                        class="${isRtl ? 'mr-3' : 'ml-3'} flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-200 hover:scale-110"
                        style="color: ${color}; background: ${color}20;"
                        onmouseover="this.style.background='${color}30';"
                        onmouseout="this.style.background='${color}20';"
                        aria-label="{{ get_translation('close') ?? 'Close' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        // Insert notification after page header (before search card)
        const headerSection = document.querySelector('.mb-8');
        if (headerSection) {
            headerSection.after(notification);
        } else {
            // Fallback: insert at the beginning of the main content area
            const mainContent = document.querySelector('main') || document.querySelector('.container') || document.body;
            if (mainContent) {
                const firstChild = mainContent.firstElementChild;
                if (firstChild) {
                    firstChild.before(notification);
                } else {
                    mainContent.prepend(notification);
                }
            }
        }
        
        notification.style.display = 'block';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);
        
        // Auto-hide after delay
        setTimeout(() => {
            if (notification && notification.style.display !== 'none') {
                closeNotification(notificationId);
            }
        }, type === 'success' ? 5000 : 7000);
    }
    
    // If page loads with validation errors, open modal
    @if($errors->any() && old('_token'))
    // Open modal automatically if there are validation errors
    setTimeout(function() {
        openAddUserModal();
        
        // Populate form with old values
        @if(old('name'))
        const nameField = document.getElementById('modal_user_name');
        if (nameField) nameField.value = '{{ old('name') }}';
        @endif
        @if(old('email'))
        const emailField = document.getElementById('modal_user_email');
        if (emailField) emailField.value = '{{ old('email') }}';
        @endif
        @if(old('role'))
        const roleField = document.getElementById('modal_user_role');
        if (roleField) roleField.value = '{{ old('role') }}';
        @endif
        
        // Show errors in modal
        if (addUserErrors) {
            addUserErrors.classList.remove('hidden');
            let errorsHtml = '<ul class="list-disc {{ is_rtl_language(app()->getLocale()) ? 'list-right' : 'list-inside' }} space-y-1">';
            @foreach($errors->all() as $error)
                errorsHtml += '<li>{{ addslashes($error) }}</li>';
            @endforeach
            errorsHtml += '</ul>';
            if (addUserErrorsList) {
                addUserErrorsList.innerHTML = errorsHtml;
            }
        }
    }, 100);
    @endif
});
</script>
@endpush
