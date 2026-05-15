<!-- Sidebar -->
<aside id="adminSidebar" class="admin-sidebar w-64 min-h-screen flex flex-col {{ is_rtl_language(app()->getLocale()) ? 'fixed right-0 top-0' : 'fixed left-0 top-0' }} transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: var(--border-color);">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
			<img src="{{ asset_logo() }}" alt="{{ get_logo_alt() }}" class="h-8 object-contain">
            <div>
                <h2 class="text-lg font-bold" style="color: var(--text-color);">{{ get_company_name() }}</h2>
                <p class="text-xs" style="color: var(--text-secondary-color);">{{ get_translation('admin_panel') }}</p>
            </div>
        </div>
        
        <!-- Mobile Close Button -->
        <button id="sidebarCloseBtn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" style="color: var(--text-secondary-color);">
            {!! lucide_icon('x', 'w-5 h-5', 'currentColor') !!}
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route_with_lang('admin.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('dashboard', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('dashboard') }}</span>
        </a>

        <a href="{{ route_with_lang('admin.users.index') }}"
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('users', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('clinic_users') }}</span>
        </a>

        <!-- Website — landing & support pages -->
        <div class="mb-2">
            <button id="websiteDropdownToggle" 
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }} {{ request()->routeIs('admin.website.*') ? 'admin-active' : '' }}">
                <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                    {!! lucide_icon('globe', 'w-5 h-5', 'currentColor') !!}
                    <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website') }}</span>
                </div>
                <svg id="websiteDropdownIcon" class="w-4 h-4 transition-transform duration-200 {{ is_rtl_language(app()->getLocale()) ? 'mr-auto' : 'ml-auto' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="websiteDropdownMenu" class="hidden overflow-hidden transition-all duration-300 ease-in-out">
                <div class="pl-4 {{ is_rtl_language(app()->getLocale()) ? 'pr-4 pl-0' : '' }} space-y-1 mt-1">
                    <a href="{{ route_with_lang('admin.website.landing') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.website.landing') || request()->routeIs('admin.website.landing.*') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('home', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website_home_page') }}</span>
                    </a>
                    <a href="{{ route_with_lang('admin.website.about') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.website.about') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('users', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website_about_us') }}</span>
                    </a>
                    <a href="{{ route_with_lang('admin.website.blog') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.website.blog') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('file-text', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website_blog') }}</span>
                    </a>
                    <a href="{{ route_with_lang('admin.website.contacts') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.website.contacts') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('mail', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website_contacts') }}</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Landing homepage (CMS) — same editor as Website → Home page -->
        <a href="{{ route_with_lang('admin.website.landing') }}"
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.website.landing') || request()->routeIs('admin.website.landing.*') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('file-text', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('website_home_page') }}</span>
        </a>
        
        <!-- Assets Group - Collapsible Dropdown -->
        <div class="mb-2">
            <!-- Assets Toggle Button -->
            <button id="assetsDropdownToggle" 
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }} {{ request()->routeIs('admin.assets.*') ? 'admin-active' : '' }}">
                <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                    {!! lucide_icon('folder', 'w-5 h-5', 'currentColor') !!}
                    <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('assets') }}</span>
                </div>
                <svg id="assetsDropdownIcon" class="w-4 h-4 transition-transform duration-200 {{ is_rtl_language(app()->getLocale()) ? 'mr-auto' : 'ml-auto' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Assets Submenu (Collapsible) -->
            <div id="assetsDropdownMenu" class="hidden overflow-hidden transition-all duration-300 ease-in-out">
                <div class="pl-4 {{ is_rtl_language(app()->getLocale()) ? 'pr-4 pl-0' : '' }} space-y-1 mt-1">
                    <!-- Brand -->
                    <a href="{{ route_with_lang('admin.assets.brand') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.brand') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('award', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('brand') }}</span>
                    </a>
                    
                    <!-- Colors -->
                    <a href="{{ route_with_lang('admin.assets.colors') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.colors') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('palette', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('colors') }}</span>
                    </a>
                    
                    <!-- Themes -->
                    <a href="{{ route_with_lang('admin.assets.themes') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.themes') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('monitor', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('themes') }}</span>
                    </a>
                    
                    <!-- Languages -->
                    <a href="{{ route_with_lang('admin.assets.languages') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.languages') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('languages', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('languages') }}</span>
                    </a>
                    
                    <!-- Company -->
                    <a href="{{ route_with_lang('admin.assets.company') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.company') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('building', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('company') }}</span>
                    </a>
                    
                    <!-- Old Brand -->
                    <a href="{{ route_with_lang('admin.assets.old-brand') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.assets.old-brand') ? 'admin-active' : 'admin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('archive', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('old_brand') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="p-4 border-t" style="border-color: var(--border-color);">
        <!-- User Info -->
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }} mb-4">
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: var(--primary-color);">
                <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ Auth::user()->name }}</p>
                <p class="text-xs truncate {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
        
        <!-- Logout Button -->
        <form method="POST" action="{{ route_with_lang('admin.logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 admin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                {!! lucide_icon('logout', 'w-4 h-4', 'currentColor') !!}
                <span class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('logout') }}</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

