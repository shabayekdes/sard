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
import { usePage, router } from '@inertiajs/react';
import { hasRole } from '@/utils/authorization';
import { useLayout } from '@/contexts/LayoutContext';
import { CreateLanguageModal } from '@/components/create-language-modal';

interface Language {
    code: string;
    name: string;
    countryCode: string;
}

// Import languages from the JSON file
import languageData from '@/../../resources/lang/language.json';

export const LanguageSwitcher: React.FC = () => {
    const { i18n } = useTranslation();
    const { auth } = usePage().props as any;
    const { updatePosition } = useLayout();
    const [showCreateModal, setShowCreateModal] = useState(false);
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
        try {
            // Change the language
            await i18n.changeLanguage(languageCode);

            // Determine if the new language is RTL
            const isRtl = rtlLanguages.includes(languageCode);
            const newDirection = isRtl ? 'right' : 'left';

            // Update document direction immediately
            document.documentElement.dir = newDirection;
            document.documentElement.setAttribute('dir', newDirection);

            // Update layout position in context
            updatePosition(newDirection as 'left' | 'right');

            // Force a re-render by dispatching a custom event
            window.dispatchEvent(new CustomEvent('languageChanged', {
                detail: { language: languageCode, direction: newDirection }
            }));

            // Force layout recalculation
            window.dispatchEvent(new Event('resize'));

        } catch (error) {
            console.error('Error changing language:', error);
        }
    };

    return (
        <>
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="flex items-center gap-2 h-8 rounded-md">
                    <Globe className="h-4 w-4" />
                    <span className="text-sm font-medium hidden md:inline-block">
                        {currentLanguage.name}
                    </span>
                    <ReactCountryFlag
                        countryCode={currentLanguage.countryCode}
                        svg
                        style={{
                            width: '1.2em',
                            height: '1.2em',
                        }}
                    />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56" align="end" forceMount>
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
                {isSuperAdmin && (
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
