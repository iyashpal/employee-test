import '@/bootstrap';
import '@/Assets/css/app.css';

import {createApp, h} from 'vue';
import type {DefineComponent} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {ZiggyVue} from '@/../../vendor/tightenco/ziggy';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({

    title: (title) => `${title} - ${appName}`,

    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ) as Promise<DefineComponent>,

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },

    progress: {
        color: '#4B5563',
    },
});
