<!-- SuperAdmin Sidebar -->
<aside id="superadminSidebar" class="superadmin-sidebar w-64 min-h-screen flex flex-col {{ is_rtl_language(app()->getLocale()) ? 'fixed right-0 top-0' : 'fixed left-0 top-0' }} transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: var(--border-color);">
        <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
            <img src="{{ asset_logo() }}" alt="{{ get_logo_alt() }}" class="h-8 object-contain">
            <div>
                <h2 class="text-lg font-bold" style="color: var(--text-color);">{{ get_company_name() }}</h2>
                <p class="text-xs" style="color: var(--text-secondary-color);">{{ get_translation('superadmin_panel') ?? 'Super Admin Panel' }}</p>
            </div>
        </div>
        
        <!-- Mobile Close Button -->
        <button id="superadminSidebarCloseBtn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" style="color: var(--text-secondary-color);">
            {!! lucide_icon('x', 'w-5 h-5', 'currentColor') !!}
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route_with_lang('superadmin.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.dashboard') ? 'superadmin-active' : 'superadmin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('dashboard', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('dashboard') }}</span>
        </a>
        
        <!-- System Management Group - Collapsible Dropdown -->
        <div class="mb-2">
            <!-- System Management Toggle Button -->
            <button id="systemManagementDropdownToggle" 
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }} {{ request()->routeIs('superadmin.system.*') ? 'superadmin-active' : '' }}">
                <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                    {!! lucide_icon('server', 'w-5 h-5', 'currentColor') !!}
                    <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('system_management') ?? 'System Management' }}</span>
                </div>
                <svg id="systemManagementDropdownIcon" class="w-4 h-4 transition-transform duration-200 {{ is_rtl_language(app()->getLocale()) ? 'mr-auto' : 'ml-auto' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- System Management Submenu (Collapsible) -->
            <div id="systemManagementDropdownMenu" class="hidden overflow-hidden transition-all duration-300 ease-in-out">
                <div class="pl-4 {{ is_rtl_language(app()->getLocale()) ? 'pr-4 pl-0' : '' }} space-y-1 mt-1">
                    <!-- Users Management -->
                    <a href="{{ route_with_lang('superadmin.users.index') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.users.*') ? 'superadmin-active' : 'superadmin-hover' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('users', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('users') }}</span>
                    </a>
                    
                    <!-- Roles & Permissions -->
                    <a href="#" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('shield', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('roles_permissions') ?? 'Roles & Permissions' }}</span>
                    </a>
                    
                    <!-- System Settings -->
                    <a href="#" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('settings', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('system_settings') ?? 'System Settings' }}</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Admin Access Group - Collapsible Dropdown -->
        <div class="mb-2">
            <!-- Admin Access Toggle Button -->
            <button id="adminAccessDropdownToggle" 
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }} {{ request()->routeIs('superadmin.admin.*') ? 'superadmin-active' : '' }}">
                <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                    {!! lucide_icon('key', 'w-5 h-5', 'currentColor') !!}
                    <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('admin_access') ?? 'Admin Access' }}</span>
                </div>
                <svg id="adminAccessDropdownIcon" class="w-4 h-4 transition-transform duration-200 {{ is_rtl_language(app()->getLocale()) ? 'mr-auto' : 'ml-auto' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Admin Access Submenu (Collapsible) -->
            <div id="adminAccessDropdownMenu" class="hidden overflow-hidden transition-all duration-300 ease-in-out">
                <div class="pl-4 {{ is_rtl_language(app()->getLocale()) ? 'pr-4 pl-0' : '' }} space-y-1 mt-1">
                    <!-- Admin Dashboard -->
                    <a href="{{ route_with_lang('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('layout-dashboard', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('admin_dashboard') }}</span>
                    </a>
                    
                    <!-- Admin Assets -->
                    <a href="{{ route_with_lang('admin.assets.brand') }}" 
                       class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                        {!! lucide_icon('folder', 'w-4 h-4', 'currentColor') !!}
                        <span class="text-sm font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('assets') }}</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Reports & Analytics -->
        <a href="#" 
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('bar-chart', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('reports_analytics') ?? 'Reports & Analytics' }}</span>
        </a>
        
        <!-- Logs & Monitoring -->
        <a href="#" 
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
            {!! lucide_icon('file-text', 'w-5 h-5', 'currentColor') !!}
            <span class="font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('logs_monitoring') ?? 'Logs & Monitoring' }}</span>
        </a>
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
        <form method="POST" action="{{ route_with_lang('superadmin.logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 superadmin-hover {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse space-x-reverse space-x-3' : 'space-x-3' }}">
                {!! lucide_icon('logout', 'w-4 h-4', 'currentColor') !!}
                <span class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('logout') }}</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="superadminSidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
