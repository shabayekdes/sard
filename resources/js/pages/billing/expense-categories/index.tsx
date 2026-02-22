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

export default function ExpenseCategories() {
  const { t, i18n } = useTranslation();
  const { auth, expenseCategories, filters: pageFilters = {} } = usePage().props as any;
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
  const [sortField, setSortField] = useState(pageFilters.sort_field || '');
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>(pageFilters.sort_direction || 'asc');

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
    router.get(route('setup.expense-categories.index'), {
      page: 1,
      search: searchTerm || undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      sort_field: sortField || undefined,
      sort_direction: sortDirection || undefined,
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
      router.post(route('setup.expense-categories.store'), formData, {
          onSuccess: (page) => {
              setIsFormModalOpen(false);
              toast.dismiss();
              if (page.props.flash.success) {
                  toast.success(page.props.flash.success);
              }
          },
          onError: (errors) => {
              toast.dismiss();
              toast.error(`Failed to create expense category: ${Object.values(errors).join(', ')}`);
          },
      });
    } else if (formMode === 'edit') {
      router.put(route('setup.expense-categories.update', currentItem.id), formData, {
          onSuccess: (page) => {
              setIsFormModalOpen(false);
              toast.dismiss();
              if (page.props.flash.success) {
                  toast.success(page.props.flash.success);
              }
          },
          onError: (errors) => {
              toast.dismiss();
              toast.error(`Failed to update expense category: ${Object.values(errors).join(', ')}`);
          },
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('setup.expense-categories.destroy', currentItem.id), {
        onSuccess: (page) => {
            setIsDeleteModalOpen(false);
            toast.dismiss();
            if (page.props.flash.success) {
                toast.success(page.props.flash.success);
            }
        },
        onError: (errors) => {
            toast.dismiss();
            toast.error(`Failed to delete expense category: ${Object.values(errors).join(', ')}`);
        },
    });
  };

  const handleSort = (field: string) => {
    const newDirection = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
    setSortField(field);
    setSortDirection(newDirection);

    router.get(
        route('setup.expense-categories.index'),
        {
            page: 1,
            search: searchTerm || undefined,
            status: selectedStatus !== 'all' ? selectedStatus : undefined,
            sort_field: field,
            sort_direction: newDirection,
            per_page: pageFilters.per_page,
        },
        { preserveState: true, preserveScroll: true },
    );
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedStatus('all');
    setSortField('');
    setSortDirection('asc');
    setShowFilters(false);

    router.get(
        route('setup.expense-categories.index'),
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

  if (hasPermission(permissions, 'create-expense-categories')) {
    pageActions.push({
      label: t('Add Expense Category'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Mast Data'), href: route('setup.index') },
        { title: t('Expense Categories') }
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
      label: t('Created'),
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
      requiredPermission: 'view-expense-categories'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-expense-categories'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-expense-categories'
    }
  ];

  // Prepare filter options
  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'active', label: t('Active') },
    { value: 'inactive', label: t('Inactive') }
  ];

  return (
      <PageTemplate title={t('Expense Categories')} url="/billing/expense-categories" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
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
              />
          </div>

          {/* Content section */}
          <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={expenseCategories?.data || []}
                  from={expenseCategories?.from || 1}
                  onAction={handleAction}
                  sortField={sortField}
                  sortDirection={sortDirection}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-expense-categories',
                      create: 'create-expense-categories',
                      edit: 'edit-expense-categories',
                      delete: 'delete-expense-categories',
                  }}
              />

              {/* Pagination section */}
              <Pagination
                  from={expenseCategories?.from || 0}
                  to={expenseCategories?.to || 0}
                  total={expenseCategories?.total || 0}
                  links={expenseCategories?.links}
                  entityName={t('expense categories')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('setup.expense-categories.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              status: selectedStatus !== 'all' ? selectedStatus : undefined,
                              sort_field: sortField || undefined,
                              sort_direction: sortDirection || undefined,
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
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
                      const transformed: any = { ...data };

                      if (transformed['name.en'] || transformed['name.ar']) {
                          transformed.name = {
                              en: transformed['name.en'] || '',
                              ar: transformed['name.ar'] || '',
                          };
                          delete transformed['name.en'];
                          delete transformed['name.ar'];
                      }

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
              title={
                  formMode === 'create'
                      ? t('Add New Expense Category')
                      : formMode === 'edit'
                        ? t('Edit Expense Category')
                        : t('View Expense Category')
              }
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
              entityName="expense category"
          />
      </PageTemplate>
  );
}