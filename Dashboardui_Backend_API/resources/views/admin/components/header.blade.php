<!-- Header -->
<header class="admin-header px-4 sm:px-6 py-4 flex items-center justify-between border-b {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }} {{ is_rtl_language(app()->getLocale()) ? 'lg:mr-64' : 'lg:ml-64' }}" style="border-color: var(--border-color);">
    <!-- Left Side -->
    <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
        <!-- Mobile Menu Button -->
        <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" style="color: var(--text-secondary-color);">
            {!! lucide_icon('menu', 'w-6 h-6', 'currentColor') !!}
        </button>
        
        <!-- Breadcrumb -->
        <nav class="hidden md:flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
            <a href="{{ route_with_lang('admin.dashboard') }}" class="text-sm font-medium hover:underline {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                {{ get_translation('admin_panel') }}
            </a>
            @if(isset($breadcrumbs))
                @foreach($breadcrumbs as $breadcrumb)
                    {!! lucide_icon('chevron-right', 'w-4 h-4', 'var(--text-secondary-color)') !!}
                    <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ $breadcrumb }}</span>
                @endforeach
            @else
                {!! lucide_icon('chevron-right', 'w-4 h-4', 'var(--text-secondary-color)') !!}
                <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">@yield('title', get_translation('dashboard'))</span>
            @endif
        </nav>
    </div>
    
    <!-- Right Side -->
    <div class="flex items-center space-x-4 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
        <!-- Notifications -->
        <div class="relative">
            <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative" style="color: var(--text-secondary-color);">
                {!! lucide_icon('bell', 'w-6 h-6', 'currentColor') !!}
                <!-- Notification Badge -->
                <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
            </button>
        </div>
        
        <!-- Language & Theme Settings -->
        <div class="relative">
            <button id="adminSettingsBtn" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" style="color: var(--text-secondary-color);">
                {!! lucide_icon('settings', 'w-6 h-6', 'currentColor') !!}
            </button>
        </div>
        
        <!-- User Profile Dropdown -->
        <div class="relative">
            <button id="userMenuBtn" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: var(--primary-color);">
                    <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="hidden md:block {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                    <p class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ Auth::user()->name }}</p>
                    <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
                {!! lucide_icon('chevron-down', 'w-4 h-4', 'var(--text-secondary-color)') !!}
            </button>
            
            <!-- User Dropdown Menu -->
            <div id="userDropdown" class="hidden absolute {{ is_rtl_language(app()->getLocale()) ? 'left-0' : 'right-0' }} mt-2 w-48 admin-card rounded-lg shadow-lg z-50">
                <div class="py-1">
                    <a href="#" class="flex items-center px-4 py-2 text-sm transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                        {!! lucide_icon('user', 'w-4 h-4', 'currentColor') !!}
                        <span class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('profile') }}</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                        {!! lucide_icon('settings', 'w-4 h-4', 'currentColor') !!}
                        <span class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('settings') }}</span>
                    </a>
                    <hr style="border-color: var(--border-color);">
                    <form method="POST" action="{{ route_with_lang('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-left transition-colors duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                            {!! lucide_icon('logout', 'w-4 h-4', 'currentColor') !!}
                            <span class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

