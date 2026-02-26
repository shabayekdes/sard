// resources/js/i18n.js
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

import en from '../lang/en.json';
import ar from '../lang/ar.json';

// Make i18n instance available for direct imports
export { default as i18next } from 'i18next';

// Apply layout direction and persist locale from current language (no API)
function applyDirectionForLocale(locale) {
  if (typeof document === 'undefined') return;
  const layoutDirection = ['ar', 'he'].includes(locale) ? 'right' : 'left';
  const domDirection = layoutDirection === 'right' ? 'rtl' : 'ltr';
  document.documentElement.dir = domDirection;
  document.documentElement.setAttribute('dir', domDirection);
  if (typeof localStorage !== 'undefined') localStorage.setItem('layoutDirection', layoutDirection);
  document.cookie = `app_direction=${layoutDirection}; path=/; max-age=${60 * 60 * 24 * 30}`;
  document.cookie = `app_language=${locale}; path=/; max-age=${60 * 60 * 24}`;
  document.documentElement.classList.add('direction-changed');
  if (typeof window !== 'undefined') window.dispatchEvent(new Event('resize'));
  setTimeout(() => document.documentElement.classList.remove('direction-changed'), 100);
}

// Function to get initial language
const getInitialLanguage = () => {
  if (typeof window !== 'undefined' && window.initialLocale) {
    return window.initialLocale;
  }
  return null;
};

// Initialize i18n with bundled JSON only (no HTTP backend)
i18n
    .use(LanguageDetector)
    .use(initReactI18next)
    .init({
        lng: getInitialLanguage(),
        fallbackLng: 'en',
        debug: process.env.NODE_ENV === 'development',

        resources: {
            en: { translation: en },
            ar: { translation: ar },
        },

        interpolation: {
            escapeValue: false,
        },

        detection: {
            order: ['localStorage', 'cookie', 'navigator'],
            lookupCookie: 'app_language',
            caches: ['localStorage', 'cookie'],
        },

        ns: ['translation'],
        defaultNS: 'translation',
    });

// Apply direction when language is set (init and changes)
const applyDirectionOnLanguage = (lng) => {
  if (lng) applyDirectionForLocale(lng);
};
i18n.on('initialized', () => applyDirectionOnLanguage(i18n.language));
i18n.on('languageChanged', applyDirectionOnLanguage);

// Export the initialized instance
export default i18n;

if (typeof window !== 'undefined') {
  window.i18next = i18n;
}
