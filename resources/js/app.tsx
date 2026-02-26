import '../css/app.css';
import '../css/dark-mode.css';

import { createInertiaApp, router, usePage } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import React, { Suspense } from 'react';

/** Keys the entire layout by URL so on navigation the full tree (including header/sidebar with Radix Portals) unmounts before the new page mounts. Prevents "removeChild" DOM errors from Portal reconciliation during Inertia navigation. */
function LayoutKeyWrapper({ children }: { children: React.ReactNode }) {
    const { url } = usePage();
    return (
        <React.Fragment key={url}>
            {children}
        </React.Fragment>
    );
}

import { LayoutProvider } from './contexts/LayoutContext';
import { SidebarProvider } from './contexts/SidebarContext';
import { BrandProvider } from './contexts/BrandContext';
import { ModalStackProvider } from './contexts/ModalStackContext';

import { initializeTheme } from './hooks/use-appearance';
import { CustomToast } from './components/custom-toast';
import { initializeGlobalSettings } from './utils/globalSettings';
import { initPerformanceMonitoring, lazyLoadImages } from './utils/performance';
import { getCookie, isDemoMode } from './utils/cookies';

import './i18n';
import './utils/axios-config';
import i18n from './i18n';

// -------------------------
// Perf + lazy images
// -------------------------
initPerformanceMonitoring();

// Ensure Ziggy base URL is current origin so route() generates same-origin URLs (avoids CORS when on tenant vs central domain)
function normalizeZiggyUrl(page: { props?: { ziggy?: Record<string, unknown> } } | undefined) {
    try {
        const ziggy = page?.props?.ziggy;
        if (ziggy && typeof ziggy === 'object') {
            (ziggy as Record<string, string>).url = window.location.origin;
        }
    } catch {
        // ignore
    }
}

document.addEventListener('DOMContentLoaded', () => {
    lazyLoadImages();
});

// Re-apply theme when system preference changes (demo mode only)
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    let savedTheme = null;

    if (isDemoMode()) {
        savedTheme = getCookie('themeSettings');
    }

    if (savedTheme) {
        const themeSettings = JSON.parse(savedTheme);
        if (themeSettings.appearance === 'system') {
            initializeTheme();
        }
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    // title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: (name) =>
        resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')).then((module: any) => {
            const Page = module.default;

            // Attach a default layout (only if page didn't define one)
            Page.layout =
                Page.layout ||
                ((page: React.ReactNode) => (
                    <LayoutKeyWrapper>
                        <ModalStackProvider>
                            <LayoutProvider>
                                <SidebarProvider>
                                    <BrandProvider>
                                        {page}
                                        <CustomToast />
                                    </BrandProvider>
                                </SidebarProvider>
                            </LayoutProvider>
                        </ModalStackProvider>
                    </LayoutKeyWrapper>
                ));

            return module;
        }),

    setup({ el, App, props }) {
        const root = createRoot(el);

        // Make initial page data globally available (if you still need it)
        try {
            (window as any).page = props.initialPage;
            normalizeZiggyUrl(props.initialPage);
        } catch (e) {
            console.warn('Could not set global page data:', e);
        }

        // Set demo mode globally
        try {
            (window as any).isDemo = props.initialPage.props?.is_demo || false;
        } catch {
            // ignore
        }

        // Initialize direction + global settings from initial shared props
        initializeDirection();

        const globalSettings = props.initialPage.props.globalSettings || {};
        if (Object.keys(globalSettings).length > 0) {
            initializeGlobalSettings(globalSettings);
        }

        // Render ONCE — Inertia handles updates on navigation
        root.render(
            <Suspense fallback={<div className="flex h-screen w-full items-center justify-center">Loading...</div>}>
                <App {...props} />
            </Suspense>
        );

        // Side effects on navigation (NO root.render)
        router.on('navigate', (event) => {
            try {
                (window as any).page = event.detail.page;
                normalizeZiggyUrl(event.detail.page);

                // Re-initialize global settings so currency/date settings reflect latest backend (e.g. after user changes settings)
                const nextGlobalSettings = event.detail.page?.props?.globalSettings || {};
                if (Object.keys(nextGlobalSettings).length > 0) {
                    initializeGlobalSettings(nextGlobalSettings);
                }

                // Optional: keep theme synced in demo mode
                const savedTheme = isDemoMode() ? getCookie('themeSettings') : null;
                if (savedTheme) {
                    const themeSettings = JSON.parse(savedTheme);
                    const isDark =
                        themeSettings.appearance === 'dark' ||
                        (themeSettings.appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

                    document.documentElement.classList.toggle('dark', isDark);
                    document.body.classList.toggle('dark', isDark);
                }
            } catch (e) {
                console.error('Navigation side-effect error:', e);
            }
        });
    },

    progress: {
        color: '#4B5563',
    },
});

// Set light/dark mode on load
initializeTheme();

// -------------------------
// Direction helpers
// -------------------------
const normalizeDirection = (direction: string | null | undefined) => {
    if (direction === 'right') return 'rtl';
    if (direction === 'left') return 'ltr';
    return direction || 'ltr';
};

const initializeDirection = () => {
    // First, check the current language to determine direction
    // This takes priority over stored settings
    const currentLang = i18n.language || (window as any).initialLocale || getCookie('app_language');
    let domDirection: string;

    if (currentLang && ['ar', 'he'].includes(currentLang)) {
        // Language requires RTL - always set to RTL regardless of stored settings
        domDirection = 'rtl';
    } else {
        // For non-RTL languages, check stored settings
        let savedDirection: string | null = null;

        if (isDemoMode()) {
            savedDirection = getCookie('layoutDirection');
        } else {
            const globalSettings = (window as any).page?.props?.globalSettings;
            if (globalSettings?.layoutDirection) {
                savedDirection = globalSettings.layoutDirection;
            }
        }

        domDirection = normalizeDirection(savedDirection);
    }

    document.documentElement.dir = domDirection;
    document.documentElement.setAttribute('dir', domDirection);
};

// Initialize direction immediately
initializeDirection();

// Global function to update direction
(window as any).updateLayoutDirection = (lng: string) => {
    const isRTL = ['ar', 'he'].includes(lng);
    const direction = isRTL ? 'rtl' : 'ltr';
    document.documentElement.dir = direction;
    document.documentElement.setAttribute('dir', direction);
};

// Override i18n changeLanguage to update direction immediately
const originalChangeLanguage = i18n.changeLanguage;
i18n.changeLanguage = function (lng: any, callback?: any) {
    const result = originalChangeLanguage.call(this, lng, callback);
    (window as any).updateLayoutDirection(lng);
    return result;
};

i18n.on('languageChanged', (window as any).updateLayoutDirection);
i18n.on('loaded', () => (window as any).updateLayoutDirection(i18n.language));
