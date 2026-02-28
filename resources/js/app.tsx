import { createInertiaApp, router, usePage } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import React, { StrictMode, Suspense } from 'react';
import { createRoot } from 'react-dom/client';

import '../css/app.css';
import '../css/dark-mode.css';
import { initializeTheme } from '@/hooks/use-appearance';
import { CustomToast } from '@/components/custom-toast';
import { GlobalLoaderOverlay } from '@/components/global-loader-overlay';
import { LayoutProvider } from '@/contexts/LayoutContext';
import { SidebarProvider } from '@/contexts/SidebarContext';
import { BrandProvider } from '@/contexts/BrandContext';
import { ModalStackProvider } from '@/contexts/ModalStackContext';
import { initializeGlobalSettings } from '@/utils/globalSettings';
import { initPerformanceMonitoring, lazyLoadImages } from '@/utils/performance';
import { getCookie, isDemoMode } from '@/utils/cookies';
import { installSameOriginRoute } from '@/utils/route-same-origin';

import './i18n';
import './utils/axios-config';
import i18n from './i18n';

initPerformanceMonitoring();

// Dev-only: avoid crash when React tries to remove a portal node that was already removed (Radix/React known issue)
if (import.meta.env.DEV && typeof Node !== 'undefined' && Node.prototype.removeChild) {
    const originalRemoveChild = Node.prototype.removeChild;
    (Node.prototype as any).removeChild = function (child: Node) {
        try {
            return originalRemoveChild.call(this, child);
        } catch (e: unknown) {
            const err = e as { name?: string; message?: string };
            if (err?.name === 'NotFoundError' && err?.message?.includes('removeChild')) {
                return child;
            }
            throw e;
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    lazyLoadImages();
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (!isDemoMode()) return;
    const savedTheme = getCookie('themeSettings');
    if (savedTheme) {
        const themeSettings = JSON.parse(savedTheme);
        if (themeSettings.appearance === 'system') initializeTheme();
    }
});

function LayoutKeyWrapper({ children }: { children: React.ReactNode }) {
    const { url } = usePage();
    return <React.Fragment key={url}>{children}</React.Fragment>;
}

function normalizeZiggyUrl(page: { props?: Record<string, unknown> } | undefined) {
    const origin = typeof window !== 'undefined' ? window.location.origin : '';
    try {
        const ziggy = page?.props?.ziggy;
        if (ziggy && typeof ziggy === 'object') {
            (ziggy as Record<string, string>).url = origin;
        }
        if (typeof (globalThis as any).Ziggy !== 'undefined' && (globalThis as any).Ziggy?.url !== origin) {
            (globalThis as any).Ziggy.url = origin;
        }
    } catch {
        // ignore
    }
}

function initializeDirection() {
    const currentLang = i18n.language || (window as any).initialLocale || getCookie('app_language');
    let domDirection: string;
    if (currentLang && ['ar', 'he'].includes(currentLang)) {
        domDirection = 'rtl';
    } else {
        let savedDirection: string | null = null;
        if (isDemoMode()) {
            savedDirection = getCookie('layoutDirection');
        } else {
            const globalSettings = (window as any).page?.props?.globalSettings;
            if (globalSettings?.layoutDirection) savedDirection = globalSettings.layoutDirection;
        }
        const d = savedDirection;
        domDirection = d === 'right' ? 'rtl' : d === 'left' ? 'ltr' : d || 'ltr';
    }
    document.documentElement.dir = domDirection;
    document.documentElement.setAttribute('dir', domDirection);
}

(window as any).updateLayoutDirection = (lng: string) => {
    const direction = ['ar', 'he'].includes(lng) ? 'rtl' : 'ltr';
    document.documentElement.dir = direction;
    document.documentElement.setAttribute('dir', direction);
};

const originalChangeLanguage = i18n.changeLanguage;
i18n.changeLanguage = function (lng: any, callback?: any) {
    const result = originalChangeLanguage.call(this, lng, callback);
    (window as any).updateLayoutDirection(lng);
    return result;
};
i18n.on('languageChanged', (window as any).updateLayoutDirection);
i18n.on('loaded', () => (window as any).updateLayoutDirection(i18n.language));

const appName = import.meta.env.VITE_APP_NAME || 'Sard App';

createInertiaApp({
    title: (title) => (title ? (title.includes(' - ') ? title : `${title} - ${appName}`) : appName),
    resolve: (name) =>
        resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')).then((module: any) => {
            const Page = module.default;
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
                                                        <GlobalLoaderOverlay />
                                                    </BrandProvider>
                                                </SidebarProvider>
                                            </LayoutProvider>
                                        </ModalStackProvider>
                                    </LayoutKeyWrapper>
                                ));
            return module;
        }),
    setup({ el, App, props }) {
        installSameOriginRoute();
        const root = createRoot(el);

        try {
            (window as any).page = props.initialPage;
            normalizeZiggyUrl(props.initialPage);
        } catch (e) {
            console.warn('Could not set global page data:', e);
        }
        try {
            (window as any).isDemo = props.initialPage.props?.is_demo ?? false;
        } catch {
            // ignore
        }
        (window as any).__isCentralDomain = (props.initialPage?.props as Record<string, unknown>)?.isCentralDomain === true;
        initializeDirection();
        const globalSettings = props.initialPage.props?.globalSettings ?? {};
        if (Object.keys(globalSettings).length > 0) {
            initializeGlobalSettings(globalSettings);
        }

        if (import.meta.env.DEV) {
            const RELOAD_KEY = 'inertia_removechild_reload';
            window.addEventListener('error', (event) => {
                const err = event.error;
                if (
                    err?.name === 'NotFoundError' &&
                    typeof err?.message === 'string' &&
                    err.message.includes('removeChild')
                ) {
                    if (!sessionStorage.getItem(RELOAD_KEY)) {
                        sessionStorage.setItem(RELOAD_KEY, '1');
                        event.preventDefault();
                        event.stopPropagation();
                        window.location.reload();
                    }
                }
            });
        }

        root.render(
            <StrictMode>
                <Suspense fallback={<div className="flex h-screen w-full items-center justify-center">Loading...</div>}>
                    <App {...props} />
                </Suspense>
            </StrictMode>,
        );

        router.on('navigate', (event: { detail: { page?: typeof props.initialPage } }) => {
            const page = event.detail?.page;
            if (!page) return;
            (window as any).__isCentralDomain = (page?.props as Record<string, unknown>)?.isCentralDomain === true;
            try {
                (window as any).page = page;
                normalizeZiggyUrl(page);
                const nextGlobalSettings = page?.props?.globalSettings ?? {};
                if (Object.keys(nextGlobalSettings).length > 0) {
                    initializeGlobalSettings(nextGlobalSettings);
                }
                const savedTheme = isDemoMode() ? getCookie('themeSettings') : null;
                if (savedTheme) {
                    const themeSettings = JSON.parse(savedTheme);
                    const isDark =
                        themeSettings.appearance === 'dark' ||
                        (themeSettings.appearance === 'system' &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);
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

initializeTheme();
initializeDirection();
