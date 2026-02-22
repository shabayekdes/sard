import { useForm } from '@inertiajs/react';
import { Lock, Mail } from 'lucide-react';
import { FormEventHandler } from 'react';

import AuthButton from '@/components/auth/auth-button';
import InputError from '@/components/input-error';
import { LanguageSwitcher } from '@/components/language-switcher';
import TextLink from '@/components/text-link';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout } from '@/contexts/LayoutContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth-layout';
import { useTranslation } from 'react-i18next';

interface ResetPasswordProps {
    token: string;
    email: string;
}

type ResetPasswordForm = {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export default function ResetPassword({ token, email }: ResetPasswordProps) {
    const { t } = useTranslation();
    const { position } = useLayout();
    const { themeColor, customColor, logoLight, logoDark } = useBrand();
    const { appearance } = useAppearance();
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];
    const currentLogo = appearance === 'light' ? logoDark : logoLight;
    const { data, setData, post, processing, errors, reset } = useForm<Required<ResetPasswordForm>>({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('password.store'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout title={t('Reset your password')} leftImageSrc="/images/sign-in.jpeg">
            <form className="space-y-5" onSubmit={submit}>
                <div className="flex items-center justify-between">
                    {currentLogo && <img src={currentLogo} alt={t('SARD')} className="h-8 object-contain" />}
                    <div className="rounded-md border border-slate-200 bg-white px-2">
                        <LanguageSwitcher />
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-2 rounded-lg border border-slate-200 bg-white p-1 text-sm font-medium">
                    <div
                        className="flex items-center justify-center rounded-md px-3 py-2"
                        style={{ backgroundColor: `${primaryColor}1A`, color: primaryColor }}
                        aria-current="page"
                    >
                        {t('Reset password')}
                    </div>
                    <TextLink
                        href={route('login')}
                        className="flex items-center justify-center rounded-md px-3 py-2 text-slate-600 transition-colors duration-200"
                    >
                        {t('Log in')}
                    </TextLink>
                </div>
                <div className="space-y-4">
                    <div className="relative">
                        <Label htmlFor="email" className="mb-3 block font-medium text-gray-700 dark:text-gray-300">
                            {t('Email')}
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
                                readOnly
                                value={data.email}
                                className="w-full rounded-lg border-gray-300 bg-white pr-12 pl-10 transition-all duration-200 dark:border-gray-600 dark:bg-gray-700"
                                style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                            />
                        </div>
                        <InputError message={errors.email} />
                    </div>

                    <div>
                        <Label htmlFor="password" className="mb-3 block font-medium text-gray-700 dark:text-gray-300">
                            {t('Password')}
                        </Label>
                        <div className="relative">
                            <div
                                className={`pointer-events-none absolute inset-y-0 z-10 flex items-center px-3 ${position === 'right' ? 'right-0' : 'left-0'}`}
                            >
                                <Lock className="h-5 w-5 text-gray-400" />
                            </div>
                            <Input
                                id="password"
                                type="password"
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="new-password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="••••••••"
                                className="w-full rounded-lg border-gray-300 bg-white pr-12 pl-10 transition-all duration-200 dark:border-gray-600 dark:bg-gray-700"
                                style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                            />
                        </div>
                        <InputError message={errors.password} />
                    </div>

                    <div>
                        <Label htmlFor="password_confirmation" className="mb-3 block font-medium text-gray-700 dark:text-gray-300">
                            {t('Confirm password')}
                        </Label>
                        <div className="relative">
                            <div
                                className={`pointer-events-none absolute inset-y-0 z-10 flex items-center px-3 ${position === 'right' ? 'right-0' : 'left-0'}`}
                            >
                                <Lock className="h-5 w-5 text-gray-400" />
                            </div>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                tabIndex={2}
                                autoComplete="new-password"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                placeholder="••••••••"
                                className="w-full rounded-lg border-gray-300 bg-white pr-12 pl-10 transition-all duration-200 dark:border-gray-600 dark:bg-gray-700"
                                style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                            />
                        </div>
                        <InputError message={errors.password_confirmation} />
                    </div>
                </div>

                <AuthButton tabIndex={3} processing={processing}>
                    {t('Reset password')}
                </AuthButton>
            </form>
        </AuthLayout>
    );
}