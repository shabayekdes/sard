import { PageTemplate } from '@/components/page-template';
import { Card, CardContent } from '@/components/ui/card';
import { hasPermission } from '@/utils/authorization';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

interface MainSetupItem {
  title: string;
  description: string;
  href: string;
  permissions: string[];
  roleCondition?: 'company';
}

function canShowMainItem(
  item: MainSetupItem,
  permissions: string[],
  userRole: string
): boolean {
  if (item.roleCondition === 'company') {
    return userRole === 'company';
  }
  return item.permissions.length === 0 || item.permissions.some((p) => hasPermission(permissions, p));
}

export default function SetupIndex() {
  const { t } = useTranslation();
  const { auth } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const userRole = auth?.user?.type || auth?.user?.role || '';

  const mainItems: MainSetupItem[] = useMemo(
    () => [
      {
        title: t('Client Management'),
        description: t('Client types and document types'),
        href: route('setup.clients'),
        permissions: ['manage-client-types', 'manage-any-client-types', 'manage-own-client-types', 'manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
      },
      {
        title: t('Case Management'),
        description: t('Case categories, types, statuses and sessions'),
        href: route('setup.cases'),
        permissions: ['manage-case-categories', 'manage-any-case-categories', 'manage-own-case-categories', 'manage-case-types', 'manage-any-case-types', 'manage-own-case-types', 'manage-case-statuses', 'manage-any-case-statuses', 'manage-own-case-statuses', 'manage-event-types', 'manage-any-event-types', 'manage-own-event-types', 'manage-hearing-types', 'manage-any-hearing-types', 'manage-own-hearing-types'],
      },
      {
        title: t('Courts & Judiciary'),
        description: t('Court types, circles and judges'),
        href: route('setup.courts'),
        permissions: ['manage-court-types', 'manage-any-court-types', 'manage-own-court-types', 'manage-circle-types', 'manage-any-circle-types', 'manage-own-circle-types', 'manage-judges', 'manage-any-judges', 'manage-own-judges'],
      },
      {
        title: t('Document Management'),
        description: t('Document categories and types'),
        href: route('setup.documents'),
        permissions: ['manage-document-categories', 'manage-any-document-categories', 'manage-own-document-categories', 'manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
      },
      {
        title: t('Search Settings'),
        description: t('Research types, sources and practice areas'),
        href: route('setup.research'),
        permissions: ['manage-research-types', 'manage-any-research-types', 'manage-own-research-types', 'manage-practice-areas', 'manage-any-practice-areas', 'manage-own-practice-areas', 'manage-research-sources', 'manage-any-research-sources', 'manage-own-research-sources'],
      },
      {
        title: t('Task Management'),
        description: t('Task types and statuses'),
        href: route('setup.tasks'),
        permissions: ['manage-task-types', 'manage-any-task-types', 'manage-own-task-types', 'manage-task-statuses', 'manage-any-task-statuses', 'manage-own-task-statuses'],
      },
      {
        title: t('Billing'),
        description: t('Expense categories'),
        href: route('setup.billing'),
        permissions: ['manage-expense-categories', 'manage-any-expense-categories', 'manage-own-expense-categories'],
      },
      {
        title: t('Notifications'),
        description: t('Notification templates'),
        href: route('setup.notifications'),
        permissions: [],
        roleCondition: 'company',
      },
    ],
    [t]
  );

  const visibleItems = useMemo(
    () => mainItems.filter((item) => canShowMainItem(item, permissions, userRole)),
    [mainItems, permissions, userRole]
  );

  return (
    <PageTemplate title={t('Settings')} url="/setup">
      <div className="space-y-4">
        {visibleItems.length === 0 ? (
          <p className="text-muted-foreground text-sm py-8">{t('No setup options available.')}</p>
        ) : (
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {visibleItems.map((item) => (
              <Link key={item.href} href={item.href}>
                <Card className="h-full transition-colors hover:bg-muted/50 cursor-pointer border rounded-lg">
                  <CardContent className="p-4 flex flex-col gap-2">
                    <div className="flex items-start gap-3">
                      <div className="rounded-md bg-muted p-2 shrink-0">
                        <LayoutGrid className="h-4 w-4 text-muted-foreground" />
                      </div>
                      <div className="min-w-0 flex-1">
                        <p className="font-medium text-sm">{item.title}</p>
                        <p className="text-xs text-muted-foreground mt-0.5 line-clamp-2">
                          {item.description}
                        </p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </Link>
            ))}
          </div>
        )}
      </div>
    </PageTemplate>
  );
}
