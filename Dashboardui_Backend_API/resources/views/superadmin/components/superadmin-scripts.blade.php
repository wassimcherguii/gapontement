<script>
    // SuperAdmin Dashboard JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Sidebar Toggle
        const mobileMenuBtn = document.getElementById('superadminMobileMenuBtn');
        const sidebar = document.getElementById('superadminSidebar');
        const sidebarOverlay = document.getElementById('superadminSidebarOverlay');
        const sidebarCloseBtn = document.getElementById('superadminSidebarCloseBtn');
        
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Show sidebar by removing the translate class
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                
                // Show overlay
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('hidden');
                }
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
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
        const userMenuBtn = document.getElementById('superadminUserMenuBtn');
        const userDropdown = document.getElementById('superadminUserDropdown');
        
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
        
        // SuperAdmin Settings Modal
        function toggleSuperadminSettingsPopup() {
            const modal = document.getElementById('superadminSettingsModal');
            const modalContent = document.getElementById('superadminSettingsModalContent');
            const button = document.getElementById('superadminSettingsBtn');
            
            if (!modal.classList.contains('modal-show')) {
                // Show modal
                modal.classList.remove('hidden');
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
                closeSuperadminModal();
            }
        }

        function closeSuperadminModal() {
            const modal = document.getElementById('superadminSettingsModal');
            const modalContent = document.getElementById('superadminSettingsModalContent');
            const button = document.getElementById('superadminSettingsBtn');
            
            if (modal.classList.contains('modal-show')) {
                // Hide modal with CSS transitions
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                button.style.transform = 'scale(1)';
                
                // Hide modal completely after animation
                setTimeout(() => {
                    modal.classList.remove('modal-show');
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        // SuperAdmin Settings Modal Event Listeners
        const superadminSettingsBtn = document.getElementById('superadminSettingsBtn');
        const closeSuperadminSettingsBtn = document.getElementById('closeSuperadminSettingsBtn');
        
        if (superadminSettingsBtn) {
            superadminSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSuperadminSettingsPopup();
            });
        }
        
        if (closeSuperadminSettingsBtn) {
            closeSuperadminSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeSuperadminModal();
            });
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('superadminSettingsModal');
            const modalContent = document.getElementById('superadminSettingsModalContent');
            const button = document.getElementById('superadminSettingsBtn');
            
            if (modal && modal.classList.contains('modal-show')) {
                if (event.target === modal || (!modalContent.contains(event.target) && !button.contains(event.target))) {
                    closeSuperadminModal();
                }
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('superadminSettingsModal');
                if (modal && modal.classList.contains('modal-show')) {
                    closeSuperadminModal();
                }
            }
        });
        
        // Theme Toggle
        function updateSuperadminThemeButtons(theme) {
            const lightBtn = document.getElementById('superadminLightThemeBtn');
            const darkBtn = document.getElementById('superadminDarkThemeBtn');
            
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
        updateSuperadminThemeButtons(savedTheme);
        
        // Add theme toggle event listeners
        const superadminLightThemeBtn = document.getElementById('superadminLightThemeBtn');
        const superadminDarkThemeBtn = document.getElementById('superadminDarkThemeBtn');
        
        if (superadminLightThemeBtn) {
            superadminLightThemeBtn.addEventListener('click', function() {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('theme', 'light');
                updateSuperadminThemeButtons('light');
            });
        }
        
        if (superadminDarkThemeBtn) {
            superadminDarkThemeBtn.addEventListener('click', function() {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                updateSuperadminThemeButtons('dark');
            });
        }
        
        // System Management Dropdown Toggle
        const systemManagementDropdownToggle = document.getElementById('systemManagementDropdownToggle');
        const systemManagementDropdownMenu = document.getElementById('systemManagementDropdownMenu');
        const systemManagementDropdownIcon = document.getElementById('systemManagementDropdownIcon');
        
        const currentPath = window.location.pathname;
        const isSystemRoute = currentPath.includes('/superadmin/system/') || currentPath.includes('/superadmin/users');
        const savedSystemState = localStorage.getItem('systemManagementDropdownOpen');
        const shouldBeOpen = isSystemRoute || savedSystemState === 'true';
        
        if (systemManagementDropdownToggle && systemManagementDropdownMenu && systemManagementDropdownIcon) {
            if (shouldBeOpen) {
                systemManagementDropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    systemManagementDropdownMenu.style.maxHeight = systemManagementDropdownMenu.scrollHeight + 'px';
                }, 10);
                systemManagementDropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                systemManagementDropdownMenu.style.maxHeight = '0px';
            }
            
            systemManagementDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isHidden = systemManagementDropdownMenu.classList.contains('hidden');
                
                if (isHidden) {
                    systemManagementDropdownMenu.classList.remove('hidden');
                    setTimeout(() => {
                        systemManagementDropdownMenu.style.maxHeight = systemManagementDropdownMenu.scrollHeight + 'px';
                    }, 10);
                    systemManagementDropdownIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('systemManagementDropdownOpen', 'true');
                } else {
                    systemManagementDropdownMenu.style.maxHeight = '0px';
                    systemManagementDropdownIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('systemManagementDropdownOpen', 'false');
                    setTimeout(() => {
                        systemManagementDropdownMenu.classList.add('hidden');
                    }, 300);
                }
            });
        }
        
        // Admin Access Dropdown Toggle
        const adminAccessDropdownToggle = document.getElementById('adminAccessDropdownToggle');
        const adminAccessDropdownMenu = document.getElementById('adminAccessDropdownMenu');
        const adminAccessDropdownIcon = document.getElementById('adminAccessDropdownIcon');
        
        const isAdminRoute = currentPath.includes('/superadmin/admin/');
        const savedAdminState = localStorage.getItem('adminAccessDropdownOpen');
        const shouldBeOpenAdmin = isAdminRoute || savedAdminState === 'true';
        
        if (adminAccessDropdownToggle && adminAccessDropdownMenu && adminAccessDropdownIcon) {
            if (shouldBeOpenAdmin) {
                adminAccessDropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    adminAccessDropdownMenu.style.maxHeight = adminAccessDropdownMenu.scrollHeight + 'px';
                }, 10);
                adminAccessDropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                adminAccessDropdownMenu.style.maxHeight = '0px';
            }
            
            adminAccessDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isHidden = adminAccessDropdownMenu.classList.contains('hidden');
                
                if (isHidden) {
                    adminAccessDropdownMenu.classList.remove('hidden');
                    setTimeout(() => {
                        adminAccessDropdownMenu.style.maxHeight = adminAccessDropdownMenu.scrollHeight + 'px';
                    }, 10);
                    adminAccessDropdownIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('adminAccessDropdownOpen', 'true');
                } else {
                    adminAccessDropdownMenu.style.maxHeight = '0px';
                    adminAccessDropdownIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('adminAccessDropdownOpen', 'false');
                    setTimeout(() => {
                        adminAccessDropdownMenu.classList.add('hidden');
                    }, 300);
                }
            });
        }
        
        // Active menu item highlighting
        function highlightActiveMenuItem() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.superadmin-sidebar a');
            
            menuItems.forEach(item => {
                item.classList.remove('superadmin-active');
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('superadmin-active');
                }
            });
        }
        
        highlightActiveMenuItem();
        
        // Add superadmin-active class styles
        const style = document.createElement('style');
        style.textContent = `
            .superadmin-active {
                background: var(--primary-color)20 !important;
                border: 1px solid var(--primary-color) !important;
                color: var(--primary-color) !important;
            }
        `;
        document.head.appendChild(style);
    });
</script>
