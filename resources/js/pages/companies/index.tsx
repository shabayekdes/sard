// pages/companies/index.tsx
import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Filter, Search, Plus, Eye, Edit, Trash2, KeyRound, Lock, Unlock, LayoutGrid, List, Info, ArrowUpRight, CreditCard, History } from 'lucide-react';
import { toast } from '@/components/custom-toast';
import { useInitials } from '@/hooks/use-initials';
import { useTranslation } from 'react-i18next';
import { DatePicker } from '@/components/ui/date-picker';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { CrudTable } from '@/components/CrudTable';
import { UpgradePlanModal } from '@/components/UpgradePlanModal';

export default function Companies() {
  const { t } = useTranslation();
  const { auth, companies, plans, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const getInitials = useInitials();

  // State
  const [activeView, setActiveView] = useState('list');
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [startDate, setStartDate] = useState<Date | undefined>(pageFilters.start_date ? new Date(pageFilters.start_date) : undefined);
  const [endDate, setEndDate] = useState<Date | undefined>(pageFilters.end_date ? new Date(pageFilters.end_date) : undefined);
  const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
  const [showFilters, setShowFilters] = useState(false);

  // Modal state
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isResetPasswordModalOpen, setIsResetPasswordModalOpen] = useState(false);
  const [isUpgradePlanModalOpen, setIsUpgradePlanModalOpen] = useState(false);

  const [currentCompany, setCurrentCompany] = useState<any>(null);
  const [availablePlans, setAvailablePlans] = useState<any[]>([]);


  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  // Check if any filters are active
  const hasActiveFilters = () => {
    return selectedStatus !== 'all' || searchTerm !== '' || startDate !== undefined || endDate !== undefined;
  };

  // Count active filters
  const activeFilterCount = () => {
    return (selectedStatus !== 'all' ? 1 : 0) +
           (searchTerm ? 1 : 0) +
           (startDate ? 1 : 0) +
           (endDate ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    const params: any = { page: 1 };

    if (searchTerm) {
      params.search = searchTerm;
    }

    if (selectedStatus !== 'all') {
      params.status = selectedStatus;
    }

    if (startDate) {
      params.start_date = startDate.toISOString().split('T')[0];
    }

    if (endDate) {
      params.end_date = endDate.toISOString().split('T')[0];
    }

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(route('companies.index'), params, { preserveState: true, preserveScroll: true });
  };

  const handleStatusFilter = (value: string) => {
    setSelectedStatus(value);

    const params: any = { page: 1 };

    if (searchTerm) {
      params.search = searchTerm;
    }

    if (value !== 'all') {
      params.status = value;
    }

    if (startDate) {
      params.start_date = startDate.toISOString().split('T')[0];
    }

    if (endDate) {
      params.end_date = endDate.toISOString().split('T')[0];
    }

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(route('companies.index'), params, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    const params: any = {
      sort_field: field,
      sort_direction: direction,
      page: 1
    };

    // Add search and filters
    if (searchTerm) {
      params.search = searchTerm;
    }

    if (selectedStatus !== 'all') {
      params.status = selectedStatus;
    }

    if (startDate) {
      params.start_date = startDate.toISOString().split('T')[0];
    }

    if (endDate) {
      params.end_date = endDate.toISOString().split('T')[0];
    }

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(route('companies.index'), params, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, company: any) => {
    setCurrentCompany(company);

    switch (action) {
      case 'login-as':
        window.open(route('companies.impersonate', company.id), '_blank');
        break;
      case 'company-info':
        setFormMode('view');
        setIsFormModalOpen(true);
        break;
      case 'upgrade-plan':
        handleUpgradePlan(company);
        break;
      case 'user-logs':
        router.get(route('users.logs', company.id));
        break;
      case 'reset-password':
        setIsResetPasswordModalOpen(true);
        break;
      case 'toggle-status':
        handleToggleStatus(company);
        break;
      case 'edit':
        setFormMode('edit');
        setIsFormModalOpen(true);
        break;
      case 'delete':
        setIsDeleteModalOpen(true);
        break;
      default:
        break;
    }
  };

  const handleAddNew = () => {
    setCurrentCompany(null);
    setFormMode('create');
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    if (formMode === 'create') {
      toast.loading(t('Creating company...'));

      router.post(route('companies.store'), formData, {
        onSuccess: () => {
          setIsFormModalOpen(false);
          toast.dismiss();
          toast.success(t('Company created successfully'));
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(`Failed to create company: ${Object.values(errors).join(', ')}`);
        }
      });
    } else if (formMode === 'edit') {
      toast.loading(t('Updating company...'));

      router.put(route('companies.update', currentCompany.id), formData, {
        onSuccess: () => {
          setIsFormModalOpen(false);
          toast.dismiss();
          toast.success(t('Company updated successfully'));
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(`Failed to update company: ${Object.values(errors).join(', ')}`);
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    toast.loading(t('Deleting company...'));

    router.delete(route("companies.destroy", currentCompany.id), {
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
        } else if (errors.error) {
          toast.error(errors.error);
        } else {
          toast.error(`Failed to delete company: ${Object.values(errors).join(', ')}`);
        }
      }
    });
  };

  const handleResetPasswordConfirm = (data: { password: string }) => {
    toast.loading(t('Resetting password...'));

    router.put(route('companies.reset-password', currentCompany.id), data, {
      onSuccess: (page) => {
        setIsResetPasswordModalOpen(false);
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
        } else if (errors.error) {
          toast.error(errors.error);
        } else {
          toast.error(`Failed to reset password: ${Object.values(errors).join(', ')}`);
        }
      }
    });
  };

  const handleToggleStatus = (company: any) => {
    toast.loading(t('Updating status...'));

    router.put(route('companies.toggle-status', company.id), {}, {
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
        } else if (errors.error) {
          toast.error(errors.error);
        } else {
          toast.error(`Failed to update status: ${Object.values(errors).join(', ')}`);
        }
      }
    });
  };

  const handleResetFilters = () => {
    setSelectedStatus('all');
    setSearchTerm('');
    setStartDate(undefined);
    setEndDate(undefined);
    setShowFilters(false);

    router.get(route('companies.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleUpgradePlan = (company: any) => {
    setCurrentCompany(company);

    // Fetch available plans
    toast.loading(t('Loading plans...'));
    fetch(route('companies.plans', company.id))
    .then(res => res.json())
    .then(data => {
        setAvailablePlans(data.plans);
        setIsUpgradePlanModalOpen(true);
        toast.dismiss();
      })
      .catch(err => {
        toast.dismiss();
        toast.error(t('Failed to load plans'));
      });
  };

  const handleUpgradePlanConfirm = (planId: number) => {
    toast.loading(t('Upgrading plan...'));

    // Use Inertia router to handle the request
    router.put(route('companies.upgrade-plan', currentCompany.id), {
      plan_id: planId
    }, {
      onSuccess: (page) => {
        setIsUpgradePlanModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        } else if (page.props.flash.error) {
          toast.error(page.props.flash.error);
        }
        router.reload();
      },
      onError: (errors) => {
        toast.dismiss();
        if (typeof errors === 'string') {
          toast.error(errors);
        } else if (errors.error) {
          toast.error(errors.error);
        } else {
          toast.error(t('Failed to upgrade plan'));
        }
      }
    });
  };





  // Define page actions
  const pageActions = [
    {
      label: t('Add Company'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    },
    {
      label: t('User Logs History'),
      icon: <History className="h-4 w-4 mr-2" />,
      variant: 'outline',
      onClick: () => router.get(route('user-logs.index'))
    }
  ];

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Companies') }
  ];

  // Define table columns for list view
  const columns = [
    {
      key: 'name',
      label: t('Name'),
      sortable: true,
      render: (value: any, row: any) => {
        return (
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
              {getInitials(row.name)}
            </div>
            <div>
              <div className="font-medium">{row.name}</div>
              <div className="text-sm text-muted-foreground">{row.email}</div>
            </div>
          </div>
        );
      }
    },
    {
      key: 'plan_name',
      label: t('Plan'),
      render: (value: string) => <span className="capitalize">{value}</span>
    },
    {
      key: 'plan_expire_date',
      label: t('Plan Expire Date'),
      render: (value: string) =>
        value
          ? (window.appSettings?.formatDateTime(value, false) || new Date(value).toLocaleDateString())
          : '-'
    },
    {
      key: 'latest_plan_ordered_at',
      label: t('Latest Plan Order'),
      render: (value: string) =>
        value
          ? (window.appSettings?.formatDateTime(value, false) || new Date(value).toLocaleDateString())
          : '-'
    },
    {
      key: 'created_at',
      label: t('Created At'),
      sortable: true,
      render: (value: string) => window.appSettings?.formatDateTime(value, false) || new Date(value).toLocaleDateString()
    }
  ];

  // Define table actions for CrudTable (Login as: open new tab then server redirects to tenant and logs in)
  const tableActions = [
    {
      label: t('Login as Company'),
      icon: 'ArrowUpRight',
      action: 'login-as',
      className: 'text-blue-500 hover:text-blue-700',
    },
    {
      label: t('Company Info'),
      icon: 'Info',
      action: 'company-info',
      className: 'text-blue-500 hover:text-blue-700',
    },
    {
      label: t('Upgrade Plan'),
      icon: 'CreditCard',
      action: 'upgrade-plan',
      className: 'text-amber-500 hover:text-amber-700',
    },
    {
      label: t('Reset Password'),
      icon: 'KeyRound',
      action: 'reset-password',
      className: 'text-blue-500 hover:text-blue-700',
    },
    {
      label: (row: any) => (row?.status === 'active' ? t('Disable Login') : t('Enable Login')),
      icon: 'toggle-status',
      action: 'toggle-status',
      className: 'text-amber-500 hover:text-amber-700',
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500 hover:text-amber-700',
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500 hover:text-red-700',
    },
  ];

  return (
      <PageTemplate title={t('Companies Management')} url="/companies" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
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
                          onChange: handleStatusFilter,
                          options: [
                              { value: 'all', label: t('All Status') },
                              { value: 'active', label: t('Active') },
                              { value: 'inactive', label: t('Inactive') },
                          ],
                      },
                      {
                          name: 'start_date',
                          label: t('Start Date'),
                          type: 'date',
                          value: startDate,
                          onChange: (date) => setStartDate(date),
                      },
                      {
                          name: 'end_date',
                          label: t('End Date'),
                          type: 'date',
                          value: endDate,
                          onChange: (date) => setEndDate(date),
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
                      const params: any = { page: 1, per_page: parseInt(value) };

                      if (searchTerm) {
                          params.search = searchTerm;
                      }

                      if (selectedStatus !== 'all') {
                          params.status = selectedStatus;
                      }

                      if (startDate) {
                          params.start_date = startDate.toISOString().split('T')[0];
                      }

                      if (endDate) {
                          params.end_date = endDate.toISOString().split('T')[0];
                      }

                      router.get(route('companies.index'), params, { preserveState: true, preserveScroll: true });
                  }}
                  showViewToggle={true}
                  activeView={activeView}
                  onViewChange={setActiveView}
              />
          </div>

          {/* Content section */}
          {activeView === 'list' ? (
              <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
                  <CrudTable
                      columns={columns}
                      actions={tableActions}
                      data={companies?.data || []}
                      from={companies?.from ?? 1}
                      onAction={handleAction}
                      sortField={pageFilters.sort_field}
                      sortDirection={pageFilters.sort_direction}
                      onSort={handleSort}
                      permissions={permissions}
                  />

                  {/* Pagination section */}
                  <Pagination
                      from={companies?.from || 0}
                      to={companies?.to || 0}
                      total={companies?.total || 0}
                      links={companies?.links}
                      entityName={t('companies')}
                      onPageChange={(url) => router.get(url)}
                  />
              </div>
          ) : (
              <div>
                  {/* Grid View */}
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                      {companies?.data?.map((company: any) => (
                          <Card key={company.id} className="rounded-lg border border-gray-300 bg-white shadow dark:border-gray-700 dark:bg-gray-900">
                              {/* Header */}
                              <div className="p-6">
                                  <div className="mb-4 flex items-start justify-between">
                                      <div className="flex items-start space-x-4">
                                          <div className="flex h-16 w-16 items-center justify-center rounded-full bg-gray-200 text-lg font-bold text-gray-700">
                                              {getInitials(company.name)}
                                          </div>
                                          <div className="min-w-0 flex-1">
                                              <h3 className="mb-2 text-lg font-bold text-gray-900">{company.name}</h3>
                                              <p className="mb-3 text-sm text-gray-600">{company.email}</p>
                                              <div className="flex items-center">
                                                  <div
                                                      className={`mr-2 h-2 w-2 rounded-full ${
                                                          company.status === 'active' ? 'bg-gray-800' : 'bg-gray-400'
                                                      }`}
                                                  ></div>
                                                  <span className="text-sm font-medium text-gray-700">
                                                      {company.status === 'active' ? t('Active') : t('Inactive')}
                                                  </span>
                                              </div>
                                          </div>
                                      </div>

                                      {/* Actions dropdown */}
                                      <DropdownMenu>
                                          <DropdownMenuTrigger asChild>
                                              <Button variant="ghost" size="sm" className="h-8 w-8 p-0 text-gray-400 hover:text-gray-600">
                                                  <svg
                                                      xmlns="http://www.w3.org/2000/svg"
                                                      width="16"
                                                      height="16"
                                                      viewBox="0 0 24 24"
                                                      fill="none"
                                                      stroke="currentColor"
                                                      strokeWidth="2"
                                                      strokeLinecap="round"
                                                      strokeLinejoin="round"
                                                  >
                                                      <circle cx="12" cy="12" r="1"></circle>
                                                      <circle cx="12" cy="5" r="1"></circle>
                                                      <circle cx="12" cy="19" r="1"></circle>
                                                  </svg>
                                              </Button>
                                          </DropdownMenuTrigger>
                                          <DropdownMenuContent align="end" className="z-50 w-48" sideOffset={5}>
                                              <DropdownMenuItem onClick={() => handleAction('login-as', company)}>
                                                  <ArrowUpRight className="mr-2 h-4 w-4" />
                                                  <span>{t('Login as Company')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('company-info', company)}>
                                                  <Info className="mr-2 h-4 w-4" />
                                                  <span>{t('Company Info')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('upgrade-plan', company)}>
                                                  <CreditCard className="mr-2 h-4 w-4" />
                                                  <span>{t('Upgrade Plan')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('user-logs', company)}>
                                                  <History className="mr-2 h-4 w-4" />
                                                  <span>{t('User Logs History')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('reset-password', company)}>
                                                  <KeyRound className="mr-2 h-4 w-4" />
                                                  <span>{t('Reset Password')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('toggle-status', company)}>
                                                  {company.status === 'active' ? (
                                                      <Lock className="mr-2 h-4 w-4" />
                                                  ) : (
                                                      <Unlock className="mr-2 h-4 w-4" />
                                                  )}
                                                  <span>{company.status === 'active' ? t('Disable Login') : t('Enable Login')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuSeparator />
                                              <DropdownMenuItem onClick={() => handleAction('edit', company)} className="text-amber-600">
                                                  <Edit className="mr-2 h-4 w-4" />
                                                  <span>{t('Edit')}</span>
                                              </DropdownMenuItem>
                                              <DropdownMenuItem onClick={() => handleAction('delete', company)} className="text-rose-600">
                                                  <Trash2 className="mr-2 h-4 w-4" />
                                                  <span>{t('Delete')}</span>
                                              </DropdownMenuItem>
                                          </DropdownMenuContent>
                                      </DropdownMenu>
                                  </div>

                                  {/* Plan info */}
                                  <div className="mb-4 rounded-md border border-gray-200 p-3">
                                      <div className="flex items-center justify-center">
                                          <CreditCard className="mr-2 h-4 w-4 text-gray-500" />
                                          <span className="text-sm font-semibold text-gray-800">{company.plan_name}</span>
                                      </div>
                                      {company.plan_expire_date && (
                                          <div className="mt-1 text-center text-xs text-gray-500">
                                              {t('Expires')}:{' '}
                                              {window.appSettings?.formatDateTime(company.plan_expire_date, false) ||
                                                  new Date(company.plan_expire_date).toLocaleDateString()}
                                          </div>
                                      )}
                                  </div>

                                  {/* Stats */}
                                  <div className="mb-4 grid grid-cols-2 gap-4">
                                      <div className="rounded-md border border-gray-200 p-4 text-center">
                                          <div className="mb-1 text-xl font-bold text-gray-900">{company.business_count || 0}</div>
                                          <div className="text-xs text-gray-600">{t('Businesses')}</div>
                                      </div>

                                      <div className="rounded-md border border-gray-200 p-4 text-center">
                                          <div className="mb-1 text-xl font-bold text-gray-900">{company.appointments_count || 0}</div>
                                          <div className="text-xs text-gray-600">{t('Appointments')}</div>
                                      </div>
                                  </div>

                                  {/* Action buttons */}
                                  <div className="flex gap-2">
                                      <Button
                                          variant="outline"
                                          size="sm"
                                          onClick={() => handleAction('edit', company)}
                                          className="h-9 flex-1 border-gray-300 text-sm"
                                      >
                                          <Edit className="mr-2 h-4 w-4" />
                                          {t('Edit')}
                                      </Button>

                                      <Button
                                          variant="outline"
                                          size="sm"
                                          onClick={() => handleAction('company-info', company)}
                                          className="h-9 flex-1 border-gray-300 text-sm"
                                      >
                                          <Eye className="mr-2 h-4 w-4" />
                                          {t('View')}
                                      </Button>

                                      <Button
                                          variant="outline"
                                          size="sm"
                                          onClick={() => handleAction('delete', company)}
                                          className="h-9 flex-1 border-gray-300 text-sm text-gray-700"
                                      >
                                          <Trash2 className="mr-2 h-4 w-4" />
                                          {t('Delete')}
                                      </Button>
                                  </div>
                              </div>
                          </Card>
                      ))}

                      {(!companies?.data || companies.data.length === 0) && (
                          <div className="col-span-full p-8 text-center text-gray-500 dark:text-gray-400">{t('No companies found')}</div>
                      )}
                  </div>

                  {/* Pagination for grid view */}
                  <div className="mt-6 overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
                      <Pagination
                          from={companies?.from || 0}
                          to={companies?.to || 0}
                          total={companies?.total || 0}
                          links={companies?.links}
                          entityName={t('companies')}
                          onPageChange={(url) => router.get(url)}
                      />
                  </div>
              </div>
          )}

          {/* Form Modal */}
          <CrudFormModal
              isOpen={isFormModalOpen && formMode !== 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={(data) => {
                  // If login_enabled is false, remove password field
                  if (data.login_enabled === false) {
                      delete data.password;
                  }
                  // Set status based on login_enabled
                  data.status = data.login_enabled ? 'active' : 'inactive';

                  // Remove login_enabled field as it's not needed in the backend
                  delete data.login_enabled;
                  handleFormSubmit(data);
              }}
              formConfig={{
                  fields: [
                      { name: 'name', label: t('Company Name'), type: 'text', required: true },
                      { name: 'email', label: t('Email'), type: 'email', required: true },
                      {
                          name: 'login_enabled',
                          label: t('Enable Login'),
                          placeholder: '', // Empty placeholder to prevent duplicate label
                          type: 'switch',
                          defaultValue: true,
                      },
                      {
                          name: 'password',
                          label: t('Password'),
                          type: 'password',
                          required: (mode) => mode === 'create',
                          conditional: (mode, data) => {
                              return data?.login_enabled === true;
                          },
                      },
                  ],
                  modalSize: 'lg',
              }}
              initialData={{
                  ...currentCompany,
                  login_enabled: currentCompany?.status === 'active',
              }}
              title={formMode === 'create' ? t('Add New Company') : t('Edit Company')}
              mode={formMode}
          />

          {/* View Company Modal */}
          <CrudFormModal
              isOpen={isFormModalOpen && formMode === 'view'}
              onClose={() => setIsFormModalOpen(false)}
              onSubmit={() => {}}
              formConfig={{
                  fields: [
                      { name: 'name', label: t('Company Name'), type: 'text', readOnly: true },
                      { name: 'phone', label: t('Phone'), type: 'text', readOnly: true },
                      { name: 'email', label: t('Email'), type: 'email', readOnly: true },
                      { name: 'city', label: t('City'), type: 'text', readOnly: true },
                      { name: 'status', label: t('Status'), type: 'text', readOnly: true },
                      { name: 'plan_name', label: t('Plan'), type: 'text', readOnly: true },
                      { name: 'created_at', label: t('Created At'), type: 'text', readOnly: true },
                      { name: 'updated_at', label: t('Updated At'), type: 'text', readOnly: true },
                  ],
                  modalSize: 'lg',
              }}
              initialData={{
                  ...currentCompany,
                  created_at: currentCompany?.created_at
                      ? window.appSettings?.formatDateTime(currentCompany.created_at) || new Date(currentCompany.created_at).toLocaleString()
                      : '',
                  updated_at: currentCompany?.updated_at
                      ? window.appSettings?.formatDateTime(currentCompany.updated_at) || new Date(currentCompany.updated_at).toLocaleString()
                      : '',
              }}
              title={t('View Company Details')}
              mode="view"
          />

          {/* Delete Modal */}
          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={currentCompany?.name || ''}
              entityName="company"
          />

          {/* Reset Password Modal */}
          <CrudFormModal
              isOpen={isResetPasswordModalOpen}
              onClose={() => setIsResetPasswordModalOpen(false)}
              onSubmit={handleResetPasswordConfirm}
              formConfig={{
                  fields: [{ name: 'password', label: t('New Password'), type: 'password', required: true }],
                  modalSize: 'sm',
              }}
              initialData={{}}
              title={`Reset Password for ${currentCompany?.name || 'Company'}`}
              mode="edit"
          />

          {/* Upgrade Plan Modal */}
          <UpgradePlanModal
              isOpen={isUpgradePlanModalOpen}
              onClose={() => setIsUpgradePlanModalOpen(false)}
              onConfirm={handleUpgradePlanConfirm}
              plans={availablePlans}
              currentPlanId={currentCompany?.plan_id}
              companyName={currentCompany?.name || ''}
          />
      </PageTemplate>
  );
}
