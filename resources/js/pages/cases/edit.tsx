import { toast } from '@/components/custom-toast';
import { PageTemplate } from '@/components/page-template';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Repeater, type RepeaterField } from '@/components/ui/repeater';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useLayout } from '@/contexts/LayoutContext';
import { router, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

const defaultFormData = {
    client_id: '',
    attributes: 'petitioner',
    opposite_parties: [] as any[],
    title: '',
    case_number: '',
    file_number: '',
    case_category_id: '',
    case_subcategory_id: '',
    case_type_id: '',
    case_status_id: '',
    priority: 'medium',
    court_id: '',
    filing_date: '',
    expected_completion_date: '',
    estimated_value: '',
    description: '',
    status: 'active',
    sync_with_google_calendar: false,
};

export default function EditCase() {
    const { t, i18n } = useTranslation();
    const { auth, case: caseProp, clients, caseTypes, caseCategories, caseStatuses, courts, countries, googleCalendarEnabled, errors = {}, base_url } =
        usePage().props as any;
    const { isRtl } = useLayout();
    const currentLocale = i18n.language || 'en';
    const caseId = caseProp?.id;

    useEffect(() => {
        const handleLanguageChange = () => {
            if (caseId) router.get(route('cases.edit', caseId), {}, { preserveState: true, preserveScroll: true });
        };
        window.addEventListener('languageChanged', handleLanguageChange);
        i18n.on('languageChanged', handleLanguageChange);
        return () => {
            window.removeEventListener('languageChanged', handleLanguageChange);
            i18n.off('languageChanged', handleLanguageChange);
        };
    }, [i18n, caseId]);

    const [formData, setFormData] = useState(() => {
        const oppositeParties = caseProp?.opposite_parties?.length
            ? caseProp.opposite_parties
            : [{ name: '', id_number: '', nationality_id: '', lawyer_name: '' }];
        return {
            ...defaultFormData,
            ...caseProp,
            opposite_parties: oppositeParties,
            client_id: caseProp?.client_id != null ? String(caseProp.client_id) : '',
            case_category_id: caseProp?.case_category_id != null ? String(caseProp.case_category_id) : '',
            case_subcategory_id: caseProp?.case_subcategory_id != null ? String(caseProp.case_subcategory_id) : '',
            case_type_id: caseProp?.case_type_id != null ? String(caseProp.case_type_id) : '',
            case_status_id: caseProp?.case_status_id != null ? String(caseProp.case_status_id) : '',
            court_id: caseProp?.court_id != null ? String(caseProp.court_id) : '',
            attributes: caseProp?.attributes || 'petitioner',
            priority: caseProp?.priority || 'medium',
            status: caseProp?.status || 'active',
        };
    });
    const [subcategoryOptions, setSubcategoryOptions] = useState<{ value: string; label: string }[]>([]);
    const [isSubcategoryLoading, setIsSubcategoryLoading] = useState(false);

    const normalizedErrors = useMemo(() => {
        const next: Record<string, string> = {};
        Object.entries(errors || {}).forEach(([key, value]) => {
            if (Array.isArray(value)) next[key] = value[0] || '';
            else if (value) next[key] = value as string;
        });
        return next;
    }, [errors]);

    const resolveTranslatableName = (item: any) => {
        if (!item) return '';
        let displayName = item.name;
        if (typeof item.name === 'object' && item.name !== null) {
            displayName = item.name[currentLocale] || item.name.en || item.name.ar || '';
        } else if (item.name_translations && typeof item.name_translations === 'object') {
            displayName = item.name_translations[currentLocale] || item.name_translations.en || item.name_translations.ar || '';
        }
        return displayName || '';
    };

    const updateField = (field: string, value: any) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
    };

    useEffect(() => {
        const loadSubcategories = async () => {
            if (!formData.case_category_id) {
                setSubcategoryOptions([]);
                return;
            }
            setIsSubcategoryLoading(true);
            try {
                const response = await fetch(`${base_url}/case/case-categories/${encodeURIComponent(formData.case_category_id)}/subcategories`);
                if (!response.ok) throw new Error('Failed to load subcategories');
                const data = await response.json();
                const options = Array.isArray(data)
                    ? data.map((item: any) => ({ value: String(item.id || item.value || ''), label: String(item.name || item.label || '') }))
                    : [];
                setSubcategoryOptions(options);
            } catch {
                setSubcategoryOptions([]);
            } finally {
                setIsSubcategoryLoading(false);
            }
        };
        loadSubcategories();
    }, [formData.case_category_id, base_url]);

    const handleFormSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        toast.loading(t('Updating case...'));

        const payload = {
            ...formData,
            case_category_id: formData.case_category_id === '' ? null : formData.case_category_id,
            case_subcategory_id: formData.case_subcategory_id === '' ? null : formData.case_subcategory_id,
        };

        router.put(route('cases.update', caseId), payload, {
            onSuccess: (page) => {
                toast.dismiss();
                const flash = (page as any)?.props?.flash;
                if (flash?.success) toast.success(flash.success);
                else if (flash?.error) toast.error(flash.error);
            },
            onError: (formErrors) => {
                toast.dismiss();
                if (typeof formErrors === 'string') toast.error(formErrors);
                else if (Object.values(formErrors).length > 0) toast.error(`Failed to update case: ${Object.values(formErrors).join(', ')}`);
            },
        });
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Case Management'), href: route('cases.index') },
        { title: t('Cases'), href: route('cases.index') },
        { title: caseProp?.title || t('Case'), href: route('cases.show', caseId) },
        { title: t('Edit Case') },
    ];

    const renderError = (field: string) =>
        normalizedErrors[field] ? <p className="text-xs text-red-500">{normalizedErrors[field]}</p> : null;
    const oppositePartyErrorKey = Object.keys(normalizedErrors).find((k) => k.startsWith('opposite_parties'));

    const oppositePartyFields: RepeaterField[] = [
        { name: 'lawyer_name', label: t('Lawyer Name'), type: 'text' },
        { name: 'name', label: t('Full Name'), type: 'text', required: true },
        {
            name: 'nationality_id',
            label: t('Nationality'),
            type: 'select',
            options: countries || [],
            placeholder: (countries || []).length > 0 ? t('Select Nationality') : t('No nationalities available'),
        },
        { name: 'id_number', label: t('ID National'), type: 'text' },
    ];

    return (
        <PageTemplate title={t('Edit Case')} url="/cases" breadcrumbs={breadcrumbs} noPadding>
            <form onSubmit={handleFormSubmit}>
                <div className="mb-6 rounded-lg border border-slate-200 bg-white p-6 dark:border-gray-800">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label>{t('Client')}</Label>
                            <Select value={formData.client_id} onValueChange={(value) => updateField('client_id', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue placeholder={t('Select Client')} />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {(clients || []).map((client: any) => (
                                        <SelectItem key={client.id} value={client.id.toString()}>
                                            {client.name}
                                        </SelectItem>
                                    ))}
                                    {auth?.user && (
                                        <SelectItem value={auth.user.id.toString()}>{auth.user.name} (Me)</SelectItem>
                                    )}
                                </SelectContent>
                            </Select>
                            {renderError('client_id')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="title">{t('Case Title')}</Label>
                            <Input id="title" value={formData.title} onChange={(e) => updateField('title', e.target.value)} required />
                            {renderError('title')}
                        </div>
                        <div className="space-y-2">
                            <Label className={isRtl ? 'block text-right' : ''}>{t('Attributes')}</Label>
                            <RadioGroup
                                value={formData.attributes}
                                onValueChange={(value) => updateField('attributes', value)}
                                className={isRtl ? 'flex justify-end gap-6' : 'flex gap-6'}
                            >
                                <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                    <RadioGroupItem value="petitioner" id="attributes_petitioner" />
                                    <Label htmlFor="attributes_petitioner" className="font-normal">{t('Petitioner')}</Label>
                                </div>
                                <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                    <RadioGroupItem value="respondent" id="attributes_respondent" />
                                    <Label htmlFor="attributes_respondent" className="font-normal">{t('Respondent')}</Label>
                                </div>
                            </RadioGroup>
                            {renderError('attributes')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label>{t('Case Type')}</Label>
                            <Select value={formData.case_type_id} onValueChange={(value) => updateField('case_type_id', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue placeholder={t('Select Type')} />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {(caseTypes || []).map((type: any) => (
                                        <SelectItem key={type.id} value={type.id.toString()}>
                                            {resolveTranslatableName(type)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('case_type_id')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Case Status')}</Label>
                            <Select value={formData.case_status_id} onValueChange={(value) => updateField('case_status_id', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue placeholder={t('Select Status')} />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {(caseStatuses || []).map((status: any) => (
                                        <SelectItem key={status.id} value={status.id.toString()}>
                                            {resolveTranslatableName(status)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('case_status_id')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Priority')}</Label>
                            <Select value={formData.priority} onValueChange={(value) => updateField('priority', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectItem value="low">{t('Low')}</SelectItem>
                                    <SelectItem value="medium">{t('Medium')}</SelectItem>
                                    <SelectItem value="high">{t('High')}</SelectItem>
                                </SelectContent>
                            </Select>
                            {renderError('priority')}
                        </div>
                    </div>

                    <div className="col-span-full my-6 h-px bg-gray-200 dark:bg-gray-800" />

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label htmlFor="case_number">{t('Case Number')}</Label>
                            <Input id="case_number" value={formData.case_number} onChange={(e) => updateField('case_number', e.target.value)} />
                            {renderError('case_number')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="file_number">{t('File Number')}</Label>
                            <Input id="file_number" value={formData.file_number} onChange={(e) => updateField('file_number', e.target.value)} />
                            {renderError('file_number')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Court')}</Label>
                            <Select value={formData.court_id} onValueChange={(value) => updateField('court_id', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue placeholder={t('Select Court')} />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {(courts || []).map((court: any) => (
                                        <SelectItem key={court.id} value={court.id.toString()}>
                                            {court.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('court_id')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label>{t('Case Main Category')}</Label>
                            <Select
                                value={formData.case_category_id}
                                onValueChange={(value) => {
                                    updateField('case_category_id', value);
                                    updateField('case_subcategory_id', '');
                                }}
                            >
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue placeholder={t('Select Main Category')} />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {(caseCategories || []).map((category: any) => (
                                        <SelectItem key={category.id} value={category.id.toString()}>
                                            {resolveTranslatableName(category)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('case_category_id')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Case Sub Category')}</Label>
                            <Select
                                value={formData.case_subcategory_id}
                                onValueChange={(value) => updateField('case_subcategory_id', value)}
                                disabled={!formData.case_category_id || isSubcategoryLoading}
                            >
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue
                                        placeholder={
                                            isSubcategoryLoading
                                                ? t('Loading...')
                                                : formData.case_category_id
                                                  ? t('Select Sub Category')
                                                  : t('Select Main Category First')
                                        }
                                    />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    {subcategoryOptions.map((sub) => (
                                        <SelectItem key={sub.value} value={sub.value}>
                                            {sub.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('case_subcategory_id')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="estimated_value">{t('Estimated Value')}</Label>
                            <Input
                                id="estimated_value"
                                type="number"
                                min="0"
                                step="0.01"
                                value={formData.estimated_value}
                                onChange={(e) => updateField('estimated_value', e.target.value)}
                            />
                            {renderError('estimated_value')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label htmlFor="filing_date">{t('Filling Date')}</Label>
                            <Input
                                id="filing_date"
                                type="date"
                                value={formData.filing_date}
                                onChange={(e) => updateField('filing_date', e.target.value)}
                            />
                            {renderError('filing_date')}
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="expected_completion_date">{t('Expecting Completion')}</Label>
                            <Input
                                id="expected_completion_date"
                                type="date"
                                value={formData.expected_completion_date}
                                onChange={(e) => updateField('expected_completion_date', e.target.value)}
                            />
                            {renderError('expected_completion_date')}
                        </div>
                        <div className="space-y-2">
                            <Label>{t('Status')}</Label>
                            <Select value={formData.status} onValueChange={(value) => updateField('status', value)}>
                                <SelectTrigger dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent dir={isRtl ? 'rtl' : 'ltr'}>
                                    <SelectItem value="active">{t('Active')}</SelectItem>
                                    <SelectItem value="inactive">{t('Inactive')}</SelectItem>
                                </SelectContent>
                            </Select>
                            {renderError('status')}
                        </div>
                    </div>

                    <div className="col-span-full my-6 h-px bg-gray-200 dark:bg-gray-800" />

                    <div className="space-y-2">
                        <Label htmlFor="description">{t('Description')}</Label>
                        <Textarea id="description" value={formData.description} onChange={(e) => updateField('description', e.target.value)} />
                        {renderError('description')}
                    </div>

                    {googleCalendarEnabled && (
                        <div className="flex items-center gap-3">
                            <Switch
                                id="sync_with_google_calendar"
                                checked={Boolean(formData.sync_with_google_calendar)}
                                onCheckedChange={(value) => updateField('sync_with_google_calendar', value)}
                            />
                            <Label htmlFor="sync_with_google_calendar">{t('Synchronize in Google Calendar')}</Label>
                            {renderError('sync_with_google_calendar')}
                        </div>
                    )}
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-6 dark:border-gray-800">
                    <h2 className="text-lg font-semibold">{t('Opposite Party')}</h2>
                    <div className="mt-4 space-y-4 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <Repeater
                            fields={oppositePartyFields}
                            value={formData.opposite_parties}
                            onChange={(value) => updateField('opposite_parties', value)}
                            minItems={1}
                            maxItems={-1}
                            addButtonText={t('Add Opposite Party')}
                            removeButtonText={t('Remove')}
                            showItemNumbers={false}
                            className="space-y-3"
                            itemClassName="bg-white border-slate-200"
                        />
                        {oppositePartyErrorKey && (
                            <p className="text-xs text-red-500">{normalizedErrors[oppositePartyErrorKey]}</p>
                        )}
                    </div>
                </div>

                <div className="sticky bottom-0 -mx-6 mt-6 border-t border-slate-200 bg-white px-6 py-4">
                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={() => router.get(route('cases.show', caseId))}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit">{t('Save')}</Button>
                    </div>
                </div>
            </form>
        </PageTemplate>
    );
}
