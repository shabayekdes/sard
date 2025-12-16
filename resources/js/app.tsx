import '../css/app.css';
import '../css/dark-mode.css';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import React, { Suspense } from 'react';

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
                    <ModalStackProvider>
                        <LayoutProvider>
                            <SidebarProvider>
                                {/* BrandProvider is now INSIDE Inertia context, so it can safely use usePage() */}
                                <BrandProvider>
                                    {page}
                                    <CustomToast />
                                </BrandProvider>
                            </SidebarProvider>
                        </LayoutProvider>
                    </ModalStackProvider>
                ));

            return module;
        }),

    setup({ el, App, props }) {
        const root = createRoot(el);

        // Make initial page data globally available (if you still need it)
        try {
            (window as any).page = props.initialPage;
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
    let savedDirection: string | null = null;

    if (isDemoMode()) {
        savedDirection = getCookie('layoutDirection');
    } else {
        const globalSettings = (window as any).page?.props?.globalSettings;
        if (globalSettings?.layoutDirection) {
            savedDirection = globalSettings.layoutDirection;
        }
    }

    const domDirection = normalizeDirection(savedDirection);
    document.documentElement.dir = domDirection;
    document.documentElement.setAttribute('dir', domDirection);
};

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
