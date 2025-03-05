// bootstrap.js
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '4cb653de810be070150e',
    cluster: 'eu',
    forceTLS: true,
    
});

// Add extensive logging
console.log('Pusher Key:', window.Echo.options.key);
console.log('Auth Endpoint:', window.Echo.options.authEndpoint);
Pusher.logToConsole = true;
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
