import CookieConsentBanner from '@/components/CookieConsentBanner';
import { LanguageSwitcher } from '@/components/language-switcher';
import { useBrand } from '@/contexts/BrandContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import { Head, usePage } from '@inertiajs/react';
import { CreditCard } from 'lucide-react';
import React, { ReactNode, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

interface AuthLayoutProps {
    children: ReactNode;
    title: string;
    description?: string;
    icon?: ReactNode;
    status?: string;
    statusType?: 'success' | 'error';
    leftContent?: ReactNode;
    leftImageSrc?: string;
    leftImageAlt?: string;
    showHeader?: boolean;
    showLanguageSwitcher?: boolean;
    cardClassName?: string;
    contentClassName?: string;
}

export default function AuthLayout({
    children,
    title,
    description,
    icon,
    status,
    statusType = 'success',
    leftImageSrc,
    leftImageAlt,
    showHeader = true,
    showLanguageSwitcher = true,
    contentClassName,
}: AuthLayoutProps) {
    const { t } = useTranslation();
    const [mounted, setMounted] = useState(false);
    const { logoLight, logoDark, themeColor, customColor } = useBrand();
    const { appearance } = useAppearance();
    const { props } = usePage();
    const globalSettings = (props as any).globalSettings;

    const currentLogo = appearance === 'dark' ? logoDark : logoLight;
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];

    useEffect(() => {
        setMounted(true);
    }, []);

    // RTL Support for landing page
    React.useEffect(() => {
        const isDemo = globalSettings?.is_demo || false;
        let storedPosition = 'left';
        if (isDemo) {
            // In demo mode, use cookies
            const getCookie = (name: string): string | null => {
                if (typeof document === 'undefined') return null;
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) {
                    const cookieValue = parts.pop()?.split(';').shift();
                    return cookieValue ? decodeURIComponent(cookieValue) : null;
                }
                return null;
            };
            const stored = getCookie('layoutPosition');
            if (stored === 'left' || stored === 'right') {
                storedPosition = stored;
            }
        } else {
            // In normal mode, get from database via globalSettings
            const stored = globalSettings?.layoutDirection;
            if (stored === 'left' || stored === 'right') {
                storedPosition = stored;
            }
        }
        const dir = storedPosition === 'right' ? 'rtl' : 'ltr';
        document.documentElement.dir = dir;
        document.documentElement.setAttribute('dir', dir);
        // Check if it was actually set
        setTimeout(() => {
            const actualDir = document.documentElement.getAttribute('dir');
            if (actualDir !== dir) {
                document.documentElement.dir = dir;
                document.documentElement.setAttribute('dir', dir);
            }
        }, 1);
    }, []);

    return (
        <div className="flex h-screen w-full overflow-hidden bg-slate-50 dark:bg-slate-900">
            <Head title={title} />

            {/* Right side - Content */}
            <div className="relative flex w-full items-center justify-center bg-white px-[62px] py-[60px] lg:w-1/2 dark:bg-slate-900">
                {/* Language Switcher - Top Right */}
                {showLanguageSwitcher && (
                    <div className="absolute top-4 right-4">
                        <LanguageSwitcher />
                    </div>
                )}

                <div
                    className={`w-full transition-all duration-700 ${mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'} ${
                        contentClassName || ''
                    }`}
                >
                    {/* Mobile branding - only visible on small screens */}
                    <div className="mb-8 flex flex-col items-center lg:hidden">
                        <div className="mb-4 inline-flex rounded-xl p-4 shadow-lg" style={{ backgroundColor: primaryColor }}>
                            {currentLogo ? (
                                <img src={currentLogo} alt="Logo" className="h-8 w-8 object-contain" />
                            ) : (
                                <CreditCard className="h-8 w-8 text-white" />
                            )}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-700 dark:bg-slate-800">
                        {showHeader && (icon || title || description) && (
                            <div className="mb-6 text-center">
                                {icon && (
                                    <div
                                        className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full"
                                        style={{ backgroundColor: `${primaryColor}20` }}
                                    >
                                        {icon}
                                    </div>
                                )}
                                {title && <h1 className="mb-2 text-2xl font-bold text-slate-900 dark:text-white">{title}</h1>}
                                {description && <p className="text-slate-600 dark:text-slate-400">{description}</p>}
                            </div>
                        )}

                        {status && (
                            <div
                                className={`mb-6 text-center text-sm font-medium ${
                                    statusType === 'success'
                                        ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-800/30 dark:bg-green-900/20 dark:text-green-400'
                                        : 'border-red-200 bg-red-50 text-red-700 dark:border-red-800/30 dark:bg-red-900/20 dark:text-red-400'
                                } rounded-lg border p-3`}
                            >
                                {status}
                            </div>
                        )}

                        {children}
                    </div>
                </div>
            </div>

            <div className="relative hidden overflow-hidden bg-white lg:block lg:w-1/2">
                <img src={leftImageSrc} alt={leftImageAlt || t('Authentication illustration')} className="h-full w-full object-cover" />
            </div>

            <CookieConsentBanner />
        </div>
    );
}
