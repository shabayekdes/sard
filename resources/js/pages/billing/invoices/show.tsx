import { PageTemplate, PageAction } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Edit, FileText, Send, DollarSign, Link, Download } from 'lucide-react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { hasPermission } from '@/utils/authorization';
import { formatCurrencyForCompany } from '@/utils/helpers';
import { CrudFormModal } from '@/components/CrudFormModal';
import { useState } from 'react';

export default function ShowInvoice() {
    const { t } = useTranslation();
    const { invoice, auth, timeEntries, invoiceItems, clientBillingInfo, currencies } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);

    // Get formatted currency using company settings
    const formatAmount = (amount: number) => {
        return formatCurrencyForCompany(amount);
    };
    console.log(formatAmount);

    const handleSend = () => {
        toast.loading(t('Sending invoice...'));

        router.put(route('billing.invoices.send', invoice.id), {}, {
            onSuccess: (page: any) => {
                toast.dismiss();
                if (page.props.flash?.success) {
                    toast.success(page.props.flash.success);
                }
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error(`Failed to send invoice: ${Object.values(errors).join(', ')}`);
            }
        });
    };

    const handleCopyLink = () => {
        if (invoice.payment_token) {
            const paymentUrl = route('invoice.payment', invoice.payment_token);
            navigator.clipboard.writeText(paymentUrl).then(() => {
                toast.success(t('Payment link copied to clipboard'));
            }).catch(() => {
                toast.error(t('Failed to copy payment link'));
            });
        } else {
            toast.error(t('Payment link not available'));
        }
    };

    const handlePaymentSubmit = (formData: any) => {
        toast.loading(t('Recording payment...'));

        router.post(route('billing.payments.store'), formData, {
            onSuccess: (page: any) => {
                toast.dismiss();
                if (page.props.flash?.success) {
                    toast.success(page.props.flash.success);
                }
                setIsPaymentModalOpen(false);
                router.reload();
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error(`Failed to record payment: ${Object.values(errors).join(', ')}`);
            }
        });
    };

    const getStatusColor = (status: string) => {
        const statusColors = {
            draft: 'bg-gray-50 text-gray-700 ring-gray-600/20',
            sent: 'bg-blue-50 text-blue-700 ring-blue-600/20',
            paid: 'bg-green-50 text-green-700 ring-green-600/20',
            overdue: 'bg-red-50 text-red-700 ring-red-600/20',
            cancelled: 'bg-gray-50 text-gray-700 ring-gray-600/20'
        };
        return statusColors[status as keyof typeof statusColors] || statusColors.draft;
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Billing & Invoicing'), href: route('billing.invoices.index') },
        { title: t('Invoices'), href: route('billing.invoices.index') },
        { title: t('View Invoice') }
    ];

    const pageActions: PageAction[] = [
        {
            label: t('Back to Invoices'),
            icon: <ArrowLeft className="h-4 w-4 mr-2" />,
            variant: 'outline' as const,
            onClick: () => router.get(route('billing.invoices.index'))
        }
    ];

    if (hasPermission(permissions, 'edit-invoices')) {
        pageActions.push({
            label: t('Edit Invoice'),
            icon: <Edit className="h-4 w-4 mr-2" />,
            variant: 'default' as const,
            onClick: () => router.get(route('billing.invoices.edit', invoice.id))
        });
    }

    if (hasPermission(permissions, 'create-payments')) {
        pageActions.push({
            label: t('Add Payment'),
            icon: <DollarSign className="h-4 w-4 mr-2" />,
            variant: 'default' as const,
            onClick: () => setIsPaymentModalOpen(true)
        });
    }

    if (hasPermission(permissions, 'view-invoices')) {
        const pdfLabel = invoice.client?.business_type === 'b2b' ? t('Tax Invoice') : t('Simplified Tax Invoice');
        pageActions.push({
            label: pdfLabel,
            icon: <Download className="h-4 w-4 mr-2" />,
            variant: 'outline' as const,
            onClick: () => window.open(route('billing.invoices.generate', invoice.id), '_blank')
        });
    }

    if (invoice.payment_token) {
        pageActions.push({
            label: t('Copy Link'),
            icon: <Link className="h-4 w-4 mr-2" />,
            variant: 'outline' as const,
            onClick: handleCopyLink
        });
    }

    if (hasPermission(permissions, 'send-invoices') && invoice.status === 'draft') {
        pageActions.push({
            label: t('Send'),
            icon: <Send className="h-4 w-4 mr-2" />,
            variant: 'default' as const,
            onClick: handleSend
        });
    }




    return (
        <PageTemplate
            title={`${t('Invoice')} #${invoice.invoice_number || invoice.id}`}
            url={route('billing.invoices.show', invoice.id)}
            breadcrumbs={breadcrumbs}
            actions={pageActions}
            noPadding
        >
            <div className="space-y-6">
                {/* Invoice Header */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <div className="mb-6 flex items-start justify-between">
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900">
                                {t('Invoice')} #{invoice.invoice_number || invoice.id}
                            </h2>
                            <p className="mt-1 text-gray-600">
                                {t('Created on')}{' '}
                                {window.appSettings?.formatDate(invoice.created_at) || new Date(invoice.created_at).toLocaleDateString()}
                            </p>
                        </div>
                        <div className="text-right">
                            <span
                                className={`inline-flex items-center rounded-md px-3 py-1 text-sm font-medium ring-1 ring-inset ${getStatusColor(invoice.status)}`}
                            >
                                {t(invoice.status?.charAt(0).toUpperCase() + invoice.status?.slice(1))}
                            </span>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <h3 className="mb-3 text-lg font-semibold">{t('Client Information')}</h3>
                            <div className="space-y-2">
                                <p>
                                    <strong>{t('Name')}:</strong> {invoice.client?.name || '-'}
                                    {invoice.client && (
                                        <span className="ml-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                            {invoice.client.business_type === 'b2b' ? t('Business') : t('Individual')}
                                        </span>
                                    )}
                                </p>
                                <p>
                                    <strong>{t('Email')}:</strong> {invoice.client?.email || '-'}
                                </p>
                                <p>
                                    <strong>{t('Phone')}:</strong> {invoice.client?.phone || '-'}
                                </p>
                                {invoice.client?.business_type === 'b2b' && (
                                    <>
                                        <p>
                                            <strong>{t('CR')}:</strong> {invoice.client?.cr_number || '-'}
                                        </p>
                                        <p>
                                            <strong>{t('Tax Number')}:</strong> {invoice.client?.tax_id || '-'}
                                        </p>
                                        <p>
                                            <strong>{t('Address')}:</strong> {invoice.client?.address || '-'}
                                        </p>
                                    </>
                                )}
                            </div>
                        </div>

                        <div>
                            <h3 className="mb-3 text-lg font-semibold">{t('Invoice Details')}</h3>
                            <div className="space-y-2">
                                <p>
                                    <strong>{t('Invoice Date')}:</strong>{' '}
                                    {window.appSettings?.formatDate(invoice.invoice_date) || new Date(invoice.invoice_date).toLocaleDateString()}
                                </p>
                                <p>
                                    <strong>{t('Due Date')}:</strong>{' '}
                                    {window.appSettings?.formatDate(invoice.due_date) || new Date(invoice.due_date).toLocaleDateString()}
                                </p>
                                {invoice.case && (
                                    <p>
                                        <strong>{t('Case')}:</strong>{' '}
                                        {invoice.case.case_id ? `${invoice.case.case_id} - ${invoice.case.title}` : invoice.case.title}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Line Items */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <h3 className="mb-4 text-lg font-semibold">{t('Invoice Items')}</h3>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                        {t('Description')}
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">{t('Quantity')}</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">{t('Rate')}</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">{t('Amount')}</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white">
                                {invoiceItems?.map((item: any, index: number) => (
                                    <tr key={index} className={item.type === 'expense' ? 'bg-orange-50' : ''}>
                                        <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            <div className="space-y-1">
                                                <div>{item.description}</div>
                                                {item.type === 'expense' && (
                                                    <div className="flex items-center text-xs text-orange-600">
                                                        <span className="rounded bg-orange-100 px-2 py-1 font-medium text-orange-700">
                                                            {t('Expense')}
                                                        </span>
                                                        {item.expense_date && (
                                                            <span className="ml-2">
                                                                {window.appSettings?.formatDate(item.expense_date) ||
                                                                    new Date(item.expense_date).toLocaleDateString()}
                                                            </span>
                                                        )}
                                                    </div>
                                                )}
                                                {item.type === 'time' && (
                                                    <div className="flex items-center text-xs text-blue-600">
                                                        <span className="rounded bg-blue-100 px-2 py-1 font-medium text-blue-700">
                                                            {t('Time Entry')}
                                                        </span>
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{item.quantity}</td>
                                        <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {formatAmount(parseFloat(item.rate || 0))}
                                        </td>
                                        <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {formatAmount(parseFloat(item.amount || 0))}
                                        </td>
                                    </tr>
                                ))}

                                {(!invoiceItems || invoiceItems.length === 0) && (
                                    <tr>
                                        <td colSpan={4} className="px-6 py-4 text-center text-gray-500">
                                            {t('No items found')}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Invoice Summary */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <div className="flex justify-end">
                        <div className="w-full max-w-sm space-y-3">
                            <div className="flex justify-between">
                                <span className="text-gray-600">{t('Subtotal')}:</span>
                                <span className="font-medium">{formatAmount(parseFloat((invoice.subtotal || 0).toString()))}</span>
                            </div>

                            {invoice.tax_amount > 0 && (
                                <div className="flex justify-between">
                                    <span className="text-gray-600">{t('Tax')}:</span>
                                    <span className="font-medium">{formatAmount(parseFloat((invoice.tax_amount || 0).toString()))}</span>
                                </div>
                            )}

                            <div className="border-t pt-3">
                                <div className="flex justify-between text-lg font-bold">
                                    <span>{t('Total')}:</span>
                                    <span>{formatAmount(parseFloat((invoice.total_amount || 0).toString()))}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Additional Info */}
                <div className="rounded-lg bg-white p-6 shadow">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900">{t('Additional Info')}</h3>
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <h4 className="mb-2 text-sm font-semibold text-gray-700">{t('NOTES')}</h4>
                            <p className="text-sm text-gray-600">
                                {invoice.notes || t('Thank you for your business. Please remit payment by due date.')}
                            </p>
                        </div>
                        <div>
                            <h4 className="mb-2 text-sm font-semibold text-gray-700">{t('TERMS')}</h4>
                            <p className="text-sm text-gray-600">
                                {(() => {
                                    const billingInfo = clientBillingInfo?.[invoice.client_id];
                                    if (billingInfo?.custom_payment_terms) {
                                        return billingInfo.custom_payment_terms;
                                    }
                                    if (billingInfo?.payment_terms) {
                                        const termsMap: Record<string, string> = {
                                            net_15: t('Net 15 days'),
                                            net_30: t('Net 30 days'),
                                            net_45: t('Net 45 days'),
                                            net_60: t('Net 60 days'),
                                            due_on_receipt: t('Due on receipt'),
                                            custom: billingInfo.custom_payment_terms || t('Custom terms'),
                                        };
                                        const termText = termsMap[billingInfo.payment_terms] || billingInfo.payment_terms;
                                        return `${termText}. ${t('Late payment fee of 1.5% per month applies.')}`;
                                    }
                                    return t('Net 30 days. Late payment fee of 1.5% per month applies.');
                                })()}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Payment Modal */}
            <CrudFormModal
                isOpen={isPaymentModalOpen}
                onClose={() => setIsPaymentModalOpen(false)}
                onSubmit={handlePaymentSubmit}
                formConfig={{
                    fields: [
                        {
                            name: 'invoice_id',
                            label: t('Invoice'),
                            type: 'select',
                            required: true,
                            disabled: true,
                            options: [
                                {
                                    value: invoice.id.toString(),
                                    label: `${invoice.invoice_number || invoice.id} - ${invoice.client?.name || '-'}`,
                                },
                            ],
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
                        { name: 'amount', label: t('Amount'), type: 'number', step: '0.01', required: true, min: '0' },
                        { name: 'payment_date', label: t('Payment Date'), type: 'date', required: true },
                        { name: 'notes', label: t('Notes'), type: 'textarea' },
                        {
                            name: 'attachment',
                            label: t('Attachment'),
                            type: 'media-picker',
                            multiple: true,
                            placeholder: t('Select files...'),
                        },
                    ],
                    modalSize: 'lg',
                }}
                initialData={{
                    invoice_id: invoice.id.toString(),
                    amount: invoice.remaining_amount || invoice.total_amount,
                    payment_date: new Date().toISOString().split('T')[0],
                    payment_method: 'cash',
                }}
                title={t('Record New Payment')}
                mode="create"
            />
        </PageTemplate>
    );
}
