import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus, Calendar, Clock } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Card, CardContent } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';

export default function Hearings() {
  const { t, i18n } = useTranslation();
  const {
    auth,
    hearings,
    courts,
    courtTypes,
    circleTypes,
    hearingStats = { total: 0, this_week: 0, scheduled: 0, completed: 0 },
    filters: pageFilters = {},
  } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

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
  const [selectedCourt, setSelectedCourt] = useState(pageFilters.court_id || 'all');
  const [selectedCourtType, setSelectedCourtType] = useState(pageFilters.court_type_id || 'all');
  const [selectedCircleType, setSelectedCircleType] = useState(pageFilters.circle_type_id || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('hearings.index'), {
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
      court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
      circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';
    router.get(route('hearings.index'), {
      sort_field: field,
      sort_direction: direction,
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
      court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
      circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, item: any) => {
    setCurrentItem(item);
    switch (action) {
      case 'view':
        setIsViewModalOpen(true);
        break;
      case 'edit':
        router.get(route('hearings.edit', item.id));
        break;
      case 'delete':
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
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
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
    setSelectedCourt('all');
    setSelectedCourtType('all');
    setSelectedCircleType('all');
    setShowFilters(false);
    router.get(route('hearings.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const pageActions = [];
  if (hasPermission(permissions, 'create-hearings')) {
    pageActions.push({
      label: t('Schedule Session'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
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
      render: (value: string, row: any) => (
        <div className="flex flex-col">
          <div className="flex items-center gap-1">
            <Calendar className="h-3 w-3" />
            <span>{window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()}</span>
          </div>
          <div className="flex items-center gap-1 text-xs text-gray-500">
            <Clock className="h-3 w-3" />
            <span>{window.appSettings?.formatTime(`2000-01-01T${row.hearing_time}`) || row.hearing_time}</span>
          </div>
        </div>
      )
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

  const courtOptions = [
    { value: 'all', label: t('All Courts') },
    ...(courts || []).map((court: any) => ({
      value: court.id.toString(),
      label: court.name
    }))
  ];

  const courtTypeOptions = [
    { value: 'all', label: t('All Court Types') },
    ...(courtTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name),
    }))
  ];

  const circleTypeOptions = [
    { value: 'all', label: t('All Circle Types') },
    ...(circleTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name),
    }))
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
                          label: t('Status'),
                          type: 'select',
                          value: selectedStatus,
                          onChange: setSelectedStatus,
                          options: statusOptions,
                      },
                      {
                          name: 'court_id',
                          label: t('Court'),
                          type: 'select',
                          value: selectedCourt,
                          onChange: setSelectedCourt,
                          options: courtOptions,
                      },
                      {
                          name: 'court_type_id',
                          label: t('Court Type'),
                          type: 'select',
                          value: selectedCourtType,
                          onChange: setSelectedCourtType,
                          options: courtTypeOptions,
                      },
                      {
                          name: 'circle_type_id',
                          label: t('Circle Type'),
                          type: 'select',
                          value: selectedCircleType,
                          onChange: setSelectedCircleType,
                          options: circleTypeOptions,
                      },
                  ]}
                  showFilters={showFilters}
                  setShowFilters={setShowFilters}
                  hasActiveFilters={() =>
                      searchTerm !== '' ||
                      selectedStatus !== 'all' ||
                      selectedCourt !== 'all' ||
                      selectedCourtType !== 'all' ||
                      selectedCircleType !== 'all'
                  }
                  activeFilterCount={() =>
                      (searchTerm ? 1 : 0) +
                      (selectedStatus !== 'all' ? 1 : 0) +
                      (selectedCourt !== 'all' ? 1 : 0) +
                      (selectedCourtType !== 'all' ? 1 : 0) +
                      (selectedCircleType !== 'all' ? 1 : 0)
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
                      create: 'create-hearings',
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
                              search: searchTerm || undefined,
                              status: selectedStatus !== 'all' ? selectedStatus : undefined,
                              court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
                              court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
                              circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
              />
          </div>

          <CrudFormModal
              isOpen={isViewModalOpen}
              onClose={() => setIsViewModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      { name: 'hearing_id', label: t('Session ID'), type: 'text', readOnly: true },
                      { name: 'title', label: t('Title'), type: 'text', readOnly: true },
                      { name: 'case', label: t('Case'), type: 'text', readOnly: true },
                      { name: 'court', label: t('Court'), type: 'text', readOnly: true },
                      { name: 'judge_name', label: t('Judge name'), type: 'text', readOnly: true },
                      { name: 'hearing_type', label: t('Session Type'), type: 'text', readOnly: true },
                      { name: 'team_members', label: t('Team Members'), type: 'text', readOnly: true },
                      { name: 'description', label: t('Description'), type: 'textarea', readOnly: true },
                      { name: 'hearing_date', label: t('Date'), type: 'text', readOnly: true },
                      { name: 'hearing_time', label: t('Time'), type: 'text', readOnly: true },
                      { name: 'duration_minutes', label: t('Duration (minutes)'), type: 'text', readOnly: true },
                      { name: 'status', label: t('Status'), type: 'text', readOnly: true },
                      { name: 'notes', label: t('Notes'), type: 'textarea', readOnly: true },
                  ],
                  modalSize: 'xl',
              }}
              initialData={{
                  ...currentItem,
                  case: currentItem?.case
                      ? (() => {
                            const caseId = currentItem.case.case_id || '-';
                            const caseName = currentItem.case.title || '-';
                            const caseNumber = currentItem.case.file_number || '';
                            if (caseNumber) {
                                return `${caseId} + ${caseName} + ${caseNumber}`;
                            }
                            return `${caseId} + ${caseName}`;
                        })()
                      : '-',
                  court: currentItem?.court
                      ? (() => {
                            const courtName = currentItem.court.name || '-';
                            const courtType = currentItem.court.court_type ? getTranslatedValue(currentItem.court.court_type.name) : '';
                            const circleType = currentItem.court.circle_type ? getTranslatedValue(currentItem.court.circle_type.name) : '';
                            const parts = [courtName];
                            if (courtType) parts.push(courtType);
                            if (circleType) parts.push(circleType);
                            return parts.join(' + ');
                        })()
                      : '-',
                  judge_name: currentItem?.judge_name?.trim() || '-',
                  hearing_type: getTranslatedValue(currentItem?.hearing_type?.name) || '-',
                  team_members: Array.isArray(currentItem?.team_members)
                      ? currentItem.team_members.map((u: { name?: string }) => u.name).filter(Boolean).join(', ') || '-'
                      : '-',
                  hearing_date: currentItem?.hearing_date
                      ? window.appSettings?.formatDate(currentItem.hearing_date) || new Date(currentItem.hearing_date).toLocaleDateString()
                      : '-',
                  hearing_time: currentItem?.hearing_time
                      ? (window.appSettings?.formatTime(`2000-01-01T${currentItem.hearing_time}`) || currentItem.hearing_time)
                      : '-',
                  duration_minutes: currentItem?.duration_minutes ? `${currentItem.duration_minutes} minutes` : '-',
                  url: currentItem?.url || '-',
                  status: currentItem?.status ? t(currentItem.status.charAt(0).toUpperCase() + currentItem.status.slice(1).replace('_', ' ')) : '-',
              }}
              title={t('View Session Details')}
              mode="view"
          />

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.title || ''}
              entityName="Hearing"
          />
      </PageTemplate>
  );
}