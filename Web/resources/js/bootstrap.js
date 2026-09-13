import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ─────────────────────────────────────────────────────────────────────────────
// Laravel Echo — WebSocket via Laravel Reverb
// ─────────────────────────────────────────────────────────────────────────────
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    // Defaults to the host serving the page, so LAN browsers do not try to
    // connect to their own localhost. Set VITE_REVERB_HOST only when Reverb
    // is served from a different hostname.
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Blade page scripts can execute before this deferred module is evaluated.
window.dispatchEvent(new Event('echo:ready'));
