import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import ReactCountryFlag from 'react-country-flag';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Globe } from 'lucide-react';
import { Loader } from '@/components/ui/loader';
import { usePage } from '@inertiajs/react';
import { hasRole } from '@/utils/authorization';
import { useLayout } from '@/contexts/LayoutContext';
import { CreateLanguageModal } from '@/components/create-language-modal';

interface Language {
    code: string;
    name: string;
    countryCode: string;
}

// Import languages from the JSON file
import languageData from '@lang/language.json';

export const LanguageSwitcher: React.FC = () => {
    const { i18n, t } = useTranslation();
    const { auth } = usePage().props as any;
    const { updatePosition } = useLayout();
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [isSwitching, setIsSwitching] = useState(false);
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const currentLanguage = React.useMemo(() => {
        const currentLang = i18n.language;
        // Try exact match first
        let found = languageData.find(lang => lang.code === currentLang);

        // If not found, try case-insensitive match
        if (!found) {
            found = languageData.find(lang => lang.code.toLowerCase() === currentLang.toLowerCase());
        }

        // If still not found, try matching the base language (e.g., 'pt' for 'pt-BR')
        if (!found) {
            const baseLang = currentLang.split('-')[0];
            found = languageData.find(lang => lang.code.split('-')[0] === baseLang);
        }

        return found || languageData[0];
    }, [i18n.language]);

    const isAuthenticated = auth?.user;
    const userRoles = auth?.user?.roles?.map((role: any) => role.name) || [];
    const isSuperAdmin = isAuthenticated && hasRole('superadmin', userRoles);

    // RTL languages list
    const rtlLanguages = ['ar', 'he'];

    const handleLanguageChange = async (languageCode: string) => {
        if (isSwitching) return;
        setIsSwitching(true);
        setDropdownOpen(false);
        const overlayStart = Date.now();
        window.dispatchEvent(new CustomEvent('language-switch-start'));

        // Let the overlay paint (flushSync in overlay + one frame)
        await new Promise((resolve) => requestAnimationFrame(resolve));

        try {
            const base = languageCode.split('-')[0];
            const localeForCookie = base === 'ar' ? 'ar' : base === 'he' ? 'he' : 'en';

            const maxAge = 60 * 60 * 24 * 30;
            document.cookie = `app_language=${localeForCookie}; path=/; max-age=${maxAge}; SameSite=Lax`;

            await i18n.changeLanguage(languageCode);

            if (isAuthenticated && ['en', 'ar'].includes(localeForCookie)) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch(route('user.locale.update'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ locale: localeForCookie }),
                }).catch(() => {});
            }

            const isRtl = rtlLanguages.includes(languageCode);
            document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
            document.documentElement.setAttribute('dir', isRtl ? 'rtl' : 'ltr');

            const newPosition = isRtl ? 'right' : 'left';
            updatePosition(newPosition as 'left' | 'right');

            window.dispatchEvent(new CustomEvent('languageChanged', {
                detail: { language: languageCode, direction: isRtl ? 'rtl' : 'ltr' }
            }));
            window.dispatchEvent(new Event('resize'));
        } catch (error) {
            console.error('Error changing language:', error);
        } finally {
            // Keep overlay visible at least 400ms so it's noticeable
            const minDisplay = 400;
            const elapsed = Date.now() - overlayStart;
            if (elapsed < minDisplay) {
                await new Promise((r) => setTimeout(r, minDisplay - elapsed));
            }
            setIsSwitching(false);
            window.dispatchEvent(new CustomEvent('language-switch-finish'));
        }
    };

    return (
        <>
            <DropdownMenu open={dropdownOpen} onOpenChange={(open) => { if (!open && isSwitching) return; setDropdownOpen(open); }}>
                <DropdownMenuTrigger asChild disabled={isSwitching}>
                    <Button variant="ghost" className="flex items-center gap-2 h-8 rounded-md" disabled={isSwitching}>
                        {isSwitching ? (
                            <Loader size="sm" className="shrink-0" />
                        ) : (
                            <Globe className="h-4 w-4 shrink-0" />
                        )}
                        <span className="text-sm font-medium hidden md:inline-block">
                            {isSwitching ? '...' : currentLanguage.name}
                        </span>
                        {!isSwitching && (
                            <ReactCountryFlag
                                countryCode={currentLanguage.countryCode}
                                svg
                                style={{
                                    width: '1.2em',
                                    height: '1.2em',
                                }}
                            />
                        )}
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="w-56" align="end" forceMount onCloseAutoFocus={(e) => { if (isSwitching) e.preventDefault(); }}>
                    {isSwitching ? (
                        <div className="flex items-center justify-center gap-2 py-6 px-4">
                            <Loader size="sm" />
                            <span className="text-sm text-muted-foreground">{t('Switching...')}</span>
                        </div>
                    ) : (
                        <DropdownMenuGroup>
                            {languageData
                                .filter((language: any) => language.enabled !== false)
                                .map((language) => (
                                    <DropdownMenuItem
                                        key={language.code}
                                        onClick={() => handleLanguageChange(language.code)}
                                        className="flex items-center gap-2"
                                    >
                                        <ReactCountryFlag
                                            countryCode={language.countryCode}
                                            svg
                                            style={{
                                                width: '1.2em',
                                                height: '1.2em',
                                            }}
                                        />
                                        <span>{language.name}</span>
                                    </DropdownMenuItem>
                                ))}
                        </DropdownMenuGroup>
                    )}
                    {isSuperAdmin && !isSwitching && (
                        <>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                onClick={() => setShowCreateModal(true)}
                                className="justify-center text-primary font-semibold cursor-pointer"
                            >
                                Create Language
                            </DropdownMenuItem>
                            <DropdownMenuItem asChild className="justify-center text-primary font-semibold cursor-pointer">
                                <a href={route('manage-language')} rel="noopener noreferrer">
                                    Manage Language
                                </a>
                            </DropdownMenuItem>
                        </>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <CreateLanguageModal
                open={showCreateModal}
                onOpenChange={setShowCreateModal}
            />
        </>
    );
};
