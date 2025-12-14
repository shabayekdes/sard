import { getBrandSettings, type BrandSettings } from '@/utils/brandSettings';
import { getCookie } from '@/utils/cookies';
import { getBaseUrl, getImagePath } from '@/utils/helpers';
import { createContext, ReactNode, useContext, useEffect, useMemo, useState } from 'react';

interface BrandContextType extends BrandSettings {
    updateBrandSettings: (settings: Partial<BrandSettings>) => void;
}

const BrandContext = createContext<BrandContextType | undefined>(undefined);

export function BrandProvider({ children, globalSettings, user }: { children: ReactNode; globalSettings?: any; user?: any }) {
    const baseUrl = getBaseUrl(globalSettings?.app_url || globalSettings?.url); // adjust key if needed

    const getEffectiveSettings = () => {
        const isDemo = globalSettings?.is_demo || false;

        if (isDemo) return null;

        const path = typeof window !== 'undefined' ? window.location.pathname : '';

        const isPublicRoute = path.includes('/public/') || path === '/' || path.includes('/auth/');

        if (isPublicRoute) return globalSettings;

        if (user?.role === 'company' && user?.globalSettings) {
            return user.globalSettings;
        }

        return globalSettings;
    };

    // ✅ keep initializer light and pure
    const [brandSettings, setBrandSettings] = useState<BrandSettings>(() => {
        return getBrandSettings(getEffectiveSettings(), globalSettings);
    });

    // ✅ whenever settings change, normalize logos with baseUrl (still pure)
    useEffect(() => {
        const isDemo = globalSettings?.is_demo || false;

        setBrandSettings((prev) => ({
            ...prev,
            logoDark: getImagePath(prev.logoDark || (isDemo ? 'images/logos/logo-dark.png' : ''), baseUrl),
            logoLight: getImagePath(prev.logoLight || (isDemo ? 'images/logos/logo-light.png' : ''), baseUrl),
            favicon: getImagePath(prev.favicon || (isDemo ? 'images/logos/favicon.ico' : ''), baseUrl),
        }));
    }, [baseUrl, globalSettings?.is_demo]);

    // Apply theme + direction
    useEffect(() => {
        if (typeof document === 'undefined') return;

        const color =
            brandSettings.themeColor === 'custom'
                ? brandSettings.customColor
                : { blue: '#3b82f6', green: '#10b981', purple: '#8b5cf6', orange: '#f97316', red: '#ef4444' }[brandSettings.themeColor] || '#3b82f6';

        document.documentElement.style.setProperty('--theme-color', color);
        document.documentElement.style.setProperty('--primary', color);
        document.documentElement.style.setProperty('--chart-1', color);

        const isDark =
            brandSettings.themeMode === 'dark' ||
            (brandSettings.themeMode === 'system' && typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', isDark);
        document.body.classList.toggle('dark', isDark);

        const domDirection =
            brandSettings.layoutDirection === 'right' ? 'rtl' : brandSettings.layoutDirection === 'left' ? 'ltr' : brandSettings.layoutDirection;

        document.documentElement.dir = domDirection;
        document.documentElement.setAttribute('dir', domDirection);
    }, [brandSettings]);

    // Demo mode: apply layout direction from cookie on initial load (exclude landing page)
    useEffect(() => {
        if (!globalSettings?.is_demo) return;
        if (typeof window === 'undefined') return;

        const isLandingPage = window.location.pathname === '/';
        if (isLandingPage) return;

        const layoutPosition = getCookie('layoutPosition');
        if (layoutPosition) {
            const direction = layoutPosition === 'right' ? 'rtl' : 'ltr';
            document.documentElement.dir = direction;
            document.documentElement.setAttribute('dir', direction);
        }
    }, [globalSettings?.is_demo]);

    // Listen for changes in settings
    useEffect(() => {
        const effectiveSettings = getEffectiveSettings();
        const updatedSettings = getBrandSettings(effectiveSettings, globalSettings);
        setBrandSettings(updatedSettings);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [globalSettings, user]);

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
    if (context === undefined) {
        throw new Error('useBrand must be used within a BrandProvider');
    }
    return context;
}
