<style>
    /* CSS Variables for Colors */
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
    }
    
    /* Body Font */
    body {
        font-family: 'Inter', sans-serif;
        @if(is_rtl_language(app()->getLocale()))
        font-family: 'Cairo', sans-serif;
        @endif
    }
    
    /* RTL Text Alignment */
    @if(is_rtl_language(app()->getLocale()))
    input[type="email"], 
    input[type="password"], 
    input[type="text"] {
        text-align: right !important;
        direction: rtl;
    }
    
    input[type="email"]::placeholder, 
    input[type="password"]::placeholder, 
    input[type="text"]::placeholder {
        text-align: right;
        direction: rtl;
    }
    @else
    input[type="email"], 
    input[type="password"], 
    input[type="text"] {
        text-align: left !important;
        direction: ltr;
    }
    
    input[type="email"]::placeholder, 
    input[type="password"]::placeholder, 
    input[type="text"]::placeholder {
        text-align: left;
        direction: ltr;
    }
    @endif
    
    /* Modal Hidden State - Prevents Flash */
    #settingsModal {
        display: none !important;
    }
    
    /* Modal Show State */
    #settingsModal.modal-show {
        display: flex !important;
    }
    
    /* Floating Animation */
    .floating-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    /* Pulse Animation */
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
