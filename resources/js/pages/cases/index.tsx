import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Repeater, RepeaterField } from '@/components/ui/repeater';

export default function Cases() {
  const { t, i18n } = useTranslation();
  const { auth, cases, caseTypes, caseCategories, caseStatuses, clients, courts, countries, googleCalendarEnabled, planLimits, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedCaseType, setSelectedCaseType] = useState(pageFilters.case_type_id || 'all');
  const [selectedCaseStatus, setSelectedCaseStatus] = useState(pageFilters.case_status_id || 'all');
  const [selectedPriority, setSelectedPriority] = useState(pageFilters.priority || 'all');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [selectedCourt, setSelectedCourt] = useState(pageFilters.court_id || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

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
      case 'edit':
        setFormMode('edit');
        setIsFormModalOpen(true);
        break;
      case 'delete':
        setIsDeleteModalOpen(true);
        break;
      case 'toggle-status':
        handleToggleStatus(item);
        break;
    }
  };

  const handleAddNew = () => {
    setCurrentItem(null);
    setFormMode('create');
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    // Convert 'none' to null for category and subcategory
    if (formData.case_category_id === 'none' || formData.case_category_id === '') {
      formData.case_category_id = null;
    }
    if (formData.case_subcategory_id === 'none' || formData.case_subcategory_id === '') {
      formData.case_subcategory_id = null;
    }

    if (formMode === 'create') {
      toast.loading(t('Creating case...'));

      router.post(route('cases.store'), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
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
            toast.error(`Failed to create case: ${Object.values(errors).join(', ')}`);
          }
        }
      });
    } else if (formMode === 'edit') {
      toast.loading(t('Updating case...'));

      router.put(route('cases.update', currentItem.id), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
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
            toast.error(`Failed to update case: ${Object.values(errors).join(', ')}`);
          }
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting case...'));

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

  const handleToggleStatus = (caseItem: any) => {
    const newStatus = caseItem.status === 'active' ? 'inactive' : 'active';
    toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} case...`);

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

  const columns = [
    {
      key: 'case_id',
      label: t('Case ID'),
      sortable: true
    },
    {
      key: 'title',
      label: t('Title'),
      sortable: true,
      render: (value: any, row: any) => {
        const title = row.title || '-';
        const caseNumber = row.case_number ? ` - ${row.case_number}` : '';
        return `${title}${caseNumber}`;
      }
    },
    {
      key: 'client',
      label: t('Client'),
      render: (value: any, row: any) => {
        if (!row.client) return '-';
        return (
          <button
            type="button"
            onClick={() => router.get(route('clients.show', row.client.id))}
            className="flex flex-col text-left text-primary hover:text-primary/80 hover:underline focus:outline-none cursor-pointer"
          >
            <span>{row.client.name || '-'}</span>
            {row.client.phone && (
              <span className="text-sm text-gray-500 dark:text-gray-400">{row.client.phone}</span>
            )}
          </button>
        );
      }
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
          {row.case_status?.name || '-'}
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
      key: 'filing_date',
      label: t('Filing Date'),
      sortable: true,
      render: (value: string) => value ? new Date(value).toLocaleDateString() : '-'
    },
    {
      key: 'status',
      label: t('Active Status'),
      render: (value: string) => (
        <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ${value === 'active'
          ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'
          : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20'
          }`}>
          {value === 'active' ? t('Active') : t('Inactive')}
        </span>
      )
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
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-cases'
    },
    {
      label: t('Toggle Status'),
      icon: 'Lock',
      action: 'toggle-status',
      className: 'text-amber-500',
      requiredPermission: 'edit-cases'
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
                                  label: type.name,
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
                                  label: status.name,
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
                                  label: court.name,
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
              />
          </div>

          <CrudFormModal
              isOpen={isFormModalOpen}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      {
                          name: 'client_id',
                          label: t('Client'),
                          type: 'select',
                          required: true,
                          options: clients
                              ? [
                                    ...clients.map((client: any) => ({
                                        value: client.id.toString(),
                                        label: client.name,
                                    })),
                                    {
                                        value: auth.user.id.toString(),
                                        label: `${auth.user.name} (Me)`,
                                    },
                                ]
                              : [
                                    {
                                        value: auth.user.id.toString(),
                                        label: `${auth.user.name} (Me)`,
                                    },
                                ],
                      },
                      {
                          name: 'attributes',
                          label: t('Attributes'),
                          type: 'radio',
                          required: true,
                          defaultValue: 'petitioner',
                          options: [
                              { value: 'petitioner', label: t('Petitioner') },
                              { value: 'respondent', label: t('Respondent') },
                          ],
                      },
                      {
                          name: 'opposite_parties',
                          label: t('Opposite Party'),
                          type: 'custom',
                          render: (field: any, formData: any, onChange: (name: string, value: any) => void) => {
                              const repeaterFields: RepeaterField[] = [
                                  { name: 'name', label: t('Name'), type: 'text', required: true },
                                  { name: 'id_number', label: t('ID'), type: 'text' },
                                  {
                                      name: 'nationality_id',
                                      label: t('Nationality'),
                                      type: 'select',
                                      options: countries,
                                      placeholder: countries.length > 0 ? t('Select Nationality') : t('No nationalities available'),
                                  },
                                  { name: 'lawyer_name', label: t('Lawyer Name'), type: 'text' },
                              ];

                              return (
                                  <Repeater
                                      fields={repeaterFields}
                                      value={formData.opposite_parties || []}
                                      onChange={(value) => onChange('opposite_parties', value)}
                                      minItems={1}
                                      maxItems={-1}
                                      addButtonText={t('Add Opposite Party')}
                                      removeButtonText={t('Remove')}
                                  />
                              );
                          },
                      },
                      { name: 'title', label: t('Case Title'), type: 'text', required: true },
                      { name: 'case_number', label: t('Case Number'), type: 'text' },
                      { name: 'file_number', label: t('File Number'), type: 'text' },
                      {
                          name: 'case_category_subcategory',
                          type: 'dependent-dropdown',
                          required: true,
                          dependentConfig: [
                              {
                                  name: 'case_category_id',
                                  label: t('Case Main Category'),
                                  options: caseCategories
                                      ? caseCategories.map((cat: any) => {
                                            // Handle translatable name
                                            let displayName = cat.name;
                                            if (typeof cat.name === 'object' && cat.name !== null) {
                                                displayName = cat.name[i18n.language] || cat.name.en || cat.name.ar || '';
                                            } else if (cat.name_translations && typeof cat.name_translations === 'object') {
                                                displayName =
                                                    cat.name_translations[i18n.language] ||
                                                    cat.name_translations.en ||
                                                    cat.name_translations.ar ||
                                                    '';
                                            }
                                            return {
                                                value: cat.id.toString(),
                                                label: displayName,
                                            };
                                        })
                                      : [],
                              },
                              {
                                  name: 'case_subcategory_id',
                                  label: t('Case Sub Category'),
                                  apiEndpoint: '/case/case-categories/{case_category_id}/subcategories',
                                  showCurrentValue: true,
                              },
                          ],
                      },
                      {
                          name: 'case_type_id',
                          label: t('Case Type'),
                          type: 'select',
                          required: true,
                          options: caseTypes
                              ? caseTypes.map((type: any) => ({
                                    value: type.id.toString(),
                                    label: type.name,
                                }))
                              : [],
                      },
                      {
                          name: 'case_status_id',
                          label: t('Case Status'),
                          type: 'select',
                          required: true,
                          options: caseStatuses
                              ? caseStatuses.map((status: any) => ({
                                    value: status.id.toString(),
                                    label: status.name,
                                }))
                              : [],
                      },
                      {
                          name: 'priority',
                          label: t('Priority'),
                          type: 'select',
                          required: true,
                          options: [
                              { value: 'low', label: t('Low') },
                              { value: 'medium', label: t('Medium') },
                              { value: 'high', label: t('High') },
                          ],
                          defaultValue: 'medium',
                      },
                      {
                          name: 'court_id',
                          label: t('Court'),
                          type: 'select',
                          options: courts
                              ? courts.map((court: any) => ({
                                    value: court.id.toString(),
                                    label: court.name,
                                    key: `court-${court.id}`,
                                }))
                              : [],
                      },
                      { name: 'filing_date', label: t('Filling Date'), type: 'date' },
                      { name: 'expected_completion_date', label: t('Expecting Completion'), type: 'date' },
                      { name: 'estimated_value', label: t('Estimated Value'), type: 'number' },
                      { name: 'description', label: t('Description'), type: 'textarea' },
                      {
                          name: 'status',
                          label: t('Status'),
                          type: 'select',
                          options: [
                              { value: 'active', label: 'Active' },
                              { value: 'inactive', label: 'Inactive' },
                          ],
                          defaultValue: 'active',
                      },
                  ].concat(
                      googleCalendarEnabled
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
              initialData={
                  currentItem
                      ? {
                            ...currentItem,
                            case_category_id: currentItem.case_category_id ? currentItem.case_category_id.toString() : '',
                            case_subcategory_id: currentItem.case_subcategory_id ? currentItem.case_subcategory_id.toString() : '',
                            opposite_parties: currentItem.opposite_parties
                                ? currentItem.opposite_parties.map((party: any) => ({
                                      name: party.name || '',
                                      id_number: party.id_number || '',
                                      nationality_id: party.nationality_id ? party.nationality_id.toString() : '',
                                      lawyer_name: party.lawyer_name || '',
                                  }))
                                : [],
                        }
                      : { case_category_id: '', case_subcategory_id: '', opposite_parties: [] }
              }
              title={formMode === 'create' ? t('Add New Case') : formMode === 'edit' ? t('Edit Case') : t('View Case')}
              mode={formMode}
          />

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.title || ''}
              entityName="case"
          />
      </PageTemplate>
  );
}