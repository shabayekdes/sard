import { useMemo, useRef, useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Card, CardContent } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { HearingMinutesModal } from '@/pages/hearings/HearingMinutesModal';

function dateToYmd(d: Date | undefined): string {
  if (!d || Number.isNaN(d.getTime())) return '';
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

export default function Hearings() {
  const { t, i18n } = useTranslation();
  const {
    auth,
    hearings,
    cases = [],
    courts = [],
    courtTypes = [],
    circleTypes = [],
    hearingTypes = [],
    hearingFilterUsers = [],
    hearingStats = { total: 0, this_week: 0, scheduled: 0, completed: 0 },
    filters: pageFilters = {},
  } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';
  const canQuickCreateCourt = hasPermission(permissions, 'create-courts');
  const [courtModalOpen, setCourtModalOpen] = useState(false);
  const courtFieldHandleChangeRef = useRef<((name: string, value: unknown) => void) | null>(null);

  // Helper function to get translated value from JSON object
  const getTranslatedValue = (value: any): string => {
    if (!value) return '-';
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null) {
      return value[currentLocale] || value.en || value.ar || '-';
    }
    return '-';
  };

  const getInitials = useInitials();

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [selectedHearingType, setSelectedHearingType] = useState(pageFilters.hearing_type_id || 'all');
  const [selectedCase, setSelectedCase] = useState(pageFilters.case_id || 'all');
  const [selectedAssigned, setSelectedAssigned] = useState(pageFilters.assigned_to || 'all');
  const [hearingDateFrom, setHearingDateFrom] = useState<string>(pageFilters.hearing_date_from || '');
  const [hearingDateTo, setHearingDateTo] = useState<string>(pageFilters.hearing_date_to || '');
  const [showFilters, setShowFilters] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isMinutesModalOpen, setIsMinutesModalOpen] = useState(false);
  const [minutesTarget, setMinutesTarget] = useState<any>(null);
  const [currentItem, setCurrentItem] = useState<any>(null);

  const courtSelectOptions = useMemo(() => {
    const rows: Array<{ id: number | string; name: string } | [string | null, string]> = [
      ['', t('No court')],
      ...(courts || []).map((c: any) => {
        const courtName = c.name || '';
        const courtType = c.court_type ? getTranslatedValue(c.court_type.name) : '';
        const circleType = c.circle_type ? getTranslatedValue(c.circle_type.name) : '';
        const parts = [courtName];
        if (courtType) parts.push(courtType);
        if (circleType) parts.push(circleType);
        return { id: c.id, name: parts.join(' + ') };
      }),
    ];
    return rows;
  }, [courts, currentLocale, t]);

  const quickCourtFormConfig = useMemo(() => {
    const typeOptions = (courtTypes || []).map((type: any) => {
      let displayName = type.name;
      if (typeof type.name === 'object' && type.name !== null) {
        displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
      } else if (type.name_translations && typeof type.name_translations === 'object') {
        displayName =
          type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
      }
      return { value: type.id.toString(), label: displayName };
    });
    const circleOptions = (circleTypes || []).map((type: any) => {
      let displayName = type.name;
      if (typeof type.name === 'object' && type.name !== null) {
        displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
      } else if (type.name_translations && typeof type.name_translations === 'object') {
        displayName =
          type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
      }
      return { value: type.id.toString(), label: displayName };
    });
    return {
      fields: [
        { name: 'name', label: t('Court Name'), type: 'text' as const, required: true },
        {
          name: 'court_type_id',
          label: t('Court Type'),
          type: 'select' as const,
          required: true,
          options: typeOptions,
        },
        {
          name: 'circle_type_id',
          label: t('Circle Type'),
          type: 'select' as const,
          required: true,
          options: circleOptions,
        },
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
  }, [courtTypes, circleTypes, currentLocale, t]);

  const handleQuickCourtSubmit = (courtForm: Record<string, unknown>) => {
    router.post(route('courts.store'), courtForm as Record<string, string | number | boolean | null>, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        setCourtModalOpen(false);
        toast.dismiss();
        const flash = (page as any)?.props?.flash;
        if (flash?.created_court_id != null) {
          courtFieldHandleChangeRef.current?.('court_id', String(flash.created_court_id));
        }
        if (flash?.success) {
          toast.success(flash.success);
        }
        if (flash?.warning) {
          toast.message(flash.warning);
        }
        if (flash?.error) {
          toast.error(flash.error);
        }
      },
      onError: (formErrors) => {
        toast.dismiss();
        if (typeof formErrors === 'string') {
          toast.error(formErrors);
        } else if (Object.values(formErrors).length > 0) {
          toast.error(
            t('Failed to create {{model}}: {{errors}}', {
              model: t('Court'),
              errors: Object.values(formErrors).join(', '),
            }),
          );
        }
      },
    });
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const hearingListFilterParams = () => ({
    search: searchTerm || undefined,
    status: selectedStatus !== 'all' ? selectedStatus : undefined,
    hearing_type_id: selectedHearingType !== 'all' ? selectedHearingType : undefined,
    case_id: selectedCase !== 'all' ? selectedCase : undefined,
    assigned_to: selectedAssigned !== 'all' ? selectedAssigned : undefined,
    hearing_date_from: hearingDateFrom || undefined,
    hearing_date_to: hearingDateTo || undefined,
  });

  const applyFilters = () => {
    router.get(
      route('hearings.index'),
      {
        page: 1,
        ...hearingListFilterParams(),
        per_page: pageFilters.per_page,
      },
      { preserveState: true, preserveScroll: true },
    );
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';
    router.get(
      route('hearings.index'),
      {
        sort_field: field,
        sort_direction: direction,
        page: 1,
        ...hearingListFilterParams(),
        per_page: pageFilters.per_page,
      },
      { preserveState: true, preserveScroll: true },
    );
  };

  const handleAction = (action: string, item: any) => {
    switch (action) {
      case 'view':
        router.get(route('hearings.show', item.id));
        break;
      case 'edit':
        router.get(route('hearings.edit', item.id));
        break;
      case 'minutes':
        setMinutesTarget(item);
        setIsMinutesModalOpen(true);
        break;
      case 'delete':
        setCurrentItem(item);
        setIsDeleteModalOpen(true);
        break;
    }
  };

  const handleAddNew = () => {
    router.get(route('hearings.create'));
  };

  const handleDeleteConfirm = () => {
    router.delete(route('hearings.destroy', currentItem.id), {
      onSuccess: (page) => {
        const flash = (page as any)?.props?.flash;
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (flash?.success) {
          toast.success(flash.success);
        }
      },
      onError: () => {
        toast.dismiss();
        toast.error('Failed to delete hearing');
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedStatus('all');
    setSelectedHearingType('all');
    setSelectedCase('all');
    setSelectedAssigned('all');
    setHearingDateFrom('');
    setHearingDateTo('');
    setShowFilters(false);
    router.get(route('hearings.index'), {
      page: 1,
      per_page: pageFilters.per_page,
    }, { preserveState: true, preserveScroll: true });
  };

  const pageActions = [];
  if (hasPermission(permissions, 'create-hearings')) {
    pageActions.push({
      label: t('Schedule Session'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default' as const,
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Case Management'), href: route('cases.index') },
    { title: t('Sessions') }
  ];

  const columns = [
    { key: 'title', label: t('Title'), sortable: true },
    {
      key: 'case',
      label: t('Case'),
      render: (value: any) => {
        if (!value) return '-';
        const caseName = value.title || '-';
        const caseNumber = value.case_number || '';
        const displayText = caseNumber
          ? `${caseName} (${caseNumber})`
          : `${caseName}`;

        return (
          <button
            type="button"
            onClick={() => router.get(route('cases.show', value.id))}
            className="text-primary font-semibold hover:text-primary/80 focus:outline-none cursor-pointer"
          >
            {displayText}
          </button>
        );
      }
    },
    {
      key: 'court',
      label: t('Court'),
      render: (value: any) => {
        if (!value) return '-';
        const courtName = value.name || '-';
        const courtType = value.court_type ? getTranslatedValue(value.court_type.name) : '';
        const circleType = value.circle_type ? getTranslatedValue(value.circle_type.name) : '';
        
        const parts = [courtName];
        if (courtType) parts.push(courtType);
        if (circleType) parts.push(circleType);

        return parts.join(' + ');
      }
    },
    {
      key: 'hearing_type',
      label: t('Session Type'),
      render: (_value: unknown, row: any) => {
        const name = row?.hearing_type ? getTranslatedValue(row.hearing_type.name) : '';
        return name && name !== '-' ? name : '-';
      },
    },
    {
      key: 'judge_name',
      label: t('Judge name'),
      sortable: true,
      render: (value: string | null | undefined) => (value && String(value).trim() ? String(value).trim() : '-'),
    },
    {
      key: 'team_members',
      label: t('Team Members'),
      render: (_value: unknown, row: any) => {
        const members = Array.isArray(row?.team_members) ? row.team_members : [];
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
              <span className="ml-2 text-xs text-muted-foreground" title={members.slice(maxShown).map((u: { name?: string }) => u.name).filter(Boolean).join(', ')}>
                +{extra}
              </span>
            ) : null}
          </div>
        );
      },
    },
    {
      key: 'hearing_date',
      label: t('Date & Time'),
      sortable: true,
      type: 'datetime' as const,
      timeKey: 'hearing_time',
    },
    {
      key: 'status',
      label: t('Status'),
      render: (value: string) => {
        const statusColors = {
          scheduled: 'bg-blue-50 text-blue-700 ring-blue-600/20',
          in_progress: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
          completed: 'bg-green-50 text-green-700 ring-green-600/20',
          postponed: 'bg-orange-50 text-orange-700 ring-orange-600/20',
          cancelled: 'bg-red-50 text-red-700 ring-red-600/20'
        };
        return (
          <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusColors[value as keyof typeof statusColors] || 'bg-gray-50 text-gray-700 ring-gray-600/20'}`}>
            {t(value?.charAt(0).toUpperCase() + value?.slice(1).replace('_', ' '))}
          </span>
        );
      }
    }
  ];

  const actions = [
    { label: t('View'), icon: 'Eye', action: 'view', className: 'text-blue-500', requiredPermission: 'view-hearings' },
    { label: t('Hearing minutes'), icon: 'FileSpreadsheet', action: 'minutes', className: 'text-emerald-600', requiredPermission: 'edit-hearings' },
    { label: t('Edit'), icon: 'Edit', action: 'edit', className: 'text-amber-500', requiredPermission: 'edit-hearings' },
    { label: t('Delete'), icon: 'Trash2', action: 'delete', className: 'text-red-500', requiredPermission: 'delete-hearings' }
  ];

  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'scheduled', label: t('Scheduled') },
    { value: 'in_progress', label: t('In Progress') },
    { value: 'completed', label: t('Completed') },
    { value: 'postponed', label: t('Postponed') },
    { value: 'cancelled', label: t('Cancelled') }
  ];

  const hearingTypeFilterOptions = [
    { value: 'all', label: t('All') },
    ...(hearingTypes || []).map((ht: any) => ({
      value: String(ht.id),
      label: getTranslatedValue(ht.name),
    })),
  ];

  const caseFilterOptions = [
    { value: 'all', label: t('All Cases') },
    ...(cases || []).map((c: any) => ({
      value: String(c.id),
      label: `${c.case_id || ''} — ${c.title || ''}`.trim(),
    })),
  ];

  const assignedUserFilterOptions = [
    { value: 'all', label: t('All') },
    ...(hearingFilterUsers || []).map((u: any) => ({
      value: String(u.id),
      label: u.email ? `${u.name} (${u.email})` : u.name,
    })),
  ];

  return (
      <PageTemplate title={t('Session Management')} url="/hearings" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          <Card className="mb-4 hover:shadow-md transition-shadow">
              <CardContent className="p-4">
                  <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
                      <div className="text-center">
                          <div className="text-xl font-bold text-blue-600">
                              {(hearingStats.total ?? 0).toLocaleString()}
                          </div>
                          <div className="text-xs text-gray-600 dark:text-gray-400">{t('Total hearings')}</div>
                      </div>
                      <div className="text-center">
                          <div className="text-xl font-bold text-violet-600">
                              {(hearingStats.this_week ?? 0).toLocaleString()}
                          </div>
                          <div className="text-xs text-gray-600 dark:text-gray-400">{t('Hearings this week')}</div>
                      </div>
                      <div className="text-center">
                          <div className="text-xl font-bold text-emerald-600">
                              {(hearingStats.scheduled ?? 0).toLocaleString()}
                          </div>
                          <div className="text-xs text-gray-600 dark:text-gray-400">{t('Scheduled')}</div>
                      </div>
                      <div className="text-center">
                          <div className="text-xl font-bold text-amber-600">
                              {(hearingStats.completed ?? 0).toLocaleString()}
                          </div>
                          <div className="text-xs text-gray-600 dark:text-gray-400">{t('Completed')}</div>
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
                          name: 'status',
                          label: t('Hearings filter — Session status'),
                          type: 'select',
                          value: selectedStatus,
                          onChange: setSelectedStatus,
                          options: statusOptions,
                      },
                      {
                          name: 'hearing_type_id',
                          label: t('Hearings filter — Session type'),
                          type: 'select',
                          value: selectedHearingType,
                          onChange: setSelectedHearingType,
                          options: hearingTypeFilterOptions,
                      },
                      {
                          name: 'case_id',
                          label: t('Hearings filter — Case'),
                          type: 'select',
                          value: selectedCase,
                          onChange: setSelectedCase,
                          options: caseFilterOptions,
                      },
                      {
                          name: 'assigned_to',
                          label: t('Hearings filter — Assigned to'),
                          type: 'select',
                          value: selectedAssigned,
                          onChange: setSelectedAssigned,
                          options: assignedUserFilterOptions,
                      },
                      {
                          name: 'hearing_date_from',
                          label: t('Hearings filter — Date from'),
                          type: 'date',
                          value: hearingDateFrom || undefined,
                          onChange: (d: Date | undefined) => setHearingDateFrom(dateToYmd(d)),
                      },
                      {
                          name: 'hearing_date_to',
                          label: t('Hearings filter — Date to'),
                          type: 'date',
                          value: hearingDateTo || undefined,
                          onChange: (d: Date | undefined) => setHearingDateTo(dateToYmd(d)),
                      },
                  ]}
                  showFilters={showFilters}
                  setShowFilters={setShowFilters}
                  hasActiveFilters={() =>
                      searchTerm !== '' ||
                      selectedStatus !== 'all' ||
                      selectedHearingType !== 'all' ||
                      selectedCase !== 'all' ||
                      selectedAssigned !== 'all' ||
                      hearingDateFrom !== '' ||
                      hearingDateTo !== ''
                  }
                  activeFilterCount={() =>
                      (searchTerm ? 1 : 0) +
                      (selectedStatus !== 'all' ? 1 : 0) +
                      (selectedHearingType !== 'all' ? 1 : 0) +
                      (selectedCase !== 'all' ? 1 : 0) +
                      (selectedAssigned !== 'all' ? 1 : 0) +
                      (hearingDateFrom ? 1 : 0) +
                      (hearingDateTo ? 1 : 0)
                  }
                  onResetFilters={handleResetFilters}
                  onApplyFilters={applyFilters}
              />
          </div>

          <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={hearings?.data || []}
                  from={hearings?.from || 1}
                  onAction={handleAction}
                  sortField={pageFilters.sort_field}
                  sortDirection={pageFilters.sort_direction}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-hearings',
                      edit: 'edit-hearings',
                      delete: 'delete-hearings',
                  }}
              />

              <Pagination
                  from={hearings?.from || 0}
                  to={hearings?.to || 0}
                  total={hearings?.total || 0}
                  links={hearings?.links}
                  entityName={t('hearings')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('hearings.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              ...hearingListFilterParams(),
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
              />
          </div>

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.title || ''}
              entityName="Hearing"
          />

          <HearingMinutesModal
            open={isMinutesModalOpen}
            onOpenChange={(open) => {
              setIsMinutesModalOpen(open);
              if (!open) {
                setMinutesTarget(null);
              }
            }}
            hearing={
              minutesTarget
                ? {
                    id: minutesTarget.id,
                    session_title: minutesTarget.title,
                    minutes_title: minutesTarget.minutes_title,
                    minutes_date: minutesTarget.minutes_date,
                    minutes_content: minutesTarget.minutes_content,
                  }
                : null
            }
          />
      </PageTemplate>
  );
}