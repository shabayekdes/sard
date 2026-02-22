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
import { Switch } from '@/components/ui/switch';

export default function Courts() {
  const { t, i18n } = useTranslation();
  const { auth, courts, courtTypes, circleTypes, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedCourtType, setSelectedCourtType] = useState(pageFilters.court_type_id || 'all');
  const [selectedCircleType, setSelectedCircleType] = useState(pageFilters.circle_type_id || 'all');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedCourtType !== 'all' || selectedCircleType !== 'all' || selectedStatus !== 'all';
  };

  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedCourtType !== 'all' ? 1 : 0) + (selectedCircleType !== 'all' ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('courts.index'), {
      page: 1,
      search: searchTerm || undefined,
      court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
      circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    router.get(route('courts.index'), {
      sort_field: field,
      sort_direction: direction,
      page: 1,
      search: searchTerm || undefined,
      court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
      circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
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
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    if (formMode === 'create') {
      router.post(route('courts.store'), formData, {
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
          toast.error(t('Failed to create {{model}}: {{errors}}', { model: t('Court'), errors: Object.values(errors).join(', ') }));
        }
      });
    } else if (formMode === 'edit') {
      router.put(route('courts.update', currentItem.id), formData, {
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
          toast.error(t('Failed to update {{model}}: {{errors}}', { model: t('Court'), errors: Object.values(errors).join(', ') }));
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('courts.destroy', currentItem.id), {
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
        toast.error(t('Failed to delete {{model}}: {{errors}}', { model: t('Court'), errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleToggleStatus = (court: any) => {
    router.put(route('courts.toggle-status', court.id), {}, {
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
        toast.error(t('Failed to update {{model}} status: {{errors}}', { model: t('Court'), errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedCourtType('all');
    setSelectedCircleType('all');
    setSelectedStatus('all');
    setShowFilters(false);

    router.get(route('courts.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const pageActions = [];

  if (hasPermission(permissions, 'create-courts')) {
    pageActions.push({
      label: t('Add Court'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Case Management'), href: route('cases.index') },
    { title: t('Courts') }
  ];

  const columns = [
    { key: 'court_id', label: t('Court ID'), sortable: true },
    { key: 'name', label: t('Court Name'), sortable: true },
    {
      key: 'court_type_id',
      label: t('Type'),
      render: (value: string, row: any) => {
        const courtType = row.court_type;
        if (!courtType) return '-';

        // Handle translatable name
        let displayName = courtType.name;
        if (typeof courtType.name === 'object' && courtType.name !== null) {
          displayName = courtType.name[currentLocale] || courtType.name.en || courtType.name.ar || '';
        } else if (courtType.name_translations && typeof courtType.name_translations === 'object') {
          displayName = courtType.name_translations[currentLocale] || courtType.name_translations.en || courtType.name_translations.ar || '';
        }

        return (
          <span
            className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
            style={{
              backgroundColor: `${courtType.color}20`,
              color: courtType.color
            }}
          >
            {displayName}
          </span>
        );
      }
    },
    {
      key: 'circle_type_id',
      label: t('Circle Type'),
      render: (value: string, row: any) => {
        const circleType = row.circle_type;
        if (!circleType) return '-';

        // Handle translatable name
        let displayName = circleType.name;
        if (typeof circleType.name === 'object' && circleType.name !== null) {
          displayName = circleType.name[currentLocale] || circleType.name.en || circleType.name.ar || '';
        } else if (circleType.name_translations && typeof circleType.name_translations === 'object') {
          displayName = circleType.name_translations[currentLocale] || circleType.name_translations.en || circleType.name_translations.ar || '';
        }

        return (
          <span
            className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
            style={{
              backgroundColor: `${circleType.color}20`,
              color: circleType.color
            }}
          >
            {displayName}
          </span>
        );
      }
    },
    {
      key: 'status',
      label: t('Status'),
      render: (value: string, row: any) => {
        const canToggleStatus = hasPermission(permissions, 'edit-courts');
        return (
          <div className="flex items-center gap-2">
            <Switch
              checked={value === 'active'}
              disabled={!canToggleStatus}
              onCheckedChange={() => {
                if (!canToggleStatus) return;
                handleToggleStatus(row);
              }}
              aria-label={value === 'active' ? t('Deactivate court') : t('Activate court')}
            />
            <span className="text-muted-foreground text-xs">{value === 'active' ? t('Active') : t('Inactive')}</span>
          </div>
        );
      }
    },
    {
      key: 'created_at',
      label: t('Created At'),
      sortable: true,
      type: 'date',
    }
  ];

  const actions = [
    { label: t('View'), icon: 'Eye', action: 'view', className: 'text-blue-500', requiredPermission: 'view-courts' },
    { label: t('Edit'), icon: 'Edit', action: 'edit', className: 'text-amber-500', requiredPermission: 'edit-courts' },
    { label: t('Delete'), icon: 'Trash2', action: 'delete', className: 'text-red-500', requiredPermission: 'delete-courts' }
  ];

  // Helper function to get translated value from JSON object
  const getTranslatedValue = (value: any): string => {
    if (!value) return '-';
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null) {
      return value[currentLocale] || value.en || value.ar || '-';
    }
    return '-';
  };

  const courtTypeOptions = [
    { value: 'all', label: t('All Types') },
    ...(courtTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name)
    }))
  ];

  const circleTypeOptions = [
    { value: 'all', label: t('All Circle Types') },
    ...(circleTypes || []).map((type: any) => ({
      value: type.id.toString(),
      label: getTranslatedValue(type.name)
    }))
  ];

  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'active', label: t('Active') },
    { value: 'inactive', label: t('Inactive') }
  ];

  return (
      <PageTemplate title={t('Court Management')} url="/courts" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={[
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
                      {
                          name: 'status',
                          label: t('Status'),
                          type: 'select',
                          value: selectedStatus,
                          onChange: setSelectedStatus,
                          options: statusOptions,
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

          <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={courts?.data || []}
                  from={courts?.from || 1}
                  onAction={handleAction}
                  sortField={pageFilters.sort_field}
                  sortDirection={pageFilters.sort_direction}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{ view: 'view-courts', create: 'create-courts', edit: 'edit-courts', delete: 'delete-courts' }}
              />

              <Pagination
                  from={courts?.from || 0}
                  to={courts?.to || 0}
                  total={courts?.total || 0}
                  links={courts?.links}
                  entityName={t('courts')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('courts.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              court_type_id: selectedCourtType !== 'all' ? selectedCourtType : undefined,
                              circle_type_id: selectedCircleType !== 'all' ? selectedCircleType : undefined,
                              status: selectedStatus !== 'all' ? selectedStatus : undefined,
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
              />
          </div>

          <CrudFormModal
              isOpen={isFormModalOpen}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      { name: 'name', label: t('Court Name'), type: 'text', required: true },
                      {
                          name: 'court_type_id',
                          label: t('Court Type'),
                          type: 'select',
                          required: true,
                          options: courtTypes
                              ? courtTypes.map((type: any) => {
                                    // Handle translatable name
                                    let displayName = type.name;
                                    if (typeof type.name === 'object' && type.name !== null) {
                                        displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
                                    } else if (type.name_translations && typeof type.name_translations === 'object') {
                                        displayName =
                                            type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
                                    }
                                    return { value: type.id.toString(), label: displayName };
                                })
                              : [],
                      },
                      {
                          name: 'circle_type_id',
                          label: t('Circle Type'),
                          type: 'select',
                          required: true,
                          options: circleTypes
                              ? circleTypes.map((type: any) => {
                                    // Handle translatable name
                                    let displayName = type.name;
                                    if (typeof type.name === 'object' && type.name !== null) {
                                        displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
                                    } else if (type.name_translations && typeof type.name_translations === 'object') {
                                        displayName =
                                            type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
                                    }
                                    return { value: type.id.toString(), label: displayName };
                                })
                              : [],
                      },
                      { name: 'address', label: t('Address'), type: 'textarea' },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
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
                  ],
                  modalSize: 'xl',
              }}
              initialData={currentItem}
              title={formMode === 'create' ? t('Add New Court') : formMode === 'edit' ? t('Edit Court') : t('View Court')}
              mode={formMode}
              columns={3}
          />

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.name || ''}
              entityName="Court"
          />

          {/* View Modal */}
          <CrudFormModal
              isOpen={isViewModalOpen}
              onClose={() => setIsViewModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      { name: 'court_id', label: t('Court ID'), type: 'text', readOnly: true },
                      { name: 'name', label: t('Court Name'), type: 'text', readOnly: true },
                      { name: 'court_type', label: t('Court Type'), type: 'text', readOnly: true },
                      { name: 'circle_type', label: t('Circle Type'), type: 'text', readOnly: true },
                      { name: 'address', label: t('Address'), type: 'textarea', readOnly: true },
                      { name: 'notes', label: t('Notes'), type: 'textarea', readOnly: true },
                      { name: 'status', label: t('Status'), type: 'text', readOnly: true },
                  ],
                  modalSize: 'xl',
              }}
              initialData={{
                  ...currentItem,
                  court_type: (() => {
                      const courtType = currentItem?.court_type;
                      if (!courtType) return '-';
                      if (typeof courtType.name === 'object' && courtType.name !== null) {
                          return courtType.name[currentLocale] || courtType.name.en || courtType.name.ar || '-';
                      } else if (courtType.name_translations && typeof courtType.name_translations === 'object') {
                          return (
                              courtType.name_translations[currentLocale] || courtType.name_translations.en || courtType.name_translations.ar || '-'
                          );
                      }
                      return courtType.name || '-';
                  })(),
                  circle_type: (() => {
                      const circleType = currentItem?.circle_type;
                      if (!circleType) return '-';
                      if (typeof circleType.name === 'object' && circleType.name !== null) {
                          return circleType.name[currentLocale] || circleType.name.en || circleType.name.ar || '-';
                      } else if (circleType.name_translations && typeof circleType.name_translations === 'object') {
                          return (
                              circleType.name_translations[currentLocale] || circleType.name_translations.en || circleType.name_translations.ar || '-'
                          );
                      }
                      return circleType.name || '-';
                  })(),
              }}
              title={t('View Court Details')}
              mode="view"
          />
      </PageTemplate>
  );
}