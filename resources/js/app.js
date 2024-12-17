require('./bootstrap');
window.$ = window.jQuery = require('jquery')
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import VueGoogleMaps from '@fawmi/vue-google-maps';
import mitt from 'mitt';
import Select2 from 'vue3-select2-component';
import Notifications from 'notiwind'
import VueApexCharts from "vue3-apexcharts";
import Multiselect from '@suadelabs/vue3-multiselect'
import vSelect from 'vue-select'
import firebase from 'firebase/app';
import "firebase/messaging";
import { Inertia } from '@inertiajs/inertia';
import config from '../../firebase_config.js';

//   BJHkjvDg-FEQeDYFrQehhuutY1yZThNeMEhWDkiR07qtxoWpKEbqj5YQionRDfd4kvTo6MsgDKtpCXKd74MexYc
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// Sweet Alert
window.swal = require('sweetalert2');
window.toast = swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    customClass: {
        title: 'text-xl'
    }
});

vSelect.props.components.default = () => ({
  OpenIndicator: {
    render: () => h('span', '🔽'),
  },
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    // resolve: (name) => require(`./Pages/${name}.vue`),
    resolve: (name) => {
        let parts = name.split('::')
        let type = false
        if (parts.length > 1) type = parts[0]
        if (type) {
            let nameVue = parts[1].split('.')[0]
            return require("../../Modules/" + parts[0] + "/Resources/js/Pages/" + nameVue + ".vue").default
        }else {
            return require(`./Pages/${name}`).default
        }
    },
    setup({ el, app, props, plugin }) {
        const appVue = createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } });
            appVue.config.globalProperties.swal = window.swal;
            appVue.config.globalProperties.toast = window.toast;
            appVue.config.globalProperties.emitter = mitt()
            appVue.component('Select2', Select2)
            appVue.component('multiselect', Multiselect)
            appVue.component('v-select', vSelect)
            appVue.use(Notifications)
            appVue.use(VueApexCharts)
            appVue.use(VueGoogleMaps, {
                load: {
                    // local key
                    // key: 'AIzaSyB7jagE3dAg0o2Z-qlDPzqEfEcOAWoFKGY',
                    // live key
                    key: 'AIzaSyB6_25wMJBgEeLqkRTBxC0aCXppPeXXCdQ',
                    libraries: "places,drawing"
                },
                installComponents: true
            });
            // fire base integration
            if(!firebase.apps.length) {
                firebase.initializeApp(config);
            }

            const messaging = firebase.messaging();
            if ("serviceWorker" in navigator) {
                window.addEventListener("load", function () {
                    navigator.serviceWorker
                    .register("/firebase-messaging-sw.js")
                    .then(function (registration) {
                        firebase.messaging().useServiceWorker(registration);
                        console.log("Service Worker registered with scope: ", registration.scope);
                    })
                    .catch(function (error) {
                        console.log("Service Worker registration failed: ", error);
                    });
                });
            }

            messaging.requestPermission()
            .then(() => {
                localStorage.setItem('permission', true);
                messaging.getToken()
                .then((currentToken) => {
                    if (currentToken) {
                        window.axios.get(route('save-device-token'), {
                        params: {
                            device_token: currentToken,
                            device_type: /Mobile|Tablet/i.test(window.navigator.userAgent) ? 'Mobile or Tablet' : 'Desktop',
                            device_name: window.navigator.userAgent.match(/\((.*?)\)/)[1],
                            language: 'en',
                            notification: 1
                        }
                    }).then((response) => {
                    })
                    .catch(error => {
                        console.log(error);
                    });
                        localStorage.setItem('firebase_token', currentToken);
                    } else {
                    console.log('No registration token available. Request permission to generate one.');
                    }
                })
                .catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
                console.log('Notification permission granted.');
            })
            .catch((error) => {
                console.log('Notification permission denied.', error);
            });
            
            messaging.onMessage((payload) => {
                const notification = new Notification(payload.notification.title, {
                    body: payload.notification.message,
                    timestamp: Date.now()
                });
                notification.addEventListener('click', (event) => {
                  Inertia.visit(route('automotive.dashboard.dealership.contact-form.show', [payload.data.module_id, payload.data.dealership_id, payload.data.contact_id]))
                })
            });

            appVue.mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
