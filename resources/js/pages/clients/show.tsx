import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudTable } from '@/components/CrudTable';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { PageTemplate } from '@/components/page-template';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Switch } from '@/components/ui/switch';
import { useLayout } from '@/contexts/LayoutContext';
import { hasPermission } from '@/utils/authorization';
import { router, usePage } from '@inertiajs/react';
import { ArrowLeft, Briefcase, CreditCard, Edit, Eye, FileText, Plus, Receipt, Save, Trash2 } from 'lucide-react';
import { useState, useEffect, type ReactNode } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useTranslation } from 'react-i18next';
import { toast } from '@/components/custom-toast';

export default function ClientShow() {
    const { t, i18n } = useTranslation();
    const {
        client,
        currencies = [],
        documents,
        documentTypes,
        cases,
        caseTypes,
        caseStatuses,
        courts,
        invoices,
        payments,
        allInvoices,
        auth,
        filters = {},
    } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const { isRtl } = useLayout();
    const [activeTab, setActiveTab] = useState('cases');
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedCaseType, setSelectedCaseType] = useState(filters.case_type_id || 'all');
    const [selectedCaseStatus, setSelectedCaseStatus] = useState(filters.case_status_id || 'all');
    const [selectedPriority, setSelectedPriority] = useState(filters.priority || 'all');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || 'all');
    const [selectedCourt, setSelectedCourt] = useState(filters.court_id || 'all');
    const [invoiceSearchTerm, setInvoiceSearchTerm] = useState(filters.invoice_search || '');
    const [paymentSearchTerm, setPaymentSearchTerm] = useState(filters.payment_search || '');
    const [showFilters, setShowFilters] = useState(false);
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
    const [currentPayment, setCurrentPayment] = useState<any>(null);
    const [paymentFormMode, setPaymentFormMode] = useState<'create' | 'edit' | 'view'>('view');
    // Documents tab
    const [documentSearchTerm, setDocumentSearchTerm] = useState(filters.document_search || '');
    const [selectedDocumentType, setSelectedDocumentType] = useState(filters.document_type_id || 'all');
    const [selectedDocumentStatus, setSelectedDocumentStatus] = useState(filters.document_status || 'all');
    const [showDocumentFilters, setShowDocumentFilters] = useState(false);
    const [isDocumentFormOpen, setIsDocumentFormOpen] = useState(false);
    const [isDocumentViewOpen, setIsDocumentViewOpen] = useState(false);
    const [isDocumentDeleteOpen, setIsDocumentDeleteOpen] = useState(false);
    const [currentDocument, setCurrentDocument] = useState<any>(null);
    const [documentFormMode, setDocumentFormMode] = useState<'create' | 'edit' | 'view'>('create');
    // Billing tab (company-profile style: same form, read-only until Edit clicked, then Save)
    const [isBillingEditing, setIsBillingEditing] = useState(false);
    const [isBillingDeleteOpen, setIsBillingDeleteOpen] = useState(false);
    const [billingFormData, setBillingFormData] = useState({
        billing_contact_name: '',
        billing_contact_email: '',
        billing_contact_phone: '',
        billing_address: '',
        payment_terms: 'net_30',
        custom_payment_terms: '',
        currency: '',
        status: 'active',
        billing_notes: '',
    });

    const emptyBillingFormData = {
        billing_contact_name: '',
        billing_contact_email: '',
        billing_contact_phone: '',
        billing_address: '',
        payment_terms: 'net_30',
        custom_payment_terms: '',
        currency: '',
        status: 'active',
        billing_notes: '',
    };

    useEffect(() => {
        if (client.billing_info) {
            setBillingFormData({
                billing_contact_name: client.billing_info.billing_contact_name ?? '',
                billing_contact_email: client.billing_info.billing_contact_email ?? '',
                billing_contact_phone: client.billing_info.billing_contact_phone ?? '',
                billing_address: client.billing_info.billing_address ?? '',
                payment_terms: client.billing_info.payment_terms ?? 'net_30',
                custom_payment_terms: client.billing_info.custom_payment_terms ?? '',
                currency: client.billing_info.currency ?? '',
                status: client.billing_info.status ?? 'active',
                billing_notes: client.billing_info.billing_notes ?? '',
            });
        } else {
            setBillingFormData(emptyBillingFormData);
            setIsBillingEditing(false);
        }
    }, [client.billing_info]);

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
        { title: getTranslatedValue(client.name) },
    ];

    const pageActions = [
        {
            label: t('Back to Clients'),
            icon: <ArrowLeft className="mr-2 h-4 w-4" />,
            variant: 'outline' as const,
            onClick: () => (window.location.href = route('clients.index')),
        },
    ];

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(
            route('clients.show', client.id),
            {
                page: 1,
                search: searchTerm || undefined,
                case_type_id: selectedCaseType !== 'all' ? selectedCaseType : undefined,
                case_status_id: selectedCaseStatus !== 'all' ? selectedCaseStatus : undefined,
                priority: selectedPriority !== 'all' ? selectedPriority : undefined,
                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
                per_page: filters.per_page || 10,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const handleResetFilters = () => {
        setSearchTerm('');
        setSelectedCaseType('all');
        setSelectedCaseStatus('all');
        setSelectedPriority('all');
        setSelectedStatus('all');
        setSelectedCourt('all');
        router.get(
            route('clients.show', client.id),
            {
                page: 1,
                per_page: filters.per_page || 10,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const applyFilters = () => {
        router.get(
            route('clients.show', client.id),
            {
                page: 1,
                search: searchTerm || undefined,
                case_type_id: selectedCaseType !== 'all' ? selectedCaseType : undefined,
                case_status_id: selectedCaseStatus !== 'all' ? selectedCaseStatus : undefined,
                priority: selectedPriority !== 'all' ? selectedPriority : undefined,
                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                court_id: selectedCourt !== 'all' ? selectedCourt : undefined,
                per_page: filters.per_page || 10,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const hasActiveFilters = () => {
        return searchTerm !== '' ||
            selectedCaseType !== 'all' ||
            selectedCaseStatus !== 'all' ||
            selectedPriority !== 'all' ||
            selectedStatus !== 'all' ||
            selectedCourt !== 'all';
    };

    const activeFilterCount = () => {
        return (searchTerm ? 1 : 0)
            + (selectedCaseType !== 'all' ? 1 : 0)
            + (selectedCaseStatus !== 'all' ? 1 : 0)
            + (selectedPriority !== 'all' ? 1 : 0)
            + (selectedStatus !== 'all' ? 1 : 0)
            + (selectedCourt !== 'all' ? 1 : 0);
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
            sortable: true,
        },
        {
            key: 'title',
            label: t('Title'),
            sortable: true,
            render: (value: any, row: any) => {
                const title = row.title || '-';
                const caseNumber = row.case_number ? ` - ${row.case_number}` : '';
                return `${title}${caseNumber}`;
            },
        },
        {
            key: 'case_status',
            label: t('Status'),
            render: (value: any, row: any) => (
                <span
                    className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                    style={{
                        backgroundColor: `${row.case_status?.color}20`,
                        color: row.case_status?.color,
                    }}
                >
                    {getTranslatedValue(row.case_status?.name) || '-'}
                </span>
            ),
        },
        {
            key: 'priority',
            label: t('Priority'),
            render: (value: string) => {
                const colors = {
                    low: 'bg-green-50 text-green-700 ring-green-600/20',
                    medium: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                    high: 'bg-red-50 text-red-700 ring-red-600/20',
                };
                return (
                    <span
                        className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${colors[value as keyof typeof colors] || colors.medium}`}
                    >
                        {value ? t(value.charAt(0).toUpperCase() + value.slice(1)) : '-'}
                    </span>
                );
            },
        },
        {
            key: 'filing_date',
            label: t('Filing Date'),
            sortable: true,
            render: (value: string) => (value ? window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString() : '-'),
        },
    ];

    const caseActions = [
        {
            label: t('View'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary',
            href: (row: any) => route('cases.show', row.id),
        },
    ];

    const invoiceColumns = [
        {
            key: 'invoice_number',
            label: t('Invoice #'),
            sortable: true,
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
            },
        },
        {
            key: 'total_amount',
            label: t('Total'),
            type: 'currency' as const
        },
        {
            key: 'invoice_date',
            label: t('Invoice Date'),
            sortable: true,
            render: (value: string) => (value ? window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString() : '-'),
        },
        {
            key: 'due_date',
            label: t('Due Date'),
            sortable: true,
            render: (value: string) => (value ? window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString() : '-'),
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
                    cancelled: 'bg-gray-50 text-gray-700 ring-gray-600/20',
                };
                return (
                    <span
                        className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusColors[value as keyof typeof statusColors] || statusColors.draft}`}
                    >
                        {t(value === 'partial_paid' ? 'Partial Paid' : value?.charAt(0).toUpperCase() + value?.slice(1).replace('_', ' '))}
                    </span>
                );
            },
        },
    ];

    const invoiceActions = [
        {
            label: t('View Invoice'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary',
            href: (row: any) => route('billing.invoices.show', row.id),
        },
    ];

    const paymentColumns = [
        {
            key: 'invoice',
            label: t('Invoice #'),
            render: (value: any) => value?.invoice_number || '-',
        },
        {
            key: 'amount',
            label: t('Amount'),
            type: 'currency' as const,
        },
        {
            key: 'payment_method',
            label: t('Payment Method'),
            render: (value: string) => (
                <span className="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {t(value ? value.charAt(0).toUpperCase() + value.slice(1).replace('_', ' ') : '-')}
                </span>
            ),
        },
        {
            key: 'payment_date',
            label: t('Payment Date'),
            sortable: true,
            render: (value: string) => (value ? window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString() : '-'),
        },
    ];

    const handlePaymentAction = (action: string, item: any) => {
        if (action === 'view') {
            const paymentData = {
                ...item,
                invoice_id: item.invoice_id ? String(item.invoice_id) : item.invoice?.id ? String(item.invoice.id) : '',
                invoice: item.invoice,
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
            className: 'text-primary',
        },
    ];

    const documentsPaginated = documents?.data !== undefined;
    const documentsData = documentsPaginated ? documents.data : (documents || []);
    const documentsTotal = documents?.total ?? documentsData.length;
    const currentLocale = i18n.language || document.documentElement.lang || 'en';
    const getDocTypeName = (docType: any) => {
        if (!docType) return '-';
        if (typeof docType.name === 'string') return docType.name;
        return docType.name_translations?.[currentLocale] || docType.name?.en || docType.name?.ar || '-';
    };

    const handleDocumentSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(route('clients.show', client.id), {
            ...filters,
            document_page: 1,
            document_search: documentSearchTerm || undefined,
            document_type_id: selectedDocumentType !== 'all' ? selectedDocumentType : undefined,
            document_status: selectedDocumentStatus !== 'all' ? selectedDocumentStatus : undefined,
            document_per_page: filters.document_per_page || 10,
        }, { preserveState: true, preserveScroll: true });
    };
    const handleDocumentResetFilters = () => {
        setDocumentSearchTerm('');
        setSelectedDocumentType('all');
        setSelectedDocumentStatus('all');
        router.get(route('clients.show', client.id), {
            ...filters,
            document_page: 1,
            document_per_page: filters.document_per_page || 10,
        }, { preserveState: true, preserveScroll: true });
    };
    const applyDocumentFilters = () => {
        router.get(route('clients.show', client.id), {
            ...filters,
            document_page: 1,
            document_search: documentSearchTerm || undefined,
            document_type_id: selectedDocumentType !== 'all' ? selectedDocumentType : undefined,
            document_status: selectedDocumentStatus !== 'all' ? selectedDocumentStatus : undefined,
            document_per_page: filters.document_per_page || 10,
        }, { preserveState: true, preserveScroll: true });
    };
    const hasDocumentFilters = () =>
        documentSearchTerm !== '' || selectedDocumentType !== 'all' || selectedDocumentStatus !== 'all';
    const documentFilterCount = () =>
        (documentSearchTerm ? 1 : 0) + (selectedDocumentType !== 'all' ? 1 : 0) + (selectedDocumentStatus !== 'all' ? 1 : 0);

    const handleDocumentAction = (action: string, row: any) => {
        setCurrentDocument(row);
        switch (action) {
            case 'view':
                if (row.file_path) window.open(row.file_path.startsWith('http') ? row.file_path : `${window.appSettings?.imageUrl || window.location.origin}${row.file_path}`, '_blank');
                else setIsDocumentViewOpen(true);
                break;
            case 'edit':
                setDocumentFormMode('edit');
                setIsDocumentFormOpen(true);
                break;
            case 'delete':
                setIsDocumentDeleteOpen(true);
                break;
            case 'download':
                const link = document.createElement('a');
                link.href = route('clients.documents.download', row.id);
                link.download = row.document_name || 'document';
                link.click();
                break;
        }
    };
    const handleDocumentStatusToggle = (row: any, checked: boolean) => {
        const newStatus = checked ? 'active' : 'archived';
        router.put(route('clients.documents.update', row.id), { status: newStatus, document_name: row.document_name, document_type_id: row.document_type_id, description: row.description }, {
            preserveScroll: true,
            onSuccess: () => toast.success(t('{{model}} status updated successfully', { model: t('Document') })),
            onError: () => toast.error(t('Failed to update status')),
        });
    };
    const handleDocumentFormSubmit = (formData: any) => {
        const payload = { ...formData, client_id: client.id };
        if (documentFormMode === 'create') {
            router.post(route('clients.documents.store'), payload, {
                onSuccess: (page) => {
                    setIsDocumentFormOpen(false);
                    toast.dismiss();
                    if ((page?.props?.flash as any)?.success) toast.success((page.props.flash as any).success);
                },
                onError: (errors) => {
                    toast.dismiss();
                    toast.error(typeof errors === 'string' ? errors : Object.values(errors).join(', '));
                },
            });
        } else {
            const updatePayload = { ...payload, file: payload.file || currentDocument?.file_path, _method: 'PUT' };
            router.post(route('clients.documents.update', currentDocument.id), updatePayload, {
                onSuccess: (page) => {
                    setIsDocumentFormOpen(false);
                    toast.dismiss();
                    if ((page?.props?.flash as any)?.success) toast.success((page.props.flash as any).success);
                },
                onError: (errors) => {
                    toast.dismiss();
                    toast.error(typeof errors === 'string' ? errors : Object.values(errors).join(', '));
                },
            });
        }
    };
    const handleDocumentDeleteConfirm = () => {
        if (!currentDocument) return;
        router.delete(route('clients.documents.destroy', currentDocument.id), {
            onSuccess: (page) => {
                setIsDocumentDeleteOpen(false);
                setCurrentDocument(null);
                toast.dismiss();
                if ((page?.props?.flash as any)?.success) toast.success((page.props.flash as any).success);
            },
            onError: () => {
                toast.dismiss();
                toast.error(t('Failed to delete document'));
            },
        });
    };

    const handleBillingChange = (name: string, value: string) => {
        setBillingFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleBillingSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const payload = { ...billingFormData, client_id: client.id };
        if (client.billing_info?.id) {
            toast.loading(t('Updating billing information...'));
            router.put(route('clients.billing.update', client.billing_info.id), payload, {
                onSuccess: (page) => {
                    toast.dismiss();
                    setIsBillingEditing(false);
                    const flash = (page?.props?.flash as any);
                    if (flash?.success) toast.success(flash.success);
                    if (flash?.error) toast.error(flash.error);
                },
                onError: (err) => {
                    toast.dismiss();
                    toast.error(typeof err === 'string' ? err : Object.values(err).join(', '));
                },
            });
        } else {
            toast.loading(t('Creating billing information...'));
            router.post(route('clients.billing.store'), payload, {
                onSuccess: (page) => {
                    toast.dismiss();
                    setIsBillingEditing(false);
                    const flash = (page?.props?.flash as any);
                    if (flash?.success) toast.success(flash.success);
                    if (flash?.error) toast.error(flash.error);
                },
                onError: (err) => {
                    toast.dismiss();
                    toast.error(typeof err === 'string' ? err : Object.values(err).join(', '));
                },
            });
        }
    };

    const documentColumns = [
        {
            key: 'document_name',
            label: t('Document Name'),
            sortable: false,
        },
        {
            key: 'document_type',
            label: t('Type'),
            render: (_: any, row: any) => {
                const docType = row.document_type;
                const name = getDocTypeName(docType);
                const color = docType?.color;
                return (
                    <span
                        className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                        style={color ? { backgroundColor: `${color}20`, color } : undefined}
                    >
                        {name}
                    </span>
                );
            },
        },
        {
            key: 'status',
            label: t('Status'),
            render: (value: string, row: any) => {
                const canToggleStatus = hasPermission(permissions, 'edit-client-documents');
                return (
                    <div className="flex items-center gap-2">
                        <Switch
                            checked={value === 'active'}
                            disabled={!canToggleStatus}
                            onCheckedChange={(checked) => {
                                if (!canToggleStatus) return;
                                handleDocumentStatusToggle(row, checked);
                            }}
                            aria-label={value === 'active' ? t('Deactivate document') : t('Activate document')}
                        />
                        <span className="text-muted-foreground text-xs">{value === 'active' ? t('Active') : t('Inactive')}</span>
                    </div>
                );
            },
        },
        {
            key: 'created_at',
            label: t('Upload Date'),
            render: (value: string) => (value ? window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString() : '-'),
        },
    ];

    const documentActions = [
        {
            label: t('View'),
            icon: 'Eye',
            action: 'view',
            className: 'text-primary',
            requiredPermission: 'view-client-documents',
        },
        {
            label: t('Edit'),
            icon: 'Edit',
            action: 'edit',
            className: 'text-amber-500',
            requiredPermission: 'edit-client-documents',
        },
        {
            label: t('Delete'),
            icon: 'Trash2',
            action: 'delete',
            className: 'text-red-500',
            requiredPermission: 'delete-client-documents',
        },
    ];

    const formatDate = (value?: string | null) => {
        if (!value) return '-';
        return window.appSettings?.formatDate(value) || new Date(value).toLocaleDateString();
    };

    const businessTypeLabel = client.business_type === 'b2b' ? t('Business') : t('Individual');
    const statusLabel = client.status === 'active' ? t('Active') : t('Inactive');
    const isBusiness = client.business_type === 'b2b';

    const detailItems: { label?: string; value?: ReactNode; type?: 'divider' }[] = [
        { label: t('Name'), value: getTranslatedValue(client.name) || '-' },
        { label: t('Client Type'), value: getTranslatedValue(client.client_type?.name_translations || client.client_type?.name) || '-' },
        { label: t('Business Type'), value: businessTypeLabel },
        { label: t('Phone'), value: client.phone || '-' },
        { label: t('Email'), value: client.email || '-' },
        { label: t('Client ID'), value: client.client_id || '-' },
        { type: 'divider' },
        { label: t('Nationality'), value: getTranslatedValue(client.nationality?.nationality_name) },
        { label: t('ID National'), value: client.id_number || '-' },
        { label: t('Gender'), value: client.gender ? t(client.gender.charAt(0).toUpperCase() + client.gender.slice(1)) : '-' },
        { label: t('Date of Birth'), value: formatDate(client.date_of_birth) },
        { label: t('Tax Rate'), value: client.tax_rate ? `${client.tax_rate}%` : '0%' },
        { label: t('Address'), value: client.address || '-' },
        { label: t('Status'), value: statusLabel },
        { label: t('Created'), value: formatDate(client.created_at) },
    ];

    if (isBusiness) {
        detailItems.splice(
            4,
            0,
            { label: t('Unified Number'), value: client.unified_number || '-' },
            { label: t('CR Number'), value: client.cr_number || '-' },
            { label: t('CR Issuance Date'), value: formatDate(client.cr_issuance_date) },
            { label: t('Tax ID'), value: client.tax_id || '-' },
        );
    }

    return (
        <PageTemplate
            title={`${t('Client Details')}: ${getTranslatedValue(client.name)}`}
            url={route('clients.show', client.id)}
            breadcrumbs={breadcrumbs}
            actions={pageActions}
            noPadding
        >
            <div className="space-y-6">
                {/* Client Details Card */}
                <Card className="px-6">
                    <div className={`mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 ${isRtl ? 'text-right' : 'text-left'}`}>
                        {detailItems.map((item, index) => {
                            if (item.type === 'divider') {
                                return <div key={`divider-${index}`} className="col-span-full h-px bg-gray-200 dark:bg-gray-800" />;
                            }

                            return (
                                <div key={`${item.label}-${index}`} className="space-y-1">
                                    <div className="text-lx font-medium text-gray-500 dark:text-gray-400">{item.label}</div>
                                    <div className="text-sm font-semibold text-gray-900 dark:text-gray-100">{item.value || '-'}</div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="my-6 space-y-2">
                      <div className="col-span-full h-px bg-gray-200 dark:bg-gray-800" />
                      
                      <div className="text-xs font-medium text-gray-500 dark:text-gray-400">{t('Notes')}</div>
                        <div className="rounded p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {client.notes || t('No notes available')}
                        </div>
                    </div>
                </Card>

                {/* Tabs */}
                <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-900">
                    <div className="border-b border-gray-200 dark:border-gray-700">
                        <nav className="flex overflow-x-auto">
                            <button
                                onClick={() => setActiveTab('cases')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'cases'
                                        ? 'border-primary text-primary'
                                        : 'hover:text-primary dark:hover:text-primary border-transparent text-gray-500 dark:text-gray-400'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <Briefcase className="h-4 w-4" />
                                    <span>
                                        {t('Cases')} ({casesTotal})
                                    </span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('invoices')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'invoices'
                                        ? 'border-primary text-primary'
                                        : 'hover:text-primary dark:hover:text-primary border-transparent text-gray-500 dark:text-gray-400'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <Receipt className="h-4 w-4" />
                                    <span>
                                        {t('Invoices')} ({invoicesTotal})
                                    </span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('payments')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'payments'
                                        ? 'border-primary text-primary'
                                        : 'hover:text-primary dark:hover:text-primary border-transparent text-gray-500 dark:text-gray-400'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <CreditCard className="h-4 w-4" />
                                    <span>
                                        {t('Payments')} ({paymentsTotal})
                                    </span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('documents')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'documents'
                                        ? 'border-primary text-primary'
                                        : 'hover:text-primary dark:hover:text-primary border-transparent text-gray-500 dark:text-gray-400'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <FileText className="h-4 w-4" />
                                    <span>
                                        {t('Documents')} ({documentsTotal})
                                    </span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('billing')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'billing'
                                        ? 'border-primary text-primary'
                                        : 'hover:text-primary dark:hover:text-primary border-transparent text-gray-500 dark:text-gray-400'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <FileText className="h-4 w-4" />
                                    <span>
                                        {t('Billing Info')} ({client.billing_info ? 1 : 0})
                                    </span>
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
                                        filters={[
                                            {
                                                name: 'case_type_id',
                                                label: t('Case Type'),
                                                type: 'select',
                                                value: selectedCaseType,
                                                onChange: setSelectedCaseType,
                                                options: [
                                                    { value: 'all', label: t('All Types') },
                                                    ...(caseTypes || []).map((type: any) => ({
                                                        value: type.id.toString(),
                                                        label: getTranslatedValue(type.name),
                                                    })),
                                                ],
                                            },
                                            {
                                                name: 'case_status_id',
                                                label: t('Case Status'),
                                                type: 'select',
                                                value: selectedCaseStatus,
                                                onChange: setSelectedCaseStatus,
                                                options: [
                                                    { value: 'all', label: t('All Statuses') },
                                                    ...(caseStatuses || []).map((status: any) => ({
                                                        value: status.id.toString(),
                                                        label: getTranslatedValue(status.name),
                                                    })),
                                                ],
                                            },
                                            {
                                                name: 'priority',
                                                label: t('Priority'),
                                                type: 'select',
                                                value: selectedPriority,
                                                onChange: setSelectedPriority,
                                                options: [
                                                    { value: 'all', label: t('All Priorities') },
                                                    { value: 'low', label: t('Low') },
                                                    { value: 'medium', label: t('Medium') },
                                                    { value: 'high', label: t('High') },
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
                                            {
                                                name: 'court_id',
                                                label: t('Court'),
                                                type: 'select',
                                                value: selectedCourt,
                                                onChange: setSelectedCourt,
                                                options: [
                                                    { value: 'all', label: t('All Courts') },
                                                    ...(courts || []).map((court: any) => ({
                                                        value: court.id.toString(),
                                                        label: getTranslatedValue(court.name),
                                                    })),
                                                ],
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
                                {casesData && casesData.length > 0 ? (
                                    <>
                                        <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
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
                                            router.get(
                                                route('clients.show', client.id),
                                                {
                                                    invoice_page: 1,
                                                    invoice_search: invoiceSearchTerm || undefined,
                                                    invoice_per_page: filters.invoice_per_page || 10,
                                                },
                                                { preserveState: true, preserveScroll: true },
                                            );
                                        }}
                                        filters={[]}
                                        showFilters={false}
                                        setShowFilters={() => { }}
                                        hasActiveFilters={() => invoiceSearchTerm !== ''}
                                        activeFilterCount={() => (invoiceSearchTerm ? 1 : 0)}
                                        onResetFilters={() => {
                                            setInvoiceSearchTerm('');
                                            router.get(
                                                route('clients.show', client.id),
                                                {
                                                    invoice_page: 1,
                                                    invoice_per_page: filters.invoice_per_page || 10,
                                                },
                                                { preserveState: true, preserveScroll: true },
                                            );
                                        }}
                                        onApplyFilters={() => { }}
                                    />
                                </div>
                                {invoicesData && invoicesData.length > 0 ? (
                                    <>
                                        <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
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
                                            router.get(
                                                route('clients.show', client.id),
                                                {
                                                    payment_page: 1,
                                                    payment_search: paymentSearchTerm || undefined,
                                                    payment_per_page: filters.payment_per_page || 10,
                                                },
                                                { preserveState: true, preserveScroll: true },
                                            );
                                        }}
                                        filters={[]}
                                        showFilters={false}
                                        setShowFilters={() => { }}
                                        hasActiveFilters={() => paymentSearchTerm !== ''}
                                        activeFilterCount={() => (paymentSearchTerm ? 1 : 0)}
                                        onResetFilters={() => {
                                            setPaymentSearchTerm('');
                                            router.get(
                                                route('clients.show', client.id),
                                                {
                                                    payment_page: 1,
                                                    payment_per_page: filters.payment_per_page || 10,
                                                },
                                                { preserveState: true, preserveScroll: true },
                                            );
                                        }}
                                        onApplyFilters={() => { }}
                                    />
                                </div>
                                {paymentsData && paymentsData.length > 0 ? (
                                    <>
                                        <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-900">
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
                                    {hasPermission(permissions, 'create-client-documents') && (
                                        <button
                                            onClick={() => { setCurrentDocument(null); setDocumentFormMode('create'); setIsDocumentFormOpen(true); }}
                                            className="flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                                        >
                                            <Plus className="h-4 w-4" />
                                            {t('Add New Document')}
                                        </button>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <SearchAndFilterBar
                                        searchTerm={documentSearchTerm}
                                        onSearchChange={setDocumentSearchTerm}
                                        onSearch={handleDocumentSearch}
                                        filters={[
                                            {
                                                name: 'document_type_id',
                                                label: t('Type'),
                                                type: 'select',
                                                value: selectedDocumentType,
                                                onChange: setSelectedDocumentType,
                                                options: [
                                                    { value: 'all', label: t('All Types') },
                                                    ...(documentTypes || []).map((type: any) => ({
                                                        value: type.id.toString(),
                                                        label: getDocTypeName(type),
                                                    })),
                                                ],
                                            },
                                            {
                                                name: 'document_status',
                                                label: t('Status'),
                                                type: 'select',
                                                value: selectedDocumentStatus,
                                                onChange: setSelectedDocumentStatus,
                                                options: [
                                                    { value: 'all', label: t('All Statuses') },
                                                    { value: 'active', label: t('Active') },
                                                    { value: 'archived', label: t('Archived') },
                                                ],
                                            },
                                        ]}
                                        showFilters={showDocumentFilters}
                                        setShowFilters={setShowDocumentFilters}
                                        hasActiveFilters={hasDocumentFilters}
                                        activeFilterCount={documentFilterCount}
                                        onResetFilters={handleDocumentResetFilters}
                                        onApplyFilters={applyDocumentFilters}
                                        currentPerPage={filters.document_per_page?.toString() || '10'}
                                        onPerPageChange={(value) => {
                                            router.get(route('clients.show', client.id), {
                                                ...filters,
                                                document_page: 1,
                                                document_per_page: parseInt(value, 10),
                                                document_search: documentSearchTerm || undefined,
                                                document_type_id: selectedDocumentType !== 'all' ? selectedDocumentType : undefined,
                                                document_status: selectedDocumentStatus !== 'all' ? selectedDocumentStatus : undefined,
                                            }, { preserveState: true, preserveScroll: true });
                                        }}
                                    />
                                </div>

                                <CrudTable
                                    columns={documentColumns}
                                    actions={documentActions}
                                    data={documentsData}
                                    from={documents?.from ?? 1}
                                    onAction={handleDocumentAction}
                                    permissions={permissions}
                                    entityPermissions={{
                                        view: 'view-client-documents',
                                        edit: 'edit-client-documents',
                                        delete: 'delete-client-documents',
                                    }}
                                />

                                <Pagination
                                    from={documents?.from || 0}
                                    to={documents?.to || 0}
                                    total={documents?.total || 0}
                                    links={documents?.links}
                                    entityName={t('documents')}
                                    onPageChange={(url) => router.get(url)}
                                />
                            </div>
                        )}

                        {activeTab === 'billing' && (
                            <div>
                                <form onSubmit={handleBillingSubmit}>
                                    <Card className="px-6">
                                        {/* Card header: title + Edit/Cancel + Delete (same as company profile) */}
                                        <div className="mt-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
                                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{t('Billing Information')}</h3>
                                            <div className={`flex items-center gap-2 ${isRtl ? 'flex-row-reverse' : ''}`}>
                                                {client.billing_info && hasPermission(permissions, 'delete-client-billing') && (
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="icon"
                                                        className="h-9 w-9 shrink-0 rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-950/30 dark:hover:text-red-400"
                                                        onClick={() => setIsBillingDeleteOpen(true)}
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </Button>
                                                )}
                                                {(hasPermission(permissions, 'edit-client-billing') || (!client.billing_info && hasPermission(permissions, 'create-client-billing'))) && (
                                                    <Button
                                                        type="button"
                                                        variant={isBillingEditing ? 'outline' : 'default'}
                                                        size="sm"
                                                        className="flex items-center gap-2"
                                                        onClick={() => setIsBillingEditing(!isBillingEditing)}
                                                    >
                                                        {isBillingEditing ? t('Cancel') : (
                                                            <>
                                                                <Edit className="h-4 w-4" />
                                                                {client.billing_info ? t('Edit') : t('Add Billing Info')}
                                                            </>
                                                        )}
                                                    </Button>
                                                )}
                                            </div>
                                        </div>
                                            {/* Same form fields as edit: read-only when !isBillingEditing */}
                                            <div className="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                <div className="space-y-2">
                                                    <Label htmlFor="billing_contact_name" className="text-sm font-medium">{t('Contact Name')}</Label>
                                                    <Input
                                                        id="billing_contact_name"
                                                        value={billingFormData.billing_contact_name}
                                                        onChange={(e) => handleBillingChange('billing_contact_name', e.target.value)}
                                                        disabled={!isBillingEditing}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="billing_contact_email" className="text-sm font-medium">{t('Contact Email')}</Label>
                                                    <Input
                                                        id="billing_contact_email"
                                                        type="email"
                                                        value={billingFormData.billing_contact_email}
                                                        onChange={(e) => handleBillingChange('billing_contact_email', e.target.value)}
                                                        disabled={!isBillingEditing}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="billing_contact_phone" className="text-sm font-medium">{t('Contact Phone')}</Label>
                                                    <Input
                                                        id="billing_contact_phone"
                                                        value={billingFormData.billing_contact_phone}
                                                        onChange={(e) => handleBillingChange('billing_contact_phone', e.target.value)}
                                                        disabled={!isBillingEditing}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <div className="space-y-2 md:col-span-2">
                                                    <Label htmlFor="billing_address" className="text-sm font-medium">{t('Billing Address')}</Label>
                                                    <Input
                                                        id="billing_address"
                                                        value={billingFormData.billing_address}
                                                        onChange={(e) => handleBillingChange('billing_address', e.target.value)}
                                                        disabled={!isBillingEditing}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="payment_terms" className="text-sm font-medium">{t('Payment Terms')}</Label>
                                                    <Select
                                                        value={billingFormData.payment_terms || 'net_30'}
                                                        onValueChange={(v) => handleBillingChange('payment_terms', v)}
                                                        disabled={!isBillingEditing}
                                                    >
                                                        <SelectTrigger className="text-sm">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="net_15" className="text-sm">{t('Net 15 days')}</SelectItem>
                                                            <SelectItem value="net_30" className="text-sm">{t('Net 30 days')}</SelectItem>
                                                            <SelectItem value="net_45" className="text-sm">{t('Net 45 days')}</SelectItem>
                                                            <SelectItem value="net_60" className="text-sm">{t('Net 60 days')}</SelectItem>
                                                            <SelectItem value="due_on_receipt" className="text-sm">{t('Due on receipt')}</SelectItem>
                                                            <SelectItem value="custom" className="text-sm">{t('Custom')}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="custom_payment_terms" className="text-sm font-medium">{t('Custom Payment Terms')}</Label>
                                                    <Input
                                                        id="custom_payment_terms"
                                                        value={billingFormData.custom_payment_terms}
                                                        onChange={(e) => handleBillingChange('custom_payment_terms', e.target.value)}
                                                        disabled={!isBillingEditing}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="currency" className="text-sm font-medium">{t('Currency')}</Label>
                                                    <Select
                                                        value={billingFormData.currency || ''}
                                                        onValueChange={(v) => handleBillingChange('currency', v)}
                                                        disabled={!isBillingEditing}
                                                    >
                                                        <SelectTrigger className="text-sm">
                                                            <SelectValue placeholder={t('Select currency')} />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {(currencies || []).map((c: any) => (
                                                                <SelectItem key={c.code || c.value} value={String(c.code ?? c.value ?? '')} className="text-sm">
                                                                    {c.name || c.label || c.code || ''}
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div className="space-y-2">
                                                    <Label htmlFor="status" className="text-sm font-medium">{t('Status')}</Label>
                                                    <Select
                                                        value={billingFormData.status || 'active'}
                                                        onValueChange={(v) => handleBillingChange('status', v)}
                                                        disabled={!isBillingEditing}
                                                    >
                                                        <SelectTrigger className="text-sm">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="active" className="text-sm">{t('Active')}</SelectItem>
                                                            <SelectItem value="suspended" className="text-sm">{t('Suspended')}</SelectItem>
                                                            <SelectItem value="closed" className="text-sm">{t('Closed')}</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div className="space-y-2 md:col-span-2">
                                                    <Label htmlFor="billing_created_at" className="text-sm font-medium">{t('Creation Date')}</Label>
                                                    <Input
                                                        id="billing_created_at"
                                                        value={client.billing_info?.created_at ? (window.appSettings?.formatDate(client.billing_info.created_at) || new Date(client.billing_info.created_at).toLocaleDateString()) : '-'}
                                                        disabled
                                                        className="text-sm bg-muted"
                                                    />
                                                </div>
                                            </div>
                                            <div className="my-6 space-y-2">
                                                <div className="col-span-full h-px bg-gray-200 dark:bg-gray-800" />
                                                <Label htmlFor="billing_notes" className="text-sm font-medium">{t('Notes')}</Label>
                                                <Textarea
                                                    id="billing_notes"
                                                    value={billingFormData.billing_notes}
                                                    onChange={(e) => handleBillingChange('billing_notes', e.target.value)}
                                                    disabled={!isBillingEditing}
                                                    rows={3}
                                                    className="text-sm"
                                                />
                                            </div>
                                        </Card>
                                        {isBillingEditing && (
                                            <div className={`flex mt-6 ${isRtl ? 'justify-start' : 'justify-end'}`}>
                                                <Button type="submit" className="flex items-center gap-2">
                                                    <Save className="h-4 w-4" />
                                                    {t('Save')}
                                                </Button>
                                            </div>
                                        )}
                                    </form>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Document Create/Edit Modal */}
            <CrudFormModal
                isOpen={isDocumentFormOpen}
                onClose={() => { setIsDocumentFormOpen(false); setCurrentDocument(null); }}
                onSubmit={handleDocumentFormSubmit}
                formConfig={{
                    fields: [
                        { name: 'document_name', label: t('Document Name'), type: 'text', required: true },
                        {
                            name: 'document_type_id',
                            label: t('Document Type'),
                            type: 'select',
                            required: true,
                            options: (documentTypes || []).map((type: any) => ({
                                value: type.id.toString(),
                                label: getDocTypeName(type),
                            })),
                        },
                        {
                            name: 'file',
                            label: t('File'),
                            type: 'media-picker',
                            required: documentFormMode === 'create',
                        },
                        { name: 'description', label: t('Description'), type: 'textarea' },
                        {
                            name: 'status',
                            label: t('Status'),
                            type: 'select',
                            options: [
                                { value: 'active', label: t('Active') },
                                { value: 'archived', label: t('Archived') },
                            ],
                        },
                    ],
                    modalSize: 'lg',
                }}
                initialData={{
                    ...currentDocument,
                    file: currentDocument?.file_path,
                    status: currentDocument?.status || 'active',
                    document_type_id: currentDocument?.document_type_id?.toString() || currentDocument?.document_type_id,
                }}
                title={documentFormMode === 'create' ? t('Add New Document') : t('Edit Document')}
                mode={documentFormMode}
            />

            {/* Document View Modal */}
            <CrudFormModal
                isOpen={isDocumentViewOpen}
                onClose={() => { setIsDocumentViewOpen(false); setCurrentDocument(null); }}
                onSubmit={() => setIsDocumentViewOpen(false)}
                formConfig={{
                    fields: [
                        { name: 'document_name', label: t('Document Name'), type: 'text', disabled: true },
                        {
                            name: 'document_type_display',
                            label: t('Type'),
                            type: 'text',
                            disabled: true,
                        },
                        { name: 'description', label: t('Description'), type: 'textarea', disabled: true },
                        { name: 'status', label: t('Status'), type: 'text', disabled: true },
                        { name: 'created_at', label: t('Upload Date'), type: 'text', disabled: true },
                    ],
                    modalSize: 'md',
                }}
                initialData={{
                    ...currentDocument,
                    document_type_display: getDocTypeName(currentDocument?.document_type),
                    created_at: currentDocument?.created_at
                        ? (window.appSettings?.formatDate(currentDocument.created_at) || new Date(currentDocument.created_at).toLocaleDateString())
                        : '',
                }}
                title={t('View Document')}
                mode="view"
            />

            {/* Document Delete Modal */}
            <CrudDeleteModal
                isOpen={isDocumentDeleteOpen}
                onClose={() => { setIsDocumentDeleteOpen(false); setCurrentDocument(null); }}
                onConfirm={handleDocumentDeleteConfirm}
                itemName={currentDocument?.document_name || ''}
                entityName="Document"
            />

            {/* Billing Delete Modal */}
            <CrudDeleteModal
                isOpen={isBillingDeleteOpen}
                onClose={() => setIsBillingDeleteOpen(false)}
                onConfirm={() => {
                    if (!client.billing_info?.id) return;
                    router.delete(route('clients.billing.destroy', client.billing_info.id), {
                        onSuccess: (page) => {
                            setIsBillingDeleteOpen(false);
                            toast.dismiss();
                            const flash = (page?.props?.flash as any);
                            if (flash?.success) toast.success(flash.success);
                            if (flash?.error) toast.error(flash.error);
                        },
                        onError: () => {
                            toast.dismiss();
                            toast.error(t('Failed to delete billing information'));
                        },
                    });
                }}
                itemName={t('Billing Information')}
                entityName="billing"
            />

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
                                    label: `${invoice.invoice_number} - ${getTranslatedValue(invoice.client?.name)}`,
                                }));

                                // If currentPayment has an invoice that's not in allInvoices, add it
                                if (currentPayment?.invoice && !invoiceOptions.find((opt: any) => opt.value === String(currentPayment.invoice.id))) {
                                    invoiceOptions.push({
                                        value: String(currentPayment.invoice.id),
                                        label: `${currentPayment.invoice.invoice_number} - ${getTranslatedValue(currentPayment.invoice.client?.name) || ''}`,
                                    });
                                }

                                return invoiceOptions;
                            })(),
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
                                { value: 'online', label: t('Online Payment') },
                            ],
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
                                    <div className="space-y-2">
                                        {fileList.map((file: string, index: number) => {
                                            const displayUrl = getDisplayUrl(file);
                                            const isImg = isImage(file);
                                            const fileName = file.split('/').pop() || file;

                                            return (
                                                <div key={index} className="flex items-center gap-2 rounded-md border bg-gray-50 p-2">
                                                    {isImg ? (
                                                        <img
                                                            src={displayUrl}
                                                            alt={fileName}
                                                            className="h-16 w-16 rounded object-cover"
                                                            onError={(e) => {
                                                                (e.target as HTMLImageElement).style.display = 'none';
                                                            }}
                                                        />
                                                    ) : (
                                                        <div className="flex h-16 w-16 items-center justify-center rounded bg-gray-200">
                                                            <span className="text-xs text-gray-500">{getFileExtension(file).toUpperCase()}</span>
                                                        </div>
                                                    )}
                                                    <div className="min-w-0 flex-1">
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
                            },
                        },
                    ],
                    modalSize: 'lg',
                }}
                initialData={currentPayment}
                title={t('View Payment')}
                mode={paymentFormMode}
            />
        </PageTemplate>
    );
}
