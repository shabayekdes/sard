import { router, useForm } from '@inertiajs/react';
import { Lock, Mail } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

import AuthButton from '@/components/auth/auth-button';
import InputError from '@/components/input-error';
import { LanguageSwitcher } from '@/components/language-switcher';
import Recaptcha from '@/components/recaptcha';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout } from '@/contexts/LayoutContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth-layout';
import { useTranslation } from 'react-i18next';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
    recaptcha_token?: string;
};

interface Business {
    id: number;
    name: string;
    slug: string;
    business_type: string;
}

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
    isNonProduction: boolean;
}

export default function Login({ canResetPassword, isNonProduction }: LoginProps) {
    const { t } = useTranslation();
    const { position } = useLayout();

    const [recaptchaToken, setRecaptchaToken] = useState<string>('');
    const { themeColor, customColor, logoLight, logoDark } = useBrand();
    const { appearance } = useAppearance();
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];
    const currentLogo = appearance === 'light' ? logoDark : logoLight;
    // Always show business buttons by default
    const { data, setData, post, processing, errors, reset } = useForm<LoginForm>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        const formData = { ...data, recaptcha_token: recaptchaToken };
        post(route('login'), formData, {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthLayout title={t('Welcome back to Sard App')} leftImageSrc="/images/sign-in.jpeg">
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
                        {t('Log in')}
                    </div>
                    <TextLink
                        href={route('register')}
                        className="flex items-center justify-center rounded-md px-3 py-2 text-slate-600 transition-colors duration-200"
                    >
                        {t('Create account')}
                    </TextLink>
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

                    <div>
                        <div className="mb-1 flex items-center justify-between">
                            <Label htmlFor="password" className="font-medium text-gray-700 dark:text-gray-300">
                                {t('Password')}
                            </Label>
                            {canResetPassword && (
                                <TextLink
                                    href={route('password.request')}
                                    className="text-sm transition-colors duration-200"
                                    style={{ color: primaryColor }}
                                    tabIndex={5}
                                >
                                    {t('Forgot password')}?
                                </TextLink>
                            )}
                        </div>
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
                                tabIndex={2}
                                autoComplete="current-password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="••••••••"
                                className="w-full rounded-lg border-gray-300 bg-white pr-12 pl-10 transition-all duration-200 dark:border-gray-600 dark:bg-gray-700"
                                style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                            />
                        </div>
                        <InputError message={errors.password} />
                    </div>

                    <div className="flex items-center">
                        <Checkbox
                            id="remember"
                            name="remember"
                            checked={data.remember}
                            onClick={() => setData('remember', !data.remember)}
                            tabIndex={3}
                            className="rounded border-gray-300"
                            style={{ '--tw-ring-color': primaryColor, color: primaryColor } as React.CSSProperties}
                        />
                        <Label htmlFor="remember" className="mx-2 text-gray-600 dark:text-gray-400">
                            {t('Remember me')}
                        </Label>
                    </div>
                </div>

                <Recaptcha onVerify={setRecaptchaToken} onExpired={() => setRecaptchaToken('')} onError={() => setRecaptchaToken('')} />

                <AuthButton tabIndex={4} processing={processing}>
                    {t('Log in')}
                </AuthButton>

                {isNonProduction && (
                    <div className="mt-6">
                        <div className="border-t border-gray-200 pt-5 dark:border-gray-700">
                            <h3 className="mb-4 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Demo Quick Access</h3>

                            <div className="flex flex-col space-y-4">
                                <div className="flex justify-center gap-3">
                                    <Button
                                        type="button"
                                        onClick={() => {
                                            // Use Inertia router to handle CSRF token automatically
                                            router.post(route('login'), {
                                                email: 'esmail@sard.app',
                                                password: '12345678',
                                                remember: false,
                                                recaptcha_token: recaptchaToken,
                                            });
                                        }}
                                        className="w-45 rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200"
                                        style={{ backgroundColor: primaryColor }}
                                    >
                                        Login as Super Admin
                                    </Button>
                                    <Button
                                        type="button"
                                        onClick={() => {
                                            // Use Inertia router to handle CSRF token automatically
                                            router.post(route('login'), {
                                                email: 'acmecorporation@example.com',
                                                password: 'password',
                                                remember: false,
                                                recaptcha_token: recaptchaToken,
                                            });
                                        }}
                                        className="w-45 rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200"
                                        style={{ backgroundColor: primaryColor }}
                                    >
                                        Login as Company
                                    </Button>
                                </div>

                                <div className="flex justify-center gap-3">
                                    <Button
                                        type="button"
                                        onClick={() => {
                                            // Use Inertia router to handle CSRF token automatically
                                            router.post(route('login'), {
                                                email: 'alaa@sard.app',
                                                password: 'password',
                                                remember: false,
                                                recaptcha_token: recaptchaToken,
                                            });
                                        }}
                                        className="w-45 rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200"
                                        style={{ backgroundColor: primaryColor }}
                                    >
                                        Login as Client
                                    </Button>
                                    <Button
                                        type="button"
                                        onClick={() => {
                                            // Use Inertia router to handle CSRF token automatically
                                            router.post(route('login'), {
                                                email: 'linda_davis_2@example.com',
                                                password: 'password',
                                                remember: false,
                                                recaptcha_token: recaptchaToken,
                                            });
                                        }}
                                        className="w-45 rounded-md px-3 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200"
                                        style={{ backgroundColor: primaryColor }}
                                    >
                                        Login as Team Member
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </form>
        </AuthLayout>
    );
}
