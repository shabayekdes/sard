import MediaPicker from '@/components/MediaPicker';
import { QuickCourtFormModal } from '@/components/QuickCourtFormModal';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { GregorianHijriDateField } from '@/components/GregorianHijriDateField';
import { toast } from '@/components/custom-toast';
import { CrudTable } from '@/components/CrudTable';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Repeater, type RepeaterField } from '@/components/ui/repeater';
import type { TableColumn } from '@/types/crud';
import { hasPermission } from '@/utils/authorization';
import { router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

/** Stage keys + badge styles; labels come from i18n (`case_referral.stage.*`). */
export const REFERRAL_STAGE_DEFS = [
    { key: 'amicable_settlement', badgeClass: 'bg-blue-100 text-blue-700' },
    { key: 'reconciliation', badgeClass: 'bg-emerald-100 text-emerald-700' },
    { key: 'first_instance', badgeClass: 'bg-amber-100 text-amber-700' },
    { key: 'appeal', badgeClass: 'bg-purple-100 text-purple-700' },
    { key: 'supreme_court', badgeClass: 'bg-indigo-100 text-indigo-700' },
    { key: 'execution', badgeClass: 'bg-rose-100 text-rose-700' },
] as const;

type ReferralRow = any;

const buildDefaultForm = (): any => ({
    stage: '',
    referral_date: '',
    reminder_enabled: false,
    reminder_duration: '',
    reminder_option: 'custom',
    notes: '',
    attachments: '',
    stage_data: {
        requesters: [],
        respondents: [],
    },
});

export function CaseReferralsSection({
    caseId,
    referrals,
    courts,
    courtTypes = [],
    circleTypes = [],
    permissions,
}: {
    caseId: number;
    referrals: any;
    courts: any[];
    courtTypes?: unknown[];
    circleTypes?: unknown[];
    permissions: string[];
}) {
    const { t } = useTranslation();

    const referralStages = useMemo(
        () =>
            REFERRAL_STAGE_DEFS.map((s) => ({
                ...s,
                label: t(`case_referral.stage.${s.key}`),
            })),
        [t],
    );

    const executionPartyRepeaterFields = useMemo<RepeaterField[]>(
        () => [
            { name: 'name', label: t('case_referral.execution_party.name'), type: 'text', placeholder: t('case_referral.execution_party.name') },
            {
                name: 'national_id',
                label: t('case_referral.execution_party.national_id'),
                type: 'text',
                placeholder: t('case_referral.execution_party.national_id'),
            },
        ],
        [t],
    );

    const canQuickCreateCourt = hasPermission(permissions, 'create-courts');
    const [courtModalOpen, setCourtModalOpen] = useState(false);
    const [isOpen, setIsOpen] = useState(false);
    const [isViewOpen, setIsViewOpen] = useState(false);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [mode, setMode] = useState<'create' | 'edit'>('create');
    const [current, setCurrent] = useState<ReferralRow | null>(null);
    const [form, setForm] = useState<any>(buildDefaultForm());

    const stageMeta = useMemo(() => Object.fromEntries(referralStages.map((s) => [s.key, s])), [referralStages]);
    /** Maps parent `form` into CrudFormModal field names so selects/textareas initialize (esp. edit mode). */
    const referralCrudInitialData = useMemo(
        () => ({
            id: mode === 'edit' && current != null ? current.id : undefined,
            stage: form.stage ?? '',
            notes: form.notes ?? '',
        }),
        [mode, current, form.stage, form.notes, isOpen],
    );
    const rows = referrals?.data || [];

    const setField = (name: string, value: any) => setForm((prev: any) => ({ ...prev, [name]: value }));
    const setStageField = (name: string, value: any) => setForm((prev: any) => ({ ...prev, stage_data: { ...prev.stage_data, [name]: value } }));
    const setExecutionPeople = (key: 'requesters' | 'respondents', value: any[]) =>
        setForm((prev: any) => ({ ...prev, stage_data: { ...prev.stage_data, [key]: value } }));

    const openCreate = () => {
        setMode('create');
        setCurrent(null);
        setForm(buildDefaultForm());
        setIsOpen(true);
    };

    const openEdit = (row: any) => {
        const reminderDuration = row.reminder_duration || '';
        const reminderOption = reminderDuration === 1 || reminderDuration === '1'
            ? '1'
            : reminderDuration === 3 || reminderDuration === '3'
                ? '3'
                : reminderDuration === 7 || reminderDuration === '7'
                    ? '7'
                    : 'custom';

        setMode('edit');
        setCurrent(row);
        setForm({
            stage: row.stage,
            referral_date: row.referral_date || '',
            reminder_enabled: !!row.reminder_enabled,
            reminder_duration: reminderDuration,
            reminder_option: reminderOption,
            notes: row.notes || '',
            attachments: Array.isArray(row.attachments) ? row.attachments.join(',') : '',
            stage_data: {
                requesters: row.stage_data?.requesters || [],
                respondents: row.stage_data?.respondents || [],
                ...(row.stage_data || {}),
            },
        });
        setIsOpen(true);
    };

    const onStageChange = (value: string) => {
        setForm((prev: any) => ({
            ...prev,
            stage: value,
            stage_data: {},
        }));
    };

    const submit = (modalData?: Record<string, any>) => {
        const stage = modalData?.stage ?? form.stage;
        const notes = modalData && 'notes' in modalData ? modalData.notes : form.notes;

        const computedReminderDuration =
            !form.reminder_enabled
                ? null
                : form.reminder_option === '1'
                    ? 1
                    : form.reminder_option === '3'
                        ? 3
                        : form.reminder_option === '7'
                            ? 7
                            : form.reminder_duration
                                ? Number(form.reminder_duration)
                                : null;

        const payload: any = {
            stage,
            referral_date: form.referral_date,
            reminder_enabled: form.reminder_enabled,
            reminder_duration: computedReminderDuration,
            notes: (notes === '' || notes == null ? null : notes) as string | null,
            attachments: (form.attachments || '').split(',').map((v: string) => v.trim()).filter(Boolean),
            stage_data: form.stage_data,
        };

        const onSuccess = () => {
            setIsOpen(false);
            toast.success(mode === 'create' ? t('case_referral.toast.created') : t('case_referral.toast.updated'));
        };

        if (mode === 'create') {
            router.post(route('cases.referrals.store', caseId), payload, { preserveScroll: true, onSuccess });
        } else {
            router.put(route('cases.referrals.update', [caseId, current?.id]), payload, { preserveScroll: true, onSuccess });
        }
    };

    const submitDelete = () => {
        if (!current) return;
        router.delete(route('cases.referrals.destroy', [caseId, current.id]), {
            preserveScroll: true,
            onSuccess: () => {
                setIsDeleteOpen(false);
                toast.success(t('case_referral.toast.deleted'));
            },
        });
    };

    const renderStageFields = () => {
        const courtSelect = (
            <div className="space-y-2">
                <Label>{t('case_referral.label.court')}</Label>
                <div className="flex gap-2">
                    <div className="min-w-0 flex-1">
                        <Select value={String(form.stage_data.court_id || '')} onValueChange={(v) => setStageField('court_id', v)}>
                            <SelectTrigger className="h-10 w-full">
                                <SelectValue placeholder={t('case_referral.placeholder.select_court')} />
                            </SelectTrigger>
                            <SelectContent>
                                {courts?.map((c: any) => (
                                    <SelectItem key={c.id} value={String(c.id)}>
                                        {c.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    {canQuickCreateCourt && (
                        <Button
                            type="button"
                            variant="outline"
                            className="h-10 shrink-0"
                            onClick={() => setCourtModalOpen(true)}
                            aria-label={t('Add court')}
                        >
                            <Plus className="h-4 w-4" />
                        </Button>
                    )}
                </div>
            </div>
        );

        if (form.stage === 'amicable_settlement') {
            return (
                <>
                    <Input placeholder={t('case_referral.placeholder.case_number')} value={form.stage_data.case_number || ''} onChange={(e) => setStageField('case_number', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.case_date || ''} onChange={(v) => setStageField('case_date', v)} />
                </>
            );
        }
        if (form.stage === 'reconciliation') {
            return (
                <div className="space-y-3">
                    <Input placeholder={t('case_referral.placeholder.reconciliation_case_number')} value={form.stage_data.reconciliation_case_number || ''} onChange={(e) => setStageField('reconciliation_case_number', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.reconciliation_case_date || ''} onChange={(v) => setStageField('reconciliation_case_date', v)} />
                    <Input placeholder={t('case_referral.placeholder.minutes_number')} value={form.stage_data.minutes_number || ''} onChange={(e) => setStageField('minutes_number', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.minutes_date || ''} onChange={(v) => setStageField('minutes_date', v)} />
                </div>
            );
        }
        if (form.stage === 'first_instance' || form.stage === 'appeal') {
            return (
                <div className="space-y-3">
                    <Input placeholder={t('case_referral.placeholder.case_type')} value={form.stage_data.case_type || ''} onChange={(e) => setStageField('case_type', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.filing_date || ''} onChange={(v) => setStageField('filing_date', v)} />
                    {courtSelect}
                    <Input placeholder={t('case_referral.placeholder.case_number')} value={form.stage_data.case_number || ''} onChange={(e) => setStageField('case_number', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.case_date || ''} onChange={(v) => setStageField('case_date', v)} />
                </div>
            );
        }
        if (form.stage === 'supreme_court') {
            return (
                <div className="space-y-3">
                    <Input placeholder={t('case_referral.placeholder.case_number')} value={form.stage_data.case_number || ''} onChange={(e) => setStageField('case_number', e.target.value)} />
                    <GregorianHijriDateField value={form.stage_data.case_date || ''} onChange={(v) => setStageField('case_date', v)} />
                </div>
            );
        }
        if (form.stage === 'execution') {
            const requesters = Array.isArray(form.stage_data.requesters) ? form.stage_data.requesters : [];
            const respondents = Array.isArray(form.stage_data.respondents) ? form.stage_data.respondents : [];

            return <div className="space-y-3">
                <div className="rounded-md border p-3">
                    <h5 className="mb-2 text-sm font-semibold">{t('case_referral.section.execution_court')}</h5>
                    {courtSelect}
                </div>

                <div className="rounded-md border p-3">
                    <h5 className="mb-2 text-sm font-semibold">{t('case_referral.section.execution_request')}</h5>
                    <div className="space-y-3">
                        <Input placeholder={t('case_referral.placeholder.request_number')} value={form.stage_data.request_number || ''} onChange={(e) => setStageField('request_number', e.target.value)} />
                        <div className="space-y-2">
                            <Label>{t('case_referral.label.request_date')}</Label>
                            <GregorianHijriDateField value={form.stage_data.request_date || ''} onChange={(v) => setStageField('request_date', v)} />
                        </div>
                        <Input placeholder={t('case_referral.placeholder.request_type')} value={form.stage_data.request_type || ''} onChange={(e) => setStageField('request_type', e.target.value)} />
                        <Input placeholder={t('case_referral.placeholder.request_value')} value={form.stage_data.request_value || ''} onChange={(e) => setStageField('request_value', e.target.value)} />
                        <Select value={form.stage_data.request_status || ''} onValueChange={(v) => setStageField('request_status', v)}>
                            <SelectTrigger>
                                <SelectValue placeholder={t('case_referral.placeholder.request_status')} />
                            </SelectTrigger>
                            <SelectContent>
                                {(['new', 'in_progress', 'suspended', 'completed', 'rejected'] as const).map((st) => (
                                    <SelectItem key={st} value={st}>
                                        {t(`case_referral.request_status.${st}`)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div className="rounded-md border p-3">
                    <h5 className="mb-2 text-sm font-semibold">{t('case_referral.section.execution_bond')}</h5>
                    <div className="space-y-3">
                        <Input placeholder={t('case_referral.placeholder.bond_type')} value={form.stage_data.bond_type || ''} onChange={(e) => setStageField('bond_type', e.target.value)} />
                        <Input placeholder={t('case_referral.placeholder.bond_number')} value={form.stage_data.bond_number || ''} onChange={(e) => setStageField('bond_number', e.target.value)} />
                        <div className="space-y-2">
                            <Label>{t('case_referral.label.bond_date')}</Label>
                            <GregorianHijriDateField value={form.stage_data.bond_date || ''} onChange={(v) => setStageField('bond_date', v)} />
                        </div>
                        <Input placeholder={t('case_referral.placeholder.bond_value')} value={form.stage_data.bond_value || ''} onChange={(e) => setStageField('bond_value', e.target.value)} />
                        <Input placeholder={t('case_referral.placeholder.bond_case_number')} value={form.stage_data.bond_case_number || ''} onChange={(e) => setStageField('bond_case_number', e.target.value)} />
                        <Input placeholder={t('case_referral.placeholder.bond_case_year')} value={form.stage_data.bond_case_year || ''} onChange={(e) => setStageField('bond_case_year', e.target.value)} />
                    </div>
                </div>

                <div className="rounded-md border p-3">
                    <h5 className="mb-3 text-sm font-semibold">{t('case_referral.section.requesters')}</h5>
                    <Repeater
                        fields={executionPartyRepeaterFields}
                        value={requesters}
                        onChange={(list) => setExecutionPeople('requesters', list)}
                        layout="table"
                        minItems={0}
                        maxItems={50}
                        addButtonText={t('case_referral.repeater.add_requester')}
                        removeButtonText={t('case_referral.repeater.remove')}
                        emptyMessage="case_referral.repeater.empty_requesters"
                        showItemNumbers={false}
                    />
                </div>

                <div className="rounded-md border p-3">
                    <h5 className="mb-3 text-sm font-semibold">{t('case_referral.section.respondents')}</h5>
                    <Repeater
                        fields={executionPartyRepeaterFields}
                        value={respondents}
                        onChange={(list) => setExecutionPeople('respondents', list)}
                        layout="table"
                        minItems={0}
                        maxItems={50}
                        addButtonText={t('case_referral.repeater.add_respondent')}
                        removeButtonText={t('case_referral.repeater.remove')}
                        emptyMessage="case_referral.repeater.empty_respondents"
                        showItemNumbers={false}
                    />
                </div>
            </div>;
        }
        return null;
    };

    const referralTableColumns = useMemo<TableColumn[]>(
        () => [
            {
                key: 'stage',
                label: t('case_referral.table.stage'),
                render: (_value: unknown, row: any) => (
                    <Badge className={stageMeta[row.stage]?.badgeClass || ''}>{stageMeta[row.stage]?.label || row.stage}</Badge>
                ),
            },
            {
                key: 'referral_date',
                label: t('case_referral.table.referral_date'),
                type: 'date',
            },
            {
                key: 'stage_case_number',
                label: t('case_referral.table.case_number'),
                render: (value: unknown) => (value == null || value === '' ? <span className="text-muted-foreground">—</span> : String(value)),
            },
            {
                key: 'stage_court_name',
                label: t('case_referral.table.court_name'),
                render: (value: unknown) => (value == null || value === '' ? <span className="text-muted-foreground">—</span> : String(value)),
            },
        ],
        [stageMeta, t],
    );

    const handleReferralTableAction = (action: string, row: any) => {
        if (action === 'view') {
            setCurrent(row);
            setIsViewOpen(true);
            return;
        }
        if (action === 'edit') {
            openEdit(row);
            return;
        }
        if (action === 'delete') {
            setCurrent(row);
            setIsDeleteOpen(true);
        }
    };

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{t('case_referral.section_title')}</h3>
                {hasPermission(permissions, 'manage-cases') && (
                    <button
                        type="button"
                        onClick={openCreate}
                        className="flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                    >
                        <Plus className="h-4 w-4" />
                        {t('case_referral.add_referral')}
                    </button>
                )}
            </div>

            <CrudTable
                columns={referralTableColumns}
                actions={[
                    { label: t('View'), icon: 'Eye', action: 'view', className: 'text-primary' },
                    { label: t('Edit'), icon: 'Edit', action: 'edit', className: 'text-amber-500' },
                    { label: t('Delete'), icon: 'Trash2', action: 'delete', className: 'text-red-500' },
                ]}
                data={rows}
                from={referrals?.from ?? 1}
                onAction={handleReferralTableAction}
                permissions={permissions}
                entityPermissions={{
                    view: 'manage-cases',
                    edit: 'manage-cases',
                    delete: 'manage-cases',
                }}
            />

            <CrudFormModal
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                onSubmit={(data) => submit(data)}
                mode={mode}
                title={mode === 'create' ? t('case_referral.modal.create_title') : t('case_referral.modal.edit_title')}
                initialData={referralCrudInitialData}
                formConfig={{
                    modalSize: '4xl',
                    fields: [
                        {
                            name: 'stage',
                            label: t('case_referral.field.referral_stage'),
                            type: 'select',
                            disabled: mode === 'edit',
                            options: referralStages.map((s) => ({ value: s.key, label: s.label })),
                            onChange: (value) => onStageChange(String(value)),
                        },
                        {
                            name: 'referral_date_field',
                            label: t('case_referral.field.referral_date'),
                            type: 'custom',
                            render: () => <GregorianHijriDateField value={form.referral_date} onChange={(v) => setField('referral_date', v)} />,
                        },
                        {
                            name: 'reminder_enabled_field',
                            label: t('case_referral.field.reminder_enabled'),
                            type: 'custom',
                            render: () => (
                                <div className="space-y-2">
                                    <div className="flex min-h-10 items-center px-3">
                                        <Switch
                                            checked={!!form.reminder_enabled}
                                            onCheckedChange={(v) => {
                                                setField('reminder_enabled', v);
                                                if (!v) {
                                                    setField('reminder_duration', '');
                                                    setField('reminder_option', 'custom');
                                                }
                                            }}
                                        />
                                    </div>
                                </div>
                            ),
                        },
                        {
                            name: 'reminder_options_field',
                            label: '',
                            type: 'custom',
                            conditional: () => !!form.reminder_enabled,
                            column: 2,
                            render: () => (
                                <div className="space-y-3">
                                    <Label>{t('case_referral.field.reminder_schedule')}</Label>
                                    <div className="flex flex-wrap gap-2">
                                        {[
                                            { value: '1', label: t('case_referral.reminder.one_day_before') },
                                            { value: '3', label: t('case_referral.reminder.three_days_before') },
                                            { value: '7', label: t('case_referral.reminder.one_week_before') },
                                            { value: 'custom', label: t('case_referral.reminder.custom') },
                                        ].map((opt) => (
                                            <button
                                                key={opt.value}
                                                type="button"
                                                onClick={() => {
                                                    setField('reminder_option', opt.value);
                                                    if (opt.value === '1' || opt.value === '3' || opt.value === '7') {
                                                        setField('reminder_duration', opt.value);
                                                    } else {
                                                        setField('reminder_duration', '');
                                                    }
                                                }}
                                                className={`rounded-full border px-3 py-1.5 text-sm font-medium transition-colors ${String(form.reminder_option || 'custom') === opt.value
                                                    ? 'border-primary bg-primary text-primary-foreground'
                                                    : 'border-input bg-background hover:bg-muted/60'
                                                    }`}
                                            >
                                                {opt.label}
                                            </button>
                                        ))}
                                    </div>
                                    {form.reminder_option === 'custom' && (
                                        <div className="max-w-xs space-y-2">
                                            <Label>{t('case_referral.field.reminder_days_min')}</Label>
                                            <Input
                                                type="number"
                                                min={1}
                                                value={form.reminder_duration}
                                                onChange={(e) => setField('reminder_duration', e.target.value)}
                                            />
                                        </div>
                                    )}
                                </div>
                            ),
                        },
                        {
                            name: 'stage_data_field',
                            label: t('case_referral.section.stage_details'),
                            type: 'custom',
                            column: 2,
                            render: () => (
                                <div
                                    className={`rounded-lg p-3 ${form.stage ? 'border border-input' : ''}`}
                                >
                                    {form.stage ? (
                                        renderStageFields()
                                    ) : (
                                        <p className="text-sm text-muted-foreground">
                                            {t('case_referral.helper.select_stage_for_details')}
                                        </p>
                                    )}
                                </div>
                            ),
                        },
                        {
                            name: 'notes',
                            label: t('case_referral.field.notes'),
                            type: 'textarea',
                            placeholder: t('case_referral.placeholder.notes'),
                            column: 2,
                        },
                        {
                            name: 'attachments',
                            label: t('case_referral.field.attachments'),
                            type: 'custom',
                            column: 2,
                            render: () => (
                                <MediaPicker
                                    multiple
                                    value={form.attachments}
                                    onChange={(v) => setField('attachments', v)}
                                    placeholder={t('case_referral.placeholder.select_files')}
                                />
                            ),
                        },
                    ],
                }}
            />

            <Dialog open={isViewOpen} onOpenChange={setIsViewOpen}>
                <DialogContent className="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>{t('case_referral.view_title')}</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-2 text-sm">
                        {current?.stage && (
                            <p>
                                <strong>{t('case_referral.label.stage')}</strong> {stageMeta[current.stage]?.label}
                            </p>
                        )}
                        {current?.referral_date && (
                            <p>
                                <strong>{t('case_referral.label.referral_date_row')}</strong>{' '}
                                {window.appSettings?.formatDate?.(current.referral_date) || current.referral_date}
                            </p>
                        )}
                        {current?.notes && (
                            <p>
                                <strong>{t('case_referral.label.notes_row')}</strong> {current.notes}
                            </p>
                        )}
                    </div>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setIsViewOpen(false)}>
                            {t('Close')}
                        </Button>
                        {current && (
                            <Button
                                onClick={() => {
                                    setIsViewOpen(false);
                                    openEdit(current);
                                }}
                            >
                                {t('Edit')}
                            </Button>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <CrudDeleteModal
                isOpen={isDeleteOpen}
                onClose={() => setIsDeleteOpen(false)}
                onConfirm={submitDelete}
                itemName={current?.id ? `#${current.id}` : t('case_referral.delete.item_fallback')}
                entityName={t('case_referral.delete.entity')}
                warningMessage={t('case_referral.delete.confirm')}
            />

            {canQuickCreateCourt && (
                <QuickCourtFormModal
                    open={courtModalOpen}
                    onOpenChange={setCourtModalOpen}
                    courtTypes={courtTypes}
                    circleTypes={circleTypes}
                    onCreated={(id) => setStageField('court_id', id)}
                    reloadOnly={['courts']}
                    title={t('Add court')}
                />
            )}
        </div>
    );
}
