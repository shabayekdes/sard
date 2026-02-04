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
import { capitalize } from '@/utils/helpers';

export default function Hearings() {
  const { t, i18n } = useTranslation();
  const { auth, hearings, cases, courts, courtTypes, circleTypes, judges, hearingTypes, googleCalendarEnabled, filters: pageFilters = {} } = usePage().props as any;
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

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [selectedCourt, setSelectedCourt] = useState(pageFilters.court_id || 'all');
  const [selectedCourtType, setSelectedCourtType] = useState(pageFilters.court_type_id || 'all');
  const [selectedCircleType, setSelectedCircleType] = useState(pageFilters.circle_type_id || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');
  const [formErrors, setFormErrors] = useState<Record<string, string>>({});

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
        setFormMode('edit');
        setIsFormModalOpen(true);
        break;
      case 'delete':
        setIsDeleteModalOpen(true);
        break;
    }
  };

  const handleAddNew = () => {
    setCurrentItem(null);
    setFormMode('create');
    setFormErrors({});
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    if (formMode === 'create') {
      toast.loading(t('Scheduling hearing...'));
      router.post(route('hearings.store'), formData, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          setFormErrors({});
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          setFormErrors(errors as Record<string, string>);
          toast.error(`Failed to schedule hearing: ${Object.values(errors).join(', ')}`);
        }
      });
    } else if (formMode === 'edit') {
      toast.loading(t('Updating hearing...'));
      router.put(route('hearings.update', currentItem.id), formData, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          setFormErrors({});
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          setFormErrors(errors as Record<string, string>);
          toast.error(`Failed to update hearing: ${Object.values(errors).join(', ')}`);
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting hearing...'));
    router.delete(route('hearings.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
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
    { title: t('Cases'), href: route('cases.index') },
    { title: t('Sessions') }
  ];

  const columns = [
    { key: 'hearing_id', label: t('Session ID'), sortable: true },
    { key: 'title', label: t('Title'), sortable: true },
    {
      key: 'case',
      label: t('Case'),
      render: (value: any) => {
        if (!value) return '-';
        const caseId = value.case_id || '-';
        const caseName = value.title || '-';
        const caseNumber = value.file_number || '';
        const displayText = caseNumber
          ? `${caseId} + ${caseName} + ${caseNumber}`
          : `${caseId} + ${caseName}`;

        return (
          <button
            type="button"
            onClick={() => router.get(route('cases.show', value.id))}
            className="text-primary hover:text-primary/80 hover:underline focus:outline-none cursor-pointer"
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
        // Extract circle number from court name if it exists (e.g., "Court #123" or "Court 123")
        const circleNoMatch = value.name?.match(/#?\s*(\d+)/);
        const circleNo = circleNoMatch ? circleNoMatch[1] : '';

        const parts = [courtName];
        if (courtType) parts.push(courtType);
        if (circleType) parts.push(circleType);
        if (circleNo) parts.push(circleNo);

        return parts.join(' + ');
      }
    },
    {
      key: 'circle',
      label: t('Circle'),
      render: (value: any) => {
        if (!value) return '-';
        const circleType = value.circle_type ? getTranslatedValue(value.circle_type.name) : '';
        // Extract circle number from court name if it exists
        const circleNoMatch = value.name?.match(/#?\s*(\d+)/);
        const circleNo = circleNoMatch ? circleNoMatch[1] : '';

        if (circleType && circleNo) {
          return `${circleType} + ${circleNo}`;
        } else if (circleType) {
          return circleType;
        } else if (circleNo) {
          return circleNo;
        }
        return '-';
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
              isOpen={isFormModalOpen}
              onClose={() => {
                  setIsFormModalOpen(false);
                  setFormErrors({});
              }}
              onSubmit={handleFormSubmit}
              externalErrors={formErrors}
              formConfig={{
                  fields: [
                      {
                          name: 'case_id',
                          label: t('Case'),
                          type: 'select',
                          required: true,
                          options: cases ? cases.map((c: any) => ({ value: c.id.toString(), label: `${c.case_id} - ${c.title}` })) : [],
                      },
                      {
                          name: 'court_id',
                          label: t('Court'),
                          type: 'select',
                          required: true,
                          options: courts
                              ? courts.map((c: any) => {
                                    const courtName = c.name || '';
                                    const courtType = c.court_type ? getTranslatedValue(c.court_type.name) : '';
                                    const circleType = c.circle_type ? getTranslatedValue(c.circle_type.name) : '';
                                    const parts = [courtName];
                                    if (courtType) parts.push(courtType);
                                    if (circleType) parts.push(circleType);
                                    return {
                                        value: c.id.toString(),
                                        label: parts.join(' + '),
                                    };
                                })
                              : [],
                      },
                      {
                          name: 'circle_number',
                          label: t('Circle Number'),
                          type: 'text',
                      },
                      {
                          name: 'judge_id',
                          label: t('Judge'),
                          type: 'select',
                          options: [
                              { value: 'none', label: t('Select Judge') },
                              ...(judges
                                  ? judges.map((j: any) => ({
                                        value: j.id.toString(),
                                        label: j.name,
                                    }))
                                  : []),
                          ],
                      },
                      {
                          name: 'hearing_type_id',
                          label: t('Session Type'),
                          type: 'select',
                          required: true,
                          options: [
                              { value: 'none', label: t('Select Type') },
                              ...(hearingTypes
                                  ? hearingTypes.map((ht: any) => ({
                                        value: ht.id.toString(),
                                        label: getTranslatedValue(ht.name),
                                    }))
                                  : []),
                          ],
                      },
                      { name: 'title', label: t('Title'), type: 'text', required: true },
                      { name: 'description', label: t('Description'), type: 'textarea' },
                      { name: 'hearing_date', label: t('Date'), type: 'date', required: true },
                      { name: 'hearing_time', label: t('Time'), type: 'time', required: true },
                      { name: 'duration_minutes', label: t('Duration (minutes)'), type: 'number', defaultValue: 60 },
                      { name: 'url', label: t('URL'), type: 'text' },
                      {
                          name: 'status',
                          label: t('Status'),
                          type: 'select',
                          options: statusOptions.filter((opt) => opt.value !== 'all'),
                          defaultValue: 'scheduled',
                      },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                      ...(formMode === 'edit' ? [{ name: 'outcome', label: t('Outcome'), type: 'textarea' }] : []),
                  ].concat(
                      googleCalendarEnabled && formMode === 'create'
                          ? [
                                {
                                    name: 'sync_with_google_calendar',
                                    label: t('Synchronize in Google Calendar'),
                                    type: 'switch',
                                    defaultValue: false,
                                },
                            ]
                          : [],
                  ),
                  modalSize: 'xl',
              }}
              initialData={currentItem}
              title={formMode === 'create' ? t('Schedule New Session') : t('Edit Session')}
              mode={formMode}
          />

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
                      { name: 'judge', label: t('Judge'), type: 'text', readOnly: true },
                      { name: 'hearing_type', label: t('Type'), type: 'text', readOnly: true },
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
                  circle_number: currentItem?.circle_number || '-',
                  judge: currentItem?.judge?.name || '-',
                  hearing_type: getTranslatedValue(currentItem?.hearing_type?.name) || '-',
                  hearing_date: currentItem?.hearing_date
                      ? window.appSettings?.formatDate(currentItem.hearing_date) || new Date(currentItem.hearing_date).toLocaleDateString()
                      : '-',
                  hearing_time: currentItem?.hearing_time || '-',
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
              entityName="hearing"
          />
      </PageTemplate>
  );
}