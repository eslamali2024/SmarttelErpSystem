import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import 'remixicon/fonts/remixicon.css';
import { createI18n } from 'vue-i18n';
import en from './locales/en';
import ar from './locales/ar';

const messages = { en, ar };

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: (name) => {
        const [module, page] = name.includes('::') ? name.split('::') : [null, name];

        if (module) {
            const modulePath = `/Modules/${module}/resources/js/pages/${page}.vue`;

            return resolvePageComponent(
                modulePath,
                import.meta.glob<DefineComponent>('/Modules/**/resources/js/pages/**/*.vue')
            );
        }

        return resolvePageComponent(
            `/resources/js/pages/${name}.vue`,
            import.meta.glob<DefineComponent>('/resources/js/pages/**/*.vue')
        );
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mount(el);
    },
    progress: { color: '#4B5563' },
});

initializeTheme();
