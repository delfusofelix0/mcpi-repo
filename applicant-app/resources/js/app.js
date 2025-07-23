import '../css/app.css';
import '../css/style.css'
import './bootstrap';
import '../css/style.css';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import {definePreset, palette} from '@primeuix/themes';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';

const appName = import.meta.env.VITE_APP_NAME || 'MCPI';

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{sky.50}',
            100: '{sky.100}',
            200: '{sky.200}',
            300: '{sky.300}',
            400: '{sky.400}',
            500: '{sky.500}',
            600: '{sky.600}',
            700: '{sky.700}',
            800: '{sky.800}',
            900: '{sky.900}',
            950: '{sky.950}'
        }
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({el, App, props, plugin}) {
        const app = createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: MyPreset,
                    options: {
                        prefix: 'p',
                        darkModeSelector: '.my-app-dark',
                        cssLayer: false
                    }
                }
            })
            .use(ToastService)
            .use(ConfirmationService)
            .mount(el);

        delete el.dataset.page;

        return app;
    },
    progress: {
        color: '#a855f7',
    },
});
