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
        <div className="flex h-screen w-full overflow-hidden bg-slate-50 dark:bg-slate-900">
            <Head title={title} />

            {/* Right side - Content - z-10 so phone country dropdown appears above the left image */}
            <div className="relative z-10 flex w-full items-center justify-center bg-white px-[62px] py-[60px] lg:w-1/2 dark:bg-slate-900">
                <div
                    className={`w-full max-w-[720px] transition-all duration-700 ${mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'}`}
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
