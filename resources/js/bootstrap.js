console.log('Starting to load bootstrap.js');
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
    authEndpoint: '/broadcasting/auth' // Explicitly set this
});

console.log('Echo initialized with authEndpoint:', '/broadcasting/auth');

console.log('Bootstrap.js loaded, Echo initialized with:', {
    broadcaster: 'pusher',
    key: '4cb653de810be070150e',
    cluster: 'eu',
    forceTLS: true,
    authEndpoint: '/broadcasting/auth'
});
