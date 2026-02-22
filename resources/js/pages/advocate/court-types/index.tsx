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

export default function CourtTypes() {
  const { t, i18n } = useTranslation();
  const { auth, courtTypes, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(
        route('setup.court-types.index'),
        {
            page: 1,
            search: searchTerm || undefined,
            status: selectedStatus !== 'all' ? selectedStatus : undefined,
            per_page: pageFilters.per_page,
        },
        { preserveState: true, preserveScroll: true },
    );
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';
    router.get(
        route('setup.court-types.index'),
        {
            sort_field: field,
            sort_direction: direction,
            page: 1,
            search: searchTerm || undefined,
            status: selectedStatus !== 'all' ? selectedStatus : undefined,
            per_page: pageFilters.per_page,
        },
        { preserveState: true, preserveScroll: true },
    );
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

  const handleToggleStatus = (item: any) => {
    const newStatus = item.status === 'active' ? 'inactive' : 'active';
    toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} court type...`);

    router.put(
        route('setup.court-types.toggle-status', item.id),
        {},
        {
            onSuccess: (page) => {
                toast.dismiss();
                if (page.props.flash.success) {
                    toast.success(page.props.flash.success);
                }
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error('Failed to update status');
            },
        },
    );
  };

  const handleFormSubmit = (formData: any) => {
    if (formMode === 'create') {
      router.post(route('setup.court-types.store'), formData, {
          onSuccess: (page) => {
              setIsFormModalOpen(false);
              toast.dismiss();
              if (page.props.flash.success) {
                  toast.success(page.props.flash.success);
              }
          },
          onError: (errors) => {
              toast.dismiss();
              toast.error(`Failed to create court type: ${Object.values(errors).join(', ')}`);
          },
      });
    } else if (formMode === 'edit') {
      router.put(route('setup.court-types.update', currentItem.id), formData, {
          onSuccess: (page) => {
              setIsFormModalOpen(false);
              toast.dismiss();
              if (page.props.flash.success) {
                  toast.success(page.props.flash.success);
              }
          },
          onError: (errors) => {
              toast.dismiss();
              toast.error(`Failed to update court type: ${Object.values(errors).join(', ')}`);
          },
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('setup.court-types.destroy', currentItem.id), {
        onSuccess: (page) => {
            setIsDeleteModalOpen(false);
            toast.dismiss();
            if (page.props.flash.success) {
                toast.success(page.props.flash.success);
            }
        },
        onError: (errors) => {
            toast.dismiss();
            toast.error('Failed to delete court type');
        },
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedStatus('all');
    setShowFilters(false);
    router.get(
        route('setup.court-types.index'),
        {
            page: 1,
            per_page: pageFilters.per_page,
        },
        { preserveState: true, preserveScroll: true },
    );
  };

  const pageActions = [];
  pageActions.push({
    label: t('Back to Master Data'),
    icon: <ChevronLeft className="h-4 w-4" />,
    variant: 'outline',
    onClick: () => router.visit(route('setup.index'))
  });

  if (hasPermission(permissions, 'create-court-types')) {
    pageActions.push({
      label: t('Add Court Type'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Mast Data'), href: route('setup.index') },
    { title: t('Court Types') }
  ];

  const columns = [
    {
      key: 'name',
      label: t('Name'),
      sortable: true,
      render: (value: any, row: any) => {
        // Check for name_translations first, then name object, then string
        if (row.name_translations && typeof row.name_translations === 'object') {
          return row.name_translations[currentLocale] || row.name_translations.en || row.name_translations.ar || '-';
        }
        if (typeof value === 'object' && value !== null) {
          return value[currentLocale] || value.en || value.ar || '-';
        }
        return value || '-';
      }
    },
    {
      key: 'description',
      label: t('Description'),
      render: (value: any, row: any) => {
        // Check for description_translations first, then description object, then string
        if (row.description_translations && typeof row.description_translations === 'object') {
          return row.description_translations[currentLocale] || row.description_translations.en || row.description_translations.ar || '-';
        }
        if (typeof value === 'object' && value !== null) {
          return value[currentLocale] || value.en || value.ar || '-';
        }
        return value || '-';
      }
    },
    {
      key: 'color',
      label: t('Color'),
      render: (value: string) => (
        <div className="flex items-center gap-2">
          <div
            className="w-4 h-4 rounded border"
            style={{ backgroundColor: value }}
          />
          <span className="text-xs text-gray-500">{value}</span>
        </div>
      )
    },
    {
      key: 'status',
      label: t('Status'),
      render: (value: string) => (
        <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ${value === 'active'
          ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'
          : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20'
          }`}>
          {value === 'active' ? t('Active') : t('Inactive')}
        </span>
      )
    },
    {
      key: 'created_at',
      label: t('Created At'),
      sortable: true,
      type: 'date',
    }
  ];

  const actions = [
    { label: t('View'), icon: 'Eye', action: 'view', className: 'text-blue-500', requiredPermission: 'view-court-types' },
    { label: t('Edit'), icon: 'Edit', action: 'edit', className: 'text-amber-500', requiredPermission: 'edit-court-types' },
    { label: t('Toggle Status'), icon: 'Lock', action: 'toggle-status', className: 'text-amber-500', requiredPermission: 'edit-court-types' },
    { label: t('Delete'), icon: 'Trash2', action: 'delete', className: 'text-red-500', requiredPermission: 'delete-court-types' }
  ];

  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'active', label: t('Active') },
    { value: 'inactive', label: t('Inactive') }
  ];

  return (
      <PageTemplate title={t('Court Types')} url="/advocate/court-types" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
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
                  hasActiveFilters={() => searchTerm !== '' || selectedStatus !== 'all'}
                  activeFilterCount={() => (searchTerm ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0)}
                  onResetFilters={handleResetFilters}
                  onApplyFilters={applyFilters}
              />
          </div>

          <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={courtTypes?.data || []}
                  from={courtTypes?.from || 1}
                  onAction={handleAction}
                  sortField={pageFilters.sort_field}
                  sortDirection={pageFilters.sort_direction}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-court-types',
                      create: 'create-court-types',
                      edit: 'edit-court-types',
                      delete: 'delete-court-types',
                  }}
              />

              <Pagination
                  from={courtTypes?.from || 0}
                  to={courtTypes?.to || 0}
                  total={courtTypes?.total || 0}
                  links={courtTypes?.links}
                  entityName={t('court types')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('setup.court-types.index'),
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

          <CrudFormModal
              isOpen={isFormModalOpen}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      {
                          name: 'name.en',
                          label: t('Name (English)'),
                          type: 'text',
                          required: true,
                      },
                      {
                          name: 'name.ar',
                          label: t('Name (Arabic)'),
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
                      { name: 'color', label: t('Color'), type: 'color', required: true, defaultValue: '#3B82F6' },
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
                            'name.en': currentItem.name_translations?.en || (typeof currentItem.name === 'object' ? currentItem.name.en : ''),
                            'name.ar': currentItem.name_translations?.ar || (typeof currentItem.name === 'object' ? currentItem.name.ar : ''),
                            'description.en':
                                currentItem.description_translations?.en ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.en : ''),
                            'description.ar':
                                currentItem.description_translations?.ar ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.ar : ''),
                        }
                      : {}
              }
              title={formMode === 'create' ? t('Add New Court Type') : formMode === 'edit' ? t('Edit Court Type') : t('View Court Type')}
              mode={formMode}
          />

          <CrudFormModal
              isOpen={isViewModalOpen}
              onClose={() => setIsViewModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      {
                          name: 'name.en',
                          label: t('Name (English)'),
                          type: 'text',
                          readOnly: true,
                      },
                      {
                          name: 'name.ar',
                          label: t('Name (Arabic)'),
                          type: 'text',
                          readOnly: true,
                      },
                      {
                          name: 'description.en',
                          label: t('Description (English)'),
                          type: 'textarea',
                          readOnly: true,
                      },
                      {
                          name: 'description.ar',
                          label: t('Description (Arabic)'),
                          type: 'textarea',
                          readOnly: true,
                      },
                      { name: 'color', label: t('Color'), type: 'text', readOnly: true },
                      { name: 'status', label: t('Status'), type: 'text', readOnly: true },
                  ],
                  modalSize: 'lg',
              }}
              initialData={
                  currentItem
                      ? {
                            ...currentItem,
                            'name.en': currentItem.name_translations?.en || (typeof currentItem.name === 'object' ? currentItem.name.en : ''),
                            'name.ar': currentItem.name_translations?.ar || (typeof currentItem.name === 'object' ? currentItem.name.ar : ''),
                            'description.en':
                                currentItem.description_translations?.en ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.en : ''),
                            'description.ar':
                                currentItem.description_translations?.ar ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.ar : ''),
                        }
                      : {}
              }
              title={t('View Court Type Details')}
              mode="view"
          />

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={
                  currentItem?.name_translations?.[currentLocale] ||
                  (typeof currentItem?.name === 'object'
                      ? currentItem?.name[currentLocale] || currentItem?.name.en || currentItem?.name.ar
                      : currentItem?.name) ||
                  ''
              }
              entityName="court type"
          />
      </PageTemplate>
  );
}