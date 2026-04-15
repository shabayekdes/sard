<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard permissions
            ['name' => 'manage-dashboard', 'module' => 'dashboard', 'label' => ['en' => 'Manage Dashboard', 'ar' => 'إدارة لوحة التحكم']],

            // User management
            ['name' => 'manage-users', 'module' => 'users', 'label' => ['en' => 'Manage Users', 'ar' => 'إدارة المستخدمين']],
            ['name' => 'manage-any-users', 'module' => 'users', 'label' => ['en' => 'Manage All Users', 'ar' => 'إدارة جميع المستخدمين']],
            ['name' => 'manage-own-users', 'module' => 'users', 'label' => ['en' => 'Manage Own Users', 'ar' => 'إدارة المستخدمين الخاصين']],
            ['name' => 'view-users', 'module' => 'users', 'label' => ['en' => 'Manage Users', 'ar' => 'عرض المستخدمين']],
            ['name' => 'create-users', 'module' => 'users', 'label' => ['en' => 'Create Users', 'ar' => 'إنشاء المستخدمين']],
            ['name' => 'edit-users', 'module' => 'users', 'label' => ['en' => 'Edit Users', 'ar' => 'تعديل المستخدمين']],
            ['name' => 'delete-users', 'module' => 'users', 'label' => ['en' => 'Delete Users', 'ar' => 'حذف المستخدمين']],
            ['name' => 'reset-password-users', 'module' => 'users', 'label' => ['en' => 'Reset Password Users', 'ar' => 'إعادة تعيين كلمة مرور المستخدمين']],
            ['name' => 'toggle-status-users', 'module' => 'users', 'label' => ['en' => 'Change Status Users', 'ar' => 'تغيير حالة المستخدمين']],

            // Role management
            ['name' => 'manage-roles', 'module' => 'roles', 'label' => ['en' => 'Manage Roles', 'ar' => 'إدارة الأدوار']],
            ['name' => 'manage-any-roles', 'module' => 'roles', 'label' => ['en' => 'Manage All Roles', 'ar' => 'إدارة جميع الأدوار']],
            ['name' => 'manage-own-roles', 'module' => 'roles', 'label' => ['en' => 'Manage Own Roles', 'ar' => 'إدارة الأدوار الخاصة']],
            ['name' => 'view-roles', 'module' => 'roles', 'label' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار']],
            ['name' => 'create-roles', 'module' => 'roles', 'label' => ['en' => 'Create Roles', 'ar' => 'إنشاء الأدوار']],
            ['name' => 'edit-roles', 'module' => 'roles', 'label' => ['en' => 'Edit Roles', 'ar' => 'تعديل الأدوار']],
            ['name' => 'delete-roles', 'module' => 'roles', 'label' => ['en' => 'Delete Roles', 'ar' => 'حذف الأدوار']],

            // Permission management
            ['name' => 'manage-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage Permissions', 'ar' => 'إدارة الصلاحيات']],
            ['name' => 'manage-any-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage All Permissions', 'ar' => 'إدارة جميع الصلاحيات']],
            ['name' => 'manage-own-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage Own Permissions', 'ar' => 'إدارة الصلاحيات الخاصة']],
            ['name' => 'view-permissions', 'module' => 'permissions', 'label' => ['en' => 'View Permissions', 'ar' => 'عرض الصلاحيات']],
            ['name' => 'create-permissions', 'module' => 'permissions', 'label' => ['en' => 'Create Permissions', 'ar' => 'إنشاء الصلاحيات']],
            ['name' => 'edit-permissions', 'module' => 'permissions', 'label' => ['en' => 'Edit Permissions', 'ar' => 'تعديل الصلاحيات']],
            ['name' => 'delete-permissions', 'module' => 'permissions', 'label' => ['en' => 'Delete Permissions', 'ar' => 'حذف الصلاحيات']],

            // Company management
            ['name' => 'manage-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Companies', 'ar' => 'إدارة الشركات']],
            ['name' => 'manage-any-companies', 'module' => 'companies', 'label' => ['en' => 'Manage All Companies', 'ar' => 'إدارة جميع الشركات']],
            ['name' => 'manage-own-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Own Companies', 'ar' => 'إدارة الشركات الخاصة']],
            ['name' => 'view-companies', 'module' => 'companies', 'label' => ['en' => 'View Companies', 'ar' => 'عرض الشركات']],
            ['name' => 'create-companies', 'module' => 'companies', 'label' => ['en' => 'Create Companies', 'ar' => 'إنشاء الشركات']],
            ['name' => 'edit-companies', 'module' => 'companies', 'label' => ['en' => 'Edit Companies', 'ar' => 'تعديل الشركات']],
            ['name' => 'delete-companies', 'module' => 'companies', 'label' => ['en' => 'Delete Companies', 'ar' => 'حذف الشركات']],
            ['name' => 'reset-password-companies', 'module' => 'companies', 'label' => ['en' => 'Reset Password Companies', 'ar' => 'إعادة تعيين كلمة مرور الشركات']],
            ['name' => 'toggle-status-companies', 'module' => 'companies', 'label' => ['en' => 'Change Status Companies', 'ar' => 'تغيير حالة الشركات']],
            ['name' => 'manage-plans-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Plan Companies', 'ar' => 'إدارة خطط الشركات']],
            ['name' => 'upgrade-plan-companies', 'module' => 'companies', 'label' => ['en' => 'Upgrade Plan Companies', 'ar' => 'ترقية خطة الشركات']],

            // Plan management
            ['name' => 'manage-plans', 'module' => 'plans', 'label' => ['en' => 'Manage Plans', 'ar' => 'إدارة الخطط']],
            ['name' => 'manage-any-plans', 'module' => 'plans', 'label' => ['en' => 'Manage All Plans', 'ar' => 'إدارة جميع الخطط']],
            ['name' => 'manage-own-plans', 'module' => 'plans', 'label' => ['en' => 'Manage Own Plans', 'ar' => 'إدارة الخطط الخاصة']],
            ['name' => 'view-plans', 'module' => 'plans', 'label' => ['en' => 'View Plans', 'ar' => 'عرض الخطط']],
            ['name' => 'create-plans', 'module' => 'plans', 'label' => ['en' => 'Create Plans', 'ar' => 'إنشاء الخطط']],
            ['name' => 'edit-plans', 'module' => 'plans', 'label' => ['en' => 'Edit Plans', 'ar' => 'تعديل الخطط']],
            ['name' => 'delete-plans', 'module' => 'plans', 'label' => ['en' => 'Delete Plans', 'ar' => 'حذف الخطط']],
            ['name' => 'request-plans', 'module' => 'plans', 'label' => ['en' => 'Request Plans', 'ar' => 'طلب الخطط']],
            ['name' => 'trial-plans', 'module' => 'plans', 'label' => ['en' => 'Trial Plans', 'ar' => 'خطط التجربة']],
            ['name' => 'subscribe-plans', 'module' => 'plans', 'label' => ['en' => 'Subscribe Plans', 'ar' => 'الاشتراك في الخطط']],

            // Coupon management
            ['name' => 'manage-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage Coupons', 'ar' => 'إدارة القسائم']],
            ['name' => 'manage-any-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage All Coupons', 'ar' => 'إدارة جميع القسائم']],
            ['name' => 'manage-own-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage Own Coupons', 'ar' => 'إدارة القسائم الخاصة']],
            ['name' => 'view-coupons', 'module' => 'coupons', 'label' => ['en' => 'View Coupons', 'ar' => 'عرض القسائم']],
            ['name' => 'create-coupons', 'module' => 'coupons', 'label' => ['en' => 'Create Coupons', 'ar' => 'إنشاء القسائم']],
            ['name' => 'edit-coupons', 'module' => 'coupons', 'label' => ['en' => 'Edit Coupons', 'ar' => 'تعديل القسائم']],
            ['name' => 'delete-coupons', 'module' => 'coupons', 'label' => ['en' => 'Delete Coupons', 'ar' => 'حذف القسائم']],
            ['name' => 'toggle-status-coupons', 'module' => 'coupons', 'label' => ['en' => 'Change Status Coupons', 'ar' => 'تغيير حالة القسائم']],

            // Plan Requests management
            ['name' => 'manage-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage Plan Requests', 'ar' => 'إدارة طلبات الخطط']],
            ['name' => 'manage-any-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage All Plan Requests', 'ar' => 'إدارة جميع طلبات الخطط']],
            ['name' => 'manage-own-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage Own Plan Requests', 'ar' => 'إدارة طلبات الخطط الخاصة']],
            ['name' => 'view-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'View Plan Requests', 'ar' => 'عرض طلبات الخطط']],
            ['name' => 'create-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Create Plan Requests', 'ar' => 'إنشاء طلبات الخطط']],
            ['name' => 'edit-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Edit Plan Requests', 'ar' => 'تعديل طلبات الخطط']],
            ['name' => 'delete-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Delete Plan Requests', 'ar' => 'حذف طلبات الخطط']],
            ['name' => 'approve-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Approve plan requests', 'ar' => 'الموافقة على طلبات الخطط']],
            ['name' => 'reject-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Reject plan requests', 'ar' => 'رفض طلبات الخطط']],

            // Plan Orders management
            ['name' => 'manage-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage Plan Orders', 'ar' => 'إدارة طلبات الاشتراك']],
            ['name' => 'manage-any-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage All Plan Orders', 'ar' => 'إدارة جميع طلبات الاشتراك']],
            ['name' => 'manage-own-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage Own Plan Orders', 'ar' => 'إدارة طلبات الاشتراك الخاصة']],
            ['name' => 'view-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'View Plan Orders', 'ar' => 'عرض طلبات الاشتراك']],
            ['name' => 'create-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Create Plan Orders', 'ar' => 'إنشاء طلبات الاشتراك']],
            ['name' => 'edit-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Edit Plan Orders', 'ar' => 'تعديل طلبات الاشتراك']],
            ['name' => 'delete-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Delete Plan Orders', 'ar' => 'حذف طلبات الاشتراك']],
            ['name' => 'approve-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Approve Plan Orders', 'ar' => 'الموافقة على طلبات الاشتراك']],
            ['name' => 'reject-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Reject Plan Orders', 'ar' => 'رفض طلبات الاشتراك']],

            // Settings
            ['name' => 'manage-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Settings', 'ar' => 'إدارة الإعدادات']],
            ['name' => 'manage-system-settings', 'module' => 'settings', 'label' => ['en' => 'Manage System Settings', 'ar' => 'إدارة إعدادات النظام']],
            ['name' => 'manage-email-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Email Settings', 'ar' => 'إدارة إعدادات البريد']],
            ['name' => 'manage-brand-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Brand Settings', 'ar' => 'إدارة إعدادات العلامة التجارية']],
            ['name' => 'manage-storage-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Storage Settings', 'ar' => 'إدارة إعدادات التخزين']],
            ['name' => 'manage-payment-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Payment Settings', 'ar' => 'إدارة إعدادات الدفع']],
            ['name' => 'manage-currency-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Currency Settings', 'ar' => 'إدارة إعدادات العملة']],
            ['name' => 'manage-recaptcha-settings', 'module' => 'settings', 'label' => ['en' => 'Manage ReCaptch Settings', 'ar' => 'إدارة إعدادات reCAPTCHA']],
            ['name' => 'manage-chatgpt-settings', 'module' => 'settings', 'label' => ['en' => 'Manage ChatGpt Settings', 'ar' => 'إدارة إعدادات ChatGPT']],
            ['name' => 'manage-cookie-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Cookie(GDPR) Settings', 'ar' => 'إدارة إعدادات ملفات التعريف (GDPR)']],
            ['name' => 'manage-seo-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Seo Settings', 'ar' => 'إدارة إعدادات SEO']],
            ['name' => 'manage-cache-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Cache Settings', 'ar' => 'إدارة إعدادات الذاكرة المؤقتة']],
            ['name' => 'manage-account-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Account Settings', 'ar' => 'إدارة إعدادات الحساب']],

            // Setup (configuration) permissions
            ['name' => 'view-setup', 'module' => 'setup', 'label' => ['en' => 'View Setup', 'ar' => 'عرض الإعداد']],

            // Contact Us management
            ['name' => 'manage-contact-us', 'module' => 'contact-us', 'label' => ['en' => 'Manage Contact Us', 'ar' => 'إدارة اتصل بنا']],
            ['name' => 'view-contact-us', 'module' => 'contact-us', 'label' => ['en' => 'View Contact Us', 'ar' => 'عرض اتصل بنا']],

            // Currency management
            ['name' => 'manage-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage Currencies', 'ar' => 'إدارة العملات']],
            ['name' => 'manage-any-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage All currencies', 'ar' => 'إدارة جميع العملات']],
            ['name' => 'manage-own-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage Own currencies', 'ar' => 'إدارة العملات الخاصة']],
            ['name' => 'view-currencies', 'module' => 'currencies', 'label' => ['en' => 'View Currencies', 'ar' => 'عرض العملات']],
            ['name' => 'create-currencies', 'module' => 'currencies', 'label' => ['en' => 'Create Currencies', 'ar' => 'إنشاء العملات']],
            ['name' => 'edit-currencies', 'module' => 'currencies', 'label' => ['en' => 'Edit Currencies', 'ar' => 'تعديل العملات']],
            ['name' => 'delete-currencies', 'module' => 'currencies', 'label' => ['en' => 'Delete Currencies', 'ar' => 'حذف العملات']],

            // Tax Rate management
            ['name' => 'manage-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage Tax Rates', 'ar' => 'إدارة معدلات الضريبة']],
            ['name' => 'manage-any-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage All tax rates', 'ar' => 'إدارة جميع معدلات الضريبة']],
            ['name' => 'manage-own-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage Own tax rates', 'ar' => 'إدارة معدلات الضريبة الخاصة']],
            ['name' => 'view-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'View Tax Rates', 'ar' => 'عرض معدلات الضريبة']],
            ['name' => 'create-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Create Tax Rates', 'ar' => 'إنشاء معدلات الضريبة']],
            ['name' => 'edit-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Edit Tax Rates', 'ar' => 'تعديل معدلات الضريبة']],
            ['name' => 'delete-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Delete Tax Rates', 'ar' => 'حذف معدلات الضريبة']],

            // Country management
            ['name' => 'manage-countries', 'module' => 'countries', 'label' => ['en' => 'Manage Countries', 'ar' => 'إدارة الدول']],
            ['name' => 'manage-any-countries', 'module' => 'countries', 'label' => ['en' => 'Manage All Countries', 'ar' => 'إدارة جميع الدول']],
            ['name' => 'manage-own-countries', 'module' => 'countries', 'label' => ['en' => 'Manage Own Countries', 'ar' => 'إدارة الدول الخاصة']],
            ['name' => 'view-countries', 'module' => 'countries', 'label' => ['en' => 'View Countries', 'ar' => 'عرض الدول']],
            ['name' => 'create-countries', 'module' => 'countries', 'label' => ['en' => 'Create Countries', 'ar' => 'إنشاء الدول']],
            ['name' => 'edit-countries', 'module' => 'countries', 'label' => ['en' => 'Edit Countries', 'ar' => 'تعديل الدول']],
            ['name' => 'delete-countries', 'module' => 'countries', 'label' => ['en' => 'Delete Countries', 'ar' => 'حذف الدول']],

            // Referral management
            ['name' => 'manage-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'إدارة الإحالة']],
            ['name' => 'manage-any-referral', 'module' => 'referral', 'label' => ['en' => 'Manage All Referral', 'ar' => 'إدارة جميع الإحالات']],
            ['name' => 'manage-own-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Own Referral', 'ar' => 'إدارة الإحالات الخاصة']],
            ['name' => 'manage-users-referral', 'module' => 'referral', 'label' => ['en' => 'Manage User Referral', 'ar' => 'إدارة إحالات المستخدمين']],
            ['name' => 'manage-setting-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral Setting', 'ar' => 'إدارة إعدادات الإحالة']],
            ['name' => 'manage-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral Payout', 'ar' => 'إدارة مدفوعات الإحالة']],
            ['name' => 'approve-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'الموافقة على مدفوعات الإحالة']],
            ['name' => 'reject-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'رفض مدفوعات الإحالة']],

            // Language management
            ['name' => 'manage-language', 'module' => 'language', 'label' => ['en' => 'Manage Language', 'ar' => 'إدارة اللغة']],
            ['name' => 'manage-any-language', 'module' => 'language', 'label' => ['en' => 'Manage All Language', 'ar' => 'إدارة جميع اللغات']],
            ['name' => 'manage-own-language', 'module' => 'language', 'label' => ['en' => 'Manage Own Language', 'ar' => 'إدارة اللغات الخاصة']],
            ['name' => 'edit-language', 'module' => 'language', 'label' => ['en' => 'Edit Language', 'ar' => 'تعديل اللغة']],
            ['name' => 'view-language', 'module' => 'language', 'label' => ['en' => 'View Language', 'ar' => 'عرض اللغة']],

            // Media management
            ['name' => 'manage-media', 'module' => 'media', 'label' => ['en' => 'Manage Media', 'ar' => 'إدارة الوسائط']],
            ['name' => 'manage-any-media', 'module' => 'media', 'label' => ['en' => 'Manage All Media', 'ar' => 'إدارة جميع الوسائط']],
            ['name' => 'manage-own-media', 'module' => 'media', 'label' => ['en' => 'Manage Own Media', 'ar' => 'إدارة الوسائط الخاصة']],
            ['name' => 'create-media', 'module' => 'media', 'label' => ['en' => 'Create media', 'ar' => 'إنشاء الوسائط']],
            ['name' => 'edit-media', 'module' => 'media', 'label' => ['en' => 'Edit media', 'ar' => 'تعديل الوسائط']],
            ['name' => 'delete-media', 'module' => 'media', 'label' => ['en' => 'Delete media', 'ar' => 'حذف الوسائط']],
            ['name' => 'view-media', 'module' => 'media', 'label' => ['en' => 'View media', 'ar' => 'عرض الوسائط']],
            ['name' => 'download-media', 'module' => 'media', 'label' => ['en' => 'Download media', 'ar' => 'تحميل الوسائط']],

            // Webhook management
            ['name' => 'manage-webhook-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Webhook Settings', 'ar' => 'إدارة إعدادات الويب هوك']],
            // Landing Page management
            ['name' => 'manage-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage Landing Page', 'ar' => 'إدارة الصفحة المقصودة']],
            ['name' => 'manage-any-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage All Landing Page', 'ar' => 'إدارة جميع الصفحات المقصودة']],
            ['name' => 'manage-own-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage Own Landing Page', 'ar' => 'إدارة الصفحات المقصودة الخاصة']],
            ['name' => 'view-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'View Landing Page', 'ar' => 'عرض الصفحة المقصودة']],
            ['name' => 'edit-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Edit Landing Page', 'ar' => 'تعديل الصفحة المقصودة']],

            // Client Type management
            ['name' => 'manage-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage Client Types', 'ar' => 'إدارة أنواع العملاء']],
            ['name' => 'manage-any-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage All Client Types', 'ar' => 'إدارة جميع أنواع العملاء']],
            ['name' => 'manage-own-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage Own Client Types', 'ar' => 'إدارة أنواع العملاء الخاصة']],
            ['name' => 'view-client-types', 'module' => 'client_types', 'label' => ['en' => 'View Client Types', 'ar' => 'عرض أنواع العملاء']],
            ['name' => 'create-client-types', 'module' => 'client_types', 'label' => ['en' => 'Create Client Types', 'ar' => 'إنشاء أنواع العملاء']],
            ['name' => 'edit-client-types', 'module' => 'client_types', 'label' => ['en' => 'Edit Client Types', 'ar' => 'تعديل أنواع العملاء']],
            ['name' => 'delete-client-types', 'module' => 'client_types', 'label' => ['en' => 'Delete Client Types', 'ar' => 'حذف أنواع العملاء']],
            ['name' => 'toggle-status-client-types', 'module' => 'client_types', 'label' => ['en' => 'Toggle Status Client Types', 'ar' => 'تبديل حالة أنواع العملاء']],

            // Client management
            ['name' => 'manage-clients', 'module' => 'clients', 'label' => ['en' => 'Manage Clients', 'ar' => 'إدارة العملاء']],
            ['name' => 'manage-any-clients', 'module' => 'clients', 'label' => ['en' => 'Manage All Clients', 'ar' => 'إدارة جميع العملاء']],
            ['name' => 'manage-own-clients', 'module' => 'clients', 'label' => ['en' => 'Manage Own Clients', 'ar' => 'إدارة العملاء الخاصين']],
            ['name' => 'view-clients', 'module' => 'clients', 'label' => ['en' => 'View Clients', 'ar' => 'عرض العملاء']],
            ['name' => 'create-clients', 'module' => 'clients', 'label' => ['en' => 'Create Clients', 'ar' => 'إنشاء العملاء']],
            ['name' => 'edit-clients', 'module' => 'clients', 'label' => ['en' => 'Edit Clients', 'ar' => 'تعديل العملاء']],
            ['name' => 'delete-clients', 'module' => 'clients', 'label' => ['en' => 'Delete Clients', 'ar' => 'حذف العملاء']],
            ['name' => 'toggle-status-clients', 'module' => 'clients', 'label' => ['en' => 'Toggle Status Clients', 'ar' => 'تبديل حالة العملاء']],
            ['name' => 'reset-client-password', 'module' => 'clients', 'label' => ['en' => 'Reset Client Password', 'ar' => 'إعادة تعيين كلمة مرور العميل']],

            // Client Communication management

            // Client Document management
            ['name' => 'manage-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage Client Documents', 'ar' => 'إدارة مستندات العملاء']],
            ['name' => 'manage-any-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage All Client Documents', 'ar' => 'إدارة جميع مستندات العملاء']],
            ['name' => 'manage-own-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage Own Client Documents', 'ar' => 'إدارة مستندات العملاء الخاصة']],
            ['name' => 'view-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'View Client Documents', 'ar' => 'عرض مستندات العملاء']],
            ['name' => 'create-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Create Client Documents', 'ar' => 'إنشاء مستندات العملاء']],
            ['name' => 'edit-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Edit Client Documents', 'ar' => 'تعديل مستندات العملاء']],
            ['name' => 'delete-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Delete Client Documents', 'ar' => 'حذف مستندات العملاء']],
            ['name' => 'download-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Download Client Documents', 'ar' => 'تحميل مستندات العملاء']],

            // Client Billing Info management
            ['name' => 'manage-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage Client Billing', 'ar' => 'إدارة فوترة العملاء']],
            ['name' => 'manage-any-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage All Client Billing', 'ar' => 'إدارة جميع فوترة العملاء']],
            ['name' => 'manage-own-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage Own Client Billing', 'ar' => 'إدارة فوترة العملاء الخاصة']],
            ['name' => 'view-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'View Client Billing', 'ar' => 'عرض فوترة العملاء']],
            ['name' => 'create-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Create Client Billing', 'ar' => 'إنشاء فوترة العملاء']],
            ['name' => 'edit-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Edit Client Billing', 'ar' => 'تعديل فوترة العملاء']],
            ['name' => 'delete-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Delete Client Billing', 'ar' => 'حذف فوترة العملاء']],

            // Company Profile management
            ['name' => 'manage-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage Company Profiles', 'ar' => 'إدارة ملفات الشركات']],
            ['name' => 'manage-any-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage All Company Profiles', 'ar' => 'إدارة جميع ملفات الشركات']],
            ['name' => 'manage-own-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage Own Company Profiles', 'ar' => 'إدارة ملفات الشركات الخاصة']],
            ['name' => 'view-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'View Company Profiles', 'ar' => 'عرض ملفات الشركات']],
            ['name' => 'create-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Create Company Profiles', 'ar' => 'إنشاء ملفات الشركات']],
            ['name' => 'edit-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Edit Company Profiles', 'ar' => 'تعديل ملفات الشركات']],
            ['name' => 'delete-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Delete Company Profiles', 'ar' => 'حذف ملفات الشركات']],
            ['name' => 'toggle-status-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Toggle Status Company Profiles', 'ar' => 'تبديل حالة ملفات الشركات']],

            // Practice Area management
            // ['name' => 'manage-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage Practice Areas', 'ar' => 'إدارة مجالات الممارسة']],
            // ['name' => 'manage-any-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage All Practice Areas', 'ar' => 'إدارة جميع مجالات الممارسة']],
            // ['name' => 'manage-own-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage Own Practice Areas', 'ar' => 'إدارة مجالات الممارسة الخاصة']],
            // ['name' => 'view-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'View Practice Areas', 'ar' => 'عرض مجالات الممارسة']],
            // ['name' => 'create-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Create Practice Areas', 'ar' => 'إنشاء مجالات الممارسة']],
            // ['name' => 'edit-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Edit Practice Areas', 'ar' => 'تعديل مجالات الممارسة']],
            // ['name' => 'delete-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Delete Practice Areas', 'ar' => 'حذف مجالات الممارسة']],
            // ['name' => 'toggle-status-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Toggle Status Practice Areas', 'ar' => 'تبديل حالة مجالات الممارسة']],

            // Case Document management
            ['name' => 'manage-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage Case Documents', 'ar' => 'إدارة مستندات القضايا']],
            ['name' => 'manage-any-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage All Case Documents', 'ar' => 'إدارة جميع مستندات القضايا']],

            ['name' => 'manage-own-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage Own Case Documents', 'ar' => 'إدارة مستندات القضايا الخاصة']],

            ['name' => 'view-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'View Case Documents', 'ar' => 'عرض مستندات القضايا']],

            ['name' => 'create-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Create Case Documents', 'ar' => 'إنشاء مستندات القضايا']],

            ['name' => 'edit-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Edit Case Documents', 'ar' => 'تعديل مستندات القضايا']],

            ['name' => 'delete-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Delete Case Documents', 'ar' => 'حذف مستندات القضايا']],

            ['name' => 'download-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Download Case Documents', 'ar' => 'تحميل مستندات القضايا']],

            // Case Note management
            ['name' => 'manage-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage Case Notes', 'ar' => 'إدارة مذكرات القضايا']],

            ['name' => 'manage-any-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage All Case Notes', 'ar' => 'إدارة جميع مذكرات القضايا']],

            ['name' => 'manage-own-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage Own Case Notes', 'ar' => 'إدارة مذكرات القضايا الخاصة']],

            ['name' => 'view-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'View Case Notes', 'ar' => 'عرض مذكرات القضايا']],

            ['name' => 'create-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Create Case Notes', 'ar' => 'إنشاء مذكرات القضايا']],

            ['name' => 'edit-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Edit Case Notes', 'ar' => 'تعديل مذكرات القضايا']],

            ['name' => 'delete-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Delete Case Notes', 'ar' => 'حذف مذكرات القضايا']],

            // Case Management
            ['name' => 'manage-cases', 'module' => 'cases', 'label' => ['en' => 'Manage Cases', 'ar' => 'إدارة القضايا']],

            ['name' => 'manage-any-cases', 'module' => 'cases', 'label' => ['en' => 'Manage All Cases', 'ar' => 'إدارة جميع القضايا']],

            ['name' => 'manage-own-cases', 'module' => 'cases', 'label' => ['en' => 'Manage Own Cases', 'ar' => 'إدارة القضايا الخاصة']],

            ['name' => 'view-cases', 'module' => 'cases', 'label' => ['en' => 'View Cases', 'ar' => 'عرض القضايا']],

            ['name' => 'create-cases', 'module' => 'cases', 'label' => ['en' => 'Create Cases', 'ar' => 'إنشاء القضايا']],

            ['name' => 'edit-cases', 'module' => 'cases', 'label' => ['en' => 'Edit Cases', 'ar' => 'تعديل القضايا']],

            ['name' => 'delete-cases', 'module' => 'cases', 'label' => ['en' => 'Delete Cases', 'ar' => 'حذف القضايا']],

            ['name' => 'toggle-status-cases', 'module' => 'cases', 'label' => ['en' => 'Toggle Status Cases', 'ar' => 'تبديل حالة القضايا']],

            // Case Types
            ['name' => 'manage-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage Case Types', 'ar' => 'إدارة أنواع القضايا']],

            ['name' => 'manage-any-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage All Case Types', 'ar' => 'إدارة جميع أنواع القضايا']],

            ['name' => 'manage-own-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage Own Case Types', 'ar' => 'إدارة أنواع القضايا الخاصة']],

            ['name' => 'view-case-types', 'module' => 'case_types', 'label' => ['en' => 'View Case Types', 'ar' => 'عرض أنواع القضايا']],

            ['name' => 'create-case-types', 'module' => 'case_types', 'label' => ['en' => 'Create Case Types', 'ar' => 'إنشاء أنواع القضايا']],

            ['name' => 'edit-case-types', 'module' => 'case_types', 'label' => ['en' => 'Edit Case Types', 'ar' => 'تعديل أنواع القضايا']],

            ['name' => 'delete-case-types', 'module' => 'case_types', 'label' => ['en' => 'Delete Case Types', 'ar' => 'حذف أنواع القضايا']],

            ['name' => 'toggle-status-case-types', 'module' => 'case_types', 'label' => ['en' => 'Toggle Status Case Types', 'ar' => 'تبديل حالة أنواع القضايا']],

            // Case Categories
            ['name' => 'manage-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage Case Categories', 'ar' => 'إدارة فئات القضايا']],

            ['name' => 'manage-any-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage All Case Categories', 'ar' => 'إدارة جميع فئات القضايا']],

            ['name' => 'manage-own-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage Own Case Categories', 'ar' => 'إدارة فئات القضايا الخاصة']],

            ['name' => 'view-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'View Case Categories', 'ar' => 'عرض فئات القضايا']],

            ['name' => 'create-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Create Case Categories', 'ar' => 'إنشاء فئات القضايا']],

            ['name' => 'edit-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Edit Case Categories', 'ar' => 'تعديل فئات القضايا']],

            ['name' => 'delete-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Delete Case Categories', 'ar' => 'حذف فئات القضايا']],

            ['name' => 'toggle-status-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Toggle Status Case Categories', 'ar' => 'تبديل حالة فئات القضايا']],

            // Case Statuses
            ['name' => 'manage-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage Case Statuses', 'ar' => 'إدارة حالات القضايا']],

            ['name' => 'manage-any-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage All Case Statuses', 'ar' => 'إدارة جميع حالات القضايا']],

            ['name' => 'manage-own-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage Own Case Statuses', 'ar' => 'إدارة حالات القضايا الخاصة']],

            ['name' => 'view-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'View Case Statuses', 'ar' => 'عرض حالات القضايا']],

            ['name' => 'create-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Create Case Statuses', 'ar' => 'إنشاء حالات القضايا']],

            ['name' => 'edit-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Edit Case Statuses', 'ar' => 'تعديل حالات القضايا']],

            ['name' => 'delete-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Delete Case Statuses', 'ar' => 'حذف حالات القضايا']],

            ['name' => 'toggle-status-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Toggle Status Case Statuses', 'ar' => 'تبديل حالة حالات القضايا']],

            // Case Timelines
            ['name' => 'manage-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage Case Timelines', 'ar' => 'إدارة الجداول الزمنية للقضايا']],

            ['name' => 'manage-any-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage All Case Timelines', 'ar' => 'إدارة جميع الجداول الزمنية للقضايا']],

            ['name' => 'manage-own-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage Own Case Timelines', 'ar' => 'إدارة الجداول الزمنية للقضايا الخاصة']],

            ['name' => 'view-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'View Case Timelines', 'ar' => 'عرض الجداول الزمنية للقضايا']],

            ['name' => 'create-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Create Case Timelines', 'ar' => 'إنشاء الجداول الزمنية للقضايا']],

            ['name' => 'edit-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Edit Case Timelines', 'ar' => 'تعديل الجداول الزمنية للقضايا']],

            ['name' => 'delete-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Delete Case Timelines', 'ar' => 'حذف الجداول الزمنية للقضايا']],

            ['name' => 'toggle-status-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Toggle Status Case Timelines', 'ar' => 'تبديل حالة الجداول الزمنية للقضايا']],

            // Case Team Members
            ['name' => 'manage-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage Case Team Members', 'ar' => 'إدارة أعضاء فريق القضية']],

            ['name' => 'manage-any-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage All Case Team Members', 'ar' => 'إدارة جميع أعضاء فريق القضية']],

            ['name' => 'manage-own-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage Own Case Team Members', 'ar' => 'إدارة أعضاء فريق القضية الخاصة']],

            ['name' => 'view-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'View Case Team Members', 'ar' => 'عرض أعضاء فريق القضية']],

            ['name' => 'create-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Create Case Team Members', 'ar' => 'إنشاء أعضاء فريق القضية']],

            ['name' => 'edit-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Edit Case Team Members', 'ar' => 'تعديل أعضاء فريق القضية']],

            ['name' => 'delete-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Delete Case Team Members', 'ar' => 'حذف أعضاء فريق القضية']],

            ['name' => 'toggle-status-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Toggle Status Case Team Members', 'ar' => 'تبديل حالة أعضاء فريق القضية']],

            // Document Types
            ['name' => 'manage-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage Document Types', 'ar' => 'إدارة أنواع المستندات']],

            ['name' => 'manage-any-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage All Document Types', 'ar' => 'إدارة جميع أنواع المستندات']],

            ['name' => 'manage-own-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage Own Document Types', 'ar' => 'إدارة أنواع المستندات الخاصة']],

            ['name' => 'view-document-types', 'module' => 'document_types', 'label' => ['en' => 'View Document Types', 'ar' => 'عرض أنواع المستندات']],

            ['name' => 'create-document-types', 'module' => 'document_types', 'label' => ['en' => 'Create Document Types', 'ar' => 'إنشاء أنواع المستندات']],

            ['name' => 'edit-document-types', 'module' => 'document_types', 'label' => ['en' => 'Edit Document Types', 'ar' => 'تعديل أنواع المستندات']],

            ['name' => 'delete-document-types', 'module' => 'document_types', 'label' => ['en' => 'Delete Document Types', 'ar' => 'حذف أنواع المستندات']],

            // Document Categories
            ['name' => 'manage-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage Document Categories', 'ar' => 'إدارة فئات المستندات']],

            ['name' => 'manage-any-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage All Document Categories', 'ar' => 'إدارة جميع فئات المستندات']],

            ['name' => 'manage-own-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage Own Document Categories', 'ar' => 'إدارة فئات المستندات الخاصة']],

            ['name' => 'view-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'View Document Categories', 'ar' => 'عرض فئات المستندات']],

            ['name' => 'create-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Create Document Categories', 'ar' => 'إنشاء فئات المستندات']],

            ['name' => 'edit-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Edit Document Categories', 'ar' => 'تعديل فئات المستندات']],

            ['name' => 'delete-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Delete Document Categories', 'ar' => 'حذف فئات المستندات']],

            ['name' => 'toggle-status-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Toggle Status Document Categories', 'ar' => 'تبديل حالة فئات المستندات']],

            // Event Types
            ['name' => 'manage-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage Event Types', 'ar' => 'إدارة أنواع الفعاليات']],

            ['name' => 'manage-any-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage All Event Types', 'ar' => 'إدارة جميع أنواع الفعاليات']],

            ['name' => 'manage-own-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage Own Event Types', 'ar' => 'إدارة أنواع الفعاليات الخاصة']],

            ['name' => 'view-event-types', 'module' => 'event_types', 'label' => ['en' => 'View Event Types', 'ar' => 'عرض أنواع الفعاليات']],

            ['name' => 'create-event-types', 'module' => 'event_types', 'label' => ['en' => 'Create Event Types', 'ar' => 'إنشاء أنواع الفعاليات']],

            ['name' => 'edit-event-types', 'module' => 'event_types', 'label' => ['en' => 'Edit Event Types', 'ar' => 'تعديل أنواع الفعاليات']],

            ['name' => 'delete-event-types', 'module' => 'event_types', 'label' => ['en' => 'Delete Event Types', 'ar' => 'حذف أنواع الفعاليات']],

            // Court Types
            ['name' => 'manage-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage Court Types', 'ar' => 'إدارة أنواع المحاكم']],

            ['name' => 'manage-any-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage All Court Types', 'ar' => 'إدارة جميع أنواع المحاكم']],

            ['name' => 'manage-own-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage Own Court Types', 'ar' => 'إدارة أنواع المحاكم الخاصة']],

            ['name' => 'view-court-types', 'module' => 'court_types', 'label' => ['en' => 'View Court Types', 'ar' => 'عرض أنواع المحاكم']],

            ['name' => 'create-court-types', 'module' => 'court_types', 'label' => ['en' => 'Create Court Types', 'ar' => 'إنشاء أنواع المحاكم']],

            ['name' => 'edit-court-types', 'module' => 'court_types', 'label' => ['en' => 'Edit Court Types', 'ar' => 'تعديل أنواع المحاكم']],

            ['name' => 'delete-court-types', 'module' => 'court_types', 'label' => ['en' => 'Delete Court Types', 'ar' => 'حذف أنواع المحاكم']],

            // Circle Types
            ['name' => 'manage-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage Circle Types', 'ar' => 'إدارة أنواع الدوائر']],

            ['name' => 'manage-any-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage All Circle Types', 'ar' => 'إدارة جميع أنواع الدوائر']],

            ['name' => 'manage-own-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage Own Circle Types', 'ar' => 'إدارة أنواع الدوائر الخاصة']],

            ['name' => 'view-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'View Circle Types', 'ar' => 'عرض أنواع الدوائر']],

            ['name' => 'create-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Create Circle Types', 'ar' => 'إنشاء أنواع الدوائر']],

            ['name' => 'edit-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Edit Circle Types', 'ar' => 'تعديل أنواع الدوائر']],

            ['name' => 'delete-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Delete Circle Types', 'ar' => 'حذف أنواع الدوائر']],

            // Hearings
            ['name' => 'manage-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage Hearings', 'ar' => 'إدارة الجلسات']],

            ['name' => 'manage-any-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage All Hearings', 'ar' => 'إدارة جميع الجلسات']],

            ['name' => 'manage-own-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage Own Hearings', 'ar' => 'إدارة الجلسات الخاصة']],

            ['name' => 'view-hearings', 'module' => 'hearings', 'label' => ['en' => 'View Hearings', 'ar' => 'عرض الجلسات']],

            ['name' => 'create-hearings', 'module' => 'hearings', 'label' => ['en' => 'Create Hearings', 'ar' => 'إنشاء الجلسات']],

            ['name' => 'edit-hearings', 'module' => 'hearings', 'label' => ['en' => 'Edit Hearings', 'ar' => 'تعديل الجلسات']],

            ['name' => 'delete-hearings', 'module' => 'hearings', 'label' => ['en' => 'Delete Hearings', 'ar' => 'حذف الجلسات']],

            // Court Management
            ['name' => 'manage-courts', 'module' => 'courts', 'label' => ['en' => 'Manage Courts', 'ar' => 'إدارة المحاكم']],

            ['name' => 'manage-any-courts', 'module' => 'courts', 'label' => ['en' => 'Manage All Courts', 'ar' => 'إدارة جميع المحاكم']],

            ['name' => 'manage-own-courts', 'module' => 'courts', 'label' => ['en' => 'Manage Own Courts', 'ar' => 'إدارة المحاكم الخاصة']],

            ['name' => 'view-courts', 'module' => 'courts', 'label' => ['en' => 'View Courts', 'ar' => 'عرض المحاكم']],

            ['name' => 'create-courts', 'module' => 'courts', 'label' => ['en' => 'Create Courts', 'ar' => 'إنشاء المحاكم']],

            ['name' => 'edit-courts', 'module' => 'courts', 'label' => ['en' => 'Edit Courts', 'ar' => 'تعديل المحاكم']],

            ['name' => 'delete-courts', 'module' => 'courts', 'label' => ['en' => 'Delete Courts', 'ar' => 'حذف المحاكم']],

            ['name' => 'toggle-status-courts', 'module' => 'courts', 'label' => ['en' => 'Toggle Status Courts', 'ar' => 'تبديل حالة المحاكم']],

            // Hearing Type Management
            ['name' => 'manage-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage Hearing Types', 'ar' => 'إدارة أنواع الجلسات']],

            ['name' => 'manage-any-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage All Hearing Types', 'ar' => 'إدارة جميع أنواع الجلسات']],

            ['name' => 'manage-own-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage Own Hearing Types', 'ar' => 'إدارة أنواع الجلسات الخاصة']],

            ['name' => 'view-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'View Hearing Types', 'ar' => 'عرض أنواع الجلسات']],

            ['name' => 'create-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Create Hearing Types', 'ar' => 'إنشاء أنواع الجلسات']],

            ['name' => 'edit-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Edit Hearing Types', 'ar' => 'تعديل أنواع الجلسات']],

            ['name' => 'delete-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Delete Hearing Types', 'ar' => 'حذف أنواع الجلسات']],

            ['name' => 'toggle-status-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Toggle Status Hearing Types', 'ar' => 'تبديل حالة أنواع الجلسات']],

            // Documents
            ['name' => 'manage-documents', 'module' => 'documents', 'label' => ['en' => 'Manage Documents', 'ar' => 'إدارة المستندات']],

            ['name' => 'manage-any-documents', 'module' => 'documents', 'label' => ['en' => 'Manage All Documents', 'ar' => 'إدارة جميع المستندات']],

            ['name' => 'manage-own-documents', 'module' => 'documents', 'label' => ['en' => 'Manage Own Documents', 'ar' => 'إدارة المستندات الخاصة']],

            ['name' => 'view-documents', 'module' => 'documents', 'label' => ['en' => 'View Documents', 'ar' => 'عرض المستندات']],

            ['name' => 'create-documents', 'module' => 'documents', 'label' => ['en' => 'Create Documents', 'ar' => 'إنشاء المستندات']],

            ['name' => 'edit-documents', 'module' => 'documents', 'label' => ['en' => 'Edit Documents', 'ar' => 'تعديل المستندات']],

            ['name' => 'delete-documents', 'module' => 'documents', 'label' => ['en' => 'Delete Documents', 'ar' => 'حذف المستندات']],

            ['name' => 'download-documents', 'module' => 'documents', 'label' => ['en' => 'Download Documents', 'ar' => 'تحميل المستندات']],

            ['name' => 'toggle-status-documents', 'module' => 'documents', 'label' => ['en' => 'Toggle Status Documents', 'ar' => 'تبديل حالة المستندات']],

            // Document Versions
            ['name' => 'manage-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage Document Versions', 'ar' => 'إدارة إصدارات المستندات']],

            ['name' => 'manage-any-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage All Document Versions', 'ar' => 'إدارة جميع إصدارات المستندات']],

            ['name' => 'manage-own-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage Own Document Versions', 'ar' => 'إدارة إصدارات المستندات الخاصة']],

            ['name' => 'view-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'View Document Versions', 'ar' => 'عرض إصدارات المستندات']],

            ['name' => 'create-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Create Document Versions', 'ar' => 'إنشاء إصدارات المستندات']],

            ['name' => 'delete-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Delete Document Versions', 'ar' => 'حذف إصدارات المستندات']],

            ['name' => 'download-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Download Document Versions', 'ar' => 'تحميل إصدارات المستندات']],

            ['name' => 'restore-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Restore Document Versions', 'ar' => 'استعادة إصدارات المستندات']],

            // Document Comments
            ['name' => 'manage-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage Document Comments', 'ar' => 'إدارة تعليقات المستندات']],

            ['name' => 'manage-any-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage All Document Comments', 'ar' => 'إدارة جميع تعليقات المستندات']],

            ['name' => 'manage-own-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage Own Document Comments', 'ar' => 'إدارة تعليقات المستندات الخاصة']],

            ['name' => 'view-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'View Document Comments', 'ar' => 'عرض تعليقات المستندات']],

            ['name' => 'create-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Create Document Comments', 'ar' => 'إنشاء تعليقات المستندات']],

            ['name' => 'edit-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Edit Document Comments', 'ar' => 'تعديل تعليقات المستندات']],

            ['name' => 'delete-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Delete Document Comments', 'ar' => 'حذف تعليقات المستندات']],

            ['name' => 'resolve-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Resolve Document Comments', 'ar' => 'حل تعليقات المستندات']],

            // Document Permissions
            ['name' => 'manage-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage Document Permissions', 'ar' => 'إدارة صلاحيات المستندات']],

            ['name' => 'manage-any-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage All Document Permissions', 'ar' => 'إدارة جميع صلاحيات المستندات']],

            ['name' => 'manage-own-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage Own Document Permissions', 'ar' => 'إدارة صلاحيات المستندات الخاصة']],

            ['name' => 'view-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'View Document Permissions', 'ar' => 'عرض صلاحيات المستندات']],

            ['name' => 'create-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Create Document Permissions', 'ar' => 'إنشاء صلاحيات المستندات']],

            ['name' => 'edit-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Edit Document Permissions', 'ar' => 'تعديل صلاحيات المستندات']],

            ['name' => 'delete-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Delete Document Permissions', 'ar' => 'حذف صلاحيات المستندات']],

            // Research Projects
            ['name' => 'manage-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage Research Projects', 'ar' => 'إدارة مشاريع البحث']],

            ['name' => 'manage-any-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage All Research Projects', 'ar' => 'إدارة جميع مشاريع البحث']],

            ['name' => 'manage-own-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage Own Research Projects', 'ar' => 'إدارة مشاريع البحث الخاصة']],

            ['name' => 'view-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'View Research Projects', 'ar' => 'عرض مشاريع البحث']],

            ['name' => 'create-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Create Research Projects', 'ar' => 'إنشاء مشاريع البحث']],

            ['name' => 'edit-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Edit Research Projects', 'ar' => 'تعديل مشاريع البحث']],

            ['name' => 'delete-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Delete Research Projects', 'ar' => 'حذف مشاريع البحث']],

            ['name' => 'toggle-status-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Toggle Status Research Projects', 'ar' => 'تبديل حالة مشاريع البحث']],

            // Research Sources
            ['name' => 'manage-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage Research Sources', 'ar' => 'إدارة مصادر البحث']],

            ['name' => 'manage-any-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage All Research Sources', 'ar' => 'إدارة جميع مصادر البحث']],

            ['name' => 'manage-own-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage Own Research Sources', 'ar' => 'إدارة مصادر البحث الخاصة']],

            ['name' => 'view-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'View Research Sources', 'ar' => 'عرض مصادر البحث']],

            ['name' => 'create-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Create Research Sources', 'ar' => 'إنشاء مصادر البحث']],

            ['name' => 'edit-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Edit Research Sources', 'ar' => 'تعديل مصادر البحث']],

            ['name' => 'delete-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Delete Research Sources', 'ar' => 'حذف مصادر البحث']],

            ['name' => 'toggle-status-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Toggle Status Research Sources', 'ar' => 'تبديل حالة مصادر البحث']],

            // Research Categories
            ['name' => 'manage-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage Research Categories', 'ar' => 'إدارة فئات البحث']],

            ['name' => 'manage-any-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage All Research Categories', 'ar' => 'إدارة جميع فئات البحث']],

            ['name' => 'manage-own-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage Own Research Categories', 'ar' => 'إدارة فئات البحث الخاصة']],

            ['name' => 'view-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'View Research Categories', 'ar' => 'عرض فئات البحث']],

            ['name' => 'create-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Create Research Categories', 'ar' => 'إنشاء فئات البحث']],

            ['name' => 'edit-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Edit Research Categories', 'ar' => 'تعديل فئات البحث']],

            ['name' => 'delete-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Delete Research Categories', 'ar' => 'حذف فئات البحث']],

            ['name' => 'toggle-status-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Toggle Status Research Categories', 'ar' => 'تبديل حالة فئات البحث']],

            // Knowledge Articles
            // ['name' => 'manage-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage Knowledge Articles', 'ar' => 'إدارة مقالات المعرفة']],
            //
            // ['name' => 'manage-any-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage All Knowledge Articles', 'ar' => 'إدارة جميع مقالات المعرفة']],
            //
            // ['name' => 'manage-own-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage Own Knowledge Articles', 'ar' => 'إدارة مقالات المعرفة الخاصة']],
            //
            // ['name' => 'view-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'View Knowledge Articles', 'ar' => 'عرض مقالات المعرفة']],
            //
            // ['name' => 'create-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Create Knowledge Articles', 'ar' => 'إنشاء مقالات المعرفة']],
            //
            // ['name' => 'edit-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Edit Knowledge Articles', 'ar' => 'تعديل مقالات المعرفة']],
            //
            // ['name' => 'delete-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Delete Knowledge Articles', 'ar' => 'حذف مقالات المعرفة']],
            //
            // ['name' => 'publish-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Publish Knowledge Articles', 'ar' => 'نشر مقالات المعرفة']],

            // Legal Precedents
            // ['name' => 'manage-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage Legal Precedents', 'ar' => 'إدارة السوابق القانونية']],
            //
            // ['name' => 'manage-any-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage All Legal Precedents', 'ar' => 'إدارة جميع السوابق القانونية']],
            //
            // ['name' => 'manage-own-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage Own Legal Precedents', 'ar' => 'إدارة السوابق القانونية الخاصة']],
            //
            // ['name' => 'view-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'View Legal Precedents', 'ar' => 'عرض السوابق القانونية']],
            //
            // ['name' => 'create-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Create Legal Precedents', 'ar' => 'إنشاء السوابق القانونية']],
            //
            // ['name' => 'edit-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Edit Legal Precedents', 'ar' => 'تعديل السوابق القانونية']],
            //
            // ['name' => 'delete-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Delete Legal Precedents', 'ar' => 'حذف السوابق القانونية']],
            //
            // ['name' => 'toggle-status-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Toggle Status Legal Precedents', 'ar' => 'تبديل حالة السوابق القانونية']],

            // Research Notes
            ['name' => 'manage-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage Research Notes', 'ar' => 'إدارة ملاحظات البحث']],

            ['name' => 'manage-any-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage All Research Notes', 'ar' => 'إدارة جميع ملاحظات البحث']],

            ['name' => 'manage-own-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage Own Research Notes', 'ar' => 'إدارة ملاحظات البحث الخاصة']],

            ['name' => 'view-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'View Research Notes', 'ar' => 'عرض ملاحظات البحث']],

            ['name' => 'create-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Create Research Notes', 'ar' => 'إنشاء ملاحظات البحث']],

            ['name' => 'edit-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Edit Research Notes', 'ar' => 'تعديل ملاحظات البحث']],

            ['name' => 'delete-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Delete Research Notes', 'ar' => 'حذف ملاحظات البحث']],

            // Research Citations
            ['name' => 'manage-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage Research Citations', 'ar' => 'إدارة استشهادات البحث']],
            ['name' => 'manage-any-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage All Research Citations', 'ar' => 'إدارة جميع استشهادات البحث']],
            ['name' => 'manage-own-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage Own Research Citations', 'ar' => 'إدارة استشهادات البحث الخاصة']],
            ['name' => 'view-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'View Research Citations', 'ar' => 'عرض استشهادات البحث']],
            ['name' => 'create-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Create Research Citations', 'ar' => 'إنشاء استشهادات البحث']],
            ['name' => 'edit-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Edit Research Citations', 'ar' => 'تعديل استشهادات البحث']],
            ['name' => 'delete-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Delete Research Citations', 'ar' => 'حذف استشهادات البحث']],

            // Research Types
            ['name' => 'manage-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage Research Types', 'ar' => 'إدارة أنواع البحث']],

            ['name' => 'manage-any-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage All Research Types', 'ar' => 'إدارة جميع أنواع البحث']],

            ['name' => 'manage-own-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage Own Research Types', 'ar' => 'إدارة أنواع البحث الخاصة']],

            ['name' => 'view-research-types', 'module' => 'research_types', 'label' => ['en' => 'View Research Types', 'ar' => 'عرض أنواع البحث']],

            ['name' => 'create-research-types', 'module' => 'research_types', 'label' => ['en' => 'Create Research Types', 'ar' => 'إنشاء أنواع البحث']],

            ['name' => 'edit-research-types', 'module' => 'research_types', 'label' => ['en' => 'Edit Research Types', 'ar' => 'تعديل أنواع البحث']],

            ['name' => 'delete-research-types', 'module' => 'research_types', 'label' => ['en' => 'Delete Research Types', 'ar' => 'حذف أنواع البحث']],

            ['name' => 'toggle-status-research-types', 'module' => 'research_types', 'label' => ['en' => 'Toggle Status Research Types', 'ar' => 'تبديل حالة أنواع البحث']],

            // Compliance Requirements
            // ['name' => 'manage-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage Compliance Requirements', 'ar' => 'إدارة متطلبات الامتثال']],
            //
            // ['name' => 'manage-any-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage All Compliance Requirements', 'ar' => 'إدارة جميع متطلبات الامتثال']],
            //
            // ['name' => 'manage-own-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage Own Compliance Requirements', 'ar' => 'إدارة متطلبات الامتثال الخاصة']],
            //
            // ['name' => 'view-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'View Compliance Requirements', 'ar' => 'عرض متطلبات الامتثال']],
            //
            // ['name' => 'create-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Create Compliance Requirements', 'ar' => 'إنشاء متطلبات الامتثال']],
            //
            // ['name' => 'edit-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Edit Compliance Requirements', 'ar' => 'تعديل متطلبات الامتثال']],
            //
            // ['name' => 'delete-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Delete Compliance Requirements', 'ar' => 'حذف متطلبات الامتثال']],
            //
            // ['name' => 'toggle-status-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Toggle Status Compliance Requirements', 'ar' => 'تبديل حالة متطلبات الامتثال']],

            // Compliance Categories
            // ['name' => 'manage-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage Compliance Categories', 'ar' => 'إدارة فئات الامتثال']],
            //
            // ['name' => 'manage-any-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage All Compliance Categories', 'ar' => 'إدارة جميع فئات الامتثال']],
            //
            // ['name' => 'manage-own-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage Own Compliance Categories', 'ar' => 'إدارة فئات الامتثال الخاصة']],
            //
            // ['name' => 'view-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'View Compliance Categories', 'ar' => 'عرض فئات الامتثال']],
            //
            // ['name' => 'create-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Create Compliance Categories', 'ar' => 'إنشاء فئات الامتثال']],
            //
            // ['name' => 'edit-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Edit Compliance Categories', 'ar' => 'تعديل فئات الامتثال']],
            //
            // ['name' => 'delete-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Delete Compliance Categories', 'ar' => 'حذف فئات الامتثال']],
            //
            // ['name' => 'toggle-status-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Toggle Status Compliance Categories', 'ar' => 'تبديل حالة فئات الامتثال']],

            // Compliance Frequencies
            // ['name' => 'manage-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage Compliance Frequencies', 'ar' => 'إدارة ترددات الامتثال']],
            //
            // ['name' => 'manage-any-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage All Compliance Frequencies', 'ar' => 'إدارة جميع ترددات الامتثال']],
            //
            // ['name' => 'manage-own-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage Own Compliance Frequencies', 'ar' => 'إدارة ترددات الامتثال الخاصة']],
            //
            // ['name' => 'view-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'View Compliance Frequencies', 'ar' => 'عرض ترددات الامتثال']],
            //
            // ['name' => 'create-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Create Compliance Frequencies', 'ar' => 'إنشاء ترددات الامتثال']],
            //
            // ['name' => 'edit-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Edit Compliance Frequencies', 'ar' => 'تعديل ترددات الامتثال']],
            //
            // ['name' => 'delete-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Delete Compliance Frequencies', 'ar' => 'حذف ترددات الامتثال']],
            //
            // ['name' => 'toggle-status-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Toggle Status Compliance Frequencies', 'ar' => 'تبديل حالة ترددات الامتثال']],

            // Professional Licenses
            // ['name' => 'manage-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage Professional Licenses', 'ar' => 'إدارة التراخيص المهنية']],
            //
            // ['name' => 'manage-any-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage All Professional Licenses', 'ar' => 'إدارة جميع التراخيص المهنية']],
            //
            // ['name' => 'manage-own-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage Own Professional Licenses', 'ar' => 'إدارة التراخيص المهنية الخاصة']],
            //
            // ['name' => 'view-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'View Professional Licenses', 'ar' => 'عرض التراخيص المهنية']],
            //
            // ['name' => 'create-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Create Professional Licenses', 'ar' => 'Create Professional Licenses']],
            //
            // ['name' => 'edit-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Edit Professional Licenses', 'ar' => 'تعديل التراخيص المهنية']],
            //
            // ['name' => 'delete-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Delete Professional Licenses', 'ar' => 'Delete Professional Licenses']],
            //
            // ['name' => 'toggle-status-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Toggle Status Professional Licenses', 'ar' => 'تبديل حالة التراخيص المهنية']],

            // Regulatory Bodies
            // ['name' => 'manage-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage Regulatory Bodies', 'ar' => 'إدارة الهيئات التنظيمية']],
            //
            // ['name' => 'manage-any-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage All Regulatory Bodies', 'ar' => 'إدارة جميع الهيئات التنظيمية']],
            //
            // ['name' => 'manage-own-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage Own Regulatory Bodies', 'ar' => 'Manage Own Regulatory Bodies']],
            //
            // ['name' => 'view-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'View Regulatory Bodies', 'ar' => 'عرض الهيئات التنظيمية']],
            //
            // ['name' => 'create-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Create Regulatory Bodies', 'ar' => 'إنشاء الهيئات التنظيمية']],
            //
            // ['name' => 'edit-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Edit Regulatory Bodies', 'ar' => 'تعديل الهيئات التنظيمية']],
            //
            // ['name' => 'delete-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Delete Regulatory Bodies', 'ar' => 'حذف الهيئات التنظيمية']],
            //
            // ['name' => 'toggle-status-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Toggle Status Regulatory Bodies', 'ar' => 'Toggle Status Regulatory Bodies']],

            // Compliance Policies
            // ['name' => 'manage-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage Compliance Policies', 'ar' => 'إدارة سياسات الامتثال']],
            //
            // ['name' => 'manage-any-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage All Compliance Policies', 'ar' => 'إدارة جميع سياسات الامتثال']],
            //
            // ['name' => 'manage-own-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage Own Compliance Policies', 'ar' => 'إدارة سياسات الامتثال الخاصة']],
            //
            // ['name' => 'view-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'View Compliance Policies', 'ar' => 'عرض سياسات الامتثال']],
            //
            // ['name' => 'create-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Create Compliance Policies', 'ar' => 'إنشاء سياسات الامتثال']],
            //
            // ['name' => 'edit-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Edit Compliance Policies', 'ar' => 'تعديل سياسات الامتثال']],
            //
            // ['name' => 'delete-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Delete Compliance Policies', 'ar' => 'حذف سياسات الامتثال']],
            //
            // ['name' => 'toggle-status-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Toggle Status Compliance Policies', 'ar' => 'تبديل حالة سياسات الامتثال']],

            // CLE Tracking
            // ['name' => 'manage-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage CLE Tracking', 'ar' => 'إدارة تتبع CLE']],
            //
            // ['name' => 'manage-any-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage All CLE Tracking', 'ar' => 'إدارة جميع تتبع CLE']],
            //
            // ['name' => 'manage-own-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage Own CLE Tracking', 'ar' => 'إدارة تتبع CLE الخاصة']],
            //
            // ['name' => 'view-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'View CLE Tracking', 'ar' => 'عرض تتبع CLE']],
            //
            // ['name' => 'create-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Create CLE Tracking', 'ar' => 'إنشاء تتبع CLE']],
            //
            // ['name' => 'edit-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Edit CLE Tracking', 'ar' => 'تعديل تتبع CLE']],
            //
            // ['name' => 'delete-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Delete CLE Tracking', 'ar' => 'حذف تتبع CLE']],
            //
            // ['name' => 'download-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Download CLE Tracking', 'ar' => 'تحميل تتبع CLE']],

            // Risk Categories
            // ['name' => 'manage-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage Risk Categories', 'ar' => 'إدارة فئات المخاطر']],
            //
            // ['name' => 'manage-any-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage All Risk Categories', 'ar' => 'إدارة جميع فئات المخاطر']],
            //
            // ['name' => 'manage-own-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage Own Risk Categories', 'ar' => 'إدارة فئات المخاطر الخاصة']],
            //
            // ['name' => 'view-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'View Risk Categories', 'ar' => 'عرض فئات المخاطر']],
            //
            // ['name' => 'create-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Create Risk Categories', 'ar' => 'إنشاء فئات المخاطر']],
            //
            // ['name' => 'edit-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Edit Risk Categories', 'ar' => 'تعديل فئات المخاطر']],
            //
            // ['name' => 'delete-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Delete Risk Categories', 'ar' => 'Delete Risk Categories']],
            //
            // ['name' => 'toggle-status-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Toggle Status Risk Categories', 'ar' => 'تبديل حالة فئات المخاطر']],

            // Risk Assessments
            // ['name' => 'manage-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage Risk Assessments', 'ar' => 'إدارة تقييمات المخاطر']],
            //
            // ['name' => 'manage-any-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage All Risk Assessments', 'ar' => 'إدارة جميع تقييمات المخاطر']],
            //
            // ['name' => 'manage-own-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage Own Risk Assessments', 'ar' => 'إدارة تقييمات المخاطر الخاصة']],
            //
            // ['name' => 'view-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'View Risk Assessments', 'ar' => 'عرض تقييمات المخاطر']],
            //
            // ['name' => 'create-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Create Risk Assessments', 'ar' => 'إنشاء تقييمات المخاطر']],
            //
            // ['name' => 'edit-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Edit Risk Assessments', 'ar' => 'تعديل تقييمات المخاطر']],
            //
            // ['name' => 'delete-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Delete Risk Assessments', 'ar' => 'حذف تقييمات المخاطر']],

            // Audit Types
            // ['name' => 'manage-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage Audit Types', 'ar' => 'إدارة أنواع التدقيق']],
            //
            // ['name' => 'manage-any-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage All Audit Types', 'ar' => 'إدارة جميع أنواع التدقيق']],
            //
            // ['name' => 'manage-own-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage Own Audit Types', 'ar' => 'إدارة أنواع التدقيق الخاصة']],
            //
            // ['name' => 'view-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'View Audit Types', 'ar' => 'عرض أنواع التدقيق']],
            //
            // ['name' => 'create-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Create Audit Types', 'ar' => 'إنشاء أنواع التدقيق']],
            //
            // ['name' => 'edit-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Edit Audit Types', 'ar' => 'Edit Audit Types']],
            //
            // ['name' => 'delete-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Delete Audit Types', 'ar' => 'حذف أنواع التدقيق']],
            //
            // ['name' => 'toggle-status-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Toggle Status Audit Types', 'ar' => 'تبديل حالة أنواع التدقيق']],

            // Compliance Audits
            // ['name' => 'manage-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage Compliance Audits', 'ar' => 'إدارة تدقيقات الامتثال']],
            //
            // ['name' => 'manage-any-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage All Compliance Audits', 'ar' => 'إدارة جميع تدقيقات الامتثال']],
            //
            // ['name' => 'manage-own-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage Own Compliance Audits', 'ar' => 'إدارة تدقيقات الامتثال الخاصة']],
            //
            // ['name' => 'view-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'View Compliance Audits', 'ar' => 'عرض تدقيقات الامتثال']],
            //
            // ['name' => 'create-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Create Compliance Audits', 'ar' => 'إنشاء تدقيقات الامتثال']],
            //
            // ['name' => 'edit-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Edit Compliance Audits', 'ar' => 'تعديل تدقيقات الامتثال']],
            //
            // ['name' => 'delete-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Delete Compliance Audits', 'ar' => 'حذف تدقيقات الامتثال']],

            // Time Entries
            ['name' => 'manage-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage Time Entries', 'ar' => 'إدارة قيود الوقت']],

            ['name' => 'manage-any-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage All Time Entries', 'ar' => 'إدارة جميع قيود الوقت']],

            ['name' => 'manage-own-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage Own Time Entries', 'ar' => 'إدارة قيود الوقت الخاصة']],

            ['name' => 'view-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'View Time Entries', 'ar' => 'عرض قيود الوقت']],

            ['name' => 'create-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Create Time Entries', 'ar' => 'إنشاء قيود الوقت']],

            ['name' => 'edit-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Edit Time Entries', 'ar' => 'Edit Time Entries']],

            ['name' => 'delete-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Delete Time Entries', 'ar' => 'حذف قيود الوقت']],

            ['name' => 'approve-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Approve Time Entries', 'ar' => 'الموافقة على قيود الوقت']],

            ['name' => 'start-timer', 'module' => 'time_entries', 'label' => ['en' => 'Start Timer', 'ar' => 'بدء المؤقت']],

            ['name' => 'stop-timer', 'module' => 'time_entries', 'label' => ['en' => 'Stop Timer', 'ar' => 'إيقاف المؤقت']],

            // Billing Rates
            ['name' => 'manage-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage Billing Rates', 'ar' => 'إدارة أسعار الفوترة']],

            ['name' => 'manage-any-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage All Billing Rates', 'ar' => 'إدارة جميع أسعار الفوترة']],

            ['name' => 'manage-own-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage Own Billing Rates', 'ar' => 'إدارة أسعار الفوترة الخاصة']],

            ['name' => 'view-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'View Billing Rates', 'ar' => 'عرض أسعار الفوترة']],

            ['name' => 'create-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Create Billing Rates', 'ar' => 'Create Billing Rates']],

            ['name' => 'edit-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Edit Billing Rates', 'ar' => 'تعديل أسعار الفوترة']],

            ['name' => 'delete-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Delete Billing Rates', 'ar' => 'Delete Billing Rates']],

            ['name' => 'toggle-status-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Toggle Status Billing Rates', 'ar' => 'تبديل حالة أسعار الفوترة']],

            // Fee Types
            // ['name' => 'manage-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage Fee Types', 'ar' => 'إدارة أنواع الرسوم']],
            //
            // ['name' => 'manage-any-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage All Fee Types', 'ar' => 'إدارة جميع أنواع الرسوم']],
            //
            // ['name' => 'manage-own-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage Own Fee Types', 'ar' => 'إدارة أنواع الرسوم الخاصة']],
            //
            // ['name' => 'view-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'View Fee Types', 'ar' => 'عرض أنواع الرسوم']],
            //
            // ['name' => 'create-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Create Fee Types', 'ar' => 'إنشاء أنواع الرسوم']],
            //
            // ['name' => 'edit-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Edit Fee Types', 'ar' => 'تعديل أنواع الرسوم']],
            //
            // ['name' => 'delete-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Delete Fee Types', 'ar' => 'حذف أنواع الرسوم']],
            //
            // ['name' => 'toggle-status-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Toggle Status Fee Types', 'ar' => 'Toggle Status Fee Types']],

            // Fee Structures
            // ['name' => 'manage-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage Fee Structures', 'ar' => 'إدارة هياكل الرسوم']],
            //
            // ['name' => 'manage-any-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage All Fee Structures', 'ar' => 'إدارة جميع هياكل الرسوم']],
            //
            // ['name' => 'manage-own-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage Own Fee Structures', 'ar' => 'إدارة هياكل الرسوم الخاصة']],
            //
            // ['name' => 'view-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'View Fee Structures', 'ar' => 'عرض هياكل الرسوم']],
            //
            // ['name' => 'create-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Create Fee Structures', 'ar' => 'إنشاء هياكل الرسوم']],
            //
            // ['name' => 'edit-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Edit Fee Structures', 'ar' => 'تعديل هياكل الرسوم']],
            //
            // ['name' => 'delete-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Delete Fee Structures', 'ar' => 'حذف هياكل الرسوم']],
            //
            // ['name' => 'toggle-status-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Toggle Status Fee Structures', 'ar' => 'Toggle Status Fee Structures']],

            // Expenses
            ['name' => 'manage-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage Expenses', 'ar' => 'إدارة المصروفات']],

            ['name' => 'manage-any-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage All Expenses', 'ar' => 'Manage All Expenses']],

            ['name' => 'manage-own-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage Own Expenses', 'ar' => 'إدارة المصروفات الخاصة']],

            ['name' => 'view-expenses', 'module' => 'expenses', 'label' => ['en' => 'View Expenses', 'ar' => 'عرض المصروفات']],

            ['name' => 'create-expenses', 'module' => 'expenses', 'label' => ['en' => 'Create Expenses', 'ar' => 'إنشاء المصروفات']],

            ['name' => 'edit-expenses', 'module' => 'expenses', 'label' => ['en' => 'Edit Expenses', 'ar' => 'Edit Expenses']],

            ['name' => 'delete-expenses', 'module' => 'expenses', 'label' => ['en' => 'Delete Expenses', 'ar' => 'حذف المصروفات']],

            ['name' => 'approve-expenses', 'module' => 'expenses', 'label' => ['en' => 'Approve Expenses', 'ar' => 'الموافقة على المصروفات']],

            // Expense Categories
            ['name' => 'manage-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage Expense Categories', 'ar' => 'إدارة فئات المصروفات']],

            ['name' => 'manage-any-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage All Expense Categories', 'ar' => 'إدارة جميع فئات المصروفات']],

            ['name' => 'manage-own-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage Own Expense Categories', 'ar' => 'إدارة فئات المصروفات الخاصة']],

            ['name' => 'view-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'View Expense Categories', 'ar' => 'عرض فئات المصروفات']],

            ['name' => 'create-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Create Expense Categories', 'ar' => 'إنشاء فئات المصروفات']],

            ['name' => 'edit-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Edit Expense Categories', 'ar' => 'تعديل فئات المصروفات']],

            ['name' => 'delete-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Delete Expense Categories', 'ar' => 'حذف فئات المصروفات']],

            ['name' => 'toggle-status-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Toggle Status Expense Categories', 'ar' => 'تبديل حالة فئات المصروفات']],

            // Invoices
            ['name' => 'manage-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage Invoices', 'ar' => 'إدارة الفواتير']],

            ['name' => 'manage-any-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage All Invoices', 'ar' => 'إدارة جميع الفواتير']],

            ['name' => 'manage-own-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage Own Invoices', 'ar' => 'إدارة الفواتير الخاصة']],

            ['name' => 'view-invoices', 'module' => 'invoices', 'label' => ['en' => 'View Invoices', 'ar' => 'عرض الفواتير']],

            ['name' => 'create-invoices', 'module' => 'invoices', 'label' => ['en' => 'Create Invoices', 'ar' => 'إنشاء الفواتير']],

            ['name' => 'edit-invoices', 'module' => 'invoices', 'label' => ['en' => 'Edit Invoices', 'ar' => 'تعديل الفواتير']],

            ['name' => 'delete-invoices', 'module' => 'invoices', 'label' => ['en' => 'Delete Invoices', 'ar' => 'حذف الفواتير']],

            ['name' => 'send-invoices', 'module' => 'invoices', 'label' => ['en' => 'Send Invoices', 'ar' => 'إرسال الفواتير']],

            // Payments
            ['name' => 'manage-payments', 'module' => 'payments', 'label' => ['en' => 'Manage Payments', 'ar' => 'إدارة المدفوعات']],

            ['name' => 'manage-any-payments', 'module' => 'payments', 'label' => ['en' => 'Manage All Payments', 'ar' => 'إدارة جميع المدفوعات']],

            ['name' => 'manage-own-payments', 'module' => 'payments', 'label' => ['en' => 'Manage Own Payments', 'ar' => 'Manage Own Payments']],

            ['name' => 'view-payments', 'module' => 'payments', 'label' => ['en' => 'View Payments', 'ar' => 'عرض المدفوعات']],

            ['name' => 'create-payments', 'module' => 'payments', 'label' => ['en' => 'Create Payments', 'ar' => 'إنشاء المدفوعات']],

            ['name' => 'edit-payments', 'module' => 'payments', 'label' => ['en' => 'Edit Payments', 'ar' => 'Edit Payments']],

            ['name' => 'delete-payments', 'module' => 'payments', 'label' => ['en' => 'Delete Payments', 'ar' => 'حذف المدفوعات']],

            ['name' => 'approve-payments', 'module' => 'payments', 'label' => ['en' => 'Approve Payments', 'ar' => 'الموافقة على المدفوعات']],

            ['name' => 'reject-payments', 'module' => 'payments', 'label' => ['en' => 'Reject Payments', 'ar' => 'رفض المدفوعات']],

            // Task Management
            ['name' => 'manage-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage Tasks', 'ar' => 'إدارة المهام']],

            ['name' => 'manage-any-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage All Tasks', 'ar' => 'إدارة جميع المهام']],

            ['name' => 'manage-own-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage Own Tasks', 'ar' => 'إدارة المهام الخاصة']],

            ['name' => 'view-tasks', 'module' => 'tasks', 'label' => ['en' => 'View Tasks', 'ar' => 'عرض المهام']],

            ['name' => 'create-tasks', 'module' => 'tasks', 'label' => ['en' => 'Create Tasks', 'ar' => 'إنشاء المهام']],

            ['name' => 'edit-tasks', 'module' => 'tasks', 'label' => ['en' => 'Edit Tasks', 'ar' => 'تعديل المهام']],

            ['name' => 'delete-tasks', 'module' => 'tasks', 'label' => ['en' => 'Delete Tasks', 'ar' => 'حذف المهام']],

            ['name' => 'assign-tasks', 'module' => 'tasks', 'label' => ['en' => 'Assign Tasks', 'ar' => 'تعيين المهام']],

            ['name' => 'toggle-status-tasks', 'module' => 'tasks', 'label' => ['en' => 'Toggle Status Tasks', 'ar' => 'تبديل حالة المهام']],

            ['name' => 'task_duplicate', 'module' => 'tasks', 'label' => ['en' => 'Task Duplicate' , 'ar' => 'تكرار المهمة']],
            ['name' => 'task_change_status', 'module' => 'tasks', 'label' => ['en' => 'Task Change Status' , 'ar' => 'تغيير حالة المهمة']],
            ['name' => 'task_view_any', 'module' => 'tasks', 'label' => ['en' => 'Task View Any' , 'ar' => 'عرض أي مهمة']],

            // Task Types
            ['name' => 'manage-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage Task Types', 'ar' => 'إدارة أنواع المهام']],

            ['name' => 'manage-any-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage All Task Types', 'ar' => 'إدارة جميع أنواع المهام']],

            ['name' => 'manage-own-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage Own Task Types', 'ar' => 'إدارة أنواع المهام الخاصة']],

            ['name' => 'view-task-types', 'module' => 'task_types', 'label' => ['en' => 'View Task Types', 'ar' => 'عرض أنواع المهام']],

            ['name' => 'create-task-types', 'module' => 'task_types', 'label' => ['en' => 'Create Task Types', 'ar' => 'إنشاء أنواع المهام']],

            ['name' => 'edit-task-types', 'module' => 'task_types', 'label' => ['en' => 'Edit Task Types', 'ar' => 'تعديل أنواع المهام']],

            ['name' => 'delete-task-types', 'module' => 'task_types', 'label' => ['en' => 'Delete Task Types', 'ar' => 'حذف أنواع المهام']],

            ['name' => 'toggle-status-task-types', 'module' => 'task_types', 'label' => ['en' => 'Toggle Status Task Types', 'ar' => 'تبديل حالة أنواع المهام']],

            // Task Statuses
            ['name' => 'manage-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage Task Statuses', 'ar' => 'إدارة حالات المهام']],

            ['name' => 'manage-any-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage All Task Statuses', 'ar' => 'إدارة جميع حالات المهام']],

            ['name' => 'manage-own-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage Own Task Statuses', 'ar' => 'إدارة حالات المهام الخاصة']],

            ['name' => 'view-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'View Task Statuses', 'ar' => 'عرض حالات المهام']],

            ['name' => 'create-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Create Task Statuses', 'ar' => 'إنشاء حالات المهام']],

            ['name' => 'edit-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Edit Task Statuses', 'ar' => 'تعديل حالات المهام']],

            ['name' => 'delete-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Delete Task Statuses', 'ar' => 'حذف حالات المهام']],

            ['name' => 'toggle-status-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Toggle Status Task Statuses', 'ar' => 'تبديل حالة حالات المهام']],

            // Workflows
            ['name' => 'manage-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage Workflows', 'ar' => 'إدارة سير العمل']],

            ['name' => 'manage-any-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage All Workflows', 'ar' => 'إدارة جميع سير العمل']],

            ['name' => 'manage-own-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage Own Workflows', 'ar' => 'إدارة سير العمل الخاصة']],

            ['name' => 'view-workflows', 'module' => 'workflows', 'label' => ['en' => 'View Workflows', 'ar' => 'View Workflows']],

            ['name' => 'create-workflows', 'module' => 'workflows', 'label' => ['en' => 'Create Workflows', 'ar' => 'إنشاء سير العمل']],

            ['name' => 'edit-workflows', 'module' => 'workflows', 'label' => ['en' => 'Edit Workflows', 'ar' => 'تعديل سير العمل']],

            ['name' => 'delete-workflows', 'module' => 'workflows', 'label' => ['en' => 'Delete Workflows', 'ar' => 'Delete Workflows']],

            ['name' => 'toggle-status-workflows', 'module' => 'workflows', 'label' => ['en' => 'Toggle Status Workflows', 'ar' => 'تبديل حالة سير العمل']],

            // Task Dependencies

            // Task Comments
            ['name' => 'manage-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage Task Comments', 'ar' => 'إدارة تعليقات المهام']],

            ['name' => 'manage-any-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage All Task Comments', 'ar' => 'إدارة جميع تعليقات المهام']],

            ['name' => 'manage-own-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage Own Task Comments', 'ar' => 'إدارة تعليقات المهام الخاصة']],

            ['name' => 'view-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'View Task Comments', 'ar' => 'عرض تعليقات المهام']],

            ['name' => 'create-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Create Task Comments', 'ar' => 'إنشاء تعليقات المهام']],

            ['name' => 'edit-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Edit Task Comments', 'ar' => 'تعديل تعليقات المهام']],

            ['name' => 'delete-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Delete Task Comments', 'ar' => 'حذف تعليقات المهام']],

            // Communication & Collaboration
            ['name' => 'manage-messages', 'module' => 'messages', 'label' => ['en' => 'Manage Messages', 'ar' => 'إدارة الرسائل']],

            ['name' => 'manage-any-messages', 'module' => 'messages', 'label' => ['en' => 'Manage All Messages', 'ar' => 'Manage All Messages']],

            ['name' => 'manage-own-messages', 'module' => 'messages', 'label' => ['en' => 'Manage Own Messages', 'ar' => 'Manage Own Messages']],

            ['name' => 'view-messages', 'module' => 'messages', 'label' => ['en' => 'View Messages', 'ar' => 'عرض الرسائل']],

            ['name' => 'send-messages', 'module' => 'messages', 'label' => ['en' => 'Send Messages', 'ar' => 'إرسال الرسائل']],

            ['name' => 'delete-messages', 'module' => 'messages', 'label' => ['en' => 'Delete Messages', 'ar' => 'حذف الرسائل']],

            // Calender
            ['name' => 'manage-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage Calendar', 'ar' => 'إدارة التقويم']],

            ['name' => 'manage-any-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage All Calendar', 'ar' => 'إدارة جميع التقويم']],

            ['name' => 'manage-own-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage Own Calendar', 'ar' => 'إدارة التقويم الخاصة']],

            ['name' => 'view-calendar', 'module' => 'calendar', 'label' => ['en' => 'View Calendar', 'ar' => 'عرض التقويم']],

            // Integrations (Google Calendar OAuth on integrations page)
            ['name' => 'view-integrations', 'module' => 'integrations', 'label' => ['en' => 'View Integrations Page', 'ar' => 'عرض صفحة التكاملات']],

            ['name' => 'manage-google-calendar-integration', 'module' => 'integrations', 'label' => ['en' => 'Connect and Disconnect Google Calendar', 'ar' => 'ربط وقطع اتصال تقويم Google']],

        ];

        // Add task permissions to company role permissions
        $taskPermissions = [
            'tasks',
            'task_types',
            'task_statuses',
        ];

        $names = [];
        foreach ($permissions as $permission) {
            $names[] = $permission['name'];
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                [
                    'module' => $permission['module'],
                    'label' => $permission['label'],
                ]
            );
        }

        Permission::whereNotIn('name', $names)->delete();
    }
}
