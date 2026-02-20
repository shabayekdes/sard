import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus, Database, ExternalLink } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';

export default function ResearchSources() {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language?.startsWith('ar') ? 'ar' : 'en';
  const { auth, sources, filters: pageFilters = {}, sourceTypeOptions = [] } = usePage().props as any;
  const permissions = auth?.permissions || [];

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedType, setSelectedType] = useState(pageFilters.source_type || 'all');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('legal-research.sources.index'), {
      page: 1,
      search: searchTerm || undefined,
      source_type: selectedType !== 'all' ? selectedType : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';
    router.get(route('legal-research.sources.index'), {
      sort_field: field,
      sort_direction: direction,
      page: 1,
      search: searchTerm || undefined,
      source_type: selectedType !== 'all' ? selectedType : undefined,
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

  const handleToggleStatus = (source: any) => {
    const newStatus = source.status === 'active' ? 'inactive' : 'active';
    toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} source...`);

    router.put(route('legal-research.sources.toggle-status', source.id), {}, {
      onSuccess: (page) => {
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(`Failed to update source status: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleFormSubmit = (formData: any) => {
    const action = formMode === 'create' ? 'store' : 'update';
    const route_name = formMode === 'create' 
      ? 'legal-research.sources.store' 
      : 'legal-research.sources.update';
    
    toast.loading(t(`${formMode === 'create' ? 'Creating' : 'Updating'} research source...`));

    const method = formMode === 'create' ? 'post' : 'put';
    const url = formMode === 'create' 
      ? route(route_name) 
      : route(route_name, currentItem.id);

    router[method](url, formData, {
      onSuccess: (page) => {
        setIsFormModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(`Failed to ${action} research source: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting research source...'));
    router.delete(route('legal-research.sources.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(`Failed to delete research source: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const pageActions = [];
  if (hasPermission(permissions, 'create-research-sources')) {
    pageActions.push({
      label: t('Add Research Source'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Legal Research') },
    { title: t('Research Sources') }
  ];

  const columns = [
    {
      key: 'source_name',
      label: t('Source Name'),
      sortable: true,
      render: (value: string, row?: any) => {
        const name = row?.source_name_translations
          ? (row.source_name_translations[currentLocale] || row.source_name_translations.en || row.source_name_translations.ar || '')
          : (value || '');
        return (
          <div className="flex items-center gap-2">
            <Database className="h-4 w-4 text-blue-500" />
            <span className="font-medium">{name}</span>
          </div>
        );
      }
    },
    {
      key: 'source_type',
      label: t('Type'),
      render: (value: string) => {
        const label = (sourceTypeOptions as { value: string; label: string }[]).find(o => o.value === value)?.label ?? value;
        return (
          <span className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-gray-50 text-gray-700 ring-gray-600/20">
            {t(label)}
          </span>
        );
      }
    },
    {
      key: 'url',
      label: t('URL'),
      render: (value: string) => (
        <div className="flex items-center gap-2">
          {value ? (
            <>
              <ExternalLink className="h-4 w-4 text-gray-500" />
              <a href={value} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:text-blue-800 truncate max-w-xs">
                {value}
              </a>
            </>
          ) : (
            <span className="text-gray-500">-</span>
          )}
        </div>
      )
    },
    {
      key: 'description',
      label: t('Description'),
      render: (value: string, row?: any) => {
        const desc = row?.description_translations
          ? (row.description_translations[currentLocale] || row.description_translations.en || row.description_translations.ar || '')
          : (value || '');
        return (
          <div className="max-w-md truncate" title={desc}>
            {desc || '-'}
          </div>
        );
      }
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
    }
  ];

  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-research-sources'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-research-sources'
    },
    {
      label: t('Toggle Status'),
      icon: 'ToggleLeft',
      action: 'toggle-status',
      className: 'text-green-500',
      requiredPermission: 'edit-research-sources'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-research-sources'
    }
  ];

  return (
      <PageTemplate title={t('Research Sources')} url="/legal-research/sources" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={[
                      {
                          name: 'source_type',
                          label: t('Source Type'),
                          type: 'select',
                          value: selectedType,
                          onChange: setSelectedType,
                          options: [
                              { value: 'all', label: t('All Types') },
                              ...(sourceTypeOptions as { value: string; label: string }[]).map(o => ({ value: o.value, label: t(o.label) })),
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
                  ]}
                  showFilters={showFilters}
                  setShowFilters={setShowFilters}
                  hasActiveFilters={() => searchTerm !== '' || selectedType !== 'all' || selectedStatus !== 'all'}
                  activeFilterCount={() => (searchTerm ? 1 : 0) + (selectedType !== 'all' ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0)}
                  onResetFilters={() => {
                      setSearchTerm('');
                      setSelectedType('all');
                      setSelectedStatus('all');
                      setShowFilters(false);
                      router.get(route('legal-research.sources.index'), { page: 1, per_page: pageFilters.per_page });
                  }}
                  onApplyFilters={applyFilters}
              />
          </div>

          <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={sources?.data || []}
                  from={sources?.from || 1}
                  onAction={handleAction}
                  sortField={pageFilters.sort_field}
                  sortDirection={pageFilters.sort_direction}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-research-sources',
                      create: 'create-research-sources',
                      edit: 'edit-research-sources',
                      delete: 'delete-research-sources',
                  }}
              />

              <Pagination
                  from={sources?.from || 0}
                  to={sources?.to || 0}
                  total={sources?.total || 0}
                  links={sources?.links}
                  entityName={t('research sources')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(route('legal-research.sources.index'), {
                          page: 1,
                          per_page: parseInt(value),
                          search: searchTerm || undefined,
                          source_type: selectedType !== 'all' ? selectedType : undefined,
                          status: selectedStatus !== 'all' ? selectedStatus : undefined,
                      });
                  }}
              />
          </div>

          <CrudFormModal
              isOpen={isFormModalOpen}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      { name: 'source_name.en', label: t('Source Name (English)'), type: 'text', required: true },
                      { name: 'source_name.ar', label: t('Source Name (Arabic)'), type: 'text' },
                      {
                          name: 'source_type',
                          label: t('Source Type'),
                          type: 'select',
                          required: true,
                          options: (sourceTypeOptions as { value: string; label: string }[]).map(o => ({ value: o.value, label: t(o.label) })),
                      },
                      { name: 'description.en', label: t('Description (English)'), type: 'textarea', rows: 3 },
                      { name: 'description.ar', label: t('Description (Arabic)'), type: 'textarea', rows: 3 },
                      { name: 'url', label: t('URL'), type: 'text' },
                      { name: 'access_info', label: t('Access Information'), type: 'textarea', rows: 2 },
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
                    const transformed = { ...data };
                    if (transformed['source_name.en'] != null || transformed['source_name.ar'] != null) {
                      transformed.source_name = {
                        en: transformed['source_name.en'] ?? '',
                        ar: transformed['source_name.ar'] ?? ''
                      };
                      delete transformed['source_name.en'];
                      delete transformed['source_name.ar'];
                    }
                    if (transformed['description.en'] != null || transformed['description.ar'] != null) {
                      transformed.description = {
                        en: transformed['description.en'] ?? '',
                        ar: transformed['description.ar'] ?? ''
                      };
                      delete transformed['description.en'];
                      delete transformed['description.ar'];
                    }
                    return transformed;
                  },
              }}
              initialData={currentItem ? {
                ...currentItem,
                'source_name.en': currentItem.source_name_translations?.en ?? (typeof currentItem.source_name === 'object' ? currentItem.source_name?.en : '') ?? '',
                'source_name.ar': currentItem.source_name_translations?.ar ?? (typeof currentItem.source_name === 'object' ? currentItem.source_name?.ar : '') ?? '',
                'description.en': currentItem.description_translations?.en ?? (typeof currentItem.description === 'object' ? currentItem.description?.en : '') ?? '',
                'description.ar': currentItem.description_translations?.ar ?? (typeof currentItem.description === 'object' ? currentItem.description?.ar : '') ?? '',
              } : undefined}
              title={
                  formMode === 'create' ? t('Add New Research Source') : formMode === 'edit' ? t('Edit Research Source') : t('View Research Source')
              }
              mode={formMode}
          />

          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.source_name_translations
                ? (currentItem.source_name_translations[currentLocale] || currentItem.source_name_translations.en || currentItem.source_name_translations.ar || '')
                : (typeof currentItem?.source_name === 'object' ? (currentItem.source_name?.[currentLocale] || currentItem.source_name?.en || currentItem.source_name?.ar) : currentItem?.source_name) || ''}
              entityName="research source"
          />
      </PageTemplate>
  );
}