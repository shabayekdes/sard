import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { ReactNode } from 'react';
import { FloatingChatGpt } from '@/components/FloatingChatGpt';

export interface PageAction {
  label: string;
  icon?: ReactNode;
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
  onClick?: () => void;
}

export interface PageTemplateProps {
  /** Page title: string or custom React node (e.g. title + badge) */
  title: string | ReactNode;
  /** Optional string used for document <Head> and breadcrumb when title is a React node */
  titleForHead?: string;
  // description: string; //TODO:: NOT USED
  url: string;
  /** Action buttons, or custom React node (e.g. buttons + dropdown) */
  actions?: PageAction[] | ReactNode;
  children: ReactNode;
  noPadding?: boolean;
  breadcrumbs?: BreadcrumbItem[];
}

export function PageTemplate({
  title,
  titleForHead,
  // description, //TODO:: NOT USED
  url,
  actions,
  children,
  noPadding = false,
  breadcrumbs
}: PageTemplateProps) {
  const titleString = titleForHead ?? (typeof title === 'string' ? title : '');
  // Default breadcrumbs if none provided
  const pageBreadcrumbs: BreadcrumbItem[] = breadcrumbs || [
    {
      title: titleString,
      href: url,
    },
  ];

  return (
    <AppLayout breadcrumbs={pageBreadcrumbs}>
      <Head title={`${titleString || 'Page'} - ${(usePage().props as any).globalSettings?.titleText || 'Sard App'}`} />


      <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto overflow-x-hidden p-4">
        {/* Header with action buttons */}
        <div className="flex items-start justify-between gap-2">
          <div className="flex min-w-0 flex-col gap-1">
            <h1 className="text-xl font-semibold text-gray-900 dark:text-white">{title}</h1>
            {pageBreadcrumbs.length > 0 && (
              <div className="text-xs text-muted-foreground md:hidden">
                <Breadcrumbs items={pageBreadcrumbs.map((item) => ({ label: item.title, href: item.href }))} />
              </div>
            )}
          </div>
          {actions && (
            <div className="flex items-center gap-2">
              {Array.isArray(actions) ? (
                actions.map((action, index) => (
                  <Button
                    key={index}
                    variant={action.variant || 'outline'}
                    size="sm"
                    onClick={action.onClick}
                    className="cursor-pointer"
                  >
                    {action.icon && <span className="sm:mr-1">{action.icon}</span>}
                    <span className="sr-only sm:not-sr-only">{action.label}</span>
                  </Button>
                ))
              ) : (
                actions
              )}
            </div>
          )}
        </div>

        {/* Content */}
        <div className={noPadding ? "" : "rounded-xl border p-6"}>
          {children}
        </div>
      </div>
      <FloatingChatGpt />
    </AppLayout>
  );
}
