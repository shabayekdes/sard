import { QuickActionsButton } from '@/components/FloatingQuickActions';
import { NavMain } from '@/components/nav-main';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { useBrand } from '@/contexts/BrandContext';
import { useLayout } from '@/contexts/LayoutContext';
import { useSidebarSettings } from '@/contexts/SidebarContext';
import { type NavItem } from '@/types';
import { hasPermission } from '@/utils/authorization';
import { getImagePath } from '@/utils/helpers';
import { Link, usePage } from '@inertiajs/react';
import {
    Briefcase,
    BriefcaseBusiness,
    CalendarClock,
    CalendarFold,
    ContactRound,
    CreditCard,
    DollarSign,
    FileChartColumnIncreasing,
    FileSearch,
    FileText,
    Gavel,
    Gift,
    Globe,
    Image,
    LayoutDashboard,
    LayoutGrid,
    Mail,
    MessageSquare,
    Palette,
    Percent,
    Settings,
    SquareCheckBig,
    UserRoundCog,
    UsersRound,
} from 'lucide-react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

export function AppSidebar() {
    const { t } = useTranslation();
    const { auth } = usePage().props as any;
    const userRole = auth.user?.type || auth.user?.role;
    const permissions = auth?.permissions || [];

    // Get current direction
    const isRtl = document.documentElement.dir === 'rtl';

    // Business switch handler removed

    const getSuperAdminNavItems = (): NavItem[] => [
        {
            title: t('Dashboard'),
            href: route('dashboard'),
            icon: LayoutGrid,
        },

        {
            title: t('Companies'),
            href: route('companies.index'),
            icon: Briefcase,
        },
        {
            title: t('Media Library'),
            href: route('media-library'),
            icon: Image,
        },

        {
            title: t('Plans'),
            icon: CreditCard,
            children: [
                {
                    title: t('Plan'),
                    href: route('plans.index'),
                },
                {
                    title: t('Plan Request'),
                    href: route('plan-requests.index'),
                },
                {
                    title: t('Plan Orders'),
                    href: route('plan-orders.index'),
                },
            ],
        },
        {
            title: t('Coupons'),
            href: route('coupons.index'),
            icon: Settings,
        },

        {
            title: t('Contact Us'),
            href: route('contact-us.index'),
            icon: MessageSquare,
        },
        {
            title: t('Newsletter'),
            href: route('newsletter.index'),
            icon: Mail,
        },
        {
            title: t('Countries'),
            href: route('countries.index'),
            icon: Globe,
        },
        {
            title: t('Currencies'),
            href: route('currencies.index'),
            icon: DollarSign,
        },
        {
            title: t('Tax Rates'),
            href: route('tax-rates.index'),
            icon: Percent,
        },
        {
            title: t('Referral Program'),
            href: route('referral.index'),
            icon: Gift,
        },
        {
            title: t('Landing Page'),
            icon: Palette,
            children: [
                {
                    title: t('Landing Page'),
                    href: route('landing-page'),
                },
                {
                    title: t('Custom Pages'),
                    href: route('landing-page.custom-pages.index'),
                },
            ],
        },
        {
            title: t('Email Templates'),
            href: route('email-templates.index'),
            icon: Mail,
        },
        {
            title: t('Settings'),
            href: route('settings'),
            icon: Settings,
        },
    ];

    const getCompanyNavItems = (): NavItem[] => {
        const items: NavItem[] = [];

        items.push({ type: 'label', title: t('Overview') });
        // 1. Dashboard
        if (
            hasPermission(permissions, 'manage-dashboard') ||
            permissions.some((p: string) => p.startsWith('manage-own-')) ||
            permissions.some((p: string) => p.startsWith('manage-any-'))
        ) {
            items.push({
                title: t('Dashboard'),
                href: route('dashboard'),
                icon: LayoutDashboard,
            });
        }

        // 2. Client
        if (
            hasPermission(permissions, 'manage-clients') ||
            hasPermission(permissions, 'manage-any-clients') ||
            hasPermission(permissions, 'manage-own-clients')
        ) {
            items.push({
                title: t('Clients'),
                icon: UsersRound,
                href: route('clients.index'),
            });
        }
        // 3. Cases
        if (
            hasPermission(permissions, 'manage-cases') ||
            hasPermission(permissions, 'manage-any-cases') ||
            hasPermission(permissions, 'manage-own-cases')
        ) {
            items.push({ type: 'label', title: t('Case Management') });

            items.push({
                title: t('Cases'),
                href: route('cases.index'),
                icon: BriefcaseBusiness,
            });
        }
        if (
            hasPermission(permissions, 'manage-hearings') ||
            hasPermission(permissions, 'manage-any-hearings') ||
            hasPermission(permissions, 'manage-own-hearings')
        ) {
            items.push({
                title: t('Sessions'),
                href: route('hearings.index'),
                icon: CalendarClock,
            });
        }
        // if (
        //     hasPermission(permissions, 'manage-courts') ||
        //     hasPermission(permissions, 'manage-any-courts') ||
        //     hasPermission(permissions, 'manage-own-courts')
        // ) {
        //   items.push({
        //       title: t('Courts'),
        //       href: route('courts.index'),
        //       icon: CalendarClock,
        //   });
        // }
        if (
            hasPermission(permissions, 'manage-tasks') ||
            hasPermission(permissions, 'manage-any-tasks') ||
            hasPermission(permissions, 'manage-own-tasks')
        ) {
            items.push({
                title: t('Tasks & Workflow'),
                href: route('tasks.index'),
                icon: SquareCheckBig,
            });
        }
        if (
            hasPermission(permissions, 'manage-research-projects') ||
            hasPermission(permissions, 'manage-any-research-projects') ||
            hasPermission(permissions, 'manage-own-research-projects')
        ) {
            items.push({
                title: t('Legal Research'),
                href: route('legal-research.projects.index'),
                icon: FileSearch,
            });
        }

        // 4. Calendar
        if (
            hasPermission(permissions, 'manage-calendar') ||
            hasPermission(permissions, 'manage-any-calendar') ||
            hasPermission(permissions, 'manage-own-calendar')
        ) {
            items.push({
                title: t('Calendar'),
                href: route('calendar.index'),
                icon: CalendarFold,
            });
        }

        // 5. Messages
        if (
            hasPermission(permissions, 'manage-messages') ||
            hasPermission(permissions, 'manage-any-messages') ||
            hasPermission(permissions, 'manage-own-messages')
        ) {
            items.push({
                title: t('Messages'),
                icon: MessageSquare,
                href: route('communication.messages.index'),
            });
        }

        // Courts
        if (
            hasPermission(permissions, 'manage-courts') ||
            hasPermission(permissions, 'manage-any-courts') ||
            hasPermission(permissions, 'manage-own-courts')
        ) {
            items.push({
                title: t('Courts'),
                href: route('courts.index'),
                icon: Gavel,
            });
        }

        // 7. Billing
        const billingChildren = [];
        if (
            hasPermission(permissions, 'manage-invoices') ||
            hasPermission(permissions, 'manage-any-invoices') ||
            hasPermission(permissions, 'manage-own-invoices')
        ) {
            billingChildren.push({
                title: t('Invoices'),
                href: route('billing.invoices.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-payments') ||
            hasPermission(permissions, 'manage-any-payments') ||
            hasPermission(permissions, 'manage-own-payments')
        ) {
            billingChildren.push({
                title: t('Payments'),
                href: route('billing.payments.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-time-entries') ||
            hasPermission(permissions, 'manage-any-time-entries') ||
            hasPermission(permissions, 'manage-own-time-entries')
        ) {
            billingChildren.push({
                title: t('Time Entries'),
                href: route('billing.time-entries.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-billing-rates') ||
            hasPermission(permissions, 'manage-any-billing-rates') ||
            hasPermission(permissions, 'manage-own-billing-rates')
        ) {
            billingChildren.push({
                title: t('Billing Rate'),
                href: route('billing.billing-rates.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-expenses') ||
            hasPermission(permissions, 'manage-any-expenses') ||
            hasPermission(permissions, 'manage-own-expenses')
        ) {
            billingChildren.push({
                title: t('Expense'),
                href: route('billing.expenses.index'),
            });
        }
        // 8. Document Management (build children for label logic)
        const documentChildren = [];
        if (
            hasPermission(permissions, 'manage-media') ||
            hasPermission(permissions, 'manage-any-media') ||
            hasPermission(permissions, 'manage-own-media')
        ) {
            documentChildren.push({
                title: t('Media Library'),
                href: route('media-library'),
            });
        }
        if (
            hasPermission(permissions, 'manage-documents') ||
            hasPermission(permissions, 'manage-any-documents') ||
            hasPermission(permissions, 'manage-own-documents')
        ) {
            documentChildren.push({
                title: t('Documents'),
                href: route('document-management.documents.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-document-versions') ||
            hasPermission(permissions, 'manage-any-document-versions') ||
            hasPermission(permissions, 'manage-own-document-versions')
        ) {
            documentChildren.push({
                title: t('Versions'),
                href: route('document-management.versions.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-document-comments') ||
            hasPermission(permissions, 'manage-any-document-comments') ||
            hasPermission(permissions, 'manage-own-document-comments')
        ) {
            documentChildren.push({
                title: t('Comments'),
                href: route('document-management.comments.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-document-permissions') ||
            hasPermission(permissions, 'manage-any-document-permissions') ||
            hasPermission(permissions, 'manage-own-document-permissions')
        ) {
            documentChildren.push({
                title: t('Permissions'),
                href: route('document-management.permissions.index'),
            });
        }

        // Invoices & Documents: label only if at least one of Billing or Document Management
        const hasInvoicesSection = billingChildren.length > 0 || documentChildren.length > 0;
        if (hasInvoicesSection) {
            items.push({ type: 'label', title: t('Invoices & Documents') });
            if (billingChildren.length > 0) {
                items.push({
                    title: t('Billing'),
                    icon: FileChartColumnIncreasing,
                    children: billingChildren,
                });
            }
            if (documentChildren.length > 0) {
                items.push({
                    title: t('Document Management'),
                    icon: FileText,
                    children: documentChildren,
                });
            }
        }

        // User Management
        const userManagementChildren = [];
        if (
            hasPermission(permissions, 'manage-users') ||
            hasPermission(permissions, 'manage-any-users') ||
            hasPermission(permissions, 'manage-own-users')
        ) {
            userManagementChildren.push({
                title: t('Users'),
                href: route('users.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-roles') ||
            hasPermission(permissions, 'manage-any-roles') ||
            hasPermission(permissions, 'manage-own-roles')
        ) {
            userManagementChildren.push({
                title: t('Roles'),
                href: route('roles.index'),
            });
        }

        // 9. Settings (build children first for label logic)
        const companyProfilesChildren = [];
        if (
            hasPermission(permissions, 'manage-company-profiles') ||
            hasPermission(permissions, 'manage-any-company-profiles') ||
            hasPermission(permissions, 'manage-own-company-profiles')
        ) {
            companyProfilesChildren.push({
                title: t('Company Profile'),
                href: route('advocate.company-profiles.index'),
            });
        }
        if (
            hasPermission(permissions, 'manage-plans') ||
            hasPermission(permissions, 'manage-any-plans') ||
            hasPermission(permissions, 'manage-own-plans')
        ) {
            companyProfilesChildren.push({
                title: t('Plans'),
                href: route('plans.index'),
            });
        }

        // General settings: label only if at least one of Setup, User Management, or Settings
        const hasSetup = hasPermission(permissions, 'view-setup');
        const hasSettings = hasPermission(permissions, 'manage-settings');

        const hasGeneralSection = userManagementChildren.length > 0 || hasSetup || companyProfilesChildren.length > 0;
        if (hasGeneralSection) {
            items.push({ type: 'label', title: t('General settings') });
            if (userManagementChildren.length > 0) {
                items.push({
                    title: t('User Management'),
                    icon: UserRoundCog,
                    children: userManagementChildren,
                });
            }
            if (companyProfilesChildren.length > 0) {
                items.push({
                    title: t('Company Profile'),
                    icon: ContactRound,
                    children: companyProfilesChildren,
                });
            }
            if (hasSetup) {
                items.push({
                    title: t('Master Data'),
                    href: route('setup.index'),
                    icon: Settings,
                });
            }
            if (hasSettings) {
                items.push({
                    title: t('System Settings'),
                    href: route('settings'),
                    icon: Settings,
                });
            }
        }

        // 11. Compliance & Regulatory Module - HIDDEN
        // const complianceChildren = [];
        // const complianceSetupChildren = [];
        // if (
        //     hasPermission(permissions, 'manage-compliance-categories') ||
        //     hasPermission(permissions, 'manage-any-compliance-categories') ||
        //     hasPermission(permissions, 'manage-own-compliance-categories')
        // ) {
        //     complianceSetupChildren.push({
        //         title: t('Compliance Category'),
        //         href: route('compliance.categories.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-compliance-frequencies') ||
        //     hasPermission(permissions, 'manage-any-compliance-frequencies') ||
        //     hasPermission(permissions, 'manage-own-compliance-frequencies')
        // ) {
        //     complianceSetupChildren.push({
        //         title: t('Compliance Frequencies'),
        //         href: route('compliance.frequencies.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-risk-categories') ||
        //     hasPermission(permissions, 'manage-any-risk-categories') ||
        //     hasPermission(permissions, 'manage-own-risk-categories')
        // ) {
        //     complianceSetupChildren.push({
        //         title: t('Risk Category'),
        //         href: route('compliance.risk-categories.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-audit-types') ||
        //     hasPermission(permissions, 'manage-any-audit-types') ||
        //     hasPermission(permissions, 'manage-own-audit-types')
        // ) {
        //     complianceSetupChildren.push({
        //         title: t('Compliance Audit Type'),
        //         href: route('compliance.audit-types.index'),
        //     });
        // }
        // if (complianceSetupChildren.length > 0) {
        //     complianceChildren.push({
        //         title: t('Compliance Setup'),
        //         children: complianceSetupChildren,
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-compliance-requirements') ||
        //     hasPermission(permissions, 'manage-any-compliance-requirements') ||
        //     hasPermission(permissions, 'manage-own-compliance-requirements')
        // ) {
        //     complianceChildren.push({
        //         title: t('Compliance Requirements'),
        //         href: route('compliance.requirements.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-professional-licenses') ||
        //     hasPermission(permissions, 'manage-any-professional-licenses') ||
        //     hasPermission(permissions, 'manage-own-professional-licenses')
        // ) {
        //     complianceChildren.push({
        //         title: t('Professional Licenses'),
        //         href: route('compliance.professional-licenses.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-cle-tracking') ||
        //     hasPermission(permissions, 'manage-any-cle-tracking') ||
        //     hasPermission(permissions, 'manage-own-cle-tracking')
        // ) {
        //     complianceChildren.push({
        //         title: t('CLE Tracking'),
        //         href: route('compliance.cle-tracking.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-regulatory-bodies') ||
        //     hasPermission(permissions, 'manage-any-regulatory-bodies') ||
        //     hasPermission(permissions, 'manage-own-regulatory-bodies')
        // ) {
        //     complianceChildren.push({
        //         title: t('Regulatory Bodies'),
        //         href: route('compliance.regulatory-bodies.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-risk-assessments') ||
        //     hasPermission(permissions, 'manage-any-risk-assessments') ||
        //     hasPermission(permissions, 'manage-own-risk-assessments')
        // ) {
        //     complianceChildren.push({
        //         title: t('Risk Assessments'),
        //         href: route('compliance.risk-assessments.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-compliance-audits') ||
        //     hasPermission(permissions, 'manage-any-compliance-audits') ||
        //     hasPermission(permissions, 'manage-own-compliance-audits')
        // ) {Database\Seeders\UserSeeder
        //     complianceChildren.push({
        //         title: t('Compliance Audit'),
        //         href: route('compliance.audits.index'),
        //     });
        // }
        // if (complianceChildren.length > 0) {
        //     items.push({
        //         title: t('Compliance & Regulatory Module'),
        //         icon: CheckSquare,
        //         children: complianceChildren,
        //     });
        // }

        // 12. Knowledge Base, Research Category (under Legal Research) - Legal Precedents HIDDEN
        // const legalResearchChildren = [];
        // if (
        //     hasPermission(permissions, 'manage-knowledge-articles') ||
        //     hasPermission(permissions, 'manage-any-knowledge-articles') ||
        //     hasPermission(permissions, 'manage-own-knowledge-articles')
        // ) {
        //     legalResearchChildren.push({
        //         title: t('Knowledge Base'),
        //         href: route('legal-research.knowledge.index'),
        //     });
        // }
        // Legal Precedents - HIDDEN
        // if (
        //     hasPermission(permissions, 'manage-legal-precedents') ||
        //     hasPermission(permissions, 'manage-any-legal-precedents') ||
        //     hasPermission(permissions, 'manage-own-legal-precedents')
        // ) {
        //     legalResearchChildren.push({
        //         title: t('Legal Precedents'),
        //         href: route('legal-research.precedents.index'),
        //     });
        // }
        // if (
        //     hasPermission(permissions, 'manage-research-categories') ||
        //     hasPermission(permissions, 'manage-any-research-categories') ||
        //     hasPermission(permissions, 'manage-own-research-categories')
        // ) {
        //     legalResearchChildren.push({
        //         title: t('Research Category'),
        //         href: route('legal-research.categories.index'),
        //     });
        // }
        // if (legalResearchChildren.length > 0) {
        //     items.push({
        //         title: t('Legal Research'),
        //         icon: BookOpen,
        //         children: legalResearchChildren,
        //     });
        // }

        return items;
    };

    const mainNavItems = userRole === 'superadmin' ? getSuperAdminNavItems() : getCompanyNavItems();

    const { position } = useLayout();
    const { variant, collapsible, style } = useSidebarSettings();
    const { logoLight, logoDark, favicon, updateBrandSettings } = useBrand();
    const [sidebarStyle, setSidebarStyle] = useState({});

    useEffect(() => {
        // Apply styles based on sidebar style
        if (style === 'colored') {
            setSidebarStyle({ backgroundColor: 'var(--primary)', color: 'white' });
        } else if (style === 'gradient') {
            setSidebarStyle({
                background: 'linear-gradient(to bottom, var(--primary), color-mix(in srgb, var(--primary), transparent 20%))',
                color: 'white',
            });
        } else {
            setSidebarStyle({});
        }
    }, [style]);

    const filteredNavItems = mainNavItems;

    // Get the first available menu item's href for logo link
    const getFirstAvailableHref = () => {
        if (filteredNavItems.length === 0) return route('dashboard');

        const firstItem = filteredNavItems[0];
        if (firstItem.href) {
            return firstItem.href;
        } else if (firstItem.children && firstItem.children.length > 0) {
            return firstItem.children[0].href || route('dashboard');
        }
        return route('dashboard');
    };

    return (
        <Sidebar side={position} collapsible={collapsible} variant={variant} className={style !== 'plain' ? 'sidebar-custom-style' : ''}>
            <SidebarHeader className={style !== 'plain' ? 'sidebar-styled' : ''} style={sidebarStyle}>
                <div className="flex items-center justify-center p-2">
                    <Link href={getFirstAvailableHref()} className="flex items-center justify-center">
                        {/* Logo for expanded sidebar */}
                        <div className="flex h-10 items-center group-data-[collapsible=icon]:hidden">
                            {(() => {
                                const isDark = document.documentElement.classList.contains('dark');
                                const currentLogo = isDark ? logoLight : logoDark;
                                const displayUrl = currentLogo ? getImagePath(currentLogo) : '';

                                return displayUrl ? (
                                    <img
                                        key={`${currentLogo}-${Date.now()}`}
                                        src={displayUrl}
                                        alt="Logo"
                                        className="h-9 w-auto max-w-[190px] transition-all duration-200"
                                        onError={() => updateBrandSettings({ [isDark ? 'logoLight' : 'logoDark']: '' })}
                                    />
                                ) : (
                                    <div className="flex h-12 items-center text-lg font-semibold tracking-tight text-inherit">WorkDo</div>
                                );
                            })()}
                        </div>

                        {/* Icon for collapsed sidebar */}
                        <div className="hidden h-8 w-8 group-data-[collapsible=icon]:block">
                            {(() => {
                                const displayFavicon = favicon ? getImagePath(favicon) : '';

                                return displayFavicon ? (
                                    <img
                                        key={`${favicon}-${Date.now()}`}
                                        src={displayFavicon}
                                        alt="Icon"
                                        className="h-8 w-8 transition-all duration-200"
                                        onError={() => updateBrandSettings({ favicon: '' })}
                                    />
                                ) : (
                                    <div className="bg-primary flex h-8 w-8 items-center justify-center rounded font-bold text-white shadow-sm">
                                        W
                                    </div>
                                );
                            })()}
                        </div>
                    </Link>
                </div>

                {/* Business Switcher removed */}
            </SidebarHeader>
            <SidebarContent>
                <div style={sidebarStyle} className={`h-full ${style !== 'plain' ? 'sidebar-styled' : ''}`}>
                    <NavMain items={filteredNavItems} position={position} />
                </div>
            </SidebarContent>

            <SidebarFooter>
                {userRole !== 'superadmin' && userRole !== 'client' && (
                    <div className="sticky bottom-0 mt-auto border-t border-sidebar-border pt-2 bg-sidebar">
                        <QuickActionsButton
                            className="w-full justify-center group-data-[collapsible=icon]:justify-center"
                            buttonClassName="h-12 w-12 group-data-[collapsible=icon]:h-10 group-data-[collapsible=icon]:w-10"
                            side="top"
                        />
                    </div>
                )}
            </SidebarFooter>
        </Sidebar>
    );
}
