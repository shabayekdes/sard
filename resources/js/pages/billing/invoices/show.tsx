import { CrudFormModal } from '@/components/CrudFormModal';
import { toast } from '@/components/custom-toast';
import { PageAction, PageTemplate } from '@/components/page-template';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { CurrencyAmount } from '@/components/currency-amount';
import { hasPermission } from '@/utils/authorization';
import { router, usePage } from '@inertiajs/react';
import { ArrowLeft, DollarSign, Download, Edit, FileText, Link, MoreVerticalIcon, Send, User } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function ShowInvoice() {
    const { t } = useTranslation();
    const {
        invoice,
        auth,
        invoiceItems,
        clientBillingInfo,
        companyProfile,
        amountPaid: amountPaidProp,
        remainingAmount: remainingAmountProp,
    } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);

    const formatDate = (date: string | null) => (date ? window.appSettings?.formatDate?.(date) || new Date(date).toLocaleDateString() : '-');

    const round2 = (n: number) => Math.round(n * 100) / 100;
    const amountPaid = round2(Number(amountPaidProp ?? invoice?.amount_paid ?? 0));
    const subtotal = round2(Number(invoice?.subtotal ?? 0));
    const taxAmount = round2(Number(invoice?.tax_amount ?? 0));
    const totalAmount = round2(Number(invoice?.total_amount ?? 0));
    const remainingAmount = round2(Number(remainingAmountProp ?? invoice?.remaining_amount ?? totalAmount - amountPaid));
    const taxRate = invoice?.tax_rate ?? invoice?.client?.tax_rate ?? 0;

    const handleSend = () => {
        router.put(
            route('billing.invoices.send', invoice.id),
            {},
            {
                onSuccess: (page: any) => {
                    toast.dismiss();
                    if (page.props.flash?.success) toast.success(page.props.flash.success);
                },
                onError: (errors) => {
                    toast.dismiss();
                    toast.error(`Failed to send invoice: ${Object.values(errors).join(', ')}`);
                },
            },
        );
    };

    const handleCopyLink = () => {
        if (invoice.payment_token) {
            const paymentUrl = route('invoice.payment', invoice.payment_token);
            navigator.clipboard.writeText(paymentUrl).then(
                () => toast.success(t('Payment link copied to clipboard')),
                () => toast.error(t('Failed to copy payment link')),
            );
        } else {
            toast.error(t('Payment link not available'));
        }
    };

    const handlePaymentSubmit = (formData: any) => {
        const payload = {
            ...formData,
            amount: Math.round(Number(formData.amount) * 100) / 100,
        };
        router.post(route('billing.payments.store'), payload, {
            onSuccess: (page: any) => {
                toast.dismiss();
                if (page.props.flash?.success) toast.success(page.props.flash.success);
                setIsPaymentModalOpen(false);
                router.reload();
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error(`Failed to record payment: ${Object.values(errors).join(', ')}`);
            },
        });
    };

    const getStatusColor = (status: string) => {
        const map: Record<string, string> = {
            draft: 'bg-gray-500 text-white',
            sent: 'bg-blue-500 text-white',
            paid: 'bg-green-500 text-white',
            partial_paid: 'bg-amber-500 text-white',
            overdue: 'bg-red-500 text-white',
            cancelled: 'bg-gray-500 text-white',
        };
        return map[status] ?? map.draft;
    };

    const getStatusLabel = (status: string) => {
        const map: Record<string, string> = {
            draft: t('Draft'),
            sent: t('Sent'),
            paid: t('Paid'),
            partial_paid: t('Partial Paid'),
            overdue: t('Overdue'),
            cancelled: t('Cancelled'),
        };
        return map[status] ?? (status ? t(status.charAt(0).toUpperCase() + status.slice(1)) : '');
    };

    const formatInvoiceNumber = (value: string | number) => {
        const s = String(value ?? '').replace(/\s/g, '');
        if (!s) return '-';
        return s.replace(/\D/g, '').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ');
    };

    // Per-line tax: distribute invoice subtotal/tax across lines by amount
    const getLineAmounts = (itemAmount: number) => {
        const amt = Number(itemAmount) || 0;
        if (totalAmount <= 0) return { subtotalWithoutTax: amt, tax: 0, total: amt };
        const ratio = amt / totalAmount;
        const subtotalWithoutTax = subtotal * ratio;
        const tax = taxAmount * ratio;
        return { subtotalWithoutTax, tax, total: amt };
    };

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Billing & Invoicing'), href: route('billing.invoices.index') },
        { title: t('Invoices'), href: route('billing.invoices.index') },
        { title: t('Invoice Details') },
    ];

    const pageActions: PageAction[] = [
        {
            label: t('Back to Invoices'),
            icon: <ArrowLeft className="h-4 w-4 sm:mr-2" />,
            variant: 'outline',
            onClick: () => router.get(route('billing.invoices.index')),
        },
    ];

    if (hasPermission(permissions, 'create-payments') && invoice?.status !== 'paid') {
        pageActions.push({
            label: t('Record New Payment'),
            icon: <DollarSign className="h-4 w-4 sm:mr-2" />,
            variant: 'default',
            onClick: () => setIsPaymentModalOpen(true),
        });
    }

    const moreOptions: PageAction[] = [];
    if (hasPermission(permissions, 'edit-invoices')) {
        moreOptions.push({
            label: t('Edit Invoice'),
            icon: <Edit className="h-4 w-4" />,
            variant: 'outline',
            onClick: () => router.get(route('billing.invoices.edit', invoice.id)),
        });
    }
    if (hasPermission(permissions, 'view-invoices')) {
        const pdfLabel = invoice.client?.business_type === 'b2b' ? t('Tax Invoice') : t('Simplified Tax Invoice');
        const pdfType = invoice.client?.business_type === 'b2b' ? 'tax' : 'simplified';
        moreOptions.push({
            label: pdfLabel,
            icon: <Download className="h-4 w-4" />,
            variant: 'outline',
            onClick: () => window.open(route('invoices.pdf', invoice.id) + `?type=${pdfType}`, '_blank'),
        });
    }
    if (invoice.payment_token) {
        moreOptions.push({
            label: t('Copy Link'),
            icon: <Link className="h-4 w-4" />,
            variant: 'outline',
            onClick: handleCopyLink,
        });
    }
    if (hasPermission(permissions, 'send-invoices') && invoice.status === 'draft') {
        moreOptions.push({
            label: t('Send'),
            icon: <Send className="h-4 w-4" />,
            variant: 'outline',
            onClick: handleSend,
        });
    }

    const termsText = (() => {
        const billingInfo = clientBillingInfo?.[invoice.client_id];
        if (billingInfo?.custom_payment_terms) return billingInfo.custom_payment_terms;
        if (billingInfo?.payment_terms) {
            const termsMap: Record<string, string> = {
                net_15: t('Net 15 days'),
                net_30: t('Net 30 days'),
                net_45: t('Net 45 days'),
                net_60: t('Net 60 days'),
                due_on_receipt: t('Due on receipt'),
                custom: billingInfo.custom_payment_terms || t('Custom terms'),
            };
            const termText = termsMap[billingInfo.payment_terms] ?? billingInfo.payment_terms;
            return `${termText}. ${t('Late payment fee of 1.5% per month applies.')}`;
        }
        return t('Net 30 days. Late payment fee of 1.5% per month applies.');
    })();

    return (
        <PageTemplate
            title={t('Invoice Details')}
            titleForHead={`${t('Invoice')} #${invoice?.invoice_number || invoice?.id}`}
            url={route('billing.invoices.show', invoice.id)}
            breadcrumbs={breadcrumbs}
            actions={
                <div className="flex items-center gap-2">
                    {pageActions.map((action, index) => (
                        <Button
                            key={index}
                            variant={action.variant as any}
                            size="sm"
                            onClick={action.onClick}
                            className={action.label === t('Add Payment') ? 'bg-primary' : ''}
                        >
                            {action.icon}
                            <span className="sr-only sm:not-sr-only">{action.label}</span>
                        </Button>
                    ))}
                    {moreOptions.length > 0 && (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" size="sm">
                                    <MoreVerticalIcon className="h-4 w-4 sm:mr-1" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                {moreOptions.map((opt, i) => (
                                    <DropdownMenuItem key={i} onClick={opt.onClick}>
                                        {opt.icon}
                                        {opt.label}
                                    </DropdownMenuItem>
                                ))}
                            </DropdownMenuContent>
                        </DropdownMenu>
                    )}
                </div>
            }
            noPadding
        >
            <div className="space-y-6">
                {/* Invoice number card (same structure as invoice/payment page) */}
                <Card className="mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <CardContent className="p-6">
                        <div className="flex flex-wrap items-start justify-between gap-4">
                            <div className="min-w-0 flex-1 text-start">
                                <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                                    {invoice?.client?.business_type === 'b2b' ? t('Tax Invoice') : t('Simplified Tax Invoice')}
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {t('Invoice Number')}: {formatInvoiceNumber(invoice?.invoice_number || invoice?.id)}
                                </p>
                                {invoice?.case && (
                                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {t('Case Title')}:{' '}
                                        {invoice.case.case_id ? `${invoice.case.case_id} - ${invoice.case.title}` : invoice.case.title}
                                    </p>
                                )}
                                <p className="mt-3 flex flex-wrap gap-x-8 text-sm text-gray-600 dark:text-gray-400">
                                    <span>
                                        {t('Invoice Date')}: {formatDate(invoice?.invoice_date)}
                                    </span>
                                    <span>
                                        {t('Due Date')}: {formatDate(invoice?.due_date)}
                                    </span>
                                </p>
                            </div>
                            <div className="flex items-center gap-3">
                                <span
                                    className={`inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ${getStatusColor(invoice?.status)}`}
                                >
                                    {getStatusLabel(invoice?.status)}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Invoice To & Invoice From */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="pb-2">
                            <div className="flex items-center gap-2">
                                <FileText className="text-muted-foreground h-5 w-5" />
                                <h3 className="text-base font-semibold">{t('Bill From')}</h3>
                            </div>
                            <p className="font-medium">{companyProfile?.name || invoice?.creator?.name || '-'}</p>
                        </CardHeader>
                        <CardContent className="space-y-2 text-sm">
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Address')}:</span> {companyProfile?.address || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Phone Number')}:</span> {companyProfile?.phone || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Email')}:</span> {companyProfile?.email || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Tax Number')}:</span> {companyProfile?.tax_number || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Commercial Register')}:</span>
                                {companyProfile?.cr || '-'}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <div className="flex items-center gap-2">
                                <User className="text-muted-foreground h-5 w-5" />
                                <h3 className="text-base font-semibold">{t('Bill To')}</h3>
                            </div>
                            <p className="font-medium">{invoice?.client?.name || '-'}</p>
                        </CardHeader>
                        <CardContent className="space-y-2 text-sm">
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Address')}:</span> {invoice?.client?.address || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Phone Number')}:</span> {invoice?.client?.phone || '-'}
                            </p>
                            <p>
                                <span className="text-muted-foreground font-medium">{t('Email')}:</span> {invoice?.client?.email || '-'}
                            </p>
                            {invoice?.client?.business_type === 'b2b' && (
                                <>
                                    <p>
                                        <span className="text-muted-foreground font-medium">{t('Tax Number')}:</span> {invoice?.client?.tax_id || '-'}
                                    </p>
                                    <p>
                                        <span className="text-muted-foreground font-medium">{t('Commercial Register')}:</span>{' '}
                                        {invoice?.client?.cr_number || '-'}
                                    </p>
                                </>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Products table */}
                <Card>
                    <CardHeader>
                        <h3 className="text-lg font-semibold">{t('Products')}</h3>
                    </CardHeader>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead className="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            {t('Description')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            {t('Type')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            {t('Quantity')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            {t('Unit Price')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400 whitespace-pre-line">
                                            {t('Subtotal without Tax')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            {t('Tax')}
                                        </th>
                                        <th className="px-6 py-3 text-start text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400 whitespace-pre-line">
                                            {t('Total including Tax')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900/50">
                                    {invoiceItems?.map((item: any, index: number) => {
                                        const { subtotalWithoutTax, tax, total } = getLineAmounts(parseFloat(item.amount || 0));
                                        const isExpense = item.type === 'expense';
                                        const isTime = item.type === 'time';
                                        const typeLabel = isExpense ? t('Expense') : isTime ? t('Time Entry') : t('Item');
                                        const typeClass = isExpense
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                            : isTime
                                              ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                              : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                        return (
                                            <tr key={index}>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    {item.description}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex rounded-full px-2 py-1 text-xs font-medium ${typeClass}`}>
                                                        {typeLabel}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    {item.quantity}
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    <CurrencyAmount amount={parseFloat(item.rate || 0)} className="text-gray-900 dark:text-gray-100" />
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    <CurrencyAmount amount={subtotalWithoutTax} className="text-gray-900 dark:text-gray-100" />
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    <CurrencyAmount amount={tax} className="text-gray-900 dark:text-gray-100" />
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    <CurrencyAmount amount={total} className="text-gray-900 dark:text-gray-100" />
                                                </td>
                                            </tr>
                                        );
                                    })}
                                    {(!invoiceItems || invoiceItems.length === 0) && (
                                        <tr>
                                            <td colSpan={7} className="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                {t('No items found')}
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                {/* Totals */}
                <Card className="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                    <CardContent className="pt-6">
                        <div className="flex justify-end">
                            <div className="w-full max-w-sm space-y-2 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">{t('Subtotal')}</span>
                                    <span className="font-medium"><CurrencyAmount amount={subtotal} className="text-gray-900 dark:text-gray-100" /></span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">{taxRate ? t('Tax Value') + ` (${taxRate}%)` : t('Tax Value')}</span>
                                    <span className="font-medium"><CurrencyAmount amount={taxAmount} className="text-gray-900 dark:text-gray-100" /></span>
                                </div>
                                <div className="border-t border-gray-200 dark:border-gray-700" />
                                <div className="flex justify-between pt-1 text-base font-bold">
                                    <span>{t('Total Invoice (VAT inclusive)')}</span>
                                    <span><CurrencyAmount amount={totalAmount} className="text-gray-900 dark:text-gray-100" /></span>
                                </div>
                                <div className="border-t border-gray-200 dark:border-gray-700" />
                                <div className="flex justify-between pt-2">
                                    <span className="text-muted-foreground">{t('Amount Paid')}</span>
                                    <span className="font-medium"><CurrencyAmount amount={amountPaid} className="text-gray-900 dark:text-gray-100" /></span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">{t('Remaining Amount')}</span>
                                    <span className="font-medium"><CurrencyAmount amount={remainingAmount} className="text-gray-900 dark:text-gray-100" /></span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Terms & Notes */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="pb-2">
                            <h4 className="text-sm font-semibold">{t('Terms')}</h4>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground text-sm">{termsText}</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <h4 className="text-sm font-semibold">{t('Notes')}</h4>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground text-sm">
                                {invoice?.notes || t('Thank you for your business. Please remit payment by due date.')}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>

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
                                { value: String(invoice.id), label: `${invoice.invoice_number || invoice.id} - ${invoice.client?.name || '-'}` },
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
                            conditional: (_mode: string, formData: any) => String(formData?.payment_method || '') === 'bank_transfer',
                        },
                    ],
                    modalSize: 'lg',
                }}
                initialData={{
                    invoice_id: String(invoice.id),
                    amount: remainingAmount,
                    payment_date: new Date().toISOString().split('T')[0],
                    payment_method: 'cash',
                }}
                title={t('Record New Payment')}
                mode="create"
            />
        </PageTemplate>
    );
}
