import { PageTemplate } from '@/components/page-template';
import { Card, CardContent } from '@/components/ui/card';
import { hasPermission } from '@/utils/authorization';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

interface SetupItem {
  title: string;
  description: string;
  href: string;
  permissions: string[];
  roleCondition?: 'company';
}

interface SetupSection {
  sectionTitle: string;
  items: SetupItem[];
}

function canShowItem(
  item: SetupItem,
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

  const sections: SetupSection[] = useMemo(
    () => [
      {
        sectionTitle: t('Client Management'),
        items: [
          {
            title: t('Client Type'),
            description: t('Categorize clients by type'),
            href: route('clients.client-types.index'),
            permissions: ['manage-client-types', 'manage-any-client-types', 'manage-own-client-types'],
          },
          {
            title: t('Document Type'),
            description: t('Define and categorize document types'),
            href: route('advocate.document-types.index'),
            permissions: ['manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
          },
        ],
      },
      {
        sectionTitle: t('Case Management'),
        items: [
          {
            title: t('Case Categories'),
            description: t('Organize cases by classification'),
            href: route('cases.case-categories.index'),
            permissions: ['manage-case-categories', 'manage-any-case-categories', 'manage-own-case-categories'],
          },
          {
            title: t('Case Types'),
            description: t('Define legal case types'),
            href: route('cases.case-types.index'),
            permissions: ['manage-case-types', 'manage-any-case-types', 'manage-own-case-types'],
          },
          {
            title: t('Session Types'),
            description: t('Define legal session types'),
            href: route('hearing-types.index'),
            permissions: ['manage-hearing-types', 'manage-any-hearing-types', 'manage-own-hearing-types'],
          },
          {
            title: t('Event Types'),
            description: t('Categorize events within the system'),
            href: route('advocate.event-types.index'),
            permissions: ['manage-event-types', 'manage-any-event-types', 'manage-own-event-types'],
          },
          {
            title: t('Case Statuses'),
            description: t('Follow up on case status and progress'),
            href: route('cases.case-statuses.index'),
            permissions: ['manage-case-statuses', 'manage-any-case-statuses', 'manage-own-case-statuses'],
          },
        ],
      },
      {
        sectionTitle: t('Courts & Judiciary'),
        items: [
          {
            title: t('Court Types'),
            description: t('Categorize courts by degree'),
            href: route('advocate.court-types.index'),
            permissions: ['manage-court-types', 'manage-any-court-types', 'manage-own-court-types'],
          },
          {
            title: t('Circle Types'),
            description: t('Organize departments within courts'),
            href: route('advocate.circle-types.index'),
            permissions: ['manage-circle-types', 'manage-any-circle-types', 'manage-own-circle-types'],
          },
          {
            title: t('Judges'),
            description: t('Manage judge data and records'),
            href: route('judges.index'),
            permissions: ['manage-judges', 'manage-any-judges', 'manage-own-judges'],
          },
        ],
      },
      {
        sectionTitle: t('Document Management'),
        items: [
          {
            title: t('Document Categories'),
            description: t('Organize documents into categories'),
            href: route('document-management.categories.index'),
            permissions: ['manage-document-categories', 'manage-any-document-categories', 'manage-own-document-categories'],
          },
          {
            title: t('Document Types'),
            description: t('Define and categorize document types'),
            href: route('advocate.document-types.index'),
            permissions: ['manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
          },
        ],
      },
      {
        sectionTitle: t('Search Settings'),
        items: [
          {
            title: t('Research Type'),
            description: t('Determine available search methods'),
            href: route('legal-research.research-types.index'),
            permissions: ['manage-research-types', 'manage-any-research-types', 'manage-own-research-types'],
          },
          {
            title: t('Research Source'),
            description: t('Determine sources of search results'),
            href: route('legal-research.sources.index'),
            permissions: ['manage-research-sources', 'manage-any-research-sources', 'manage-own-research-sources'],
          },
          {
            title: t('Practice Area'),
            description: t('Categorize search by area'),
            href: route('advocate.practice-areas.index'),
            permissions: ['manage-practice-areas', 'manage-any-practice-areas', 'manage-own-practice-areas'],
          },
        ],
      },
      {
        sectionTitle: t('Task Management'),
        items: [
          {
            title: t('Task Types'),
            description: t('Categorize tasks by type'),
            href: route('tasks.task-types.index'),
            permissions: ['manage-task-types', 'manage-any-task-types', 'manage-own-task-types'],
          },
          {
            title: t('Task Statuses'),
            description: t('Follow up on task progress'),
            href: route('tasks.task-statuses.index'),
            permissions: ['manage-task-statuses', 'manage-any-task-statuses', 'manage-own-task-statuses'],
          },
        ],
      },
      {
        sectionTitle: t('Billing'),
        items: [
          {
            title: t('Expense Category'),
            description: t('Categorize and organize expense items'),
            href: route('billing.expense-categories.index'),
            permissions: ['manage-expense-categories', 'manage-any-expense-categories', 'manage-own-expense-categories'],
          },
        ],
      },
      {
        sectionTitle: t('Notifications'),
        items: [
          {
            title: t('Notification Template'),
            description: t('Manage and customize notification templates'),
            href: route('notification-templates.index'),
            permissions: [],
            roleCondition: 'company',
          },
        ],
      },
    ],
    [t]
  );

  const sectionsWithVisibleItems = useMemo(
    () =>
      sections
        .map((section) => ({
          ...section,
          visibleItems: section.items.filter((item) => canShowItem(item, permissions, userRole)),
        }))
        .filter((section) => section.visibleItems.length > 0),
    [sections, permissions, userRole]
  );

  return (
    <PageTemplate title={t('Settings')} url="/setup">
      <div className="space-y-8">
        {sectionsWithVisibleItems.length === 0 ? (
          <p className="text-muted-foreground text-sm py-8">{t('No setup options available.')}</p>
        ) : (
          sectionsWithVisibleItems.map((section) => (
            <div key={section.sectionTitle} className="space-y-3">
              <h2 className="text-sm font-medium text-muted-foreground">{section.sectionTitle}</h2>
              <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                {section.visibleItems.map((item) => (
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
            </div>
          ))
        )}
      </div>
    </PageTemplate>
  );
}
