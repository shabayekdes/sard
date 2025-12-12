import { SidebarProvider } from '@/components/ui/sidebar';
import { useLayout } from '@/contexts/LayoutContext';
import { FloatingChatGpt } from '@/components/FloatingChatGpt';
import { cn } from '@/lib/utils';
import { useState } from 'react';
import CookieConsentBanner from '@/components/CookieConsentBanner';
import { usePage } from '@inertiajs/react';


interface AppShellProps {
    children: React.ReactNode;
    variant?: 'header' | 'sidebar';
}

export function AppShell({ children, variant = 'header' }: AppShellProps) {
    const [isOpen, setIsOpen] = useState(() => (typeof window !== 'undefined' ? localStorage.getItem('sidebar') !== 'false' : true));
    const { props } = usePage();
    const globalSettings = (props as any).globalSettings || {};
    const isAuthPage = typeof window !== 'undefined' && (window.location.pathname.includes('/login') || window.location.pathname.includes('/register') || window.location.pathname.includes('/auth'));
    const shouldShowCookie = !isAuthPage;

    const handleSidebarChange = (open: boolean) => {
        setIsOpen(open);

        if (typeof window !== 'undefined') {
            localStorage.setItem('sidebar', String(open));
        }
    };

    if (variant === 'header') {
        return (
            <div className="flex min-h-screen w-full flex-col">
                {children}
                <FloatingChatGpt />
                                {shouldShowCookie && <CookieConsentBanner />}
            </div>
        );
    }

    const { position } = useLayout();

    return (
        <SidebarProvider defaultOpen={isOpen} open={isOpen} onOpenChange={handleSidebarChange}>
            <div className={cn('flex w-full', position === 'right' ? 'flex-row-reverse' : 'flex-row')}>
                {children}
                <FloatingChatGpt />
                {shouldShowCookie && <CookieConsentBanner />}
            </div>
        </SidebarProvider>
    );
}
