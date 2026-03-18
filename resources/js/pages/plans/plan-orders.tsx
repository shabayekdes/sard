import { PageTemplate } from '@/components/page-template';
import { CrudTable } from '@/components/CrudTable';
import { planOrdersConfig } from '@/config/crud/plan-orders';
import { useEffect, useState } from 'react';
import { usePage, router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Filter, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { CurrencyAmount } from '@/components/currency-amount';

/** Resolve API value that may be translatable { en, ar } or plain string to a string. */
function resolveDisplayValue(value: unknown, locale: string): string {
  if (value == null) return '—';
  if (typeof value === 'string') return value;
  if (typeof value === 'object' && value !== null && ('en' in value || 'ar' in value)) {
    const o = value as Record<string, string>;
    return o[locale] || o.en || o.ar || '—';
  }
  return String(value);
}

export default function PlanOrdersPage() {
  const { t, i18n } = useTranslation();
  const locale = i18n.language?.startsWith('ar') ? 'ar' : 'en';
  const { flash, planOrders, filters: pageFilters = {}, auth } = usePage().props as any;
  const permissions = auth?.permissions || [];
  
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [filterValues, setFilterValues] = useState<Record<string, any>>({});
  const [showFilters, setShowFilters] = useState(false);
  const [isRejectDialogOpen, setIsRejectDialogOpen] = useState(false);
  const [rejectionReason, setRejectionReason] = useState('');
  const [selectedOrder, setSelectedOrder] = useState<any>(null);
  const [viewOrder, setViewOrder] = useState<any>(null);
  const [isViewModalOpen, setIsViewModalOpen] = useState(false);
  
  useEffect(() => {
    if (flash?.success) {
      toast.success(flash.success);
    }
    if (flash?.error) {
      toast.error(flash.error);
    }
  }, [flash]);

  useEffect(() => {
    const initialFilters: Record<string, any> = {};
    planOrdersConfig.filters?.forEach(filter => {
      initialFilters[filter.key] = pageFilters[filter.key] || 'all';
    });
    setFilterValues(initialFilters);
  }, []);

  const handleAction = (action: string, item: any) => {
    if (action === 'view') {
      setViewOrder(item);
      setIsViewModalOpen(true);
    } else if (action === 'approve') {
      router.post(route("plan-orders.approve", item.id), {}, {
        onSuccess: () => {
        },
        onError: () => {
          toast.error(t('Failed to approve plan order'));
        }
      });
    } else if (action === 'reject') {
      setSelectedOrder(item);
      setIsRejectDialogOpen(true);
    }
  };

  const attachmentUrl = (path: string) => {
    if (!path) return '#';
    return path.startsWith('http') ? path : `/storage/${path}`;
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
    
    Object.entries(filterValues).forEach(([key, value]) => {
      if (value && value !== 'all') {
        params[key] = value;
      }
    });
    
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }
    
    router.get(route("plan-orders.index"), params, { preserveState: true, preserveScroll: true });
  };

  const handleFilterChange = (key: string, value: any) => {
    setFilterValues(prev => ({ ...prev, [key]: value }));
    
    const params: any = { page: 1 };
    
    if (searchTerm) {
      params.search = searchTerm;
    }
    
    const newFilters = { ...filterValues, [key]: value };
    Object.entries(newFilters).forEach(([k, v]) => {
      if (v && v !== 'all') {
        params[k] = v;
      }
    });
    
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }
    
    router.get(route("plan-orders.index"), params, { preserveState: true, preserveScroll: true });
  };

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Plans'), href: route('plans.index') },
    { title: t('Plan Orders') }
  ];

  const hasActiveFilters = () => {
    return Object.entries(filterValues).some(([key, value]) => {
      return value && value !== '';
    }) || searchTerm !== '';
  };

  const handleRejectConfirm = () => {
    if (selectedOrder) {
      router.post(route("plan-orders.reject", selectedOrder.id), { notes: rejectionReason }, {
        onSuccess: () => {
          setIsRejectDialogOpen(false);
          setRejectionReason('');
          setSelectedOrder(null);
        },
        onError: () => {
          toast.error(t('Failed to reject plan order'));
        }
      });
    }
  };

  const filteredActions = planOrdersConfig.table.actions?.map(action => ({
    ...action,
    label: t(action.label)
  }));

  return (
      <PageTemplate title={t('Plan Orders')} url="/plan-orders" breadcrumbs={breadcrumbs} noPadding>
          <div className="mb-4 rounded-lg bg-white">
              <SearchAndFilterBar
                  searchTerm={searchTerm}
                  onSearchChange={setSearchTerm}
                  onSearch={handleSearch}
                  filters={
                      planOrdersConfig.filters?.map((filter) => ({
                          name: filter.key,
                          label: t(filter.label),
                          type: 'select',
                          value: filterValues[filter.key] || '',
                          onChange: (value) => handleFilterChange(filter.key, value),
                          options:
                              filter.options?.map((option) => ({
                                  value: option.value,
                                  label: t(option.label),
                              })) || [],
                      })) || []
                  }
                  showFilters={showFilters}
                  setShowFilters={setShowFilters}
                  hasActiveFilters={hasActiveFilters}
                  activeFilterCount={() => {
                      return Object.values(filterValues).filter((v) => v && v !== '').length + (searchTerm ? 1 : 0);
                  }}
                  onResetFilters={() => {
                      setSearchTerm('');
                      setFilterValues({});
                      router.get(route('plan-orders.index'), { page: 1 }, { preserveState: true, preserveScroll: true });
                  }}
                  onApplyFilters={applyFilters}
                  currentPerPage={pageFilters.per_page?.toString() || '10'}
                  onPerPageChange={(value) => {
                      const params: any = { page: 1, per_page: parseInt(value) };

                      if (searchTerm) {
                          params.search = searchTerm;
                      }

                      Object.entries(filterValues).forEach(([key, val]) => {
                          if (val && val !== '') {
                              params[key] = val;
                          }
                      });

                      router.get(route('plan-orders.index'), params, { preserveState: true, preserveScroll: true });
                  }}
              />
          </div>

          <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
              <CrudTable
                  columns={planOrdersConfig.table.columns.map((col) => ({
                      ...col,
                      label: t(col.label),
                  }))}
                  actions={filteredActions}
                  data={planOrders?.data || []}
                  from={planOrders?.from || 1}
                  onAction={handleAction}
                  permissions={permissions}
                  entityPermissions={planOrdersConfig.entity.permissions}
              />

              <Pagination
                  from={planOrders?.from || 0}
                  to={planOrders?.to || 0}
                  total={planOrders?.total || 0}
                  links={planOrders?.links}
                  entityName={t('plan orders')}
                  onPageChange={(url) => {
                      if (url) {
                          const urlObj = new URL(url, window.location.origin);
                          if (pageFilters.per_page) {
                              urlObj.searchParams.set('per_page', pageFilters.per_page.toString());
                          }
                          router.get(urlObj.toString());
                      }
                  }}
              />
          </div>

          <Dialog open={isViewModalOpen} onOpenChange={(open) => { setIsViewModalOpen(open); if (!open) setViewOrder(null); }}>
              <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                  <DialogHeader>
                      <DialogTitle>
                          {t('View Plan Order')}
                          {viewOrder?.order_number && (
                              <span className="ml-2 font-mono text-sm text-muted-foreground">
                                  {resolveDisplayValue(viewOrder.order_number, locale)}
                              </span>
                          )}
                      </DialogTitle>
                  </DialogHeader>
                  <div className="grid gap-4 sm:grid-cols-2">
                      <div>
                          <Label className="text-muted-foreground">{t('Order Number')}</Label>
                          <p className="mt-1 text-sm font-mono">{resolveDisplayValue(viewOrder?.order_number, locale)}</p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Order Date')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.ordered_at ? window.appSettings?.formatDateTime?.(viewOrder.ordered_at, false) : '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('User Name')}</Label>
                          <p className="mt-1 text-sm">{resolveDisplayValue(viewOrder?.user?.name, locale)}</p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Plan Name')}</Label>
                          <p className="mt-1 text-sm">{resolveDisplayValue(viewOrder?.plan?.name, locale)}</p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Billing Cycle')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.billing_cycle === 'yearly' ? t('Yearly') : viewOrder?.billing_cycle === 'monthly' ? t('Monthly') : viewOrder?.billing_cycle ?? '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Payment Method')}</Label>
                          <p className="mt-1 text-sm capitalize">
                              {resolveDisplayValue(viewOrder?.payment_method, locale).replace(/_/g, ' ')}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Payment ID')}</Label>
                          <p className="mt-1 truncate text-sm font-mono" title={typeof viewOrder?.payment_id === 'string' ? viewOrder.payment_id : ''}>{resolveDisplayValue(viewOrder?.payment_id, locale)}</p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Status')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.status === 'pending' ? t('Pending') : viewOrder?.status === 'approved' ? t('Approved') : viewOrder?.status === 'rejected' ? t('Rejected') : viewOrder?.status ?? '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Original Price')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.original_price != null ? <CurrencyAmount amount={viewOrder.original_price} /> : '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Coupon Code')}</Label>
                          <p className="mt-1 text-sm">{resolveDisplayValue(viewOrder?.coupon_code, locale)}</p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Discount')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.discount_amount > 0 ? <span className="inline-flex items-center gap-1"><span>-</span><CurrencyAmount amount={viewOrder.discount_amount} /></span> : '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Final Price')}</Label>
                          <p className="mt-1 text-sm font-medium">
                              {viewOrder?.final_price != null ? <CurrencyAmount amount={viewOrder.final_price} /> : '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Processed At')}</Label>
                          <p className="mt-1 text-sm">
                              {viewOrder?.processed_at ? window.appSettings?.formatDateTime?.(viewOrder.processed_at, false) : '—'}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Processed By')}</Label>
                          <p className="mt-1 text-sm">{resolveDisplayValue(viewOrder?.processed_by?.name ?? viewOrder?.processedBy?.name, locale)}</p>
                      </div>
                  </div>
                  <div className="space-y-4 border-t pt-4">
                      <div>
                          <Label className="text-muted-foreground">{t('Note')}</Label>
                          <p className="mt-1 rounded border bg-muted/30 p-3 text-sm">
                              {resolveDisplayValue(viewOrder?.notes, locale)}
                          </p>
                      </div>
                      <div>
                          <Label className="text-muted-foreground">{t('Attachment')}</Label>
                          <div className="mt-1 space-y-1">
                              {viewOrder?.attachment?.length ? (
                                  viewOrder.attachment.map((path: string, i: number) => (
                                      <a
                                          key={i}
                                          href={attachmentUrl(path)}
                                          target="_blank"
                                          rel="noopener noreferrer"
                                          className="block text-sm text-primary underline hover:no-underline"
                                      >
                                          {path.split('/').pop() || t('Attachment')} {i + 1}
                                      </a>
                                  ))
                              ) : (
                                  <p className="text-sm text-muted-foreground">—</p>
                              )}
                          </div>
                      </div>
                  </div>
                  <DialogFooter>
                      <Button variant="outline" onClick={() => setIsViewModalOpen(false)}>
                          {t('Close')}
                      </Button>
                  </DialogFooter>
              </DialogContent>
          </Dialog>

          <Dialog open={isRejectDialogOpen} onOpenChange={setIsRejectDialogOpen}>
              <DialogContent>
                  <DialogHeader>
                      <DialogTitle>{t('Reject Plan Order')}</DialogTitle>
                  </DialogHeader>
                  <div className="space-y-4">
                      <div>
                          <Label htmlFor="rejection-reason">{t('Rejection Reason (Optional)')}</Label>
                          <Textarea
                              id="rejection-reason"
                              value={rejectionReason}
                              onChange={(e) => setRejectionReason(e.target.value)}
                              placeholder={t('Enter reason for rejection...')}
                              rows={4}
                          />
                      </div>
                  </div>
                  <DialogFooter>
                      <Button variant="outline" onClick={() => setIsRejectDialogOpen(false)}>
                          {t('Cancel')}
                      </Button>
                      <Button variant="destructive" onClick={handleRejectConfirm}>
                          {t('Reject Order')}
                      </Button>
                  </DialogFooter>
              </DialogContent>
          </Dialog>
      </PageTemplate>
  );
}