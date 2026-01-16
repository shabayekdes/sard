import type { PageProps } from '@/types/page-props';
import { THEME_COLORS } from '@/hooks/use-appearance';
import { getBrandSettings, type BrandSettings } from '@/utils/brandSettings';
import { getBaseUrl, getImagePath } from '@/utils/helpers';
import { usePage } from '@inertiajs/react';
import { createContext, ReactNode, useContext, useEffect, useMemo, useState } from 'react';
import i18n from '../i18n';

interface BrandContextType extends BrandSettings {
    updateBrandSettings: (settings: Partial<BrandSettings>) => void;
}

const BrandContext = createContext<BrandContextType | undefined>(undefined);

export function BrandProvider({ children }: { children: ReactNode }) {
    const { props } = usePage<PageProps>();
    const globalSettings = props.globalSettings || {};
    const user = props.auth?.user;
    const baseUrl = getBaseUrl(globalSettings?.app_url || globalSettings?.url || props.base_url);

    const getEffectiveSettings = () => {
        const isDemo = globalSettings?.is_demo || props.is_demo || false;
        if (isDemo) return null;

        const path = typeof window !== 'undefined' ? window.location.pathname : '';
        const isPublicRoute = path.includes('/public/') || path === '/' || path.includes('/auth/');

        if (isPublicRoute) return globalSettings;
        if (user?.role === 'company' && user?.globalSettings) return user.globalSettings;

        return globalSettings;
    };

    const [brandSettings, setBrandSettings] = useState<BrandSettings>(() => {
        return getBrandSettings(getEffectiveSettings(), globalSettings);
    });

    useEffect(() => {
        const isDemo = globalSettings?.is_demo || props.is_demo || false;

        setBrandSettings((prev) => ({
            ...prev,
            logoDark: getImagePath(prev.logoDark || (isDemo ? 'images/logos/logo-dark.png' : ''), baseUrl),
            logoLight: getImagePath(prev.logoLight || (isDemo ? 'images/logos/logo-light.png' : ''), baseUrl),
            favicon: getImagePath(prev.favicon || (isDemo ? 'images/logos/favicon.ico' : ''), baseUrl),
        }));
    }, [baseUrl, globalSettings?.is_demo, props.is_demo]);

    useEffect(() => {
        if (typeof document === 'undefined') return;

        const color =
            brandSettings.themeColor === 'custom'
                ? brandSettings.customColor
                : THEME_COLORS[brandSettings.themeColor as keyof typeof THEME_COLORS] ||
                  THEME_COLORS.green;

        document.documentElement.style.setProperty('--theme-color', color);
        document.documentElement.style.setProperty('--primary', color);
        document.documentElement.style.setProperty('--chart-1', color);

        const isDark =
            brandSettings.themeMode === 'dark' ||
            (brandSettings.themeMode === 'system' && typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', isDark);
        document.body.classList.toggle('dark', isDark);

        // Determine direction: prioritize language code over stored layoutDirection
        let domDirection: string;
        const currentLang = i18n.language || (window as any).initialLocale;
        if (currentLang && ['ar', 'he'].includes(currentLang)) {
            // Language requires RTL - always set to RTL regardless of stored layoutDirection
            domDirection = 'rtl';
        } else if (brandSettings.layoutDirection === 'right') {
            domDirection = 'rtl';
        } else if (brandSettings.layoutDirection === 'left') {
            domDirection = 'ltr';
        } else {
            domDirection = brandSettings.layoutDirection || 'ltr';
        }

        document.documentElement.dir = domDirection;
        document.documentElement.setAttribute('dir', domDirection);
    }, [brandSettings]);

    useEffect(() => {
        // Whenever inertia props change (navigation), refresh settings
        const effectiveSettings = getEffectiveSettings();
        const updatedSettings = getBrandSettings(effectiveSettings, globalSettings);
        setBrandSettings(updatedSettings);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [globalSettings, user]);

    // Update direction when language changes
    useEffect(() => {
        const updateDirectionFromLanguage = () => {
            const currentLang = i18n.language || (window as any).initialLocale;
            if (currentLang && ['ar', 'he'].includes(currentLang)) {
                document.documentElement.dir = 'rtl';
                document.documentElement.setAttribute('dir', 'rtl');
            } else if (currentLang) {
                // Only set to LTR if we're sure it's not an RTL language
                const currentDir = document.documentElement.getAttribute('dir');
                if (currentDir === 'rtl' && !['ar', 'he'].includes(currentLang)) {
                    document.documentElement.dir = 'ltr';
                    document.documentElement.setAttribute('dir', 'ltr');
                }
            }
        };

        // Initial check
        updateDirectionFromLanguage();

        // Listen for language changes
        i18n.on('languageChanged', updateDirectionFromLanguage);
        i18n.on('loaded', updateDirectionFromLanguage);
        i18n.on('initialized', updateDirectionFromLanguage);

        return () => {
            i18n.off('languageChanged', updateDirectionFromLanguage);
            i18n.off('loaded', updateDirectionFromLanguage);
            i18n.off('initialized', updateDirectionFromLanguage);
        };
    }, []);

    const updateBrandSettings = (newSettings: Partial<BrandSettings>) => {
        const processedSettings = { ...newSettings };

        if (newSettings.logoDark) processedSettings.logoDark = getImagePath(newSettings.logoDark, baseUrl);
        if (newSettings.logoLight) processedSettings.logoLight = getImagePath(newSettings.logoLight, baseUrl);
        if (newSettings.favicon) processedSettings.favicon = getImagePath(newSettings.favicon, baseUrl);

        setBrandSettings((prev) => ({ ...prev, ...processedSettings }));
    };

    const value = useMemo(() => ({ ...brandSettings, updateBrandSettings }), [brandSettings]);

    return <BrandContext.Provider value={value}>{children}</BrandContext.Provider>;
}

export function useBrand() {
    const context = useContext(BrandContext);
    if (context === undefined) throw new Error('useBrand must be used within a BrandProvider');
    return context;
}
