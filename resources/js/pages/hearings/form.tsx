import { useCallback, useMemo, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { PageTemplate } from '@/components/page-template';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { CrudFormModal } from '@/components/CrudFormModal';
import { toast } from '@/components/custom-toast';
import { hasPermission } from '@/utils/authorization';
import { ArrowLeft, Plus } from 'lucide-react';

type DurationUnit = 'minutes' | 'hours';

function normalizeHearingTime(t: string | null | undefined) {
  if (t == null) return '';
  const s = String(t);
  const p = s.split(':');
  if (p.length >= 2) return `${p[0]!.padStart(2, '0')}:${p[1]!.padStart(2, '0')}`;
  return '';
}

export default function HearingForm() {
  const { t, i18n } = useTranslation();
  const props = usePage().props as {
    mode: 'create' | 'edit';
    hearing?: any;
    cases: any[];
    courts: any[];
    courtTypes: any[];
    circleTypes: any[];
    hearingTypes: any[];
    googleCalendarEnabled: boolean;
    prefillCaseId: number | null;
    hideCaseField: boolean;
    returnToCaseId: number | null;
    reminderMinutes: number | null;
  };
  const { auth } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

  const {
    mode,
    hearing,
    cases,
    courts,
    courtTypes,
    circleTypes,
    hearingTypes,
    googleCalendarEnabled,
    prefillCaseId,
    hideCaseField,
    returnToCaseId,
    reminderMinutes,
  } = props;

  const canQuickCreateCourt = hasPermission(permissions, 'create-courts');
  const [courtModalOpen, setCourtModalOpen] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const [durationUnit, setDurationUnit] = useState<DurationUnit>(() => {
    const m = hearing?.duration_minutes ?? 30;
    if (m >= 60 && m % 60 === 0) return 'hours';
    return 'minutes';
  });

  const [form, setForm] = useState(() => ({
    case_id:
      mode === 'edit' && hearing
        ? String(hearing.case_id)
        : prefillCaseId
          ? String(prefillCaseId)
          : '',
    court_id: hearing?.court_id != null ? String(hearing.court_id) : '',
    circle_number: hearing?.circle_number ?? '',
    hearing_type_id: hearing?.hearing_type_id != null ? String(hearing.hearing_type_id) : 'none',
    title: hearing?.title ?? '',
    description: hearing?.description ?? '',
    hearing_date: hearing?.hearing_date ? (hearing.hearing_date as string).split('T')[0] : '',
    hearing_time: normalizeHearingTime(hearing?.hearing_time),
    duration_minutes: hearing?.duration_minutes ?? 30,
    reminder_minutes: reminderMinutes ?? 60,
    url: hearing?.url ?? '',
    status: hearing?.status ?? 'scheduled',
    notes: hearing?.notes ?? '',
    outcome: hearing?.outcome ?? '',
    sync_with_google_calendar: false,
  }));

  const update = useCallback((field: keyof typeof form, value: string | boolean | number) => {
    setForm((prev) => ({ ...prev, [field]: value } as typeof form));
    setErrors((e) => {
      const n = { ...e };
      delete n[field as string];
      return n;
    });
  }, []);

  const getTranslatedValue = (value: any): string => {
    if (!value) return '-';
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null) {
      return value[currentLocale] || value.en || value.ar || '-';
    }
    return '-';
  };

  const displayDuration = useMemo(() => {
    if (durationUnit === 'hours') return (form.duration_minutes / 60).toString();
    return String(form.duration_minutes);
  }, [durationUnit, form.duration_minutes]);

  const setDisplayDuration = (raw: string) => {
    const v = parseFloat(raw);
    if (Number.isNaN(v) || v < 0) return;
    if (durationUnit === 'hours') {
      const mins = Math.round(v * 60);
      setForm((p) => ({ ...p, duration_minutes: Math.min(480, Math.max(15, mins)) }));
    } else {
      const mins = Math.round(v);
      setForm((p) => ({ ...p, duration_minutes: Math.min(480, Math.max(15, mins)) }));
    }
  };

  const onDurationUnitChange = (u: DurationUnit) => {
    if (u === durationUnit) return;
    setDurationUnit(u);
  };

  const quickCourtFormConfig = useMemo(() => {
    const typeOptions = (courtTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name),
    }));
    const circleOptions = (circleTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name),
    }));
    return {
      fields: [
        { name: 'name', label: t('Court Name'), type: 'text' as const, required: true },
        { name: 'court_type_id', label: t('Court Type'), type: 'select' as const, required: true, options: typeOptions },
        { name: 'circle_type_id', label: t('Circle Type'), type: 'select' as const, required: true, options: circleOptions },
        { name: 'address', label: t('Address'), type: 'textarea' as const },
        { name: 'notes', label: t('Notes'), type: 'textarea' as const },
        {
          name: 'status',
          label: t('Status'),
          type: 'select' as const,
          options: [
            { value: 'active', label: t('Active') },
            { value: 'inactive', label: t('Inactive') },
          ],
          defaultValue: 'active',
        },
      ],
      modalSize: 'xl' as const,
    };
  }, [courtTypes, circleTypes, t, currentLocale]);

  const handleQuickCourtSubmit = (courtData: Record<string, unknown>) => {
    router.post(route('courts.store'), courtData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (p) => {
        setCourtModalOpen(false);
        const flash = (p as any)?.props?.flash;
        const newId = flash?.created_court_id;
        if (newId != null) {
          setForm((prev) => ({ ...prev, court_id: String(newId) }));
        }
        if (flash?.success) {
          toast.success(flash.success);
        }
        router.reload({ only: ['courts'] });
      },
      onError: (e) => {
        toast.error(Object.values(e).join(', ') || t('Failed to save'));
      },
    });
  };

  const buildPayload = () => {
    const caseId = hideCaseField ? String(prefillCaseId ?? hearing?.case_id ?? '') : form.case_id;
    const court_id = form.court_id && form.court_id !== 'none' ? form.court_id : null;
    const hearing_type_id = form.hearing_type_id && form.hearing_type_id !== 'none' ? form.hearing_type_id : null;
    const payload: Record<string, unknown> = {
      case_id: caseId,
      court_id,
      circle_number: form.circle_number || null,
      hearing_type_id,
      title: form.title,
      description: form.description || null,
      hearing_date: form.hearing_date,
      hearing_time: form.hearing_time,
      duration_minutes: Math.round(Number(form.duration_minutes)),
      reminder_minutes: form.reminder_minutes ? Math.round(Number(form.reminder_minutes)) : null,
      url: form.url || null,
      status: form.status,
      notes: form.notes || null,
    };
    if (mode === 'edit') {
      payload.outcome = form.outcome || null;
    }
    if (mode === 'create' && googleCalendarEnabled) {
      payload.sync_with_google_calendar = form.sync_with_google_calendar;
    }
    return payload;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});
    setProcessing(true);
    const p = buildPayload();
    if (!p.case_id) {
      setProcessing(false);
      setErrors((er) => ({ ...er, case_id: t('The case field is required.') }));
      return;
    }
    if (!p.hearing_type_id) {
      setProcessing(false);
      setErrors((er) => ({ ...er, hearing_type_id: t('The session type field is required.') }));
      return;
    }
    if (!p.hearing_date) {
      setProcessing(false);
      setErrors((er) => ({ ...er, hearing_date: t('The date field is required.') }));
      return;
    }
    if (!p.hearing_time) {
      setProcessing(false);
      setErrors((er) => ({ ...er, hearing_time: t('The time field is required.') }));
      return;
    }
    if (p.duration_minutes == null || Number(p.duration_minutes) < 15 || Number(p.duration_minutes) > 480) {
      setProcessing(false);
      setErrors((er) => ({ ...er, duration_minutes: t('Duration must be between 15 and 480 minutes.') }));
      return;
    }
    if (p.reminder_minutes != null && (Number(p.reminder_minutes) < 1 || Number(p.reminder_minutes) > 10080)) {
      setProcessing(false);
      setErrors((er) => ({ ...er, reminder_minutes: t('Reminder must be between 1 and 10080 minutes.') }));
      return;
    }

    const options = {
      preserveScroll: true,
      onFinish: () => setProcessing(false),
      onSuccess: (pg: any) => {
        toast.dismiss();
        if (pg?.props?.flash?.success) {
          toast.success(pg.props.flash.success);
        }
        if (returnToCaseId) {
          router.get(route('cases.show', returnToCaseId));
        } else {
          router.get(route('hearings.index'));
        }
      },
      onError: (er: any) => {
        toast.dismiss();
        const flat: Record<string, string> = {};
        Object.keys(er).forEach((k) => {
          const v = (er as any)[k];
          flat[k] = Array.isArray(v) ? v[0] : String(v);
        });
        setErrors(flat);
        toast.error(Object.values(flat).join(', ') || t('Failed to save'));
      },
    };

    if (mode === 'create') {
      router.post(route('hearings.store'), p as any, options);
    } else if (hearing) {
      router.put(route('hearings.update', hearing.id), p as any, options);
    }
  };

  const backHref = returnToCaseId
    ? route('cases.show', returnToCaseId)
    : route('hearings.index');

  const statusOptions = [
    { value: 'scheduled', label: t('Scheduled') },
    { value: 'in_progress', label: t('In Progress') },
    { value: 'completed', label: t('Completed') },
    { value: 'postponed', label: t('Postponed') },
    { value: 'cancelled', label: t('Cancelled') },
  ];

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Case Management'), href: route('cases.index') },
    { title: t('Sessions'), href: route('hearings.index') },
    { title: mode === 'create' ? t('Schedule New Session') : t('Edit Session') },
  ];

  return (
    <PageTemplate
      title={mode === 'create' ? t('Schedule New Session') : t('Edit Session')}
      url={mode === 'create' ? '/hearings/create' : `/hearings/${hearing?.id}/edit`}
      breadcrumbs={breadcrumbs}
      noPadding
      actions={[
        {
          label: t('Back'),
          icon: <ArrowLeft className="h-4 w-4" />,
          variant: 'outline' as const,
          onClick: () => router.visit(backHref),
        },
      ]}
    >
      <form onSubmit={handleSubmit} className="space-y-6 p-6 pb-8">
        <Card>
          <CardContent className="space-y-4 p-6">
            <div className="space-y-2">
              <Label required>{t('Title')}</Label>
              <Input
                value={form.title}
                onChange={(e) => update('title', e.target.value)}
                className="h-10"
                required
                aria-invalid={errors.title ? true : undefined}
              />
              {errors.title ? <p className="text-sm text-destructive">{errors.title}</p> : null}
            </div>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div className={hideCaseField ? 'space-y-2 md:col-span-2' : 'space-y-2'}>
                <Label required>{t('Session Type')}</Label>
                <Select value={form.hearing_type_id} onValueChange={(v) => update('hearing_type_id', v)}>
                  <SelectTrigger className="h-10 w-full" aria-invalid={errors.hearing_type_id ? true : undefined}>
                    <SelectValue placeholder={t('Select Type')} />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="none">{t('Select Type')}</SelectItem>
                    {(hearingTypes || []).map((ht) => (
                      <SelectItem key={ht.id} value={String(ht.id)}>
                        {getTranslatedValue(ht.name)}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.hearing_type_id ? <p className="text-sm text-destructive">{errors.hearing_type_id}</p> : null}
              </div>

              {!hideCaseField && (
                <div className="space-y-2">
                  <Label required>{t('Case')}</Label>
                  <Select value={form.case_id || ''} onValueChange={(v) => update('case_id', v)}>
                    <SelectTrigger className="h-10 w-full" aria-invalid={errors.case_id ? true : undefined}>
                      <SelectValue placeholder={t('Select')} />
                    </SelectTrigger>
                    <SelectContent>
                      {(cases || []).map((c) => (
                        <SelectItem key={c.id} value={String(c.id)}>
                          {`${c.case_id || ''} - ${c.title || ''}`.trim()}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {errors.case_id ? <p className="text-sm text-destructive">{errors.case_id}</p> : null}
                </div>
              )}
            </div>

            <div className="space-y-2">
              <Label>{t('Description')}</Label>
              <Textarea value={form.description} onChange={(e) => update('description', e.target.value)} rows={3} />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <div className="space-y-2">
                <Label required>{t('Date')}</Label>
                <Input
                  type="date"
                  value={form.hearing_date}
                  onChange={(e) => update('hearing_date', e.target.value)}
                  className="h-10"
                  aria-invalid={errors.hearing_date ? true : undefined}
                />
                {errors.hearing_date ? <p className="text-sm text-destructive">{errors.hearing_date}</p> : null}
              </div>
              <div className="space-y-2">
                <Label required>{t('Time')}</Label>
                <Input
                  type="time"
                  value={form.hearing_time}
                  onChange={(e) => update('hearing_time', e.target.value)}
                  className="h-10"
                  aria-invalid={errors.hearing_time ? true : undefined}
                />
                {errors.hearing_time ? <p className="text-sm text-destructive">{errors.hearing_time}</p> : null}
              </div>
              <div className="space-y-2">
                <Label>{t('Duration (minutes)')}</Label>
                <div className="flex gap-2">
                  <Input
                    type="number"
                    min={durationUnit === 'hours' ? 0.25 : 15}
                    max={durationUnit === 'hours' ? 8 : 480}
                    step={durationUnit === 'hours' ? 0.25 : 1}
                    className="h-10"
                    value={displayDuration}
                    onChange={(e) => setDisplayDuration(e.target.value)}
                    aria-invalid={errors.duration_minutes ? true : undefined}
                  />
                  <Button
                    type="button"
                    variant={durationUnit === 'minutes' ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => onDurationUnitChange('minutes')}
                    className="h-10"
                  >
                    {t('minutes')}
                  </Button>
                  <Button
                    type="button"
                    variant={durationUnit === 'hours' ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => onDurationUnitChange('hours')}
                    className="h-10"
                  >
                    {t('Hours')}
                  </Button>
                </div>
                {errors.duration_minutes ? <p className="text-sm text-destructive">{errors.duration_minutes}</p> : null}
              </div>
              <div className="space-y-2">
                <Label>{t('Reminder (minutes before)')}</Label>
                <Input
                  type="number"
                  min={1}
                  max={10080}
                  className="h-10"
                  value={form.reminder_minutes}
                  onChange={(e) => update('reminder_minutes', e.target.value === '' ? '' : Number(e.target.value))}
                  aria-invalid={errors.reminder_minutes ? true : undefined}
                />
                {errors.reminder_minutes ? <p className="text-sm text-destructive">{errors.reminder_minutes}</p> : null}
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="space-y-4 p-6">
            <div className="space-y-2">
              <Label>{t('URL')}</Label>
              <Input
                type="url"
                className="h-10"
                value={form.url}
                onChange={(e) => update('url', e.target.value)}
                placeholder="https://"
                aria-invalid={errors.url ? true : undefined}
              />
              {errors.url ? <p className="text-sm text-destructive">{errors.url}</p> : null}
            </div>

            <div className="space-y-2">
              <Label>{t('Court')}</Label>
              <div className="flex gap-2">
                <div className="min-w-0 flex-1">
                  <Select value={form.court_id || 'none'} onValueChange={(v) => update('court_id', v === 'none' ? '' : v)}>
                    <SelectTrigger className="h-10 w-full">
                      <SelectValue placeholder={t('No court')} />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="none">{t('No court')}</SelectItem>
                      {(courts || []).map((c) => {
                        const courtName = c.name || '';
                        const courtType = c.court_type ? getTranslatedValue(c.court_type.name) : '';
                        const circleType = c.circle_type ? getTranslatedValue(c.circle_type.name) : '';
                        const parts = [courtName];
                        if (courtType) parts.push(courtType);
                        if (circleType) parts.push(circleType);
                        return (
                          <SelectItem key={c.id} value={String(c.id)}>
                            {parts.join(' + ')}
                          </SelectItem>
                        );
                      })}
                    </SelectContent>
                  </Select>
                </div>
                {canQuickCreateCourt && (
                  <Button type="button" variant="outline" className="h-10 shrink-0" onClick={() => setCourtModalOpen(true)} aria-label={t('Add court')}>
                    <Plus className="h-4 w-4" />
                  </Button>
                )}
              </div>
            </div>

            <div className="space-y-2">
              <Label>{t('Circle Number')}</Label>
              <Input
                className="h-10"
                value={form.circle_number}
                onChange={(e) => update('circle_number', e.target.value)}
                aria-invalid={errors.circle_number ? true : undefined}
              />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="space-y-4 p-6">
            <div className="space-y-2">
              <Label>{t('Status')}</Label>
              <Select value={form.status} onValueChange={(v) => update('status', v)}>
                <SelectTrigger className="h-10 w-full">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {statusOptions.map((o) => (
                    <SelectItem key={o.value} value={o.value}>
                      {o.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label>{t('Notes')}</Label>
              <Textarea value={form.notes} onChange={(e) => update('notes', e.target.value)} rows={2} />
            </div>

            {mode === 'edit' && (
              <div className="space-y-2">
                <Label>{t('Outcome')}</Label>
                <Textarea value={form.outcome} onChange={(e) => update('outcome', e.target.value)} rows={2} />
              </div>
            )}

            {mode === 'create' && googleCalendarEnabled && (
              <div className="flex items-center justify-between gap-4 rounded-lg border p-3">
                <div className="space-y-0.5">
                  <Label className="cursor-pointer" htmlFor="gcal">
                    {t('Synchronize in Google Calendar')}
                  </Label>
                </div>
                <Switch
                  id="gcal"
                  checked={form.sync_with_google_calendar}
                  onCheckedChange={(c) => update('sync_with_google_calendar', c)}
                />
              </div>
            )}
          </CardContent>
        </Card>

        <div className="flex flex-wrap items-center justify-end gap-2">
          <Button type="button" variant="outline" asChild>
            <Link href={backHref}>{t('Cancel')}</Link>
          </Button>
          <Button type="submit" disabled={processing}>
            {mode === 'create' ? t('Schedule Session') : t('Update')}
          </Button>
        </div>
      </form>

      <CrudFormModal
        isOpen={courtModalOpen}
        onClose={() => setCourtModalOpen(false)}
        onSubmit={handleQuickCourtSubmit}
        formConfig={quickCourtFormConfig as any}
        initialData={null}
        title={t('Add New Court')}
        mode="create"
      />
    </PageTemplate>
  );
}
