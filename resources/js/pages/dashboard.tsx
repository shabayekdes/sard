import React, { useEffect, useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { RefreshCw, Scale, Users, Calendar, DollarSign, Clock, TrendingUp, CheckCircle, Gavel, Timer, Target, BarChart3, Briefcase, PieChart } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { useTranslation } from 'react-i18next';
import { Link, router } from '@inertiajs/react';
import { formatCurrency } from '@/utils/helpers';
import { CrudFormModal } from '@/components/CrudFormModal';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip as RechartsTooltip, ResponsiveContainer, Legend, AreaChart, Area, BarChart, Bar } from 'recharts';

interface CompanyDashboardData {
  stats: {
    totalCases: number;
    activeCases: number;
    totalClients: number;
    activeClients?: number;
    currentUsers?: number;
    totalRevenue: number;
    monthlyGrowth: number;
    pendingTasks: number;
    upcomingHearings: number;
    unreadMessages: number;
    successRate?: number;
    avgResolutionDays?: number;
    collectionRate?: number;
    billableHours?: number;
  };
  recentActivity: Array<{
    id: number;
    type: 'case' | 'client' | 'hearing' | 'message' | 'task';
    title: string;
    description: string;
    time: string;
    status: 'success' | 'warning' | 'error' | 'info';
  }>;
  casesByStatus: Array<{ name: string; value: number; color: string }>;
  revenueData: Array<{ month: string; revenue: number; cases: number }>;
  casesByYear?: Array<{
    year: number;
    month: number;
    month_name: string;
    critical: number;
    high: number;
    medium: number;
    low: number;
  }>;
  yearlyRevenue?: Array<{
    year: number;
    month: number;
    month_name: string;
    revenue: number;
  }>;
  tasksByStatus?: Array<{
    year: number;
    month: number;
    month_name: string;
    not_started: number;
    in_progress: number;
    completed: number;
    on_hold: number;
  }>;
  overdueInvoices?: Array<{
    id: number;
    invoice_number: string;
    total_amount: number;
    client?: { name?: string } | null;
  }>;
  upcomingHearings: Array<{
    id: number;
    hearing_id?: string | null;
    title: string;
    case?: {
      case_id?: string;
      title?: string;
      file_number?: string;
    } | null;
    court: { name?: string; court_type?: string | null; circle_type?: string | null } | string | null;
    judge?: { name?: string } | null;
    hearing_type?: { name?: string; name_translations?: Record<string, string> } | null;
    description?: string | null;
    hearing_date?: string | null;
    hearing_time?: string | null;
    duration_minutes?: number | null;
    status?: string | null;
    notes?: string | null;
    url?: string | null;
    date: string;
    time: string;
    type: string;
    type_translations?: Record<string, string> | null;
  }>;
  recentCases?: Array<{
    id: number;
    title: string;
    case_number?: string | null;
    client?: { name?: string } | null;
    created_at?: string;
  }>;
  upcomingTasks?: Array<{
    id: number;
    title: string;
    due_date?: string | null;
    assigned_to?: { name?: string } | null;
    task_type?: { name?: string } | null;
    description?: string | null;
    status?: string | number | null;
    priority?: string | null;
  }>;
  tasksStatus: Array<{ status: string; count: number; color: string }>;
  plan: {
    name: string;
    storage_limit: number;
    max_users?: number;
    max_cases?: number;
    max_clients?: number;
    price?: number;
    yearly_price?: number;
    is_trial?: boolean;
    trial_expire_date?: string | null;
  };
  storage?: {
    total_used?: number;
    limit?: number;
  };
}

interface PageAction {
  label: string;
  icon: React.ReactNode;
  variant: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
  onClick: () => void;
}

export default function Dashboard({ dashboardData }: { dashboardData: CompanyDashboardData }) {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language || 'en';

  const getTranslatedLabel = (translations?: Record<string, string> | null, fallback?: string | null) => {
    if (translations) {
      return translations[currentLocale] || translations.en || translations.ar || fallback || '-';
    }
    return fallback || '-';
  };

  const taskStatusLabels: Record<string, string> = {
    not_started: t('Not Started'),
    in_progress: t('In Progress'),
    completed: t('Completed'),
    on_hold: t('On Hold')
  };
  const pageActions: PageAction[] = [
    {
      label: t('Refresh'),
      icon: <RefreshCw className="h-4 w-4" />,
      variant: 'outline',
      onClick: () => window.location.reload()
    },
  ];

  const stats = dashboardData?.stats || {
    totalCases: 156,
    activeCases: 89,
    totalClients: 234,
    activeClients: 180,
    totalRevenue: 125000,
    monthlyGrowth: 12.5,
    pendingTasks: 23,
    upcomingHearings: 8,
    unreadMessages: 15,
    successRate: 84.5,
    avgResolutionDays: 23.2,
    collectionRate: 72.4,
    billableHours: 348.5,
  };

  const recentActivity = dashboardData?.recentActivity || [];
  const casesByStatus = dashboardData?.casesByStatus || [
    { name: 'Active', value: 45, color: '#10b981' },
    { name: 'Pending', value: 25, color: '#f59e0b' },
    { name: 'Closed', value: 30, color: '#6b7280' }
  ];
  const revenueData = dashboardData?.revenueData || [];
  const upcomingHearings = dashboardData?.upcomingHearings || [];
  const recentCases = dashboardData?.recentCases || [];
  const upcomingTasks = dashboardData?.upcomingTasks || [];
  const casesByYear = dashboardData?.casesByYear || [];
  const yearlyRevenue = dashboardData?.yearlyRevenue || [];
  const overdueInvoices = dashboardData?.overdueInvoices || [];
  const tasksByStatus = dashboardData?.tasksByStatus || [];
  const tasksStatus = dashboardData?.tasksStatus || [
    { status: 'not_started', count: 8, color: '#94a3b8' },
    { status: 'in_progress', count: 12, color: '#3b82f6' },
    { status: 'completed', count: 6, color: '#10b981' },
    { status: 'on_hold', count: 3, color: '#f59e0b' }
  ];

  // ShareModal state variables removed



  const [isSessionViewOpen, setIsSessionViewOpen] = useState(false);
  const [currentSession, setCurrentSession] = useState<any>(null);
  const [selectedCaseYear, setSelectedCaseYear] = useState<number>(new Date().getFullYear());
  const [selectedRevenueYear, setSelectedRevenueYear] = useState<number>(new Date().getFullYear());
  const [selectedTaskYear, setSelectedTaskYear] = useState<number>(new Date().getFullYear());
  const [isTaskViewOpen, setIsTaskViewOpen] = useState(false);
  const [currentTask, setCurrentTask] = useState<any>(null);

  useEffect(() => {
    if (typeof window === 'undefined') {
      return;
    }
    const params = new URLSearchParams(window.location.search);
    const viewId = params.get('view');
    if (!viewId) {
      return;
    }
    const targetId = Number(viewId);
    const target = upcomingHearings.find((item) => Number(item.id) === targetId);
    if (target) {
      setCurrentSession(target);
      setIsSessionViewOpen(true);
    }
  }, [upcomingHearings]);

  const openSessionView = (session: any) => {
    setCurrentSession(session);
    setIsSessionViewOpen(true);
    router.get(route('dashboard'), { view: session.id }, { preserveState: true, preserveScroll: true, replace: true });
  };

  const closeSessionView = () => {
    setIsSessionViewOpen(false);
    setCurrentSession(null);
    router.get(route('dashboard'), {}, { preserveState: true, preserveScroll: true, replace: true });
  };

  const formatCourtLabel = (court: any) => {
    if (!court) return '-';
    if (typeof court === 'string') return court;
    const parts = [court.name, court.court_type, court.circle_type].filter(Boolean);
    return parts.length ? parts.join(' + ') : '-';
  };

  const formatCaseLabel = (caseItem: any) => {
    if (!caseItem) return '-';
    const caseId = caseItem.case_id || '-';
    const caseName = caseItem.title || '-';
    const caseNumber = caseItem.file_number || '';
    return caseNumber ? `${caseId} + ${caseName} + ${caseNumber}` : `${caseId} + ${caseName}`;
  };

  const openTaskView = (task: any) => {
    setCurrentTask(task);
    setIsTaskViewOpen(true);
  };

  const closeTaskView = () => {
    setIsTaskViewOpen(false);
    setCurrentTask(null);
  };

  return (
    <PageTemplate
      title={t('Dashboard')}
      url="/dashboard"
      actions={pageActions}
    >
      <div className="space-y-6">
        {/* Card View */}
        <div className="space-y-3">
          <div className="flex items-center justify-between">
            <h2 className="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
              {t('Card View')}
            </h2>
          </div>
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Link href={route('cases.index')}>
              <Card className="cursor-pointer hover:shadow-md transition-shadow">
                <CardContent className="p-5">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-xs font-medium text-muted-foreground">{t('Active Cases')}</p>
                      <h3 className="mt-2 text-2xl font-bold">{stats.activeCases}</h3>
                      <p className="text-xs text-muted-foreground mt-1">
                        {stats.totalCases} {t('total')}
                      </p>
                    </div>
                    <div className="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                      <Scale className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Link href={route('clients.index')}>
              <Card className="cursor-pointer hover:shadow-md transition-shadow">
                <CardContent className="p-5">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-xs font-medium text-muted-foreground">{t('Active Client')}</p>
                      <h3 className="mt-2 text-2xl font-bold">{stats.activeClients || stats.totalClients}</h3>
                      <p className="text-xs text-green-600 mt-1">+{stats.monthlyGrowth}% {t('this month')}</p>
                    </div>
                    <div className="rounded-full bg-green-100 p-3 dark:bg-green-900">
                      <Users className="h-5 w-5 text-green-600 dark:text-green-400" />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Link href={route('clients.billing.index')}>
              <Card className="cursor-pointer hover:shadow-md transition-shadow">
                <CardContent className="p-5">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-xs font-medium text-muted-foreground">{t('Total Revenue')}</p>
                      <h3 className="mt-2 text-2xl font-bold">{formatCurrency(stats?.totalRevenue ?? 0)}</h3>
                      <p className="text-xs text-muted-foreground mt-1">{t('This year')}</p>
                    </div>
                    <div className="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900">
                      <DollarSign className="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Link href={route('tasks.index')}>
              <Card className="cursor-pointer hover:shadow-md transition-shadow">
                <CardContent className="p-5">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-xs font-medium text-muted-foreground">{t('Pending Tasks')}</p>
                      <h3 className="mt-2 text-2xl font-bold">{stats.pendingTasks}</h3>
                      <p className="text-xs text-orange-600 mt-1">{stats.upcomingHearings} {t('hearings due')}</p>
                    </div>
                    <div className="rounded-full bg-orange-100 p-3 dark:bg-orange-900">
                      <Clock className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Card className="hover:shadow-md transition-shadow">
              <CardContent className="p-5">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-xs font-medium text-muted-foreground">{t('Success Rate')}</p>
                    <h3 className="mt-2 text-2xl font-bold">{stats.successRate ?? 0}%</h3>
                    <p className="text-xs text-muted-foreground mt-1">{t('Closed cases')}</p>
                  </div>
                  <div className="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900">
                    <CheckCircle className="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow">
              <CardContent className="p-5">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-xs font-medium text-muted-foreground">{t('Avg Resolutions')}</p>
                    <h3 className="mt-2 text-2xl font-bold">{stats.avgResolutionDays ?? 0}</h3>
                    <p className="text-xs text-muted-foreground mt-1">{t('days')}</p>
                  </div>
                  <div className="rounded-full bg-sky-100 p-3 dark:bg-sky-900">
                    <Timer className="h-5 w-5 text-sky-600 dark:text-sky-400" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow">
              <CardContent className="p-5">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-xs font-medium text-muted-foreground">{t('Collection Rate')}</p>
                    <h3 className="mt-2 text-2xl font-bold">{stats.collectionRate ?? 0}%</h3>
                    <p className="text-xs text-muted-foreground mt-1">{t('Collected revenue')}</p>
                  </div>
                  <div className="rounded-full bg-indigo-100 p-3 dark:bg-indigo-900">
                    <TrendingUp className="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="hover:shadow-md transition-shadow">
              <CardContent className="p-5">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-xs font-medium text-muted-foreground">{t('Billable Hours')}</p>
                    <h3 className="mt-2 text-2xl font-bold">{stats.billableHours ?? 0}</h3>
                    <p className="text-xs text-muted-foreground mt-1">{t('hours')}</p>
                  </div>
                  <div className="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                    <PieChart className="h-5 w-5 text-purple-600 dark:text-purple-400" />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        {/* Main Dashboard Content */}
        <div className="grid gap-6 lg:grid-cols-12">
          {/* Upcoming Sessions */}
          <Card className="lg:col-span-6">
            <CardHeader>
              <Link href={route('hearings.index')} className="block">
                <CardTitle className="flex items-center justify-between hover:text-primary transition-colors">
                  <div className="flex items-center gap-2">
                    <Gavel className="h-5 w-5" />
                    {t('Upcoming Session')}
                  </div>
                  <Badge variant="secondary">{stats.upcomingHearings}</Badge>
                </CardTitle>
              </Link>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {upcomingHearings.slice(0, 4).map((hearing) => (
                  <button
                    key={hearing.id}
                    type="button"
                    onClick={() => openSessionView(hearing)}
                    className="block w-full text-start"
                  >
                    <div className="flex items-start gap-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors">
                      <div className="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                        <Calendar className="h-4 w-4 text-purple-600 dark:text-purple-400" />
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="font-medium text-sm truncate">{hearing.title}</p>
                        <p className="text-xs text-muted-foreground">
                          {typeof hearing.court === 'string' ? hearing.court : hearing.court?.name}
                        </p>
                        <p className="text-xs text-muted-foreground">{hearing.date} at {hearing.time}</p>
                      </div>
                      <Badge variant="outline" className="text-xs">
                        {getTranslatedLabel(hearing.type_translations, hearing.type)}
                      </Badge>
                    </div>
                  </button>
                ))}
                {upcomingHearings.length === 0 && (
                  <div className="text-center py-8 text-muted-foreground">
                    <Calendar className="h-12 w-12 mx-auto mb-2 opacity-50" />
                    <p className="text-sm">{t('No upcoming sessions')}</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Recent Cases */}
          <Card className="lg:col-span-6">
            <CardHeader>
              <CardTitle className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Briefcase className="h-5 w-5" />
                  {t('Recent Cases')}
                </div>
                <Badge variant="secondary">{stats.totalCases}</Badge>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {recentCases.slice(0, 4).map((caseItem) => (
                  <Link key={caseItem.id} href={route('cases.show', caseItem.id)} className="block">
                    <div className="flex items-start gap-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors">
                      <div className="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                        <Briefcase className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="font-medium text-sm truncate">{caseItem.title}</p>
                        <p className="text-xs text-muted-foreground">
                          {caseItem.case_number ? `${t('Case')} ${caseItem.case_number}` : t('Case number not set')}
                        </p>
                      </div>
                      <Badge variant="outline" className="text-xs capitalize">
                        {caseItem.client?.name || t('No client')}
                      </Badge>
                    </div>
                  </Link>
                ))}
                {recentCases.length === 0 && (
                  <div className="text-center py-8 text-muted-foreground">
                    <Briefcase className="h-12 w-12 mx-auto mb-2 opacity-50" />
                    <p className="text-sm">{t('No recent cases')}</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Cases by Year */}
          <Card className="hover:shadow-lg transition-shadow lg:col-span-12">
            <CardHeader className="pb-4">
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2 text-lg">
                  <BarChart3 className="h-5 w-5 text-blue-600" />
                  {t('Cases by Year')}
                </CardTitle>
                <Select value={selectedCaseYear.toString()} onValueChange={(value) => setSelectedCaseYear(parseInt(value, 10))}>
                  <SelectTrigger className="w-28">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i).map((year) => (
                      <SelectItem key={year} value={year.toString()}>
                        {year}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </CardHeader>
            <CardContent>
              {casesByYear.filter((item) => item.year === selectedCaseYear).length > 0 ? (
                <ResponsiveContainer width="100%" height={300}>
                  <LineChart data={casesByYear.filter((item) => item.year === selectedCaseYear)}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                    <XAxis dataKey="month_name" tick={{ fontSize: 12 }} />
                    <YAxis tick={{ fontSize: 12 }} />
                    <RechartsTooltip
                      contentStyle={{
                        backgroundColor: '#fff',
                        border: '1px solid #e2e8f0',
                        borderRadius: '8px',
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                      }}
                    />
                    <Line type="monotone" dataKey="critical" stroke="#ef4444" strokeWidth={3} dot={{ fill: '#ef4444', strokeWidth: 2, r: 4 }} />
                    <Line type="monotone" dataKey="high" stroke="#f59e0b" strokeWidth={3} dot={{ fill: '#f59e0b', strokeWidth: 2, r: 4 }} />
                    <Line type="monotone" dataKey="medium" stroke="#10b981" strokeWidth={3} dot={{ fill: '#10b981', strokeWidth: 2, r: 4 }} />
                    <Line type="monotone" dataKey="low" stroke="#6b7280" strokeWidth={3} dot={{ fill: '#6b7280', strokeWidth: 2, r: 4 }} />
                    <Legend />
                  </LineChart>
                </ResponsiveContainer>
              ) : (
                <div className="flex items-center justify-center h-[300px] text-muted-foreground">
                  <div className="text-center">
                    <BarChart3 className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p>{t('No case data available for')} {selectedCaseYear}</p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Yearly Revenue + Overdue Invoices */}
        <div className="grid gap-6 lg:grid-cols-12">
          <Card className="hover:shadow-lg transition-shadow lg:col-span-8">
            <CardHeader className="pb-4">
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2 text-lg">
                  <BarChart3 className="h-5 w-5 text-blue-600" />
                  {t('Yearly Revenue Trend')}
                </CardTitle>
                <Select value={selectedRevenueYear.toString()} onValueChange={(value) => setSelectedRevenueYear(parseInt(value, 10))}>
                  <SelectTrigger className="w-28">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i).map((year) => (
                      <SelectItem key={year} value={year.toString()}>
                        {year}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </CardHeader>
            <CardContent>
              {yearlyRevenue.filter((item) => item.year === selectedRevenueYear).length > 0 ? (
                <ResponsiveContainer width="100%" height={300}>
                  <AreaChart data={yearlyRevenue.filter((item) => item.year === selectedRevenueYear)}>
                    <defs>
                      <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor="#3b82f6" stopOpacity={0.35} />
                        <stop offset="95%" stopColor="#3b82f6" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                    <XAxis dataKey="month_name" tick={{ fontSize: 12 }} />
                    <YAxis tick={{ fontSize: 12 }} />
                    <RechartsTooltip
                      formatter={(value: number) => formatCurrency(value)}
                      contentStyle={{
                        backgroundColor: '#fff',
                        border: '1px solid #e2e8f0',
                        borderRadius: '8px',
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                      }}
                    />
                    <Area type="monotone" dataKey="revenue" stroke="#3b82f6" strokeWidth={3} fill="url(#revenueGradient)" />
                  </AreaChart>
                </ResponsiveContainer>
              ) : (
                <div className="flex items-center justify-center h-[300px] text-muted-foreground">
                  <div className="text-center">
                    <BarChart3 className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p>{t('No revenue data available for')} {selectedRevenueYear}</p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          <Card className="hover:shadow-lg transition-shadow lg:col-span-4">
            <CardHeader className="pb-4">
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2 text-lg">
                  <BarChart3 className="h-5 w-5 text-red-600" />
                  {t('Overdue Invoices')}
                </CardTitle>
                <Link href={route('billing.invoices.index')} className="text-sm text-muted-foreground hover:text-foreground">
                  {t('View All')}
                </Link>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {overdueInvoices.length > 0 ? (
                  overdueInvoices.slice(0, 5).map((invoice) => (
                    <div
                      key={invoice.id}
                      className="flex items-center justify-between rounded-lg border border-red-200 bg-red-50 p-4 hover:bg-red-100 transition-colors"
                    >
                      <div>
                        <p className="font-semibold text-sm">{invoice.invoice_number}</p>
                        <p className="text-xs text-muted-foreground">{invoice.client?.name || t('No client')}</p>
                      </div>
                      <span className="font-bold text-red-600 text-lg">
                        {formatCurrency(invoice.total_amount)}
                      </span>
                    </div>
                  ))
                ) : (
                  <div className="flex items-center justify-center py-12 text-muted-foreground">
                    <div className="text-center">
                      <BarChart3 className="h-12 w-12 mx-auto mb-4 opacity-50" />
                      <p>{t('No overdue invoices')}</p>
                    </div>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        </div>



        {/* Additional Analytics */}
        <div className="grid gap-6 lg:grid-cols-12">
          {/* Tasks by Status (summary) */}
          <Card className="lg:col-span-4">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="h-5 w-5" />
                {t('Tasks by Status')}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {tasksStatus.map((task, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <div className="w-4 h-4 rounded-full" style={{ backgroundColor: task.color }} />
                      <span className="font-medium">{taskStatusLabels[task.status] || task.status}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-2xl font-bold">{task.count}</span>
                      <span className="text-sm text-muted-foreground">{t('tasks')}</span>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Upcoming Tasks */}
          <Card className="lg:col-span-4">
            <CardHeader>
              <CardTitle className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Clock className="h-5 w-5" />
                  {t('Upcoming Tasks')}
                </div>
                <Badge variant="secondary">{stats.pendingTasks}</Badge>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {upcomingTasks.slice(0, 4).map((task) => (
                  <button
                    key={task.id}
                    type="button"
                    onClick={() => openTaskView(task)}
                    className="block w-full text-left"
                  >
                    <div className="flex items-start gap-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors">
                      <div className="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                        <Clock className="h-4 w-4 text-orange-600 dark:text-orange-400" />
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="font-medium text-sm truncate">{task.title}</p>
                        <p className="text-xs text-muted-foreground">
                          {task.assigned_to?.name || t('Unassigned')} • {task.due_date || t('No due date')}
                        </p>
                      </div>
                      <Badge variant="outline" className="text-xs">
                        {task.task_type?.name || t('General')}
                      </Badge>
                    </div>
                  </button>
                ))}
                {upcomingTasks.length === 0 && (
                  <div className="text-center py-8 text-muted-foreground">
                    <Clock className="h-12 w-12 mx-auto mb-2 opacity-50" />
                    <p className="text-sm">{t('No upcoming tasks')}</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Tasks by Status (chart) */}
          <Card className="lg:col-span-4">
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                  <Target className="h-5 w-5" />
                  {t('Tasks by Status')}
                </CardTitle>
                <Select value={selectedTaskYear.toString()} onValueChange={(value) => setSelectedTaskYear(parseInt(value, 10))}>
                  <SelectTrigger className="w-24">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i).map((year) => (
                      <SelectItem key={year} value={year.toString()}>
                        {year}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </CardHeader>
            <CardContent>
              {tasksByStatus.filter((item) => item.year === selectedTaskYear).length > 0 ? (
                <ResponsiveContainer width="100%" height={250}>
                  <BarChart data={tasksByStatus.filter((item) => item.year === selectedTaskYear)}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                    <XAxis dataKey="month_name" tick={{ fontSize: 12 }} />
                    <YAxis tick={{ fontSize: 12 }} />
                    <RechartsTooltip />
                    <Bar dataKey="not_started" fill="#94a3b8" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="in_progress" fill="#3b82f6" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="completed" fill="#10b981" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="on_hold" fill="#f59e0b" radius={[4, 4, 0, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              ) : (
                <div className="flex items-center justify-center h-[250px] text-muted-foreground">
                  <div className="text-center">
                    <Target className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p>{t('No task status data')}</p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>


      </div>

      <CrudFormModal
        isOpen={isSessionViewOpen}
        onClose={closeSessionView}
        onSubmit={() => { }}
        formConfig={{
          fields: [
            { name: 'hearing_id', label: t('Session ID'), type: 'text' },
            { name: 'title', label: t('Title'), type: 'text' },
            { name: 'case', label: t('Case'), type: 'text' },
            { name: 'court', label: t('Court'), type: 'text' },
            { name: 'judge', label: t('Judge'), type: 'text' },
            { name: 'hearing_type', label: t('Type'), type: 'text' },
            { name: 'description', label: t('Description'), type: 'textarea' },
            { name: 'hearing_date', label: t('Date'), type: 'text' },
            { name: 'hearing_time', label: t('Time'), type: 'text' },
            { name: 'duration_minutes', label: t('Duration (minutes)'), type: 'text' },
            { name: 'status', label: t('Status'), type: 'text' },
            { name: 'notes', label: t('Notes'), type: 'textarea' }
          ],
          modalSize: 'xl'
        }}
        initialData={{
          ...currentSession,
          case: formatCaseLabel(currentSession?.case),
          court: formatCourtLabel(currentSession?.court),
          judge: currentSession?.judge?.name || '-',
          hearing_type: getTranslatedLabel(
            currentSession?.hearing_type?.name_translations || currentSession?.type_translations,
            currentSession?.hearing_type?.name || currentSession?.type
          ),
          hearing_date: currentSession?.hearing_date || currentSession?.date || '-',
          hearing_time: currentSession?.hearing_time || currentSession?.time || '-',
          duration_minutes: currentSession?.duration_minutes ? `${currentSession.duration_minutes} minutes` : '-',
          status: currentSession?.status || '-'
        }}
        title={t('View Session Details')}
        mode="view"
      />

      <CrudFormModal
        isOpen={isTaskViewOpen}
        onClose={closeTaskView}
        onSubmit={() => { }}
        formConfig={{
          fields: [
            { name: 'title', label: t('Task Title'), type: 'text' },
            { name: 'assigned_to', label: t('Assigned To'), type: 'text' },
            { name: 'due_date', label: t('Due Date'), type: 'text' },
            { name: 'task_type', label: t('Type'), type: 'text' },
            { name: 'priority', label: t('Priority'), type: 'text' },
            { name: 'status', label: t('Status'), type: 'text' },
            { name: 'description', label: t('Description'), type: 'textarea' }
          ],
          modalSize: 'lg'
        }}
        initialData={{
          ...currentTask,
          assigned_to: currentTask?.assigned_to?.name || t('Unassigned'),
          task_type: currentTask?.task_type?.name || t('General'),
          due_date: currentTask?.due_date || '-',
          priority: currentTask?.priority || '-',
          status: currentTask?.status || '-'
        }}
        title={t('View Task Details')}
        mode="view"
      />

      {/* Share Modal removed */}
    </PageTemplate>
  );
}
