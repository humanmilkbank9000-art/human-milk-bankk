import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// If Echo and pusher are installed, initialize Echo (this is optional and will
// only run if the libraries are available after npm install).
try {
	import('laravel-echo').then(({ default: Echo }) => {
		// pusher-js is expected to be installed as well
		import('pusher-js').then(({ default: Pusher }) => {
			window.Pusher = Pusher;
			window.Echo = new Echo({
				broadcaster: 'pusher',
				key: process.env.MIX_PUSHER_APP_KEY || process.env.VITE_PUSHER_APP_KEY || '{{ env("PUSHER_APP_KEY") }}',
				cluster: process.env.MIX_PUSHER_APP_CLUSTER || process.env.VITE_PUSHER_APP_CLUSTER || '{{ env("PUSHER_APP_CLUSTER") }}',
				forceTLS: (process.env.MIX_PUSHER_APP_USE_TLS || process.env.VITE_PUSHER_APP_USE_TLS || '{{ env("PUSHER_APP_USE_TLS", true) }}') === 'true' || true,
			});
		}).catch(() => {
			// pusher-js not installed — Echo will not be initialized.
			// This is an optional enhancement; keep graceful degradation.
		});
	}).catch(() => {
		// laravel-echo not installed — skip real-time setup.
	});
} catch (e) {
	// ignore any dynamic import errors
}
