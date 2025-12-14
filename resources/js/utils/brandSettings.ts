import type { Appearance, ThemeColor } from "@/hooks/use-appearance";
import type { LayoutPosition } from "@/contexts/LayoutContext";
import { getCookie } from "@/utils/cookies";

// Define the brand settings interface
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

// Default brand settings
export const DEFAULT_BRAND_SETTINGS: BrandSettings = {
    logoDark: "/images/logos/logo-dark.png",
    logoLight: "/images/logos/logo-light.png",
    favicon: "/images/logos/favicon.ico",
    titleText: "WorkDo",
    footerText: "© 2024 WorkDo. All rights reserved.",
    themeColor: "green",
    customColor: "#10b981",
    sidebarVariant: "inset",
    sidebarStyle: "plain",
    layoutDirection: "left",
    themeMode: "light",
};

// Get brand settings from props or cookies/localStorage as fallback
export const getBrandSettings = (
    userSettings?: Record<string, any> | null,
    globalSettings?: any
): BrandSettings => {
    const isDemo = globalSettings?.is_demo || false;

    // Demo mode: prioritize cookies
    if (isDemo) {
        try {
            const themeSettings = getCookie("themeSettings");
            const sidebarSettings = getCookie("sidebarSettings");
            const layoutPosition = getCookie("layoutPosition");
            const brandSettings = getCookie("brandSettings");

            const parsedTheme = themeSettings ? JSON.parse(themeSettings) : {};
            const parsedSidebar = sidebarSettings ? JSON.parse(sidebarSettings) : {};
            const parsedBrand = brandSettings ? JSON.parse(brandSettings) : {};

            return {
                logoDark: parsedBrand.logoDark || userSettings?.logoDark || DEFAULT_BRAND_SETTINGS.logoDark,
                logoLight: parsedBrand.logoLight || userSettings?.logoLight || DEFAULT_BRAND_SETTINGS.logoLight,
                favicon: parsedBrand.favicon || userSettings?.favicon || DEFAULT_BRAND_SETTINGS.favicon,
                titleText: parsedBrand.titleText || userSettings?.titleText || DEFAULT_BRAND_SETTINGS.titleText,
                footerText: parsedBrand.footerText || userSettings?.footerText || DEFAULT_BRAND_SETTINGS.footerText,
                themeColor: parsedTheme.themeColor || (userSettings?.themeColor as ThemeColor) || DEFAULT_BRAND_SETTINGS.themeColor,
                customColor: parsedTheme.customColor || userSettings?.customColor || DEFAULT_BRAND_SETTINGS.customColor,
                sidebarVariant: parsedSidebar.variant || userSettings?.sidebarVariant || DEFAULT_BRAND_SETTINGS.sidebarVariant,
                sidebarStyle: parsedSidebar.style || userSettings?.sidebarStyle || DEFAULT_BRAND_SETTINGS.sidebarStyle,
                layoutDirection: layoutPosition || (userSettings?.layoutDirection as LayoutPosition) || DEFAULT_BRAND_SETTINGS.layoutDirection,
                themeMode: parsedTheme.appearance || (userSettings?.themeMode as Appearance) || DEFAULT_BRAND_SETTINGS.themeMode,
            };
        } catch {
            // fall through
        }
    }

    // Normal mode: backend settings
    if (userSettings) {
        return {
            logoDark: userSettings.logoDark || DEFAULT_BRAND_SETTINGS.logoDark,
            logoLight: userSettings.logoLight || DEFAULT_BRAND_SETTINGS.logoLight,
            favicon: userSettings.favicon || DEFAULT_BRAND_SETTINGS.favicon,
            titleText: userSettings.titleText || DEFAULT_BRAND_SETTINGS.titleText,
            footerText: userSettings.footerText || DEFAULT_BRAND_SETTINGS.footerText,
            themeColor: (userSettings.themeColor as ThemeColor) || DEFAULT_BRAND_SETTINGS.themeColor,
            customColor: userSettings.customColor || DEFAULT_BRAND_SETTINGS.customColor,
            sidebarVariant: userSettings.sidebarVariant || DEFAULT_BRAND_SETTINGS.sidebarVariant,
            sidebarStyle: userSettings.sidebarStyle || DEFAULT_BRAND_SETTINGS.sidebarStyle,
            layoutDirection: (userSettings.layoutDirection as LayoutPosition) || DEFAULT_BRAND_SETTINGS.layoutDirection,
            themeMode: (userSettings.themeMode as Appearance) || DEFAULT_BRAND_SETTINGS.themeMode,
        };
    }

    return DEFAULT_BRAND_SETTINGS;
};
