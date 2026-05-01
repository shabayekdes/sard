import { useMemo, useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { CrudFormModal } from '@/components/CrudFormModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { ClientTableCell } from '@/components/client-table-cell';
import { Card, CardContent } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useLayout } from '@/contexts/LayoutContext';
import { CaseReferralFormModal } from '@/components/cases/CaseReferralFormModal';
import { AppealReminderDurationField } from '@/components/AppealReminderDurationField';
import type { FormField } from '@/types/crud';

function resolveTranslatable(val: unknown, locale: string): string {
  if (val == null) return '';
  if (typeof val === 'string') return val;
  if (typeof val === 'object' && val !== null && ('en' in val || 'ar' in val)) {
    const o = val as Record<string, string>;
    return o[locale] || o.en || o.ar || '';
  }
  return String(val);
}

export default function Cases() {
  const { t, i18n } = useTranslation();
  const {
    auth,
    cases,
    caseTypes,
    caseCategories,
    caseStatuses,
    clients,
    courts,
    courtTypes = [],
    circleTypes = [],
    countries,
    googleCalendarEnabled,
    planLimits,
    caseIndexStats = { total: 0, active: 0, hearings_this_week: 0, struck_off: 0 },
    teamMemberAssignUsers = [],
    filters: pageFilters = {},
  } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';
  const getInitials = useInitials();
  const { isRtl } = useLayout();
  const selectDir = isRtl ? 'rtl' : 'ltr';

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedCaseType, setSelectedCaseType] = useState(pageFilters.case_type_id || 'all');
  const [selectedCaseStatus, setSelectedCaseStatus] = useState(pageFilters.case_status_id || 'all');
  const [selectedPriority, setSelectedPriority] = useState(pageFilters.priority || 'all');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [selectedCourt, setSelectedCourt] = useState(pageFilters.court_id || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [isCaseStatusModalOpen, setIsCaseStatusModalOpen] = useState(false);
  const [caseForStatusChange, setCaseForStatusChange] = useState<any>(null);
  const [pendingCaseStatusId, setPendingCaseStatusId] = useState<string>('');
  const [isAssignTeamMemberModalOpen, setIsAssignTeamMemberModalOpen] = useState(false);
  const [assignTeamMemberCase, setAssignTeamMemberCase] = useState<any>(null);
  const [referralModalCase, setReferralModalCase] = useState<any>(null);
  const [isReferralModalOpen, setIsReferralModalOpen] = useState(false);
  const [judgmentModalCase, setJudgmentModalCase] = useState<any>(null);
  const [isJudgmentModalOpen, setIsJudgmentModalOpen] = useState(false);

  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedCaseType !== 'all' || selectedCaseStatus !== 'all' ||
      selectedPriority !== 'all' || selectedStatus !== 'all' || selectedCourt !== 'all';
  };

  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedCaseType !== 'all' ? 1 : 0) +
      (selectedCaseStatus !== 'all' ? 1 : 0) + (selectedPriority !== 'all' ? 1 : 0) +
      (selectedStatus !== 'all' ? 1 : 0) + (selectedCourt !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('cases.index'), {
      page: 1,
      search: searchTerm || undefined,
      case_type_id: selectedCaseType !== 'all' ? selectedCaseType : undefined,
      case_status_id: selectedCaseStatus !== 'all' ? selectedCaseStatus : undefined,
      priority: selectedPriority !== 'all' ? selectedPriority : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    router.get(route('cases.index'), {
      sort_field: field,
      sort_direction: direction,
      page: 1,
      search: searchTerm || undefined,
      case_type_id: selectedCaseType !== 'all' ? selectedCaseType : undefined,
      case_status_id: selectedCaseStatus !== 'all' ? selectedCaseStatus : undefined,
      priority: selectedPriority !== 'all' ? selectedPriority : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, item: any) => {
    setCurrentItem(item);

    switch (action) {
      case 'view':
        router.get(route('cases.show', item.id));
        break;
      case 'view-timeline':
        router.get(route('cases.show', item.id), { tab: 'timelines' });
        break;
      case 'edit':
        router.get(route('cases.edit', item.id));
        break;
      case 'delete':
        setIsDeleteModalOpen(true);
        break;
      case 'toggle-status':
        handleToggleStatus(item);
        break;
      case 'change-case-status':
        setCaseForStatusChange(item);
        setPendingCaseStatusId(item.case_status_id != null ? String(item.case_status_id) : '');
        setIsCaseStatusModalOpen(true);
        break;
      case 'assign-team-members':
        setAssignTeamMemberCase(item);
        setIsAssignTeamMemberModalOpen(true);
        break;
      case 'add-referral':
        setReferralModalCase(item);
        setIsReferralModalOpen(true);
        break;
      case 'add-judgment':
        setJudgmentModalCase(item);
        setIsJudgmentModalOpen(true);
        break;
    }
  };

  const handleAddNew = () => {
    router.get(route('cases.create'));
  };

  const handleDeleteConfirm = () => {
    router.delete(route('cases.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        } else if (page.props.flash.error) {
          toast.error(page.props.flash.error);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        if (typeof errors === 'string') {
          toast.error(errors);
        } else {
          toast.error(`Failed to delete case: ${Object.values(errors).join(', ')}`);
        }
      }
    });
  };

  const handleCaseStatusSave = () => {
    if (!caseForStatusChange?.id || !pendingCaseStatusId) return;
    router.put(
      route('cases.update-case-status', caseForStatusChange.id),
      { case_status_id: pendingCaseStatusId },
      {
        preserveScroll: true,
        onSuccess: (page) => {
          toast.dismiss();
          setIsCaseStatusModalOpen(false);
          setCaseForStatusChange(null);
          const flash = (page as any)?.props?.flash;
          if (flash?.success) {
            toast.success(flash.success);
          } else if (flash?.error) {
            toast.error(flash.error);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          if (typeof errors === 'string') {
            toast.error(errors);
          } else {
            toast.error(
              t('Failed to update {{model}} status: {{errors}}', {
                model: t('Case'),
                errors: Object.values(errors).join(', '),
              }),
            );
          }
        },
      },
    );
  };

  const handleToggleStatus = (caseItem: any) => {
    router.put(route('cases.toggle-status', caseItem.id), {}, {
      onSuccess: (page) => {
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        } else if (page.props.flash.error) {
          toast.error(page.props.flash.error);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        if (typeof errors === 'string') {
          toast.error(errors);
        } else {
          toast.error(`Failed to update case status: ${Object.values(errors).join(', ')}`);
        }
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedCaseType('all');
    setSelectedCaseStatus('all');
    setSelectedPriority('all');
    setSelectedStatus('all');
    setSelectedCourt('all');
    setShowFilters(false);

    router.get(route('cases.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const pageActions = [];

  if (hasPermission(permissions, 'create-cases')) {
    const canCreate = !planLimits || planLimits.can_create;
    pageActions.push({
      label: planLimits && !canCreate ? t('Case Limit Reached ({{current}}/{{max}})', { current: planLimits.current_cases, max: planLimits.max_cases }) : t('Add Case'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: canCreate ? 'default' : 'outline',
      onClick: canCreate ? () => handleAddNew() : () => toast.error(t('Case limit exceeded. Your plan allows maximum {{max}} cases. Please upgrade your plan.', { max: planLimits.max_cases })),
      disabled: !canCreate
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Case Management'), href: route('cases.index') },
    { title: t('Cases') }
  ];

  const assignTeamMemberInitialData = useMemo(() => {
    if (!assignTeamMemberCase) {
      return {};
    }
    return {
      case_id: String(assignTeamMemberCase.id),
      user_id: '',
      assigned_date: new Date().toISOString().split('T')[0],
      status: 'active',
    };
  }, [assignTeamMemberCase]);

  const assignTeamMemberFormConfig = useMemo(() => {
    const assignedUserIds = new Set<number>(
      (assignTeamMemberCase?.team_members ?? [])
        .map((tm: { user_id?: number; user?: { id?: number } }) =>
          Number(tm.user_id ?? tm.user?.id ?? NaN),
        )
        .filter((id: number) => !Number.isNaN(id)),
    );

    return {
      fields: [
        {
          name: 'case_id',
          label: t('Case'),
          type: 'select' as const,
          required: true,
          disabled: true,
          options: assignTeamMemberCase
            ? [
                {
                  value: String(assignTeamMemberCase.id),
                  label:
                    `${assignTeamMemberCase.case_id ?? ''} — ${assignTeamMemberCase.title ?? ''}`.trim() ||
                    String(assignTeamMemberCase.id),
                },
              ]
            : [],
        },
        {
          name: 'user_id',
          label: t('Team Member'),
          type: 'select' as const,
          required: true,
          options: (teamMemberAssignUsers as { id: number; name: string }[]).map((u) => {
            const id = Number(u.id);
            const already = assignedUserIds.has(id);
            return {
              value: String(u.id),
              label: already ? `${u.name} (${t('Already assigned')})` : u.name,
              disabled: already,
            };
          }),
        },
        {
          name: 'assigned_date',
          label: t('Assigned Date'),
          type: 'date' as const,
          required: true,
          defaultValue: new Date().toISOString().split('T')[0],
        },
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
      modalSize: 'lg' as const,
    };
  }, [assignTeamMemberCase, teamMemberAssignUsers, t]);

  const handleJudgmentModalSubmit = (formData: Record<string, unknown>) => {
    if (!judgmentModalCase?.id) return;
    router.post(
      route('advocate.case-judgments.store'),
      { ...formData, case_id: judgmentModalCase.id },
      {
        preserveScroll: true,
        onSuccess: () => {
          setIsJudgmentModalOpen(false);
          setJudgmentModalCase(null);
          toast.dismiss();
          toast.success(t('Judgment record created'));
        },
        onError: () => {
          toast.dismiss();
          toast.error(t('Failed to save judgment'));
        },
      },
    );
  };

  const judgmentModalFormConfig = useMemo(
    () => ({
      fields: [
        { name: 'judgment_number', label: t('Judgment number'), type: 'text' as const, required: true },
        { name: 'judgment_date', label: t('Judgment date'), type: 'date' as const, required: true },
        { name: 'receipt_date', label: t('Judgment receipt date'), type: 'date' as const },
        { name: 'appeal_deadline_date', label: t('Appeal deadline date'), type: 'date' as const },
        {
          name: 'appeal_reminder_enabled',
          label: t('Enable appeal deadline reminder'),
          type: 'switch' as const,
          defaultValue: false,
        },
        {
          name: 'appeal_reminder_duration',
          label: '',
          type: 'custom' as const,
          required: true,
          defaultValue: 'one_day_before',
          conditional: (_mode: string, formData: Record<string, unknown>) => Boolean(formData.appeal_reminder_enabled),
          render: (
            _field: FormField,
            formData: Record<string, unknown>,
            handleChange: (name: string, value: unknown) => void,
          ) => (
            <AppealReminderDurationField
              value={(formData.appeal_reminder_duration as string) || 'one_day_before'}
              onChange={(v) => handleChange('appeal_reminder_duration', v)}
              disabled={false}
            />
          ),
        },
        {
          name: 'appeal_reminder_custom_days',
          label: t('Appeal reminder custom days label'),
          type: 'number' as const,
          min: 1,
          max: 366,
          step: 1,
          required: true,
          placeholder: t('Appeal reminder custom days placeholder'),
          conditional: (_mode: string, formData: Record<string, unknown>) =>
            Boolean(formData.appeal_reminder_enabled) && formData.appeal_reminder_duration === 'custom',
        },
        {
          name: 'status',
          label: t('Judgment status'),
          type: 'select' as const,
          required: true,
          selectAllowEmpty: false,
          defaultValue: 'pending_issuance',
          options: [
            { value: 'pending_issuance', label: t('Judgment status pending issuance') },
            { value: 'issued', label: t('Judgment status issued') },
            { value: 'appealed', label: t('Judgment status appealed') },
            { value: 'final', label: t('Judgment status final') },
            { value: 'executed', label: t('Judgment status executed') },
          ],
        },
        {
          name: 'attachments',
          label: t('Upload judgment files'),
          type: 'media-picker' as const,
          multiple: true,
          placeholder: t('Upload judgment files'),
        },
        {
          name: 'grounds',
          label: t('Grounds'),
          type: 'textarea' as const,
          placeholder: t('Placeholders grounds'),
        },
        {
          name: 'summary',
          label: t('Judgment summary'),
          type: 'textarea' as const,
          placeholder: t('Placeholders judgment summary'),
        },
      ],
      modalSize: 'xl' as const,
    }),
    [t],
  );

  const handleAssignTeamMemberSubmit = (formData: Record<string, unknown>) => {
    toast.loading(t('Assigning team member...'));
    router.post(route('cases.case-team-members.store'), formData as Record<string, string>, {
      preserveScroll: true,
      onSuccess: (page) => {
        setIsAssignTeamMemberModalOpen(false);
        setAssignTeamMemberCase(null);
        toast.dismiss();
        const flash = (page as any)?.props?.flash;
        if (flash?.success) {
          toast.success(flash.success);
        } else if (flash?.error) {
          toast.error(flash.error);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        if (typeof errors === 'string') {
          toast.error(errors);
        } else {
          toast.error(`Failed to assign team member: ${Object.values(errors as Record<string, string>).join(', ')}`);
        }
      },
    });
  };

  const columns = [
    {
      key: 'title',
      label: t('Case'),
      sortable: true,
      render: (_value: any, row: any) => {
        const title = row.title || '-';
        const caseNumber = row.case_number ? ` - ${row.case_number}` : '';
        const idLine = row.case_id ? String(row.case_id) : null;
        return (
          <div className="min-w-0">
            {idLine ? (
              <div className="text-xs font-mono text-muted-foreground tabular-nums">{idLine}</div>
            ) : null}
            <div className="break-words font-medium text-foreground">
              {title}
              {caseNumber}
            </div>
          </div>
        );
      },
    },
    {
      key: 'client',
      label: t('Client'),
      render: (_value: any, row: any) => <ClientTableCell client={row.client} locale={currentLocale} />,
    },
    {
      key: 'court_id',
      label: t('Court'),
      render: (_value: any, row: any) => {
        const court = row.court;
        if (!court) return <span className="text-muted-foreground">—</span>;
        const courtName = resolveTranslatable(court.name, currentLocale) || court.name || '-';
        const courtType = court.court_type
          ? resolveTranslatable(court.court_type.name, currentLocale)
          : '';
        const circleType = court.circle_type
          ? resolveTranslatable(court.circle_type.name, currentLocale)
          : '';
        const parts = [courtName];
        if (courtType) parts.push(courtType);
        if (circleType) parts.push(circleType);
        return <span className="text-foreground break-words">{parts.join(' + ')}</span>;
      },
    },
    {
      key: 'case_type_id',
      label: t('Case Type'),
      sortable: true,
      render: (_value: any, row: any) => (
        <span className="text-foreground">
          {resolveTranslatable(row.case_type?.name, currentLocale) || '-'}
        </span>
      ),
    },
    {
      key: 'authority_type',
      label: t('Entity type'),
      render: (_value: any, row: any) => (
        <span className="text-foreground break-words">
          {row.authority_type
            ? t(`authority_type_label_${row.authority_type}`)
            : '—'}
        </span>
      ),
    },
    {
      key: 'case_status',
      label: t('Status'),
      render: (value: any, row: any) => (
        <span
          className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
          style={{
            backgroundColor: `${row.case_status?.color}20`,
            color: row.case_status?.color
          }}
        >
          {resolveTranslatable(row.case_status?.name, currentLocale) || '-'}
        </span>
      )
    },
    {
      key: 'priority',
      label: t('Priority'),
      render: (value: string) => {
        const colors = {
          low: 'bg-green-50 text-green-700 ring-green-600/20',
          medium: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
          high: 'bg-red-50 text-red-700 ring-red-600/20'
        };
        return (
          <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${colors[value as keyof typeof colors] || colors.medium}`}>
            {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
          </span>
        );
      }
    },
    {
      key: 'team_members',
      label: t('Assigns'),
      render: (_value: unknown, row: any) => {
        const rows = Array.isArray(row?.team_members) ? row.team_members : [];
        const members = rows
          .filter((tm: { status?: string; user?: { id?: number } }) => tm.user && tm.status === 'active')
          .map((tm: { user: { id: number; name?: string; avatar?: string | null } }) => tm.user);
        if (members.length === 0) {
          return <span className="text-muted-foreground">-</span>;
        }
        const maxShown = 4;
        const shown = members.slice(0, maxShown);
        const extra = members.length - shown.length;
        return (
          <div className="flex items-center">
            <div className="flex -space-x-2">
              {shown.map((u: { id: number; name?: string; avatar?: string | null }) => (
                <Avatar
                  key={u.id}
                  className="h-8 w-8 border-2 border-background ring-0"
                  title={u.name || ''}
                >
                  <AvatarImage src={u.avatar || undefined} alt={u.name || ''} />
                  <AvatarFallback className="text-xs font-medium">
                    {getInitials(u.name || '?')}
                  </AvatarFallback>
                </Avatar>
              ))}
            </div>
            {extra > 0 ? (
              <span
                className="ml-2 text-xs text-muted-foreground"
                title={members.slice(maxShown).map((u: { name?: string }) => u.name).filter(Boolean).join(', ')}
              >
                +{extra}
              </span>
            ) : null}
          </div>
        );
      },
    },
    {
      key: 'filing_date',
      label: t('Filing Date'),
      sortable: true,
      type: 'date' as const,
    },
    {
      key: 'status',
      label: t('Status'),
      type: 'switch',
      switchAction: 'toggle-status',
      switchPermission: 'edit-cases',
    }
  ];

  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-cases'
    },
    {
      label: t('View case timeline'),
      icon: 'History',
      action: 'view-timeline',
      className: 'text-sky-600',
      requiredPermission: 'view-cases'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-cases'
    },
    {
      label: t('Change case status'),
      icon: 'Tags',
      action: 'change-case-status',
      className: 'text-violet-600',
      requiredPermission: 'edit-cases'
    },
    {
      label: t('Assign team members'),
      icon: 'UserPlus',
      action: 'assign-team-members',
      className: 'text-emerald-600',
      requiredPermission: 'create-case-team-members',
    },
    {
      label: t('Add referral'),
      icon: 'Share2',
      action: 'add-referral',
      className: 'text-orange-600',
      requiredPermission: 'manage-cases',
    },
    {
      label: t('Add court judgment'),
      icon: 'Gavel',
      action: 'add-judgment',
      className: 'text-indigo-600',
      requiredPermission: 'create-case-judgments',
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-cases'
    }
  ];


  return (
    <PageTemplate title={t('Case Management')} url="/cases" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
      <Card className="mb-4 border-slate-200 transition-shadow hover:shadow-md dark:border-gray-800">
        <CardContent className="p-4">
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div className="text-center">
              <div className="text-xl font-bold text-blue-600">
                {(caseIndexStats.total ?? 0).toLocaleString()}
              </div>
              <div className="text-xs text-gray-600 dark:text-gray-400">{t('Total cases')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-emerald-600">
                {(caseIndexStats.active ?? 0).toLocaleString()}
              </div>
              <div className="text-xs text-gray-600 dark:text-gray-400">{t('Active cases')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-violet-600">
                {(caseIndexStats.hearings_this_week ?? 0).toLocaleString()}
              </div>
              <div className="text-xs text-gray-600 dark:text-gray-400">{t('Hearings this week')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-red-600">
                {(caseIndexStats.struck_off ?? 0).toLocaleString()}
              </div>
              <div className="text-xs text-gray-600 dark:text-gray-400">{t('Struck off cases')}</div>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="mb-4 rounded-lg bg-white">
        <SearchAndFilterBar
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          onSearch={handleSearch}
          filters={[
            {
              name: 'case_type_id',
              label: t('Case Type'),
              type: 'select',
              value: selectedCaseType,
              onChange: setSelectedCaseType,
              options: [
                { value: 'all', label: t('All Types') },
                ...(caseTypes || []).map((type: any) => ({
                  value: type.id.toString(),
                  label: resolveTranslatable(type.name, currentLocale),
                })),
              ],
            },
            {
              name: 'case_status_id',
              label: t('Case Status'),
              type: 'select',
              value: selectedCaseStatus,
              onChange: setSelectedCaseStatus,
              options: [
                { value: 'all', label: t('All Statuses') },
                ...(caseStatuses || []).map((status: any) => ({
                  value: status.id.toString(),
                  label: resolveTranslatable(status.name, currentLocale),
                })),
              ],
            },
            {
              name: 'priority',
              label: t('Priority'),
              type: 'select',
              value: selectedPriority,
              onChange: setSelectedPriority,
              options: [
                { value: 'all', label: t('All Priorities') },
                { value: 'low', label: t('Low') },
                { value: 'medium', label: t('Medium') },
                { value: 'high', label: t('High') },
              ],
            },
            {
              name: 'status',
              label: t('Status'),
              type: 'select',
              value: selectedStatus,
              onChange: setSelectedStatus,
              options: [
                { value: 'all', label: t('All Statuses') },
                { value: 'active', label: t('Active') },
                { value: 'inactive', label: t('Inactive') },
              ],
            },
            {
              name: 'court_id',
              label: t('Court'),
              type: 'select',
              value: selectedCourt,
              onChange: setSelectedCourt,
              options: [
                { value: 'all', label: t('All Courts') },
                ...(courts || []).map((court: any) => ({
                  value: court.id.toString(),
                  label: resolveTranslatable(court.name, currentLocale),
                  key: `filter-court-${court.id}`,
                })),
              ],
            },
          ]}
          showFilters={showFilters}
          setShowFilters={setShowFilters}
          hasActiveFilters={hasActiveFilters}
          activeFilterCount={activeFilterCount}
          onResetFilters={handleResetFilters}
          onApplyFilters={applyFilters}
        />
      </div>

      <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
        <CrudTable
          columns={columns}
          actions={actions}
          data={cases?.data || []}
          from={cases?.from || 1}
          onAction={handleAction}
          sortField={pageFilters.sort_field}
          sortDirection={pageFilters.sort_direction}
          onSort={handleSort}
          permissions={permissions}
          entityPermissions={{
            view: 'view-cases',
            create: 'create-cases',
            edit: 'edit-cases',
            delete: 'delete-cases',
          }}
        />

        <Pagination
          from={cases?.from || 0}
          to={cases?.to || 0}
          total={cases?.total || 0}
          links={cases?.links}
          entityName={t('cases')}
          onPageChange={(url) => router.get(url)}
          currentPerPage={pageFilters.per_page?.toString() || '10'}
          onPerPageChange={(value) => {
            router.get(
              route('cases.index'),
              {
                page: 1,
                per_page: parseInt(value),
                search: searchTerm || undefined,
                case_type_id: selectedCaseType !== 'all' ? selectedCaseType : undefined,
                case_status_id: selectedCaseStatus !== 'all' ? selectedCaseStatus : undefined,
                priority: selectedPriority !== 'all' ? selectedPriority : undefined,
                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
              },
              { preserveState: true, preserveScroll: true },
            );
          }}
        />
      </div>

      <Dialog
        open={isCaseStatusModalOpen}
        onOpenChange={(open) => {
          setIsCaseStatusModalOpen(open);
          if (!open) {
            setCaseForStatusChange(null);
          }
        }}
      >
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>{t('Change case status')}</DialogTitle>
            <DialogDescription>
              {caseForStatusChange?.title
                ? t('Select a new status for "{{title}}".', { title: caseForStatusChange.title })
                : t('Select a new case status.')}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-2 py-2">
            <Label htmlFor="case-status-select">{t('Case Status')}</Label>
            <Select
              dir={selectDir}
              value={pendingCaseStatusId}
              onValueChange={setPendingCaseStatusId}
            >
              <SelectTrigger id="case-status-select" dir={selectDir}>
                <SelectValue placeholder={t('Case Status')} />
              </SelectTrigger>
              <SelectContent dir={selectDir}>
                {(caseStatuses || []).map((status: any) => (
                  <SelectItem key={status.id} value={String(status.id)}>
                    {resolveTranslatable(status.name, currentLocale)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <DialogFooter className="sm:justify-end">
            <Button
              type="button"
              variant="outline"
              onClick={() => {
                setIsCaseStatusModalOpen(false);
                setCaseForStatusChange(null);
              }}
            >
              {t('Cancel')}
            </Button>
            <Button
              type="button"
              onClick={handleCaseStatusSave}
              disabled={
                !pendingCaseStatusId ||
                (caseForStatusChange &&
                  String(caseForStatusChange.case_status_id ?? '') === pendingCaseStatusId)
              }
            >
              {t('Save')}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {referralModalCase ? (
        <CaseReferralFormModal
          key={`referral-${referralModalCase.id}`}
          caseId={referralModalCase.id}
          open={isReferralModalOpen}
          onClose={() => {
            setIsReferralModalOpen(false);
            setReferralModalCase(null);
          }}
          mode="create"
          editRow={null}
          courts={courts || []}
          courtTypes={courtTypes}
          circleTypes={circleTypes}
          permissions={permissions}
        />
      ) : null}

      {judgmentModalCase ? (
        <CrudFormModal
          key={`judgment-${judgmentModalCase.id}`}
          isOpen={isJudgmentModalOpen}
          onClose={() => {
            setIsJudgmentModalOpen(false);
            setJudgmentModalCase(null);
          }}
          onSubmit={handleJudgmentModalSubmit}
          formConfig={judgmentModalFormConfig}
          initialData={{
            status: 'pending_issuance',
            appeal_reminder_enabled: false,
            appeal_reminder_duration: 'one_day_before',
            appeal_reminder_custom_days: '',
          }}
          title={t('Add Judgment')}
          mode="create"
        />
      ) : null}

      {assignTeamMemberCase ? (
        <CrudFormModal
          key={assignTeamMemberCase.id}
          isOpen={isAssignTeamMemberModalOpen}
          onClose={() => {
            setIsAssignTeamMemberModalOpen(false);
            setAssignTeamMemberCase(null);
          }}
          onSubmit={handleAssignTeamMemberSubmit}
          formConfig={assignTeamMemberFormConfig}
          initialData={assignTeamMemberInitialData}
          title={t('Assign Team Member')}
          mode="create"
        />
      ) : null}

      <CrudDeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
        itemName={currentItem?.title || ''}
        entityName="Case"
      />
    </PageTemplate>
  );
}