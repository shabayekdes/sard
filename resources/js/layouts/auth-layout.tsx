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
    status,
    statusType = 'success',
    leftImageSrc,
    leftImageAlt,
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

    return (
        <div className="flex min-h-svh w-full bg-slate-50 dark:bg-slate-900 lg:h-screen lg:overflow-hidden">
            <Head title={title} />

            {/* Right side - Content. On mobile: no overflow so country dropdown isn't clipped; page scrolls. On lg: centered panel. */}
            <div className="relative z-10 flex min-h-svh w-full flex-col items-center justify-center bg-white px-4 py-6 sm:px-6 sm:py-8 lg:min-h-0 lg:w-1/2 lg:flex-1 lg:py-[60px] lg:px-[62px] dark:bg-slate-900">
                <div
                    className={`flex w-full max-w-[720px] flex-col transition-all duration-700 lg:flex-1 lg:min-h-0 ${mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'}`}
                >
                    <div className="flex flex-1 flex-col rounded-none border-0 bg-white p-4 shadow-none sm:rounded-2xl sm:border sm:border-slate-200 sm:p-6 sm:shadow-xl lg:rounded-2xl lg:border lg:border-slate-200 lg:p-8 lg:shadow-xl dark:bg-slate-800 dark:sm:border-slate-700 dark:lg:border-slate-700">
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
