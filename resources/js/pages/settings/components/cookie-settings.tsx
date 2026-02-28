import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { useState, useEffect, useRef } from 'react';
import { Save, Download } from 'lucide-react';
import { SettingsSection } from '@/components/settings-section';
import { useTranslation } from 'react-i18next';
import { router, usePage } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';

interface CookieSettingsProps {
  settings?: Record<string, string>;
}

export default function CookieSettings({ settings = {} }: CookieSettingsProps) {
  const { t } = useTranslation();
  const pageProps = usePage().props as any;
  
  // Default settings
  const defaultSettings = {
    enableLogging: false,
    strictlyNecessaryCookies: true,
    cookieTitleEn: 'Cookie Consent',
    cookieTitleAr: 'إشعار ملفات تعريف الارتباط',
    strictlyCookieTitleEn: 'Strictly Necessary Cookies',
    strictlyCookieTitleAr: 'ملفات تعريف الارتباط الضرورية',
    cookieDescriptionEn: 'We use cookies to improve your browsing experience, analyze website performance, and provide content tailored to your preferences.',
    cookieDescriptionAr: 'نستخدم ملفات تعريف الارتباط لتحسين تجربة التصفح، وتحليل أداء الموقع، وتقديم محتوى يتناسب مع تفضيلاتك.',
    strictlyCookieDescriptionEn: 'These cookies are essential for the proper functioning of the website and cannot be disabled as they enable core features such as security and accessibility.',
    strictlyCookieDescriptionAr: 'تُعد ملفات تعريف الارتباط هذه ضرورية لعمل الموقع بشكل صحيح، ولا يمكن تعطيلها، حيث تُمكّن الميزات الأساسية مثل الأمان وإمكانية الوصول.',
    contactUsDescriptionEn: 'If you have any questions or concerns regarding our cookie policy, please feel free to contact us.',
    contactUsDescriptionAr: 'إذا كان لديك أي استفسار أو ملاحظات بخصوص سياسة ملفات تعريف الارتباط، يُرجى التواصل معنا.',
    contactUsUrl: 'info@sard.app'
  };
  
  // Combine settings from props and page props
  const settingsData = Object.keys(settings).length > 0 
    ? settings 
    : (pageProps.settings || {});
  
  const getMergedCookie = () => ({
    enableLogging: settingsData.enableLogging === '1' || settingsData.enableLogging === true || defaultSettings.enableLogging,
    strictlyNecessaryCookies: settingsData.strictlyNecessaryCookies === '1' || settingsData.strictlyNecessaryCookies === true || defaultSettings.strictlyNecessaryCookies,
    cookieTitleEn: settingsData.cookieTitleEn || settingsData.cookieTitle || defaultSettings.cookieTitleEn,
    cookieTitleAr: settingsData.cookieTitleAr || defaultSettings.cookieTitleAr,
    strictlyCookieTitleEn: settingsData.strictlyCookieTitleEn || settingsData.strictlyCookieTitle || defaultSettings.strictlyCookieTitleEn,
    strictlyCookieTitleAr: settingsData.strictlyCookieTitleAr || defaultSettings.strictlyCookieTitleAr,
    cookieDescriptionEn: settingsData.cookieDescriptionEn || settingsData.cookieDescription || defaultSettings.cookieDescriptionEn,
    cookieDescriptionAr: settingsData.cookieDescriptionAr || defaultSettings.cookieDescriptionAr,
    strictlyCookieDescriptionEn: settingsData.strictlyCookieDescriptionEn || settingsData.strictlyCookieDescription || defaultSettings.strictlyCookieDescriptionEn,
    strictlyCookieDescriptionAr: settingsData.strictlyCookieDescriptionAr || defaultSettings.strictlyCookieDescriptionAr,
    contactUsDescriptionEn: settingsData.contactUsDescriptionEn || settingsData.contactUsDescription || defaultSettings.contactUsDescriptionEn,
    contactUsDescriptionAr: settingsData.contactUsDescriptionAr || defaultSettings.contactUsDescriptionAr,
    contactUsUrl: settingsData.contactUsUrl || defaultSettings.contactUsUrl,
  });
  const [cookieSettings, setCookieSettings] = useState(() => getMergedCookie());
  const initialValuesRef = useRef(getMergedCookie());

  useEffect(() => {
    if (Object.keys(settingsData).length > 0) {
      const merged = getMergedCookie();
      setCookieSettings(merged);
      initialValuesRef.current = { ...merged };
    }
  }, [settingsData]);

  // Handle cookie settings form changes
  const handleCookieSettingsChange = (field: string, value: string | boolean) => {
    setCookieSettings(prev => ({
      ...prev,
      [field]: value
    }));
  };

  // Handle cookie settings form submission
  const submitCookieSettings = (e: React.FormEvent) => {
    e.preventDefault();
    const init = initialValuesRef.current;
    const changed: Record<string, string | boolean> = {};
    (Object.keys(cookieSettings) as (keyof typeof cookieSettings)[]).forEach((key) => {
      if (cookieSettings[key] !== init[key]) {
        changed[key] = cookieSettings[key];
      }
    });
    if (Object.keys(changed).length === 0) {
      toast.info(t('No changes to save'));
      return;
    }
    router.post(route('settings.cookie.update'), changed, {
      preserveScroll: true,
      onSuccess: (page: any) => {
        initialValuesRef.current = { ...cookieSettings };
        const successMessage = page.props.flash?.success;
        const errorMessage = page.props.flash?.error;
        
        if (successMessage) {
          toast.success(successMessage);
        } else if (errorMessage) {
          toast.error(errorMessage);
        }
      },
      onError: (errors) => {
        const errorMessage = errors.error || Object.values(errors).join(', ') || t('Failed to update cookie settings');
        toast.error(errorMessage);
      }
    });
  };

  // Handle CSV download
  const downloadCookieData = () => {
    window.location.href = route('cookie.consent.download');
  };

  return (
    <SettingsSection
      title={t("Cookie Settings")}
      description={t("Configure cookie consent and privacy settings for your application")}
      action={
        <Button type="submit" form="cookie-settings-form" size="sm">
          <Save className="h-4 w-4 mr-2" />
          {t("Save Changes")}
        </Button>
      }
    >
      <form id="cookie-settings-form" onSubmit={submitCookieSettings} className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Enable Logging Switch */}
          <div className="flex items-center justify-between space-x-2">
            <div className="space-y-0.5">
              <Label htmlFor="enableLogging">{t("Enable Logging")}</Label>
              <p className="text-sm text-muted-foreground">
                {t("Enable cookie activity logging")}
              </p>
            </div>
            <Switch
              id="enableLogging"
              checked={cookieSettings.enableLogging}
              onCheckedChange={(checked) => handleCookieSettingsChange('enableLogging', checked)}
            />
          </div>

          {/* Strictly Necessary Cookies Switch */}
          <div className="flex items-center justify-between space-x-2">
            <div className="space-y-0.5">
              <Label htmlFor="strictlyNecessaryCookies">{t("Strictly Necessary Cookies")}</Label>
              <p className="text-sm text-muted-foreground">
                {t("Enable strictly necessary cookies")}
              </p>
            </div>
            <Switch
              id="strictlyNecessaryCookies"
              checked={cookieSettings.strictlyNecessaryCookies}
              onCheckedChange={(checked) => handleCookieSettingsChange('strictlyNecessaryCookies', checked)}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Cookie Title EN */}
          <div className="grid gap-2">
            <Label htmlFor="cookieTitleEn">{t("Cookie Title (EN)")}</Label>
            <Input
              id="cookieTitleEn"
              type="text"
              value={cookieSettings.cookieTitleEn}
              onChange={(e) => handleCookieSettingsChange('cookieTitleEn', e.target.value)}
              placeholder={t("Enter the main cookie consent title")}
            />
          </div>

          {/* Cookie Title AR */}
          <div className="grid gap-2">
            <Label htmlFor="cookieTitleAr">{t("Cookie Title (AR)")}</Label>
            <Input
              id="cookieTitleAr"
              type="text"
              value={cookieSettings.cookieTitleAr}
              onChange={(e) => handleCookieSettingsChange('cookieTitleAr', e.target.value)}
              placeholder={t("Enter the main cookie consent title")}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Strictly Cookie Title EN */}
          <div className="grid gap-2">
            <Label htmlFor="strictlyCookieTitleEn">{t("Strictly Cookie Title (EN)")}</Label>
            <Input
              id="strictlyCookieTitleEn"
              type="text"
              value={cookieSettings.strictlyCookieTitleEn}
              onChange={(e) => handleCookieSettingsChange('strictlyCookieTitleEn', e.target.value)}
              placeholder={t("Enter the strictly necessary cookies title")}
            />
          </div>

          {/* Strictly Cookie Title AR */}
          <div className="grid gap-2">
            <Label htmlFor="strictlyCookieTitleAr">{t("Strictly Cookie Title (AR)")}</Label>
            <Input
              id="strictlyCookieTitleAr"
              type="text"
              value={cookieSettings.strictlyCookieTitleAr}
              onChange={(e) => handleCookieSettingsChange('strictlyCookieTitleAr', e.target.value)}
              placeholder={t("Enter the strictly necessary cookies title")}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Cookie Description EN */}
          <div className="grid gap-2">
            <Label htmlFor="cookieDescriptionEn">{t("Cookie Description (EN)")}</Label>
            <Textarea
              id="cookieDescriptionEn"
              value={cookieSettings.cookieDescriptionEn}
              onChange={(e) => handleCookieSettingsChange('cookieDescriptionEn', e.target.value)}
              placeholder={t("Enter the cookie consent description")}
              rows={4}
            />
          </div>

          {/* Cookie Description AR */}
          <div className="grid gap-2">
            <Label htmlFor="cookieDescriptionAr">{t("Cookie Description (AR)")}</Label>
            <Textarea
              id="cookieDescriptionAr"
              value={cookieSettings.cookieDescriptionAr}
              onChange={(e) => handleCookieSettingsChange('cookieDescriptionAr', e.target.value)}
              placeholder={t("Enter the cookie consent description")}
              rows={4}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Strictly Cookie Description EN */}
          <div className="grid gap-2">
            <Label htmlFor="strictlyCookieDescriptionEn">{t("Strictly Cookie Description (EN)")}</Label>
            <Textarea
              id="strictlyCookieDescriptionEn"
              value={cookieSettings.strictlyCookieDescriptionEn}
              onChange={(e) => handleCookieSettingsChange('strictlyCookieDescriptionEn', e.target.value)}
              placeholder={t("Enter the strictly necessary cookies description")}
              rows={4}
            />
          </div>

          {/* Strictly Cookie Description AR */}
          <div className="grid gap-2">
            <Label htmlFor="strictlyCookieDescriptionAr">{t("Strictly Cookie Description (AR)")}</Label>
            <Textarea
              id="strictlyCookieDescriptionAr"
              value={cookieSettings.strictlyCookieDescriptionAr}
              onChange={(e) => handleCookieSettingsChange('strictlyCookieDescriptionAr', e.target.value)}
              placeholder={t("Enter the strictly necessary cookies description")}
              rows={4}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Contact Us Description EN */}
          <div className="grid gap-2">
            <Label htmlFor="contactUsDescriptionEn">{t("Contact Us Description (EN)")}</Label>
            <Textarea
              id="contactUsDescriptionEn"
              value={cookieSettings.contactUsDescriptionEn}
              onChange={(e) => handleCookieSettingsChange('contactUsDescriptionEn', e.target.value)}
              placeholder={t("Enter the contact us description for cookie inquiries")}
              rows={3}
            />
          </div>

          {/* Contact Us Description AR */}
          <div className="grid gap-2">
            <Label htmlFor="contactUsDescriptionAr">{t("Contact Us Description (AR)")}</Label>
            <Textarea
              id="contactUsDescriptionAr"
              value={cookieSettings.contactUsDescriptionAr}
              onChange={(e) => handleCookieSettingsChange('contactUsDescriptionAr', e.target.value)}
              placeholder={t("Enter the contact us description for cookie inquiries")}
              rows={3}
            />
          </div>

          {/* Contact Us URL */}
          <div className="grid gap-2">
            <Label htmlFor="contactUsUrl">{t("Contact Us URL")}</Label>
            <Input
              id="contactUsUrl"
              type="url"
              value={cookieSettings.contactUsUrl}
              onChange={(e) => handleCookieSettingsChange('contactUsUrl', e.target.value)}
              placeholder={t("Enter the contact us URL for cookie inquiries")}
            />
          </div>
        </div>

        {/* Download CSV Section */}
        <div className="pt-4 border-t">
          <div className="flex items-center justify-between">
            <div>
              <h4 className="text-sm font-medium">{t("Download Accepted Cookies")}</h4>
              <p className="text-sm text-muted-foreground">
                Download a CSV file of accepted cookie preferences
              </p>
            </div>
            <Button type="button" variant="outline" size="sm" onClick={downloadCookieData}>
              <Download className="h-4 w-4 mr-2" />
              Download CSV
            </Button>
          </div>
        </div>
      </form>
    </SettingsSection>
  );
}