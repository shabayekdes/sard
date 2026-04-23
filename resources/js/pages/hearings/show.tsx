import { useMemo, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { PageTemplate } from '@/components/page-template';
import { FileSpreadsheet, FileText, Pencil, Plus, Trash2, Users } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { toast } from '@/components/custom-toast';
import { HearingAssignMemberModal } from '@/pages/hearings/HearingAssignMemberModal';
import { HearingAttachmentsSection, type HearingAttachmentRow } from '@/pages/hearings/HearingAttachmentsSection';
import { HearingMinutesModal } from '@/pages/hearings/HearingMinutesModal';

type HearingShowProps = {
  hearing: any;
  reminderMinutes: number[];
  returnToCaseId: number | null;
  hearingAttachmentMedia?: HearingAttachmentRow[];
};

function formatReminders(minutes: number[], t: (k: string, o?: Record<string, unknown>) => string): string {
  if (!minutes?.length) {
    return '—';
  }
  const preset = (m: number) => {
    switch (m) {
      case 60:
        return t('1 hour before');
      case 180:
        return t('3 hours before');
      case 1440:
        return t('1 day before');
      case 4320:
        return t('3 days before');
      default:
        return `${m} ${t('minutes')}`;
    }
  };
  return minutes.map(preset).join(', ');
}

function DetailPair({
  label,
  value,
  valueClassName,
}: {
  label: string;
  value: React.ReactNode;
  valueClassName?: string;
}) {
  return (
    <div className="flex flex-row items-start justify-between gap-4 py-3">
      <div className="shrink-0 text-xs text-gray-500 dark:text-gray-400">{label}</div>
      <div
        className={cn(
          'min-w-0 flex-1 text-end text-sm font-semibold text-gray-900 dark:text-gray-100',
          valueClassName,
        )}
      >
        {value}
      </div>
    </div>
  );
}

function FullWidthBlock({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="pt-4">
      <div className="mb-2 text-start text-xs text-gray-500 dark:text-gray-400">{label}</div>
      <div className="text-start text-sm leading-relaxed text-gray-900 dark:text-gray-100">{children}</div>
    </div>
  );
}

/** Top border + spacing before a section (use after the first block on the page) */
function SectionWithTopRule({ children }: { children: React.ReactNode }) {
  return <div className="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">{children}</div>;
}

export default function HearingShow() {
  const { t, i18n } = useTranslation();
  const { auth } = usePage().props as { auth?: { permissions?: string[] } };
  const permissions = auth?.permissions || [];
  const { hearing, reminderMinutes, returnToCaseId, hearingAttachmentMedia = [] } = usePage().props as unknown as HearingShowProps;
  const locale = i18n.language || 'en';
  const getInitials = useInitials();
  const [isMinutesModalOpen, setIsMinutesModalOpen] = useState(false);
  const [isAssignMemberModalOpen, setIsAssignMemberModalOpen] = useState(false);

  const getTranslated = (value: unknown): string => {
    if (value == null) return '—';
    if (typeof value === 'string') return value || '—';
    if (typeof value === 'object' && value !== null) {
      const o = value as Record<string, string>;
      return o[locale] || o.en || o.ar || '—';
    }
    return '—';
  };

  const backHref = returnToCaseId ? route('cases.show', returnToCaseId) : route('hearings.index');

  const hearingTimeStr =
    hearing?.hearing_time != null && hearing?.hearing_time !== ''
      ? String(hearing.hearing_time).slice(0, 5)
      : '';
  const timeDisplay = hearingTimeStr
    ? window.appSettings?.formatTime(`2000-01-01T${hearingTimeStr}`) || hearingTimeStr
    : '—';

  const dateDisplay = hearing?.hearing_date
    ? window.appSettings?.formatDate(hearing.hearing_date) || new Date(hearing.hearing_date).toLocaleDateString()
    : '—';

  const durationDisplay =
    hearing?.duration_minutes != null && hearing.duration_minutes !== ''
      ? `${hearing.duration_minutes} ${t('minutes')}`
      : '—';

  const courtLabel = hearing?.court
    ? (() => {
        const name = hearing.court.name || '';
        const parts = [name];
        const ct = hearing.court.court_type ? getTranslated(hearing.court.court_type.name) : '';
        if (ct && ct !== '—') parts.push(ct);
        return parts.filter(Boolean).join(' — ') || '—';
      })()
    : '—';

  const typeLabel = getTranslated(hearing?.hearing_type?.name);

  const statusRaw = hearing?.status ? String(hearing.status) : '';
  const statusLabel = statusRaw
    ? t(statusRaw.charAt(0).toUpperCase() + statusRaw.slice(1).replace(/_/g, ' '))
    : '—';

  const circleDisplay = hearing?.circle_number?.toString().trim() || '—';

  const caseRow = hearing?.case ? (
    <Link
      href={route('cases.show', hearing.case.id)}
      className="text-left font-semibold text-green-800 hover:text-green-900 hover:underline dark:text-green-400 dark:hover:text-green-300"
    >
      {[hearing.case.title, hearing.case.file_number].filter(Boolean).join(' — ') || hearing.case.case_id || '—'}
    </Link>
  ) : (
    '—'
  );

  const clientRow =
    hearing?.case?.client?.id && hearing.case.client.name ? (
      hasPermission(permissions, 'view-clients') ? (
        <Link
          href={route('clients.show', hearing.case.client.id)}
          className="text-left font-semibold text-green-800 hover:text-green-900 hover:underline dark:text-green-400 dark:hover:text-green-300"
        >
          {hearing.case.client.name}
        </Link>
      ) : (
        <span className="font-semibold text-green-800 dark:text-green-400">{hearing.case.client.name}</span>
      )
    ) : (
      '—'
    );

  const assignedMembers: Array<{
    id: number;
    name?: string;
    email?: string;
    status?: string;
    roles?: Array<{ id?: number; name?: string }>;
    avatar?: string | null;
  }> = Array.isArray(hearing?.teamMembers)
    ? hearing.teamMembers
    : Array.isArray(hearing?.team_members)
      ? hearing.team_members
      : [];

  const roleLabelFor = (member: { roles?: Array<{ name?: string }> }) => {
    const names = (member.roles || []).map((r) => r.name).filter(Boolean) as string[];
    if (!names.length) return '—';
    return names.map((n) => (t(n) !== n ? t(n) : n)).join(', ');
  };

  const teamListSource = hearing?.teamMembers ?? hearing?.team_members;
  const assignedHearingUserIds = useMemo(
    () => (Array.isArray(teamListSource) ? teamListSource.map((m: { id: number }) => m.id) : []),
    [teamListSource],
  );
  const caseIdForHearing: number | null = (() => {
    const raw = hearing?.case_id ?? hearing?.case?.id;
    if (raw == null) return null;
    const n = Number(raw);
    return Number.isFinite(n) ? n : null;
  })();

  const hearingAttachmentIds = useMemo(() => {
    const raw = hearing?.attachments;
    if (!Array.isArray(raw)) {
      return [];
    }
    return raw.map((x) => Number(x)).filter((n) => !Number.isNaN(n) && n > 0);
  }, [hearing?.attachments]);

  const removeTeamMember = (userId: number) => {
    if (!window.confirm(t('Remove member from session?'))) return;
    router.delete(route('hearings.team-members.destroy', { hearing: hearing.id, user: userId }), {
      preserveScroll: true,
      onSuccess: (page) => {
        toast.dismiss();
        const flash = (page.props as { flash?: { success?: string; error?: string } }).flash;
        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
      },
      onError: () => {
        toast.dismiss();
        toast.error(t('Failed to remove team member'));
      },
    });
  };

  const typeBadge = (
    <span className="inline-flex rounded-full bg-violet-100 px-3 py-0.5 text-sm font-medium text-violet-800 dark:bg-violet-950 dark:text-violet-200">
      {typeLabel}
    </span>
  );

  const statusBadge = (
    <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-0.5 text-sm font-medium text-emerald-900 dark:bg-emerald-950 dark:text-emerald-100">
      <span className="h-1.5 w-1.5 rounded-full bg-emerald-700 dark:bg-emerald-300" aria-hidden />
      {statusLabel}
    </span>
  );

  const pageActions = [];
  if (hasPermission(permissions, 'edit-hearings')) {
    pageActions.push({
      label: t('Edit'),
      icon: <Pencil className="h-4 w-4 ltr:mr-2 rtl:ml-2" />,
      variant: 'default' as const,
      onClick: () =>
        router.get(
          route('hearings.edit', hearing.id),
          returnToCaseId ? { from_case: 1, case_id: returnToCaseId } : {},
        ),
    });
  }

  const breadcrumbs = returnToCaseId
    ? [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Case Management'), href: route('cases.index') },
        { title: hearing?.case?.case_id || t('Case'), href: route('cases.show', returnToCaseId) },
        { title: t('Session details'), href: route('hearings.show', hearing.id) },
      ]
    : [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Case Management'), href: route('cases.index') },
        { title: t('Sessions'), href: route('hearings.index') },
        { title: t('Session details'), href: route('hearings.show', hearing.id) },
      ];

  return (
    <PageTemplate
      title={t('Session details')}
      titleForHead={t('Session details')}
      url={`/hearings/${hearing?.id}`}
      breadcrumbs={breadcrumbs}
      actions={[
        {
          label: t('Back'),
          variant: 'outline',
          onClick: () => router.get(backHref),
        },
        ...pageActions,
      ]}
    >
      <div className="w-full min-w-0">
        <div className="rounded-xl bg-white p-6 dark:bg-gray-950">
          <div className="mb-6 flex items-center gap-2 pb-4">
            <FileText className="h-5 w-5 shrink-0 text-green-600 dark:text-green-500" aria-hidden />
            <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t('Session details')}</h2>
          </div>

          <div className="grid grid-cols-1 gap-x-10 md:grid-cols-2">
            <div>
              <DetailPair label={t('Title')} value={hearing?.title || '—'} />
              <DetailPair label={t('Time')} value={timeDisplay} />
              <DetailPair label={t('Session Type')} value={typeBadge} />
              <DetailPair label={t('Circle Number')} value={circleDisplay} />
              <DetailPair label={t('Case')} value={caseRow} />
              <DetailPair label={t('Status')} value={statusBadge} />
            </div>
            <div>
              <DetailPair label={t('Date')} value={dateDisplay} />
              <DetailPair label={t('Duration')} value={durationDisplay} />
              <DetailPair label={t('Court')} value={courtLabel} />
              <DetailPair label={t('Judge name')} value={hearing?.judge_name?.trim() || '—'} />
              <DetailPair label={t('Client')} value={clientRow} />
              <DetailPair label={t('Reminders')} value={formatReminders(reminderMinutes || [], t)} />
            </div>
          </div>

          {(hearing?.description || hearing?.notes) && (
            <div className="mt-2 space-y-4">
              {hearing?.description ? (
                <FullWidthBlock label={t('Description')}>{hearing.description}</FullWidthBlock>
              ) : null}
              {hearing?.notes ? <FullWidthBlock label={t('Notes')}>{hearing.notes}</FullWidthBlock> : null}
            </div>
          )}

          {hearing?.outcome ? (
            <FullWidthBlock label={t('Outcome')}>{hearing.outcome}</FullWidthBlock>
          ) : null}
        </div>

        <SectionWithTopRule>
        <div className="rounded-xl bg-white p-6 dark:bg-gray-950">
          <div className="mb-4 flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-2">
              <Users className="h-5 w-5 shrink-0 text-gray-700 dark:text-gray-300" aria-hidden />
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t('Session assigned members')}</h2>
              <span className="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-200 px-2 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                {assignedMembers.length}
              </span>
            </div>
            {hasPermission(permissions, 'edit-hearings') ? (
              <Button
                type="button"
                variant="outline"
                size="sm"
                className="inline-flex shrink-0 items-center gap-1.5 self-start"
                onClick={() => setIsAssignMemberModalOpen(true)}
              >
                <Plus className="h-4 w-4 shrink-0" aria-hidden />
                {t('Add assigned member')}
              </Button>
            ) : null}
          </div>

          <div className="overflow-x-auto" dir="auto">
            <table className="w-full min-w-[640px] table-fixed border-collapse text-sm">
              <colgroup>
                <col className="w-12" />
                <col />
                <col className="w-36" />
                <col className="w-32" />
                <col className="w-16" />
              </colgroup>
              <thead>
                <tr className="text-start text-xs font-medium text-gray-500 dark:text-gray-400">
                  <th className="w-12 px-3 py-3 font-medium">#</th>
                  <th className="px-3 py-3 font-medium">{t('Team Members')}</th>
                  <th className="w-36 px-3 py-3 font-medium">{t('Role')}</th>
                  <th className="w-32 px-3 py-3 font-medium">{t('Status')}</th>
                  <th className="w-16 px-3 py-3 font-medium">{t('Actions')}</th>
                </tr>
              </thead>
              <tbody>
                {assignedMembers.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                      {t('No assigned members')}
                    </td>
                  </tr>
                ) : (
                  assignedMembers.map((member, index) => {
                    const isActive = String(member.status || '').toLowerCase() === 'active';
                    const name = member.name || '—';
                    const email = member.email || '';
                    return (
                      <tr key={member.id}>
                        <td className="w-12 px-3 py-3 align-top text-gray-600 dark:text-gray-300">
                          {index + 1}
                        </td>
                        <td className="min-w-0 px-3 py-3 align-top">
                          <div className="flex items-start gap-3">
                            <Avatar className="h-10 w-10 shrink-0 border border-gray-100 dark:border-gray-700">
                              {member.avatar ? (
                                <AvatarImage src={member.avatar} alt="" />
                              ) : null}
                              <AvatarFallback className="bg-gray-100 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                {getInitials(name)}
                              </AvatarFallback>
                            </Avatar>
                            <div className="min-w-0">
                              <div className="font-semibold text-gray-900 dark:text-white">{name}</div>
                              {email ? (
                                <div className="text-xs text-gray-500 dark:text-gray-400">{email}</div>
                              ) : null}
                            </div>
                          </div>
                        </td>
                        <td className="w-36 px-3 py-3 align-top text-gray-800 dark:text-gray-200">{roleLabelFor(member)}</td>
                        <td className="w-32 px-3 py-3 align-top">
                          <span className="inline-flex items-center gap-1.5 text-gray-800 dark:text-gray-200">
                            <span
                              className={cn(
                                'h-1.5 w-1.5 shrink-0 rounded-full',
                                isActive ? 'bg-emerald-600' : 'bg-gray-400',
                              )}
                              aria-hidden
                            />
                            {isActive ? t('Active') : member.status || t('Inactive')}
                          </span>
                        </td>
                        <td className="w-16 px-3 py-3 align-top">
                          {hasPermission(permissions, 'edit-hearings') ? (
                            <button
                              type="button"
                              className="rounded p-1.5 text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                              aria-label={t('Delete')}
                              onClick={() => removeTeamMember(member.id)}
                            >
                              <Trash2 className="h-4 w-4" />
                            </button>
                          ) : (
                            <span className="text-gray-400">—</span>
                          )}
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </div>
        </SectionWithTopRule>

        <SectionWithTopRule>
        <div className="rounded-xl bg-white p-6 dark:bg-gray-950">
          <div className="mb-4 pb-4">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
              <div className="min-w-0 flex-1">
                <div className="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 sm:gap-x-3 text-start">
                  <FileSpreadsheet className="h-5 w-5 shrink-0 text-green-600 dark:text-green-500" aria-hidden />
                  <h2 className="shrink-0 text-lg font-bold text-gray-900 dark:text-white">{t('Session minutes')}</h2>
                  {hearing?.minutes_title ? (
                    <span className="min-w-0 text-sm font-semibold text-gray-900 dark:text-white">
                      {hearing.minutes_title}
                    </span>
                  ) : null}
                  {hearing?.minutes_date ? (
                    <span className="shrink-0 text-xs text-gray-500 dark:text-gray-400">
                      {window.appSettings?.formatDate(
                        typeof hearing.minutes_date === 'string' ? hearing.minutes_date : String(hearing.minutes_date),
                      ) || new Date(hearing.minutes_date).toLocaleDateString()}
                    </span>
                  ) : null}
                </div>
              </div>
              {hasPermission(permissions, 'edit-hearings') ? (
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  className="inline-flex shrink-0 items-center gap-1.5 self-start"
                  onClick={() => setIsMinutesModalOpen(true)}
                >
                  <Pencil className="h-4 w-4 shrink-0" aria-hidden />
                  {t('Edit minutes')}
                </Button>
              ) : null}
            </div>
          </div>

          {hearing?.minutes_content ? (
            <div
              className="min-h-8 text-start text-sm leading-relaxed text-gray-900 dark:text-gray-100 [&_a]:text-primary [&_a]:underline [&_li]:my-0.5 [&_ol]:list-decimal [&_ol]:ps-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:ps-5"
              // eslint-disable-next-line react/no-danger
              dangerouslySetInnerHTML={{ __html: hearing.minutes_content }}
            />
          ) : (
            <p className="text-sm text-gray-500 dark:text-gray-400">{t('No minutes added yet')}</p>
          )}
        </div>
        </SectionWithTopRule>

        <SectionWithTopRule>
        <HearingAttachmentsSection
          hearingId={hearing.id}
          attachmentIds={hearingAttachmentIds}
          mediaRows={hearingAttachmentMedia}
          canEdit={hasPermission(permissions, 'edit-hearings')}
          permissions={permissions}
          onAfterChange={() => {
            router.get(
              route('hearings.show', hearing.id),
              returnToCaseId ? { case_id: returnToCaseId } : {},
              { preserveScroll: true, replace: true },
            );
          }}
        />
        </SectionWithTopRule>

        <HearingAssignMemberModal
          open={isAssignMemberModalOpen}
          onOpenChange={setIsAssignMemberModalOpen}
          hearingId={hearing.id}
          caseId={caseIdForHearing}
          sessionTitle={hearing?.title}
          assignedUserIds={assignedHearingUserIds}
          onAfterSave={() => {
            router.get(
              route('hearings.show', hearing.id),
              returnToCaseId ? { case_id: returnToCaseId } : {},
              { preserveScroll: true, replace: true },
            );
          }}
        />

        <HearingMinutesModal
          open={isMinutesModalOpen}
          onOpenChange={setIsMinutesModalOpen}
          onAfterSave={() => {
            router.get(
              route('hearings.show', hearing.id),
              returnToCaseId ? { case_id: returnToCaseId } : {},
              { preserveScroll: true, replace: true },
            );
          }}
          hearing={
            hearing
              ? {
                  id: hearing.id,
                  session_title: hearing.title,
                  minutes_title: hearing.minutes_title,
                  minutes_date: hearing.minutes_date,
                  minutes_content: hearing.minutes_content,
                }
              : null
          }
        />
      </div>
    </PageTemplate>
  );
}
