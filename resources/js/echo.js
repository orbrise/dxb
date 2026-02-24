/** 
 * Laravel Echo Configuration for Reverb WebSocket Server
 * 
 * This file configures Laravel Echo to connect to the Reverb WebSocket server
 * for real-time event broadcasting.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Debug connection status in development
if (import.meta.env.DEV) {
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Connected to Reverb WebSocket server');
    });

    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('❌ Disconnected from Reverb WebSocket server');
    });

    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('🔴 Reverb connection error:', err);
    });
}
