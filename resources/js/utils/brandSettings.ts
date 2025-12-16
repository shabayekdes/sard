import type { LayoutPosition } from '@/contexts/LayoutContext';
import type { Appearance, ThemeColor } from '@/hooks/use-appearance';
import { getCookie } from '@/utils/cookies';

export interface BrandSettings {
    logoDark: string;
    logoLight: string;
    favicon: string;
    titleText: string;
    footerText: string;
    themeColor: ThemeColor;
    customColor: string;
    sidebarVariant: string;
    sidebarStyle: string;
    layoutDirection: LayoutPosition;
    themeMode: Appearance;
}

export type BrandSettingsInput = Partial<{
    logoDark: string;
    logoLight: string;
    favicon: string;
    titleText: string;
    footerText: string;
    themeColor: ThemeColor;
    customColor: string;
    sidebarVariant: string;
    sidebarStyle: string;
    layoutDirection: LayoutPosition;
    themeMode: Appearance;
}>;

type ThemeCookie = Partial<{ themeColor: ThemeColor; customColor: string; appearance: Appearance }>;
type SidebarCookie = Partial<{ variant: string; style: string }>;
type BrandCookie = Partial<{ logoDark: string; logoLight: string; favicon: string; titleText: string; footerText: string }>;

export const DEFAULT_BRAND_SETTINGS: BrandSettings = {
    logoDark: '/images/logos/logo-dark.png',
    logoLight: '/images/logos/logo-light.png',
    favicon: '/images/logos/favicon.ico',
    titleText: 'WorkDo',
    footerText: '© 2024 WorkDo. All rights reserved.',
    themeColor: 'green',
    customColor: '#10b981',
    sidebarVariant: 'inset',
    sidebarStyle: 'plain',
    layoutDirection: 'left',
    themeMode: 'light',
};

export const getBrandSettings = (userSettings?: BrandSettingsInput | null, globalSettings?: { is_demo?: boolean } | null): BrandSettings => {
    const isDemo = !!globalSettings?.is_demo;

    if (isDemo) {
        try {
            const themeSettings = getCookie('themeSettings');
            const sidebarSettings = getCookie('sidebarSettings');
            const layoutPosition = getCookie('layoutPosition') as LayoutPosition | null;
            const brandSettings = getCookie('brandSettings');

            const parsedTheme: ThemeCookie = themeSettings ? JSON.parse(themeSettings) : {};
            const parsedSidebar: SidebarCookie = sidebarSettings ? JSON.parse(sidebarSettings) : {};
            const parsedBrand: BrandCookie = brandSettings ? JSON.parse(brandSettings) : {};

            return {
                logoDark: parsedBrand.logoDark || userSettings?.logoDark || DEFAULT_BRAND_SETTINGS.logoDark,
                logoLight: parsedBrand.logoLight || userSettings?.logoLight || DEFAULT_BRAND_SETTINGS.logoLight,
                favicon: parsedBrand.favicon || userSettings?.favicon || DEFAULT_BRAND_SETTINGS.favicon,
                titleText: parsedBrand.titleText || userSettings?.titleText || DEFAULT_BRAND_SETTINGS.titleText,
                footerText: parsedBrand.footerText || userSettings?.footerText || DEFAULT_BRAND_SETTINGS.footerText,
                themeColor: parsedTheme.themeColor || userSettings?.themeColor || DEFAULT_BRAND_SETTINGS.themeColor,
                customColor: parsedTheme.customColor || userSettings?.customColor || DEFAULT_BRAND_SETTINGS.customColor,
                sidebarVariant: parsedSidebar.variant || userSettings?.sidebarVariant || DEFAULT_BRAND_SETTINGS.sidebarVariant,
                sidebarStyle: parsedSidebar.style || userSettings?.sidebarStyle || DEFAULT_BRAND_SETTINGS.sidebarStyle,
                layoutDirection: layoutPosition || userSettings?.layoutDirection || DEFAULT_BRAND_SETTINGS.layoutDirection,
                themeMode: parsedTheme.appearance || userSettings?.themeMode || DEFAULT_BRAND_SETTINGS.themeMode,
            };
        } catch {
            // fall through to normal mode/defaults
        }
    }

    if (userSettings) {
        return {
            logoDark: userSettings.logoDark || DEFAULT_BRAND_SETTINGS.logoDark,
            logoLight: userSettings.logoLight || DEFAULT_BRAND_SETTINGS.logoLight,
            favicon: userSettings.favicon || DEFAULT_BRAND_SETTINGS.favicon,
            titleText: userSettings.titleText || DEFAULT_BRAND_SETTINGS.titleText,
            footerText: userSettings.footerText || DEFAULT_BRAND_SETTINGS.footerText,
            themeColor: userSettings.themeColor || DEFAULT_BRAND_SETTINGS.themeColor,
            customColor: userSettings.customColor || DEFAULT_BRAND_SETTINGS.customColor,
            sidebarVariant: userSettings.sidebarVariant || DEFAULT_BRAND_SETTINGS.sidebarVariant,
            sidebarStyle: userSettings.sidebarStyle || DEFAULT_BRAND_SETTINGS.sidebarStyle,
            layoutDirection: userSettings.layoutDirection || DEFAULT_BRAND_SETTINGS.layoutDirection,
            themeMode: userSettings.themeMode || DEFAULT_BRAND_SETTINGS.themeMode,
        };
    }

    return DEFAULT_BRAND_SETTINGS;
};
