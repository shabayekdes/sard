import { router } from '@inertiajs/react';
import { SidebarInset } from '@/components/ui/sidebar';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface AppContentProps extends React.ComponentProps<'main'> {
    variant?: 'header' | 'sidebar';
}

export function AppContent({ variant = 'header', children, className, ...props }: AppContentProps) {
    const [isNavigating, setIsNavigating] = React.useState(false);

    React.useEffect(() => {
        const removeStart = router.on('start', () => setIsNavigating(true));
        const removeFinish = router.on('finish', () => setIsNavigating(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    if (variant === 'sidebar') {
        return (
            <SidebarInset {...props} className={className} aria-busy={isNavigating}>
                {children}
            </SidebarInset>
        );
    }

    return (
        <main
            className={cn(
                'mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl pb-20 md:pb-0 relative',
                className
            )}
            {...props}
            aria-busy={isNavigating}
        >
            {children}
        </main>
    );
}
