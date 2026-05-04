<script>
    // Admin Dashboard JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Sidebar Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('adminSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        
        console.log('Mobile menu button:', mobileMenuBtn);
        console.log('Sidebar:', sidebar);
        console.log('Sidebar overlay:', sidebarOverlay);
        
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Mobile menu clicked');
                
                // Show sidebar by removing the translate class
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                
                // Show overlay
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('hidden');
                }
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                console.log('Sidebar classes after click:', sidebar.className);
            });
        }
        
        if (sidebarCloseBtn && sidebar) {
            sidebarCloseBtn.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('hidden');
                }
                document.body.style.overflow = '';
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                sidebarOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }
        
        // User Dropdown Toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
        
        // Admin Settings Modal - Using same approach as login page
        function toggleAdminSettingsPopup() {
            const modal = document.getElementById('adminSettingsModal');
            const modalContent = document.getElementById('adminSettingsModalContent');
            const button = document.getElementById('adminSettingsBtn');
            
            if (!modal.classList.contains('modal-show')) {
                // Show modal
                modal.classList.add('modal-show');
                button.style.transform = 'scale(1.1)';
                
                // Trigger content animation
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            } else {
                closeAdminModal();
            }
        }

        function closeAdminModal() {
            const modal = document.getElementById('adminSettingsModal');
            const modalContent = document.getElementById('adminSettingsModalContent');
            const button = document.getElementById('adminSettingsBtn');
            
            if (modal.classList.contains('modal-show')) {
                // Hide modal with CSS transitions
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                button.style.transform = 'scale(1)';
                
                // Hide modal completely after animation
                setTimeout(() => {
                    modal.classList.remove('modal-show');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        // Admin Settings Modal Event Listeners
        const adminSettingsBtn = document.getElementById('adminSettingsBtn');
        const closeSettingsBtn = document.getElementById('closeSettingsBtn');
        
        if (adminSettingsBtn) {
            adminSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleAdminSettingsPopup();
            });
        }
        
        if (closeSettingsBtn) {
            closeSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeAdminModal();
            });
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('adminSettingsModal');
            const modalContent = document.getElementById('adminSettingsModalContent');
            const button = document.getElementById('adminSettingsBtn');
            
            if (modal && modal.classList.contains('modal-show')) {
                if (event.target === modal || (!modalContent.contains(event.target) && !button.contains(event.target))) {
                    closeAdminModal();
                }
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('adminSettingsModal');
                if (modal && modal.classList.contains('modal-show')) {
                    closeAdminModal();
                }
            }
        });
        
        // Initialize admin modal on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure modal is closed on page load
            closeAdminModal();
        });
        
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.classList.remove(currentTheme);
            html.classList.add(newTheme);
            
            // Save to localStorage
            localStorage.setItem('theme', newTheme);
            
            // Update theme buttons
            updateThemeButtons(newTheme);
        }
        
        function updateThemeButtons(theme) {
            const lightBtn = document.getElementById('lightThemeBtn');
            const darkBtn = document.getElementById('darkThemeBtn');
            
            if (lightBtn && darkBtn) {
                if (theme === 'light') {
                    lightBtn.style.background = 'var(--primary-color)20';
                    lightBtn.style.borderColor = 'var(--primary-color)';
                    lightBtn.style.color = 'var(--primary-color)';
                    darkBtn.style.background = 'var(--background-color)';
                    darkBtn.style.borderColor = 'var(--border-color)';
                    darkBtn.style.color = 'var(--text-color)';
                } else {
                    darkBtn.style.background = 'var(--primary-color)20';
                    darkBtn.style.borderColor = 'var(--primary-color)';
                    darkBtn.style.color = 'var(--primary-color)';
                    lightBtn.style.background = 'var(--background-color)';
                    lightBtn.style.borderColor = 'var(--border-color)';
                    lightBtn.style.color = 'var(--text-color)';
                }
            }
        }
        
        // Initialize theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.add(savedTheme);
        updateThemeButtons(savedTheme);
        
        // Add theme toggle event listeners
        const lightThemeBtn = document.getElementById('lightThemeBtn');
        const darkThemeBtn = document.getElementById('darkThemeBtn');
        
        if (lightThemeBtn) {
            lightThemeBtn.addEventListener('click', function() {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('theme', 'light');
                updateThemeButtons('light');
            });
        }
        
        if (darkThemeBtn) {
            darkThemeBtn.addEventListener('click', function() {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                updateThemeButtons('dark');
            });
        }
        
        // Website Dropdown Toggle (same behavior as Assets)
        const websiteDropdownToggle = document.getElementById('websiteDropdownToggle');
        const websiteDropdownMenu = document.getElementById('websiteDropdownMenu');
        const websiteDropdownIcon = document.getElementById('websiteDropdownIcon');
        const currentPathForMenus = window.location.pathname;
        const isWebsiteRoute = currentPathForMenus.includes('/admin/website/');
        const savedWebsiteState = localStorage.getItem('websiteDropdownOpen');
        const websiteShouldBeOpen = isWebsiteRoute || savedWebsiteState === 'true';

        if (websiteDropdownToggle && websiteDropdownMenu && websiteDropdownIcon) {
            if (websiteShouldBeOpen) {
                websiteDropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    websiteDropdownMenu.style.maxHeight = websiteDropdownMenu.scrollHeight + 'px';
                }, 10);
                websiteDropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                websiteDropdownMenu.style.maxHeight = '0px';
            }

            websiteDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const isHidden = websiteDropdownMenu.classList.contains('hidden');

                if (isHidden) {
                    websiteDropdownMenu.classList.remove('hidden');
                    setTimeout(() => {
                        websiteDropdownMenu.style.maxHeight = websiteDropdownMenu.scrollHeight + 'px';
                    }, 10);
                    websiteDropdownIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('websiteDropdownOpen', 'true');
                } else {
                    websiteDropdownMenu.style.maxHeight = '0px';
                    websiteDropdownIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('websiteDropdownOpen', 'false');
                    setTimeout(() => {
                        websiteDropdownMenu.classList.add('hidden');
                    }, 300);
                }
            });
        }

        // Assets Dropdown Toggle
        const assetsDropdownToggle = document.getElementById('assetsDropdownToggle');
        const assetsDropdownMenu = document.getElementById('assetsDropdownMenu');
        const assetsDropdownIcon = document.getElementById('assetsDropdownIcon');
        
        // Check if current route is an assets route to auto-expand
        const currentPath = window.location.pathname;
        const isAssetsRoute = currentPath.includes('/admin/assets/');
        
        // Initialize dropdown state - open if on assets route, otherwise check localStorage
        const savedAssetsState = localStorage.getItem('assetsDropdownOpen');
        const shouldBeOpen = isAssetsRoute || savedAssetsState === 'true';
        
        if (assetsDropdownToggle && assetsDropdownMenu && assetsDropdownIcon) {
            // Set initial state
            if (shouldBeOpen) {
                assetsDropdownMenu.classList.remove('hidden');
                // Use setTimeout to ensure transition works
                setTimeout(() => {
                    assetsDropdownMenu.style.maxHeight = assetsDropdownMenu.scrollHeight + 'px';
                }, 10);
                assetsDropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                assetsDropdownMenu.style.maxHeight = '0px';
            }
            
            // Toggle dropdown on click
            assetsDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isHidden = assetsDropdownMenu.classList.contains('hidden');
                
                if (isHidden) {
                    // Open dropdown
                    assetsDropdownMenu.classList.remove('hidden');
                    // Set max-height to actual height for smooth transition
                    setTimeout(() => {
                        assetsDropdownMenu.style.maxHeight = assetsDropdownMenu.scrollHeight + 'px';
                    }, 10);
                    assetsDropdownIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('assetsDropdownOpen', 'true');
                } else {
                    // Close dropdown
                    assetsDropdownMenu.style.maxHeight = '0px';
                    assetsDropdownIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('assetsDropdownOpen', 'false');
                    // Hide after transition
                    setTimeout(() => {
                        assetsDropdownMenu.classList.add('hidden');
                    }, 300);
                }
            });
        }
        
        // Active menu item highlighting
        function highlightActiveMenuItem() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.admin-sidebar a');
            
            menuItems.forEach(item => {
                item.classList.remove('admin-active');
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('admin-active');
                }
            });
        }
        
        highlightActiveMenuItem();
        
        // Add admin-active class styles
        const style = document.createElement('style');
        style.textContent = `
            .admin-active {
                background: var(--primary-color)20 !important;
                border: 1px solid var(--primary-color) !important;
                color: var(--primary-color) !important;
            }
        `;
        document.head.appendChild(style);
    });
</script>
