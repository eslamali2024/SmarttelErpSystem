import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h, watch } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import 'remixicon/fonts/remixicon.css';
import { createI18n } from 'vue-i18n';
import en from './locales/en';
import ar from './locales/ar';
import { useLang } from './composables/useLang';

// Sit General Messages
const generalMessages = { en, ar };
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const { lang } = useLang();

// Sit Language for Site
export const i18n = createI18n({
    legacy: false,
    locale: lang.value,
    fallbackLocale: 'en',
    messages: generalMessages
});
watch(lang, (newLang) => {
    i18n.global.locale.value = newLang;
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: async (name) => {
        const [module, page] = name.includes('::') ? name.split('::') : [null, name];

        let moduleMessages = { en: {}, ar: {} };

        if (module) {
            // Load module-specific translations
            try {
                const modules = import.meta.glob('/Modules/*/resources/js/locales/*.js');

                const enPath = `/Modules/${module}/resources/js/locales/en.js`;
                const arPath = `/Modules/${module}/resources/js/locales/ar.js`;

                const enModule = modules[enPath] ? await modules[enPath]() : { default: {} };
                const arModule = modules[arPath] ? await modules[arPath]() : { default: {} };

                moduleMessages = {
                    en: enModule.default,
                    ar: arModule.default
                };
            } catch {
                console.warn(`No module-specific languages found for module: ${module}`);
            }

            const mergedMessages = {
                en: { ...generalMessages.en, ...moduleMessages.en },
                ar: { ...generalMessages.ar, ...moduleMessages.ar }
            };

            i18n.global.setLocaleMessage('en', mergedMessages.en);
            i18n.global.setLocaleMessage('ar', mergedMessages.ar);

            i18n.global.locale.value = 'en';
        }

        // Load page-specific Module Component
        return resolvePageComponent(
            module
                ? `/Modules/${module}/resources/js/pages/${page}.vue`
                : `/resources/js/pages/${page}.vue`,
            import.meta.glob<DefineComponent>('/{Modules/**/resources/js/pages,resources/js/pages}/**/*.vue')
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
