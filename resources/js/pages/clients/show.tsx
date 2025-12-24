import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Pagination } from '@/components/ui/pagination';
import { ArrowLeft, FileText, Briefcase } from 'lucide-react';
import { formatCurrency } from '@/utils/helpers';
import { useLayout } from '@/contexts/LayoutContext';
export default function ClientShow() {
    const { t } = useTranslation();
    const { client, documents, cases, filters = {} } = usePage().props as any;
    const { position } = useLayout();
    const [activeTab, setActiveTab] = useState('cases');
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [showFilters, setShowFilters] = useState(false);

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


    return (
        <PageTemplate title={`${t('Client')}: ${client.name}`} url={route('clients.show', client.id)} breadcrumbs={breadcrumbs} actions={pageActions} noPadding>
            <div className="space-y-6">
                {/* Client Details Card */}
                <Card className="p-6">
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <h3 className="mb-4 text-lg font-semibold">{t('Basic Information')}</h3>
                            <div className="space-y-2">
                                <div>
                                    <strong>{t('Client ID')}:</strong> {client.client_id}
                                </div>
                                <div>
                                    <strong>{t('Name')}:</strong> {client.name}
                                </div>
                                <div>
                                    <strong>{t('Type')}:</strong> {client.type === 'b2c' ? t('Individual') : t('Company')}
                                </div>
                                <div>
                                    <strong>{t('Email')}:</strong> {client.email || '-'}
                                </div>
                                <div>
                                    <strong>{t('Phone')}:</strong> {client.phone || '-'}
                                </div>
                                <div>
                                    <strong>{t('Type')}:</strong> {client.client_type?.name || '-'}
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
                            <h3 className="mb-4 text-lg font-semibold">{t('Contact Information')}</h3>
                            <div className="space-y-2">
                                <div>
                                    <strong>{t('Address')}:</strong> {client.address || '-'}
                                </div>
                                <div>
                                    <strong>{t('Company')}:</strong> {client.company_name || '-'}
                                </div>
                                <div>
                                    <strong>{t('Tax ID')}:</strong> {client.tax_id || '-'}
                                </div>
                                <div>
                                    <strong>{t('Tax Rate')}:</strong> {client.tax_rate ? `${client.tax_rate}%` : '0%'}
                                </div>
                                <div>
                                    <strong>{t('Date of Birth')}:</strong>{' '}
                                    {client.date_of_birth
                                        ? window.appSettings?.formatDate(client.date_of_birth) || new Date(client.date_of_birth).toLocaleDateString()
                                        : '-'}
                                </div>
                                <div>
                                    <strong>{t('Referral Source')}:</strong> {client.referral_source || '-'}
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 className="mb-4 text-lg font-semibold">{t('Additional Information')}</h3>
                            <div className="space-y-2">
                                <div>
                                    <strong>{t('Notes')}:</strong>
                                </div>
                                <div className="rounded bg-gray-50 p-3 text-sm text-gray-600">{client.notes || t('No notes available')}</div>
                                <div>
                                    <strong>{t('Created')}:</strong>{' '}
                                    {window.appSettings?.formatDate(client.created_at) || new Date(client.created_at).toLocaleDateString()}
                                </div>
                            </div>
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
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                    }`}
                            >
                                <div className="flex items-center space-x-2">
                                    <Briefcase className="h-4 w-4" />
                                    <span>{t('Cases')} ({casesTotal})</span>
                                </div>
                            </button>
                            <button
                                onClick={() => setActiveTab('documents')}
                                className={`flex-shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors ${activeTab === 'documents'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
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
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
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
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead className="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Case ID')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Title')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Type')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Status')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Priority')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Filing Date')}
                                                        </th>
                                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {t('Actions')}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                                    {casesData.map((caseItem: any) => (
                                                        <tr key={caseItem.id} className="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                                {caseItem.case_id}
                                                            </td>
                                                            <td className="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                                {caseItem.title}
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                                {caseItem.case_type ? (
                                                                    <span
                                                                        className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                                                                        style={{
                                                                            backgroundColor: `${caseItem.case_type.color}20`,
                                                                            color: caseItem.case_type.color
                                                                        }}
                                                                    >
                                                                        {caseItem.case_type.name}
                                                                    </span>
                                                                ) : (
                                                                    <span className="text-gray-500">-</span>
                                                                )}
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                                {caseItem.case_status ? (
                                                                    <span
                                                                        className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                                                                        style={{
                                                                            backgroundColor: `${caseItem.case_status.color}20`,
                                                                            color: caseItem.case_status.color
                                                                        }}
                                                                    >
                                                                        {caseItem.case_status.name}
                                                                    </span>
                                                                ) : (
                                                                    <span className="text-gray-500">-</span>
                                                                )}
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                                {caseItem.priority ? (
                                                                    <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${caseItem.priority === 'low' ? 'bg-green-50 text-green-700 ring-green-600/20' :
                                                                        caseItem.priority === 'medium' ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20' :
                                                                            'bg-red-50 text-red-700 ring-red-600/20'
                                                                        }`}>
                                                                        {t(caseItem.priority.charAt(0).toUpperCase() + caseItem.priority.slice(1))}
                                                                    </span>
                                                                ) : (
                                                                    <span className="text-gray-500">-</span>
                                                                )}
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                                {caseItem.filing_date
                                                                    ? (window.appSettings?.formatDate(caseItem.filing_date) || new Date(caseItem.filing_date).toLocaleDateString())
                                                                    : '-'}
                                                            </td>
                                                            <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                                                <a
                                                                    href={route('cases.show', caseItem.id)}
                                                                    className="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                                >
                                                                    {t('View')}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
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
        </PageTemplate>
    );
}
