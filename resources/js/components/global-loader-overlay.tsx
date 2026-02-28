import { router } from '@inertiajs/react';
import { createPortal, flushSync } from 'react-dom';
import * as React from 'react';
import { Loader } from '@/components/ui/loader';
import { cn } from '@/lib/utils';

const LANGUAGE_SWITCH_START = 'language-switch-start';
const LANGUAGE_SWITCH_FINISH = 'language-switch-finish';

/**
 * Full-page loader overlay for Inertia navigation and language switch.
 * Renders into document.body so it appears above sidebar and all content.
 */
export function GlobalLoaderOverlay() {
    const [isNavigating, setIsNavigating] = React.useState(false);
    const [isLanguageSwitching, setIsLanguageSwitching] = React.useState(false);

    React.useEffect(() => {
        const removeStart = router.on('start', () => setIsNavigating(true));
        const removeFinish = router.on('finish', () => setIsNavigating(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    React.useEffect(() => {
        const onStart = () => {
            flushSync(() => setIsLanguageSwitching(true));
        };
        const onFinish = () => setIsLanguageSwitching(false);
        window.addEventListener(LANGUAGE_SWITCH_START, onStart);
        window.addEventListener(LANGUAGE_SWITCH_FINISH, onFinish);
        return () => {
            window.removeEventListener(LANGUAGE_SWITCH_START, onStart);
            window.removeEventListener(LANGUAGE_SWITCH_FINISH, onFinish);
        };
    }, []);

    const showOverlay = isNavigating || isLanguageSwitching;

    if (typeof document === 'undefined') return null;

    return createPortal(
        <div
            className={cn(
                'fixed inset-0 flex items-center justify-center bg-background/60 backdrop-blur-sm transition-opacity duration-200',
                showOverlay ? 'opacity-100' : 'pointer-events-none opacity-0',
                'z-[100]',
            )}
            aria-hidden={!showOverlay}
            role="status"
            aria-live="polite"
        >
            <Loader size="lg" />
        </div>,
        document.body,
    );
}
