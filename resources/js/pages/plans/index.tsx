import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { CurrencyAmount } from '@/components/currency-amount';
import { toast } from '@/components/custom-toast';
import { PageTemplate } from '@/components/page-template';
import { PlanSubscriptionModal } from '@/components/plan-subscription-modal';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { router, usePage } from '@inertiajs/react';
import {
    Banknote,
    BarChart2,
    Bot,
    Box,
    CheckCircle2,
    Clock,
    Coins,
    CreditCard,
    Crown,
    FileText,
    Globe,
    HardDrive,
    IndianRupee,
    Mail,
    Pencil,
    Plus,
    Scale,
    Sparkles,
    Target,
    Trash2,
    Users,
    Wallet,
    X,
    Zap,
} from 'lucide-react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

function resolveTranslatable(
    val: string | Record<string, string> | undefined | null,
    locale: string
): string {
    if (val == null) return '';
    if (typeof val === 'string') return val;
    const o = val as Record<string, string>;
    if (Object.keys(o).length === 0) return '';
    const lang = locale.startsWith('ar') ? 'ar' : 'en';
    return o[lang] || o.en || o.ar || Object.values(o)[0] || '';
}

function daysFromToday(dateStr: string | null | undefined): number | null {
    if (!dateStr) return null;
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);
    return Math.floor((date.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
}

interface Plan {
    id: number;
    name: string;
    name_translations?: Record<string, string>;
    price: string | number;
    formatted_price?: string;
    duration: string;
    billing_cycle?: 'monthly' | 'yearly' | 'both';
    description: string;
    description_translations?: Record<string, string>;
    trial_days: number;
    features: string[];
    stats: {
        businesses: number | string;
        users: number | string;
        storage: string;
        templates: number | string;
    };
    status: boolean;
    recommended?: boolean;
    is_default?: boolean;
    is_current?: boolean;
    is_trial_available?: boolean;
    has_pending_request?: boolean;
    has_users?: boolean;
}

interface PlanStatusData {
    name: string;
    name_translations?: Record<string, string>;
    usage: {
        team_members: { used: number; limit: number };
        storage: { used_gb: number; limit_gb: number };
        cases: { used: number; limit: number };
        clients: { used: number; limit: number };
    };
    plan_details: {
        team_members: number;
        cases: number;
        clients: number;
        storage_gb: number;
    };
    price_monthly: number | string;
    formatted_price_monthly: string;
    plan_expire_date?: string | null;
    trial_expire_date?: string | null;
    is_trial?: boolean;
}

interface Props {
    plans: Plan[];
    billingCycle: 'monthly' | 'yearly';
    hasDefaultPlan?: boolean;
    hasMonthlyPlans?: boolean;
    hasYearlyPlans?: boolean;
    isAdmin?: boolean;
    currentPlan?: any;
    planStatus?: PlanStatusData | null;
    userTrialUsed?: boolean;
    paymentMethods?: any[];
    pendingRequests?: any;
}

export default function Plans({
    plans: initialPlans,
    billingCycle: initialBillingCycle = 'monthly',
    hasDefaultPlan,
    hasMonthlyPlans = true,
    hasYearlyPlans = true,
    isAdmin = false,
    currentPlan,
    planStatus = null,
    userTrialUsed,
    paymentMethods = [],
    pendingRequests = {},
}: Props) {
    const { t, i18n } = useTranslation();
    const currentLocale = i18n.language?.startsWith('ar') ? 'ar' : 'en';
    const isRtl = currentLocale === 'ar';
    const valueDirProps = isRtl ? { dir: 'ltr' as const } : {};
    const { flash } = usePage().props as any;
    const [plans, setPlans] = useState<Plan[]>(initialPlans);
    const [billingCycle, setBillingCycle] = useState<'monthly' | 'yearly'>(initialBillingCycle);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [planToDelete, setPlanToDelete] = useState<Plan | null>(null);
    const [isSubscriptionModalOpen, setIsSubscriptionModalOpen] = useState(false);
    const [selectedPlan, setSelectedPlan] = useState<Plan | null>(null);

    const [processing, setProcessing] = useState(false);

    // Update plans when initialPlans changes
    useEffect(() => {
        setPlans(initialPlans);
    }, [initialPlans]);

    const isUnlimited = (value: number | string) => {
        if (value === null || value === undefined) return false;
        const numeric = typeof value === 'string' ? parseFloat(value) : value;
        return numeric === -1;
    };

    const planStatusProgress = (used: number, limit: number) => {
        if (limit === -1 || limit === 0) return 0;
        const p = Math.min(100, (used / limit) * 100);
        return Math.round(p);
    };

    const formatLimitValue = (value: number | string, unit?: string) => {
        if (value === null || value === undefined) return '-';
        if (isUnlimited(value)) return t('Unlimited');
        if (!unit) return value;
        if (typeof value === 'string' && /[a-zA-Z]/.test(value)) return value;
        return `${value} ${unit}`;
    };

    useEffect(() => {
        if (billingCycle === 'monthly' && !hasMonthlyPlans && hasYearlyPlans) {
            handleBillingCycleChange('yearly');
        }
        if (billingCycle === 'yearly' && !hasYearlyPlans && hasMonthlyPlans) {
            handleBillingCycleChange('monthly');
        }
    }, [billingCycle, hasMonthlyPlans, hasYearlyPlans]);

    // Show flash messages
    useEffect(() => {
        if (flash?.error) {
            toast.error(flash.error);
        }
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [flash]);

    // Function to handle billing cycle change
    const handleBillingCycleChange = (value: 'monthly' | 'yearly') => {
        setBillingCycle(value);
        router.get(route('plans.index'), { billing_cycle: value }, { preserveState: true });
    };

    // Company plan actions
    const handlePlanRequest = (planId: number) => {
        setProcessing(true);

        router.post(
            route('plans.request'),
            {
                plan_id: planId,
                billing_cycle: billingCycle,
            },
            {
                onSuccess: () => {
                    setProcessing(false);
                },
                onError: () => {
                    setProcessing(false);
                },
            },
        );
    };

    const handleCancelRequest = (planId: number) => {
        const request = pendingRequests[planId];
        if (!request) return;

        setProcessing(true);

        router.post(
            route('plan-requests.cancel', request.id),
            {},
            {
                onSuccess: () => {
                    setProcessing(false);
                },
                onError: () => {
                    setProcessing(false);
                },
            },
        );
    };

    const handleStartTrial = (planId: number) => {
        setProcessing(true);

        router.post(
            route('plans.trial'),
            {
                plan_id: planId,
            },
            {
                onSuccess: () => {
                    setProcessing(false);
                    toast.success(t('Trial started successfully'));
                },
                onError: () => {
                    setProcessing(false);
                },
            },
        );
    };

    const planPriceForCurrentCycle = (plan: Plan) => {
        const p = typeof plan.price === 'string' ? parseFloat(plan.price) : plan.price;
        return Number.isNaN(p) ? 0 : p;
    };

    const handleSubscribe = async (planId: number) => {
        const plan = plans.find((p) => p.id === planId);
        if (!plan) return;
        const price = planPriceForCurrentCycle(plan);
        if (price === 0) {
            setProcessing(true);
            router.post(route('plans.subscribe-free'), { plan_id: planId, billing_cycle: billingCycle }, {
                onSuccess: () => {
                    setProcessing(false);
                    toast.success(t('Plan activated successfully!'));
                },
                onError: () => setProcessing(false),
            });
            return;
        }
        try {
            const url = `${route('payment.methods')}?saas=1`;
            const response = await fetch(url);
            const paymentSettings = await response.json();
            setSelectedPlan({ ...plan, paymentMethods: paymentSettings });
            setIsSubscriptionModalOpen(true);
        } catch {
            toast.error(t('Failed to load payment methods'));
        }
    };

    const formatPaymentMethods = (paymentSettings: any) => {
        const methods = [];

        if (paymentSettings?.bank_transfer_enabled === true || paymentSettings?.bank_transfer_enabled === '1') {
            methods.push({
                id: 'bank_transfer',
                name: t('Bank Transfer'),
                icon: <Banknote className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.stripe_enabled === true || paymentSettings?.stripe_enabled === '1') {
            methods.push({
                id: 'stripe',
                name: t('Stripe'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paypal_enabled === true || paymentSettings?.paypal_enabled === '1') {
            methods.push({
                id: 'paypal',
                name: t('PayPal'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.razorpay_enabled === true || paymentSettings?.razorpay_enabled === '1') {
            methods.push({
                id: 'razorpay',
                name: t('Razorpay'),
                icon: <IndianRupee className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (
            (paymentSettings?.mercadopago_enabled === true || paymentSettings?.mercadopago_enabled === '1') &&
            paymentSettings?.mercadopago_access_token
        ) {
            methods.push({
                id: 'mercadopago',
                name: t('MercadoPago'),
                icon: <Wallet className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paystack_enabled === true || paymentSettings?.paystack_enabled === '1') {
            methods.push({
                id: 'paystack',
                name: t('Paystack'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.flutterwave_enabled === true || paymentSettings?.flutterwave_enabled === '1') {
            methods.push({
                id: 'flutterwave',
                name: t('Flutterwave'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paytabs_enabled === true || paymentSettings?.paytabs_enabled === '1') {
            methods.push({
                id: 'paytabs',
                name: t('PayTabs'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.skrill_enabled === true || paymentSettings?.skrill_enabled === '1') {
            methods.push({
                id: 'skrill',
                name: t('Skrill'),
                icon: <Wallet className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.coingate_enabled === true || paymentSettings?.coingate_enabled === '1') {
            methods.push({
                id: 'coingate',
                name: t('CoinGate'),
                icon: <Coins className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.payfast_enabled === true || paymentSettings?.payfast_enabled === '1') {
            methods.push({
                id: 'payfast',
                name: t('Payfast'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.tap_enabled === true || paymentSettings?.tap_enabled === '1') {
            methods.push({
                id: 'tap',
                name: t('Tap'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.xendit_enabled === true || paymentSettings?.xendit_enabled === '1') {
            methods.push({
                id: 'xendit',
                name: t('Xendit'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paytr_enabled === true || paymentSettings?.paytr_enabled === '1') {
            methods.push({
                id: 'paytr',
                name: t('PayTR'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.mollie_enabled === true || paymentSettings?.mollie_enabled === '1') {
            methods.push({
                id: 'mollie',
                name: t('Mollie'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.toyyibpay_enabled === true || paymentSettings?.toyyibpay_enabled === '1') {
            methods.push({
                id: 'toyyibpay',
                name: t('toyyibPay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.cashfree_enabled === true || paymentSettings?.cashfree_enabled === '1') {
            methods.push({
                id: 'cashfree',
                name: t('Cashfree'),
                icon: <IndianRupee className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.khalti_enabled === true || paymentSettings?.khalti_enabled === '1') {
            methods.push({
                id: 'khalti',
                name: t('Khalti'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.iyzipay_enabled === true || paymentSettings?.iyzipay_enabled === '1') {
            methods.push({
                id: 'iyzipay',
                name: t('Iyzipay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.benefit_enabled === true || paymentSettings?.benefit_enabled === '1') {
            methods.push({
                id: 'benefit',
                name: t('Benefit'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.ozow_enabled === true || paymentSettings?.ozow_enabled === '1') {
            methods.push({
                id: 'ozow',
                name: t('Ozow'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.easebuzz_enabled === true || paymentSettings?.easebuzz_enabled === '1') {
            methods.push({
                id: 'easebuzz',
                name: t('Easebuzz'),
                icon: <IndianRupee className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.authorizenet_enabled === true || paymentSettings?.authorizenet_enabled === '1') {
            methods.push({
                id: 'authorizenet',
                name: t('AuthorizeNet'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.fedapay_enabled === true || paymentSettings?.fedapay_enabled === '1') {
            methods.push({
                id: 'fedapay',
                name: t('FedaPay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.payhere_enabled === true || paymentSettings?.payhere_enabled === '1') {
            methods.push({
                id: 'payhere',
                name: t('PayHere'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.cinetpay_enabled === true || paymentSettings?.cinetpay_enabled === '1') {
            methods.push({
                id: 'cinetpay',
                name: t('CinetPay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paiement_enabled === true || paymentSettings?.paiement_enabled === '1') {
            methods.push({
                id: 'paiement',
                name: t('Paiement Pro'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.nepalste_enabled === true || paymentSettings?.nepalste_enabled === '1') {
            methods.push({
                id: 'nepalste',
                name: t('Nepalste'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.yookassa_enabled === true || paymentSettings?.yookassa_enabled === '1') {
            methods.push({
                id: 'yookassa',
                name: t('YooKassa'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.aamarpay_enabled === true || paymentSettings?.aamarpay_enabled === '1') {
            methods.push({
                id: 'aamarpay',
                name: t('Aamarpay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.midtrans_enabled === true || paymentSettings?.midtrans_enabled === '1') {
            methods.push({
                id: 'midtrans',
                name: t('Midtrans'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.paymentwall_enabled === true || paymentSettings?.paymentwall_enabled === '1') {
            methods.push({
                id: 'paymentwall',
                name: t('PaymentWall'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        if (paymentSettings?.sspay_enabled === true || paymentSettings?.sspay_enabled === '1') {
            methods.push({
                id: 'sspay',
                name: t('SSPay'),
                icon: <CreditCard className="h-5 w-5" />,
                enabled: true,
            });
        }

        return methods;
    };

    const getActionButton = (plan: Plan) => {
        // Check if user has active subscription to this plan
        if (currentPlan && currentPlan.id === plan.id && currentPlan.expires_at && new Date(currentPlan.expires_at) > new Date()) {
            return (
                <Button disabled className="w-full border-green-200 bg-green-100 text-green-800">
                    <Crown className="mr-2 h-4 w-4" />
                    {t('Already Subscribed')}
                </Button>
            );
        }

        if (plan.is_current) {
            return (
                <Button disabled className="w-full">
                    <Crown className="mr-2 h-4 w-4" />
                    {t('Current Plan')}
                </Button>
            );
        }

        return (
            <div className="space-y-2">
                {plan.is_trial_available && !userTrialUsed && (
                    <Button onClick={() => handleStartTrial(plan.id)} disabled={processing} variant="outline" className="w-full">
                        <Zap className="mr-2 h-4 w-4" />
                        {t('Start {{days}} Day Trial', { days: plan.trial_days })}
                    </Button>
                )}
                {/*{plan.has_pending_request ? (*/}
                {/*    <Button*/}
                {/*        onClick={() => handleCancelRequest(plan.id)}*/}
                {/*        disabled={processing}*/}
                {/*        variant="outline"*/}
                {/*        className="w-full border-red-200 text-red-600 hover:bg-red-50"*/}
                {/*    >*/}
                {/*        <X className="mr-2 h-4 w-4" />*/}
                {/*        {t('Cancel Request')}*/}
                {/*    </Button>*/}
                {/*) : (*/}
                {/*    <Button onClick={() => handlePlanRequest(plan.id)} disabled={processing} variant="outline" className="w-full">*/}
                {/*        <Clock className="mr-2 h-4 w-4" />*/}
                {/*        {t('Request Plan')}*/}
                {/*    </Button>*/}
                {/*)}*/}
                <Button onClick={() => handleSubscribe(plan.id)} disabled={processing || plan.has_pending_order} className="w-full">
                    {plan.has_pending_order ? t('Subscription Pending') : t('Subscribe Now')}
                </Button>
            </div>
        );
    };

    // Function to get the appropriate icon for a feature
    const getFeatureIcon = (feature: string) => {
        switch (feature) {
            case 'Custom Domain':
                return <Globe className="h-4 w-4" />;
            case 'Subdomain':
                return <Globe className="h-4 w-4" />;
            case 'PWA':
                return <FileText className="h-4 w-4" />;
            case 'Blog Module':
                return <FileText className="h-4 w-4" />;
            case 'AI Integration':
                return <Bot className="h-4 w-4" />;
            case 'Analytics':
                return <BarChart2 className="h-4 w-4" />;
            case 'Email Support':
                return <Mail className="h-4 w-4" />;
            case 'API Access':
                return <Box className="h-4 w-4" />;
            case 'Priority Support':
                return <Users className="h-4 w-4" />;
            case 'Storage':
                return <HardDrive className="h-4 w-4" />;
            default:
                return <CheckCircle2 className="h-4 w-4" />;
        }
    };

    // Function to check if a feature is included in the plan
    const isFeatureIncluded = (plan: Plan, feature: string) => {
        return plan.features.includes(feature);
    };

    // Function to toggle plan status
    const togglePlanStatus = (planId: number) => {
        // Send request to toggle plan status
        router.post(
            route('plans.toggle-status', planId),
            {},
            {
                preserveState: true,
                onSuccess: () => {
                    // Update local state
                    setPlans(plans.map((plan) => (plan.id === planId ? { ...plan, status: !plan.status } : plan)));
                },
            },
        );
    };

    // Function to handle delete
    const handleDelete = (plan: Plan) => {
        setPlanToDelete(plan);
        setIsDeleteModalOpen(true);
    };

    // Function to handle delete confirmation
    const handleDeleteConfirm = () => {
        if (planToDelete) {
            router.delete(route('plans.destroy', planToDelete.id), {
                onSuccess: () => {
                    setIsDeleteModalOpen(false);
                    setPlanToDelete(null);
                },
            });
        }
    };

    // Common features to display for all plans
    const commonFeatures = [
        // 'AI Integration'
    ];

    // Define stat icons
    const statIcons = {
        users: <Users className="h-5 w-5" />,
        cases: <Scale className="h-5 w-5" />,
        clients: <Users className="h-5 w-5" />,
        storage: <HardDrive className="h-5 w-5" />,
    };

    const showPlanStatusRow = !isAdmin && planStatus;
    const plansGridCols = showPlanStatusRow ? 'md:grid-cols-2 lg:grid-cols-3' : 'md:grid-cols-2 lg:grid-cols-4';

    return (
        <PageTemplate title={t('Plans')} description={t('Manage subscription plans for your customers')} url="/plans">
            <div className="space-y-8">
                {/* Header with controls */}
                <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="mb-2 text-3xl font-bold tracking-tight">{isAdmin ? t('Subscription Plans') : t('Choose Your Plan')}</h1>
                        <p className="text-muted-foreground max-w-2xl">
                            {isAdmin
                                ? t('Create and manage subscription plans to offer different service tiers to your customers.')
                                : t('Select the perfect plan for your business needs')}
                        </p>
                    </div>
                    <div className="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                        {hasMonthlyPlans && hasYearlyPlans && (
                            <Tabs
                                value={billingCycle}
                                onValueChange={(v) => handleBillingCycleChange(v as 'monthly' | 'yearly')}
                                className="w-full sm:w-[400px]"
                            >
                                <TabsList className="grid w-full grid-cols-2">
                                    <TabsTrigger value="monthly">{t('Monthly')}</TabsTrigger>
                                    <TabsTrigger value="yearly">{t('Yearly')}</TabsTrigger>
                                </TabsList>
                            </Tabs>
                        )}
                        {isAdmin && (
                            <Button className="w-full sm:w-auto" onClick={() => router.get(route('plans.create'))}>
                                <Plus className="mr-2 h-4 w-4" />
                                {t('Add Plan')}
                            </Button>
                        )}
                    </div>
                </div>

                {/* Plan Status + Plans in same row (company) or plans only (admin) */}
                <div className={showPlanStatusRow ? 'grid grid-cols-1 items-start gap-8 xl:grid-cols-[minmax(300px,380px)_1fr]' : ''}>
                    {showPlanStatusRow && (
                        <Card className="overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-sm xl:sticky xl:top-8">
                            <CardHeader className="pb-4">
                                <div className="flex flex-wrap items-center justify-between gap-4">
                                    <div className="flex items-center gap-2">
                                        <div className="bg-primary/10 text-primary flex h-9 w-9 items-center justify-center rounded-lg">
                                            <Target className="h-5 w-5" />
                                        </div>
                                        <h2 className="text-lg font-semibold tracking-tight">{t('Plan Status')}</h2>
                                    </div>
                                    <Badge variant="secondary" className="rounded-md bg-gray-100 px-2.5 py-1 text-sm font-medium text-gray-700">
                                        {resolveTranslatable(planStatus.name_translations, currentLocale) || planStatus.name}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div className="space-y-4">
                                    <div>
                                        <div className="mb-1.5 flex items-center justify-between text-sm">
                                            <span className="font-medium text-gray-700">{t('Team Members')}</span>
                                            <span className="text-muted-foreground" {...valueDirProps}>
                                                {planStatus.usage.team_members.limit === -1
                                                    ? isRtl
                                                        ? `${t('Unlimited')} / ${planStatus.usage.team_members.used}`
                                                        : `${planStatus.usage.team_members.used} / ${t('Unlimited')}`
                                                    : isRtl
                                                        ? `${planStatus.usage.team_members.limit} / ${planStatus.usage.team_members.used}`
                                                        : `${planStatus.usage.team_members.used} / ${planStatus.usage.team_members.limit}`}
                                            </span>
                                        </div>
                                        <Progress
                                            value={planStatusProgress(planStatus.usage.team_members.used, planStatus.usage.team_members.limit)}
                                            className="h-2 bg-gray-100 [&>div]:bg-emerald-500"
                                        />
                                    </div>
                                    <div>
                                        <div className="mb-1.5 flex items-center justify-between text-sm">
                                            <span className="font-medium text-gray-700">{t('Storage')}</span>
                                            <span className="text-muted-foreground" {...valueDirProps}>
                                                {planStatus.usage.storage.limit_gb === -1
                                                    ? isRtl
                                                        ? `${t('Unlimited')} / ${planStatus.usage.storage.used_gb} GB`
                                                        : `${planStatus.usage.storage.used_gb} GB / ${t('Unlimited')}`
                                                    : isRtl
                                                        ? `${planStatus.usage.storage.limit_gb} GB / ${planStatus.usage.storage.used_gb} GB`
                                                        : `${planStatus.usage.storage.used_gb} GB / ${planStatus.usage.storage.limit_gb} GB`}
                                            </span>
                                        </div>
                                        <Progress
                                            value={planStatusProgress(planStatus.usage.storage.used_gb, planStatus.usage.storage.limit_gb)}
                                            className="h-2 bg-gray-100 [&>div]:bg-emerald-500"
                                        />
                                    </div>
                                    <div>
                                        <div className="mb-1.5 flex items-center justify-between text-sm">
                                            <span className="font-medium text-gray-700">{t('Cases')}</span>
                                            <span className="text-muted-foreground" {...valueDirProps}>
                                                {isRtl ? (
                                                    <>
                                                        <span dir={planStatus.usage.cases.limit === -1 ? 'rtl' : 'ltr'}>
                                                            {planStatus.usage.cases.limit === -1 ? t('Unlimited') : planStatus.usage.cases.limit}
                                                        </span>
                                                        <span dir="ltr"> / {planStatus.usage.cases.used}</span>
                                                    </>
                                                ) : planStatus.usage.cases.limit === -1
                                                    ? `${planStatus.usage.cases.used} / ${t('Unlimited')}`
                                                    : `${planStatus.usage.cases.used} / ${planStatus.usage.cases.limit}`}
                                            </span>
                                        </div>
                                        <Progress
                                            value={planStatusProgress(planStatus.usage.cases.used, planStatus.usage.cases.limit)}
                                            className="h-2 bg-gray-100 [&>div]:bg-emerald-500"
                                        />
                                    </div>
                                    <div>
                                        <div className="mb-1.5 flex items-center justify-between text-sm">
                                            <span className="font-medium text-gray-700">{t('Clients')}</span>
                                            <span className="text-muted-foreground" {...valueDirProps}>
                                                {isRtl ? (
                                                    <>
                                                        <span dir={planStatus.usage.clients.limit === -1 ? 'rtl' : 'ltr'}>
                                                            {planStatus.usage.clients.limit === -1 ? t('Unlimited') : planStatus.usage.clients.limit}
                                                        </span>
                                                        <span dir="ltr"> / {planStatus.usage.clients.used}</span>
                                                    </>
                                                ) : planStatus.usage.clients.limit === -1
                                                    ? `${planStatus.usage.clients.used} / ${t('Unlimited')}`
                                                    : `${planStatus.usage.clients.used} / ${planStatus.usage.clients.limit}`}
                                            </span>
                                        </div>
                                        <Progress
                                            value={planStatusProgress(planStatus.usage.clients.used, planStatus.usage.clients.limit)}
                                            className="h-2 bg-gray-100 [&>div]:bg-emerald-500"
                                        />
                                    </div>
                                </div>

                                {(() => {
                                    const expireDate = planStatus.is_trial ? planStatus.trial_expire_date : planStatus.plan_expire_date;
                                    const daysLeft = daysFromToday(expireDate);
                                    if (expireDate == null) return null;
                                    const maxDays = 365;
                                    const progressRemaining = daysLeft != null && daysLeft >= 0 ? Math.min(100, (daysLeft / maxDays) * 100) : 0;
                                    const isExpired = daysLeft != null && daysLeft < 0;
                                    return (
                                        <div>
                                            <div className="mb-1.5 flex items-center justify-between text-sm">
                                                <span className="font-medium text-gray-700">{t('Days left')}</span>
                                                <span className={`tabular-nums ${isExpired ? 'font-medium text-red-600' : 'text-muted-foreground'}`} {...valueDirProps}>
                                                    {daysLeft == null
                                                        ? '—'
                                                        : isExpired
                                                            ? t('Expired')
                                                            : t('{{count}} days left', { count: daysLeft })}
                                                </span>
                                            </div>
                                            <Progress
                                                value={progressRemaining}
                                                className={`h-2 bg-gray-100 [&>div]:${isExpired ? 'bg-red-500' : 'bg-emerald-500'}`}
                                            />
                                        </div>
                                    );
                                })()}

                                <div>
                                    <h3 className="mb-2 text-sm font-semibold text-gray-700">{t('Plan Details')}</h3>
                                    <ul className="space-y-1 text-sm text-gray-600">
                                        <li>
                                            • <span {...valueDirProps}>{planStatus.plan_details.team_members === -1 ? t('Unlimited') : planStatus.plan_details.team_members}</span>{' '}
                                            {t('Team Members')}
                                        </li>
                                        <li>
                                            • <span {...valueDirProps}>{planStatus.plan_details.cases === -1 ? t('Unlimited') : planStatus.plan_details.cases}</span> {t('Cases')}
                                        </li>
                                        <li>
                                            • <span {...valueDirProps}>{planStatus.plan_details.clients === -1 ? t('Unlimited') : planStatus.plan_details.clients}</span>{' '}
                                            {t('Clients')}
                                        </li>
                                        <li>
                                            •{' '}
                                            <span {...valueDirProps}>{planStatus.plan_details.storage_gb === -1 ? t('Unlimited') : `${planStatus.plan_details.storage_gb} GB`}</span>{' '}
                                            {t('Storage')}
                                        </li>
                                    </ul>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                    <div className={`grid grid-cols-1 gap-8 ${plansGridCols}`}>
                        {plans.map((plan) => (
                            <div key={plan.id} className={`group relative flex h-full flex-col ${plan.recommended ? 'z-10 scale-[1.02]' : ''}`}>
                                {/* Card with decorative elements */}
                                <div
                                    className={`absolute inset-0 rounded-2xl ${
                                        plan.recommended
                                            ? 'from-primary/20 via-primary/10 border-primary/30 bg-gradient-to-br to-transparent'
                                            : 'border-gray-200/80 bg-gradient-to-br from-gray-100/80 via-gray-50/50 to-transparent'
                                    } group-hover:shadow-primary/5 overflow-hidden border shadow-lg transition-all duration-300 group-hover:shadow-xl`}
                                >
                                    {/* Decorative background elements */}
                                    <div className="from-primary/10 absolute top-0 right-0 -mt-16 -mr-16 h-32 w-32 rounded-full bg-gradient-to-br to-transparent opacity-70"></div>
                                    <div className="from-primary/10 absolute bottom-0 left-0 -mb-12 -ml-12 h-24 w-24 rounded-full bg-gradient-to-tr to-transparent opacity-50"></div>
                                </div>

                                {/* Recommended indicator */}
                                {plan.recommended && (
                                    <div className="absolute -top-4 right-0 left-0 z-20 flex justify-center">
                                        <div className="bg-primary text-primary-foreground flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-medium shadow-lg">
                                            <Sparkles className="h-4 w-4" />
                                            {t('Recommended')}
                                        </div>
                                    </div>
                                )}

                                {/* Status indicator - Admin only */}
                                {isAdmin && (
                                    <div className="absolute top-4 right-4 z-10 flex gap-2">
                                        {plan.is_default && (
                                            <div className="flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                {t('Default')}
                                            </div>
                                        )}
                                        <div
                                            className={`flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ${
                                                plan.status ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'
                                            } `}
                                        >
                                            <span className={`h-2 w-2 rounded-full ${plan.status ? 'bg-emerald-500' : 'bg-red-500'} `}></span>
                                            {plan.status ? t('Active') : t('Inactive')}
                                        </div>
                                    </div>
                                )}

                                {/* Current plan indicator - Company only */}
                                {!isAdmin && plan.is_current && (
                                    <div className="absolute top-4 end-4 z-10">
                                        <div className="bg-primary/10 text-primary flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium">
                                            <Crown className="h-3 w-3" />
                                            {t('Current')}
                                        </div>
                                    </div>
                                )}

                                {/* Content container */}
                                <div className="relative z-10 flex h-full flex-col p-6 pt-8">
                                    {/* Plan header */}
                                    <div className="mb-6">
                                        <h3 className={`mb-2 text-2xl font-bold ${plan.recommended ? 'text-primary' : ''} `}>{resolveTranslatable(plan.name_translations, currentLocale) || plan.name}</h3>
                                        <div className="mb-3 flex items-baseline gap-1.5">
                                            <span className={`text-3xl font-extrabold ${plan.recommended ? 'text-primary' : ''} `}>
                                                <CurrencyAmount amount={plan.price} variant="superadmin" />
                                            </span>
                                            <span className="text-muted-foreground text-sm">/{t(plan.duration.toLowerCase())}</span>
                                        </div>
                                        <p className="text-muted-foreground mb-3 line-clamp-2 text-sm leading-relaxed">{resolveTranslatable(plan.description_translations, currentLocale) || plan.description}</p>
                                        {plan.trial_days > 0 && (
                                            <div className="text-primary flex items-center gap-1.5 text-sm">
                                                <Sparkles className="h-3.5 w-3.5" />
                                                {t('{{days}} days free trial', { days: plan.trial_days })}
                                            </div>
                                        )}
                                    </div>

                                    {/* Divider with icon */}
                                    <div className="relative my-4 flex items-center">
                                        <div className="flex-grow border-t border-gray-200"></div>
                                        <div className="bg-primary/10 text-primary mx-3 rounded-full p-1.5">
                                            <CheckCircle2 className="h-4 w-4" />
                                        </div>
                                        <div className="flex-grow border-t border-gray-200"></div>
                                    </div>

                                    {/* Features */}
                                    {commonFeatures.length > 0 && (
                                        <div className="mb-6 flex-1">
                                            <h4 className="text-muted-foreground mb-3 text-sm font-semibold tracking-wider uppercase">
                                                {t('Features')}
                                            </h4>
                                            <ul className="space-y-2.5">
                                                {commonFeatures.map((feature, index) => {
                                                    const included = isFeatureIncluded(plan, feature);
                                                    return (
                                                        <li key={index} className="flex items-center gap-3">
                                                            {included ? (
                                                                <div className="bg-primary/10 text-primary flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full">
                                                                    <CheckCircle2 className="h-3.5 w-3.5" />
                                                                </div>
                                                            ) : (
                                                                <div className="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                                                    <X className="h-3.5 w-3.5" />
                                                                </div>
                                                            )}
                                                            <span className={`text-sm ${included ? 'font-medium' : 'text-muted-foreground'}`}>
                                                                {t(feature)}
                                                            </span>
                                                        </li>
                                                    );
                                                })}
                                            </ul>
                                        </div>
                                    )}

                                    {/* Usage limits */}
                                    <div className="mb-6">
                                        <h4 className="text-muted-foreground mb-3 text-sm font-semibold tracking-wider uppercase">
                                            {t('Usage Limits')}
                                        </h4>
                                        <div className="grid grid-cols-2 gap-3">
                                            <div className="group-hover:border-primary/30 relative overflow-hidden rounded-xl border border-gray-200 bg-white p-3 transition-colors">
                                                <div className="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-70"></div>
                                                <div className="relative mb-1 flex items-center gap-2">
                                                    <div className="rounded-full bg-emerald-100 p-1.5 text-emerald-600">{statIcons.users}</div>
                                                    <div className="text-xl font-bold text-emerald-700">{formatLimitValue(plan.stats.users)}</div>
                                                </div>
                                                <div className="relative text-xs font-medium tracking-wide text-emerald-600 uppercase">
                                                    {t('Team Members')}
                                                </div>
                                            </div>
                                            <div className="group-hover:border-primary/30 relative overflow-hidden rounded-xl border border-gray-200 bg-white p-3 transition-colors">
                                                <div className="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-70"></div>
                                                <div className="relative mb-1 flex items-center gap-2">
                                                    <div className="rounded-full bg-blue-100 p-1.5 text-blue-600">{statIcons.cases}</div>
                                                    <div className="text-xl font-bold text-blue-700">{formatLimitValue(plan.stats.cases)}</div>
                                                </div>
                                                <div className="relative text-xs font-medium tracking-wide text-blue-600 uppercase">{t('Cases')}</div>
                                            </div>
                                            <div className="group-hover:border-primary/30 relative overflow-hidden rounded-xl border border-gray-200 bg-white p-3 transition-colors">
                                                <div className="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-70"></div>
                                                <div className="relative mb-1 flex items-center gap-2">
                                                    <div className="rounded-full bg-purple-100 p-1.5 text-purple-600">{statIcons.clients}</div>
                                                    <div className="text-xl font-bold text-purple-700">{formatLimitValue(plan.stats.clients)}</div>
                                                </div>
                                                <div className="relative text-xs font-medium tracking-wide text-purple-600 uppercase">
                                                    {t('Clients')}
                                                </div>
                                            </div>
                                            <div className="group-hover:border-primary/30 relative overflow-hidden rounded-xl border border-gray-200 bg-white p-3 transition-colors">
                                                <div className="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent opacity-70"></div>
                                                <div className="relative mb-1 flex items-center gap-2">
                                                    <div className="rounded-full bg-amber-100 p-1.5 text-amber-600">{statIcons.storage}</div>
                                                    <div className="text-xl font-bold text-amber-700">
                                                        {formatLimitValue(plan.stats.storage, 'GB')}
                                                    </div>
                                                </div>
                                                <div className="relative text-xs font-medium tracking-wide text-amber-600 uppercase">
                                                    {t('Storage')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Actions */}
                                    <div className="mt-auto border-t border-gray-200 pt-4">
                                        {isAdmin ? (
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-2">
                                                    <Switch
                                                        checked={plan.status}
                                                        onCheckedChange={() => togglePlanStatus(plan.id)}
                                                        className={plan.status ? 'data-[state=checked]:bg-primary' : ''}
                                                    />
                                                    <span className="text-muted-foreground text-sm">{plan.status ? t('Active') : t('Inactive')}</span>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        className="hover:border-primary hover:text-primary h-9 w-9 border-gray-200 p-0"
                                                        title={t('Edit')}
                                                        onClick={() => router.get(route('plans.edit', plan.id))}
                                                    >
                                                        <Pencil className="h-4 w-4" />
                                                    </Button>

                                                    {!plan.is_default && !plan.has_users && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            className="h-9 w-9 border-gray-200 p-0 hover:border-red-400 hover:text-red-600"
                                                            title={t('Delete')}
                                                            onClick={() => handleDelete(plan)}
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    )}
                                                </div>
                                            </div>
                                        ) : (
                                            getActionButton(plan)
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
                {/* Delete Modal - Admin only */}
                {isAdmin && (
                    <CrudDeleteModal
                        isOpen={isDeleteModalOpen}
                        onClose={() => setIsDeleteModalOpen(false)}
                        onConfirm={handleDeleteConfirm}
                        itemName={planToDelete ? (resolveTranslatable(planToDelete.name_translations, currentLocale) || planToDelete.name) : ''}
                        entityName="plan"
                    />
                )}

                {/* Subscription Modal - Company only */}
                {!isAdmin && selectedPlan && (
                    <PlanSubscriptionModal
                        isOpen={isSubscriptionModalOpen}
                        onClose={() => {
                            setIsSubscriptionModalOpen(false);
                            setSelectedPlan(null);
                        }}
                        plan={selectedPlan}
                        billingCycle={billingCycle}
                        paymentMethods={formatPaymentMethods(selectedPlan.paymentMethods)}
                    />
                )}
            </div>
        </PageTemplate>
    );
}
