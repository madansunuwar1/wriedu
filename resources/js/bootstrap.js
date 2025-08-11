import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

// Set up Axios globally
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Optimize Pusher instance to use less memory
Pusher.logToConsole = false;

window.Pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    disableStats: true
});

window.Echo = new Echo({
    broadcaster: 'pusher',
    client: window.Pusher
});

// Listen for notifications efficiently
const userId = window.Laravel?.userId; // Ensure userId is available
if (userId) {
    const channel = window.Echo.private(`App.Models.User.${userId}`);
    channel.notification((notification) => {
        console.log('New notification:', notification);
        // Handle UI updates (e.g., add to notification list, show toast)
    });
}
