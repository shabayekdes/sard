import MediaPicker from '@/components/MediaPicker';
import { QuickCourtFormModal } from '@/components/QuickCourtFormModal';
import { CrudFormModal } from '@/components/CrudFormModal';
import { GregorianHijriDateField } from '@/components/GregorianHijriDateField';
import { toast } from '@/components/custom-toast';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Repeater, type RepeaterField } from '@/components/ui/repeater';
import { hasPermission } from '@/utils/authorization';
import { router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import type { PageProps } from '@/types/page-props';

type ReferralRow = Record<string, unknown>;

const buildDefaultForm = (): Record<string, unknown> => ({
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

export interface CaseReferralFormModalProps {
    caseId: number;
    open: boolean;
    onClose: () => void;
    mode: 'create' | 'edit';
    editRow?: ReferralRow | null;
    courts: unknown[];
    courtTypes?: unknown[];
    circleTypes?: unknown[];
    permissions: string[];
}

export function CaseReferralFormModal({
    caseId,
    open,
    onClose,
    mode,
    editRow,
    courts,
    courtTypes = [],
    circleTypes = [],
    permissions,
}: CaseReferralFormModalProps) {
    const { t } = useTranslation();
    const { caseReferralStageDefs } = usePage<PageProps>().props;
    const [form, setForm] = useState<Record<string, unknown>>(buildDefaultForm());
    const [courtModalOpen, setCourtModalOpen] = useState(false);

    const referralStages = useMemo(
        () =>
            caseReferralStageDefs.map((s) => ({
                ...s,
                label: t(`case_referral.stage.${s.key}`),
            })),
        [caseReferralStageDefs, t],
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

    useEffect(() => {
        if (!open) {
            return;
        }
        if (mode === 'create') {
            setForm(buildDefaultForm());
            return;
        }
        if (mode === 'edit' && editRow) {
            const row = editRow as Record<string, any>;
            const reminderDuration = row.reminder_duration || '';
            const reminderOption =
                reminderDuration === 1 || reminderDuration === '1'
                    ? '1'
                    : reminderDuration === 3 || reminderDuration === '3'
                      ? '3'
                      : reminderDuration === 7 || reminderDuration === '7'
                        ? '7'
                        : 'custom';

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
        }
    }, [open, mode, editRow?.id]);

    const referralCrudInitialData = useMemo(
        () => ({
            id: mode === 'edit' && editRow != null ? (editRow as { id?: number }).id : undefined,
            stage: (form.stage as string) ?? '',
            notes: (form.notes as string) ?? '',
        }),
        [mode, editRow, form.stage, form.notes, open],
    );

    const setField = (name: string, value: unknown) => setForm((prev) => ({ ...prev, [name]: value }));
    const setStageField = (name: string, value: unknown) =>
        setForm((prev) => ({
            ...prev,
            stage_data: { ...(prev.stage_data as Record<string, unknown>), [name]: value },
        }));
    const setExecutionPeople = (key: 'requesters' | 'respondents', value: unknown[]) =>
        setForm((prev) => ({
            ...prev,
            stage_data: { ...(prev.stage_data as Record<string, unknown>), [key]: value },
        }));

    const onStageChange = (value: string) => {
        setForm((prev) => ({
            ...prev,
            stage: value,
            stage_data: {},
        }));
    };

    const submit = (modalData?: Record<string, unknown>) => {
        const stage = (modalData?.stage ?? form.stage) as string;
        const notes = modalData && 'notes' in modalData ? modalData.notes : form.notes;

        const computedReminderDuration = !form.reminder_enabled
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

        const payload: Record<string, unknown> = {
            stage,
            referral_date: form.referral_date,
            reminder_enabled: form.reminder_enabled,
            reminder_duration: computedReminderDuration,
            notes: (notes === '' || notes == null ? null : notes) as string | null,
            attachments: String(form.attachments || '')
                .split(',')
                .map((v: string) => v.trim())
                .filter(Boolean),
            stage_data: form.stage_data,
        };

        const onSuccess = () => {
            onClose();
            toast.success(mode === 'create' ? t('case_referral.toast.created') : t('case_referral.toast.updated'));
        };

        if (mode === 'create') {
            router.post(route('cases.referrals.store', caseId), payload, { preserveScroll: true, onSuccess });
        } else {
            const id = (editRow as { id?: number })?.id;
            router.put(route('cases.referrals.update', [caseId, id]), payload, { preserveScroll: true, onSuccess });
        }
    };

    const renderStageFields = () => {
        const courtSelect = (
            <div className="space-y-2">
                <Label>{t('case_referral.label.court')}</Label>
                <div className="flex gap-2">
                    <div className="min-w-0 flex-1">
                        <Select
                            value={String((form.stage_data as Record<string, unknown>)?.court_id || '')}
                            onValueChange={(v) => setStageField('court_id', v)}
                        >
                            <SelectTrigger className="h-10 w-full">
                                <SelectValue placeholder={t('case_referral.placeholder.select_court')} />
                            </SelectTrigger>
                            <SelectContent>
                                {(courts as { id: number; name: string }[])?.map((c) => (
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

        const sd = (form.stage_data || {}) as Record<string, unknown>;

        if (form.stage === 'amicable_settlement') {
            return (
                <>
                    <Input
                        placeholder={t('case_referral.placeholder.case_number')}
                        value={(sd.case_number as string) || ''}
                        onChange={(e) => setStageField('case_number', e.target.value)}
                    />
                    <GregorianHijriDateField value={(sd.case_date as string) || ''} onChange={(v) => setStageField('case_date', v)} />
                </>
            );
        }
        if (form.stage === 'reconciliation') {
            return (
                <div className="space-y-3">
                    <Input
                        placeholder={t('case_referral.placeholder.reconciliation_case_number')}
                        value={(sd.reconciliation_case_number as string) || ''}
                        onChange={(e) => setStageField('reconciliation_case_number', e.target.value)}
                    />
                    <GregorianHijriDateField
                        value={(sd.reconciliation_case_date as string) || ''}
                        onChange={(v) => setStageField('reconciliation_case_date', v)}
                    />
                    <Input
                        placeholder={t('case_referral.placeholder.minutes_number')}
                        value={(sd.minutes_number as string) || ''}
                        onChange={(e) => setStageField('minutes_number', e.target.value)}
                    />
                    <GregorianHijriDateField value={(sd.minutes_date as string) || ''} onChange={(v) => setStageField('minutes_date', v)} />
                </div>
            );
        }
        if (form.stage === 'first_instance' || form.stage === 'appeal') {
            return (
                <div className="space-y-3">
                    <Input
                        placeholder={t('case_referral.placeholder.case_type')}
                        value={(sd.case_type as string) || ''}
                        onChange={(e) => setStageField('case_type', e.target.value)}
                    />
                    <GregorianHijriDateField value={(sd.filing_date as string) || ''} onChange={(v) => setStageField('filing_date', v)} />
                    {courtSelect}
                    <Input
                        placeholder={t('case_referral.placeholder.case_number')}
                        value={(sd.case_number as string) || ''}
                        onChange={(e) => setStageField('case_number', e.target.value)}
                    />
                    <GregorianHijriDateField value={(sd.case_date as string) || ''} onChange={(v) => setStageField('case_date', v)} />
                </div>
            );
        }
        if (form.stage === 'supreme_court') {
            return (
                <div className="space-y-3">
                    <Input
                        placeholder={t('case_referral.placeholder.case_number')}
                        value={(sd.case_number as string) || ''}
                        onChange={(e) => setStageField('case_number', e.target.value)}
                    />
                    <GregorianHijriDateField value={(sd.case_date as string) || ''} onChange={(v) => setStageField('case_date', v)} />
                </div>
            );
        }
        if (form.stage === 'execution') {
            const requesters = Array.isArray(sd.requesters) ? sd.requesters : [];
            const respondents = Array.isArray(sd.respondents) ? sd.respondents : [];

            return (
                <div className="space-y-3">
                    <div className="rounded-md border p-3">
                        <h5 className="mb-2 text-sm font-semibold">{t('case_referral.section.execution_court')}</h5>
                        {courtSelect}
                    </div>

                    <div className="rounded-md border p-3">
                        <h5 className="mb-2 text-sm font-semibold">{t('case_referral.section.execution_request')}</h5>
                        <div className="space-y-3">
                            <Input
                                placeholder={t('case_referral.placeholder.request_number')}
                                value={(sd.request_number as string) || ''}
                                onChange={(e) => setStageField('request_number', e.target.value)}
                            />
                            <div className="space-y-2">
                                <Label>{t('case_referral.label.request_date')}</Label>
                                <GregorianHijriDateField value={(sd.request_date as string) || ''} onChange={(v) => setStageField('request_date', v)} />
                            </div>
                            <Input
                                placeholder={t('case_referral.placeholder.request_type')}
                                value={(sd.request_type as string) || ''}
                                onChange={(e) => setStageField('request_type', e.target.value)}
                            />
                            <Input
                                placeholder={t('case_referral.placeholder.request_value')}
                                value={(sd.request_value as string) || ''}
                                onChange={(e) => setStageField('request_value', e.target.value)}
                            />
                            <Select value={(sd.request_status as string) || ''} onValueChange={(v) => setStageField('request_status', v)}>
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
                            <Input
                                placeholder={t('case_referral.placeholder.bond_type')}
                                value={(sd.bond_type as string) || ''}
                                onChange={(e) => setStageField('bond_type', e.target.value)}
                            />
                            <Input
                                placeholder={t('case_referral.placeholder.bond_number')}
                                value={(sd.bond_number as string) || ''}
                                onChange={(e) => setStageField('bond_number', e.target.value)}
                            />
                            <div className="space-y-2">
                                <Label>{t('case_referral.label.bond_date')}</Label>
                                <GregorianHijriDateField value={(sd.bond_date as string) || ''} onChange={(v) => setStageField('bond_date', v)} />
                            </div>
                            <Input
                                placeholder={t('case_referral.placeholder.bond_value')}
                                value={(sd.bond_value as string) || ''}
                                onChange={(e) => setStageField('bond_value', e.target.value)}
                            />
                            <Input
                                placeholder={t('case_referral.placeholder.bond_case_number')}
                                value={(sd.bond_case_number as string) || ''}
                                onChange={(e) => setStageField('bond_case_number', e.target.value)}
                            />
                            <Input
                                placeholder={t('case_referral.placeholder.bond_case_year')}
                                value={(sd.bond_case_year as string) || ''}
                                onChange={(e) => setStageField('bond_case_year', e.target.value)}
                            />
                        </div>
                    </div>

                    <div className="rounded-md border p-3">
                        <h5 className="mb-3 text-sm font-semibold">{t('case_referral.section.requesters')}</h5>
                        <Repeater
                            fields={executionPartyRepeaterFields}
                            value={requesters as Record<string, unknown>[]}
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
                            value={respondents as Record<string, unknown>[]}
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
                </div>
            );
        }
        return null;
    };

    return (
        <>
            <CrudFormModal
                isOpen={open}
                onClose={onClose}
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
                            render: () => (
                                <GregorianHijriDateField
                                    value={(form.referral_date as string) || ''}
                                    onChange={(v) => setField('referral_date', v)}
                                />
                            ),
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
                                                value={form.reminder_duration as string | number}
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
                                <div className={`rounded-lg p-3 ${form.stage ? 'border border-input' : ''}`}>
                                    {form.stage ? (
                                        renderStageFields()
                                    ) : (
                                        <p className="text-sm text-muted-foreground">{t('case_referral.helper.select_stage_for_details')}</p>
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
                                    value={form.attachments as string}
                                    onChange={(v) => setField('attachments', v)}
                                    placeholder={t('case_referral.placeholder.select_files')}
                                />
                            ),
                        },
                    ],
                }}
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
        </>
    );
}
