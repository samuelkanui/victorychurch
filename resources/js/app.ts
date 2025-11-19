import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import Toast from './components/Toast.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h('div', [
                h(App, props),
                h(Toast)
            ])
        });
        
        app.use(plugin).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// Prevent back button access after logout
// This ensures users can't navigate back to protected pages after logging out
window.addEventListener('pageshow', function(event) {
    // Check if page is loaded from cache (back/forward button)
    if (event.persisted) {
        // Force reload to check authentication status
        window.location.reload();
    }
});

// Additional protection: Disable back button functionality on auth pages
if (window.history && window.history.pushState) {
    // Push a dummy state when page loads
    window.history.pushState(null, '', window.location.href);
    
    // Listen for popstate (back/forward button)
    window.addEventListener('popstate', function() {
        // Push the state again to prevent going back
        window.history.pushState(null, '', window.location.href);
    });
}
