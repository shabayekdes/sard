import { createContext, ReactNode, useContext, useEffect, useState } from 'react';
import i18n from '../i18n';

export type LayoutPosition = 'left' | 'right';

type LayoutContextType = {
    position: LayoutPosition;
    effectivePosition: LayoutPosition;
    updatePosition: (val: LayoutPosition) => void;
    saveLayoutPosition: () => void;
    isRtl: boolean;
};

const LayoutContext = createContext<LayoutContextType | undefined>(undefined);

export const LayoutProvider = ({ children }: { children: ReactNode }) => {
    const [position, setPosition] = useState<LayoutPosition>('left');
    const [isRtl, setIsRtl] = useState<boolean>(false);

    useEffect(() => {
        // Direction and sidebar side are locale-driven.
        const updatePositionFromLanguage = () => {
            const currentLang = i18n.language || (window as any).initialLocale;
            const langBase = String(currentLang || '')
                .toLowerCase()
                .split('-')[0];
            const isRtlLang = Boolean(langBase && ['ar', 'he'].includes(langBase));

            setIsRtl(isRtlLang);
            setPosition(isRtlLang ? 'right' : 'left');
        };

        // Initial position update
        updatePositionFromLanguage();

        // Listen for language changes
        i18n.on('languageChanged', updatePositionFromLanguage);
        i18n.on('loaded', updatePositionFromLanguage);
        i18n.on('initialized', updatePositionFromLanguage);

        return () => {
            i18n.off('languageChanged', updatePositionFromLanguage);
            i18n.off('loaded', updatePositionFromLanguage);
            i18n.off('initialized', updatePositionFromLanguage);
        };
    }, []);

    const updatePosition = (_val: LayoutPosition) => {
        const currentLang = i18n.language || (window as any).initialLocale;
        const langBase = String(currentLang || '')
            .toLowerCase()
            .split('-')[0];
        const isRtlLang = Boolean(langBase && ['ar', 'he'].includes(langBase));
        setPosition(isRtlLang ? 'right' : 'left');
    };

    const saveLayoutPosition = () => {
        // Kept for API compatibility; layout side is locale-driven and not persisted.
    };

    // Calculate effective position based on RTL mode
    const effectivePosition: LayoutPosition = isRtl ?
        (position === 'left' ? 'right' : 'left') :
        position;

    return <LayoutContext.Provider value={{ position, effectivePosition, updatePosition, saveLayoutPosition, isRtl }}>{children}</LayoutContext.Provider>;
};

export const useLayout = () => {
    const context = useContext(LayoutContext);
    if (!context) throw new Error('useLayout must be used within LayoutProvider');
    return context;
};
