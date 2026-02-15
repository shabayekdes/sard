import { useMemo } from 'react';
import { Plus, Scale, Users, Gavel, MessageSquare, ClipboardList } from 'lucide-react';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { useTranslation } from 'react-i18next';
import { cn } from '@/lib/utils';

interface QuickActionItem {
  label: string;
  icon: React.ReactNode;
  routeName: string;
  openModal?: boolean;
  modalKey?: 'cases' | 'clients' | 'tasks' | 'hearings';
}

const getQuickActions = (t: (key: string) => string): QuickActionItem[] => [
  { label: t('New Case'), icon: <Scale className="h-4 w-4" />, routeName: 'cases.index', openModal: true, modalKey: 'cases' },
  { label: t('New Client'), icon: <Users className="h-4 w-4" />, routeName: 'clients.index', openModal: true, modalKey: 'clients' },
  { label: t('Messages'), icon: <MessageSquare className="h-4 w-4" />, routeName: 'communication.messages.index' },
  { label: t('Schedule Session'), icon: <Gavel className="h-4 w-4" />, routeName: 'hearings.index', openModal: true, modalKey: 'hearings' },
  { label: t('New Task'), icon: <ClipboardList className="h-4 w-4" />, routeName: 'tasks.index', openModal: true, modalKey: 'tasks' },
];

/** Reusable quick actions dropdown; use in sidebar or floating. */
export function QuickActionsButton({
  className,
  buttonClassName,
  side = 'top',
}: {
  className?: string;
  buttonClassName?: string;
  side?: 'top' | 'bottom' | 'left' | 'right';
}) {
  const { t } = useTranslation();
  const actions = useMemo(() => getQuickActions(t), [t]);

  return (
    <div className={cn('flex', className)}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            className={cn('h-14 w-14 rounded-full shadow-lg hover:shadow-xl transition-shadow', buttonClassName)}
            size="lg"
            aria-label={t('Quick Actions')}
          >
            <Plus className="h-6 w-6" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" side={side} className="w-52">
          <DropdownMenuLabel>{t('Quick Actions')}</DropdownMenuLabel>
          <DropdownMenuSeparator />
          {actions.map((action) => (
            <DropdownMenuItem
              key={action.routeName}
              onSelect={(event) => {
                event.preventDefault();
                if (action.openModal && action.modalKey) {
                  window.dispatchEvent(new CustomEvent('quickAction:openModal', { detail: { key: action.modalKey } }));
                  return;
                }
                window.location.href = route(action.routeName);
              }}
            >
              {action.icon}
              <span>{action.label}</span>
            </DropdownMenuItem>
          ))}
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}

export function FloatingQuickActions() {
  return (
    <div className="fixed bottom-10 rtl:left-6 ltr:right-6 z-[9999] hidden md:block">
      <QuickActionsButton />
    </div>
  );
}
