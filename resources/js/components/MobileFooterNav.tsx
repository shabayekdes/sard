import { useMemo, type ReactNode } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { ClipboardList, Gavel, Home, MessageSquare, Plus, Scale, User, Users } from 'lucide-react';

interface QuickActionItem {
    label: string;
    icon: ReactNode;
    routeName: string;
    openModal?: boolean;
    modalKey?: 'cases' | 'clients' | 'tasks' | 'hearings';
}

const getPathname = (url: string) => {
    if (typeof window === 'undefined') return url;
    try {
        return new URL(url, window.location.origin).pathname;
    } catch {
        return url;
    }
};

export function MobileFooterNav() {
    const { t } = useTranslation();
    const { props, url } = usePage();
    const isAuthenticated = !!(props as any).auth?.user;

    const actions: QuickActionItem[] = useMemo(
        () => [
            { label: t('New Case'), icon: <Scale className="h-4 w-4" />, routeName: 'cases.index', openModal: true, modalKey: 'cases' },
            { label: t('New Client'), icon: <Users className="h-4 w-4" />, routeName: 'clients.index', openModal: true, modalKey: 'clients' },
            { label: t('Messages'), icon: <MessageSquare className="h-4 w-4" />, routeName: 'communication.messages.index' },
            { label: t('Schedule Session'), icon: <Gavel className="h-4 w-4" />, routeName: 'hearings.index', openModal: true, modalKey: 'hearings' },
            { label: t('New Task'), icon: <ClipboardList className="h-4 w-4" />, routeName: 'tasks.index', openModal: true, modalKey: 'tasks' },
        ],
        [t]
    );

    const navItems = [
        { label: t('Home'), href: route('dashboard'), icon: Home },
        { label: t('Cases'), href: route('cases.index'), icon: Scale },
        { label: t('Clients'), href: route('clients.index'), icon: Users },
        { label: t('Profile'), href: route('profile'), icon: User },
    ];

    if (!isAuthenticated) {
        return null;
    }

    const currentPath = getPathname(url);

    return (
        <nav className="fixed bottom-0 left-0 right-0 z-[9990] md:hidden" aria-label={t('Mobile footer navigation')}>
            <div className="relative border-t border-border bg-background/95 pb-2 pt-4 backdrop-blur supports-[backdrop-filter]:bg-background/80">
                <div className="flex items-center justify-between px-3">
                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = currentPath === getPathname(item.href);

                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={`flex flex-1 flex-col items-center gap-1 text-[11px] ${isActive ? 'text-primary' : 'text-muted-foreground'}`}
                            >
                                <Icon className="h-4 w-4" />
                                <span>{item.label}</span>
                            </Link>
                        );
                    })}
                </div>

                <div className="absolute left-1/2 top-0 flex -translate-x-1/2 -translate-y-1/2">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button className="h-12 w-12 rounded-full shadow-lg hover:shadow-xl transition-shadow" size="lg">
                                <Plus className="h-5 w-5" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="center" side="top" className="w-52">
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
            </div>
        </nav>
    );
}
