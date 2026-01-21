import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Pagination } from '@/components/ui/pagination';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudTable } from '@/components/CrudTable';
import { toast } from '@/components/custom-toast';
import { ArrowLeft, FileText, Briefcase, Receipt, CreditCard } from 'lucide-react';
import { formatCurrency } from '@/utils/helpers';
import { useLayout } from '@/contexts/LayoutContext';

export default function ClientShow() {
    const { t, i18n } = useTranslation();
    const { client, documents, cases, invoices, payments, allInvoices, filters = {} } = usePage().props as any;
    const { position } = useLayout();
    const [activeTab, setActiveTab] = useState('cases');
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [invoiceSearchTerm, setInvoiceSearchTerm] = useState(filters.invoice_search || '');
    const [paymentSearchTerm, setPaymentSearchTerm] = useState(filters.payment_search || '');
    const [showFilters, setShowFilters] = useState(false);
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
    const [currentPayment, setCurrentPayment] = useState<any>(null);
    const [paymentFormMode, setPaymentFormMode] = useState<'create' | 'edit' | 'view'>('view');

    // Helper function to get translated value from translation object
    const getTranslatedValue = (value: any): string => {
        if (!value) return '-';
        if (typeof value === 'string') return value;
        if (typeof value === 'object' && value !== null) {
            const locale = i18n.language || document.documentElement.lang || 'en';
            return value[locale] || value.en || value.ar || '-';
        }
        return '-';
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Client Management'), href: route('clients.index') },
        { title: t('Clients'), href: route('clients.index') },
        { title: client.name }
    ];

    const pageActions = [
        {
            label: t('Back to Clients'),
            icon: <ArrowLeft className="h-4 w-4 mr-2" />,
            variant: 'outline' as const,
            onClick: () => window.location.href = route('clients.index')
        }
    ];

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(route('clients.show', client.id), {
            page: 1,
            search: searchTerm || undefined,
            per_page: filters.per_page || 10
        }, { preserveState: true, preserveScroll: true });
    };

    const handleResetFilters = () => {
        setSearchTerm('');
        router.get(route('clients.show', client.id), {
            page: 1,
            per_page: filters.per_page || 10
        }, { preserveState: true, preserveScroll: true });
    };

    const applyFilters = () => {
        router.get(route('clients.show', client.id), {
            page: 1,
            search: searchTerm || undefined,
            per_page: filters.per_page || 10
        }, { preserveState: true, preserveScroll: true });
    };

    const hasActiveFilters = () => {
        return searchTerm !== '';
    };

    const activeFilterCount = () => {
        return searchTerm ? 1 : 0;
    };

    const casesData = cases?.data || cases || [];
    const casesTotal = cases?.total || casesData.length;
    const invoicesData = invoices?.data || invoices || [];
    const invoicesTotal = invoices?.total || invoicesData.length;
    const paymentsData = payments?.data || payments || [];
    const paymentsTotal = payments?.total || paymentsData.length;

    const caseColumns = [
        {
            key: 'case_id',
            label: t('Case ID'),
            sortable: true
        },
        {
            key: 'title',
            label: t('Title'),
            sortable: true,
            render: (value: any, row: any) => {
                const title = row.title || '-';
                const caseNumber = row.case_number ? ` - ${row.case_number}` : '';
                return `${title}${caseNumber}`;
            }
        },
        {
            key: 'case_status',
            label: t('Status'),
            render: (value: any, row: any) => (
                <span
                    className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                    style={{
                        backgroundColor: `${row.case_status?.color}20`,
                        color: row.case_status?.color
                    }}
                >
                    {row.case_status?.name || '-'}
                </span>
            )
        },
        {
            key: 'priority',
            label: t('Priority'),
            render: (value: string) => {
                const colors = {
                    low: 'bg-green-50 text-green-700 ring-green-600/20',
                    medium: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                    high: 'bg-red-50 text-red-700 ring-red-600/20'
                };
                return (
                    <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${colors[value as keyof typeof colors] || colors.medium}`}>
                        {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
                    </span>
                );
            }
        },
        {
            key: 'filing_date',
            label: t('Filing Date'),
            sortable: true,
            render: (value: string) => value ? (window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()) : '-'
        }
    ];

    const caseActions = [
        {
            label: t('View'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary',
            href: (row: any) => route('cases.show', row.id)
        }
    ];

    const invoiceColumns = [
        {
            key: 'invoice_number',
            label: t('Invoice #'),
            sortable: true
        },
        {
            key: 'case',
            label: t('Case'),
            render: (value: any, row: any) => {
                if (!row.case) return '-';
                return (
                    <div className="flex flex-col">
                        <span className="font-medium">{row.case.case_id || '-'}</span>
                        <span className="text-xs text-gray-500 dark:text-gray-400">{row.case.title || '-'}</span>
                    </div>
                );
            }
        },
        {
            key: 'total_amount',
            label: t('Total'),
            render: (value: any) => {
                const amount = parseFloat(value);
                return isNaN(amount) ? formatCurrency(0.00) : formatCurrency(amount);
            }
        },
        {
            key: 'invoice_date',
            label: t('Invoice Date'),
            sortable: true,
            render: (value: string) => value ? (window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()) : '-'
        },
        {
            key: 'due_date',
            label: t('Due Date'),
            sortable: true,
            render: (value: string) => value ? (window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()) : '-'
        },
        {
            key: 'status',
            label: t('Status'),
            render: (value: string) => {
                const statusColors = {
                    draft: 'bg-gray-50 text-gray-700 ring-gray-600/20',
                    sent: 'bg-blue-50 text-blue-700 ring-blue-600/20',
                    paid: 'bg-green-50 text-green-700 ring-green-600/20',
                    partial: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                    partial_paid: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                    overdue: 'bg-red-50 text-red-700 ring-red-600/20',
                    cancelled: 'bg-gray-50 text-gray-700 ring-gray-600/20'
                };
                return (
                    <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusColors[value as keyof typeof statusColors] || statusColors.draft}`}>
                        {t(value === 'partial_paid' ? 'Partial Paid' : value?.charAt(0).toUpperCase() + value?.slice(1).replace('_', ' '))}
                    </span>
                );
            }
        }
    ];

    const invoiceActions = [
        {
            label: t('View Invoice'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary',
            href: (row: any) => route('billing.invoices.show', row.id)
        }
    ];

    const paymentColumns = [
        {
            key: 'invoice',
            label: t('Invoice #'),
            render: (value: any) => value?.invoice_number || '-'
        },
        {
            key: 'amount',
            label: t('Amount'),
            render: (value: any) => {
                const amount = parseFloat(value);
                return isNaN(amount) ? formatCurrency(0.00) : formatCurrency(amount.toFixed(2));
            }
        },
        {
            key: 'payment_method',
            label: t('Payment Method'),
            render: (value: string) => (
                <span className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {t(value ? value.charAt(0).toUpperCase() + value.slice(1).replace('_', ' ') : '-')}
                </span>
            )
        },
        {
            key: 'payment_date',
            label: t('Payment Date'),
            sortable: true,
            render: (value: string) => value ? (window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString()) : '-'
        }
    ];

    const handlePaymentAction = (action: string, item: any) => {
        if (action === 'view') {
            const paymentData = {
                ...item,
                invoice_id: item.invoice_id ? String(item.invoice_id) : (item.invoice?.id ? String(item.invoice.id) : ''),
                invoice: item.invoice
            };
            setCurrentPayment(paymentData);
            setPaymentFormMode('view');
            setIsPaymentModalOpen(true);
        }
    };

    const paymentActions = [
        {
            label: t('View Payment'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary'
        }
    ];


    return (
        <PageTemplate title={`${t('Client')}: ${client.name}`} url={route('clients.show', client.id)} breadcrumbs={breadcrumbs} actions={pageActions} noPadding>
            <div className="space-y-6">
                {/* Client Details Card */}
                <Card className="p-6">
                    {(client.business_type === 'b2c' || !client.business_type) ? (
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Basic Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Client ID')}:</strong> {client.client_id}
                                    </div>
                                    <div>
                                        <strong>{t('Name')}:</strong> {client.name}
                                        <Badge className="ml-2" variant="outline">{t('Individual')}</Badge>
                                    </div>
                                    <div>
                                        <strong>{t('Type')}:</strong> {getTranslatedValue(client.client_type?.name_translations || client.client_type?.name) || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Nationality')}:</strong> {getTranslatedValue(client.nationality?.nationality_name)}
                                    </div>
                                    <div>
                                        <strong>{t('ID')}:</strong> {client.id_number || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Gender')}:</strong> {client.gender ? t(client.gender.charAt(0).toUpperCase() + client.gender.slice(1)) : '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Date of Birth')}:</strong>{' '}
                                        {client.date_of_birth
                                            ? window.appSettings?.formatDate(client.date_of_birth) || new Date(client.date_of_birth).toLocaleDateString()
                                            : '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Status')}:</strong>
                                        <Badge className="ml-2" variant={client.status === 'active' ? 'default' : 'secondary'}>
                                            {client.status === 'active' ? t('Active') : t('Inactive')}
                                        </Badge>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Contact Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Email')}:</strong> {client.email || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Phone')}:</strong> {client.phone || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Address')}:</strong> {client.address || '-'}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Additional Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Tax Rate')}:</strong> {client.tax_rate ? `${client.tax_rate}%` : '0%'}
                                    </div>
                                    <div>
                                        <strong>{t('Notes')}:</strong>
                                    </div>
                                    <div className="rounded bg-gray-50 p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">{client.notes || t('No notes available')}</div>
                                    <div>
                                        <strong>{t('Referral Source')}:</strong> {client.referral_source || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Created')}:</strong>{' '}
                                        {window.appSettings?.formatDate(client.created_at) || new Date(client.created_at).toLocaleDateString()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Basic Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Client ID')}:</strong> {client.client_id}
                                    </div>
                                    <div>
                                        <strong>{t('Name')}:</strong> {client.name}
                                        <Badge className="ml-2" variant="outline">{t('Business')}</Badge>
                                    </div>
                                    <div>
                                        <strong>{t('Type')}:</strong> {getTranslatedValue(client.client_type?.name_translations || client.client_type?.name) || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Unified Number')}:</strong> {client.unified_number || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('CR Number')}:</strong> {client.cr_number || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('CR Issuance Date')}:</strong>{' '}
                                        {client.cr_issuance_date
                                            ? window.appSettings?.formatDate(client.cr_issuance_date) || new Date(client.cr_issuance_date).toLocaleDateString()
                                            : '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Tax ID')}:</strong> {client.tax_id || '-'}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Contact Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Email')}:</strong> {client.email || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Phone')}:</strong> {client.phone || '-'}
                                    </div>
                                    <div>
                                        <strong>{t('Address')}:</strong> {client.address || '-'}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 className="mb-4 text-lg font-semibold">{t('Additional Info')}</h3>
                                <div className="space-y-2">
                                    <div>
                                        <strong>{t('Tax Rate')}:</strong> {client.tax_rate ? `${client.tax_rate}%` : '0%'}
                                    </div>
                                    <div>
                                        <strong>{t('Notes')}:</strong>
                                    </div>
                                    <div className="rounded bg-gray-50 p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">{client.notes || t('No notes available')}</div>
                                    {/*<div>*/}
                                    {/*    <strong>{t('Referral Source')}:</strong> {client.referral_source || '-'}*/}
                                    {/*</div>*/}
                                    <div>
                                        <strong>{t('Created')}:</strong>{' '}
                                        {window.appSettings?.formatDate(client.created_at) || new Date(client.created_at).toLocaleDateString()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </Card>

                {/* Tabs */}
                <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-900">
                    <div className="border-b border-gray-200 dark:border-gray-700">
                        <nav className="flex overflow-x-auto">
                            <button
                                onClick={() => setActiveTab('cases')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'cases'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <Briefcase className="h-4 w-4" />
                                    <span>{t('Cases')} ({casesTotal})</span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('invoices')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'invoices'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <Receipt className="h-4 w-4" />
                                    <span>{t('Invoices')} ({invoicesTotal})</span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('payments')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'payments'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <CreditCard className="h-4 w-4" />
                                    <span>{t('Payments')} ({paymentsTotal})</span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('documents')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'documents'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <FileText className="h-4 w-4" />
                                    <span>{t('Documents')} ({documents?.length || 0})</span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('billing')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'billing'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <FileText className="h-4 w-4" />
                                    <span>{t('Billing Info')} ({client.billing_info ? 1 : 0})</span>
                                </div>
                            </button>
                        </nav>
                    </div>

                    <div className="p-6">
                        {activeTab === 'cases' && (
                            <div>
                                <div className="mb-6">
                                    <SearchAndFilterBar
                                        searchTerm={searchTerm}
                                        onSearchChange={setSearchTerm}
                                        onSearch={handleSearch}
                                        filters={[]}
                                        showFilters={showFilters}
                                        setShowFilters={setShowFilters}
                                        hasActiveFilters={hasActiveFilters}
                                        activeFilterCount={activeFilterCount}
                                        onResetFilters={handleResetFilters}
                                        onApplyFilters={applyFilters}
                                        currentPerPage={filters.per_page?.toString() || '10'}
                                        onPerPageChange={(value) => {
                                            router.get(route('clients.show', client.id), {
                                                page: 1,
                                                per_page: parseInt(value),
                                                search: searchTerm || undefined
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                    />
                                </div>
                                {casesData && casesData.length > 0 ? (
                                    <>
                                        <div className="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                                            <CrudTable
                                                columns={caseColumns}
                                                actions={caseActions}
                                                data={casesData}
                                                from={cases?.from || 1}
                                                onAction={() => { }}
                                                permissions={[]}
                                            />
                                        </div>
                                        {cases?.links && (
                                            <div className="mt-4">
                                                <Pagination
                                                    from={cases?.from || 0}
                                                    to={cases?.to || 0}
                                                    total={cases?.total || 0}
                                                    links={cases?.links}
                                                    entityName={t('cases')}
                                                    onPageChange={(url) => router.get(url)}
                                                />
                                            </div>
                                        )}
                                    </>
                                ) : (
                                    <div className="py-8 text-center text-gray-500">{t('No cases found for this client')}</div>
                                )}
                            </div>
                        )}

                        {activeTab === 'invoices' && (
                            <div>
                                <div className="mb-6">
                                    <SearchAndFilterBar
                                        searchTerm={invoiceSearchTerm}
                                        onSearchChange={setInvoiceSearchTerm}
                                        onSearch={(e) => {
                                            e.preventDefault();
                                            router.get(route('clients.show', client.id), {
                                                invoice_page: 1,
                                                invoice_search: invoiceSearchTerm || undefined,
                                                invoice_per_page: filters.invoice_per_page || 10
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                        filters={[]}
                                        showFilters={false}
                                        setShowFilters={() => { }}
                                        hasActiveFilters={() => invoiceSearchTerm !== ''}
                                        activeFilterCount={() => invoiceSearchTerm ? 1 : 0}
                                        onResetFilters={() => {
                                            setInvoiceSearchTerm('');
                                            router.get(route('clients.show', client.id), {
                                                invoice_page: 1,
                                                invoice_per_page: filters.invoice_per_page || 10
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                        onApplyFilters={() => { }}
                                        currentPerPage={filters.invoice_per_page?.toString() || '10'}
                                        onPerPageChange={(value) => {
                                            router.get(route('clients.show', client.id), {
                                                invoice_page: 1,
                                                invoice_per_page: parseInt(value),
                                                invoice_search: invoiceSearchTerm || undefined
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                    />
                                </div>
                                {invoicesData && invoicesData.length > 0 ? (
                                    <>
                                        <div className="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                                            <CrudTable
                                                columns={invoiceColumns}
                                                actions={invoiceActions}
                                                data={invoicesData}
                                                from={invoices?.from || 1}
                                                onAction={() => { }}
                                                permissions={[]}
                                            />
                                        </div>
                                        {invoices?.links && (
                                            <div className="mt-4">
                                                <Pagination
                                                    from={invoices?.from || 0}
                                                    to={invoices?.to || 0}
                                                    total={invoices?.total || 0}
                                                    links={invoices?.links}
                                                    entityName={t('invoices')}
                                                    onPageChange={(url) => router.get(url)}
                                                />
                                            </div>
                                        )}
                                    </>
                                ) : (
                                    <div className="py-8 text-center text-gray-500">{t('No invoices found for this client')}</div>
                                )}
                            </div>
                        )}

                        {activeTab === 'payments' && (
                            <div>
                                <div className="mb-6">
                                    <SearchAndFilterBar
                                        searchTerm={paymentSearchTerm}
                                        onSearchChange={setPaymentSearchTerm}
                                        onSearch={(e) => {
                                            e.preventDefault();
                                            router.get(route('clients.show', client.id), {
                                                payment_page: 1,
                                                payment_search: paymentSearchTerm || undefined,
                                                payment_per_page: filters.payment_per_page || 10
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                        filters={[]}
                                        showFilters={false}
                                        setShowFilters={() => { }}
                                        hasActiveFilters={() => paymentSearchTerm !== ''}
                                        activeFilterCount={() => paymentSearchTerm ? 1 : 0}
                                        onResetFilters={() => {
                                            setPaymentSearchTerm('');
                                            router.get(route('clients.show', client.id), {
                                                payment_page: 1,
                                                payment_per_page: filters.payment_per_page || 10
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                        onApplyFilters={() => { }}
                                        currentPerPage={filters.payment_per_page?.toString() || '10'}
                                        onPerPageChange={(value) => {
                                            router.get(route('clients.show', client.id), {
                                                payment_page: 1,
                                                payment_per_page: parseInt(value),
                                                payment_search: paymentSearchTerm || undefined
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                    />
                                </div>
                                {paymentsData && paymentsData.length > 0 ? (
                                    <>
                                        <div className="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                                            <CrudTable
                                                columns={paymentColumns}
                                                actions={paymentActions}
                                                data={paymentsData}
                                                from={payments?.from || 1}
                                                onAction={handlePaymentAction}
                                                permissions={[]}
                                            />
                                        </div>
                                        {payments?.links && (
                                            <div className="mt-4">
                                                <Pagination
                                                    from={payments?.from || 0}
                                                    to={payments?.to || 0}
                                                    total={payments?.total || 0}
                                                    links={payments?.links}
                                                    entityName={t('payments')}
                                                    onPageChange={(url) => router.get(url)}
                                                />
                                            </div>
                                        )}
                                    </>
                                ) : (
                                    <div className="py-8 text-center text-gray-500">{t('No payments found for this client')}</div>
                                )}
                            </div>
                        )}

                        {activeTab === 'documents' && (
                            <div>
                                <div className="mb-6 flex items-center justify-between">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{t('Client Documents')}</h3>
                                </div>
                                {documents && documents.length > 0 ? (
                                    <div className="grid gap-4">
                                        {documents.map((doc: any, index: number) => (
                                            <Card
                                                key={index}
                                                className="cursor-pointer p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                                                onClick={() => window.open(doc.file_path, '_blank')}
                                            >
                                                <div className="flex gap-4">
                                                    <div className="flex-shrink-0">
                                                        {doc.file_path ? (
                                                            <img
                                                                src={doc.file_path}
                                                                alt={doc.document_name}
                                                                className="h-16 w-16 rounded border object-cover"
                                                                onError={(e) => {
                                                                    e.currentTarget.style.display = 'none';
                                                                    const nextSibling = e.currentTarget.nextElementSibling as HTMLElement;
                                                                    if (nextSibling) {
                                                                        nextSibling.style.display = 'flex';
                                                                    }
                                                                }}
                                                            />
                                                        ) : null}
                                                        <div
                                                            className="flex h-16 w-16 items-center justify-center rounded border bg-gray-100 dark:bg-gray-800"
                                                            style={{ display: doc.file_path ? 'none' : 'flex' }}
                                                        >
                                                            <FileText className="h-6 w-6 text-gray-500" />
                                                        </div>
                                                    </div>
                                                    <div className="flex-1">
                                                        <div className="mb-2 flex items-start justify-between">
                                                            <h4 className="font-medium">{doc.document_name}</h4>
                                                            <Badge variant="outline">{doc.document_type}</Badge>
                                                        </div>
                                                        <div className="grid grid-cols-2 gap-4 text-sm">
                                                            <div>
                                                                <strong>{t('Uploaded')}:</strong>{' '}
                                                                {window.appSettings?.formatDate(doc.created_at) ||
                                                                    new Date(doc.created_at).toLocaleDateString()}
                                                            </div>
                                                            <div className="col-span-2">
                                                                <strong>{t('Description')}:</strong> {doc.description || '-'}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </Card>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="py-8 text-center text-gray-500">{t('No documents found for this client')}</div>
                                )}
                            </div>
                        )}

                        {activeTab === 'billing' && (
                            <div>
                                <div className="mb-6 flex items-center justify-between">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{t('Billing Information')}</h3>
                                </div>
                                {client.billing_info ? (
                                    <Card className="p-4">
                                        <div className="mb-4 flex items-start justify-between">
                                            <h4 className="font-medium">{t('Client Billing Details')}</h4>
                                            {client.billing_info.status && (
                                                <Badge variant={client.billing_info.status === 'active' ? 'default' : 'secondary'}>
                                                    {client.billing_info.status}
                                                </Badge>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                                            {client.billing_info.billing_address && (
                                                <div>
                                                    <strong>{t('Billing Address')}:</strong> {client.billing_info.billing_address}
                                                </div>
                                            )}
                                            {client.billing_info.billing_contact_name && (
                                                <div>
                                                    <strong>{t('Contact Name')}:</strong> {client.billing_info.billing_contact_name}
                                                </div>
                                            )}
                                            {client.billing_info.billing_contact_email && (
                                                <div>
                                                    <strong>{t('Contact Email')}:</strong> {client.billing_info.billing_contact_email}
                                                </div>
                                            )}
                                            {client.billing_info.billing_contact_phone && (
                                                <div>
                                                    <strong>{t('Contact Phone')}:</strong> {client.billing_info.billing_contact_phone}
                                                </div>
                                            )}
                                            {client.billing_info.payment_terms && (
                                                <div>
                                                    <strong>{t('Payment Terms')}:</strong>{' '}
                                                    {client.billing_info.formatted_payment_terms || client.billing_info.payment_terms}
                                                </div>
                                            )}
                                            {client.billing_info.custom_payment_terms && (
                                                <div>
                                                    <strong>{t('Custom Payment Terms')}:</strong> {client.billing_info.custom_payment_terms}
                                                </div>
                                            )}

                                            {(client.billing_info.currency_name || client.billing_info.currency) && (
                                                <div>
                                                    <strong>{t('Currency')}:</strong>{' '}
                                                    {client.billing_info.currency_name || client.billing_info.currency}{' '}
                                                    {client.billing_info.currency_code && `(${client.billing_info.currency_code})`}
                                                </div>
                                            )}
                                            {client.billing_info.billing_notes && (
                                                <div className="col-span-2">
                                                    <strong>{t('Billing Notes')}:</strong> {client.billing_info.billing_notes}
                                                </div>
                                            )}
                                        </div>
                                    </Card>
                                ) : (
                                    <div className="py-8 text-center text-gray-500">{t('No billing information found for this client')}</div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Payment Modal */}
            <CrudFormModal
                isOpen={isPaymentModalOpen}
                onClose={() => {
                    setIsPaymentModalOpen(false);
                    setCurrentPayment(null);
                }}
                onSubmit={() => {
                    // View mode - no submit action needed
                    setIsPaymentModalOpen(false);
                }}
                formConfig={{
                    fields: [
                        {
                            name: 'invoice_id',
                            label: t('Invoice'),
                            type: 'select',
                            required: true,
                            disabled: true,
                            options: (() => {
                                // Get all invoices and ensure the payment's invoice is included
                                const invoiceOptions = (allInvoices || []).map((invoice: any) => ({
                                    value: invoice.id.toString(),
                                    label: `${invoice.invoice_number} - ${invoice.client?.name}`
                                }));

                                // If currentPayment has an invoice that's not in allInvoices, add it
                                if (currentPayment?.invoice && !invoiceOptions.find((opt: any) => opt.value === String(currentPayment.invoice.id))) {
                                    invoiceOptions.push({
                                        value: String(currentPayment.invoice.id),
                                        label: `${currentPayment.invoice.invoice_number} - ${currentPayment.invoice.client?.name || ''}`
                                    });
                                }

                                return invoiceOptions;
                            })()
                        },
                        {
                            name: 'payment_method',
                            label: t('Payment Method'),
                            type: 'select',
                            required: true,
                            disabled: true,
                            options: [
                                { value: 'cash', label: t('Cash') },
                                { value: 'check', label: t('Check') },
                                { value: 'credit_card', label: t('Credit Card') },
                                { value: 'bank_transfer', label: t('Bank Transfer') },
                                { value: 'online', label: t('Online Payment') }
                            ]
                        },
                        { name: 'amount', label: t('Amount'), type: 'number', step: '0.01', required: true, min: '0', disabled: true },
                        { name: 'payment_date', label: t('Payment Date'), type: 'date', required: true, disabled: true },
                        { name: 'notes', label: t('Notes'), type: 'textarea', disabled: true },
                        {
                            name: 'attachment',
                            label: t('Attachment'),
                            type: 'custom',
                            render: (field: any, formData: any) => {
                                const files = formData[field.name];
                                if (!files) {
                                    return <div className="rounded-md border bg-gray-50 p-2">-</div>;
                                }

                                // Handle both comma-separated string and array
                                const fileList = typeof files === 'string'
                                    ? files.split(',').filter(Boolean).map(f => f.trim())
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
                                    <div className="space-y-2">
                                        {fileList.map((file: string, index: number) => {
                                            const displayUrl = getDisplayUrl(file);
                                            const isImg = isImage(file);
                                            const fileName = file.split('/').pop() || file;

                                            return (
                                                <div key={index} className="flex items-center gap-2 p-2 border rounded-md bg-gray-50">
                                                    {isImg ? (
                                                        <img
                                                            src={displayUrl}
                                                            alt={fileName}
                                                            className="w-16 h-16 object-cover rounded"
                                                            onError={(e) => {
                                                                (e.target as HTMLImageElement).style.display = 'none';
                                                            }}
                                                        />
                                                    ) : (
                                                        <div className="w-16 h-16 flex items-center justify-center bg-gray-200 rounded">
                                                            <span className="text-xs text-gray-500">{getFileExtension(file).toUpperCase()}</span>
                                                        </div>
                                                    )}
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-sm font-medium text-gray-900 truncate">{fileName}</p>
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
                        }
                    ],
                    modalSize: 'lg'
                }}
                initialData={currentPayment}
                title={t('View Payment')}
                mode={paymentFormMode}
            />
        </PageTemplate>
    );
}
