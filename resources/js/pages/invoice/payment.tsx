import { useState, useEffect } from 'react';
import { usePage, router, Head } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { FileText, Calendar, User, Building2, Clock, Banknote, CreditCard, IndianRupee, Wallet, Coins, Copy, Download, List } from 'lucide-react';
import { toast } from '@/components/custom-toast';
import { PaymentGatewaySelection } from '@/components/payment-gateway-selection';
import { StripePaymentModal } from '@/components/payment-modals/stripe-payment-modal';
import { BankPaymentModal } from '@/components/payment-modals/bank-payment-modal';
import { PayPalPaymentModal } from '@/components/payment-modals/paypal-payment-modal';
import { RazorpayPaymentModal } from '@/components/payment-modals/razorpay-payment-modal';
import { FlutterwavePaymentModal } from '@/components/payment-modals/flutterwave-payment-modal';
import { TapPaymentModal } from '@/components/payment-modals/tap-payment-modal';
import { XenditPaymentModal } from '@/components/payment-modals/xendit-payment-modal';
import { PaystackPaymentModal } from '@/components/payment-modals/paystack-payment-modal';
import { CoinGatePaymentModal } from '@/components/payment-modals/coingate-payment-modal';
import { AamarpayPaymentModal } from '@/components/payment-modals/aamarpay-payment-modal';
import { AuthorizeNetPaymentModal } from '@/components/payment-modals/authorizenet-payment-modal';
import { BenefitPaymentModal } from '@/components/payment-modals/benefit-payment-modal';
import { CashfreePaymentModal } from '@/components/payment-modals/cashfree-payment-modal';
import { CinetPayPaymentModal } from '@/components/payment-modals/cinetpay-payment-modal';
import { EasebuzzPaymentModal } from '@/components/payment-modals/easebuzz-payment-modal';
import { FedaPayPaymentModal } from '@/components/payment-modals/fedapay-payment-modal';
import { IyzipayPaymentModal } from '@/components/payment-modals/iyzipay-payment-modal';
import { KhaltiPaymentModal } from '@/components/payment-modals/khalti-payment-modal';
import { MidtransPaymentModal } from '@/components/payment-modals/midtrans-payment-modal';
import { MolliePaymentModal } from '@/components/payment-modals/mollie-payment-modal';
import { OzowPaymentModal } from '@/components/payment-modals/ozow-payment-modal';
import { PaiementPaymentModal } from '@/components/payment-modals/paiement-payment-modal';
import { PayfastPaymentModal } from '@/components/payment-modals/payfast-payment-modal';
import { PayHerePaymentModal } from '@/components/payment-modals/payhere-payment-modal';
import { PayTabsPaymentModal } from '@/components/payment-modals/paytabs-payment-modal';
import { PayTRPaymentModal } from '@/components/payment-modals/paytr-payment-modal';
import { PaymentWallPaymentModal } from '@/components/payment-modals/paymentwall-payment-modal';

import { SSPayPaymentModal } from '@/components/payment-modals/sspay-payment-modal';
import { ToyyibPayPaymentModal } from '@/components/payment-modals/toyyibpay-payment-modal';
import { YooKassaPaymentModal } from '@/components/payment-modals/yookassa-payment-modal';
import { SkrillPaymentModal } from '@/components/payment-modals/skrill-payment-modal';
import { LanguageSwitcher } from '@/components/language-switcher';

export default function InvoicePayment() {
    const { t, i18n } = useTranslation();
    const { invoice, enabledGateways, remainingAmount, clientBillingInfo, currencies, paypalClientId, flutterwavePublicKey, tapPublicKey, paystackPublicKey, flash, company, companyProfile, companyLogo, favicon, appName } = usePage().props as any;
    const [selectedGateway, setSelectedGateway] = useState<string | null>(null);
    const [showPaymentModal, setShowPaymentModal] = useState(false);
    const [showGatewayModal, setShowGatewayModal] = useState(false);
    const [showSuccessMessage, setShowSuccessMessage] = useState(false);
    const [showCopiedMessage, setShowCopiedMessage] = useState(false);
    const [paymentAmount, setPaymentAmount] = useState(remainingAmount || invoice.total_amount || 0);

    // Payment method icons mapping with Lucide React icons (same as plans)
    const getLogoUrl = (url?: string) => {
        if (!url) return '';
        if (url.startsWith('http')) return url;
        if (url.startsWith('/')) {
            return `${window.appSettings?.imageUrl || ''}${url}`;
        }
        return `${window.appSettings?.imageUrl || ''}/${url}`;
    };

    const getPaymentMethodIcon = (gatewayId: string) => {
        const iconMap = {
            bank_transfer: <Banknote className="h-5 w-5" />,
            stripe: <CreditCard className="h-5 w-5" />,
            paypal: <CreditCard className="h-5 w-5" />,
            razorpay: <IndianRupee className="h-5 w-5" />,
            mercadopago: <Wallet className="h-5 w-5" />,
            paystack: <CreditCard className="h-5 w-5" />,
            flutterwave: <CreditCard className="h-5 w-5" />,
            paytabs: <CreditCard className="h-5 w-5" />,
            skrill: <Wallet className="h-5 w-5" />,
            coingate: <Coins className="h-5 w-5" />,
            payfast: <CreditCard className="h-5 w-5" />,
            tap: <CreditCard className="h-5 w-5" />,
            xendit: <CreditCard className="h-5 w-5" />,
            paytr: <CreditCard className="h-5 w-5" />,
            mollie: <CreditCard className="h-5 w-5" />,
            toyyibpay: <CreditCard className="h-5 w-5" />,
            cashfree: <IndianRupee className="h-5 w-5" />,
            khalti: <CreditCard className="h-5 w-5" />,
            iyzipay: <CreditCard className="h-5 w-5" />,
            benefit: <CreditCard className="h-5 w-5" />,
            ozow: <CreditCard className="h-5 w-5" />,
            easebuzz: <IndianRupee className="h-5 w-5" />,
            authorizenet: <CreditCard className="h-5 w-5" />,
            fedapay: <CreditCard className="h-5 w-5" />,
            payhere: <CreditCard className="h-5 w-5" />,
            cinetpay: <CreditCard className="h-5 w-5" />,
            paiement: <CreditCard className="h-5 w-5" />,
            nepalste: <CreditCard className="h-5 w-5" />,
            yookassa: <CreditCard className="h-5 w-5" />,
            aamarpay: <CreditCard className="h-5 w-5" />,
            midtrans: <CreditCard className="h-5 w-5" />,
            paymentwall: <CreditCard className="h-5 w-5" />,
            sspay: <CreditCard className="h-5 w-5" />
        };
        return iconMap[gatewayId] || <CreditCard className="h-5 w-5" />;
    };

    // Add icons to enabled gateways
    const gatewaysWithIcons = enabledGateways?.map(gateway => ({
        ...gateway,
        icon: getPaymentMethodIcon(gateway.id)
    })) || [];

    // Default to Arabic (RTL) on payment page when no language preference is stored
    useEffect(() => {
        const hasStoredLang = document.cookie.includes('app_language=') || localStorage.getItem('i18nextLng');
        if (!hasStoredLang && i18n.language !== 'ar') {
            i18n.changeLanguage('ar');
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps -- run once on mount
    }, []);

    // Keep document direction and lang in sync for RTL
    useEffect(() => {
        const lng = i18n.language || 'ar';
        const isRtl = ['ar', 'he'].includes(lng);
        document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
        document.documentElement.setAttribute('lang', lng === 'he' ? 'he' : lng === 'ar' ? 'ar' : (lng || 'en'));
    }, [i18n.language]);

    useEffect(() => {
        // Add a small delay to ensure DOM is ready
        const timer = setTimeout(() => {
            if (flash?.success) {
                toast.success(flash.success);
                setShowSuccessMessage(true);
                setTimeout(() => setShowSuccessMessage(false), 5000);
            }
            if (flash?.error) {
                toast.error(flash.error);
            }
        }, 100);

        return () => clearTimeout(timer);
    }, [flash]);

    // Check for payment status changes and show toast
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const paymentSuccess = urlParams.get('payment_success');
        const paymentStatus = urlParams.get('status');

        if (paymentSuccess === 'true' || paymentStatus === 'success') {
            toast.success(t('Payment successful'));
            // Clean URL parameters
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }, []);

    // Monitor invoice status changes
    useEffect(() => {
        const checkPaymentStatus = () => {
            if (invoice?.status === 'paid' && remainingAmount === 0 && !flash?.success) {
                toast.success(t('Payment successful'));
            }
        };

        checkPaymentStatus();
    }, [invoice?.status, remainingAmount, flash?.success]);

    // Get formatted currency using company settings
    const formatAmount = (amount) => {
        if (typeof window !== 'undefined' && window.appSettings?.formatCurrency) {
            const numericAmount = typeof amount === 'number' ? amount : parseFloat(amount);
            return window.appSettings.formatCurrency(numericAmount, { showSymbol: true });
        }
        return `$${parseFloat(amount).toFixed(2)}`;
    };

    const subtotal = Number(invoice?.subtotal ?? 0);
    const taxAmount = Number(invoice?.tax_amount ?? 0);
    const totalAmount = Number(invoice?.total_amount ?? 0);
    const getLineAmounts = (itemAmount: number) => {
        const amt = Number(itemAmount) || 0;
        if (totalAmount <= 0) return { subtotalWithoutTax: amt, tax: 0, total: amt };
        const ratio = amt / totalAmount;
        const subtotalWithoutTax = subtotal * ratio;
        const tax = taxAmount * ratio;
        return { subtotalWithoutTax, tax, total: amt };
    };

    const isOverdue = new Date(invoice.due_date) < new Date();

    const handleGatewaySelect = (gatewayId: string) => {
        try {
            console.log('Selected gateway:', gatewayId);
            setSelectedGateway(gatewayId);
            setShowPaymentModal(true);
        } catch (error) {
            console.error('Error selecting payment gateway:', error);
            toast.error(t('Failed to select payment method. Please try again.'));
        }
    };

    const closeModal = () => {
        try {
            setShowPaymentModal(false);
            setSelectedGateway(null);
        } catch (error) {
            console.error('Error closing modal:', error);
            toast.error(t('An error occurred. Please refresh the page.'));
        }
    };

    const handlePaymentSuccess = () => {
        toast.success(t('Payment successful'));
        closeModal();
        // Reload page after a short delay to show updated status
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    };

    const renderPaymentModal = () => {
        if (!selectedGateway || !showPaymentModal) return null;

        const modalProps = {
            isOpen: showPaymentModal,
            onClose: closeModal,
            onSuccess: handlePaymentSuccess,
            invoice,
            amount: Number(paymentAmount)
        };

        switch (selectedGateway) {
            case 'stripe':
                return <StripePaymentModal {...modalProps} />;
            case 'bank_transfer':
                return <BankPaymentModal {...modalProps} />;
            case 'paypal':
                return <PayPalPaymentModal {...modalProps} paypalClientId={paypalClientId} />;
            case 'razorpay':
                return <RazorpayPaymentModal {...modalProps} />;
            case 'flutterwave':
                return <FlutterwavePaymentModal {...modalProps} flutterwavePublicKey={flutterwavePublicKey} />;
            case 'tap':
                return <TapPaymentModal {...modalProps} tapPublicKey={tapPublicKey} />;
            case 'xendit':
                return <XenditPaymentModal {...modalProps} />;
            case 'paystack':
                return <PaystackPaymentModal
                    isOpen={showPaymentModal}
                    onClose={closeModal}
                    invoice={invoice}
                    amount={Number(paymentAmount)}
                    paystackKey={paystackPublicKey}
                />;
            case 'coingate':
                return <CoinGatePaymentModal {...modalProps} amount={paymentAmount} />;
            case 'aamarpay':
                return <AamarpayPaymentModal {...modalProps} />;
            case 'authorizenet':
                return <AuthorizeNetPaymentModal {...modalProps} />;
            case 'benefit':
                return <BenefitPaymentModal {...modalProps} />;
            case 'cashfree':
                return <CashfreePaymentModal {...modalProps} />;
            case 'cinetpay':
                return <CinetPayPaymentModal {...modalProps} />;
            case 'easebuzz':
                return <EasebuzzPaymentModal {...modalProps} />;
            case 'fedapay':
                return <FedaPayPaymentModal {...modalProps} />;
            case 'iyzipay':
                return <IyzipayPaymentModal {...modalProps} />;
            case 'khalti':
                return <KhaltiPaymentModal {...modalProps} />;
            case 'midtrans':
                return <MidtransPaymentModal {...modalProps} />;
            case 'mollie':
                return <MolliePaymentModal {...modalProps} />;
            case 'ozow':
                return <OzowPaymentModal {...modalProps} />;
            case 'paiement':
                return <PaiementPaymentModal {...modalProps} />;
            case 'payfast':
                return <PayfastPaymentModal {...modalProps} />;
            case 'payhere':
                return <PayHerePaymentModal {...modalProps} />;
            case 'paytabs':
                return (
                    <PayTabsPaymentModal
                        invoiceToken={invoice.payment_token}
                        amount={modalProps.amount}
                        onSuccess={handlePaymentSuccess}
                        onCancel={closeModal}
                    />
                );
            case 'paytr':
                return <PayTRPaymentModal {...modalProps} />;
            case 'paymentwall':
                return <PaymentWallPaymentModal {...modalProps} />;
            case 'skrill':
                return <SkrillPaymentModal {...modalProps} />;
            case 'sspay':
                return <SSPayPaymentModal {...modalProps} />;
            case 'toyyibpay':
                return <ToyyibPayPaymentModal {...modalProps} />;
            case 'yookassa':
                return <YooKassaPaymentModal {...modalProps} />;
            default:
                return null;
        }
    };

    return (
        <>
            <Head title={`${t('Invoice')} - ${company?.name || appName || 'Sard App'}`}>
                {favicon && <link rel="icon" type="image/x-icon" href={favicon} />}
            </Head>
            <div
                className="min-h-screen bg-gray-50"
                dir={i18n.language === 'ar' || i18n.language === 'he' ? 'rtl' : 'ltr'}
                lang={i18n.language === 'ar' ? 'ar' : i18n.language === 'he' ? 'he' : 'en'}
            >
                {/* Header: light gray bar - Invoice Details + Secure gateway | Language + Due pill */}
                <header className="sticky top-0 z-10 border-b border-gray-200 bg-gray-100">
                    <div className="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                        <div className="flex flex-wrap items-center justify-between gap-4">
                            <div className="text-start">
                                <span className="text-lg font-bold text-gray-900 sm:text-xl">{t('Invoice Details')}</span>
                                <span className="ms-1 mt-0.5 text-sm text-gray-600">{t('Secure Payment Portal')}</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <span
                                    className={`inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium ${isOverdue ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-800'}`}
                                >
                                    {isOverdue ? t('Overdue') : t('Due')}{' '}
                                    {(() => {
                                        const d = new Date(invoice.due_date);
                                        return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
                                    })()}
                                </span>
                                <LanguageSwitcher />
                            </div>
                        </div>
                    </div>
                </header>

                <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    {/* White card: status badge, invoice# + case title, then 3 action buttons */}
                    <Card className="mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <CardContent className="p-6">
                            <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
                                <div className="min-w-0 flex-1 text-start">
                                    <h2 className="text-xl font-bold text-gray-900">
                                        {t('Invoice #')} {invoice.invoice_number}
                                    </h2>
                                    <p className="mt-1 text-sm text-gray-600">
                                        {t('Case Title')}: {invoice.case?.title ?? '—'}
                                    </p>
                                </div>
                                <div className="flex items-center gap-3">
                                    <span
                                        className={`inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium text-white ${invoice.status === 'paid' ? 'bg-green-500' : invoice.status === 'partial_paid' ? 'bg-amber-500' : 'bg-gray-400'}`}
                                    >
                                        {invoice.status === 'paid' ? t('Paid') : invoice.status === 'partial_paid' ? t('Partial Paid') : t('Unpaid')}
                                    </span>
                                </div>
                            </div>
                            <div className="flex flex-wrap gap-3">
                                {(invoice.status === 'partial_paid' || (invoice.status !== 'paid' && remainingAmount > 0)) && (
                                    <Button
                                        className="h-10 rounded-lg bg-primary px-4 text-sm font-medium text-white hover:bg-green-700"
                                        onClick={() => {
                                            try {
                                                setShowGatewayModal(true);
                                            } catch (error) {
                                                console.error('Error opening payment modal:', error);
                                                toast.error(t('Failed to open payment options. Please try again.'));
                                            }
                                        }}
                                    >
                                        <CreditCard className="me-2 h-4 w-4" />
                                        {t('Pay Invoice Now')}
                                    </Button>
                                )}
                                <Button
                                    variant="outline"
                                    className="h-10 rounded-lg border-gray-300 px-4 text-sm font-medium"
                                    onClick={async () => {
                                        try {
                                            await navigator.clipboard.writeText(window.location.href);
                                            setShowCopiedMessage(true);
                                            setTimeout(() => setShowCopiedMessage(false), 2000);
                                            toast.success(t('Link copied to clipboard!'));
                                        } catch (error) {
                                            console.error('Error copying link:', error);
                                            toast.error(t('Failed to copy link. Please try again.'));
                                        }
                                    }}
                                >
                                    {showCopiedMessage ? (
                                        <span className="flex items-center text-green-600">✓ {t('Copied!')}</span>
                                    ) : (
                                        <>
                                            <Copy className="me-2 h-4 w-4" />
                                            {t('Copy Link')}
                                        </>
                                    )}
                                </Button>
                                <Button
                                    variant="outline"
                                    className="h-10 rounded-lg border-gray-300 px-4 text-sm font-medium"
                                    onClick={() => {
                                        const pdfType = invoice.client?.business_type === 'b2b' ? 'tax' : 'simplified';
                                        window.open(route('invoices.pdf', invoice.id) + `?type=${pdfType}`, '_blank');
                                    }}
                                >
                                    <Download className="me-2 h-4 w-4" />
                                    {invoice.client?.business_type === 'b2b' ? t('Download Tax Invoice') : t('Download Simplified Tax Invoice')}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    {/* State cards: 2 rows × 3 cols — Total, Paid, Due | Due Date, Invoice Date, Products */}
                    <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <Wallet className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Total Amount')}</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{formatAmount(invoice.total_amount)}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <Wallet className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Paid Amount')}</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{formatAmount((invoice.total_amount ?? 0) - (remainingAmount ?? 0))}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <Wallet className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Due Amount')}</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{formatAmount(remainingAmount)}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <Calendar className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Due Date')}:</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{new Date(invoice.due_date).toLocaleDateString('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' })}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <Calendar className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Invoice Date')}:</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{new Date(invoice.invoice_date).toLocaleDateString('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' })}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border border-[#F1F1F4] bg-white shadow-sm">
                            <CardContent className="p-6">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                        <List className="h-5 w-5 text-gray-400" strokeWidth={1.5} />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-medium text-gray-500">{t('Products')}</p>
                                        <p className="mt-1 text-xl font-bold text-gray-900">{invoice.line_items?.length ?? 0}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="grid grid-cols-1 gap-8 xl:grid-cols-1">
                        {/* Invoice Details - Left Side */}
                        <div className="space-y-6 xl:col-span-3">
                            {/* Company (Bill From) and Client (Bill To) — two white cards */}
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <Card className="overflow-hidden rounded-xl border border-[#F1F1F4] bg-white shadow-sm">
                                    <CardContent className="p-6">
                                        <div className="flex items-center gap-2">
                                            <Building2 className="h-5 w-5 text-gray-500" />
                                            <p className="text-sm font-medium text-gray-500">{t('Bill From')}</p>
                                        </div>
                                        <p className="mt-2 text-lg font-bold text-gray-900">
                                            {companyProfile?.name || company?.name || appName}
                                        </p>
                                        <dl className="mt-4 space-y-2 text-sm">
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('CR Number')}:</dt>
                                                <dd className="text-gray-600">{companyProfile?.cr || companyProfile?.registration_number || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Tax ID')}:</dt>
                                                <dd className="text-gray-600">{companyProfile?.tax_id || companyProfile?.tax_number || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Address')}:</dt>
                                                <dd className="text-gray-600">{companyProfile?.address || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Phone')}:</dt>
                                                <dd className="text-gray-600">{companyProfile?.phone || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Email')}:</dt>
                                                <dd className="text-gray-600">{companyProfile?.email || '-'}</dd>
                                            </div>
                                        </dl>
                                    </CardContent>
                                </Card>

                                <Card className="overflow-hidden rounded-xl border border-[#F1F1F4] bg-white shadow-sm">
                                    <CardContent className="p-6">
                                        <div className="flex items-center gap-2">
                                            <User className="h-5 w-5 text-gray-500" />
                                            <p className="text-sm font-medium text-gray-500">{t('Bill To')}</p>
                                        </div>
                                        <p className="mt-2 text-lg font-bold text-gray-900">{invoice.client?.name || '-'}</p>
                                        <dl className="mt-4 space-y-2 text-sm">
                                            {invoice.client?.business_type === 'b2b' && (
                                                <>
                                                    <div className="flex flex-wrap gap-x-2">
                                                        <dt className="font-semibold text-gray-700">{t('CR Number')}:</dt>
                                                        <dd className="text-gray-600">{invoice.client?.cr_number || '-'}</dd>
                                                    </div>
                                                    <div className="flex flex-wrap gap-x-2">
                                                        <dt className="font-semibold text-gray-700">{t('Tax ID')}:</dt>
                                                        <dd className="text-gray-600">{invoice.client?.tax_id || '-'}</dd>
                                                    </div>
                                                </>
                                            )}
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Address')}:</dt>
                                                <dd className="text-gray-600">{invoice.client?.address || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Phone')}:</dt>
                                                <dd className="text-gray-600">{invoice.client?.phone || '-'}</dd>
                                            </div>
                                            <div className="flex flex-wrap gap-x-2">
                                                <dt className="font-semibold text-gray-700">{t('Email')}:</dt>
                                                <dd className="text-gray-600">{invoice.client?.email || '-'}</dd>
                                            </div>
                                            {invoice.case && (
                                                <div className="flex flex-wrap gap-x-2">
                                                    <dt className="font-semibold text-gray-700">{t('Case Title')}:</dt>
                                                    <dd className="text-gray-600">{invoice.case.title}</dd>
                                                </div>
                                            )}
                                        </dl>
                                    </CardContent>
                                </Card>
                            </div>

                            {/* Products table and summary (same structure as billing/invoices/show) */}
                            {invoice.line_items && invoice.line_items.length > 0 && (
                                <Card className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                    <CardHeader className="px-6 pb-0">
                                        <h3 className="text-lg font-semibold">{t('Products')}</h3>
                                    </CardHeader>
                                    <CardContent className="p-0">
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700" dir={i18n.language === 'ar' ? 'rtl' : 'ltr'}>
                                                <thead className="bg-gray-50 dark:bg-gray-800/50">
                                                    <tr>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{t('Description')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{t('Type')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{t('Quantity')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{t('Unit Price')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 whitespace-pre-line">{t('Subtotal without Tax')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{t('Tax')}</th>
                                                        <th className="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 whitespace-pre-line">{t('Total including Tax')}</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900/50">
                                                    {invoice.line_items.map((item: any, index: number) => {
                                                        const { subtotalWithoutTax, tax, total } = getLineAmounts(parseFloat(item.amount || 0));
                                                        const isExpense = item.type === 'expense';
                                                        const isTime = item.type === 'time';
                                                        const typeLabel = isExpense ? t('Expense') : isTime ? t('Time Entry') : t('Item');
                                                        const typeClass = isExpense ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : isTime ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                                        return (
                                                            <tr key={index}>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{item.description}</td>
                                                                <td className="whitespace-nowrap px-6 py-4">
                                                                    <span className={`inline-flex rounded-full px-2 py-1 text-xs font-medium ${typeClass}`}>
                                                                        {typeLabel}
                                                                    </span>
                                                                </td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{item.quantity}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{formatAmount(parseFloat(item.rate || 0))}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{formatAmount(subtotalWithoutTax)}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{formatAmount(tax)}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{formatAmount(total)}</td>
                                                            </tr>
                                                        );
                                                    })}
                                                </tbody>
                                            </table>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {/* Totals (same structure as billing/invoices/show) */}
                            <Card>
                                <CardContent className="pt-6">
                                    <div className="flex justify-end">
                                        <div className="w-full max-w-sm space-y-2 text-sm">
                                            <div className="flex justify-between">
                                                <span className="text-muted-foreground">{t('Subtotal')}:</span>
                                                <span className="font-medium">{formatAmount(invoice?.subtotal ?? 0)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-muted-foreground">{t('Tax Value')}:</span>
                                                <span className="font-medium">{formatAmount(invoice?.tax_amount ?? 0)}</span>
                                            </div>
                                            <div className="flex justify-between border-t pt-3 text-lg font-bold">
                                                <span>{t('Total')}:</span>
                                                <span>{formatAmount(invoice?.total_amount ?? 0)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Additional Information */}
                            <Card className="border-0 shadow-sm">
                                <CardContent className="p-6">
                                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                                        <div>
                                            <h4 className="mb-2 text-sm font-semibold text-gray-700">{t('Notes')}</h4>
                                            <p className="text-sm text-gray-600">
                                                {invoice.notes || t('Thank you for your business. Please remit payment by due date.')}
                                            </p>
                                        </div>
                                        <div>
                                            <h4 className="mb-2 text-sm font-semibold text-gray-700">{t('Terms')}</h4>
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
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>

                {/* Payment Gateway Selection Modal */}
                <Dialog open={showGatewayModal} onOpenChange={setShowGatewayModal}>
                    <DialogContent className="max-h-[80vh] max-w-md">
                        <DialogHeader>
                            <DialogTitle className="text-center">
                                {t('Pay Invoice')} #{invoice.invoice_number}
                            </DialogTitle>
                        </DialogHeader>

                        <div className="space-y-4">
                            <div className="rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-blue-700">
                                        {t('Invoice')} #{invoice.invoice_number}
                                    </span>
                                    <span className="font-bold text-blue-900">{formatAmount(invoice.total_amount)}</span>
                                </div>
                                <div className="mt-1 text-xs text-blue-600">{invoice.client?.name}</div>
                                <div className="mt-1 text-xs text-blue-600">
                                    {t('Remaining')}: {formatAmount(remainingAmount)}
                                </div>
                            </div>

                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-700">{t('Payment Amount')}</label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    max={remainingAmount}
                                    value={paymentAmount}
                                    onChange={(e) => setPaymentAmount(Number(e.target.value))}
                                    placeholder={t('Enter amount to pay')}
                                    className="w-full"
                                />
                                <div className="mt-2 flex gap-2">
                                    <Button variant="outline" size="sm" onClick={() => setPaymentAmount(Math.round((remainingAmount / 2) * 100) / 100)}>
                                        50%
                                    </Button>
                                    <Button variant="outline" size="sm" onClick={() => setPaymentAmount(Math.round(remainingAmount * 100) / 100)}>
                                        {t('Full Amount')}
                                    </Button>
                                </div>
                            </div>

                            <div>
                                <label className="mb-3 block text-sm font-medium text-gray-700">{t('Select Payment Method')}</label>
                                <div className="max-h-64 space-y-3 overflow-y-auto">
                                    {gatewaysWithIcons.map((gateway) => (
                                        <div
                                            key={gateway.id}
                                            className={`flex cursor-pointer items-center rounded-lg border p-4 transition-all ${
                                                selectedGateway === gateway.id
                                                    ? 'border-blue-500 bg-blue-50'
                                                    : 'border-gray-200 hover:border-gray-300'
                                            }`}
                                            onClick={() => {
                                                try {
                                                    setSelectedGateway(gateway.id);
                                                } catch (error) {
                                                    console.error('Error selecting gateway:', error);
                                                    toast.error(t('Failed to select payment method. Please try again.'));
                                                }
                                            }}
                                        >
                                            <div className="text-primary mx-3">{gateway.icon}</div>
                                            <span className="text-sm font-medium text-gray-900">{gateway.name}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="flex gap-3 pt-4">
                                <Button
                                    variant="outline"
                                    className="h-12 flex-1 border-gray-800 bg-gray-800 text-white hover:bg-gray-700"
                                    onClick={() => {
                                        try {
                                            setShowGatewayModal(false);
                                        } catch (error) {
                                            console.error('Error closing modal:', error);
                                            toast.error(t('An error occurred. Please refresh the page.'));
                                        }
                                    }}
                                >
                                    {t('Cancel')}
                                </Button>
                                <Button
                                    className="h-12 flex-1 bg-primary text-white hover:bg-primary/90"
                                    onClick={() => {
                                        try {
                                            if (!selectedGateway) {
                                                toast.error(t('Please select a payment method first.'));
                                                return;
                                            }
                                            if (!paymentAmount || paymentAmount <= 0) {
                                                toast.error(t('Please enter a valid payment amount.'));
                                                return;
                                            }
                                            if (paymentAmount > remainingAmount) {
                                                toast.error(t('Payment amount cannot exceed remaining balance.'));
                                                return;
                                            }
                                            setShowGatewayModal(false);
                                            setShowPaymentModal(true);
                                        } catch (error) {
                                            console.error('Error processing payment:', error);
                                            toast.error(t('Failed to process payment. Please try again.'));
                                        }
                                    }}
                                    disabled={!selectedGateway || !paymentAmount || paymentAmount <= 0}
                                >
                                    {t('Pay')} {formatAmount(paymentAmount)}
                                </Button>
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>

                {/* Payment Modals */}
                {renderPaymentModal()}
            </div>
        </>
    );
}
