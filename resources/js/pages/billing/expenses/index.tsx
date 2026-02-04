import { useState, useEffect } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus, FileText, File } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { formatCurrency } from '@/utils/helpers';

export default function Expenses() {
  const { t, i18n } = useTranslation();
  const { auth, expenses, categories, cases, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const currentLocale = i18n.language || 'en';

  // State
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedCategory, setSelectedCategory] = useState(pageFilters.expense_category_id || 'all');
  const [selectedBillable, setSelectedBillable] = useState(pageFilters.is_billable || 'all');
  const [selectedApproved, setSelectedApproved] = useState(pageFilters.is_approved || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');
  const [sortField, setSortField] = useState(pageFilters.sort_field || '');
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>(pageFilters.sort_direction || 'asc');

  // Reload data when language changes
  useEffect(() => {
    const handleLanguageChange = () => {
      // Build params object
      const params: any = {
        page: pageFilters.page || 1,
      };

      if (searchTerm) {
        params.search = searchTerm;
      }

      if (selectedCategory !== 'all') {
        params.expense_category_id = selectedCategory;
      }

      if (selectedBillable !== 'all') {
        params.is_billable = selectedBillable;
      }

      if (selectedApproved !== 'all') {
        params.is_approved = selectedApproved;
      }

      // Only include sort parameters if sort_field has a value
      if (sortField) {
        params.sort_field = sortField;
        params.sort_direction = sortDirection || 'asc';
      }

      if (pageFilters.per_page) {
        params.per_page = pageFilters.per_page;
      }

      // Reload the current page with current filters to get translated data
      router.get(route('billing.expenses.index'), params, {
        preserveState: false,
        preserveScroll: false
      });
    };

    // Listen for both window custom event and i18n language change event
    window.addEventListener('languageChanged', handleLanguageChange);
    i18n.on('languageChanged', handleLanguageChange);

    // Cleanup listeners on unmount
    return () => {
      window.removeEventListener('languageChanged', handleLanguageChange);
      i18n.off('languageChanged', handleLanguageChange);
    };
  }, [searchTerm, selectedCategory, selectedBillable, selectedApproved, sortField, sortDirection, pageFilters, i18n]);

  // Check if any filters are active
  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedCategory !== 'all' || selectedBillable !== 'all' || selectedApproved !== 'all';
  };

  // Count active filters
  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedCategory !== 'all' ? 1 : 0) + (selectedBillable !== 'all' ? 1 : 0) + (selectedApproved !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('billing.expenses.index'), {
      page: 1,
      search: searchTerm || undefined,
      expense_category_id: selectedCategory !== 'all' ? selectedCategory : undefined,
      is_billable: selectedBillable !== 'all' ? selectedBillable : undefined,
      is_approved: selectedApproved !== 'all' ? selectedApproved : undefined,
      sort_field: sortField || undefined,
      sort_direction: sortDirection || undefined,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, item: any) => {
    // Convert receipt_file array to comma-separated string for MediaPicker
    const processedItem = { ...item };
    if (processedItem.receipt_file && Array.isArray(processedItem.receipt_file)) {
      processedItem.receipt_file = processedItem.receipt_file.filter(Boolean).join(',');
    }
    setCurrentItem(processedItem);

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
      case 'approve':
        handleApprove(item);
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
      toast.loading(t('Creating expense...'));

      router.post(route('billing.expenses.store'), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(`Failed to create expense: ${Object.values(errors).join(', ')}`);
        }
      });
    } else if (formMode === 'edit') {
      toast.loading(t('Updating expense...'));

      router.put(route('billing.expenses.update', currentItem.id), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(`Failed to update expense: ${Object.values(errors).join(', ')}`);
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting expense...'));

    router.delete(route('billing.expenses.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(`Failed to delete expense: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleApprove = (expense: any) => {
    const action = expense.is_approved ? 'unapproving' : 'approving';
    toast.loading(t(`${action} expense...`));

    router.put(route('billing.expenses.approve', expense.id), {}, {
      onSuccess: (page) => {
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(`Failed to ${action} expense: ${Object.values(errors).join(', ')}`);
      }
    });
  };

  const handleSort = (field: string) => {
    const newDirection = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
    setSortField(field);
    setSortDirection(newDirection);

    router.get(route('billing.expenses.index'), {
      page: 1,
      search: searchTerm || undefined,
      expense_category_id: selectedCategory !== 'all' ? selectedCategory : undefined,
      is_billable: selectedBillable !== 'all' ? selectedBillable : undefined,
      is_approved: selectedApproved !== 'all' ? selectedApproved : undefined,
      sort_field: field,
      sort_direction: newDirection,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedCategory('all');
    setSelectedBillable('all');
    setSelectedApproved('all');
    setSortField('');
    setSortDirection('asc');
    setShowFilters(false);

    router.get(route('billing.expenses.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  // Define page actions
  const pageActions = [];

  if (hasPermission(permissions, 'create-expenses')) {
    pageActions.push({
      label: t('Add Expense'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Billing & Invoicing'), href: route('billing.time-entries.index') },
    { title: t('Expenses') }
  ];

  const getCategoryLabel = (category: any) => {
    if (!category) return '';
    const translations = category.name_translations || (typeof category.name === 'object' ? category.name : null);
    if (translations && typeof translations === 'object') {
      return translations[currentLocale] || translations.en || translations.ar || '';
    }
    return category.name || '';
  };

  // Define table columns
  const columns = [
    {
      key: 'case',
      label: t('Case'),
      render: (value: any, row: any) => {
        // if (!row.case_id) return t('General');
        const caseItem = (cases || []).find((c: any) => c.id === row.case_id);
        return caseItem ? `${caseItem.case_id} - ${caseItem.title}` : '-';
      }
    },
    {
      key: 'expense_category',
      label: t('Category'),
      render: (value: any, row: any) => {
        // Fallback to finding in categories list
        const category = (categories || []).find((cat: any) => cat.id === row.expense_category_id);
        if (!category) return '-';

        return getCategoryLabel(category);
      }
    },
    {
      key: 'description',
      label: t('Description'),
      render: (value: string) => (
        <div className="max-w-md truncate" title={value}>
          {value}
        </div>
      )
    },
    {
      key: 'amount',
      label: t('Amount'),
      render: (value: any) => {
        const amount = parseFloat(value);
        return isNaN(amount) ? formatCurrency(0.00) : formatCurrency(amount);
      }
    },
    {
      key: 'expense_date',
      label: t('Date'),
      render: (value: string) => window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()
    },
    {
      key: 'is_billable',
      label: t('Billable'),
      render: (value: boolean) => (
        <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ${value
          ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'
          : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20'
          }`}>
          {value ? t('Yes') : t('No')}
        </span>
      )
    },
    {
      key: 'is_approved',
      label: t('Status'),
      render: (value: boolean) => (
        <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ${value
          ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'
          : 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20'
          }`}>
          {value ? t('Approved') : t('Pending')}
        </span>
      )
    }
  ];

  // Define table actions
  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-expenses'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-expenses'
    },
    {
      label: t('Approve'),
      icon: 'CheckCircle',
      action: 'approve',
      className: 'text-green-500',
      requiredPermission: 'approve-expenses',
      condition: (row: any) => !row.is_approved
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-expenses'
    }
  ];

  // Prepare filter options
  const categoryOptions = [
    { value: 'all', label: t('All Categories') },
    ...(categories || []).map((category: any) => ({
      value: category.id.toString(),
      label: getCategoryLabel(category)
    }))
  ];

  const billableOptions = [
    { value: 'all', label: t('All') },
    { value: '1', label: t('Billable') },
    { value: '0', label: t('Non-billable') }
  ];

  const approvedOptions = [
    { value: 'all', label: t('All Status') },
    { value: '1', label: t('Approved') },
    { value: '0', label: t('Pending') }
  ];

  return (
      <PageTemplate title={t('Expenses')} url="/billing/expenses" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          {/* Search and filters section */}
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={[
                      {
                          name: 'expense_category_id',
                          label: t('Category'),
                          type: 'select',
                          value: selectedCategory,
                          onChange: setSelectedCategory,
                          options: categoryOptions,
                      },
                      {
                          name: 'is_billable',
                          label: t('Billable'),
                          type: 'select',
                          value: selectedBillable,
                          onChange: setSelectedBillable,
                          options: billableOptions,
                      },
                      {
                          name: 'is_approved',
                          label: t('Status'),
                          type: 'select',
                          value: selectedApproved,
                          onChange: setSelectedApproved,
                          options: approvedOptions,
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
                  data={expenses?.data || []}
                  from={expenses?.from || 1}
                  onAction={handleAction}
                  sortField={sortField}
                  sortDirection={sortDirection}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-expenses',
                      create: 'create-expenses',
                      edit: 'edit-expenses',
                      delete: 'delete-expenses',
                  }}
              />

              {/* Pagination section */}
              <Pagination
                  from={expenses?.from || 0}
                  to={expenses?.to || 0}
                  total={expenses?.total || 0}
                  links={expenses?.links}
                  entityName={t('expenses')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('billing.expenses.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              expense_category_id: selectedCategory !== 'all' ? selectedCategory : undefined,
                              is_billable: selectedBillable !== 'all' ? selectedBillable : undefined,
                              is_approved: selectedApproved !== 'all' ? selectedApproved : undefined,
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
              isOpen={isFormModalOpen && formMode !== 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      {
                          name: 'case_id',
                          label: t('Case'),
                          type: 'select',
                          options: [
                              // { value: '', label: t('No Case (General Expense)') },
                              ...(cases || []).map((caseItem: any) => ({
                                  value: caseItem.id.toString(),
                                  label: caseItem.case_id ? `${caseItem.case_id} - ${caseItem.title}` : caseItem.title,
                              })),
                          ],
                      },
                      {
                          name: 'expense_category_id',
                          label: t('Category'),
                          type: 'select',
                          required: true,
                          options: (categories || [])
                              .filter((category) => category.id && getCategoryLabel(category))
                              .map((category: any) => ({
                                  value: category.id.toString(),
                                  label: getCategoryLabel(category),
                              })),
                      },
                      { name: 'description', label: t('Description'), type: 'text', required: true },
                      { name: 'amount', label: t('Amount'), type: 'number', step: '0.01', required: true, min: '0' },
                      { name: 'expense_date', label: t('Expense Date'), type: 'date', required: true },
                      {
                          name: 'is_billable',
                          label: t('Billable'),
                          type: 'select',
                          options: [
                              { value: '1', label: t('Yes') },
                              { value: '0', label: t('No') },
                          ],
                          defaultValue: '0',
                      },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                      {
                          name: 'receipt_file',
                          label: t('Files'),
                          type: 'media-picker',
                          multiple: true,
                          placeholder: t('Select files...'),
                      },
                  ],
                  modalSize: 'lg',
              }}
              initialData={currentItem}
              title={formMode === 'create' ? t('Add New Expense') : t('Edit Expense')}
              mode={formMode !== 'view' ? formMode : 'create'}
          />

          {/* View Modal */}
          <CrudFormModal
              isOpen={isFormModalOpen && formMode === 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      {
                          name: 'category',
                          label: t('Category'),
                          type: 'text',
                          render: () => {
                              const category = (categories || []).find((cat: any) => cat.id === currentItem?.expense_category_id);
                              return <div className="rounded-md border bg-gray-50 p-2">{category ? getCategoryLabel(category) : '-'}</div>;
                          },
                      },
                      { name: 'description', label: t('Description'), type: 'text' },
                      {
                          name: 'amount',
                          label: t('Amount'),
                          type: 'text',
                          render: () => {
                              const amount = parseFloat(currentItem?.amount);
                              return (
                                  <div className="rounded-md border bg-gray-50 p-2">
                                      {isNaN(amount) ? formatCurrency(0.0) : formatCurrency(amount)}
                                  </div>
                              );
                          },
                      },
                      { name: 'expense_date', label: t('Expense Date'), type: 'text' },
                      {
                          name: 'is_billable',
                          label: t('Billable'),
                          type: 'text',
                          render: () => {
                              const isBillable = currentItem?.is_billable;
                              return <div className="rounded-md border bg-gray-50 p-2">{isBillable ? t('Yes') : t('No')}</div>;
                          },
                      },
                      {
                          name: 'is_approved',
                          label: t('Status'),
                          type: 'text',
                          render: () => {
                              const isApproved = currentItem?.is_approved;
                              return <div className="rounded-md border bg-gray-50 p-2">{isApproved ? t('Approved') : t('Pending')}</div>;
                          },
                      },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                      {
                          name: 'receipt_file',
                          label: t('Files'),
                          type: 'custom',
                          render: (field, formData) => {
                              const files = formData[field.name];
                              if (!files) {
                                  return <div className="rounded-md border bg-gray-50 p-2">-</div>;
                              }

                              // Handle both comma-separated string and array
                              const fileList =
                                  typeof files === 'string'
                                      ? files
                                            .split(',')
                                            .filter(Boolean)
                                            .map((f) => f.trim())
                                      : Array.isArray(files)
                                        ? files.filter(Boolean)
                                        : [];

                              if (fileList.length === 0) {
                                  return <div className="rounded-md border bg-gray-50 p-2">-</div>;
                              }

                              // Get display URL helper
                              const getDisplayUrl = (url: string) => {
                                  if (!url) return '';
                                  if (url.startsWith('http')) return url;
                                  if (url.startsWith('/')) {
                                      return `${window.appSettings?.imageUrl || window.location.origin}${url}`;
                                  }
                                  return `${window.appSettings?.imageUrl || window.location.origin}/${url}`;
                              };

                              // Get file extension
                              const getFileExtension = (path: string) => {
                                  const filename = path.split('/').pop() || path;
                                  return filename.split('.').pop()?.toLowerCase() || '';
                              };

                              // Check file type
                              const isImage = (path: string) => {
                                  const ext = getFileExtension(path);
                                  return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext);
                              };

                              const isPdf = (path: string) => {
                                  return getFileExtension(path) === 'pdf';
                              };

                              const isDoc = (path: string) => {
                                  const ext = getFileExtension(path);
                                  return ['doc', 'docx'].includes(ext);
                              };

                              return (
                                  <div className="space-y-2">
                                      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                          {fileList.map((filePath, index) => {
                                              const displayUrl = getDisplayUrl(filePath);
                                              const filename = filePath.split('/').pop() || filePath;

                                              return (
                                                  <div key={index} className="group relative">
                                                      <a href={displayUrl} target="_blank" rel="noopener noreferrer" className="block">
                                                          {isImage(filePath) ? (
                                                              <div className="relative aspect-square overflow-hidden rounded border bg-gray-50">
                                                                  <img
                                                                      src={displayUrl}
                                                                      alt={filename}
                                                                      className="h-full w-full object-cover"
                                                                      onError={(e) => {
                                                                          const target = e.target as HTMLImageElement;
                                                                          target.style.display = 'none';
                                                                          const fallback = target.nextElementSibling as HTMLElement;
                                                                          if (fallback) fallback.style.display = 'flex';
                                                                      }}
                                                                  />
                                                                  <div className="hidden h-full w-full flex-col items-center justify-center rounded bg-gray-100">
                                                                      <span className="text-xs text-gray-600">Image</span>
                                                                  </div>
                                                              </div>
                                                          ) : isPdf(filePath) ? (
                                                              <div className="relative flex aspect-square flex-col items-center justify-center rounded border border-red-200 bg-red-50">
                                                                  <div className="mb-1 flex h-8 w-8 items-center justify-center rounded bg-red-100">
                                                                      <FileText className="h-5 w-5 text-red-600" />
                                                                  </div>
                                                                  <span className="text-xs font-medium text-red-600">PDF</span>
                                                              </div>
                                                          ) : isDoc(filePath) ? (
                                                              <div className="relative flex aspect-square flex-col items-center justify-center rounded border border-blue-200 bg-blue-50">
                                                                  <div className="mb-1 flex h-8 w-8 items-center justify-center rounded bg-blue-100">
                                                                      <FileText className="h-5 w-5 text-blue-600" />
                                                                  </div>
                                                                  <span className="text-xs font-medium text-blue-600">DOC</span>
                                                              </div>
                                                          ) : (
                                                              <div className="relative flex aspect-square flex-col items-center justify-center rounded border border-gray-200 bg-gray-50">
                                                                  <div className="mb-1 flex h-8 w-8 items-center justify-center rounded bg-gray-100">
                                                                      <File className="h-5 w-5 text-gray-600" />
                                                                  </div>
                                                                  <span className="text-xs font-medium text-gray-600">FILE</span>
                                                              </div>
                                                          )}
                                                      </a>
                                                      <div className="mt-1 truncate text-xs text-gray-600" title={filename}>
                                                          {filename}
                                                      </div>
                                                  </div>
                                              );
                                          })}
                                      </div>
                                  </div>
                              );
                          },
                      },
                  ],
                  modalSize: 'lg',
              }}
              initialData={currentItem}
              title={t('View Expense')}
              mode="view"
          />

          {/* Delete Modal */}
          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentItem?.description || ''}
              entityName="expense"
          />
      </PageTemplate>
  );
}