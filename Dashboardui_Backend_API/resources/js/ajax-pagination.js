/**
 * Reusable AJAX Pagination Handler
 * Works with any table and preserves query parameters (tab, search, etc.)
 */
class AjaxPagination {
    constructor(options = {}) {
        this.containerId = options.containerId || 'table-container';
        this.paginationContainerId = options.paginationContainerId || 'pagination-container';
        this.loadingId = options.loadingId || 'pagination-loading';
        this.baseUrl = options.baseUrl || window.location.pathname;
        this.preserveParams = options.preserveParams || ['tab', 'search'];
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this.init();
    }
    
    init() {
        // Attach click handlers to all pagination links
        document.addEventListener('click', (e) => {
            if (e.target.closest('.ajax-pagination-link')) {
                e.preventDefault();
                const link = e.target.closest('.ajax-pagination-link');
                const url = link.getAttribute('href');
                this.loadPage(url);
            }
        });
        
        // Also handle tab changes to reset pagination
        const tabLinks = document.querySelectorAll('[data-tab-link]');
        tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Reset to page 1 when changing tabs
                const url = new URL(link.href);
                url.searchParams.set('page', '1');
                // Don't prevent default if it's not an AJAX tab link
                if (link.hasAttribute('data-ajax-tab')) {
                    e.preventDefault();
                    this.loadPage(url.toString(), true);
                }
            });
        });
    }
    
    /**
     * Load a page via AJAX
     * @param {string} url - The URL to load
     * @param {boolean} updateUrl - Whether to update browser URL
     */
    async loadPage(url, updateUrl = true) {
        try {
            // Show loading state
            this.showLoading();
            
            // Parse URL to get parameters
            const urlObj = new URL(url, window.location.origin);
            const params = new URLSearchParams(urlObj.search);
            
            // Ensure AJAX request header
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };
            
            if (this.csrfToken) {
                headers['X-CSRF-TOKEN'] = this.csrfToken;
            }
            
            // Make AJAX request
            const response = await fetch(url, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin',
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.html) {
                // Update table container
                const container = document.getElementById(this.containerId);
                if (container) {
                    container.innerHTML = data.html;
                    
                    // Add fade-in animation
                    container.classList.add('fade-in');
                    setTimeout(() => {
                        container.classList.remove('fade-in');
                    }, 300);
                }
                
                // Update pagination container
                if (data.pagination) {
                    const paginationContainer = document.getElementById(this.paginationContainerId);
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
                
                // Update total count if provided
                if (data.total !== undefined) {
                    const totalElement = document.getElementById('total-users-count');
                    if (totalElement) {
                        totalElement.textContent = data.total;
                    }
                }
                
                // Update browser URL without reload
                if (updateUrl) {
                    window.history.pushState(
                        { page: params.get('page') || 1, tab: params.get('tab') || 'all' },
                        '',
                        url
                    );
                }
                
                // Scroll to top of table
                const tableContainer = document.getElementById(this.containerId);
                if (tableContainer) {
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error('AJAX Pagination Error:', error);
            this.hideLoading();
            
            // Show error message
            this.showError('Failed to load page. Please try again.');
        } finally {
            this.hideLoading();
        }
    }
    
    /**
     * Show loading indicator
     */
    showLoading() {
        let loadingElement = document.getElementById(this.loadingId);
        if (!loadingElement) {
            loadingElement = document.createElement('div');
            loadingElement.id = this.loadingId;
            loadingElement.className = 'fixed inset-0 z-50 flex items-center justify-center';
            loadingElement.style.cssText = 'background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px);';
            loadingElement.innerHTML = `
                <div class="superadmin-card p-6 rounded-xl" style="background: var(--surface-color);">
                    <div class="flex items-center space-x-3 ${isRtl() ? 'flex-row-reverse space-x-reverse' : ''}">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: var(--primary-color);"></div>
                        <span style="color: var(--text-color);">${getTranslation('loading') || 'Loading...'}</span>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingElement);
        }
        loadingElement.style.display = 'flex';
    }
    
    /**
     * Hide loading indicator
     */
    hideLoading() {
        const loadingElement = document.getElementById(this.loadingId);
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }
    
    /**
     * Show error message
     */
    showError(message) {
        // Create or update error notification
        let errorElement = document.getElementById('ajax-pagination-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = 'ajax-pagination-error';
            errorElement.className = 'fixed top-4 right-4 z-50 max-w-md';
            document.body.appendChild(errorElement);
        }
        
        errorElement.innerHTML = `
            <div class="superadmin-card p-4 rounded-lg border border-red-200 bg-red-50" style="border-color: var(--error-color)40; background: var(--error-color)10;">
                <div class="flex items-center space-x-3 ${isRtl() ? 'flex-row-reverse space-x-reverse' : ''}">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium" style="color: var(--error-color);">${message}</p>
                </div>
            </div>
        `;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorElement) {
                errorElement.style.opacity = '0';
                errorElement.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (errorElement && errorElement.parentNode) {
                        errorElement.parentNode.removeChild(errorElement);
                    }
                }, 300);
            }
        }, 5000);
    }
}

// Helper functions
function isRtl() {
    return document.documentElement.dir === 'rtl' || 
           document.documentElement.getAttribute('dir') === 'rtl';
}

function getTranslation(key) {
    // This would typically call a translation helper
    // For now, return a simple fallback
    return window.translations?.[key] || null;
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.ajaxPagination = new AjaxPagination({
            containerId: 'users-table-container',
            paginationContainerId: 'users-pagination-container',
            loadingId: 'pagination-loading',
            preserveParams: ['tab', 'search']
        });
    });
} else {
    window.ajaxPagination = new AjaxPagination({
        containerId: 'users-table-container',
        paginationContainerId: 'users-pagination-container',
        loadingId: 'pagination-loading',
        preserveParams: ['tab', 'search']
    });
}

// Handle browser back/forward buttons
window.addEventListener('popstate', (event) => {
    if (window.ajaxPagination && event.state) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', event.state.page || '1');
        if (event.state.tab) {
            url.searchParams.set('tab', event.state.tab);
        }
        window.ajaxPagination.loadPage(url.toString(), false);
    }
});
