import { createI18n } from 'vue-i18n';
import fr from './locales/fr.json';
import en from './locales/en.json';

const messages = {
    fr,
    en,
};

const browserLocale = () => {
    const stored = localStorage.getItem('locale');
    if (stored && messages[stored]) {
        return stored;
    }

    const navigatorLocale = navigator.language.split('-')[0];
    return messages[navigatorLocale] ? navigatorLocale : 'fr';
};

const i18n = createI18n({
    legacy: false,
    locale: browserLocale(),
    fallbackLocale: 'fr',
    messages,
    globalInjection: true,
});

export default i18n;
