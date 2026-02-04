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

export default function CaseTypes() {
  const { t, i18n } = useTranslation();
  const { auth, caseTypes, caseCategories, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];

  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');
  console.log('ENTER.......')
  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedStatus !== 'all';
  };

  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('cases.case-types.index'), {
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    router.get(route('cases.case-types.index'), {
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
    // Validate that subcategory is selected (required field)
    if (!formData.case_subcategory_id || formData.case_subcategory_id === '' || formData.case_subcategory_id === 'none') {
      toast.error(t('Please select a case subcategory.'));
      return;
    }

    // Save subcategory_id as case_category_id (required field)
    formData.case_category_id = formData.case_subcategory_id;
    // Remove the temporary case_subcategory_id field
    delete formData.case_subcategory_id;

    if (formMode === 'create') {
      toast.loading(t('Creating case type...'));

      router.post(route('cases.case-types.store'), formData, {
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
          toast.error(`Failed to create case type: ${Object.values(errors).join(', ')}`);
        }
      });
    } else if (formMode === 'edit') {
      toast.loading(t('Updating case type...'));

      router.put(route('cases.case-types.update', currentItem.id), formData, {
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
          toast.error(`Failed to update case type: ${Object.values(errors).join(', ')}`);
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting case type...'));

    router.delete(route('cases.case-types.destroy', currentItem.id), {
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
        toast.error(`Failed to delete case type: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleToggleStatus = (caseType: any) => {
    const newStatus = caseType.status === 'active' ? 'inactive' : 'active';
    toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} case type...`);

    router.put(route('cases.case-types.toggle-status', caseType.id), {}, {
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
        toast.error(`Failed to update case type status: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedStatus('all');
    setShowFilters(false);

    router.get(route('cases.case-types.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const pageActions = [];

  if (hasPermission(permissions, 'create-case-types')) {
    pageActions.push({
      label: t('Add Case Type'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Case Management'), href: route('cases.index') },
    { title: t('Case Types') }
  ];

  const currentLocale = i18n.language || 'en';

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
          ></div>
          <span className="text-sm ">{value}</span>
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
    }
  ];

  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-case-types'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-case-types'
    },
    {
      label: t('Toggle Status'),
      icon: 'Lock',
      action: 'toggle-status',
      className: 'text-amber-500',
      requiredPermission: 'edit-case-types'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-case-types'
    }
  ];

  return (
    <PageTemplate
      title={t("Case Types")}
      url="/cases/case-types"
      actions={pageActions}
      breadcrumbs={breadcrumbs}
      noPadding
    >
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
              options: [
                { value: 'all', label: t('All Statuses') },
                { value: 'active', label: t('Active') },
                { value: 'inactive', label: t('Inactive') }
              ]
            }
          ]}
          showFilters={showFilters}
          setShowFilters={setShowFilters}
          hasActiveFilters={hasActiveFilters}
          activeFilterCount={activeFilterCount}
          onResetFilters={handleResetFilters}
          onApplyFilters={applyFilters}
          currentPerPage={pageFilters.per_page?.toString() || "10"}
          onPerPageChange={(value) => {
            router.get(route('cases.case-types.index'), {
              page: 1,
              per_page: parseInt(value),
              search: searchTerm || undefined,
              status: selectedStatus !== 'all' ? selectedStatus : undefined
            }, { preserveState: true, preserveScroll: true });
          }}
        />
      </div>

      <div className="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
        <CrudTable
          columns={columns}
          actions={actions}
          data={caseTypes?.data || []}
          from={caseTypes?.from || 1}
          onAction={handleAction}
          sortField={pageFilters.sort_field}
          sortDirection={pageFilters.sort_direction}
          onSort={handleSort}
          permissions={permissions}
          entityPermissions={{
            view: 'view-case-types',
            create: 'create-case-types',
            edit: 'edit-case-types',
            delete: 'delete-case-types'
          }}
        />

        <Pagination
          from={caseTypes?.from || 0}
          to={caseTypes?.to || 0}
          total={caseTypes?.total || 0}
          links={caseTypes?.links}
          entityName={t("case types")}
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
              name: 'name.en',
              label: t('Name (English)'),
              type: 'text',
              required: true
            },
            {
              name: 'name.ar',
              label: t('Name (Arabic)'),
              type: 'text',
              required: false
            },
            {
              name: 'description.en',
              label: t('Description (English)'),
              type: 'textarea'
            },
            {
              name: 'description.ar',
              label: t('Description (Arabic)'),
              type: 'textarea'
            },
            {
              name: 'case_category_subcategory',
              label: t('Case Category & Subcategory'),
              type: 'dependent-dropdown',
              required: true,
              dependentConfig: [
                {
                  name: 'case_category_id',
                  label: t('Case Category'),
                  options: caseCategories ? caseCategories.map((cat: any) => {
                    // Handle translatable name
                    let displayName = cat.name;
                    if (typeof cat.name === 'object' && cat.name !== null) {
                      displayName = cat.name[i18n.language] || cat.name.en || cat.name.ar || '';
                    } else if (cat.name_translations && typeof cat.name_translations === 'object') {
                      displayName = cat.name_translations[i18n.language] || cat.name_translations.en || cat.name_translations.ar || '';
                    }
                    return {
                      value: cat.id.toString(),
                      label: displayName
                    };
                  }) : []
                },
                {
                  name: 'case_subcategory_id',
                  label: t('Case Subcategory'),
                  apiEndpoint: '/case/case-categories/{case_category_id}/subcategories',
                  showCurrentValue: true
                }
              ]
            },
            { name: 'color', label: t('Color'), type: 'color', defaultValue: '#3B82F6' },
            {
              name: 'status',
              label: t('Status'),
              type: 'select',
              options: [
                { value: 'active', label: 'Active' },
                { value: 'inactive', label: 'Inactive' }
              ],
              defaultValue: 'active'
            }
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
          }
        }}
        initialData={
          currentItem
            ? {
              ...currentItem,
              // Transform name and description to flat structure for form
              'name.en': currentItem.name_translations?.en ||
                (typeof currentItem.name === 'object' && currentItem.name !== null ? currentItem.name.en : '') ||
                (typeof currentItem.name === 'string' ? currentItem.name : '') ||
                '',
              'name.ar': currentItem.name_translations?.ar ||
                (typeof currentItem.name === 'object' && currentItem.name !== null ? currentItem.name.ar : '') ||
                '',
              'description.en': currentItem.description_translations?.en ||
                (typeof currentItem.description === 'object' && currentItem.description !== null ? currentItem.description.en : '') ||
                (typeof currentItem.description === 'string' ? currentItem.description : '') ||
                '',
              'description.ar': currentItem.description_translations?.ar ||
                (typeof currentItem.description === 'object' && currentItem.description !== null ? currentItem.description.ar : '') ||
                '',
              // Get parent category_id from caseCategory's parent if it exists
              'case_category_id': currentItem.caseCategory?.parent_id
                ? currentItem.caseCategory.parent_id.toString()
                : '',
              // Set subcategory_id (which is stored in case_category_id)
              'case_subcategory_id': currentItem.case_category_id ? currentItem.case_category_id.toString() : '',
            }
            : { case_category_id: '', case_subcategory_id: '' }
        }
        title={
          formMode === 'create'
            ? t('Add New Case Type')
            : formMode === 'edit'
              ? t('Edit Case Type')
              : t('View Case Type')
        }
        mode={formMode}
      />

      <CrudDeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
        itemName={
          currentItem?.name_translations
            ? (currentItem.name_translations[currentLocale] || currentItem.name_translations.en || currentItem.name_translations.ar || '')
            : (currentItem?.name
              ? (typeof currentItem.name === 'object' && currentItem.name !== null
                ? (currentItem.name[currentLocale] || currentItem.name.en || currentItem.name.ar || '')
                : currentItem.name)
              : '')
        }
        entityName="case type"
      />
    </PageTemplate>
  );
}