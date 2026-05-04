<?php

if (! function_exists('lucide_icon')) {
    function lucide_icon($name, $class = 'w-6 h-6', $stroke = 'currentColor', $strokeWidth = '2') {
        $icons = [
            // User & Profile Icons
            'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M20 8v6"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M17 11h6"></path>',
            'user-plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M8.5 11a4 4 0 100-8 4 4 0 000 8zM20 8v6M17 11h6"></path>',
            'user-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9 12l2 2 4-4"></path>',
            
            // Navigation Icons
            'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
            'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4z"></path>',
            'menu' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M4 6h16M4 12h16M4 18h16"></path>',
            'globe' => '<circle stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" cx="12" cy="12" r="10"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>',
            
            // Settings & Tools
            'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
            'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>',
            'bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>',
            
            // Content & Documents
            'file-text' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M14 2v6h6"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 13H8"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 17H8"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M10 9H8"></path>',
            'plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>',
            'edit' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>',
            
            // Analytics & Charts
            'bar-chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>',
            'trending-up' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>',
            'trending-down' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>',
            'chart-pie' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>',
            
            // E-commerce
            'shopping-bag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>',
            'dollar-sign' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>',
            'shopping-cart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5a2 2 0 100 4 2 2 0 000-4zm-6 0a2 2 0 100 4 2 2 0 000-4z"></path>',
            
            // Actions
            'logout' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>',
            'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9 5l7 7-7 7"></path>',
            'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M19 9l-7 7-7-7"></path>',
            'x' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M6 18L18 6M6 6l12 12"></path>',
            'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M5 13l4 4L19 7"></path>',
            
            // Activity & Notifications
            'activity' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M22 12h-4l-3 9L9 3l-3 9H2"></path>',
            'clock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
            'mail' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
            
            // Assets & Brand Management
            'award' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>',
            'palette' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
            'monitor' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
            'languages' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>',
            'database' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>',
            'git-compare' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M9 6H7.5a2.5 2.5 0 0 0 0 5h1.5M9 6V4.5a2.5 2.5 0 0 1 5 0V6M9 6h1.5a2.5 2.5 0 0 1 0 5H9M15 6h1.5a2.5 2.5 0 0 1 0 5H15M15 6V4.5a2.5 2.5 0 0 0-5 0V6"></path>',
                'upload' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>',
                'download' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"></path>',
                'sync' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>',
                
                // Old Brand Management Icons
                'image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>',
                'archive' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>',
                'eye' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 9a3 3 0 100 6 3 3 0 000-6z"></path>',
                'refresh-cw' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>',
                'trash-2' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14zM10 11v6M14 11v6"></path>',
        ];
        
        if (!isset($icons[$name])) {
            return '<svg class="' . $class . '" fill="none" stroke="' . $stroke . '" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="' . $strokeWidth . '" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'; // Default clock icon
        }
        
        return '<svg class="' . $class . '" fill="none" stroke="' . $stroke . '" viewBox="0 0 24 24">' . $icons[$name] . '</svg>';
    }
}
