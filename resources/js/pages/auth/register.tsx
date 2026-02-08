import { useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

import AuthButton from '@/components/auth/auth-button';
import InputError from '@/components/input-error';
import { LanguageSwitcher } from '@/components/language-switcher';
import Recaptcha from '@/components/recaptcha';
import TextLink from '@/components/text-link';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout } from '@/contexts/LayoutContext';
import { THEME_COLORS, useAppearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth-layout';
import { Lock } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';

type RegisterForm = {
    name: string;
    phone: string;
    email: string;
    city: string;
    password: string;
    password_confirmation: string;
    terms: boolean;
    recaptcha_token?: string;
    plan_id?: string;
    referral_code?: string;
};

export default function Register({ referralCode, planId }: { referralCode?: string; planId?: string }) {
    const { t } = useTranslation();
    const { position } = useLayout();

    const [recaptchaToken, setRecaptchaToken] = useState<string>('');
    const { themeColor, customColor, logoLight, logoDark } = useBrand();
    const { appearance } = useAppearance();
    const primaryColor = themeColor === 'custom' ? customColor : THEME_COLORS[themeColor as keyof typeof THEME_COLORS];
    const currentLogo = appearance === 'light' ? logoDark : logoLight;
    const { props } = usePage();
    const { phoneCountries = [], defaultCountry = '' } = props as any;
    const phoneCountriesByCode = new Map((phoneCountries || []).map((country: any) => [String(country.code || '').toLowerCase(), country]));
    const phoneCountryCodes = (phoneCountries || []).map((country: any) => String(country.code || '').toLowerCase()).filter((code: string) => code);
    const allowedPhoneCountries = phoneCountryCodes.length
        ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
        : defaultCountries;
    const defaultPhoneCountry =
        phoneCountriesByCode.get(String(defaultCountry).toLowerCase()) || phoneCountriesByCode.get('sa') || (phoneCountries || [])[0];

    const { data, setData, post, processing, errors, reset } = useForm<RegisterForm>({
        name: '',
        phone: '',
        email: '',
        city: '',
        password: '',
        password_confirmation: '',
        terms: false,
        plan_id: planId,
        referral_code: referralCode,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), {
            data: { ...data, recaptcha_token: recaptchaToken },
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };
    const currentCountryCode = String(defaultPhoneCountry?.code || defaultCountry || 'sa').toLowerCase();

    return (
        <AuthLayout title={t('Create account')} leftImageSrc="/images/sign-in.jpeg">
            <form className="space-y-4" onSubmit={submit}>
                <div className="flex items-center justify-between">
                    {currentLogo && <img src={currentLogo} alt={t('SARD')} className="h-8 object-contain" />}
                    <div className="rounded-md border border-slate-200 bg-white px-2">
                        <LanguageSwitcher />
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-2 rounded-lg border border-slate-200 bg-white p-1 text-sm font-medium">
                    <TextLink
                        href={route('login')}
                        className="flex items-center justify-center rounded-md px-3 py-2 text-slate-600 transition-colors duration-200"
                    >
                        {t('Log in')}
                    </TextLink>
                    <div
                        className="flex items-center justify-center rounded-md px-3 py-2"
                        style={{ backgroundColor: `${primaryColor}1A`, color: primaryColor }}
                        aria-current="page"
                    >
                        {t('Create account')}
                    </div>
                </div>
                <div className="space-y-3">
                    <div className="relative">
                        <Label htmlFor="name" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {t('Full Name')}
                        </Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder={t('Enter your full name')}
                            className="h-10 w-full rounded-md border-slate-200 bg-white px-3 text-sm text-slate-700 transition-all duration-200 placeholder:text-slate-400 dark:border-gray-600 dark:bg-gray-700"
                            style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                        />
                        <InputError message={errors.name} />
                    </div>
                    <div className="relative">
                        <Label htmlFor="phone" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {t('Phone Number')}
                        </Label>
                        <PhoneInput
                            defaultCountry={currentCountryCode || undefined}
                            value={data.phone || ''}
                            countries={allowedPhoneCountries}
                            inputProps={{ id: 'phone', name: 'phone', required: true }}
                            className="w-full"
                            inputClassName="w-full !h-10 !border !border-input !bg-background !text-sm !text-foreground"
                            countrySelectorStyleProps={{
                                buttonClassName: '!h-10 !border !border-input !bg-background',
                                dropdownStyleProps: {
                                    className: '!bg-background !text-foreground',
                                },
                            }}
                            onChange={(value, meta) => setData('phone', value || '')}
                        />
                        <InputError message={errors.phone} />
                    </div>

                    <div className="relative">
                        <Label htmlFor="email" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {t('Email Address')}
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            tabIndex={3}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder={t('Enter your email address')}
                            className="h-10 w-full rounded-md border-slate-200 bg-white px-3 text-sm text-slate-700 transition-all duration-200 placeholder:text-slate-400 dark:border-gray-600 dark:bg-gray-700"
                            style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="relative">
                        <Label htmlFor="city" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {t('City')}
                        </Label>
                        <Input
                            id="city"
                            type="text"
                            required
                            tabIndex={4}
                            autoComplete="city"
                            value={data.city}
                            onChange={(e) => setData('city', e.target.value)}
                            placeholder={t('أدخل مدينتك')}
                            className="h-10 w-full rounded-md border-slate-200 bg-white px-3 text-sm text-slate-700 transition-all duration-200 placeholder:text-slate-400 dark:border-gray-600 dark:bg-gray-700"
                            style={{ '--tw-ring-color': primaryColor } as React.CSSProperties}
                        />
                        <InputError message={errors.city} />
                    </div>

                    <div>
                        <Label htmlFor="password" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                                tabIndex={5}
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
                        <Label htmlFor="password_confirmation" className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {t('Confirm Password')}
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
                                tabIndex={6}
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

                    <div className="flex items-start gap-2">
                        <Checkbox
                            id="terms"
                            name="terms"
                            checked={data.terms}
                            onClick={() => setData('terms', !data.terms)}
                            tabIndex={7}
                            className="mt-1 rounded border-gray-300"
                            style={{ '--tw-ring-color': primaryColor, color: primaryColor } as React.CSSProperties}
                        />
                        <Label htmlFor="terms" className="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            {t('I agree to the terms and conditions and privacy policy')}
                        </Label>
                    </div>
                    <InputError message={errors.terms} />
                </div>

                <div className="origin-top scale-90">
                    <Recaptcha onVerify={setRecaptchaToken} onExpired={() => setRecaptchaToken('')} onError={() => setRecaptchaToken('')} />
                </div>

                <AuthButton
                    tabIndex={8}
                    processing={processing}
                    className="h-10 rounded-md text-sm shadow-none hover:-translate-y-0 hover:shadow-none"
                >
                    {t('Create a free account')}
                </AuthButton>

                <p className="text-center text-xs text-gray-500 dark:text-gray-400">{t('No credit card needed')}</p>
            </form>
        </AuthLayout>
    );
}
