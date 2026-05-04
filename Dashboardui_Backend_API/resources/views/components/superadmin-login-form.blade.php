<!-- SuperAdmin Login Form -->
<div class="w-full max-w-md space-y-8">
    <!-- Logo and Header -->
    <div class="text-center">
        <div class="mx-auto h-20 w-20 bg-white rounded-full shadow-lg overflow-hidden mb-6 floating-animation">
            <img src="{{ asset_logo() }}" alt="{{ get_logo_alt() }}" class="w-full h-full object-cover rounded-full">
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ get_translation('welcome_back') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ get_translation('superadmin_login_subtitle') ?? get_translation('login_subtitle') }}
        </p>
        <div class="mt-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background: var(--primary-color)20; color: var(--primary-color);">
                {{ get_translation('superadmin_panel') ?? 'Super Admin Panel' }}
            </span>
        </div>
    </div>

    <!-- Login Form Card -->
    <div class="shadow-2xl rounded-2xl p-8" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <form class="space-y-6" id="loginForm" action="{{ route_with_lang('superadmin.login.submit') }}" method="POST">
        @csrf
        
        <!-- Error Messages -->
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ get_translation('invalid_credentials') }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="space-y-4">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-color);">
                    {{ get_translation('email') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 {{ is_rtl_language(app()->getLocale()) ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: var(--text-secondary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           value="{{ old('email') }}"
                           class="appearance-none rounded-lg relative block w-full py-3 focus:outline-none focus:ring-2 focus:z-10 sm:text-sm transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left pl-10 pr-4' }}"
                           style="border: 1px solid var(--border-color); color: var(--text-color); background: var(--surface-color);"
                           placeholder="{{ get_translation('email') }}"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20';"
                           onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-color);">
                    {{ get_translation('password') }}
                </label>
                <div class="relative">
                    <!-- Lock Icon (Left side for LTR, Right side for RTL) -->
                    <div class="absolute inset-y-0 {{ is_rtl_language(app()->getLocale()) ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: var(--text-secondary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    
                    <!-- Password Input -->
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="appearance-none rounded-lg relative block w-full py-3 focus:outline-none focus:ring-2 focus:z-10 sm:text-sm transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left pl-10 pr-10' }}"
                           style="border: 1px solid var(--border-color); color: var(--text-color); background: var(--surface-color);"
                           placeholder="{{ get_translation('password') }}"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20';"
                           onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                    
                    <!-- Toggle Password Button (Right side for LTR, Left side for RTL) -->
                    <button type="button" 
                            id="togglePassword" 
                            class="absolute inset-y-0 flex items-center transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'left-0 pl-3' : 'right-0 pr-3' }}"
                            style="color: var(--text-secondary-color);"
                            onmouseover="this.style.color='var(--primary-color)';"
                            onmouseout="this.style.color='var(--text-secondary-color)';">
                        <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                <input id="remember" 
                       name="remember" 
                       type="checkbox" 
                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                <label for="remember" class="{{ is_rtl_language(app()->getLocale()) ? 'mr-2' : 'ml-2' }} block text-sm" style="color: var(--text-color);">
                    {{ get_translation('remember_me') }}
                </label>
            </div>
            <div class="text-sm">
                <a href="#" class="font-medium transition-colors duration-200" style="color: var(--primary-color);" onmouseover="this.style.color='var(--primary-hover)';" onmouseout="this.style.color='var(--primary-color)';">
                    {{ get_translation('forgot_password') }}
                </a>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" 
                    id="loginButton"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105"
                    style="background: var(--primary-color);"
                    onmouseover="this.style.background='var(--primary-hover)';"
                    onmouseout="this.style.background='var(--primary-color)';">
                <span class="absolute {{ is_rtl_language(app()->getLocale()) ? 'right-0 pr-3' : 'left-0 pl-3' }} inset-y-0 flex items-center">
                    <svg id="loginIcon" class="h-5 w-5 text-white group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </span>
                <span id="loginText">{{ get_translation('sign_in') }}</span>
            </button>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-6 p-4 rounded-lg" style="background: var(--primary-color)10; border: 1px solid var(--primary-color)30;">
            <h4 class="text-sm font-semibold mb-2" style="color: var(--primary-color);">{{ get_translation('demo_credentials') ?? 'Demo Credentials:' }}</h4>
            <div class="text-xs space-y-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
                <p><strong>{{ get_translation('super_admin') ?? 'Super Admin' }}:</strong> superadmin@example.com / password</p>
            </div>
        </div>
        </form>
    </div>
</div>
