import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

import AuthButton from '@/components/auth/auth-button';
import TextLink from '@/components/text-link';
import { LanguageSwitcher } from '@/components/language-switcher';
import { useBrand } from '@/contexts/BrandContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth-layout';
import { useTranslation } from 'react-i18next';
import { toast } from '@/components/custom-toast';

export default function VerifyEmail({ status }: { status?: string }) {
    const { t } = useTranslation();
    const { themeColor, customColor, logoLight, logoDark } = useBrand();
    const { appearance } = useAppearance();
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];
    const currentLogo = appearance === 'light' ? logoDark : logoLight;
    const { post, processing } = useForm({});
    const statusMessage = status === 'verification-link-sent'
        ? t('A new verification link has been sent to the email address you provided during registration.')
        : undefined;

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('verification.send'), {
            onSuccess: () => {
                toast.success(t('A new verification link has been sent to your email address.'));
            },
            onError: () => {
                toast.error(t('Failed to send verification email. Please try again.'));
            },
        });
    };

    return (
        <AuthLayout title={t('Verify your email')} leftImageSrc="/images/sign-in.jpeg" status={statusMessage}>
            <form className="space-y-5" onSubmit={submit}>
                <div className="flex items-center justify-between">
                    {currentLogo && <img src={currentLogo} alt={t('SARD')} className="h-8 object-contain" />}
                    <div className="rounded-md border border-slate-200 bg-white px-2">
                        <LanguageSwitcher />
                    </div>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                    {t('Please verify your email address by clicking on the link we just emailed to you.')}
                </p>
                <AuthButton processing={processing}>
                    {t('Resend verification email')}
                </AuthButton>
                <div className="text-center">
                    <TextLink
                        href={route('logout')}
                        method="post"
                        className="font-medium transition-colors duration-200"
                        style={{ color: primaryColor }}
                    >
                        {t('Log out')}
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}