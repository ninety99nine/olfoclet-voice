import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const accessToken = localStorage.getItem('accessToken');

if(accessToken) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
