<!-- Settings Modal Overlay -->
<div id="settingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" 
     style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    
    <!-- Modal Content -->
    <div id="settingsModalContent" 
         class="relative w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0"
         style="background: var(--surface-color); border: 1px solid var(--border-color);">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold" style="color: var(--text-color);">
                {{ get_translation('settings') }}
            </h3>
            <button onclick="toggleSettingsPopup()" 
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-200 hover:scale-110"
                    style="color: var(--text-secondary-color); background: var(--background-color);"
                    onmouseover="this.style.background='var(--primary-color)20'; this.style.color='var(--primary-color)';"
                    onmouseout="this.style.background='var(--background-color)'; this.style.color='var(--text-secondary-color)';">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-8">
            <!-- Language Selector Section -->
            <div>
                <h4 class="text-lg font-semibold mb-4" style="color: var(--text-color);">
                    {{ get_translation('language') }}
                </h4>
                <div class="space-y-3">
                    @foreach(get_supported_languages() as $code => $language)
                    <button data-language-link="{{ $code }}"
                            class="w-full flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:scale-105 {{ app()->getLocale() === $code ? 'ring-2' : '' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}"
                            style="{{ app()->getLocale() === $code 
                                     ? 'background: var(--primary-color)20; border: 2px solid var(--primary-color); color: var(--primary-color);' 
                                     : 'background: var(--background-color); border: 1px solid var(--border-color); color: var(--text-color);' }}"
                            onmouseover="if('{{ app()->getLocale() }}' !== '{{ $code }}') { this.style.background='var(--primary-color)10'; this.style.borderColor='var(--primary-color)'; this.style.transform='scale(1.02)'; }"
                            onmouseout="if('{{ app()->getLocale() }}' !== '{{ $code }}') { this.style.background='var(--background-color)'; this.style.borderColor='var(--border-color)'; this.style.transform='scale(1)'; }">
                        <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse space-x-4' : 'space-x-4' }}">
                            <span class="text-2xl">{{ $language['flag'] }}</span>
                            <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                                <div class="font-semibold">{{ $language['native'] }}</div>
                                <div class="text-sm opacity-75">{{ $language['name'] }}</div>
                            </div>
                        </div>
                        @if(app()->getLocale() === $code)
                        <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background: var(--primary-color);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Theme Selector Section -->
            <div>
                <h4 class="text-lg font-semibold mb-4" style="color: var(--text-color);">
                    {{ get_translation('theme') }}
                </h4>
                <div class="flex space-x-3">
                    <button id="lightThemeBtn" 
                            onclick="changeTheme('light')"
                            class="flex-1 flex items-center justify-center p-4 rounded-xl transition-all duration-200 hover:scale-105"
                            style="background: var(--background-color); border: 1px solid var(--border-color); color: var(--text-color);">
                        <div class="text-center">
                            <div class="text-2xl mb-2">☀️</div>
                            <div class="font-semibold">{{ get_translation('light') }}</div>
                        </div>
                    </button>
                    <button id="darkThemeBtn" 
                            onclick="changeTheme('dark')"
                            class="flex-1 flex items-center justify-center p-4 rounded-xl transition-all duration-200 hover:scale-105"
                            style="background: var(--background-color); border: 1px solid var(--border-color); color: var(--text-color);">
                        <div class="text-center">
                            <div class="text-2xl mb-2">🌙</div>
                            <div class="font-semibold">{{ get_translation('dark') }}</div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Contact Us Button -->
            <div>
                <button onclick="goToContact()" 
                        class="w-full flex items-center justify-center p-4 rounded-xl transition-all duration-200 hover:scale-105"
                        style="background: var(--primary-color)20; border: 1px solid var(--primary-color); color: var(--primary-color);"
                        onmouseover="this.style.background='var(--primary-color)30'; this.style.transform='scale(1.02)';"
                        onmouseout="this.style.background='var(--primary-color)20'; this.style.transform='scale(1)';">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold">{{ get_translation('contact_us') }}</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
