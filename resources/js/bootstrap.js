
// Import the functions you need from the SDKs you need
window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
Popper = require('@popperjs/core');
window.bootstrap = require('bootstrap');


window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Show wait dialog
window.showWaitDialog = () => {
    window.swal.fire({
        html: 'Please wait...',
        showConfirmButton: false,
        backdrop: true,
        allowOutsideClick: false,
        width: '12rem',
        customClass: 'swal2-wait-dialog'
    })
}

// Hide wait dialog
window.hideWaitDialog = () => {
    window.swal.close()
}


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
