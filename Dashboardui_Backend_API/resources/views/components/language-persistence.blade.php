<script>
// Language Persistence System
class LanguagePersistence {
    constructor() {
        this.storageKey = 'technodec_language';
        this.defaultLang = '{{ get_default_language() }}';
        this.supportedLanguages = ['en', 'fr', 'ar'];
        this.init();
    }

    init() {
        // Set initial language from localStorage or default
        this.setInitialLanguage();
        
        // Listen for language changes
        this.setupLanguageListeners();
        
        // Apply language on page load
        this.applyLanguage();
    }

    setInitialLanguage() {
        const storedLang = localStorage.getItem(this.storageKey);
        const urlLang = this.getLanguageFromUrl();
        
        let targetLang = this.defaultLang;
        
        // Priority: URL parameter > localStorage > default
        if (urlLang && this.supportedLanguages.includes(urlLang)) {
            targetLang = urlLang;
        } else if (storedLang && this.supportedLanguages.includes(storedLang)) {
            targetLang = storedLang;
        }
        
        // Save to localStorage if not already set
        if (!storedLang) {
            localStorage.setItem(this.storageKey, targetLang);
        }
        
        // Always update URL with current language
        this.updateUrlLanguage(targetLang);
    }

    getLanguageFromUrl() {
        // Get language from URL path (first segment)
        const pathSegments = window.location.pathname.split('/').filter(segment => segment);
        const supportedLanguages = ['en', 'fr', 'ar'];
        
        if (pathSegments.length > 0 && supportedLanguages.includes(pathSegments[0])) {
            return pathSegments[0];
        }
        
        // Fallback: check query parameter (for backward compatibility)
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('lang');
    }

    updateUrlLanguage(lang) {
        const currentPath = window.location.pathname;
        const pathSegments = currentPath.split('/').filter(segment => segment);
        const supportedLanguages = ['en', 'fr', 'ar'];
        
        // Check if first segment is a language
        if (pathSegments.length > 0 && supportedLanguages.includes(pathSegments[0])) {
            // Replace the language in the path
            pathSegments[0] = lang;
            const newPath = '/' + pathSegments.join('/');
            const newUrl = new URL(newPath + window.location.search, window.location.origin);
            window.history.replaceState({}, '', newUrl);
        } else {
            // No language in path, add it
            const newPath = '/' + lang + currentPath;
            const newUrl = new URL(newPath + window.location.search, window.location.origin);
            window.history.replaceState({}, '', newUrl);
        }
    }

    setupLanguageListeners() {
        // Listen for language selector changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-language-selector]')) {
                const selectedLang = e.target.value;
                this.changeLanguage(selectedLang);
            }
        });

        // Listen for language links (more robust for RTL)
        document.addEventListener('click', (e) => {
            // Check if clicked element or its parent has data-language-link
            const languageButton = e.target.closest('[data-language-link]');
            if (languageButton) {
                console.log('Language button clicked:', languageButton);
                console.log('Button classes:', languageButton.className);
                console.log('Button attributes:', languageButton.attributes);
                
                e.preventDefault();
                e.stopPropagation();
                
                const selectedLang = languageButton.getAttribute('data-language-link');
                console.log('Selected language:', selectedLang);
                
                // Add visual feedback
                languageButton.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    languageButton.style.transform = '';
                }, 150);
                
                this.changeLanguage(selectedLang);
            }
        });
    }

    changeLanguage(lang) {
        if (!this.supportedLanguages.includes(lang)) {
            console.warn(`Unsupported language: ${lang}`);
            return;
        }

        console.log(`Changing language to: ${lang}`);
        
        // Save to localStorage
        localStorage.setItem(this.storageKey, lang);
        
        // Get current path and replace language segment
        const currentPath = window.location.pathname;
        const pathSegments = currentPath.split('/').filter(segment => segment);
        const supportedLanguages = ['en', 'fr', 'ar'];
        
        let newPath;
        if (pathSegments.length > 0 && supportedLanguages.includes(pathSegments[0])) {
            // Replace language in existing path
            pathSegments[0] = lang;
            newPath = '/' + pathSegments.join('/');
        } else {
            // Add language to path
            newPath = '/' + lang + currentPath;
        }
        
        // Close any open modals before reloading
        const settingsModal = document.getElementById('settingsModal');
        const adminSettingsModal = document.getElementById('adminSettingsModal');
        
        if (settingsModal && settingsModal.classList.contains('modal-show')) {
            console.log('Closing login settings modal before language change');
            settingsModal.classList.remove('modal-show');
            document.body.style.overflow = '';
        }
        
        if (adminSettingsModal && adminSettingsModal.classList.contains('modal-show')) {
            console.log('Closing admin settings modal before language change');
            adminSettingsModal.classList.remove('modal-show');
            document.body.style.overflow = '';
        }
        
        // Add a small delay to show the visual feedback
        setTimeout(() => {
            // Redirect to new language path
            window.location.href = newPath + window.location.search;
        }, 200);
    }

    applyLanguage() {
        const currentLang = this.getCurrentLanguage();
        
        // Update HTML attributes
        document.documentElement.setAttribute('lang', currentLang);
        document.documentElement.setAttribute('dir', this.getLanguageDirection(currentLang));
        
        // Update language selector if exists
        const languageSelector = document.querySelector('[data-language-selector]');
        if (languageSelector) {
            languageSelector.value = currentLang;
        }
        
        // Update active language links
        document.querySelectorAll('[data-language-link]').forEach(link => {
            link.classList.remove('active', 'bg-primary-100', 'text-primary-800');
            if (link.getAttribute('data-language-link') === currentLang) {
                link.classList.add('active', 'bg-primary-100', 'text-primary-800');
            }
        });
    }

    getCurrentLanguage() {
        const urlLang = this.getLanguageFromUrl();
        const storedLang = localStorage.getItem(this.storageKey);
        
        if (urlLang && this.supportedLanguages.includes(urlLang)) {
            return urlLang;
        } else if (storedLang && this.supportedLanguages.includes(storedLang)) {
            return storedLang;
        }
        
        return this.defaultLang;
    }

    getLanguageDirection(lang) {
        const directions = {
            'ar': 'rtl',
            'en': 'ltr',
            'fr': 'ltr'
        };
        return directions[lang] || 'ltr';
    }

    // Public method to get current language
    getLanguage() {
        return this.getCurrentLanguage();
    }

    // Public method to change language programmatically
    setLanguage(lang) {
        this.changeLanguage(lang);
    }
}

// Initialize language persistence when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.languagePersistence = new LanguagePersistence();
});

// Also initialize immediately if DOM is already ready
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
} else {
    // DOM is already ready, initialize immediately
    window.languagePersistence = new LanguagePersistence();
}
</script>
