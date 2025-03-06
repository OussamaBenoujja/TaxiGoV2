// resources/js/bootstrap.js
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Debug logging for Pusher
console.log('Setting up Pusher with:');
console.log('- Key:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('- Cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);

// Check if Pusher key is available
if (!import.meta.env.VITE_PUSHER_APP_KEY) {
    console.error('VITE_PUSHER_APP_KEY is missing in your environment variables.');
    console.warn('This will cause Echo to fail. Check your .env file and ensure you have recompiled assets.');
}

// Enable Pusher debug logs
Pusher.logToConsole = true;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'missing-key',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    authEndpoint: '/broadcasting/auth', // Explicitly set auth endpoint
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
});

console.log('Echo initialized in bootstrap.js');

// Import echo.js with additional listeners
import './echo';