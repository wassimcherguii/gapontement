<script>
// Modal Functions
function toggleSettingsPopup() {
    const modal = document.getElementById('settingsModal');
    const modalContent = document.getElementById('settingsModalContent');
    const button = document.getElementById('settingsButton');
    
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
        closeModal();
    }
}

function closeModal() {
    const modal = document.getElementById('settingsModal');
    const modalContent = document.getElementById('settingsModalContent');
    const button = document.getElementById('settingsButton');
    
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

// Language change functionality is now handled by language-persistence.blade.php

// Theme change functionality
function changeTheme(theme) {
    // Store theme in localStorage
    localStorage.setItem('theme', theme);
    
    // Apply theme to document
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Update theme buttons
    const lightBtn = document.getElementById('lightThemeBtn');
    const darkBtn = document.getElementById('darkThemeBtn');
    
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

// Contact us functionality
function goToContact() {
    // For now, just show an alert
    alert('Contact us page will be created soon!');
    // Later: window.location.href = '/contact';
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const defaultTheme = '{{ get_default_theme() }}';
    const savedTheme = localStorage.getItem('theme') || defaultTheme;
    changeTheme(savedTheme);
    
    // Ensure modal is closed on page load
    closeModal();
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('settingsModal');
    const modalContent = document.getElementById('settingsModalContent');
    const button = document.getElementById('settingsButton');
    
    if (modal && modal.classList.contains('modal-show')) {
        if (event.target === modal || (!modalContent.contains(event.target) && !button.contains(event.target))) {
            closeModal();
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('settingsModal');
        if (modal && modal.classList.contains('modal-show')) {
            closeModal();
        }
    }
});
</script>
