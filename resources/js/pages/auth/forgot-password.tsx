import { useForm } from '@inertiajs/react';
import { Mail } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

import AuthButton from '@/components/auth/auth-button';
import InputError from '@/components/input-error';
import { LanguageSwitcher } from '@/components/language-switcher';
import Recaptcha from '@/components/recaptcha';
import TextLink from '@/components/text-link';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout } from '@/contexts/LayoutContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth-layout';
import { useTranslation } from 'react-i18next';

export default function ForgotPassword({ status }: { status?: string }) {
    const { t } = useTranslation();
    const { position } = useLayout();

    const [recaptchaToken, setRecaptchaToken] = useState<string>('');
    const { themeColor, customColor, logoLight, logoDark } = useBrand();
    const { appearance } = useAppearance();
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];
    const currentLogo = appearance === 'light' ? logoDark : logoLight;

    const { data, setData, post, processing, errors, transform } = useForm<{ email: string }>({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        transform((formData) => ({ ...formData, recaptcha_token: recaptchaToken }));
        post(route('password.email'));
    };

    return (
        <AuthLayout
            title={t('Reset your password')}
            leftImageSrc="/images/sign-in.jpeg"
            status={status}
        >
            <form className="space-y-5" onSubmit={submit}>
                <div className="flex items-center justify-between">
                    {currentLogo && <img src={currentLogo} alt={t('SARD')} className="h-8 object-contain" />}
                    <div className="rounded-md border border-slate-200 bg-white px-2">
                        <LanguageSwitcher />
                    </div>
                </div>
                <div className="space-y-4">
                    <div className="relative">
                        <Label htmlFor="email" className="mb-3 block font-medium text-gray-700 dark:text-gray-300">
                            {t('Email address')}
                        </Label>
                        <div className="relative">
                            <div
                                className={`pointer-events-none absolute inset-y-0 z-10 flex items-center px-3 ${position === 'right' ? 'right-0' : 'left-0'}`}
                            >
                                <Mail className="h-5 w-5 text-gray-400" />
                            </div>
                            <Input
                                id="email"
                                type="email"
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="email@example.com"
                                className="w-full rounded-lg border-gray-300 bg-white pr-12 pl-10 transition-all duration-200 dark:border-gray-600 dark:bg-gray-700"
                                style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                            />
                        </div>
                        <InputError message={errors.email} />
                    </div>
                </div>

                <Recaptcha
                    onVerify={setRecaptchaToken}
                    onExpired={() => setRecaptchaToken('')}
                    onError={() => setRecaptchaToken('')}
                />

                <AuthButton tabIndex={2} processing={processing}>
                    {t('Email password reset link')}
                </AuthButton>

                <div className="text-center text-sm text-gray-600 dark:text-gray-400">
                    {t('Remember your password?')}{' '}
                    <TextLink
                        href={route('login')}
                        className="font-medium transition-colors duration-200"
                        style={{ color: primaryColor }}
                        tabIndex={3}
                    >
                        {t('Back to login')}
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}
