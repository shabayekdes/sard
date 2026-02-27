import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { ChevronLeft, Plus } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Switch } from '@/components/ui/switch';

export default function ClientTypes() {
  const { t, i18n } = useTranslation();
  const { auth, clientTypes, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

  // State
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  // Check if any filters are active
  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedStatus !== 'all';
  };

  // Count active filters
  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('setup.client-types.index'), {
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    router.get(route('setup.client-types.index'), {
      sort_field: field,
      sort_direction: direction,
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, item: any) => {
    setCurrentItem(item);

    switch (action) {
      case 'view':
        setFormMode('view');
        setIsFormModalOpen(true);
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

      router.post(route('setup.client-types.store'), formData, {
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
            toast.error(t('Failed to create {{model}}: {{errors}}', { model: t('Client type'), errors: Object.values(errors).join(', ') }));
          }
        }
      });
    } else if (formMode === 'edit') {
      router.put(route('setup.client-types.update', currentItem.id), formData, {
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
            toast.error(t('Failed to update {{model}}: {{errors}}', { model: t('Client type'), errors: Object.values(errors).join(', ') }));
          }
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('setup.client-types.destroy', currentItem.id), {
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
          toast.error(t('Failed to delete {{model}}: {{errors}}', { model: t('Client type'), errors: Object.values(errors).join(', ') }));
        }
      }
    });
  };

  const handleToggleStatus = (clientType: any) => {
    router.put(route('setup.client-types.toggle-status', clientType.id), {}, {
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
          toast.error(t('Failed to update {{model}} status: {{errors}}', { model: t('Client type'), errors: Object.values(errors).join(', ') }));
        }
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedStatus('all');
    setShowFilters(false);

    router.get(
        route('setup.client-types.index'),
        {
            page: 1,
            per_page: pageFilters.per_page,
        },
        { preserveState: true, preserveScroll: true },
    );
  };

  // Define page actions
  const pageActions = [];
  pageActions.push({
    label: t('Back to Master Data'),
    icon: <ChevronLeft className="h-4 w-4" />,
    variant: 'outline',
    onClick: () => router.visit(route('setup.index'))
  });

  if (hasPermission(permissions, 'create-client-types')) {
    pageActions.push({
      label: t('Add Client Type'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Mast Data'), href: route('setup.index') },
        { title: t('Client Types') }
      ];

  // Define table columns
  const columns = [
    {
      key: 'name',
      label: t('Name'),
      sortable: true,
      render: (value: any, row: any) => {
        // Use name_translations if available (full translations object)
        const translations = row.name_translations || (typeof value === 'object' ? value : null);
        if (translations && typeof translations === 'object') {
          return translations[currentLocale] || translations.en || translations.ar || '-';
        }
        // Fallback to value if it's a string
        return value || '-';
      }
    },
    {
      key: 'description',
      label: t('Description'),
      render: (value: any, row: any) => {
        // Use description_translations if available (full translations object)
        const translations = row.description_translations || (typeof value === 'object' ? value : null);
        if (translations && typeof translations === 'object') {
          return translations[currentLocale] || translations.en || translations.ar || '-';
        }
        // Fallback to value if it's a string
        return value || '-';
      }
    },
    {
      key: 'status',
      label: t('Status'),
      render: (value: string, row: any) => {
        const canToggleStatus = hasPermission(permissions, 'edit-client-types');
        return (
          <div className="flex items-center gap-2">
            <Switch
              checked={value === 'active'}
              disabled={!canToggleStatus}
              onCheckedChange={() => {
                if (!canToggleStatus) return;
                handleToggleStatus(row);
              }}
              aria-label={value === 'active' ? t('Deactivate client type') : t('Activate client type')}
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

  // Define table actions
  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-client-types'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-client-types'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-client-types'
    }
  ];

  // Prepare status options for filter
  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'active', label: t('Active') },
    { value: 'inactive', label: t('Inactive') }
  ];

  return (
      <PageTemplate title={t('Client Type Management')} url="/clients/client-types" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          {/* Search and filters section */}
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
                          route('setup.client-types.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              status: selectedStatus !== 'all' ? selectedStatus : undefined,
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
              />
          </div>

          {/* Content section */}
          <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={clientTypes?.data || []}
                  from={clientTypes?.from || 1}
                  onAction={handleAction}
                  sortField={pageFilters.sort_field}
                  sortDirection={pageFilters.sort_direction}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-client-types',
                      create: 'create-client-types',
                      edit: 'edit-client-types',
                      delete: 'delete-client-types',
                  }}
              />

              {/* Pagination section */}
              <Pagination
                  from={clientTypes?.from || 0}
                  to={clientTypes?.to || 0}
                  total={clientTypes?.total || 0}
                  links={clientTypes?.links}
                  entityName={t('client types')}
                  onPageChange={(url) => router.get(url)}
              />
          </div>

          {/* Form Modal */}
          <CrudFormModal
              isOpen={isFormModalOpen}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      {
                          name: 'name.en',
                          label: t('Client Type Name (English)'),
                          type: 'text',
                          required: true,
                      },
                      {
                          name: 'name.ar',
                          label: t('Client Type Name (Arabic)'),
                          type: 'text',
                          required: true,
                      },
                      {
                          name: 'description.en',
                          label: t('Description (English)'),
                          type: 'textarea',
                      },
                      {
                          name: 'description.ar',
                          label: t('Description (Arabic)'),
                          type: 'textarea',
                      },
                      {
                          name: 'status',
                          label: t('Status'),
                          type: 'select',
                          options: [
                              { value: 'active', label: t('Active') },
                              { value: 'inactive', label: t('Inactive') },
                          ],
                          defaultValue: 'active',
                      },
                  ],
                  modalSize: 'lg',
                  transformData: (data: any) => {
                      // Transform flat structure to nested structure for translatable fields
                      const transformed: any = { ...data };

                      // Handle name field
                      if (transformed['name.en'] || transformed['name.ar']) {
                          transformed.name = {
                              en: transformed['name.en'] || '',
                              ar: transformed['name.ar'] || '',
                          };
                          delete transformed['name.en'];
                          delete transformed['name.ar'];
                      }

                      // Handle description field
                      if (transformed['description.en'] || transformed['description.ar']) {
                          transformed.description = {
                              en: transformed['description.en'] || '',
                              ar: transformed['description.ar'] || '',
                          };
                          delete transformed['description.en'];
                          delete transformed['description.ar'];
                      }

                      return transformed;
                  },
              }}
              initialData={
                  currentItem
                      ? {
                            ...currentItem,
                            'name.en': currentItem.name_translations?.en || (typeof currentItem.name === 'object' ? currentItem.name?.en : '') || '',
                            'name.ar': currentItem.name_translations?.ar || (typeof currentItem.name === 'object' ? currentItem.name?.ar : '') || '',
                            'description.en':
                                currentItem.description_translations?.en ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.en : '') ||
                                '',
                            'description.ar':
                                currentItem.description_translations?.ar ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.ar : '') ||
                                '',
                        }
                      : {}
              }
              title={formMode === 'create' ? t('Add New Client Type') : formMode === 'edit' ? t('Edit Client Type') : t('View Client Type')}
              mode={formMode}
          />

          {/* Delete Modal */}
          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={
                  currentItem?.name_translations
                      ? currentItem.name_translations[currentLocale] || currentItem.name_translations.en || currentItem.name_translations.ar || ''
                      : currentItem?.name
                        ? typeof currentItem.name === 'object'
                            ? currentItem.name[currentLocale] || currentItem.name.en || currentItem.name.ar || ''
                            : currentItem.name
                        : ''
              }
              entityName="Client Type"
          />
      </PageTemplate>
  );
}