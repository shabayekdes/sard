import { toast } from '@/components/custom-toast';
import { PageTemplate } from '@/components/page-template';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Repeater, type RepeaterField } from '@/components/ui/repeater';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useLayout } from '@/contexts/LayoutContext';
import { router, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';

export default function EditClient() {
    const { t, i18n } = useTranslation();
    const { client, clientTypes, countries, documentTypes, phoneCountries, defaultCountry = '', defaultTaxRate = '', errors = {} } = usePage().props as any;
    const currentLocale = i18n.language || 'en';
    const { isRtl } = useLayout();

    useEffect(() => {
        const handleLanguageChange = () => {
            if (!client?.id) return;
            router.get(route('clients.edit', client.id), {}, { preserveState: true, preserveScroll: true });
        };

        window.addEventListener('languageChanged', handleLanguageChange);
        i18n.on('languageChanged', handleLanguageChange);
        return () => {
            window.removeEventListener('languageChanged', handleLanguageChange);
            i18n.off('languageChanged', handleLanguageChange);
        };
    }, [client?.id, i18n]);

    const phoneCountriesByCode = new Map((phoneCountries || []).map((country: any) => [String(country.code).toLowerCase(), country]));
    const phoneCountryCodes = (phoneCountries || []).map((country: any) => String(country.code || '').toLowerCase()).filter((code: string) => code);
    const allowedPhoneCountries = phoneCountryCodes.length
        ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
        : defaultCountries;
    const defaultPhoneCountry =
        phoneCountriesByCode.get(String(defaultCountry).toLowerCase()) || phoneCountriesByCode.get('sa') || (phoneCountries || [])[0];
    const countriesByCode = new Map((countries || []).map((country: any) => [String(country.code || '').toLowerCase(), country]));
    const defaultNationality = countriesByCode.get(String(defaultCountry).toLowerCase()) || (countries || [])[0];

    const [formData, setFormData] = useState(() => ({
        name: client?.name || '',
        country_id: client?.country_id ? String(client.country_id) : defaultPhoneCountry?.value ? String(defaultPhoneCountry.value) : '',
        phone: client?.phone || '',
        email: client?.email || '',
        client_type_id: client?.client_type_id ? String(client.client_type_id) : '',
        business_type: client?.business_type || 'b2c',
        nationality_id: client?.nationality_id ? String(client.nationality_id) : defaultNationality?.value ? String(defaultNationality.value) : '',
        id_number: client?.id_number || '',
        gender: client?.gender || '',
        date_of_birth: client?.date_of_birth || '',
        unified_number: client?.unified_number || '',
        cr_number: client?.cr_number || '',
        cr_issuance_date: client?.cr_issuance_date || '',
        tax_id: client?.tax_id || '',
        address: client?.address || '',
        tax_rate: client?.tax_rate ?? (defaultTaxRate ? Number(defaultTaxRate) : 0),
        notes: client?.notes || '',
        status: client?.status || 'active',
        documents: (client?.documents || []).map((doc: any) => ({
            document_name: doc.document_name || '',
            document_type_id: doc.document_type_id ? String(doc.document_type_id) : '',
            file: doc.file || doc.file_path || '',
        })),
    }));

    const normalizedErrors = useMemo(() => {
        const next: Record<string, string> = {};
        Object.entries(errors || {}).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                next[key] = value[0] || '';
            } else if (value) {
                next[key] = value as string;
            }
        });
        return next;
    }, [errors]);

    const updateField = (field: string, value: any) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
    };

    const handleFormSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!client?.id) {
            toast.error(t('Client not found'));
            return;
        }

        toast.loading(t('Updating client...'));

        const filteredDocuments = (formData.documents || []).filter((document: any) => {
            return document?.document_name || document?.document_type_id || document?.file;
        });

        const payload = { ...formData, documents: filteredDocuments };

        router.put(route('clients.update', client.id), payload, {
            onSuccess: (page) => {
                toast.dismiss();
                const flash = (page as any)?.props?.flash;
                if (flash?.success) {
                    toast.success(flash.success);
                } else if (flash?.error) {
                    toast.error(flash.error);
                }
            },
            onError: (formErrors) => {
                toast.dismiss();
                if (typeof formErrors === 'string') {
                    toast.error(formErrors);
                } else if (Object.values(formErrors).length > 0) {
                    toast.error(t('Failed to update {{model}}: {{errors}}', { model: t('Client'), errors: Object.values(formErrors).join(', ') }));
                }
            },
        });
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Client Management'), href: route('clients.index') },
        { title: t('Clients'), href: route('clients.index') },
        { title: client?.name || t('Edit Client') },
    ];

    const renderError = (field: string) => (normalizedErrors[field] ? <p className="text-xs text-red-500">{normalizedErrors[field]}</p> : null);

    const documentTypeOptions = (documentTypes || []).map((type: any) => {
        let displayName = type.name;
        if (typeof type.name === 'object' && type.name !== null) {
            displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
        } else if (type.name_translations && typeof type.name_translations === 'object') {
            displayName = type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
        }
        return { value: type.id.toString(), label: displayName };
    });

    const documentFields: RepeaterField[] = [
        { name: 'document_name', label: t('Document Name'), type: 'text', required: true },
        {
            name: 'document_type_id',
            label: t('Document Type'),
            type: 'select',
            required: true,
            options: documentTypeOptions,
            placeholder: t('Select Document Type'),
        },
        { name: 'file', label: t('File'), type: 'media-picker', required: true },
    ];

    return (
        <PageTemplate title={t('Edit Client')} url="/clients" breadcrumbs={breadcrumbs} noPadding>
            <div className="rounded-lg border border-slate-200 bg-white px-6 pb-10 dark:border-gray-800">
                <form onSubmit={handleFormSubmit} className="mt-6 space-y-6">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label htmlFor="name">{t('Client Name')}</Label>
                            <Input id="name" value={formData.name} onChange={(e) => updateField('name', e.target.value)} required />
                            {renderError('name')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Client Type')}</Label>
                            <Select value={formData.client_type_id} onValueChange={(value) => updateField('client_type_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Type')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {(clientTypes || []).map((type: any) => {
                                        const translations = type.name_translations || (typeof type.name === 'object' ? type.name : null);
                                        let displayName = type.name;
                                        if (translations && typeof translations === 'object') {
                                            displayName = translations[currentLocale] || translations.en || translations.ar || type.name || '';
                                        } else if (typeof type.name === 'object') {
                                            displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
                                        }
                                        return (
                                            <SelectItem key={type.id} value={type.id.toString()}>
                                                {displayName}
                                            </SelectItem>
                                        );
                                    })}
                                </SelectContent>
                            </Select>
                            {renderError('client_type_id')}
                        </div>
                        <div className="space-y-2">
                            <Label className={isRtl ? 'block text-right' : ''}>{t('Business Type')}</Label>
                            <RadioGroup
                                value={formData.business_type}
                                onValueChange={(value) => updateField('business_type', value)}
                                className={isRtl ? 'flex justify-end gap-6' : 'flex gap-6'}
                            >
                                <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                    <RadioGroupItem value="b2c" id="business_type_b2c" />
                                    <Label htmlFor="business_type_b2c" className="font-normal">
                                        {t('Individual')}
                                    </Label>
                                </div>
                                <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                    <RadioGroupItem value="b2b" id="business_type_b2b" />
                                    <Label htmlFor="business_type_b2b" className="font-normal">
                                        {t('Business')}
                                    </Label>
                                </div>
                            </RadioGroup>
                            {renderError('business_type')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label htmlFor="phone">{t('Phone Number')}</Label>
                            <div className="phone-left-selector">
                                <PhoneInput
                                    defaultCountry={(defaultPhoneCountry?.code || '').toLowerCase() || undefined}
                                    value={formData.phone}
                                    countries={allowedPhoneCountries}
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
                                        const selectedCountry = phoneCountriesByCode.get(code) as any;
                                        if (selectedCountry) {
                                            updateField('country_id', String(selectedCountry.value));
                                        }
                                    }}
                                />
                            </div>
                            {renderError('phone')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="email">{t('Email')}</Label>
                            <Input id="email" type="email" value={formData.email} onChange={(e) => updateField('email', e.target.value)} required />
                            {renderError('email')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Status')}</Label>
                            <Select value={formData.status} onValueChange={(value) => updateField('status', value)}>
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="active">{t('Active')}</SelectItem>
                                    <SelectItem value="inactive">{t('Inactive')}</SelectItem>
                                </SelectContent>
                            </Select>
                            {renderError('status')}
                        </div>
                    </div>

                    {formData.business_type === 'b2c' && (
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div className="space-y-2">
                                <Label>{t('Nationality')}</Label>
                                <Select value={formData.nationality_id} onValueChange={(value) => updateField('nationality_id', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select Nationality')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {(countries || []).map((country: any) => (
                                            <SelectItem key={country.value} value={String(country.value)}>
                                                {country.label || country.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {renderError('nationality_id')}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="id_number">{t('ID National')}</Label>
                                <Input id="id_number" value={formData.id_number} onChange={(e) => updateField('id_number', e.target.value)} />
                                {renderError('id_number')}
                            </div>
                            <div className="space-y-2">
                                <Label className={isRtl ? 'block text-right' : ''}>{t('Gender')}</Label>
                                <RadioGroup
                                    value={formData.gender}
                                    onValueChange={(value) => updateField('gender', value)}
                                    className={isRtl ? 'flex justify-end gap-6' : 'flex gap-6'}
                                >
                                    <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                        <RadioGroupItem value="male" id="gender_male" />
                                        <Label htmlFor="gender_male" className="font-normal">
                                            {t('Male')}
                                        </Label>
                                    </div>
                                    <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                        <RadioGroupItem value="female" id="gender_female" />
                                        <Label htmlFor="gender_female" className="font-normal">
                                            {t('Female')}
                                        </Label>
                                    </div>
                                </RadioGroup>
                                {renderError('gender')}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="date_of_birth">{t('Date of Birth')}</Label>
                                <Input
                                    id="date_of_birth"
                                    type="date"
                                    className={isRtl ? 'rtl-date-input' : ''}
                                    lang={i18n.language}
                                    value={formData.date_of_birth}
                                    onChange={(e) => updateField('date_of_birth', e.target.value)}
                                />
                                {renderError('date_of_birth')}
                            </div>
                        </div>
                    )}

                    {formData.business_type === 'b2b' && (
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="unified_number">{t('Unified Number')}</Label>
                                <Input
                                    id="unified_number"
                                    value={formData.unified_number}
                                    onChange={(e) => updateField('unified_number', e.target.value)}
                                />
                                {renderError('unified_number')}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="cr_number">{t('CR Number')}</Label>
                                <Input id="cr_number" value={formData.cr_number} onChange={(e) => updateField('cr_number', e.target.value)} />
                                {renderError('cr_number')}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="cr_issuance_date">{t('CR Issuance Date')}</Label>
                                <Input
                                    id="cr_issuance_date"
                                    type="date"
                                    value={formData.cr_issuance_date}
                                    onChange={(e) => updateField('cr_issuance_date', e.target.value)}
                                />
                                {renderError('cr_issuance_date')}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="tax_id">{t('Tax ID')}</Label>
                                <Input id="tax_id" value={formData.tax_id} onChange={(e) => updateField('tax_id', e.target.value)} />
                                {renderError('tax_id')}
                            </div>
                        </div>
                    )}

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label htmlFor="tax_rate">{t('Tax Rate')} (%)</Label>
                            <Input
                                id="tax_rate"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                value={formData.tax_rate}
                                onChange={(e) => updateField('tax_rate', e.target.value === '' ? '' : Number(e.target.value))}
                            />
                            {renderError('tax_rate')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="address">{t('Address')}</Label>
                            <Input id="address" value={formData.address} onChange={(e) => updateField('address', e.target.value)} />
                            {renderError('address')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="notes">{t('Note')}</Label>
                            <Textarea id="notes" value={formData.notes} onChange={(e) => updateField('notes', e.target.value)} />
                            {renderError('notes')}
                        </div>
                    </div>

                <div className="rounded-lg border border-slate-200 bg-white p-6 dark:border-gray-800">
                    <h2 className="text-lg font-semibold">{t('Client Documents')}</h2>

                    <div className="space-y-4 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <div className="flex items-center justify-between">
                            <h3 className="text-sm font-semibold text-slate-700">{t('Client Documents')}</h3>
                        </div>
                        <Repeater
                            fields={documentFields}
                            value={formData.documents}
                            onChange={(value) => updateField('documents', value)}
                            minItems={0}
                            maxItems={-1}
                            addButtonText={t('Add Document')}
                            removeButtonText={t('Remove')}
                            showItemNumbers={false}
                            className="space-y-3"
                            itemClassName="bg-white border-slate-200"
                        />
                        {renderError('documents')}
                    </div>
                </div>

                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={() => router.get(route('clients.index'))}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit">{t('Save')}</Button>
                    </div>
                </form>
            </div>
        </PageTemplate>
    );
}
