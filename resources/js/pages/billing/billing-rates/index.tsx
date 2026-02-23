import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus, DollarSign } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Switch } from '@/components/ui/switch';
import { CurrencyAmount } from '@/components/currency-amount';

export default function BillingRates() {
  const { t } = useTranslation();
  const { auth, billingRates, users, clients, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];

  // State
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedUser, setSelectedUser] = useState(pageFilters.user_id || 'all');
  const [selectedClient, setSelectedClient] = useState(pageFilters.client_id || 'all');
  const [selectedRateType, setSelectedRateType] = useState(pageFilters.rate_type || 'all');
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
    return searchTerm !== '' || selectedUser !== 'all' || selectedClient !== 'all' || 
           selectedRateType !== 'all' || selectedStatus !== 'all';
  };

  // Count active filters
  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0) + (selectedUser !== 'all' ? 1 : 0) + (selectedClient !== 'all' ? 1 : 0) + 
           (selectedRateType !== 'all' ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('billing.billing-rates.index'), {
      page: 1,
      search: searchTerm || undefined,
      user_id: selectedUser !== 'all' ? selectedUser : undefined,
      client_id: selectedClient !== 'all' ? selectedClient : undefined,
      rate_type: selectedRateType !== 'all' ? selectedRateType : undefined,
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
      router.post(route('billing.billing-rates.store'), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t('Failed to create {{model}}: {{errors}}', { model: t('Billing rate'), errors: Object.values(errors).join(', ') }));
        }
      });
    } else if (formMode === 'edit') {
      router.put(route('billing.billing-rates.update', currentItem.id), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t('Failed to update {{model}}: {{errors}}', { model: t('Billing rate'), errors: Object.values(errors).join(', ') }));
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('billing.billing-rates.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(t('Failed to delete {{model}}: {{errors}}', { model: t('Billing rate'), errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleToggleStatus = (billingRate: any) => {
    router.put(route('billing.billing-rates.toggle-status', billingRate.id), {}, {
      onSuccess: (page) => {
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(t('Failed to update {{model}} status: {{errors}}', { model: t('Billing rate'), errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleSort = (field: string) => {
    const newDirection = sortField === field && sortDirection === 'asc' ? 'desc' : 'asc';
    setSortField(field);
    setSortDirection(newDirection);
    
    router.get(route('billing.billing-rates.index'), {
      page: 1,
      search: searchTerm || undefined,
      user_id: selectedUser !== 'all' ? selectedUser : undefined,
      client_id: selectedClient !== 'all' ? selectedClient : undefined,
      rate_type: selectedRateType !== 'all' ? selectedRateType : undefined,
      status: selectedStatus !== 'all' ? selectedStatus : undefined,
      sort_field: field,
      sort_direction: newDirection,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedUser('all');
    setSelectedClient('all');
    setSelectedRateType('all');
    setSelectedStatus('all');
    setSortField('');
    setSortDirection('asc');
    setShowFilters(false);

    router.get(route('billing.billing-rates.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  // Define page actions
  const pageActions = [];

  if (hasPermission(permissions, 'create-billing-rates')) {
    pageActions.push({
      label: t('Add Billing Rate'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Billing & Invoicing'), href: route('billing.billing-rates.index') },
    { title: t('Billing Rates') }
  ];

  // Define table columns
  const columns = [
    {
      key: 'user',
      label: t('User'),
      render: (value: any) => value?.name || '-'
    },
    {
      key: 'client',
      label: t('Client'),
      render: (value: any) => value?.name || t('Default Rate')
    },
    {
      key: 'rate_type',
      label: t('Rate Type'),
      render: (value: string) => (
        <span className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
          {t(value?.charAt(0).toUpperCase() + value?.slice(1))}
        </span>
      )
    },
    {
      key: 'display_rate',
      label: t('Rate'),
      render: (value: any, row: any) => {
        switch (row.rate_type) {
          case 'hourly':
            return <><CurrencyAmount amount={row.hourly_rate} />/hr</>;
          case 'fixed':
            return <CurrencyAmount amount={row.fixed_amount} />;
          case 'contingency':
            return `${row.contingency_percentage}%`;
          default:
            return '-';
        }
      }
    },
    {
      key: 'effective_date',
      label: t('Effective Date'),
      sortable: true,
      render: (value: string) => window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()
    },
    {
      key: 'end_date',
      label: t('End Date'),
      render: (value: string) => value ? (window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()) : '-'
    },
    {
      key: 'status',
      label: t('Status'),
      render: (value: string, row: any) => {
        const canToggleStatus = hasPermission(permissions, 'toggle-status-billing-rates');
        return (
          <div className="flex items-center gap-2">
            <Switch
              checked={value === 'active'}
              disabled={!canToggleStatus}
              onCheckedChange={() => {
                if (!canToggleStatus) return;
                handleToggleStatus(row);
              }}
              aria-label={value === 'active' ? t('Deactivate billing rate') : t('Activate billing rate')}
            />
            <span className="text-muted-foreground text-xs">{value === 'active' ? t('Active') : t('Inactive')}</span>
          </div>
        );
      }
    }
  ];

  // Define table actions
  const actions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-billing-rates'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-billing-rates'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-billing-rates'
    }
  ];

  // Prepare filter options
  const userOptions = [
    { value: 'all', label: t('All Users') },
    ...(users || []).map((user: any) => ({
      value: user.id.toString(),
      label: user.name
    }))
  ];

  const clientOptions = [
    { value: 'all', label: t('All Clients') },
    { value: 'null', label: t('Default Rate') },
    ...(clients || []).map((client: any) => ({
      value: client.id.toString(),
      label: client.name
    }))
  ];

  const rateTypeOptions = [
    { value: 'all', label: t('All Types') },
    { value: 'hourly', label: t('Hourly') },
    { value: 'fixed', label: t('Fixed') },
    { value: 'contingency', label: t('Contingency') }
  ];

  const statusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'active', label: t('Active') },
    { value: 'inactive', label: t('Inactive') }
  ];

  return (
      <PageTemplate title={t('Billing Rates')} url="/billing/billing-rates" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          {/* Search and filters section */}
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={[
                      {
                          name: 'user_id',
                          label: t('User'),
                          type: 'select',
                          value: selectedUser,
                          onChange: setSelectedUser,
                          options: userOptions,
                      },
                      {
                          name: 'client_id',
                          label: t('Client'),
                          type: 'select',
                          value: selectedClient,
                          onChange: setSelectedClient,
                          options: clientOptions,
                      },
                      {
                          name: 'rate_type',
                          label: t('Rate Type'),
                          type: 'select',
                          value: selectedRateType,
                          onChange: setSelectedRateType,
                          options: rateTypeOptions,
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

          {/* Content section */}
          <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={billingRates?.data || []}
                  from={billingRates?.from || 1}
                  onAction={handleAction}
                  sortField={sortField}
                  sortDirection={sortDirection}
                  onSort={handleSort}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-billing-rates',
                      create: 'create-billing-rates',
                      edit: 'edit-billing-rates',
                      delete: 'delete-billing-rates',
                  }}
              />

              {/* Pagination section */}
              <Pagination
                  from={billingRates?.from || 0}
                  to={billingRates?.to || 0}
                  total={billingRates?.total || 0}
                  links={billingRates?.links}
                  entityName={t('billing rates')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('billing.billing-rates.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              user_id: selectedUser !== 'all' ? selectedUser : undefined,
                              client_id: selectedClient !== 'all' ? selectedClient : undefined,
                              rate_type: selectedRateType !== 'all' ? selectedRateType : undefined,
                              status: selectedStatus !== 'all' ? selectedStatus : undefined,
                              sort_field: sortField || undefined,
                              sort_direction: sortDirection || undefined,
                          },
                          { preserveState: true, preserveScroll: true },
                      );
                  }}
              />
          </div>

          {/* Form Modal (Create/Edit) */}
          <CrudFormModal
              isOpen={isFormModalOpen && formMode !== 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={handleFormSubmit}
              formConfig={{
                  fields: [
                      {
                          name: 'user_id',
                          label: t('Team Member'),
                          type: 'select',
                          required: true,
                          options: (users || []).map((user: any) => ({
                              value: user.id.toString(),
                              label: user.name,
                          })),
                      },
                      {
                          name: 'client_id',
                          label: t('Client'),
                          type: 'select',
                          options: [
                              { value: null, label: t('Default Rate (All Clients)') },
                              ...(clients || []).map((client: any) => ({
                                  value: client.id,
                                  label: client.name,
                              })),
                          ],
                      },
                      {
                          name: 'rate_type',
                          label: t('Rate Type'),
                          type: 'select',
                          required: true,
                          options: [
                              { value: 'hourly', label: t('Hourly') },
                              { value: 'fixed', label: t('Fixed') },
                              { value: 'contingency', label: t('Contingency') },
                          ],
                      },
                      { name: 'hourly_rate', label: t('Hourly Rate'), type: 'number', step: '0.01', min: '0' },
                      { name: 'fixed_amount', label: t('Fixed Amount'), type: 'number', step: '0.01', min: '0' },
                      { name: 'contingency_percentage', label: t('Contingency %'), type: 'number', step: '0.01', min: '0', max: '100' },
                      { name: 'effective_date', label: t('Effective Date'), type: 'date', required: true },
                      { name: 'end_date', label: t('End Date'), type: 'date' },
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
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                  ],
                  modalSize: 'xl',
              }}
              initialData={currentItem}
              title={formMode === 'create' ? t('Add New Billing Rate') : t('Edit Billing Rate')}
              mode={formMode}
          />

          {/* View Modal */}
          <CrudFormModal
              isOpen={isFormModalOpen && formMode === 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      {
                          name: 'user',
                          label: t('User'),
                          type: 'text',
                          render: () => {
                              return <div className="rounded-md border bg-gray-50 p-2">{currentItem?.user?.name || '-'}</div>;
                          },
                      },
                      {
                          name: 'client',
                          label: t('Client'),
                          type: 'text',
                          render: () => {
                              return (
                                  <div className="rounded-md border bg-gray-50 p-2">
                                      {currentItem?.client?.name || t('Default Rate (All Clients)')}
                                  </div>
                              );
                          },
                      },
                      {
                          name: 'rate_type',
                          label: t('Rate Type'),
                          type: 'text',
                          render: () => {
                              const rateType = currentItem?.rate_type;
                              return (
                                  <div className="rounded-md border bg-gray-50 p-2">{t(rateType?.charAt(0).toUpperCase() + rateType?.slice(1))}</div>
                              );
                          },
                      },
                      {
                          name: 'rate_display',
                          label: t('Rate'),
                          type: 'text',
                          render: () => {
                              const item = currentItem;
                              if (!item) return <div className="rounded-md border bg-gray-50 p-2">-</div>;
                              switch (item.rate_type) {
                                  case 'hourly':
                                      return <div className="rounded-md border bg-gray-50 p-2"><CurrencyAmount amount={item.hourly_rate} />/hr</div>;
                                  case 'fixed':
                                      return <div className="rounded-md border bg-gray-50 p-2"><CurrencyAmount amount={item.fixed_amount} /></div>;
                                  case 'contingency':
                                      return <div className="rounded-md border bg-gray-50 p-2">{item.contingency_percentage}%</div>;
                                  default:
                                      return <div className="rounded-md border bg-gray-50 p-2">-</div>;
                              }
                          },
                      },
                      { name: 'effective_date', label: t('Effective Date'), type: 'text' },
                      { name: 'end_date', label: t('End Date'), type: 'text' },
                      {
                          name: 'is_active',
                          label: t('Status'),
                          type: 'text',
                          render: () => {
                              const status = currentItem?.status;
                              return <div className="rounded-md border bg-gray-50 p-2">{status === 'active' ? t('Active') : t('Inactive')}</div>;
                          },
                      },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                  ],
                  modalSize: 'xl',
              }}
              initialData={currentItem}
              title={t('View Billing Rate')}
              mode="view"
          />

          {/* Delete Modal */}
          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={`${currentItem?.user?.name} - ${currentItem?.client?.name || 'Default'}`}
              entityName="Billing Rate"
          />
      </PageTemplate>
  );
}