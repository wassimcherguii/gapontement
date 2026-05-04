import './bootstrap';
import 'flowbite';
import { initFlowbite } from 'flowbite';

// Initialize Flowbite when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initFlowbite();
});

// Also initialize immediately if DOM is already ready
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
} else {
    // DOM is already ready, initialize immediately
    initFlowbite();
}
