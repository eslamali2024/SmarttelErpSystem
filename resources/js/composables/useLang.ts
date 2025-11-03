import { ref } from 'vue';

type Lang = 'ar' | 'en';

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') return;
    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStoredLang = (): Lang | null => {
    if (typeof window === 'undefined') return null;
    const stored = localStorage.getItem('lang');
    return stored === 'ar' || stored === 'en' ? stored : null;
};

const lang = ref<Lang>('en');

export function useLang() {
    if (typeof window !== 'undefined') {
        const savedLang = getStoredLang();
        if (savedLang) {
            lang.value = savedLang;
        }
    }

    function updateLang(value: Lang) {
        lang.value = value;
        localStorage.setItem('lang', value);
        setCookie('lang', value);
    }

    return {
        lang,
        updateLang,
    };
}
