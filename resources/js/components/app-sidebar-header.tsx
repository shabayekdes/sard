import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useLayout } from '@/contexts/LayoutContext';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { ProfileMenu } from '@/components/profile-menu';
import { LanguageSwitcher } from '@/components/language-switcher';
import { usePage, router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    const { t } = useTranslation();
    const { position } = useLayout();

    return (
        <>
            <header className="border-sidebar-border/50 sticky top-0 z-30 flex min-h-[3.5rem] shrink-0 items-center gap-2 border-b bg-background/95 px-4 py-2 backdrop-blur transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:static md:z-auto md:bg-transparent md:py-0 md:backdrop-blur-0 md:px-3">
            <div className="flex w-full flex-wrap items-center justify-between gap-2">
                <div className="flex flex-wrap items-center gap-2">
                    {position === 'left' && <SidebarTrigger className="-ml-1" />}
                    {position === 'right' && <SidebarTrigger className="-ml-1" />}
                    <div className="hidden md:flex">
                        <Breadcrumbs items={breadcrumbs.map(b => ({ label: b.title, href: b.href }))} />
                    </div>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    {(usePage().props as any).isImpersonating && (
                        <button 
                            onClick={() => router.post(route('impersonate.leave'))}
                            className="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 cursor-pointer"
                        >
                            {t("Return Back")}
                        </button>
                    )}
                    <LanguageSwitcher />
                    <ProfileMenu />
                </div>
            </div>
        </header>
        </>
    );
}
