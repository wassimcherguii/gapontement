<style>
    /* CSS Variables for SuperAdmin Colors */
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
        
        /* SuperAdmin Specific Colors */
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
        
        /* SuperAdmin Specific Colors - Dark */
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
    
    /* SuperAdmin Layout Styles */
    .superadmin-sidebar {
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        transition: all 0.3s ease;
    }
    
    .superadmin-header {
        background: var(--header-bg);
        border-bottom: 1px solid var(--header-border);
        transition: all 0.3s ease;
    }
    
    .superadmin-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        transition: all 0.3s ease;
    }
    
    .superadmin-hover:hover {
        background: var(--hover-bg);
        transition: all 0.2s ease;
    }
    
    /* Mobile Sidebar Specific Styles */
    @media (max-width: 1023px) {
        .superadmin-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 50 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease-in-out !important;
        }
        
        .superadmin-sidebar.translate-x-0 {
            transform: translateX(0) !important;
        }
        
        /* RTL Mobile Sidebar */
        [dir="rtl"] .superadmin-sidebar {
            left: auto !important;
            right: 0 !important;
            transform: translateX(100%) !important;
        }
        
        [dir="rtl"] .superadmin-sidebar.translate-x-0 {
            transform: translateX(0) !important;
        }
    }
    
    /* RTL Support */
    @if(is_rtl_language(app()->getLocale()))
    .superadmin-sidebar {
        border-right: none;
        border-left: 1px solid var(--sidebar-border);
    }
    
    .superadmin-header {
        border-bottom: 1px solid var(--header-border);
    }
    @endif
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .superadmin-sidebar {
            transform: translateX(-100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
        }
        
        .superadmin-sidebar.mobile-open {
            transform: translateX(0);
        }
        
        @if(is_rtl_language(app()->getLocale()))
        .superadmin-sidebar {
            transform: translateX(100%);
            right: 0;
            left: auto;
        }
        
        .superadmin-sidebar.mobile-open {
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
    
    /* Active Menu Item */
    .superadmin-active {
        background: var(--primary-color)20 !important;
        border: 1px solid var(--primary-color) !important;
        color: var(--primary-color) !important;
    }
    
    /* Modal Styles */
    .modal-show {
        display: flex !important;
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
    
    [dir="rtl"] .space-x-2 > * + * {
        margin-left: 0;
        margin-right: 0.5rem;
    }
    
    [dir="rtl"] .space-x-3 > * + * {
        margin-left: 0;
        margin-right: 0.75rem;
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
    [dir="rtl"] .superadmin-card {
        text-align: right !important;
    }
    
    /* RTL Navigation */
    [dir="rtl"] nav {
        direction: rtl !important;
    }
    
    [dir="rtl"] nav a {
        text-align: right !important;
    }
    
    /* RTL Layout Support */
    [dir="rtl"] .superadmin-sidebar {
        right: 0;
        left: auto;
    }
    
    [dir="rtl"] .superadmin-header {
        margin-right: 16rem;
        margin-left: 0;
        width: calc(100% - 16rem);
    }
    
    [dir="rtl"] .superadmin-main-content {
        margin-right: 16rem;
        margin-left: 0;
        width: calc(100% - 16rem);
    }
    
    /* LTR Layout Support */
    [dir="ltr"] .superadmin-sidebar {
        left: 0;
        right: auto;
    }
    
    [dir="ltr"] .superadmin-header {
        margin-left: 16rem;
        margin-right: 0;
        width: calc(100% - 16rem);
    }
    
    [dir="ltr"] .superadmin-main-content {
        margin-left: 16rem;
        margin-right: 0;
        width: calc(100% - 16rem);
    }
    
    /* Dropdown Menu Styles */
    #systemManagementDropdownMenu,
    #adminAccessDropdownMenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-in-out;
    }
    
    #systemManagementDropdownMenu:not(.hidden),
    #adminAccessDropdownMenu:not(.hidden) {
        transition: max-height 0.3s ease-in-out;
    }
    
    #systemManagementDropdownIcon,
    #adminAccessDropdownIcon {
        transition: transform 0.3s ease-in-out;
    }
    
    /* Header Layout Fixes */
    .superadmin-header {
        position: relative;
        z-index: 10;
    }
    
    /* Ensure both headers have same height */
    .superadmin-sidebar .flex.items-center.justify-between {
        height: 4.5rem; /* Fixed height instead of min-height */
    }
    
    .superadmin-header {
        height: 4.5rem; /* Fixed height to match sidebar */
    }
    
    /* Force exact height matching */
    .superadmin-sidebar .flex.items-center.justify-between,
    .superadmin-header {
        height: 4.5rem !important;
        min-height: 4.5rem !important;
        max-height: 4.5rem !important;
    }
    
    /* Ensure header touches sidebar */
    [dir="rtl"] .superadmin-header {
        margin-right: 16rem !important;
        margin-left: 0 !important;
        width: calc(100vw - 16rem) !important;
        max-width: calc(100vw - 16rem) !important;
    }
    
    [dir="ltr"] .superadmin-header {
        margin-left: 16rem !important;
        margin-right: 0 !important;
        width: calc(100vw - 16rem) !important;
        max-width: calc(100vw - 16rem) !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 1024px) {
        [dir="rtl"] .superadmin-header,
        [dir="ltr"] .superadmin-header {
            margin-right: 0 !important;
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    
    /* RTL Header Content Alignment */
    [dir="rtl"] .superadmin-header > div:first-child {
        flex-direction: row-reverse;
    }
    
    [dir="rtl"] .superadmin-header > div:last-child {
        flex-direction: row-reverse;
    }
    
    /* RTL User Dropdown Badge Positioning */
    [dir="rtl"] .superadmin-header .relative .absolute.-top-1 {
        right: auto !important;
        left: -0.25rem !important;
    }
    
    /* RTL Breadcrumb Navigation */
    [dir="rtl"] .superadmin-header nav {
        flex-direction: row-reverse;
    }
    
    [dir="rtl"] .superadmin-header nav svg {
        transform: scaleX(-1);
    }
    
    /* Tab Styles */
    .superadmin-tabs {
        border-bottom: 1px solid var(--border-color);
    }
    
    .superadmin-tab {
        position: relative;
        transition: all 0.2s ease;
    }
    
    .superadmin-tab:hover {
        background: var(--hover-bg);
    }
    
    .superadmin-tab-active {
        border-bottom-color: var(--primary-color) !important;
        color: var(--primary-color) !important;
    }
    
    .superadmin-tab-active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-color);
    }
    
    /* RTL Tab Support */
    [dir="rtl"] .superadmin-tab-active::after {
        left: auto;
        right: 0;
    }
    
    /* Tab Scrollbar */
    .superadmin-tabs::-webkit-scrollbar {
        height: 4px;
    }
    
    .superadmin-tabs::-webkit-scrollbar-track {
        background: var(--background-color);
    }
    
    .superadmin-tabs::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 2px;
    }
    
    .superadmin-tabs::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary-color);
    }
    
    /* Modal Show Class */
    .modal-show {
        display: flex !important;
    }
    
    /* Add User Modal Specific Styles */
    #addUserModal {
        display: none !important;
    }
    
    #addUserModal.modal-show {
        display: flex !important;
    }
    
    #addUserModalContent {
        transition: all 0.3s ease;
    }
    
    /* AJAX Pagination Styles */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #users-table-container {
        transition: opacity 0.2s ease-in-out;
    }
    
    /* Loading Spinner Animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Pagination Link Styles */
    .ajax-pagination-link:hover,
    .ajax-tab-link:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    /* Notification Styles */
    #successNotification,
    #errorNotification {
        transition: opacity 0.3s ease-out, transform 0.3s ease-out, max-height 0.3s ease-out, margin-bottom 0.3s ease-out, padding 0.3s ease-out;
    }
    
    #successNotification.closing,
    #errorNotification.closing {
        opacity: 0;
        transform: translateY(-10px);
        max-height: 0 !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
        overflow: hidden;
    }
</style>
