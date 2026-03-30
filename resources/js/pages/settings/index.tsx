import { PageTemplate } from '@/components/page-template';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { useEffect, useRef, useState, type ReactNode } from 'react';
import { Settings as SettingsIcon, Building, DollarSign, Users, RefreshCw, Palette, BookOpen, Award, FileText, Mail, Bell, Link2, CreditCard, Calendar, HardDrive, Shield, Bot, Cookie, Search, Webhook, Wallet, MessageSquare, Slack, Phone } from 'lucide-react';
import { ScrollArea } from '@/components/ui/scroll-area';
import SystemSettings from './components/system-settings';
import { usePage } from '@inertiajs/react';

import CurrencySettings from './components/currency-settings';

import BrandSettings from './components/brand-settings';
import EmailSettings from './components/email-settings';
import PaymentSettings from './components/payment-settings';
import StorageSettings from './components/storage-settings';
import RecaptchaSettings from './components/recaptcha-settings';
import ChatGptSettings from './components/chatgpt-settings';
import CookieSettings from './components/cookie-settings';
import SeoSettings from './components/seo-settings';
import CacheSettings from './components/cache-settings';
import WebhookSettings from './components/webhook-settings';
import EmailNotificationSettings from './components/email-notification-settings';
import SlackSettings from './components/slack-settings';
import TwilioNotificationSettings from './components/twilio-settings';
import GoogleCalendarSettings from './components/google-calendar-settings';
import { Toaster } from '@/components/ui/toaster';
import { useTranslation } from 'react-i18next';
import { useLayout } from '@/contexts/LayoutContext';

/** Permissions a company user may access on the settings page (SaaS users use any granted permission). */
const COMPANY_SETTINGS_PERMISSIONS = new Set([
    'manage-system-settings',
    'manage-brand-settings',
    'manage-currency-settings',
    'manage-email-notifications',
    'manage-payment-settings',
]);

type SettingsSidebarNavItem = {
    title: string;
    href: string;
    icon: ReactNode;
    condition: () => boolean;
};

export default function Settings() {
    const { t } = useTranslation();
    const { position } = useLayout();

    const { systemSettings = {}, cacheSize = '0.00', timezones = {}, dateFormats = {}, timeFormats = {}, paymentSettings = {}, webhooks = [], auth = {}, emailTemplates = [], slackSettings = {}, twilioSettings = {}, notificationTemplates = [], countries = [], taxRates = [] } = usePage().props as any;
    const [activeSection, setActiveSection] = useState('system-settings');

    function settingsSidebarCondition(permission: string | undefined): boolean {
        if (!permission || !auth.permissions?.includes(permission)) {
            return false;
        }
        const userType = auth.user?.type;
        const isSaas = userType === 'superadmin' || userType === 'super admin';
        const isCompany = userType === 'company';
        if (isSaas) {
            return true;
        }
        if (isCompany) {
            return COMPANY_SETTINGS_PERMISSIONS.has(permission);
        }
        return false;
    }

    const sidebarNavItems: SettingsSidebarNavItem[] = [
        {
            title: t('System Settings'),
            href: '#system-settings',
            icon: <SettingsIcon className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-system-settings'),
        },
        {
            title: t('Brand Settings'),
            href: '#brand-settings',
            icon: <Palette className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-brand-settings'),
        },
        {
            title: t('Currency Settings'),
            href: '#currency-settings',
            icon: <DollarSign className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-currency-settings'),
        },
        {
            title: t('Email Settings'),
            href: '#email-settings',
            icon: <Mail className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-email-settings'),
        },
        {
            title: t('Email Notification Settings'),
            href: '#email-notification-settings',
            icon: <Bell className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-email-notifications'),
        },
        {
            title: t('Slack Settings'),
            href: '#slack-settings',
            icon: <Slack className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-slack-notifications'),
        },
        {
            title: t('Twilio Settings'),
            href: '#twilio-settings',
            icon: <Phone className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-twilio-notifications'),
        },
        {
            title: t('Payment Settings'),
            href: '#payment-settings',
            icon: <CreditCard className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-payment-settings'),
        },
        {
            title: t('Storage Settings'),
            href: '#storage-settings',
            icon: <HardDrive className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-storage-settings'),
        },
        {
            title: t('ReCaptcha Settings'),
            href: '#recaptcha-settings',
            icon: <Shield className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-recaptcha-settings'),
        },
        {
            title: t('Chat GPT Settings'),
            href: '#chatgpt-settings',
            icon: <Bot className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-chatgpt-settings'),
        },
        {
            title: t('Cookie Settings'),
            href: '#cookie-settings',
            icon: <Cookie className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-cookie-settings'),
        },
        {
            title: t('SEO Settings'),
            href: '#seo-settings',
            icon: <Search className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-seo-settings'),
        },
        {
            title: t('Cache Settings'),
            href: '#cache-settings',
            icon: <HardDrive className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-cache-settings'),
        },
        {
            title: t('Google Calendar Settings'),
            href: '#google-calendar-settings',
            icon: <Calendar className="h-4 w-4 mr-2" />,
            condition: () => settingsSidebarCondition('manage-google-calendar-settings'),
        },
    ];

    // Refs for each section
    const systemSettingsRef = useRef<HTMLDivElement>(null);
    const brandSettingsRef = useRef<HTMLDivElement>(null);

    const currencySettingsRef = useRef<HTMLDivElement>(null);
    const emailSettingsRef = useRef<HTMLDivElement>(null);
    const paymentSettingsRef = useRef<HTMLDivElement>(null);
    const storageSettingsRef = useRef<HTMLDivElement>(null);
    const recaptchaSettingsRef = useRef<HTMLDivElement>(null);
    const chatgptSettingsRef = useRef<HTMLDivElement>(null);
    const cookieSettingsRef = useRef<HTMLDivElement>(null);
    const seoSettingsRef = useRef<HTMLDivElement>(null);
    const cacheSettingsRef = useRef<HTMLDivElement>(null);
    const webhookSettingsRef = useRef<HTMLDivElement>(null);
    const emailNotificationSettingsRef = useRef<HTMLDivElement>(null);
    const slackSettingsRef = useRef<HTMLDivElement>(null);
    const twilioSettingsRef = useRef<HTMLDivElement>(null);
    const googleCalendarSettingsRef = useRef<HTMLDivElement>(null);




    // Smart scroll functionality
    useEffect(() => {
        const handleScroll = () => {
            const scrollPosition = window.scrollY + 100; // Add offset for better UX

            // Get positions of each section
            const systemSettingsPosition = systemSettingsRef.current?.offsetTop || 0;
            const brandSettingsPosition = brandSettingsRef.current?.offsetTop || 0;

            const currencySettingsPosition = currencySettingsRef.current?.offsetTop || 0;
            const emailSettingsPosition = emailSettingsRef.current?.offsetTop || 0;
            const paymentSettingsPosition = paymentSettingsRef.current?.offsetTop || 0;
            const storageSettingsPosition = storageSettingsRef.current?.offsetTop || 0;
            const recaptchaSettingsPosition = recaptchaSettingsRef.current?.offsetTop || 0;
            const chatgptSettingsPosition = chatgptSettingsRef.current?.offsetTop || 0;
            const cookieSettingsPosition = cookieSettingsRef.current?.offsetTop || 0;
            const seoSettingsPosition = seoSettingsRef.current?.offsetTop || 0;
            const cacheSettingsPosition = cacheSettingsRef.current?.offsetTop || 0;
            const webhookSettingsPosition = webhookSettingsRef.current?.offsetTop || 0;
            const emailNotificationSettingsPosition = emailNotificationSettingsRef.current?.offsetTop || 0;
            const slackSettingsPosition = slackSettingsRef.current?.offsetTop || 0;
            const twilioSettingsPosition = twilioSettingsRef.current?.offsetTop || 0;
            const googleCalendarSettingsPosition = googleCalendarSettingsRef.current?.offsetTop || 0;



            // Determine active section based on scroll position
            if (scrollPosition >= webhookSettingsPosition) {
                setActiveSection('webhook-settings');
            } else if (scrollPosition >= googleCalendarSettingsPosition) {
                setActiveSection('google-calendar-settings');
            } else if (scrollPosition >= twilioSettingsPosition) {
                setActiveSection('twilio-settings');
            } else if (scrollPosition >= slackSettingsPosition) {
                setActiveSection('slack-settings');
            } else if (scrollPosition >= cacheSettingsPosition) {
                setActiveSection('cache-settings');
            } else if (scrollPosition >= seoSettingsPosition) {
                setActiveSection('seo-settings');
            } else if (scrollPosition >= cookieSettingsPosition) {
                setActiveSection('cookie-settings');
            } else if (scrollPosition >= chatgptSettingsPosition) {
                setActiveSection('chatgpt-settings');
            } else if (scrollPosition >= recaptchaSettingsPosition) {
                setActiveSection('recaptcha-settings');
            } else if (scrollPosition >= storageSettingsPosition) {
                setActiveSection('storage-settings');
            } else if (scrollPosition >= paymentSettingsPosition) {
                setActiveSection('payment-settings');
            } else if (scrollPosition >= emailNotificationSettingsPosition) {
                setActiveSection('email-notification-settings');
            } else if (scrollPosition >= emailSettingsPosition) {
                setActiveSection('email-settings');
            } else if (scrollPosition >= currencySettingsPosition) {
                setActiveSection('currency-settings');
            } else if (scrollPosition >= brandSettingsPosition) {
                setActiveSection('brand-settings');
            } else {
                setActiveSection('system-settings');
            }
        };

        // Add scroll event listener
        window.addEventListener('scroll', handleScroll);

        // Initial check for hash in URL
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            const element = document.getElementById(hash);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
                setActiveSection(hash);
            }
        }

        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    // Handle navigation click
    const handleNavClick = (href: string) => {
        const id = href.replace('#', '');
        const element = document.getElementById(id);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
            setActiveSection(id);
        }
    };

    return (
        <PageTemplate title={t('Settings')} url="/settings">
            <div className={`flex flex-col gap-8 md:flex-row ${position === 'right' ? 'md:flex-row' : ''}`}>
                {/* <div className="flex flex-col md:flex-row gap-8"> */}
                {/* Sidebar Navigation */}
                <div className="flex-shrink-0 md:w-64">
                    <div className="sticky top-20">
                        <ScrollArea className="h-[calc(100vh-5rem)]">
                            <div className={`space-y-1 ${position === 'right' ? 'pl-4' : 'pr-4'}`}>
                                {/* <div className="pr-4 space-y-1"> */}
                                {sidebarNavItems.map((item) =>
                                    item.condition() ? (
                                        <Button
                                            key={item.href}
                                            variant="ghost"
                                            className={cn('w-full justify-start', {
                                                'bg-muted font-medium': activeSection === item.href.replace('#', ''),
                                                'flex-row-reverse': position === 'right',
                                            })}
                                            onClick={() => handleNavClick(item.href)}
                                        >
                                            {item.icon}
                                            {item.title}
                                        </Button>
                                    ) : null,
                                )}
                            </div>
                        </ScrollArea>
                    </div>
                </div>
                {/* Main Content */}
                <div className={`flex-1 ${position === 'right' ? 'md:order-1' : 'md:order-2'}`}>
                    {/* System Settings Section */}
                    {settingsSidebarCondition('manage-system-settings') && (
                        <section id="system-settings" ref={systemSettingsRef} className="mb-8">
                            <SystemSettings
                                settings={systemSettings}
                                timezones={timezones}
                                dateFormats={dateFormats}
                                timeFormats={timeFormats}
                                countries={countries}
                                taxRates={taxRates}
                            />
                        </section>
                    )}

                    {/* Brand Settings Section */}
                    {settingsSidebarCondition('manage-brand-settings') && (
                        <section id="brand-settings" ref={brandSettingsRef} className="mb-8">
                            <BrandSettings />
                        </section>
                    )}

                    {/* Currency Settings Section */}
                    {settingsSidebarCondition('manage-currency-settings') && (
                        <section id="currency-settings" ref={currencySettingsRef} className="mb-8">
                            <CurrencySettings />
                        </section>
                    )}

                    {/* Email Settings Section */}
                    {settingsSidebarCondition('manage-email-settings') && (
                        <section id="email-settings" ref={emailSettingsRef} className="mb-8">
                            <EmailSettings />
                        </section>
                    )}

                    {/* Email Notification Settings Section */}
                    {settingsSidebarCondition('manage-email-notifications') && (
                        <section id="email-notification-settings" ref={emailNotificationSettingsRef} className="mb-8">
                            <EmailNotificationSettings templates={emailTemplates} />
                        </section>
                    )}

                    {/* Slack Settings Section */}
                    {settingsSidebarCondition('manage-slack-notifications') && (
                        <section id="slack-settings" ref={slackSettingsRef} className="mb-8">
                            <SlackSettings settings={slackSettings} notificationTemplates={notificationTemplates} />
                        </section>
                    )}

                    {/* Twilio Settings Section */}
                    {settingsSidebarCondition('manage-twilio-notifications') && (
                        <section id="twilio-settings" ref={twilioSettingsRef} className="mb-8">
                            <TwilioNotificationSettings />
                        </section>
                    )}

                    {/* Payment Settings Section */}
                    {settingsSidebarCondition('manage-payment-settings') && (
                        <section id="payment-settings" ref={paymentSettingsRef} className="mb-8">
                            <PaymentSettings settings={paymentSettings} />
                        </section>
                    )}

                    {/* Storage Settings Section */}
                    {settingsSidebarCondition('manage-storage-settings') && (
                        <section id="storage-settings" ref={storageSettingsRef} className="mb-8">
                            <StorageSettings settings={systemSettings} />
                        </section>
                    )}

                    {/* ReCaptcha Settings Section */}
                    {settingsSidebarCondition('manage-recaptcha-settings') && (
                        <section id="recaptcha-settings" ref={recaptchaSettingsRef} className="mb-8">
                            <RecaptchaSettings settings={systemSettings} />
                        </section>
                    )}

                    {/* Chat GPT Settings Section */}
                    {settingsSidebarCondition('manage-chatgpt-settings') && (
                        <section id="chatgpt-settings" ref={chatgptSettingsRef} className="mb-8">
                            <ChatGptSettings settings={systemSettings} />
                        </section>
                    )}

                    {/* Cookie Settings Section */}
                    {settingsSidebarCondition('manage-cookie-settings') && (
                        <section id="cookie-settings" ref={cookieSettingsRef} className="mb-8">
                            <CookieSettings settings={systemSettings} />
                        </section>
                    )}

                    {/* SEO Settings Section */}
                    {settingsSidebarCondition('manage-seo-settings') && (
                        <section id="seo-settings" ref={seoSettingsRef} className="mb-8">
                            <SeoSettings settings={systemSettings} />
                        </section>
                    )}

                    {/* Cache Settings Section */}
                    {settingsSidebarCondition('manage-cache-settings') && (
                        <section id="cache-settings" ref={cacheSettingsRef} className="mb-8">
                            <CacheSettings cacheSize={cacheSize} />
                        </section>
                    )}

                    {/* Google Calendar Settings Section */}
                    {settingsSidebarCondition('manage-google-calendar-settings') && (
                        <section id="google-calendar-settings" ref={googleCalendarSettingsRef} className="mb-8">
                            <GoogleCalendarSettings settings={systemSettings} />
                        </section>
                    )}
                </div>
            </div>
            <Toaster />
        </PageTemplate>
    );
}
