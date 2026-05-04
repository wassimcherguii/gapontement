<!-- Admin Settings Modal -->
<div id="adminSettingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" 
     style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    
    <!-- Modal Content -->
    <div id="adminSettingsModalContent" 
         class="relative w-full max-w-md admin-card rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0"
         style="background: var(--surface-color); border: 1px solid var(--border-color);">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold" style="color: var(--text-color);">
                {{ get_translation('settings') }}
            </h3>
            <button id="closeSettingsBtn" 
                    class="w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-200 hover:scale-110"
                    style="color: var(--text-secondary-color); background: var(--background-color);"
                    onmouseover="this.style.background='var(--primary-color)20'; this.style.color='var(--primary-color)';"
                    onmouseout="this.style.background='var(--background-color)'; this.style.color='var(--text-secondary-color)';">
                {!! lucide_icon('x', 'w-6 h-6', 'currentColor') !!}
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
                            class="w-full flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:scale-105 {{ app()->getLocale() === $code ? 'ring-2 ring-primary' : '' }} {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}"
                            style="{{ app()->getLocale() === $code 
                                     ? 'background: var(--primary-color)20; border: 2px solid var(--primary-color); color: var(--primary-color);' 
                                     : 'background: var(--background-color); border: 1px solid var(--border-color); color: var(--text-color);' }}"
                            onmouseover="if('{{ app()->getLocale() }}' !== '{{ $code }}') { this.style.background='var(--primary-color)10'; this.style.borderColor='var(--primary-color)'; this.style.transform='scale(1.02)'; }"
                            onmouseout="if('{{ app()->getLocale() }}' !== '{{ $code }}') { this.style.background='var(--background-color)'; this.style.borderColor='var(--border-color)'; this.style.transform='scale(1)'; }"
                            onclick="console.log('Button clicked: {{ $code }}, Current: {{ app()->getLocale() }}');">
                        <div class="flex items-center {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse space-x-4' : 'space-x-4' }}">
                            <span class="text-2xl">{{ $language['flag'] }}</span>
                            <div class="{{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                                <div class="font-semibold">{{ $language['native'] }}</div>
                                <div class="text-sm opacity-75">{{ $language['name'] }}</div>
                            </div>
                        </div>
                        @if(app()->getLocale() === $code)
                        <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background: var(--primary-color);">
                            {!! lucide_icon('check', 'w-4 h-4', 'white') !!}
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
                            class="flex-1 flex items-center justify-center p-4 rounded-xl transition-all duration-200 hover:scale-105"
                            style="background: var(--background-color); border: 1px solid var(--border-color); color: var(--text-color);">
                        <div class="text-center">
                            <div class="text-2xl mb-2">☀️</div>
                            <div class="font-semibold">{{ get_translation('light') }}</div>
                        </div>
                    </button>
                    <button id="darkThemeBtn" 
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
                        {!! lucide_icon('mail', 'w-5 h-5', 'currentColor') !!}
                        <span class="font-semibold">{{ get_translation('contact_us') }}</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Contact Us function
    function goToContact() {
        // You can implement contact page redirection here
        alert('{{ get_translation("contact_us") }} - Coming Soon!');
    }
</script>
