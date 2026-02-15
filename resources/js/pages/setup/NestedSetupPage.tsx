import { PageTemplate } from '@/components/page-template';
import { Card, CardContent } from '@/components/ui/card';
import { hasPermission } from '@/utils/authorization';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export interface NestedSetupItem {
  title: string;
  description: string;
  href: string;
  permissions: string[];
  roleCondition?: 'company';
}

function canShowItem(
  item: NestedSetupItem,
  permissions: string[],
  userRole: string
): boolean {
  if (item.roleCondition === 'company') {
    return userRole === 'company';
  }
  return item.permissions.length === 0 || item.permissions.some((p: string) => hasPermission(permissions, p));
}

interface NestedSetupPageProps {
  title: string;
  url: string;
  items: NestedSetupItem[];
}

export function NestedSetupPage({ title, url, items }: NestedSetupPageProps) {
  const { t } = useTranslation();
  const { auth } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const userRole = auth?.user?.type || auth?.user?.role || '';

  const visibleItems = useMemo(
    () => items.filter((item) => canShowItem(item, permissions, userRole)),
    [items, permissions, userRole]
  );

  const breadcrumbs = [
    { title: t('Settings'), href: route('setup.index') },
    { title, href: url },
  ];

  return (
    <PageTemplate title={title} url={url} breadcrumbs={breadcrumbs}>
      <div className="space-y-4">
        {visibleItems.length === 0 ? (
          <p className="text-muted-foreground text-sm py-8">{t('No configuration options available.')}</p>
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
