import { useState, useEffect } from 'react';
import type { ReactNode } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus, CheckCircle, XCircle, Clock } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { formatCurrencyAmount } from '@/components/currency-amount';
import { capitalize } from '@/utils/helpers';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';

export default function Payments() {
  const { t } = useTranslation();
  const { auth, payments, invoices, filters: pageFilters = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];

  // State
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [selectedInvoice, setSelectedInvoice] = useState(pageFilters.invoice_id || 'all');
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState(pageFilters.payment_method || 'all');
  const [selectedApprovalStatus, setSelectedApprovalStatus] = useState(pageFilters.approval_status || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');
  const [isRejectModalOpen, setIsRejectModalOpen] = useState(false);
  const [rejectionReason, setRejectionReason] = useState('');

  // Auto-open modal from invoice page
  const [isAutoOpen, setIsAutoOpen] = useState(false);
  
  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_open') === 'true') {
      const invoiceNumber = urlParams.get('invoice_number');
      const amount = urlParams.get('amount');
      const invoiceId = urlParams.get('invoice_id');
      
      setIsAutoOpen(true);
      setCurrentItem({
        invoice_id: invoiceId,
        amount: amount,
        payment_date: new Date().toISOString().split('T')[0],
        payment_method: 'cash'
      });
      setFormMode('create');
      setIsFormModalOpen(true);
      
      // Clean URL
      window.history.replaceState({}, '', route('billing.payments.index'));
    }
  }, []);

  // Check if any filters are active
  const hasActiveFilters = () => {
    return searchTerm !== '' || selectedInvoice !== 'all' || selectedPaymentMethod !== 'all' || selectedApprovalStatus !== 'all';
  };

  // Count active filters
  const activeFilterCount = () => {
    return (searchTerm ? 1 : 0)
      + (selectedInvoice !== 'all' ? 1 : 0)
      + (selectedPaymentMethod !== 'all' ? 1 : 0)
      + (selectedApprovalStatus !== 'all' ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    router.get(route('billing.payments.index'), {
      page: 1,
      search: searchTerm || undefined,
      invoice_id: selectedInvoice !== 'all' ? selectedInvoice : undefined,
      payment_method: selectedPaymentMethod !== 'all' ? selectedPaymentMethod : undefined,
      approval_status: selectedApprovalStatus !== 'all' ? selectedApprovalStatus : undefined,
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
      case 'approve':
        router.post(route('billing.payments.approve', item.id), {}, {
          onSuccess: (page) => {
            toast.dismiss();
            if (page.props.flash?.success) {
              toast.success(page.props.flash.success);
            }
          },
          onError: (errors) => {
            toast.dismiss();
            toast.error(t('Failed to approve payment: {{errors}}', { errors: Object.values(errors).join(', ') }));
          }
        });
        break;
      case 'reject':
        setRejectionReason('');
        setIsRejectModalOpen(true);
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
      router.post(route('billing.payments.store'), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t('Failed to record payment: {{errors}}', { errors: Object.values(errors).join(', ') }));
        }
      });
    } else if (formMode === 'edit') {
      router.put(route('billing.payments.update', currentItem.id), formData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t('Failed to update payment: {{errors}}', { errors: Object.values(errors).join(', ') }));
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    router.delete(route('billing.payments.destroy', currentItem.id), {
      onSuccess: (page) => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        if (page.props.flash.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(t('Failed to delete payment: {{errors}}', { errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleResetFilters = () => {
    setSearchTerm('');
    setSelectedInvoice('all');
    setSelectedPaymentMethod('all');
    setSelectedApprovalStatus('all');
    setShowFilters(false);

    router.get(route('billing.payments.index'), {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  const handleRejectConfirm = () => {
    if (!currentItem) return;
    router.post(route('billing.payments.reject', currentItem.id), { rejection_reason: rejectionReason }, {
      onSuccess: (page) => {
        setIsRejectModalOpen(false);
        setRejectionReason('');
        toast.dismiss();
        if (page.props.flash?.success) {
          toast.success(page.props.flash.success);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(t('Failed to reject payment: {{errors}}', { errors: Object.values(errors).join(', ') }));
      }
    });
  };

  // Define page actions
  const pageActions = [];

  if (hasPermission(permissions, 'create-payments')) {
    pageActions.push({
      label: t('Record Payment'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Billing & Invoicing'), href: route('billing.time-entries.index') },
    { title: t('Payments') }
  ];

  // Define table columns
  const columns = [
    {
      key: 'invoice',
      label: t('Invoice #'),
      render: (value: any) => value?.invoice_number || '-'
    },
    {
      key: 'client',
      label: t('Client'),
      render: (value: any, row: any) => row.invoice?.client?.name || '-'
    },
    {
      key: 'amount',
      label: t('Amount'),
      type: 'currency' as const
    },
    {
      key: 'payment_method',
      label: t('Method'),
      render: (value: string) => (
        <span className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
          {value === 'bank_transfer' ? t('Bank Transfer') : t(capitalize(value))}
        </span>
      )
    },
    {
      key: 'approval_status',
      label: t('Approval'),
      render: (value: string) => {
        const status = value || 'approved';
        const statusClasses: Record<string, string> = {
          pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
          approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
          rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        };
        const statusIcons: Record<string, ReactNode> = {
          pending: <Clock className="h-3.5 w-3.5" />,
          approved: <CheckCircle className="h-3.5 w-3.5" />,
          rejected: <XCircle className="h-3.5 w-3.5" />,
        };
        return (
          <span className={`inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium ${statusClasses[status] || 'bg-gray-100 text-gray-800'}`}>
            {statusIcons[status] || null}
            {t(capitalize(status))}
          </span>
        );
      }
    },
    {
      key: 'payment_date',
      label: t('Date'),
      render: (value: string) => window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()
    },

  ];

  // Define table actions
  const actions = [
    {
      label: t('Approve'),
      icon: 'Check',
      action: 'approve',
      className: 'text-green-600',
      requiredPermission: 'approve-payments',
      condition: (row: any) => row.payment_method === 'bank_transfer' && row.approval_status === 'pending'
    },
    {
      label: t('Reject'),
      icon: 'X',
      action: 'reject',
      className: 'text-red-600',
      requiredPermission: 'reject-payments',
      condition: (row: any) => row.payment_method === 'bank_transfer' && row.approval_status === 'pending'
    },
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-payments'
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-payments'
    },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-payments'
    }
  ];

  // Prepare filter options
  const invoiceOptions = [
    { value: 'all', label: t('All Invoices') },
    ...(invoices || []).map((invoice: any) => ({
      value: invoice.id.toString(),
      label: `${invoice.invoice_number} - ${invoice.client?.name}`
    }))
  ];

  const paymentMethodOptions = [
    { value: 'all', label: t('All Methods') },
    { value: 'cash', label: t('Cash') },
    { value: 'check', label: t('Check') },
    { value: 'credit_card', label: t('Credit Card') },
    { value: 'bank_transfer', label: t('Bank Transfer') },
    { value: 'online', label: t('Online Payment') }
  ];

  const approvalStatusOptions = [
    { value: 'all', label: t('All Statuses') },
    { value: 'pending', label: t('Pending') },
    { value: 'approved', label: t('Approved') },
    { value: 'rejected', label: t('Rejected') },
  ];

  return (
      <PageTemplate title={t('Payments')} url="/billing/payments" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
          {/* Search and filters section */}
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={[
                      {
                          name: 'invoice_id',
                          label: t('Invoice'),
                          type: 'select',
                          value: selectedInvoice,
                          onChange: setSelectedInvoice,
                          options: invoiceOptions,
                      },
                      {
                          name: 'payment_method',
                          label: t('Payment Method'),
                          type: 'select',
                          value: selectedPaymentMethod,
                          onChange: setSelectedPaymentMethod,
                          options: paymentMethodOptions,
                      },
                      {
                          name: 'approval_status',
                          label: t('Approval Status'),
                          type: 'select',
                          value: selectedApprovalStatus,
                          onChange: setSelectedApprovalStatus,
                          options: approvalStatusOptions,
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
          <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
              <CrudTable
                  columns={columns}
                  actions={actions}
                  data={payments?.data || []}
                  from={payments?.from || 1}
                  onAction={handleAction}
                  permissions={permissions}
                  entityPermissions={{
                      view: 'view-payments',
                      create: 'create-payments',
                      edit: 'edit-payments',
                      delete: 'delete-payments',
                  }}
              />

              {/* Pagination section */}
              <Pagination
                  from={payments?.from || 0}
                  to={payments?.to || 0}
                  total={payments?.total || 0}
                  links={payments?.links}
                  entityName={t('payments')}
                  onPageChange={(url) => router.get(url)}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      router.get(
                          route('billing.payments.index'),
                          {
                              page: 1,
                              per_page: parseInt(value),
                              search: searchTerm || undefined,
                              invoice_id: selectedInvoice !== 'all' ? selectedInvoice : undefined,
                              payment_method: selectedPaymentMethod !== 'all' ? selectedPaymentMethod : undefined,
                              approval_status: selectedApprovalStatus !== 'all' ? selectedApprovalStatus : undefined,
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
                          name: 'approval_status',
                          label: t('Approval Status'),
                          type: 'custom',
                          conditional: (mode) => mode === 'view',
                          render: (field, formData) => {
                              const status = formData?.approval_status || 'approved';
                              const statusClasses: Record<string, string> = {
                                  pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                  approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                  rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                              };
                              return (
                                  <div className="space-y-2">
                                      <div className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium">
                                          <span className={`rounded-md px-2 py-1 text-xs font-medium ${statusClasses[status] || 'bg-gray-100 text-gray-800'}`}>
                                              {t(capitalize(status))}
                                          </span>
                                      </div>
                                      {status === 'rejected' && formData?.rejection_reason && (
                                          <div className="rounded-md border bg-gray-50 p-2 text-sm">
                                              {formData.rejection_reason}
                                          </div>
                                      )}
                                  </div>
                              );
                          },
                      },
                      {
                          name: 'invoice_id',
                          label: t('Invoice'),
                          type: 'select',
                          required: true,
                          disabled: isAutoOpen,
                          options: (invoices || []).map((invoice: any) => ({
                              value: invoice.id.toString(),
                              label: `${invoice.invoice_number} - ${invoice.client?.name}`,
                          })),
                      },
                      {
                          name: 'payment_method',
                          label: t('Payment Method'),
                          type: 'select',
                          required: true,
                          options: [
                              { value: 'cash', label: t('Cash') },
                              { value: 'check', label: t('Check') },
                              { value: 'credit_card', label: t('Credit Card') },
                              { value: 'bank_transfer', label: t('Bank Transfer') },
                              { value: 'online', label: t('Online Payment') },
                          ],
                      },
                      { name: 'amount', label: t('Amount'), type: 'number', step: '0.01', required: true, min: '0', disabled: isAutoOpen },
                      { name: 'payment_date', label: t('Payment Date'), type: 'date', required: true, disabled: isAutoOpen },
                      { name: 'notes', label: t('Notes'), type: 'textarea' },
                      {
                          name: 'attachment',
                          label: t('Attachment'),
                          type: formMode === 'view' ? 'custom' : 'media-picker',
                          multiple: true,
                          placeholder: t('Select files...'),
                          render:
                              formMode === 'view'
                                  ? (field, formData) => {
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

                                        return (
                                            <div className="space-y-2" dir="auto">
                                                {fileList.map((file, index) => {
                                                    const displayUrl = getDisplayUrl(file);
                                                    const isImg = isImage(file);
                                                    const fileName = file.split('/').pop() || file;

                                                    return (
                                                        <div
                                                            key={index}
                                                            className="flex items-center gap-2 rounded-md border bg-gray-50 p-2 rtl:flex-row-reverse"
                                                        >
                                                            {isImg ? (
                                                                <img
                                                                    src={displayUrl}
                                                                    alt={fileName}
                                                                    className="h-16 w-16 shrink-0 rounded object-cover"
                                                                    onError={(e) => {
                                                                        (e.target as HTMLImageElement).style.display = 'none';
                                                                    }}
                                                                />
                                                            ) : (
                                                                <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded bg-gray-200">
                                                                    <span className="text-xs text-gray-500">
                                                                        {getFileExtension(file).toUpperCase()}
                                                                    </span>
                                                                </div>
                                                            )}
                                                            <div className="min-w-0 flex-1 text-start">
                                                                <p className="truncate text-sm font-medium text-gray-900">{fileName}</p>
                                                                <a
                                                                    href={displayUrl}
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    className="text-xs text-blue-600 hover:text-blue-800"
                                                                >
                                                                    {t('View')}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        );
                                    }
                                  : undefined,
                      },
                  ],
                  modalSize: 'lg',
              }}
              initialData={currentItem}
              title={formMode === 'create' ? t('Record New Payment') : formMode === 'edit' ? t('Edit Payment') : t('View Payment')}
              mode={formMode}
          />

          {/* Delete Modal */}
          <CrudDeleteModal
              isOpen={isDeleteModalOpen}
              onClose={() => setIsDeleteModalOpen(false)}
              onConfirm={handleDeleteConfirm}
              itemName={`${currentItem?.invoice?.invoice_number} - ${formatCurrencyAmount(currentItem?.amount ?? 0, 'company', { showSymbol: true, showCode: false })}`}
              entityName="Payment"
          />

          <Dialog open={isRejectModalOpen} onOpenChange={setIsRejectModalOpen}>
              <DialogContent>
                  <DialogHeader>
                      <DialogTitle>{t('Reject Payment')}</DialogTitle>
                  </DialogHeader>
                  <div className="space-y-4">
                      <div>
                          <Label htmlFor="payment-rejection-reason">{t('Rejection Reason (Optional)')}</Label>
                          <Textarea
                              id="payment-rejection-reason"
                              value={rejectionReason}
                              onChange={(e) => setRejectionReason(e.target.value)}
                              placeholder={t('Enter reason for rejection...')}
                              rows={4}
                          />
                      </div>
                  </div>
                  <DialogFooter>
                      <Button variant="outline" onClick={() => setIsRejectModalOpen(false)}>
                          {t('Cancel')}
                      </Button>
                      <Button variant="destructive" onClick={handleRejectConfirm}>
                          {t('Reject Payment')}
                      </Button>
                  </DialogFooter>
              </DialogContent>
          </Dialog>
      </PageTemplate>
  );
}