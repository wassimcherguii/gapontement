<style>
    /* CSS Variables for Admin Colors */
    :root {
        /* Light Theme Colors */
        --primary-color: {{ get_light_colors()['brand']['primary'] ?? '#94131D' }};
        --primary-hover: {{ get_light_colors()['brand']['primary-hover'] ?? '#7a0f18' }};
        --primary-dark: {{ get_light_colors()['brand']['primary-dark'] ?? '#6d0e15' }};
        --primary-light: {{ get_light_colors()['brand']['primary-light'] ?? '#b0151f' }};
        --secondary-color: {{ get_light_colors()['brand']['secondary'] ?? '#706f6c' }};
        --accent-color: {{ get_light_colors()['brand']['accent'] ?? '#1D9413' }};
        --background-color: {{ get_light_colors()['usage']['background'] ?? '#FDFDFC' }};
        --surface-color: {{ get_light_colors()['usage']['surface'] ?? '#ffffff' }};
        --text-color: {{ get_light_colors()['usage']['text'] ?? '#1b1b18' }};
        --text-secondary-color: {{ get_light_colors()['usage']['text-secondary'] ?? '#706f6c' }};
        --border-color: {{ get_light_colors()['usage']['border'] ?? '#e3e3e0' }};
        --success-color: {{ get_light_colors()['semantic']['success'] ?? '#1D9413' }};
        --error-color: {{ get_light_colors()['semantic']['error'] ?? '#94131D' }};
        --warning-color: {{ get_light_colors()['semantic']['warning'] ?? '#f59e0b' }};
        --info-color: {{ get_light_colors()['semantic']['info'] ?? '#131D94' }};
        
        /* Admin Specific Colors */
        --sidebar-bg: {{ get_light_colors()['usage']['surface'] ?? '#ffffff' }};
        --sidebar-border: {{ get_light_colors()['usage']['border'] ?? '#e3e3e0' }};
        --header-bg: {{ get_light_colors()['usage']['surface'] ?? '#ffffff' }};
        --header-border: {{ get_light_colors()['usage']['border'] ?? '#e3e3e0' }};
        --card-bg: {{ get_light_colors()['usage']['surface'] ?? '#ffffff' }};
        --card-border: {{ get_light_colors()['usage']['border'] ?? '#e3e3e0' }};
        --hover-bg: {{ get_light_colors()['neutral']['gray-50'] ?? '#f8fafc' }};
    }
    
    /* Dark Theme Colors */
    .dark {
        --primary-color: {{ get_dark_colors()['brand']['primary'] ?? '#b0151f' }};
        --primary-hover: {{ get_dark_colors()['brand']['primary-hover'] ?? '#7a0f18' }};
        --primary-dark: {{ get_dark_colors()['brand']['primary-dark'] ?? '#94131D' }};
        --primary-light: {{ get_dark_colors()['brand']['primary-light'] ?? '#d41a26' }};
        --secondary-color: {{ get_dark_colors()['brand']['secondary'] ?? '#94a3b8' }};
        --accent-color: {{ get_dark_colors()['brand']['accent'] ?? '#34d399' }};
        --background-color: {{ get_dark_colors()['usage']['background'] ?? '#0f172a' }};
        --surface-color: {{ get_dark_colors()['usage']['surface'] ?? '#1e293b' }};
        --text-color: {{ get_dark_colors()['usage']['text'] ?? '#f8fafc' }};
        --text-secondary-color: {{ get_dark_colors()['usage']['text-secondary'] ?? '#cbd5e1' }};
        --border-color: {{ get_dark_colors()['usage']['border'] ?? '#334155' }};
        --success-color: {{ get_dark_colors()['semantic']['success'] ?? '#34d399' }};
        --error-color: {{ get_dark_colors()['semantic']['error'] ?? '#f87171' }};
        --warning-color: {{ get_dark_colors()['semantic']['warning'] ?? '#fbbf24' }};
        --info-color: {{ get_dark_colors()['semantic']['info'] ?? '#60a5fa' }};
        
        /* Admin Specific Colors - Dark */
        --sidebar-bg: {{ get_dark_colors()['usage']['surface'] ?? '#1e293b' }};
        --sidebar-border: {{ get_dark_colors()['usage']['border'] ?? '#334155' }};
        --header-bg: {{ get_dark_colors()['usage']['surface'] ?? '#1e293b' }};
        --header-border: {{ get_dark_colors()['usage']['border'] ?? '#334155' }};
        --card-bg: {{ get_dark_colors()['usage']['surface'] ?? '#1e293b' }};
        --card-border: {{ get_dark_colors()['usage']['border'] ?? '#334155' }};
        --hover-bg: {{ get_dark_colors()['neutral']['gray-50'] ?? '#334155' }};
    }
    
    /* Body Font */
    body {
        font-family: 'Inter', sans-serif;
        @if(is_rtl_language(app()->getLocale()))
        font-family: 'Cairo', sans-serif;
        @endif
    }
    
    /* Admin Layout Styles */
    .admin-sidebar {
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        transition: all 0.3s ease;
    }
    
    .admin-header {
        background: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        transition: all 0.3s ease;
    }
    
    .admin-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        transition: all 0.3s ease;
    }
    
    .admin-hover:hover {
        background: var(--hover-bg);
        transition: all 0.2s ease;
    }
    
    /* Mobile Sidebar Specific Styles */
    @media (max-width: 1023px) {
        .admin-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 50 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease-in-out !important;
        }
        
        .admin-sidebar.translate-x-0 {
            transform: translateX(0) !important;
        }
        
        /* RTL Mobile Sidebar */
        [dir="rtl"] .admin-sidebar {
            left: auto !important;
            right: 0 !important;
            transform: translateX(100%) !important;
        }
        
        [dir="rtl"] .admin-sidebar.translate-x-0 {
            transform: translateX(0) !important;
        }
    }
    
    /* RTL Support */
    @if(is_rtl_language(app()->getLocale()))
    .admin-sidebar {
        border-right: none;
        border-left: 1px solid var(--sidebar-border);
    }
    
    .admin-header {
        border-bottom: 1px solid var(--header-border);
    }
    @endif
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .admin-sidebar {
            transform: translateX(-100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
        }
        
        .admin-sidebar.mobile-open {
            transform: translateX(0);
        }
        
        @if(is_rtl_language(app()->getLocale()))
        .admin-sidebar {
            transform: translateX(100%);
            right: 0;
            left: auto;
        }
        
        .admin-sidebar.mobile-open {
            transform: translateX(0);
        }
        @endif
    }
    
    /* Animations */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .slide-in {
        animation: slideIn 0.3s ease-in-out;
    }
    
    @keyframes slideIn {
        from { transform: translateX(-20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    /* Modal Styles - Using same approach as login page */
    #adminSettingsModal {
        display: none !important;
    }
    
    #adminSettingsModal.modal-show {
        display: flex !important;
    }
    
    #adminSettingsModalContent {
        transition: all 0.3s ease;
    }
    
    /* RTL Layout Support */
    [dir="rtl"] .admin-sidebar {
        right: 0;
        left: auto;
    }
    
    [dir="rtl"] .admin-header {
        margin-right: 16rem;
        margin-left: 0;
        width: calc(100% - 16rem);
    }
    
    [dir="rtl"] .admin-main-content {
        margin-right: 16rem;
        margin-left: 0;
        width: calc(100% - 16rem);
    }
    
    /* LTR Layout Support */
    [dir="ltr"] .admin-sidebar {
        left: 0;
        right: auto;
    }
    
    [dir="ltr"] .admin-header {
        margin-left: 16rem;
        margin-right: 0;
        width: calc(100% - 16rem);
    }
    
    [dir="ltr"] .admin-main-content {
        margin-left: 16rem;
        margin-right: 0;
        width: calc(100% - 16rem);
    }
    
    /* RTL Text Direction - Force all text to right in Arabic */
    [dir="rtl"] * {
        text-align: right !important;
        direction: rtl !important;
    }
    
    [dir="rtl"] .text-left {
        text-align: right !important;
    }
    
    [dir="rtl"] .text-right {
        text-align: right !important;
    }
    
    [dir="rtl"] .text-center {
        text-align: center !important;
    }
    
    /* RTL Flex Direction */
    [dir="rtl"] .flex-row {
        flex-direction: row-reverse;
    }
    
    [dir="rtl"] .flex-row-reverse {
        flex-direction: row;
    }
    
    /* RTL Spacing */
    [dir="rtl"] .space-x-4 > * + * {
        margin-left: 0;
        margin-right: 1rem;
    }
    
    [dir="rtl"] .space-x-reverse > * + * {
        margin-right: 0;
        margin-left: 1rem;
    }
    
    /* RTL Input Fields */
    [dir="rtl"] input, [dir="rtl"] textarea, [dir="rtl"] select {
        text-align: right !important;
        direction: rtl !important;
    }
    
    /* RTL Buttons */
    [dir="rtl"] button {
        text-align: right !important;
    }
    
    /* RTL Tables */
    [dir="rtl"] table {
        direction: rtl !important;
    }
    
    [dir="rtl"] th, [dir="rtl"] td {
        text-align: right !important;
    }
    
    /* RTL Cards */
    [dir="rtl"] .admin-card {
        text-align: right !important;
    }
    
    /* RTL Navigation */
    [dir="rtl"] nav {
        direction: rtl !important;
    }
    
    [dir="rtl"] nav a {
        text-align: right !important;
    }
    
    /* Assets Dropdown Styles */
    #assetsDropdownMenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out;
    }
    
    #assetsDropdownMenu:not(.hidden) {
        transition: max-height 0.3s ease-in-out;
    }
    
    #assetsDropdownIcon {
        transition: transform 0.3s ease-in-out;
    }
    
    /* Header Layout Fixes */
    .admin-header {
        position: relative;
        z-index: 10;
    }
    
    /* Ensure both headers have same height */
    .admin-sidebar .flex.items-center.justify-between {
        height: 4.5rem; /* Fixed height instead of min-height */
    }
    
    .admin-header {
        height: 4.5rem; /* Fixed height to match sidebar */
    }
    
    /* Force exact height matching */
    .admin-sidebar .flex.items-center.justify-between,
    .admin-header {
        height: 4.5rem !important;
        min-height: 4.5rem !important;
        max-height: 4.5rem !important;
    }
    
    /* Ensure header touches sidebar */
    [dir="rtl"] .admin-header {
        margin-right: 16rem !important;
        margin-left: 0 !important;
        width: calc(100vw - 16rem) !important;
        max-width: calc(100vw - 16rem) !important;
    }
    
    [dir="ltr"] .admin-header {
        margin-left: 16rem !important;
        margin-right: 0 !important;
        width: calc(100vw - 16rem) !important;
        max-width: calc(100vw - 16rem) !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 1024px) {
        [dir="rtl"] .admin-header,
        [dir="ltr"] .admin-header {
            margin-right: 0 !important;
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>
