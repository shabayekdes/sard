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
import DependentDropdown from '@/components/DependentDropdown';
import { useLayout } from '@/contexts/LayoutContext';
import { router, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function CreateCase() {
    const { t, i18n } = useTranslation();
    const {
        auth,
        clients,
        caseTypes,
        caseCategories,
        caseStatuses,
        courts,
        countries,
        documentTypes,
        googleCalendarEnabled,
        planLimits,
        errors = {},
        base_url,
    } = usePage().props as any;
    const { isRtl } = useLayout();
    const canCreate = !planLimits || planLimits.can_create;
    const currentLocale = i18n.language || 'en';

    useEffect(() => {
        const handleLanguageChange = () => {
            router.get(route('cases.create'), {}, { preserveState: true, preserveScroll: true });
        };

        window.addEventListener('languageChanged', handleLanguageChange);
        i18n.on('languageChanged', handleLanguageChange);
        return () => {
            window.removeEventListener('languageChanged', handleLanguageChange);
            i18n.off('languageChanged', handleLanguageChange);
        };
    }, [i18n]);

    const [formData, setFormData] = useState(() => ({
        client_id: '',
        attributes: 'petitioner',
        opposite_parties: [],
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
        documents: [],
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

    const uniqueClients = useMemo(() => {
        const seen = new Set<string>();
        return (clients || []).filter((c: any) => {
            const id = c.id.toString();
            if (seen.has(id)) return false;
            seen.add(id);
            return true;
        });
    }, [clients]);

    const uniqueCaseStatuses = useMemo(() => {
        const seen = new Set<string>();
        return (caseStatuses || []).filter((s: any) => {
            const id = s.id.toString();
            if (seen.has(id)) return false;
            seen.add(id);
            return true;
        });
    }, [caseStatuses]);

    const uniqueCourts = useMemo(() => {
        const seen = new Set<string>();
        return (courts || []).filter((c: any) => {
            const id = c.id.toString();
            if (seen.has(id)) return false;
            seen.add(id);
            return true;
        });
    }, [courts]);

    const categorySubcategoryTypeFields = useMemo(
        () => [
            {
                name: 'case_category_id',
                label: t('Case Main Category'),
                options: (caseCategories || []).map((cat: any) => ({
                    value: cat.id.toString(),
                    label: resolveTranslatableName(cat),
                })),
            },
            {
                name: 'case_subcategory_id',
                label: t('Case Sub Category'),
                apiEndpoint: '/case/case-categories/{case_category_id}/subcategories',
            },
            {
                name: 'case_type_id',
                label: t('Case Type'),
                apiEndpoint: '/case/case-categories/{case_subcategory_id}/case-types',
            },
        ],
        [caseCategories, currentLocale, t]
    );

    const updateField = (field: string, value: any) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
    };

    const handleFormSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!canCreate) {
            toast.error(
                t('Case limit exceeded. Your plan allows maximum {{max}} cases. Please upgrade your plan.', {
                    max: planLimits?.max_cases,
                }),
            );
            return;
        }

        const filteredDocuments = (formData.documents || []).filter(
            (doc: any) => doc?.document_name && doc?.document_type_id && doc?.confidentiality && doc?.file,
        );

        const payload = {
            ...formData,
            case_category_id: formData.case_category_id === '' ? null : formData.case_category_id,
            case_subcategory_id: formData.case_subcategory_id === '' ? null : formData.case_subcategory_id,
            documents: filteredDocuments,
        };

        router.post(route('cases.store'), payload, {
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
                    toast.error(t('Failed to create {{model}}: {{errors}}', { model: t('Case'), errors: Object.values(formErrors).join(', ') }));
                }
            },
        });
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Case Management'), href: route('cases.index') },
        { title: t('Cases'), href: route('cases.index') },
        { title: t('Add Case') },
    ];

    const renderError = (field: string) => (normalizedErrors[field] ? <p className="text-xs text-red-500">{normalizedErrors[field]}</p> : null);

    const oppositePartyErrorKey = Object.keys(normalizedErrors).find((key) => key.startsWith('opposite_parties'));

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

    const documentTypeOptions = (documentTypes || []).map((type: any) => ({
        value: type.id.toString(),
        label: resolveTranslatableName(type),
    }));
    const caseDocumentFields: RepeaterField[] = [
        { name: 'document_name', label: t('Document Name'), type: 'text', required: true },
        {
            name: 'document_type_id',
            label: t('Document Type'),
            type: 'select',
            required: true,
            options: documentTypeOptions,
            placeholder: t('Select Document Type'),
        },
        {
            name: 'confidentiality',
            label: t('Confidentiality Level'),
            type: 'select',
            required: true,
            options: [
                { value: 'public', label: t('Public') },
                { value: 'confidential', label: t('Confidential') },
                { value: 'privileged', label: t('Privileged') },
            ],
            placeholder: t('Select {{label}}', { label: t('Confidentiality Level') }),
        },
        { name: 'file', label: t('Upload New Document'), type: 'media-picker', required: true },
    ];

    return (
        <PageTemplate title={t('Add New Case')} url="/cases" breadcrumbs={breadcrumbs} noPadding>
            <form onSubmit={handleFormSubmit}>
                <div className="mb-6 rounded-lg border border-slate-200 bg-white p-6 dark:border-gray-800">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label>{t('Client')}</Label>
                            <Select value={formData.client_id} onValueChange={(value) => updateField('client_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Client')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {uniqueClients.map((client: any) => (
                                        <SelectItem key={`client-${client.id}`} value={client.id.toString()}>
                                            {client.name}
                                        </SelectItem>
                                    ))}
                                    {auth?.user && !uniqueClients.some((c: any) => c.id === auth.user?.id) && (
                                        <SelectItem key={`client-me-${auth.user.id}`} value={auth.user.id.toString()}>{auth.user.name} (Me)</SelectItem>
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
                                    <Label htmlFor="attributes_petitioner" className="font-normal">
                                        {t('Petitioner')}
                                    </Label>
                                </div>
                                <div className={isRtl ? 'flex flex-row-reverse items-center gap-2' : 'flex items-center gap-2'}>
                                    <RadioGroupItem value="respondent" id="attributes_respondent" />
                                    <Label htmlFor="attributes_respondent" className="font-normal">
                                        {t('Respondent')}
                                    </Label>
                                </div>
                            </RadioGroup>
                            {renderError('attributes')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div className="space-y-2">
                            <Label>{t('Case Status')}</Label>
                            <Select value={formData.case_status_id} onValueChange={(value) => updateField('case_status_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Status')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {uniqueCaseStatuses.map((status: any) => (
                                        <SelectItem key={`case-status-${status.id}`} value={status.id.toString()}>
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
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="low">{t('Low')}</SelectItem>
                                    <SelectItem value="medium">{t('Medium')}</SelectItem>
                                    <SelectItem value="high">{t('High')}</SelectItem>
                                </SelectContent>
                            </Select>
                            {renderError('priority')}
                        </div>
                    </div>

                    <div className="col-span-full my-6 h-px space-y-2 bg-gray-200 dark:bg-gray-800" />

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
                                <SelectTrigger>
                                    <SelectValue placeholder={t('Select Court')} />
                                </SelectTrigger>
                                <SelectContent>
                                    {uniqueCourts.map((court: any) => (
                                        <SelectItem key={`court-${court.id}`} value={court.id.toString()}>
                                            {court.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {renderError('court_id')}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div className="space-y-2 md:col-span-3">
                            <DependentDropdown
                                layout="row"
                                fields={categorySubcategoryTypeFields}
                                values={{
                                    case_category_id: formData.case_category_id,
                                    case_subcategory_id: formData.case_subcategory_id,
                                    case_type_id: formData.case_type_id,
                                }}
                                onChange={(fieldName, value) => updateField(fieldName, value)}
                                errors={{
                                    case_category_id: normalizedErrors.case_category_id,
                                    case_subcategory_id: normalizedErrors.case_subcategory_id,
                                    case_type_id: normalizedErrors.case_type_id,
                                }}
                            />
                            {(normalizedErrors.case_category_id || normalizedErrors.case_subcategory_id || normalizedErrors.case_type_id) && (
                                <p className="text-xs text-red-500">
                                    {normalizedErrors.case_category_id || normalizedErrors.case_subcategory_id || normalizedErrors.case_type_id}
                                </p>
                            )}
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

                    <div className="col-span-full my-6 h-px space-y-2 bg-gray-200 dark:bg-gray-800" />

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
                        {oppositePartyErrorKey && <p className="text-xs text-red-500">{normalizedErrors[oppositePartyErrorKey]}</p>}
                    </div>
                </div>

                <div className="mt-6 rounded-lg border border-slate-200 bg-white p-6 dark:border-gray-800">
                    <h2 className="text-lg font-semibold">{t('Case Documents')}</h2>
                    <div className="mt-4 space-y-4 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <Repeater
                            fields={caseDocumentFields}
                            value={formData.documents}
                            onChange={(value) => updateField('documents', value)}
                            minItems={0}
                            maxItems={-1}
                            addButtonText={t('Add New Document')}
                            removeButtonText={t('Remove')}
                            showItemNumbers={false}
                            className="space-y-3"
                            itemClassName="bg-white border-slate-200"
                        />
                        {Object.keys(normalizedErrors).some((k) => k.startsWith('documents')) && (
                            <p className="text-xs text-red-500">
                                {normalizedErrors['documents.0.document_name'] ||
                                    normalizedErrors['documents.0.file'] ||
                                    normalizedErrors['documents.0.document_type_id'] ||
                                    normalizedErrors['documents.0.confidentiality'] ||
                                    t('Please fill all required document fields.')}
                            </p>
                        )}
                    </div>
                </div>

                <div className="sticky bottom-0 -mx-6 mt-6 border-t border-slate-200 bg-white px-6 py-4">
                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={() => router.get(route('cases.index'))}>
                            {t('Cancel')}
                        </Button>
                        <Button type="submit" disabled={!canCreate}>
                            {t('Save')}
                        </Button>
                    </div>
                </div>
            </form>
        </PageTemplate>
    );
}
