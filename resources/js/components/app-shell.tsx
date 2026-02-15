import CookieConsentBanner from '@/components/CookieConsentBanner';
import { FloatingChatGpt } from '@/components/FloatingChatGpt';
import { FloatingQuickActions } from '@/components/FloatingQuickActions';
import { GlobalQuickActionModals } from '@/components/GlobalQuickActionModals';
import { MobileFooterNav } from '@/components/MobileFooterNav';
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';

interface AppShellProps {
    children: React.ReactNode;
    variant?: 'header' | 'sidebar';
}

export function AppShell({ children, variant = 'header' }: AppShellProps) {
    const [isOpen, setIsOpen] = useState(() => (typeof window !== 'undefined' ? localStorage.getItem('sidebar') !== 'false' : true));
    const { props } = usePage();
    const globalSettings = (props as any).globalSettings || {};
    const isAuthenticated = !!(props as any).auth?.user;
    const isAuthPage =
        typeof window !== 'undefined' &&
        (window.location.pathname.includes('/login') || window.location.pathname.includes('/register') || window.location.pathname.includes('/auth'));
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
                <FloatingQuickActions />
                <FloatingChatGpt />
                <GlobalQuickActionModals />
                {isAuthenticated && !isAuthPage && <MobileFooterNav />}
                {shouldShowCookie && <CookieConsentBanner />}
            </div>
        );
    }

    return (
        <SidebarProvider defaultOpen={isOpen} open={isOpen} onOpenChange={handleSidebarChange}>
            <div className="flex min-h-[100dvh] w-full flex-row overflow-x-hidden">
                {children}
                <FloatingChatGpt />
                <GlobalQuickActionModals />
                {isAuthenticated && !isAuthPage && <MobileFooterNav />}
                {shouldShowCookie && <CookieConsentBanner />}
            </div>
        </SidebarProvider>
    );
}
