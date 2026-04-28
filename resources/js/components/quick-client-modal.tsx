import { toast } from '@/components/custom-toast';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Switch } from '@/components/ui/switch';
import { useLayout } from '@/contexts/LayoutContext';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';

export type QuickClientModalProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    phoneCountries: any[];
    defaultCountry?: string;
    onCreated?: (clientId: string) => void;
};

function buildFormState(defaultCountry: string, phoneCountries: any[]) {
    const phoneCountriesByCode = new Map((phoneCountries || []).map((c: any) => [String(c.code).toLowerCase(), c]));
    const phoneCountryCodes = (phoneCountries || [])
        .map((c: any) => String(c.code || '').toLowerCase())
        .filter((code: string) => code);
    const defaultPhoneCountry =
        phoneCountriesByCode.get(String(defaultCountry).toLowerCase()) || phoneCountriesByCode.get('sa') || (phoneCountries || [])[0];
    return {
        name: '',
        country_id: defaultPhoneCountry?.value ? String(defaultPhoneCountry.value) : '',
        phone: '',
        business_type: 'b2c',
        client_login_enabled: false,
        email: '',
        password: '',
        _defaultPhoneIso: (defaultPhoneCountry?.code || '').toLowerCase() || undefined,
        _allowedPhoneCountries: phoneCountryCodes.length
            ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
            : defaultCountries,
        _phoneCountriesByCode: phoneCountriesByCode,
    };
}

export function QuickClientModal({ open, onOpenChange, phoneCountries, defaultCountry = '', onCreated }: QuickClientModalProps) {
    const { t } = useTranslation();
    const { isRtl } = useLayout();
    const [submitting, setSubmitting] = useState(false);
    const [formData, setFormData] = useState(() => buildFormState(defaultCountry, phoneCountries));

    useEffect(() => {
        if (open) {
            setFormData(buildFormState(defaultCountry, phoneCountries));
            setSubmitting(false);
        }
    }, [open, defaultCountry, phoneCountries]);

    const updateField = (field: string, value: any) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        const payload: Record<string, unknown> = {
            name: formData.name,
            country_id: formData.country_id,
            phone: formData.phone,
            business_type: formData.business_type,
            client_login_enabled: formData.client_login_enabled,
            email: formData.email || null,
        };
        if (formData.client_login_enabled) {
            payload.password = formData.password;
        }
        router.post(route('clients.quick-store'), payload as Parameters<typeof router.post>[1], {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                setSubmitting(false);
                onOpenChange(false);
                toast.dismiss();
                const flash = (page as any)?.props?.flash;
                if (flash?.created_client_id != null) {
                    onCreated?.(String(flash.created_client_id));
                }
                if (flash?.success) toast.success(flash.success);
                if (flash?.warning) toast.message(flash.warning);
                if (flash?.error) toast.error(flash.error);
            },
            onError: (formErrors) => {
                setSubmitting(false);
                toast.dismiss();
                if (typeof formErrors === 'string') {
                    toast.error(formErrors);
                } else if (Object.values(formErrors).length > 0) {
                    toast.error(
                        t('Failed to create {{model}}: {{errors}}', {
                            model: t('Client'),
                            errors: Object.values(formErrors).join(', '),
                        }),
                    );
                }
            },
            onFinish: () => setSubmitting(false),
        });
    };

    const phoneCountriesByCode = formData._phoneCountriesByCode as Map<string, any>;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] max-w-lg overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('Quick add client')}</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="qc_name" required>
                            {t('Client Name')}
                        </Label>
                        <Input id="qc_name" value={formData.name} onChange={(e) => updateField('name', e.target.value)} required />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="qc_phone">{t('Phone Number')}</Label>
                        <div className="phone-left-selector">
                            <PhoneInput
                                defaultCountry={formData._defaultPhoneIso as string | undefined}
                                value={formData.phone}
                                countries={formData._allowedPhoneCountries as typeof defaultCountries}
                                inputProps={{ name: 'phone', required: true }}
                                className="w-full"
                                inputClassName="w-full !h-10 !border !border-input !bg-background !text-sm !text-foreground"
                                countrySelectorStyleProps={{
                                    buttonClassName: '!h-10 !border !border-input !bg-background',
                                    dropdownStyleProps: {
                                        className: '!bg-background !text-foreground phone-country-dropdown',
                                    },
                                }}
                                onChange={(value, meta) => {
                                    updateField('phone', value || '');
                                    const code = String(meta?.country?.iso2 || '').toLowerCase();
                                    const selected = phoneCountriesByCode.get(code) as any;
                                    if (selected) {
                                        updateField('country_id', String(selected.value));
                                    }
                                }}
                            />
                        </div>
                    </div>
                    <div className="space-y-2">
                        <Label className={isRtl ? 'block text-right' : ''}>{t('Business Type')}</Label>
                        <RadioGroup
                            value={formData.business_type}
                            onValueChange={(value) => updateField('business_type', value)}
                            className={isRtl ? 'flex justify-end gap-6' : 'flex gap-6'}
                        >
                            <div className="flex items-center gap-2">
                                <RadioGroupItem value="b2b" id="qc_business_type_b2b" />
                                <Label htmlFor="qc_business_type_b2b" className="font-normal">
                                    {t('Business')}
                                </Label>
                            </div>
                            <div className="flex items-center gap-2">
                                <RadioGroupItem value="b2c" id="qc_business_type_b2c" />
                                <Label htmlFor="qc_business_type_b2c" className="font-normal">
                                    {t('Individual')}
                                </Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="qc_client_login_enabled">{t('Enable client login')}</Label>
                            <div className="flex h-10 items-center">
                                <Switch
                                    id="qc_client_login_enabled"
                                    checked={formData.client_login_enabled}
                                    onCheckedChange={(checked) => {
                                        updateField('client_login_enabled', checked);
                                        if (!checked) updateField('password', '');
                                    }}
                                />
                            </div>
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="qc_email" required={formData.client_login_enabled}>
                                {formData.client_login_enabled ? t('Email') : `${t('Email')} (${t('Optional')})`}
                            </Label>
                            <Input
                                id="qc_email"
                                type="email"
                                autoComplete="email"
                                value={formData.email}
                                onChange={(e) => updateField('email', e.target.value)}
                                required={formData.client_login_enabled}
                            />
                        </div>
                        {formData.client_login_enabled ? (
                            <div className="space-y-2">
                                <Label htmlFor="qc_password" required>
                                    {t('Password')}
                                </Label>
                                <Input
                                    id="qc_password"
                                    type="password"
                                    autoComplete="new-password"
                                    value={formData.password}
                                    onChange={(e) => updateField('password', e.target.value)}
                                    required
                                />
                            </div>
                        ) : null}
                    </div>

                    <DialogFooter className={isRtl ? 'flex-row-reverse gap-2 sm:justify-start' : 'gap-2'}>
                        <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={submitting}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={submitting}>
                            {t('Save')}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
