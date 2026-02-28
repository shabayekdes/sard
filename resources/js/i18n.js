// resources/js/i18n.js
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

import en from '@lang/en.json';
import ar from '@lang/ar.json';

// Make i18n instance available for direct imports
export { default as i18next } from 'i18next';

// Apply layout direction and persist locale from current language (no API).
// Deferred to avoid running during React commit (prevents Radix Portal removeChild errors).
function normalizeLocaleForCookie(locale) {
  if (!locale || typeof locale !== 'string') return 'en';
  const base = locale.split('-')[0];
  return base === 'ar' || base === 'he' ? base : 'en';
}

function applyDirectionForLocale(locale) {
  if (typeof document === 'undefined' || !locale) return;
  const base = locale.split('-')[0];
  const layoutDirection = base === 'ar' || base === 'he' ? 'right' : 'left';
  const domDirection = layoutDirection === 'right' ? 'rtl' : 'ltr';
  const localeForCookie = normalizeLocaleForCookie(locale);
  const maxAge = 60 * 60 * 24 * 30;
  document.cookie = `app_direction=${layoutDirection}; path=/; max-age=${maxAge}; SameSite=Lax`;
  document.cookie = `app_language=${localeForCookie}; path=/; max-age=${maxAge}; SameSite=Lax`;
  const run = () => {
    document.documentElement.dir = domDirection;
    document.documentElement.setAttribute('dir', domDirection);
    if (typeof localStorage !== 'undefined') localStorage.setItem('layoutDirection', layoutDirection);
    document.documentElement.classList.add('direction-changed');
    setTimeout(() => document.documentElement.classList.remove('direction-changed'), 100);
  };
  if (typeof requestAnimationFrame !== 'undefined') {
    requestAnimationFrame(run);
  } else {
    setTimeout(run, 0);
  }
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
