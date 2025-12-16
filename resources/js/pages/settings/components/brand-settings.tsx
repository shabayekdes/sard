import MediaPicker from '@/components/MediaPicker';
import { SettingsSection } from '@/components/settings-section';
import { ThemePreview } from '@/components/theme-preview';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout, type LayoutPosition } from '@/contexts/LayoutContext';
import { useSidebarSettings } from '@/contexts/SidebarContext';
import { useAppearance, type Appearance, type ThemeColor } from '@/hooks/use-appearance';
import { getCookie } from '@/utils/cookies';
import { getImagePath } from '@/utils/helpers';
import { router, usePage } from '@inertiajs/react';
import { Check, FileText, Layout, Moon, Palette, Save, SidebarIcon, Upload } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { toast } from 'sonner';
import { Separator } from '@/components/ui/separator';

// ✅ Import the pure settings + types from the new file
import { getBrandSettings, type BrandSettings } from '@/utils/brandSettings';

interface BrandSettingsProps {
    userSettings?: Record<string, string>;
}

export default function BrandSettings({ userSettings }: BrandSettingsProps) {
    const { t } = useTranslation();
    const { props } = usePage();
    const currentGlobalSettings = (props as any).globalSettings;

    const { updateBrandSettings } = useBrand();

    const [settings, setSettings] = useState<BrandSettings>(() => getBrandSettings(currentGlobalSettings || userSettings, currentGlobalSettings));

    const [isLoading, setIsLoading] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [activeSection, setActiveSection] = useState<'logos' | 'text' | 'theme'>('logos');

    // State to track logo errors
    const [logoErrors, setLogoErrors] = useState({
        logoDark: false,
        logoLight: false,
        favicon: false,
    });

    // Theme hooks
    const { updateAppearance, updateThemeColor, updateCustomColor, saveThemeSettings } = useAppearance();

    const { updatePosition, saveLayoutPosition } = useLayout();
    const { updateVariant, updateStyle, saveSidebarSettings } = useSidebarSettings();

    // Load settings when globalSettings change (but not while saving)
    useEffect(() => {
        if (isSaving) return;

        const newBrandSettings = getBrandSettings(currentGlobalSettings || userSettings, currentGlobalSettings);
        setSettings(newBrandSettings);

        // Sync sidebar settings from cookies or localStorage
        try {
            const isDemo = currentGlobalSettings?.is_demo || false;
            let sidebarSettings: string | null = null;

            if (isDemo) {
                sidebarSettings = getCookie('sidebarSettings');
            } else {
                sidebarSettings = localStorage.getItem('sidebarSettings');
            }

            if (sidebarSettings) {
                const parsedSettings = JSON.parse(sidebarSettings);
                setSettings((prev) => ({
                    ...prev,
                    sidebarVariant: parsedSettings.variant || prev.sidebarVariant,
                    sidebarStyle: parsedSettings.style || prev.sidebarStyle,
                }));
            }
        } catch (error) {
            console.error('Error loading sidebar settings', error);
        }
    }, [currentGlobalSettings, userSettings, isSaving]);

    // Handle input changes
    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setSettings((prev) => ({ ...prev, [name]: value }));

        // Update brand context if the input is for a logo
        if (['logoLight', 'logoDark', 'favicon'].includes(name)) {
            updateBrandSettings({ [name]: value } as Partial<BrandSettings>);
        }
    };

    // Handle media picker selection
    const handleMediaSelect = (name: string, url: string) => {
        setSettings((prev) => ({ ...prev, [name]: url }));
        updateBrandSettings({ [name]: url } as Partial<BrandSettings>);
    };

    // Handle theme color change
    const handleThemeColorChange = (color: ThemeColor) => {
        setSettings((prev) => ({ ...prev, themeColor: color }));
        updateThemeColor(color);
    };

    // Handle custom color change
    const handleCustomColorChange = (color: string) => {
        setSettings((prev) => ({ ...prev, customColor: color }));
        updateCustomColor(color, true);
    };

    // Handle sidebar variant change
    const handleSidebarVariantChange = (variant: string) => {
        setSettings((prev) => ({ ...prev, sidebarVariant: variant }));
        updateVariant(variant as any);
    };

    // Handle sidebar style change
    const handleSidebarStyleChange = (style: string) => {
        setSettings((prev) => ({ ...prev, sidebarStyle: style }));
        updateStyle(style);
    };

    // Handle layout direction change
    const handleLayoutDirectionChange = (direction: LayoutPosition) => {
        setSettings((prev) => ({ ...prev, layoutDirection: direction }));
        updatePosition(direction);
    };

    // Handle theme mode change
    const handleThemeModeChange = (mode: Appearance) => {
        setSettings((prev) => ({ ...prev, themeMode: mode }));
        updateAppearance(mode);

        // Re-apply current theme color immediately
        setTimeout(() => {
            updateThemeColor(settings.themeColor);
            if (settings.themeColor === 'custom') {
                updateCustomColor(settings.customColor);
            }
        }, 0);
    };

    // Save settings
    const saveSettings = () => {
        setIsLoading(true);
        setIsSaving(true);

        // Apply theme settings immediately
        updateThemeColor(settings.themeColor);
        if (settings.themeColor === 'custom') {
            updateCustomColor(settings.customColor);
        }
        updateAppearance(settings.themeMode);
        updatePosition(settings.layoutDirection);

        // Sidebar settings
        updateVariant(settings.sidebarVariant as any);
        updateStyle(settings.sidebarStyle);

        // Persist to cookies/storage
        saveThemeSettings();
        saveLayoutPosition();
        saveSidebarSettings();

        // Update brand context with all settings
        updateBrandSettings({
            logoLight: settings.logoLight,
            logoDark: settings.logoDark,
            favicon: settings.favicon,
            themeColor: settings.themeColor,
            customColor: settings.customColor,
            themeMode: settings.themeMode,
            layoutDirection: settings.layoutDirection,
            sidebarVariant: settings.sidebarVariant,
            sidebarStyle: settings.sidebarStyle,
            titleText: settings.titleText,
            footerText: settings.footerText,
        });

        // Save to database (Inertia)
        router.post(
            route('settings.brand.update'),
            { settings },
            {
                preserveScroll: true,
                onSuccess: (page) => {
                    setIsLoading(false);
                    const successMessage = (page.props as any).flash?.success;
                    const errorMessage = (page.props as any).flash?.error;

                    if (successMessage) {
                        toast.success(successMessage);
                        setTimeout(() => setIsSaving(false), 500);
                    } else if (errorMessage) {
                        toast.error(errorMessage);
                        setIsSaving(false);
                    } else {
                        setIsSaving(false);
                    }
                },
                onError: (errors) => {
                    setIsLoading(false);
                    setIsSaving(false);
                    const errorMessage = (errors as any).error || Object.values(errors as any).join(', ') || t('Failed to save brand settings');
                    toast.error(errorMessage);
                },
            },
        );
    };

    return (
        <SettingsSection
            title={t('Brand Settings')}
            description={t("Customize your application's branding and appearance")}
            action={
                <Button onClick={saveSettings} disabled={isLoading} size="sm">
                    <Save className="mr-2 h-4 w-4" />
                    {isLoading ? t('Saving...') : t('Save Changes')}
                </Button>
            }
        >
            <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div className="lg:col-span-2">
                    <div className="mb-6 flex space-x-2">
                        <Button
                            variant={activeSection === 'logos' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => setActiveSection('logos')}
                            className="flex-1"
                        >
                            <Upload className="mr-2 h-4 w-4" />
                            {t('Logos')}
                        </Button>

                        <Button
                            variant={activeSection === 'text' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => setActiveSection('text')}
                            className="flex-1"
                        >
                            <FileText className="mr-2 h-4 w-4" />
                            {t('Text')}
                        </Button>

                        <Button
                            variant={activeSection === 'theme' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => setActiveSection('theme')}
                            className="flex-1"
                        >
                            <Palette className="mr-2 h-4 w-4" />
                            {t('Theme')}
                        </Button>
                    </div>

                    {/* Logos Section */}
                    {activeSection === 'logos' && (
                        <div className="space-y-6">
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div className="space-y-3">
                                    <Label>{t('Logo Dark')}</Label>
                                    <div className="flex flex-col gap-3">
                                        <div className="bg-muted/30 flex h-32 items-center justify-center rounded-md border p-4">
                                            {settings.logoDark && !logoErrors.logoDark ? (
                                                <img
                                                    key={`preview-dark-${Date.now()}`}
                                                    src={getImagePath(settings.logoDark)}
                                                    alt="Dark Logo"
                                                    className="max-h-full max-w-full object-contain"
                                                    onError={() => setLogoErrors((prev) => ({ ...prev, logoDark: true }))}
                                                />
                                            ) : (
                                                <div className="text-muted-foreground flex flex-col items-center gap-2">
                                                    <div className="bg-muted flex h-12 w-24 items-center justify-center rounded border border-dashed">
                                                        <span className="text-muted-foreground font-semibold">{t('Logo')}</span>
                                                    </div>
                                                    <span className="text-xs">
                                                        {logoErrors.logoDark ? 'Failed to load image' : 'No logo selected'}
                                                    </span>
                                                </div>
                                            )}
                                        </div>

                                        <MediaPicker
                                            label=""
                                            value={settings.logoDark}
                                            onChange={(url) => handleMediaSelect('logoDark', url)}
                                            placeholder="Select dark mode logo..."
                                            showPreview={false}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-3">
                                    <Label>{t('Logo Light')}</Label>
                                    <div className="flex flex-col gap-3">
                                        <div className="bg-muted/30 flex h-32 items-center justify-center rounded-md border p-4">
                                            {settings.logoLight && !logoErrors.logoLight ? (
                                                <img
                                                    key={`preview-light-${Date.now()}`}
                                                    src={getImagePath(settings.logoLight)}
                                                    alt="Light Logo"
                                                    className="max-h-full max-w-full object-contain"
                                                    onError={() => setLogoErrors((prev) => ({ ...prev, logoLight: true }))}
                                                />
                                            ) : (
                                                <div className="text-muted-foreground flex flex-col items-center gap-2">
                                                    <div className="bg-muted flex h-12 w-24 items-center justify-center rounded border border-dashed">
                                                        <span className="text-muted-foreground font-semibold">{t('Logo')}</span>
                                                    </div>
                                                    <span className="text-xs">
                                                        {logoErrors.logoLight ? 'Failed to load image' : 'No logo selected'}
                                                    </span>
                                                </div>
                                            )}
                                        </div>

                                        <MediaPicker
                                            label=""
                                            value={settings.logoLight}
                                            onChange={(url) => handleMediaSelect('logoLight', url)}
                                            placeholder="Select light mode logo..."
                                            showPreview={false}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-3">
                                    <Label>{t('Favicon')}</Label>
                                    <div className="flex flex-col gap-3">
                                        <div className="bg-muted/30 flex h-20 items-center justify-center rounded-md border p-4">
                                            {settings.favicon && !logoErrors.favicon ? (
                                                <img
                                                    key={`preview-favicon-${Date.now()}`}
                                                    src={getImagePath(settings.favicon)}
                                                    alt="Favicon"
                                                    className="h-16 w-16 object-contain"
                                                    onError={() => setLogoErrors((prev) => ({ ...prev, favicon: true }))}
                                                />
                                            ) : (
                                                <div className="text-muted-foreground flex flex-col items-center gap-1">
                                                    <div className="bg-muted flex h-10 w-10 items-center justify-center rounded border border-dashed">
                                                        <span className="text-muted-foreground text-xs font-semibold">{t('Icon')}</span>
                                                    </div>
                                                    <span className="text-xs">
                                                        {logoErrors.favicon ? 'Failed to load image' : 'No favicon selected'}
                                                    </span>
                                                </div>
                                            )}
                                        </div>

                                        <MediaPicker
                                            label=""
                                            value={settings.favicon}
                                            onChange={(url) => handleMediaSelect('favicon', url)}
                                            placeholder="Select favicon..."
                                            showPreview={false}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Text Section */}
                    {activeSection === 'text' && (
                        <div className="space-y-6">
                            <div className="grid grid-cols-1 gap-6">
                                <div className="space-y-3">
                                    <Label htmlFor="titleText">{t('Title Text')}</Label>
                                    <Input
                                        id="titleText"
                                        name="titleText"
                                        value={settings.titleText}
                                        onChange={handleInputChange}
                                        placeholder="WorkDo"
                                    />
                                    <p className="text-muted-foreground text-xs">{t('Application title displayed in the browser tab')}</p>
                                </div>

                                <div className="space-y-3">
                                    <Label htmlFor="footerText">{t('Footer Text')}</Label>
                                    <Input
                                        id="footerText"
                                        name="footerText"
                                        value={settings.footerText}
                                        onChange={handleInputChange}
                                        placeholder="© 2025 WorkDo. All rights reserved."
                                    />
                                    <p className="text-muted-foreground text-xs">{t('Text displayed in the footer')}</p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Theme Section */}
                    {activeSection === 'theme' && (
                        <div className="space-y-6">
                            <div className="flex flex-col space-y-8">
                                {/* Theme Color Section */}
                                <div className="space-y-4">
                                    <div className="flex items-center">
                                        <Palette className="text-muted-foreground mr-2 h-5 w-5" />
                                        <h3 className="text-base font-medium">{t('Theme Color')}</h3>
                                    </div>
                                    <Separator className="my-2" />

                                    <div className="grid grid-cols-6 gap-2">
                                        {Object.entries({
                                            blue: '#3b82f6',
                                            green: '#10b981',
                                            purple: '#8b5cf6',
                                            orange: '#f97316',
                                            red: '#ef4444',
                                        }).map(([color, hex]) => (
                                            <Button
                                                key={color}
                                                type="button"
                                                variant={settings.themeColor === color ? 'default' : 'outline'}
                                                className="relative h-8 w-full p-0"
                                                style={{ backgroundColor: settings.themeColor === color ? hex : 'transparent' }}
                                                onClick={() => handleThemeColorChange(color as ThemeColor)}
                                            >
                                                <span className="absolute inset-1 rounded-sm" style={{ backgroundColor: hex }} />
                                            </Button>
                                        ))}
                                        <Button
                                            type="button"
                                            variant={settings.themeColor === 'custom' ? 'default' : 'outline'}
                                            className="relative h-8 w-full p-0"
                                            style={{ backgroundColor: settings.themeColor === 'custom' ? settings.customColor : 'transparent' }}
                                            onClick={() => handleThemeColorChange('custom')}
                                        >
                                            <span className="absolute inset-1 rounded-sm" style={{ backgroundColor: settings.customColor }} />
                                        </Button>
                                    </div>

                                    {settings.themeColor === 'custom' && (
                                        <div className="mt-4 space-y-2">
                                            <Label htmlFor="customColor">{t('Custom Color')}</Label>
                                            <div className="flex gap-2">
                                                <div className="relative">
                                                    <Input
                                                        id="colorPicker"
                                                        type="color"
                                                        value={settings.customColor}
                                                        onChange={(e) => handleCustomColorChange(e.target.value)}
                                                        className="absolute inset-0 cursor-pointer opacity-0"
                                                    />
                                                    <div
                                                        className="h-10 w-10 cursor-pointer rounded border"
                                                        style={{ backgroundColor: settings.customColor }}
                                                    />
                                                </div>
                                                <Input
                                                    id="customColor"
                                                    name="customColor"
                                                    type="text"
                                                    value={settings.customColor}
                                                    onChange={(e) => handleCustomColorChange(e.target.value)}
                                                    placeholder="#3b82f6"
                                                />
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Sidebar Section */}
                                <div className="space-y-4">
                                    <div className="flex items-center">
                                        <SidebarIcon className="text-muted-foreground mr-2 h-5 w-5" />
                                        <h3 className="text-base font-medium">{t('Sidebar')}</h3>
                                    </div>
                                    <Separator className="my-2" />

                                    <div className="space-y-6">
                                        <div>
                                            <Label className="mb-2 block">{t('Sidebar Variant')}</Label>
                                            <div className="grid grid-cols-3 gap-3">
                                                {['inset', 'floating', 'minimal'].map((variant) => (
                                                    <Button
                                                        key={variant}
                                                        type="button"
                                                        variant={settings.sidebarVariant === variant ? 'default' : 'outline'}
                                                        className="h-10 justify-start"
                                                        style={{
                                                            backgroundColor:
                                                                settings.sidebarVariant === variant
                                                                    ? settings.themeColor === 'custom'
                                                                        ? settings.customColor
                                                                        : null
                                                                    : 'transparent',
                                                        }}
                                                        onClick={() => handleSidebarVariantChange(variant)}
                                                    >
                                                        {variant.charAt(0).toUpperCase() + variant.slice(1)}
                                                        {settings.sidebarVariant === variant && <Check className="ml-2 h-4 w-4" />}
                                                    </Button>
                                                ))}
                                            </div>
                                        </div>

                                        <div>
                                            <Label className="mb-2 block">{t('Sidebar Style')}</Label>
                                            <div className="grid grid-cols-3 gap-3">
                                                {[
                                                    { id: 'plain', name: 'Plain' },
                                                    { id: 'colored', name: 'Colored' },
                                                    { id: 'gradient', name: 'Gradient' },
                                                ].map((style) => (
                                                    <Button
                                                        key={style.id}
                                                        type="button"
                                                        variant={settings.sidebarStyle === style.id ? 'default' : 'outline'}
                                                        className="h-10 justify-start"
                                                        style={{
                                                            backgroundColor:
                                                                settings.sidebarStyle === style.id
                                                                    ? settings.themeColor === 'custom'
                                                                        ? settings.customColor
                                                                        : null
                                                                    : 'transparent',
                                                        }}
                                                        onClick={() => handleSidebarStyleChange(style.id)}
                                                    >
                                                        {style.name}
                                                        {settings.sidebarStyle === style.id && <Check className="ml-2 h-4 w-4" />}
                                                    </Button>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Layout Section */}
                                <div className="space-y-4">
                                    <div className="flex items-center">
                                        <Layout className="text-muted-foreground mr-2 h-5 w-5" />
                                        <h3 className="text-base font-medium">{t('Layout')}</h3>
                                    </div>
                                    <Separator className="my-2" />

                                    <div className="space-y-2">
                                        <Label className="mb-2 block">{t('Layout Direction')}</Label>
                                        <div className="grid grid-cols-2 gap-2">
                                            <Button
                                                type="button"
                                                variant={settings.layoutDirection === 'left' ? 'default' : 'outline'}
                                                className="h-10 justify-start"
                                                style={{
                                                    backgroundColor:
                                                        settings.layoutDirection === 'left'
                                                            ? settings.themeColor === 'custom'
                                                                ? settings.customColor
                                                                : null
                                                            : 'transparent',
                                                }}
                                                onClick={() => handleLayoutDirectionChange('left')}
                                            >
                                                {t('Left-to-Right')}
                                                {settings.layoutDirection === 'left' && <Check className="ml-2 h-4 w-4" />}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={settings.layoutDirection === 'right' ? 'default' : 'outline'}
                                                className="h-10 justify-start"
                                                style={{
                                                    backgroundColor:
                                                        settings.layoutDirection === 'right'
                                                            ? settings.themeColor === 'custom'
                                                                ? settings.customColor
                                                                : null
                                                            : 'transparent',
                                                }}
                                                onClick={() => handleLayoutDirectionChange('right')}
                                            >
                                                {t('Right-to-Left')}
                                                {settings.layoutDirection === 'right' && <Check className="ml-2 h-4 w-4" />}
                                            </Button>
                                        </div>
                                    </div>
                                </div>

                                {/* Mode Section */}
                                <div className="space-y-4">
                                    <div className="flex items-center">
                                        <Moon className="text-muted-foreground mr-2 h-5 w-5" />
                                        <h3 className="text-base font-medium">{t('Theme Mode')}</h3>
                                    </div>
                                    <Separator className="my-2" />

                                    <div className="space-y-2">
                                        <div className="grid grid-cols-3 gap-2">
                                            <Button
                                                type="button"
                                                variant={settings.themeMode === 'light' ? 'default' : 'outline'}
                                                className="h-10 justify-start"
                                                style={{
                                                    backgroundColor:
                                                        settings.themeMode === 'light'
                                                            ? settings.themeColor === 'custom'
                                                                ? settings.customColor
                                                                : null
                                                            : 'transparent',
                                                }}
                                                onClick={() => handleThemeModeChange('light')}
                                            >
                                                {t('Light')}
                                                {settings.themeMode === 'light' && <Check className="ml-2 h-4 w-4" />}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={settings.themeMode === 'dark' ? 'default' : 'outline'}
                                                className="h-10 justify-start"
                                                style={{
                                                    backgroundColor:
                                                        settings.themeMode === 'dark'
                                                            ? settings.themeColor === 'custom'
                                                                ? settings.customColor
                                                                : null
                                                            : 'transparent',
                                                }}
                                                onClick={() => handleThemeModeChange('dark')}
                                            >
                                                {t('Dark')}
                                                {settings.themeMode === 'dark' && <Check className="ml-2 h-4 w-4" />}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant={settings.themeMode === 'system' ? 'default' : 'outline'}
                                                className="h-10 justify-start"
                                                style={{
                                                    backgroundColor:
                                                        settings.themeMode === 'system'
                                                            ? settings.themeColor === 'custom'
                                                                ? settings.customColor
                                                                : null
                                                            : 'transparent',
                                                }}
                                                onClick={() => handleThemeModeChange('system')}
                                            >
                                                {t('System')}
                                                {settings.themeMode === 'system' && <Check className="ml-2 h-4 w-4" />}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                {/* Preview Column */}
                <div className="lg:col-span-1">
                    <div className="sticky top-20 space-y-6">
                        <div className="rounded-md border p-4">
                            <div className="mb-4 flex items-center gap-2">
                                <Palette className="h-4 w-4" />
                                <h3 className="font-medium">{t('Live Preview')}</h3>
                            </div>

                            <ThemePreview />

                            <div className="mt-4 border-t pt-4">
                                <div className="text-muted-foreground mb-2 text-xs">
                                    {t('Title:')} <span className="text-foreground font-medium">{settings.titleText}</span>
                                </div>
                                <div className="text-muted-foreground text-xs">
                                    {t('Footer:')} <span className="text-foreground font-medium">{settings.footerText}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SettingsSection>
    );
}
