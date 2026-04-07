// utils/i18n.ts
import i18next from 'i18next';

/** Backend fields that may be a string or a locale map (e.g. { en, ar }). */
export function localizedString(
  value: string | Record<string, string> | null | undefined,
  locale: string
): string {
  if (value == null || value === '') return '';
  if (typeof value === 'string') return value;
  const l = locale || 'en';
  const short = l.split('-')[0] || 'en';
  const o = value as Record<string, string>;
  return o[l] || o[short] || o.en || o.ar || '';
}

// Export the direct translation function
export const t = (key: string, options?: any): string => {
  // If i18next is not initialized yet, return the key as fallback
  if (!i18next.isInitialized) {
    return typeof key === 'string' ? key : String(key);
  }
  return i18next.t(key, options);
};