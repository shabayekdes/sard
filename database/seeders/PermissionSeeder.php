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
            ['name' => 'manage-dashboard', 'module' => 'dashboard', 'label' => ['en' => 'Manage Dashboard', 'ar' => 'إدارة لوحة التحكم'], 'description' => ['en' => 'Can view dashboard', 'ar' => 'يمكنه عرض لوحة التحكم']],

            // User management
            ['name' => 'manage-users', 'module' => 'users', 'label' => ['en' => 'Manage Users', 'ar' => 'إدارة المستخدمين'], 'description' => ['en' => 'Can manage users', 'ar' => 'يمكنه إدارة المستخدمين']],
            ['name' => 'manage-any-users', 'module' => 'users', 'label' => ['en' => 'Manage All Users', 'ar' => 'إدارة جميع المستخدمين'], 'description' => ['en' => 'Manage Any Users', 'ar' => 'إدارة أي مستخدمين']],
            ['name' => 'manage-own-users', 'module' => 'users', 'label' => ['en' => 'Manage Own Users', 'ar' => 'إدارة المستخدمين الخاصين'], 'description' => ['en' => 'Manage Limited Users that is created by own', 'ar' => 'إدارة المستخدمين المحدودين المنشأين بواسطته']],
            ['name' => 'view-users', 'module' => 'users', 'label' => ['en' => 'Manage Users', 'ar' => 'عرض المستخدمين'], 'description' => ['en' => 'View Users', 'ar' => 'عرض المستخدمين']],
            ['name' => 'create-users', 'module' => 'users', 'label' => ['en' => 'Create Users', 'ar' => 'إنشاء المستخدمين'], 'description' => ['en' => 'Can create users', 'ar' => 'يمكنه إنشاء المستخدمين']],
            ['name' => 'edit-users', 'module' => 'users', 'label' => ['en' => 'Edit Users', 'ar' => 'تعديل المستخدمين'], 'description' => ['en' => 'Can edit users', 'ar' => 'يمكنه تعديل المستخدمين']],
            ['name' => 'delete-users', 'module' => 'users', 'label' => ['en' => 'Delete Users', 'ar' => 'حذف المستخدمين'], 'description' => ['en' => 'Can delete users', 'ar' => 'يمكنه حذف المستخدمين']],
            ['name' => 'reset-password-users', 'module' => 'users', 'label' => ['en' => 'Reset Password Users', 'ar' => 'إعادة تعيين كلمة مرور المستخدمين'], 'description' => ['en' => 'Can reset password users', 'ar' => 'يمكنه إعادة تعيين كلمة المرور']],
            ['name' => 'toggle-status-users', 'module' => 'users', 'label' => ['en' => 'Change Status Users', 'ar' => 'تغيير حالة المستخدمين'], 'description' => ['en' => 'Can change status users', 'ar' => 'يمكنه تغيير حالة المستخدمين']],

            // Role management
            ['name' => 'manage-roles', 'module' => 'roles', 'label' => ['en' => 'Manage Roles', 'ar' => 'إدارة الأدوار'], 'description' => ['en' => 'Can manage roles', 'ar' => 'يمكنه إدارة الأدوار']],
            ['name' => 'manage-any-roles', 'module' => 'roles', 'label' => ['en' => 'Manage All Roles', 'ar' => 'إدارة جميع الأدوار'], 'description' => ['en' => 'Manage Any Roles', 'ar' => 'إدارة أي أدوار']],
            ['name' => 'manage-own-roles', 'module' => 'roles', 'label' => ['en' => 'Manage Own Roles', 'ar' => 'إدارة الأدوار الخاصة'], 'description' => ['en' => 'Manage Limited Roles that is created by own', 'ar' => 'إدارة الأدوار المحدودة المنشأة بواسطته']],
            ['name' => 'view-roles', 'module' => 'roles', 'label' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار'], 'description' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار']],
            ['name' => 'create-roles', 'module' => 'roles', 'label' => ['en' => 'Create Roles', 'ar' => 'إنشاء الأدوار'], 'description' => ['en' => 'Can create roles', 'ar' => 'يمكنه إنشاء الأدوار']],
            ['name' => 'edit-roles', 'module' => 'roles', 'label' => ['en' => 'Edit Roles', 'ar' => 'تعديل الأدوار'], 'description' => ['en' => 'Can edit roles', 'ar' => 'يمكنه تعديل الأدوار']],
            ['name' => 'delete-roles', 'module' => 'roles', 'label' => ['en' => 'Delete Roles', 'ar' => 'حذف الأدوار'], 'description' => ['en' => 'Can delete roles', 'ar' => 'يمكنه حذف الأدوار']],

            // Permission management
            ['name' => 'manage-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage Permissions', 'ar' => 'إدارة الصلاحيات'], 'description' => ['en' => 'Can manage permissions', 'ar' => 'يمكنه إدارة الصلاحيات']],
            ['name' => 'manage-any-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage All Permissions', 'ar' => 'إدارة جميع الصلاحيات'], 'description' => ['en' => 'Manage Any Permissions', 'ar' => 'إدارة أي صلاحيات']],
            ['name' => 'manage-own-permissions', 'module' => 'permissions', 'label' => ['en' => 'Manage Own Permissions', 'ar' => 'إدارة الصلاحيات الخاصة'], 'description' => ['en' => 'Manage Limited Permissions that is created by own', 'ar' => 'إدارة الصلاحيات المحدودة المنشأة بواسطته']],
            ['name' => 'view-permissions', 'module' => 'permissions', 'label' => ['en' => 'View Permissions', 'ar' => 'عرض الصلاحيات'], 'description' => ['en' => 'View Permissions', 'ar' => 'عرض الصلاحيات']],
            ['name' => 'create-permissions', 'module' => 'permissions', 'label' => ['en' => 'Create Permissions', 'ar' => 'إنشاء الصلاحيات'], 'description' => ['en' => 'Can create permissions', 'ar' => 'يمكنه إنشاء الصلاحيات']],
            ['name' => 'edit-permissions', 'module' => 'permissions', 'label' => ['en' => 'Edit Permissions', 'ar' => 'تعديل الصلاحيات'], 'description' => ['en' => 'Can edit permissions', 'ar' => 'يمكنه تعديل الصلاحيات']],
            ['name' => 'delete-permissions', 'module' => 'permissions', 'label' => ['en' => 'Delete Permissions', 'ar' => 'حذف الصلاحيات'], 'description' => ['en' => 'Can delete permissions', 'ar' => 'يمكنه حذف الصلاحيات']],

            // Company management
            ['name' => 'manage-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Companies', 'ar' => 'إدارة الشركات'], 'description' => ['en' => 'Can manage Companies', 'ar' => 'يمكنه إدارة الشركات']],
            ['name' => 'manage-any-companies', 'module' => 'companies', 'label' => ['en' => 'Manage All Companies', 'ar' => 'إدارة جميع الشركات'], 'description' => ['en' => 'Manage Any Companies', 'ar' => 'إدارة أي شركات']],
            ['name' => 'manage-own-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Own Companies', 'ar' => 'إدارة الشركات الخاصة'], 'description' => ['en' => 'Manage Limited Companies that is created by own', 'ar' => 'إدارة الشركات المحدودة المنشأة بواسطته']],
            ['name' => 'view-companies', 'module' => 'companies', 'label' => ['en' => 'View Companies', 'ar' => 'عرض الشركات'], 'description' => ['en' => 'View Companies', 'ar' => 'عرض الشركات']],
            ['name' => 'create-companies', 'module' => 'companies', 'label' => ['en' => 'Create Companies', 'ar' => 'إنشاء الشركات'], 'description' => ['en' => 'Can create Companies', 'ar' => 'يمكنه إنشاء الشركات']],
            ['name' => 'edit-companies', 'module' => 'companies', 'label' => ['en' => 'Edit Companies', 'ar' => 'تعديل الشركات'], 'description' => ['en' => 'Can edit Companies', 'ar' => 'يمكنه تعديل الشركات']],
            ['name' => 'delete-companies', 'module' => 'companies', 'label' => ['en' => 'Delete Companies', 'ar' => 'حذف الشركات'], 'description' => ['en' => 'Can delete Companies', 'ar' => 'يمكنه حذف الشركات']],
            ['name' => 'reset-password-companies', 'module' => 'companies', 'label' => ['en' => 'Reset Password Companies', 'ar' => 'إعادة تعيين كلمة مرور الشركات'], 'description' => ['en' => 'Can reset password Companies', 'ar' => 'يمكنه إعادة تعيين كلمة مرور الشركات']],
            ['name' => 'toggle-status-companies', 'module' => 'companies', 'label' => ['en' => 'Change Status Companies', 'ar' => 'تغيير حالة الشركات'], 'description' => ['en' => 'Can change status companies', 'ar' => 'يمكنه تغيير حالة الشركات']],
            ['name' => 'manage-plans-companies', 'module' => 'companies', 'label' => ['en' => 'Manage Plan Companies', 'ar' => 'إدارة خطط الشركات'], 'description' => ['en' => 'Can manage plans companies', 'ar' => 'يمكنه إدارة خطط الشركات']],
            ['name' => 'upgrade-plan-companies', 'module' => 'companies', 'label' => ['en' => 'Upgrade Plan Companies', 'ar' => 'ترقية خطة الشركات'], 'description' => ['en' => 'Can upgrade plan of companies', 'ar' => 'يمكنه ترقية خطة الشركات']],

            // Plan management
            ['name' => 'manage-plans', 'module' => 'plans', 'label' => ['en' => 'Manage Plans', 'ar' => 'إدارة الخطط'], 'description' => ['en' => 'Can manage subscription plans', 'ar' => 'يمكنه إدارة خطط الاشتراك']],
            ['name' => 'manage-any-plans', 'module' => 'plans', 'label' => ['en' => 'Manage All Plans', 'ar' => 'إدارة جميع الخطط'], 'description' => ['en' => 'Manage Any Plans', 'ar' => 'إدارة أي خطط']],
            ['name' => 'manage-own-plans', 'module' => 'plans', 'label' => ['en' => 'Manage Own Plans', 'ar' => 'إدارة الخطط الخاصة'], 'description' => ['en' => 'Manage Limited Plans that is created by own', 'ar' => 'إدارة الخطط المحدودة المنشأة بواسطته']],
            ['name' => 'view-plans', 'module' => 'plans', 'label' => ['en' => 'View Plans', 'ar' => 'عرض الخطط'], 'description' => ['en' => 'View Plans', 'ar' => 'عرض الخطط']],
            ['name' => 'create-plans', 'module' => 'plans', 'label' => ['en' => 'Create Plans', 'ar' => 'إنشاء الخطط'], 'description' => ['en' => 'Can create subscription plans', 'ar' => 'يمكنه إنشاء خطط الاشتراك']],
            ['name' => 'edit-plans', 'module' => 'plans', 'label' => ['en' => 'Edit Plans', 'ar' => 'تعديل الخطط'], 'description' => ['en' => 'Can edit subscription plans', 'ar' => 'يمكنه تعديل الخطط']],
            ['name' => 'delete-plans', 'module' => 'plans', 'label' => ['en' => 'Delete Plans', 'ar' => 'حذف الخطط'], 'description' => ['en' => 'Can delete subscription plans', 'ar' => 'يمكنه حذف الخطط']],
            ['name' => 'request-plans', 'module' => 'plans', 'label' => ['en' => 'Request Plans', 'ar' => 'طلب الخطط'], 'description' => ['en' => 'Can request subscription plans', 'ar' => 'يمكنه طلب خطط الاشتراك']],
            ['name' => 'trial-plans', 'module' => 'plans', 'label' => ['en' => 'Trial Plans', 'ar' => 'خطط التجربة'], 'description' => ['en' => 'Can start trial for subscription plans', 'ar' => 'يمكنه بدء تجربة الخطط']],
            ['name' => 'subscribe-plans', 'module' => 'plans', 'label' => ['en' => 'Subscribe Plans', 'ar' => 'الاشتراك في الخطط'], 'description' => ['en' => 'Can subscribe to subscription plans', 'ar' => 'يمكنه الاشتراك في الخطط']],

            // Coupon management
            ['name' => 'manage-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage Coupons', 'ar' => 'إدارة القسائم'], 'description' => ['en' => 'Can manage subscription Coupons', 'ar' => 'يمكنه إدارة قسائم الاشتراك']],
            ['name' => 'manage-any-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage All Coupons', 'ar' => 'إدارة جميع القسائم'], 'description' => ['en' => 'Manage Any Coupons', 'ar' => 'إدارة أي قسائم']],
            ['name' => 'manage-own-coupons', 'module' => 'coupons', 'label' => ['en' => 'Manage Own Coupons', 'ar' => 'إدارة القسائم الخاصة'], 'description' => ['en' => 'Manage Limited Coupons that is created by own', 'ar' => 'إدارة القسائم المحدودة المنشأة بواسطته']],
            ['name' => 'view-coupons', 'module' => 'coupons', 'label' => ['en' => 'View Coupons', 'ar' => 'عرض القسائم'], 'description' => ['en' => 'View Coupons', 'ar' => 'عرض القسائم']],
            ['name' => 'create-coupons', 'module' => 'coupons', 'label' => ['en' => 'Create Coupons', 'ar' => 'إنشاء القسائم'], 'description' => ['en' => 'Can create subscription Coupons', 'ar' => 'يمكنه إنشاء قسائم الاشتراك']],
            ['name' => 'edit-coupons', 'module' => 'coupons', 'label' => ['en' => 'Edit Coupons', 'ar' => 'تعديل القسائم'], 'description' => ['en' => 'Can edit subscription Coupons', 'ar' => 'يمكنه تعديل القسائم']],
            ['name' => 'delete-coupons', 'module' => 'coupons', 'label' => ['en' => 'Delete Coupons', 'ar' => 'حذف القسائم'], 'description' => ['en' => 'Can delete subscription Coupons', 'ar' => 'يمكنه حذف القسائم']],
            ['name' => 'toggle-status-coupons', 'module' => 'coupons', 'label' => ['en' => 'Change Status Coupons', 'ar' => 'تغيير حالة القسائم'], 'description' => ['en' => 'Can change status Coupons', 'ar' => 'يمكنه تغيير حالة القسائم']],

            // Plan Requests management
            ['name' => 'manage-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage Plan Requests', 'ar' => 'إدارة طلبات الخطط'], 'description' => ['en' => 'Can manage plan requests', 'ar' => 'يمكنه إدارة طلبات الخطط']],
            ['name' => 'manage-any-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage All Plan Requests', 'ar' => 'إدارة جميع طلبات الخطط'], 'description' => ['en' => 'Manage Any Plan Requests', 'ar' => 'إدارة أي طلبات خطط']],
            ['name' => 'manage-own-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Manage Own Plan Requests', 'ar' => 'إدارة طلبات الخطط الخاصة'], 'description' => ['en' => 'Manage Limited Plan Requests that is created by own', 'ar' => 'إدارة طلبات الخطط المحدودة المنشأة بواسطته']],
            ['name' => 'view-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'View Plan Requests', 'ar' => 'عرض طلبات الخطط'], 'description' => ['en' => 'View Plan Requests', 'ar' => 'عرض طلبات الخطط']],
            ['name' => 'create-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Create Plan Requests', 'ar' => 'إنشاء طلبات الخطط'], 'description' => ['en' => 'Can create plan requests', 'ar' => 'يمكنه إنشاء طلبات الخطط']],
            ['name' => 'edit-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Edit Plan Requests', 'ar' => 'تعديل طلبات الخطط'], 'description' => ['en' => 'Can edit plan requests', 'ar' => 'يمكنه تعديل طلبات الخطط']],
            ['name' => 'delete-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Delete Plan Requests', 'ar' => 'حذف طلبات الخطط'], 'description' => ['en' => 'Can delete plan requests', 'ar' => 'يمكنه حذف طلبات الخطط']],
            ['name' => 'approve-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Approve plan requests', 'ar' => 'الموافقة على طلبات الخطط'], 'description' => ['en' => 'Can approve plan requests', 'ar' => 'يمكنه الموافقة على طلبات الخطط']],
            ['name' => 'reject-plan-requests', 'module' => 'plan_requests', 'label' => ['en' => 'Reject plan requests', 'ar' => 'رفض طلبات الخطط'], 'description' => ['en' => 'Can reject plplan requests', 'ar' => 'يمكنه رفض طلبات الخطط']],

            // Plan Orders management
            ['name' => 'manage-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage Plan Orders', 'ar' => 'إدارة طلبات الاشتراك'], 'description' => ['en' => 'Can manage plan orders', 'ar' => 'يمكنه إدارة طلبات الاشتراك']],
            ['name' => 'manage-any-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage All Plan Orders', 'ar' => 'إدارة جميع طلبات الاشتراك'], 'description' => ['en' => 'Manage Any Plan Orders', 'ar' => 'إدارة أي طلبات اشتراك']],
            ['name' => 'manage-own-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Manage Own Plan Orders', 'ar' => 'إدارة طلبات الاشتراك الخاصة'], 'description' => ['en' => 'Manage Limited Plan Orders that is created by own', 'ar' => 'إدارة طلبات الاشتراك المحدودة المنشأة بواسطته']],
            ['name' => 'view-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'View Plan Orders', 'ar' => 'عرض طلبات الاشتراك'], 'description' => ['en' => 'View Plan Orders', 'ar' => 'عرض طلبات الاشتراك']],
            ['name' => 'create-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Create Plan Orders', 'ar' => 'إنشاء طلبات الاشتراك'], 'description' => ['en' => 'Can create plan orders', 'ar' => 'يمكنه إنشاء طلبات الاشتراك']],
            ['name' => 'edit-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Edit Plan Orders', 'ar' => 'تعديل طلبات الاشتراك'], 'description' => ['en' => 'Can edit plan orders', 'ar' => 'يمكنه تعديل طلبات الاشتراك']],
            ['name' => 'delete-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Delete Plan Orders', 'ar' => 'حذف طلبات الاشتراك'], 'description' => ['en' => 'Can delete plan orders', 'ar' => 'يمكنه حذف طلبات الاشتراك']],
            ['name' => 'approve-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Approve Plan Orders', 'ar' => 'الموافقة على طلبات الاشتراك'], 'description' => ['en' => 'Can approve plan orders', 'ar' => 'يمكنه الموافقة على طلبات الاشتراك']],
            ['name' => 'reject-plan-orders', 'module' => 'plan_orders', 'label' => ['en' => 'Reject Plan Orders', 'ar' => 'رفض طلبات الاشتراك'], 'description' => ['en' => 'Can reject plan orders', 'ar' => 'يمكنه رفض طلبات الاشتراك']],

            // Settings
            ['name' => 'manage-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Settings', 'ar' => 'إدارة الإعدادات'], 'description' => ['en' => 'Can manage All settings', 'ar' => 'يمكنه إدارة جميع الإعدادات']],
            ['name' => 'manage-system-settings', 'module' => 'settings', 'label' => ['en' => 'Manage System Settings', 'ar' => 'إدارة إعدادات النظام'], 'description' => ['en' => 'Can manage system settings', 'ar' => 'يمكنه إدارة إعدادات النظام']],
            ['name' => 'manage-email-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Email Settings', 'ar' => 'إدارة إعدادات البريد'], 'description' => ['en' => 'Can manage email settings', 'ar' => 'يمكنه إدارة إعدادات البريد']],
            ['name' => 'manage-brand-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Brand Settings', 'ar' => 'إدارة إعدادات العلامة التجارية'], 'description' => ['en' => 'Can manage brand settings', 'ar' => 'يمكنه إدارة إعدادات العلامة التجارية']],
            ['name' => 'manage-company-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Company Settings', 'ar' => 'إدارة إعدادات الشركة'], 'description' => ['en' => 'Can manage Company settings', 'ar' => 'يمكنه إدارة إعدادات الشركة']],
            ['name' => 'manage-storage-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Storage Settings', 'ar' => 'إدارة إعدادات التخزين'], 'description' => ['en' => 'Can manage storage settings', 'ar' => 'يمكنه إدارة إعدادات التخزين']],
            ['name' => 'manage-payment-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Payment Settings', 'ar' => 'إدارة إعدادات الدفع'], 'description' => ['en' => 'Can manage payment settings', 'ar' => 'يمكنه إدارة إعدادات الدفع']],
            ['name' => 'manage-currency-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Currency Settings', 'ar' => 'إدارة إعدادات العملة'], 'description' => ['en' => 'Can manage currency settings', 'ar' => 'يمكنه إدارة إعدادات العملة']],
            ['name' => 'manage-recaptcha-settings', 'module' => 'settings', 'label' => ['en' => 'Manage ReCaptch Settings', 'ar' => 'إدارة إعدادات reCAPTCHA'], 'description' => ['en' => 'Can manage recaptcha settings', 'ar' => 'يمكنه إدارة إعدادات reCAPTCHA']],
            ['name' => 'manage-chatgpt-settings', 'module' => 'settings', 'label' => ['en' => 'Manage ChatGpt Settings', 'ar' => 'إدارة إعدادات ChatGPT'], 'description' => ['en' => 'Can manage chatgpt settings', 'ar' => 'يمكنه إدارة إعدادات ChatGPT']],
            ['name' => 'manage-cookie-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Cookie(GDPR) Settings', 'ar' => 'إدارة إعدادات ملفات التعريف (GDPR)'], 'description' => ['en' => 'Can manage cookie settings', 'ar' => 'يمكنه إدارة إعدادات ملفات التعريف']],
            ['name' => 'manage-seo-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Seo Settings', 'ar' => 'إدارة إعدادات SEO'], 'description' => ['en' => 'Can manage seo settings', 'ar' => 'يمكنه إدارة إعدادات SEO']],
            ['name' => 'manage-cache-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Cache Settings', 'ar' => 'إدارة إعدادات الذاكرة المؤقتة'], 'description' => ['en' => 'Can manage cache settings', 'ar' => 'يمكنه إدارة إعدادات الذاكرة المؤقتة']],
            ['name' => 'manage-account-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Account Settings', 'ar' => 'إدارة إعدادات الحساب'], 'description' => ['en' => 'Can manage account settings', 'ar' => 'يمكنه إدارة إعدادات الحساب']],

            // Setup (configuration) permissions
            ['name' => 'view-setup', 'module' => 'setup', 'label' => ['en' => 'View Setup', 'ar' => 'عرض الإعداد'], 'description' => ['en' => 'Can view setup and configuration pages', 'ar' => 'يمكنه عرض صفحات الإعداد والتكوين']],

            // Contact Us management
            ['name' => 'manage-contact-us', 'module' => 'contact-us', 'label' => ['en' => 'Manage Contact Us', 'ar' => 'إدارة اتصل بنا'], 'description' => ['en' => 'Can manage contact us messages', 'ar' => 'يمكنه إدارة رسائل اتصل بنا']],
            ['name' => 'view-contact-us', 'module' => 'contact-us', 'label' => ['en' => 'View Contact Us', 'ar' => 'عرض اتصل بنا'], 'description' => ['en' => 'View Contact Us messages', 'ar' => 'عرض رسائل اتصل بنا']],

            // Currency management
            ['name' => 'manage-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage Currencies', 'ar' => 'إدارة العملات'], 'description' => ['en' => 'Can manage currencies', 'ar' => 'يمكنه إدارة العملات']],
            ['name' => 'manage-any-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage All currencies', 'ar' => 'إدارة جميع العملات'], 'description' => ['en' => 'Manage Any currencies', 'ar' => 'إدارة أي عملات']],
            ['name' => 'manage-own-currencies', 'module' => 'currencies', 'label' => ['en' => 'Manage Own currencies', 'ar' => 'إدارة العملات الخاصة'], 'description' => ['en' => 'Manage Limited currencies that is created by own', 'ar' => 'إدارة العملات المحدودة المنشأة بواسطته']],
            ['name' => 'view-currencies', 'module' => 'currencies', 'label' => ['en' => 'View Currencies', 'ar' => 'عرض العملات'], 'description' => ['en' => 'View Currencies', 'ar' => 'عرض العملات']],
            ['name' => 'create-currencies', 'module' => 'currencies', 'label' => ['en' => 'Create Currencies', 'ar' => 'إنشاء العملات'], 'description' => ['en' => 'Can create currencies', 'ar' => 'يمكنه إنشاء العملات']],
            ['name' => 'edit-currencies', 'module' => 'currencies', 'label' => ['en' => 'Edit Currencies', 'ar' => 'تعديل العملات'], 'description' => ['en' => 'Can edit currencies', 'ar' => 'يمكنه تعديل العملات']],
            ['name' => 'delete-currencies', 'module' => 'currencies', 'label' => ['en' => 'Delete Currencies', 'ar' => 'حذف العملات'], 'description' => ['en' => 'Can delete currencies', 'ar' => 'يمكنه حذف العملات']],

            // Tax Rate management
            ['name' => 'manage-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage Tax Rates', 'ar' => 'إدارة معدلات الضريبة'], 'description' => ['en' => 'Can manage tax rates', 'ar' => 'يمكنه إدارة معدلات الضريبة']],
            ['name' => 'manage-any-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage All tax rates', 'ar' => 'إدارة جميع معدلات الضريبة'], 'description' => ['en' => 'Manage Any tax rates', 'ar' => 'إدارة أي معدلات ضريبة']],
            ['name' => 'manage-own-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Manage Own tax rates', 'ar' => 'إدارة معدلات الضريبة الخاصة'], 'description' => ['en' => 'Manage Limited tax rates that is created by own', 'ar' => 'إدارة معدلات الضريبة المحدودة المنشأة بواسطته']],
            ['name' => 'view-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'View Tax Rates', 'ar' => 'عرض معدلات الضريبة'], 'description' => ['en' => 'View tax rates', 'ar' => 'عرض معدلات الضريبة']],
            ['name' => 'create-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Create Tax Rates', 'ar' => 'إنشاء معدلات الضريبة'], 'description' => ['en' => 'Can create tax rates', 'ar' => 'يمكنه إنشاء معدلات الضريبة']],
            ['name' => 'edit-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Edit Tax Rates', 'ar' => 'تعديل معدلات الضريبة'], 'description' => ['en' => 'Can edit tax rates', 'ar' => 'يمكنه تعديل معدلات الضريبة']],
            ['name' => 'delete-tax-rates', 'module' => 'tax_rates', 'label' => ['en' => 'Delete Tax Rates', 'ar' => 'حذف معدلات الضريبة'], 'description' => ['en' => 'Can delete tax rates', 'ar' => 'يمكنه حذف معدلات الضريبة']],

            // Country management
            ['name' => 'manage-countries', 'module' => 'countries', 'label' => ['en' => 'Manage Countries', 'ar' => 'إدارة الدول'], 'description' => ['en' => 'Can manage countries', 'ar' => 'يمكنه إدارة الدول']],
            ['name' => 'manage-any-countries', 'module' => 'countries', 'label' => ['en' => 'Manage All Countries', 'ar' => 'إدارة جميع الدول'], 'description' => ['en' => 'Manage Any Countries', 'ar' => 'إدارة أي دول']],
            ['name' => 'manage-own-countries', 'module' => 'countries', 'label' => ['en' => 'Manage Own Countries', 'ar' => 'إدارة الدول الخاصة'], 'description' => ['en' => 'Manage Limited Countries that is created by own', 'ar' => 'إدارة الدول المحدودة المنشأة بواسطته']],
            ['name' => 'view-countries', 'module' => 'countries', 'label' => ['en' => 'View Countries', 'ar' => 'عرض الدول'], 'description' => ['en' => 'View Countries', 'ar' => 'عرض الدول']],
            ['name' => 'create-countries', 'module' => 'countries', 'label' => ['en' => 'Create Countries', 'ar' => 'إنشاء الدول'], 'description' => ['en' => 'Can create countries', 'ar' => 'يمكنه إنشاء الدول']],
            ['name' => 'edit-countries', 'module' => 'countries', 'label' => ['en' => 'Edit Countries', 'ar' => 'تعديل الدول'], 'description' => ['en' => 'Can edit countries', 'ar' => 'يمكنه تعديل الدول']],
            ['name' => 'delete-countries', 'module' => 'countries', 'label' => ['en' => 'Delete Countries', 'ar' => 'حذف الدول'], 'description' => ['en' => 'Can delete countries', 'ar' => 'يمكنه حذف الدول']],

            // Referral management
            ['name' => 'manage-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'إدارة الإحالة'], 'description' => ['en' => 'Can manage referral program', 'ar' => 'يمكنه إدارة برنامج الإحالة']],
            ['name' => 'manage-any-referral', 'module' => 'referral', 'label' => ['en' => 'Manage All Referral', 'ar' => 'إدارة جميع الإحالات'], 'description' => ['en' => 'Manage Any Referral', 'ar' => 'إدارة أي إحالات']],
            ['name' => 'manage-own-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Own Referral', 'ar' => 'إدارة الإحالات الخاصة'], 'description' => ['en' => 'Manage Limited Referral that is created by own', 'ar' => 'إدارة الإحالات المحدودة المنشأة بواسطته']],
            ['name' => 'manage-users-referral', 'module' => 'referral', 'label' => ['en' => 'Manage User Referral', 'ar' => 'إدارة إحالات المستخدمين'], 'description' => ['en' => 'Can manage user referral program', 'ar' => 'يمكنه إدارة برنامج إحالة المستخدمين']],
            ['name' => 'manage-setting-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral Setting', 'ar' => 'إدارة إعدادات الإحالة'], 'description' => ['en' => 'Can manage Referral Setting', 'ar' => 'يمكنه إدارة إعدادات الإحالة']],
            ['name' => 'manage-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral Payout', 'ar' => 'إدارة مدفوعات الإحالة'], 'description' => ['en' => 'Can manage Referral Payout program', 'ar' => 'يمكنه إدارة مدفوعات برنامج الإحالة']],
            ['name' => 'approve-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'الموافقة على مدفوعات الإحالة'], 'description' => ['en' => 'Can approve payout request', 'ar' => 'يمكنه الموافقة على طلبات المدفوعات']],
            ['name' => 'reject-payout-referral', 'module' => 'referral', 'label' => ['en' => 'Manage Referral', 'ar' => 'رفض مدفوعات الإحالة'], 'description' => ['en' => 'Can approve payout request', 'ar' => 'يمكنه رفض طلبات المدفوعات']],

            // Language management
            ['name' => 'manage-language', 'module' => 'language', 'label' => ['en' => 'Manage Language', 'ar' => 'إدارة اللغة'], 'description' => ['en' => 'Can manage language', 'ar' => 'يمكنه إدارة اللغة']],
            ['name' => 'manage-any-language', 'module' => 'language', 'label' => ['en' => 'Manage All Language', 'ar' => 'إدارة جميع اللغات'], 'description' => ['en' => 'Manage Any Language', 'ar' => 'إدارة أي لغات']],
            ['name' => 'manage-own-language', 'module' => 'language', 'label' => ['en' => 'Manage Own Language', 'ar' => 'إدارة اللغات الخاصة'], 'description' => ['en' => 'Manage Limited Language that is created by own', 'ar' => 'إدارة اللغات المحدودة المنشأة بواسطته']],
            ['name' => 'edit-language', 'module' => 'language', 'label' => ['en' => 'Edit Language', 'ar' => 'تعديل اللغة'], 'description' => ['en' => 'Edit Language', 'ar' => 'تعديل اللغة']],
            ['name' => 'view-language', 'module' => 'language', 'label' => ['en' => 'View Language', 'ar' => 'عرض اللغة'], 'description' => ['en' => 'View Language', 'ar' => 'عرض اللغة']],

            // Media management
            ['name' => 'manage-media', 'module' => 'media', 'label' => ['en' => 'Manage Media', 'ar' => 'إدارة الوسائط'], 'description' => ['en' => 'Can manage media', 'ar' => 'يمكنه إدارة الوسائط']],
            ['name' => 'manage-any-media', 'module' => 'media', 'label' => ['en' => 'Manage All Media', 'ar' => 'إدارة جميع الوسائط'], 'description' => ['en' => 'Manage Any media', 'ar' => 'إدارة أي وسائط']],
            ['name' => 'manage-own-media', 'module' => 'media', 'label' => ['en' => 'Manage Own Media', 'ar' => 'إدارة الوسائط الخاصة'], 'description' => ['en' => 'Manage Limited media that is created by own', 'ar' => 'إدارة الوسائط المحدودة المنشأة بواسطته']],
            ['name' => 'create-media', 'module' => 'media', 'label' => ['en' => 'Create media', 'ar' => 'إنشاء الوسائط'], 'description' => ['en' => 'Create media', 'ar' => 'إنشاء الوسائط']],
            ['name' => 'edit-media', 'module' => 'media', 'label' => ['en' => 'Edit media', 'ar' => 'تعديل الوسائط'], 'description' => ['en' => 'Edit media', 'ar' => 'تعديل الوسائط']],
            ['name' => 'delete-media', 'module' => 'media', 'label' => ['en' => 'Delete media', 'ar' => 'حذف الوسائط'], 'description' => ['en' => 'Delete media', 'ar' => 'حذف الوسائط']],
            ['name' => 'view-media', 'module' => 'media', 'label' => ['en' => 'View media', 'ar' => 'عرض الوسائط'], 'description' => ['en' => 'View media', 'ar' => 'عرض الوسائط']],
            ['name' => 'download-media', 'module' => 'media', 'label' => ['en' => 'Download media', 'ar' => 'تحميل الوسائط'], 'description' => ['en' => 'Download media', 'ar' => 'تحميل الوسائط']],

            // Webhook management
            ['name' => 'manage-webhook-settings', 'module' => 'settings', 'label' => ['en' => 'Manage Webhook Settings', 'ar' => 'إدارة إعدادات الويب هوك'], 'description' => ['en' => 'Can manage webhook settings', 'ar' => 'يمكنه إدارة إعدادات الويب هوك']],
            // Landing Page management
            ['name' => 'manage-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage Landing Page', 'ar' => 'إدارة الصفحة المقصودة'], 'description' => ['en' => 'Can manage landing page', 'ar' => 'يمكنه إدارة الصفحة المقصودة']],
            ['name' => 'manage-any-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage All Landing Page', 'ar' => 'إدارة جميع الصفحات المقصودة'], 'description' => ['en' => 'Manage Any Landing Page', 'ar' => 'إدارة أي صفحات مقصودة']],
            ['name' => 'manage-own-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Manage Own Landing Page', 'ar' => 'إدارة الصفحات المقصودة الخاصة'], 'description' => ['en' => 'Manage Limited Landing Page that is created by own', 'ar' => 'إدارة الصفحات المقصودة المحدودة المنشأة بواسطته']],
            ['name' => 'view-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'View Landing Page', 'ar' => 'عرض الصفحة المقصودة'], 'description' => ['en' => 'View landing page', 'ar' => 'عرض الصفحة المقصودة']],
            ['name' => 'edit-landing-page', 'module' => 'landing_page', 'label' => ['en' => 'Edit Landing Page', 'ar' => 'تعديل الصفحة المقصودة'], 'description' => ['en' => 'Edit landing page', 'ar' => 'تعديل الصفحة المقصودة']],

            // Client Type management
            ['name' => 'manage-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage Client Types', 'ar' => 'إدارة أنواع العملاء'], 'description' => ['en' => 'Can manage client types', 'ar' => 'يمكنه إدارة أنواع العملاء']],
            ['name' => 'manage-any-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage All Client Types', 'ar' => 'إدارة جميع أنواع العملاء'], 'description' => ['en' => 'Manage Any Client Types', 'ar' => 'إدارة أي أنواع عملاء']],
            ['name' => 'manage-own-client-types', 'module' => 'client_types', 'label' => ['en' => 'Manage Own Client Types', 'ar' => 'إدارة أنواع العملاء الخاصة'], 'description' => ['en' => 'Manage Limited Client Types that is created by own', 'ar' => 'إدارة أنواع العملاء المحدودة المنشأة بواسطته']],
            ['name' => 'view-client-types', 'module' => 'client_types', 'label' => ['en' => 'View Client Types', 'ar' => 'عرض أنواع العملاء'], 'description' => ['en' => 'View Client Types', 'ar' => 'عرض أنواع العملاء']],
            ['name' => 'create-client-types', 'module' => 'client_types', 'label' => ['en' => 'Create Client Types', 'ar' => 'إنشاء أنواع العملاء'], 'description' => ['en' => 'Can create client types', 'ar' => 'يمكنه إنشاء أنواع العملاء']],
            ['name' => 'edit-client-types', 'module' => 'client_types', 'label' => ['en' => 'Edit Client Types', 'ar' => 'تعديل أنواع العملاء'], 'description' => ['en' => 'Can edit client types', 'ar' => 'يمكنه تعديل أنواع العملاء']],
            ['name' => 'delete-client-types', 'module' => 'client_types', 'label' => ['en' => 'Delete Client Types', 'ar' => 'حذف أنواع العملاء'], 'description' => ['en' => 'Can delete client types', 'ar' => 'يمكنه حذف أنواع العملاء']],
            ['name' => 'toggle-status-client-types', 'module' => 'client_types', 'label' => ['en' => 'Toggle Status Client Types', 'ar' => 'تبديل حالة أنواع العملاء'], 'description' => ['en' => 'Can toggle status of client types', 'ar' => 'يمكنه تبديل حالة أنواع العملاء']],

            // Client management
            ['name' => 'manage-clients', 'module' => 'clients', 'label' => ['en' => 'Manage Clients', 'ar' => 'إدارة العملاء'], 'description' => ['en' => 'Can manage clients', 'ar' => 'يمكنه إدارة العملاء']],
            ['name' => 'manage-any-clients', 'module' => 'clients', 'label' => ['en' => 'Manage All Clients', 'ar' => 'إدارة جميع العملاء'], 'description' => ['en' => 'Manage Any Clients', 'ar' => 'إدارة أي عملاء']],
            ['name' => 'manage-own-clients', 'module' => 'clients', 'label' => ['en' => 'Manage Own Clients', 'ar' => 'إدارة العملاء الخاصين'], 'description' => ['en' => 'Manage Limited Clients that is created by own', 'ar' => 'إدارة العملاء المحدودين المنشأين بواسطته']],
            ['name' => 'view-clients', 'module' => 'clients', 'label' => ['en' => 'View Clients', 'ar' => 'عرض العملاء'], 'description' => ['en' => 'View Clients', 'ar' => 'عرض العملاء']],
            ['name' => 'create-clients', 'module' => 'clients', 'label' => ['en' => 'Create Clients', 'ar' => 'إنشاء العملاء'], 'description' => ['en' => 'Can create clients', 'ar' => 'يمكنه إنشاء العملاء']],
            ['name' => 'edit-clients', 'module' => 'clients', 'label' => ['en' => 'Edit Clients', 'ar' => 'تعديل العملاء'], 'description' => ['en' => 'Can edit clients', 'ar' => 'يمكنه تعديل العملاء']],
            ['name' => 'delete-clients', 'module' => 'clients', 'label' => ['en' => 'Delete Clients', 'ar' => 'حذف العملاء'], 'description' => ['en' => 'Can delete clients', 'ar' => 'يمكنه حذف العملاء']],
            ['name' => 'toggle-status-clients', 'module' => 'clients', 'label' => ['en' => 'Toggle Status Clients', 'ar' => 'تبديل حالة العملاء'], 'description' => ['en' => 'Can toggle status of clients', 'ar' => 'يمكنه تبديل حالة العملاء']],
            ['name' => 'reset-client-password', 'module' => 'clients', 'label' => ['en' => 'Reset Client Password', 'ar' => 'إعادة تعيين كلمة مرور العميل'], 'description' => ['en' => 'Can reset client passwords', 'ar' => 'يمكنه إعادة تعيين كلمات مرور العملاء']],

            // Client Communication management

            // Client Document management
            ['name' => 'manage-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage Client Documents', 'ar' => 'Manage Client Documents'], 'description' => ['en' => 'Can manage client documents', 'ar' => 'Can manage client documents']],

            ['name' => 'manage-any-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage All Client Documents', 'ar' => 'Manage All Client Documents'], 'description' => ['en' => 'Manage Any Client Documents', 'ar' => 'Manage Any Client Documents']],

            ['name' => 'manage-own-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Manage Own Client Documents', 'ar' => 'Manage Own Client Documents'], 'description' => ['en' => 'Manage Limited Client Documents that is created by own', 'ar' => 'Manage Limited Client Documents that is created by own']],

            ['name' => 'view-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'View Client Documents', 'ar' => 'View Client Documents'], 'description' => ['en' => 'View Client Documents', 'ar' => 'View Client Documents']],

            ['name' => 'create-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Create Client Documents', 'ar' => 'Create Client Documents'], 'description' => ['en' => 'Can create client documents', 'ar' => 'Can create client documents']],

            ['name' => 'edit-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Edit Client Documents', 'ar' => 'Edit Client Documents'], 'description' => ['en' => 'Can edit client documents', 'ar' => 'Can edit client documents']],

            ['name' => 'delete-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Delete Client Documents', 'ar' => 'Delete Client Documents'], 'description' => ['en' => 'Can delete client documents', 'ar' => 'Can delete client documents']],

            ['name' => 'download-client-documents', 'module' => 'client_documents', 'label' => ['en' => 'Download Client Documents', 'ar' => 'Download Client Documents'], 'description' => ['en' => 'Can download client documents', 'ar' => 'Can download client documents']],

            // Client Billing Info management
            ['name' => 'manage-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage Client Billing', 'ar' => 'Manage Client Billing'], 'description' => ['en' => 'Can manage client billing information', 'ar' => 'Can manage client billing information']],

            ['name' => 'manage-any-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage All Client Billing', 'ar' => 'Manage All Client Billing'], 'description' => ['en' => 'Manage Any Client Billing', 'ar' => 'Manage Any Client Billing']],

            ['name' => 'manage-own-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Manage Own Client Billing', 'ar' => 'Manage Own Client Billing'], 'description' => ['en' => 'Manage Limited Client Billing that is created by own', 'ar' => 'Manage Limited Client Billing that is created by own']],

            ['name' => 'view-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'View Client Billing', 'ar' => 'View Client Billing'], 'description' => ['en' => 'View Client Billing', 'ar' => 'View Client Billing']],

            ['name' => 'create-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Create Client Billing', 'ar' => 'Create Client Billing'], 'description' => ['en' => 'Can create client billing information', 'ar' => 'Can create client billing information']],

            ['name' => 'edit-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Edit Client Billing', 'ar' => 'Edit Client Billing'], 'description' => ['en' => 'Can edit client billing information', 'ar' => 'Can edit client billing information']],

            ['name' => 'delete-client-billing', 'module' => 'client_billing', 'label' => ['en' => 'Delete Client Billing', 'ar' => 'Delete Client Billing'], 'description' => ['en' => 'Can delete client billing information', 'ar' => 'Can delete client billing information']],

            // Company Profile management
            ['name' => 'manage-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage Company Profiles', 'ar' => 'Manage Company Profiles'], 'description' => ['en' => 'Can manage company profiles', 'ar' => 'Can manage company profiles']],

            ['name' => 'manage-any-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage All Company Profiles', 'ar' => 'Manage All Company Profiles'], 'description' => ['en' => 'Manage Any Company Profiles', 'ar' => 'Manage Any Company Profiles']],

            ['name' => 'manage-own-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Manage Own Company Profiles', 'ar' => 'Manage Own Company Profiles'], 'description' => ['en' => 'Manage Limited Company Profiles that is created by own', 'ar' => 'Manage Limited Company Profiles that is created by own']],

            ['name' => 'view-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'View Company Profiles', 'ar' => 'View Company Profiles'], 'description' => ['en' => 'View Company Profiles', 'ar' => 'View Company Profiles']],

            ['name' => 'create-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Create Company Profiles', 'ar' => 'Create Company Profiles'], 'description' => ['en' => 'Can create company profiles', 'ar' => 'Can create company profiles']],

            ['name' => 'edit-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Edit Company Profiles', 'ar' => 'Edit Company Profiles'], 'description' => ['en' => 'Can edit company profiles', 'ar' => 'Can edit company profiles']],

            ['name' => 'delete-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Delete Company Profiles', 'ar' => 'Delete Company Profiles'], 'description' => ['en' => 'Can delete company profiles', 'ar' => 'Can delete company profiles']],

            ['name' => 'toggle-status-company-profiles', 'module' => 'company_profiles', 'label' => ['en' => 'Toggle Status Company Profiles', 'ar' => 'Toggle Status Company Profiles'], 'description' => ['en' => 'Can toggle status of company profiles', 'ar' => 'Can toggle status of company profiles']],

            // Practice Area management
            ['name' => 'manage-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage Practice Areas', 'ar' => 'Manage Practice Areas'], 'description' => ['en' => 'Can manage practice areas', 'ar' => 'Can manage practice areas']],

            ['name' => 'manage-any-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage All Practice Areas', 'ar' => 'Manage All Practice Areas'], 'description' => ['en' => 'Manage Any Practice Areas', 'ar' => 'Manage Any Practice Areas']],

            ['name' => 'manage-own-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Manage Own Practice Areas', 'ar' => 'Manage Own Practice Areas'], 'description' => ['en' => 'Manage Limited Practice Areas that is created by own', 'ar' => 'Manage Limited Practice Areas that is created by own']],

            ['name' => 'view-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'View Practice Areas', 'ar' => 'View Practice Areas'], 'description' => ['en' => 'View Practice Areas', 'ar' => 'View Practice Areas']],

            ['name' => 'create-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Create Practice Areas', 'ar' => 'Create Practice Areas'], 'description' => ['en' => 'Can create practice areas', 'ar' => 'Can create practice areas']],

            ['name' => 'edit-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Edit Practice Areas', 'ar' => 'Edit Practice Areas'], 'description' => ['en' => 'Can edit practice areas', 'ar' => 'Can edit practice areas']],

            ['name' => 'delete-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Delete Practice Areas', 'ar' => 'Delete Practice Areas'], 'description' => ['en' => 'Can delete practice areas', 'ar' => 'Can delete practice areas']],

            ['name' => 'toggle-status-practice-areas', 'module' => 'practice_areas', 'label' => ['en' => 'Toggle Status Practice Areas', 'ar' => 'Toggle Status Practice Areas'], 'description' => ['en' => 'Can toggle status of practice areas', 'ar' => 'Can toggle status of practice areas']],

            // Company Setting management
            ['name' => 'manage-company-settings', 'module' => 'company_settings', 'label' => ['en' => 'Manage Company Settings', 'ar' => 'Manage Company Settings'], 'description' => ['en' => 'Can manage company settings', 'ar' => 'Can manage company settings']],

            ['name' => 'manage-any-company-settings', 'module' => 'company_settings', 'label' => ['en' => 'Manage All Company Settings', 'ar' => 'Manage All Company Settings'], 'description' => ['en' => 'Manage Any Company Settings', 'ar' => 'Manage Any Company Settings']],

            ['name' => 'manage-own-company-settings', 'module' => 'company_settings', 'label' => ['en' => 'Manage Own Company Settings', 'ar' => 'Manage Own Company Settings'], 'description' => ['en' => 'Manage Limited Company Settings that is created by own', 'ar' => 'Manage Limited Company Settings that is created by own']],

            ['name' => 'view-company-settings', 'module' => 'company_settings', 'label' => ['en' => 'View Company Settings', 'ar' => 'View Company Settings'], 'description' => ['en' => 'View Company Settings', 'ar' => 'View Company Settings']],

            ['name' => 'edit-company-settings', 'module' => 'company_settings', 'label' => ['en' => 'Edit Company Settings', 'ar' => 'Edit Company Settings'], 'description' => ['en' => 'Can edit company settings', 'ar' => 'Can edit company settings']],

            // Case Document management
            ['name' => 'manage-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage Case Documents', 'ar' => 'Manage Case Documents'], 'description' => ['en' => 'Can manage case documents', 'ar' => 'Can manage case documents']],

            ['name' => 'manage-any-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage All Case Documents', 'ar' => 'Manage All Case Documents'], 'description' => ['en' => 'Manage Any Case Documents', 'ar' => 'Manage Any Case Documents']],

            ['name' => 'manage-own-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Manage Own Case Documents', 'ar' => 'Manage Own Case Documents'], 'description' => ['en' => 'Manage Limited Case Documents that is created by own', 'ar' => 'Manage Limited Case Documents that is created by own']],

            ['name' => 'view-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'View Case Documents', 'ar' => 'View Case Documents'], 'description' => ['en' => 'View Case Documents', 'ar' => 'View Case Documents']],

            ['name' => 'create-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Create Case Documents', 'ar' => 'Create Case Documents'], 'description' => ['en' => 'Can create case documents', 'ar' => 'Can create case documents']],

            ['name' => 'edit-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Edit Case Documents', 'ar' => 'Edit Case Documents'], 'description' => ['en' => 'Can edit case documents', 'ar' => 'Can edit case documents']],

            ['name' => 'delete-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Delete Case Documents', 'ar' => 'Delete Case Documents'], 'description' => ['en' => 'Can delete case documents', 'ar' => 'Can delete case documents']],

            ['name' => 'download-case-documents', 'module' => 'case_documents', 'label' => ['en' => 'Download Case Documents', 'ar' => 'Download Case Documents'], 'description' => ['en' => 'Can download case documents', 'ar' => 'Can download case documents']],

            // Case Note management
            ['name' => 'manage-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage Case Notes', 'ar' => 'Manage Case Notes'], 'description' => ['en' => 'Can manage case notes', 'ar' => 'Can manage case notes']],

            ['name' => 'manage-any-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage All Case Notes', 'ar' => 'Manage All Case Notes'], 'description' => ['en' => 'Manage Any Case Notes', 'ar' => 'Manage Any Case Notes']],

            ['name' => 'manage-own-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Manage Own Case Notes', 'ar' => 'Manage Own Case Notes'], 'description' => ['en' => 'Manage Limited Case Notes that is created by own', 'ar' => 'Manage Limited Case Notes that is created by own']],

            ['name' => 'view-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'View Case Notes', 'ar' => 'View Case Notes'], 'description' => ['en' => 'View Case Notes', 'ar' => 'View Case Notes']],

            ['name' => 'create-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Create Case Notes', 'ar' => 'Create Case Notes'], 'description' => ['en' => 'Can create case notes', 'ar' => 'Can create case notes']],

            ['name' => 'edit-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Edit Case Notes', 'ar' => 'Edit Case Notes'], 'description' => ['en' => 'Can edit case notes', 'ar' => 'Can edit case notes']],

            ['name' => 'delete-case-notes', 'module' => 'case_notes', 'label' => ['en' => 'Delete Case Notes', 'ar' => 'Delete Case Notes'], 'description' => ['en' => 'Can delete case notes', 'ar' => 'Can delete case notes']],

            // Case Management
            ['name' => 'manage-cases', 'module' => 'cases', 'label' => ['en' => 'Manage Cases', 'ar' => 'Manage Cases'], 'description' => ['en' => 'Can manage cases', 'ar' => 'Can manage cases']],

            ['name' => 'manage-any-cases', 'module' => 'cases', 'label' => ['en' => 'Manage All Cases', 'ar' => 'Manage All Cases'], 'description' => ['en' => 'Manage Any Cases', 'ar' => 'Manage Any Cases']],

            ['name' => 'manage-own-cases', 'module' => 'cases', 'label' => ['en' => 'Manage Own Cases', 'ar' => 'Manage Own Cases'], 'description' => ['en' => 'Manage Limited Cases that is created by own', 'ar' => 'Manage Limited Cases that is created by own']],

            ['name' => 'view-cases', 'module' => 'cases', 'label' => ['en' => 'View Cases', 'ar' => 'View Cases'], 'description' => ['en' => 'View Cases', 'ar' => 'View Cases']],

            ['name' => 'create-cases', 'module' => 'cases', 'label' => ['en' => 'Create Cases', 'ar' => 'Create Cases'], 'description' => ['en' => 'Can create cases', 'ar' => 'Can create cases']],

            ['name' => 'edit-cases', 'module' => 'cases', 'label' => ['en' => 'Edit Cases', 'ar' => 'Edit Cases'], 'description' => ['en' => 'Can edit cases', 'ar' => 'Can edit cases']],

            ['name' => 'delete-cases', 'module' => 'cases', 'label' => ['en' => 'Delete Cases', 'ar' => 'Delete Cases'], 'description' => ['en' => 'Can delete cases', 'ar' => 'Can delete cases']],

            ['name' => 'toggle-status-cases', 'module' => 'cases', 'label' => ['en' => 'Toggle Status Cases', 'ar' => 'Toggle Status Cases'], 'description' => ['en' => 'Can toggle status of cases', 'ar' => 'Can toggle status of cases']],

            // Case Types
            ['name' => 'manage-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage Case Types', 'ar' => 'Manage Case Types'], 'description' => ['en' => 'Can manage case types', 'ar' => 'Can manage case types']],

            ['name' => 'manage-any-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage All Case Types', 'ar' => 'Manage All Case Types'], 'description' => ['en' => 'Manage Any Case Types', 'ar' => 'Manage Any Case Types']],

            ['name' => 'manage-own-case-types', 'module' => 'case_types', 'label' => ['en' => 'Manage Own Case Types', 'ar' => 'Manage Own Case Types'], 'description' => ['en' => 'Manage Limited Case Types that is created by own', 'ar' => 'Manage Limited Case Types that is created by own']],

            ['name' => 'view-case-types', 'module' => 'case_types', 'label' => ['en' => 'View Case Types', 'ar' => 'View Case Types'], 'description' => ['en' => 'View Case Types', 'ar' => 'View Case Types']],

            ['name' => 'create-case-types', 'module' => 'case_types', 'label' => ['en' => 'Create Case Types', 'ar' => 'Create Case Types'], 'description' => ['en' => 'Can create case types', 'ar' => 'Can create case types']],

            ['name' => 'edit-case-types', 'module' => 'case_types', 'label' => ['en' => 'Edit Case Types', 'ar' => 'Edit Case Types'], 'description' => ['en' => 'Can edit case types', 'ar' => 'Can edit case types']],

            ['name' => 'delete-case-types', 'module' => 'case_types', 'label' => ['en' => 'Delete Case Types', 'ar' => 'Delete Case Types'], 'description' => ['en' => 'Can delete case types', 'ar' => 'Can delete case types']],

            ['name' => 'toggle-status-case-types', 'module' => 'case_types', 'label' => ['en' => 'Toggle Status Case Types', 'ar' => 'Toggle Status Case Types'], 'description' => ['en' => 'Can toggle status of case types', 'ar' => 'Can toggle status of case types']],

            // Case Categories
            ['name' => 'manage-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage Case Categories', 'ar' => 'Manage Case Categories'], 'description' => ['en' => 'Can manage case categories', 'ar' => 'Can manage case categories']],

            ['name' => 'manage-any-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage All Case Categories', 'ar' => 'Manage All Case Categories'], 'description' => ['en' => 'Manage Any Case Categories', 'ar' => 'Manage Any Case Categories']],

            ['name' => 'manage-own-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Manage Own Case Categories', 'ar' => 'Manage Own Case Categories'], 'description' => ['en' => 'Manage Limited Case Categories that is created by own', 'ar' => 'Manage Limited Case Categories that is created by own']],

            ['name' => 'view-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'View Case Categories', 'ar' => 'View Case Categories'], 'description' => ['en' => 'View Case Categories', 'ar' => 'View Case Categories']],

            ['name' => 'create-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Create Case Categories', 'ar' => 'Create Case Categories'], 'description' => ['en' => 'Can create case categories', 'ar' => 'Can create case categories']],

            ['name' => 'edit-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Edit Case Categories', 'ar' => 'Edit Case Categories'], 'description' => ['en' => 'Can edit case categories', 'ar' => 'Can edit case categories']],

            ['name' => 'delete-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Delete Case Categories', 'ar' => 'Delete Case Categories'], 'description' => ['en' => 'Can delete case categories', 'ar' => 'Can delete case categories']],

            ['name' => 'toggle-status-case-categories', 'module' => 'case_categories', 'label' => ['en' => 'Toggle Status Case Categories', 'ar' => 'Toggle Status Case Categories'], 'description' => ['en' => 'Can toggle status of case categories', 'ar' => 'Can toggle status of case categories']],

            // Case Statuses
            ['name' => 'manage-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage Case Statuses', 'ar' => 'Manage Case Statuses'], 'description' => ['en' => 'Can manage case statuses', 'ar' => 'Can manage case statuses']],

            ['name' => 'manage-any-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage All Case Statuses', 'ar' => 'Manage All Case Statuses'], 'description' => ['en' => 'Manage Any Case Statuses', 'ar' => 'Manage Any Case Statuses']],

            ['name' => 'manage-own-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Manage Own Case Statuses', 'ar' => 'Manage Own Case Statuses'], 'description' => ['en' => 'Manage Limited Case Statuses that is created by own', 'ar' => 'Manage Limited Case Statuses that is created by own']],

            ['name' => 'view-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'View Case Statuses', 'ar' => 'View Case Statuses'], 'description' => ['en' => 'View Case Statuses', 'ar' => 'View Case Statuses']],

            ['name' => 'create-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Create Case Statuses', 'ar' => 'Create Case Statuses'], 'description' => ['en' => 'Can create case statuses', 'ar' => 'Can create case statuses']],

            ['name' => 'edit-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Edit Case Statuses', 'ar' => 'Edit Case Statuses'], 'description' => ['en' => 'Can edit case statuses', 'ar' => 'Can edit case statuses']],

            ['name' => 'delete-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Delete Case Statuses', 'ar' => 'Delete Case Statuses'], 'description' => ['en' => 'Can delete case statuses', 'ar' => 'Can delete case statuses']],

            ['name' => 'toggle-status-case-statuses', 'module' => 'case_statuses', 'label' => ['en' => 'Toggle Status Case Statuses', 'ar' => 'Toggle Status Case Statuses'], 'description' => ['en' => 'Can toggle status of case statuses', 'ar' => 'Can toggle status of case statuses']],

            // Case Timelines
            ['name' => 'manage-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage Case Timelines', 'ar' => 'Manage Case Timelines'], 'description' => ['en' => 'Can manage case timelines', 'ar' => 'Can manage case timelines']],

            ['name' => 'manage-any-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage All Case Timelines', 'ar' => 'Manage All Case Timelines'], 'description' => ['en' => 'Manage Any Case Timelines', 'ar' => 'Manage Any Case Timelines']],

            ['name' => 'manage-own-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Manage Own Case Timelines', 'ar' => 'Manage Own Case Timelines'], 'description' => ['en' => 'Manage Limited Case Timelines that is created by own', 'ar' => 'Manage Limited Case Timelines that is created by own']],

            ['name' => 'view-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'View Case Timelines', 'ar' => 'View Case Timelines'], 'description' => ['en' => 'View Case Timelines', 'ar' => 'View Case Timelines']],

            ['name' => 'create-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Create Case Timelines', 'ar' => 'Create Case Timelines'], 'description' => ['en' => 'Can create case timelines', 'ar' => 'Can create case timelines']],

            ['name' => 'edit-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Edit Case Timelines', 'ar' => 'Edit Case Timelines'], 'description' => ['en' => 'Can edit case timelines', 'ar' => 'Can edit case timelines']],

            ['name' => 'delete-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Delete Case Timelines', 'ar' => 'Delete Case Timelines'], 'description' => ['en' => 'Can delete case timelines', 'ar' => 'Can delete case timelines']],

            ['name' => 'toggle-status-case-timelines', 'module' => 'case_timelines', 'label' => ['en' => 'Toggle Status Case Timelines', 'ar' => 'Toggle Status Case Timelines'], 'description' => ['en' => 'Can toggle status of case timelines', 'ar' => 'Can toggle status of case timelines']],

            // Case Team Members
            ['name' => 'manage-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage Case Team Members', 'ar' => 'Manage Case Team Members'], 'description' => ['en' => 'Can manage case team members', 'ar' => 'Can manage case team members']],

            ['name' => 'manage-any-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage All Case Team Members', 'ar' => 'Manage All Case Team Members'], 'description' => ['en' => 'Manage Any Case Team Members', 'ar' => 'Manage Any Case Team Members']],

            ['name' => 'manage-own-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Manage Own Case Team Members', 'ar' => 'Manage Own Case Team Members'], 'description' => ['en' => 'Manage Limited Case Team Members that is created by own', 'ar' => 'Manage Limited Case Team Members that is created by own']],

            ['name' => 'view-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'View Case Team Members', 'ar' => 'View Case Team Members'], 'description' => ['en' => 'View Case Team Members', 'ar' => 'View Case Team Members']],

            ['name' => 'create-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Create Case Team Members', 'ar' => 'Create Case Team Members'], 'description' => ['en' => 'Can create case team members', 'ar' => 'Can create case team members']],

            ['name' => 'edit-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Edit Case Team Members', 'ar' => 'Edit Case Team Members'], 'description' => ['en' => 'Can edit case team members', 'ar' => 'Can edit case team members']],

            ['name' => 'delete-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Delete Case Team Members', 'ar' => 'Delete Case Team Members'], 'description' => ['en' => 'Can delete case team members', 'ar' => 'Can delete case team members']],

            ['name' => 'toggle-status-case-team-members', 'module' => 'case_team_members', 'label' => ['en' => 'Toggle Status Case Team Members', 'ar' => 'Toggle Status Case Team Members'], 'description' => ['en' => 'Can toggle status of case team members', 'ar' => 'Can toggle status of case team members']],

            // Document Types
            ['name' => 'manage-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage Document Types', 'ar' => 'Manage Document Types'], 'description' => ['en' => 'Can manage document types', 'ar' => 'Can manage document types']],

            ['name' => 'manage-any-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage All Document Types', 'ar' => 'Manage All Document Types'], 'description' => ['en' => 'Manage Any Document Types', 'ar' => 'Manage Any Document Types']],

            ['name' => 'manage-own-document-types', 'module' => 'document_types', 'label' => ['en' => 'Manage Own Document Types', 'ar' => 'Manage Own Document Types'], 'description' => ['en' => 'Manage Limited Document Types that is created by own', 'ar' => 'Manage Limited Document Types that is created by own']],

            ['name' => 'view-document-types', 'module' => 'document_types', 'label' => ['en' => 'View Document Types', 'ar' => 'View Document Types'], 'description' => ['en' => 'View Document Types', 'ar' => 'View Document Types']],

            ['name' => 'create-document-types', 'module' => 'document_types', 'label' => ['en' => 'Create Document Types', 'ar' => 'Create Document Types'], 'description' => ['en' => 'Can create document types', 'ar' => 'Can create document types']],

            ['name' => 'edit-document-types', 'module' => 'document_types', 'label' => ['en' => 'Edit Document Types', 'ar' => 'Edit Document Types'], 'description' => ['en' => 'Can edit document types', 'ar' => 'Can edit document types']],

            ['name' => 'delete-document-types', 'module' => 'document_types', 'label' => ['en' => 'Delete Document Types', 'ar' => 'Delete Document Types'], 'description' => ['en' => 'Can delete document types', 'ar' => 'Can delete document types']],

            // Document Categories
            ['name' => 'manage-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage Document Categories', 'ar' => 'Manage Document Categories'], 'description' => ['en' => 'Can manage document categories', 'ar' => 'Can manage document categories']],

            ['name' => 'manage-any-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage All Document Categories', 'ar' => 'Manage All Document Categories'], 'description' => ['en' => 'Manage Any Document Categories', 'ar' => 'Manage Any Document Categories']],

            ['name' => 'manage-own-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Manage Own Document Categories', 'ar' => 'Manage Own Document Categories'], 'description' => ['en' => 'Manage Limited Document Categories that is created by own', 'ar' => 'Manage Limited Document Categories that is created by own']],

            ['name' => 'view-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'View Document Categories', 'ar' => 'View Document Categories'], 'description' => ['en' => 'View Document Categories', 'ar' => 'View Document Categories']],

            ['name' => 'create-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Create Document Categories', 'ar' => 'Create Document Categories'], 'description' => ['en' => 'Can create document categories', 'ar' => 'Can create document categories']],

            ['name' => 'edit-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Edit Document Categories', 'ar' => 'Edit Document Categories'], 'description' => ['en' => 'Can edit document categories', 'ar' => 'Can edit document categories']],

            ['name' => 'delete-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Delete Document Categories', 'ar' => 'Delete Document Categories'], 'description' => ['en' => 'Can delete document categories', 'ar' => 'Can delete document categories']],

            ['name' => 'toggle-status-document-categories', 'module' => 'document_categories', 'label' => ['en' => 'Toggle Status Document Categories', 'ar' => 'Toggle Status Document Categories'], 'description' => ['en' => 'Can toggle status of document categories', 'ar' => 'Can toggle status of document categories']],

            // Event Types
            ['name' => 'manage-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage Event Types', 'ar' => 'Manage Event Types'], 'description' => ['en' => 'Can manage event types', 'ar' => 'Can manage event types']],

            ['name' => 'manage-any-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage All Event Types', 'ar' => 'Manage All Event Types'], 'description' => ['en' => 'Manage Any Event Types', 'ar' => 'Manage Any Event Types']],

            ['name' => 'manage-own-event-types', 'module' => 'event_types', 'label' => ['en' => 'Manage Own Event Types', 'ar' => 'Manage Own Event Types'], 'description' => ['en' => 'Manage Limited Event Types that is created by own', 'ar' => 'Manage Limited Event Types that is created by own']],

            ['name' => 'view-event-types', 'module' => 'event_types', 'label' => ['en' => 'View Event Types', 'ar' => 'View Event Types'], 'description' => ['en' => 'View Event Types', 'ar' => 'View Event Types']],

            ['name' => 'create-event-types', 'module' => 'event_types', 'label' => ['en' => 'Create Event Types', 'ar' => 'Create Event Types'], 'description' => ['en' => 'Can create event types', 'ar' => 'Can create event types']],

            ['name' => 'edit-event-types', 'module' => 'event_types', 'label' => ['en' => 'Edit Event Types', 'ar' => 'Edit Event Types'], 'description' => ['en' => 'Can edit event types', 'ar' => 'Can edit event types']],

            ['name' => 'delete-event-types', 'module' => 'event_types', 'label' => ['en' => 'Delete Event Types', 'ar' => 'Delete Event Types'], 'description' => ['en' => 'Can delete event types', 'ar' => 'Can delete event types']],

            // Court Types
            ['name' => 'manage-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage Court Types', 'ar' => 'Manage Court Types'], 'description' => ['en' => 'Can manage court types', 'ar' => 'Can manage court types']],

            ['name' => 'manage-any-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage All Court Types', 'ar' => 'Manage All Court Types'], 'description' => ['en' => 'Manage Any Court Types', 'ar' => 'Manage Any Court Types']],

            ['name' => 'manage-own-court-types', 'module' => 'court_types', 'label' => ['en' => 'Manage Own Court Types', 'ar' => 'Manage Own Court Types'], 'description' => ['en' => 'Manage Limited Court Types that is created by own', 'ar' => 'Manage Limited Court Types that is created by own']],

            ['name' => 'view-court-types', 'module' => 'court_types', 'label' => ['en' => 'View Court Types', 'ar' => 'View Court Types'], 'description' => ['en' => 'View Court Types', 'ar' => 'View Court Types']],

            ['name' => 'create-court-types', 'module' => 'court_types', 'label' => ['en' => 'Create Court Types', 'ar' => 'Create Court Types'], 'description' => ['en' => 'Can create court types', 'ar' => 'Can create court types']],

            ['name' => 'edit-court-types', 'module' => 'court_types', 'label' => ['en' => 'Edit Court Types', 'ar' => 'Edit Court Types'], 'description' => ['en' => 'Can edit court types', 'ar' => 'Can edit court types']],

            ['name' => 'delete-court-types', 'module' => 'court_types', 'label' => ['en' => 'Delete Court Types', 'ar' => 'Delete Court Types'], 'description' => ['en' => 'Can delete court types', 'ar' => 'Can delete court types']],

            // Circle Types
            ['name' => 'manage-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage Circle Types', 'ar' => 'Manage Circle Types'], 'description' => ['en' => 'Can manage circle types', 'ar' => 'Can manage circle types']],

            ['name' => 'manage-any-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage All Circle Types', 'ar' => 'Manage All Circle Types'], 'description' => ['en' => 'Manage Any Circle Types', 'ar' => 'Manage Any Circle Types']],

            ['name' => 'manage-own-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Manage Own Circle Types', 'ar' => 'Manage Own Circle Types'], 'description' => ['en' => 'Manage Limited Circle Types that is created by own', 'ar' => 'Manage Limited Circle Types that is created by own']],

            ['name' => 'view-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'View Circle Types', 'ar' => 'View Circle Types'], 'description' => ['en' => 'View Circle Types', 'ar' => 'View Circle Types']],

            ['name' => 'create-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Create Circle Types', 'ar' => 'Create Circle Types'], 'description' => ['en' => 'Can create circle types', 'ar' => 'Can create circle types']],

            ['name' => 'edit-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Edit Circle Types', 'ar' => 'Edit Circle Types'], 'description' => ['en' => 'Can edit circle types', 'ar' => 'Can edit circle types']],

            ['name' => 'delete-circle-types', 'module' => 'circle_types', 'label' => ['en' => 'Delete Circle Types', 'ar' => 'Delete Circle Types'], 'description' => ['en' => 'Can delete circle types', 'ar' => 'Can delete circle types']],

            // Hearings
            ['name' => 'manage-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage Hearings', 'ar' => 'Manage Hearings'], 'description' => ['en' => 'Can manage hearings', 'ar' => 'Can manage hearings']],

            ['name' => 'manage-any-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage All Hearings', 'ar' => 'Manage All Hearings'], 'description' => ['en' => 'Manage Any Hearings', 'ar' => 'Manage Any Hearings']],

            ['name' => 'manage-own-hearings', 'module' => 'hearings', 'label' => ['en' => 'Manage Own Hearings', 'ar' => 'Manage Own Hearings'], 'description' => ['en' => 'Manage Limited Hearings that is created by own', 'ar' => 'Manage Limited Hearings that is created by own']],

            ['name' => 'view-hearings', 'module' => 'hearings', 'label' => ['en' => 'View Hearings', 'ar' => 'View Hearings'], 'description' => ['en' => 'View Hearings', 'ar' => 'View Hearings']],

            ['name' => 'create-hearings', 'module' => 'hearings', 'label' => ['en' => 'Create Hearings', 'ar' => 'Create Hearings'], 'description' => ['en' => 'Can create hearings', 'ar' => 'Can create hearings']],

            ['name' => 'edit-hearings', 'module' => 'hearings', 'label' => ['en' => 'Edit Hearings', 'ar' => 'Edit Hearings'], 'description' => ['en' => 'Can edit hearings', 'ar' => 'Can edit hearings']],

            ['name' => 'delete-hearings', 'module' => 'hearings', 'label' => ['en' => 'Delete Hearings', 'ar' => 'Delete Hearings'], 'description' => ['en' => 'Can delete hearings', 'ar' => 'Can delete hearings']],

            // Court Management
            ['name' => 'manage-courts', 'module' => 'courts', 'label' => ['en' => 'Manage Courts', 'ar' => 'Manage Courts'], 'description' => ['en' => 'Can manage courts', 'ar' => 'Can manage courts']],

            ['name' => 'manage-any-courts', 'module' => 'courts', 'label' => ['en' => 'Manage All Courts', 'ar' => 'Manage All Courts'], 'description' => ['en' => 'Manage Any Courts', 'ar' => 'Manage Any Courts']],

            ['name' => 'manage-own-courts', 'module' => 'courts', 'label' => ['en' => 'Manage Own Courts', 'ar' => 'Manage Own Courts'], 'description' => ['en' => 'Manage Limited Courts that is created by own', 'ar' => 'Manage Limited Courts that is created by own']],

            ['name' => 'view-courts', 'module' => 'courts', 'label' => ['en' => 'View Courts', 'ar' => 'View Courts'], 'description' => ['en' => 'View Courts', 'ar' => 'View Courts']],

            ['name' => 'create-courts', 'module' => 'courts', 'label' => ['en' => 'Create Courts', 'ar' => 'Create Courts'], 'description' => ['en' => 'Can create courts', 'ar' => 'Can create courts']],

            ['name' => 'edit-courts', 'module' => 'courts', 'label' => ['en' => 'Edit Courts', 'ar' => 'Edit Courts'], 'description' => ['en' => 'Can edit courts', 'ar' => 'Can edit courts']],

            ['name' => 'delete-courts', 'module' => 'courts', 'label' => ['en' => 'Delete Courts', 'ar' => 'Delete Courts'], 'description' => ['en' => 'Can delete courts', 'ar' => 'Can delete courts']],

            ['name' => 'toggle-status-courts', 'module' => 'courts', 'label' => ['en' => 'Toggle Status Courts', 'ar' => 'Toggle Status Courts'], 'description' => ['en' => 'Can toggle status of courts', 'ar' => 'Can toggle status of courts']],

            // Hearing Type Management
            ['name' => 'manage-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage Hearing Types', 'ar' => 'Manage Hearing Types'], 'description' => ['en' => 'Can manage hearing types', 'ar' => 'Can manage hearing types']],

            ['name' => 'manage-any-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage All Hearing Types', 'ar' => 'Manage All Hearing Types'], 'description' => ['en' => 'Manage Any Hearing Types', 'ar' => 'Manage Any Hearing Types']],

            ['name' => 'manage-own-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Manage Own Hearing Types', 'ar' => 'Manage Own Hearing Types'], 'description' => ['en' => 'Manage Limited Hearing Types that is created by own', 'ar' => 'Manage Limited Hearing Types that is created by own']],

            ['name' => 'view-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'View Hearing Types', 'ar' => 'View Hearing Types'], 'description' => ['en' => 'View Hearing Types', 'ar' => 'View Hearing Types']],

            ['name' => 'create-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Create Hearing Types', 'ar' => 'Create Hearing Types'], 'description' => ['en' => 'Can create hearing types', 'ar' => 'Can create hearing types']],

            ['name' => 'edit-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Edit Hearing Types', 'ar' => 'Edit Hearing Types'], 'description' => ['en' => 'Can edit hearing types', 'ar' => 'Can edit hearing types']],

            ['name' => 'delete-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Delete Hearing Types', 'ar' => 'Delete Hearing Types'], 'description' => ['en' => 'Can delete hearing types', 'ar' => 'Can delete hearing types']],

            ['name' => 'toggle-status-hearing-types', 'module' => 'hearing_types', 'label' => ['en' => 'Toggle Status Hearing Types', 'ar' => 'Toggle Status Hearing Types'], 'description' => ['en' => 'Can toggle status of hearing types', 'ar' => 'Can toggle status of hearing types']],

            // Documents
            ['name' => 'manage-documents', 'module' => 'documents', 'label' => ['en' => 'Manage Documents', 'ar' => 'Manage Documents'], 'description' => ['en' => 'Can manage documents', 'ar' => 'Can manage documents']],

            ['name' => 'manage-any-documents', 'module' => 'documents', 'label' => ['en' => 'Manage All Documents', 'ar' => 'Manage All Documents'], 'description' => ['en' => 'Manage Any Documents', 'ar' => 'Manage Any Documents']],

            ['name' => 'manage-own-documents', 'module' => 'documents', 'label' => ['en' => 'Manage Own Documents', 'ar' => 'Manage Own Documents'], 'description' => ['en' => 'Manage Limited Documents that is created by own', 'ar' => 'Manage Limited Documents that is created by own']],

            ['name' => 'view-documents', 'module' => 'documents', 'label' => ['en' => 'View Documents', 'ar' => 'View Documents'], 'description' => ['en' => 'View Documents', 'ar' => 'View Documents']],

            ['name' => 'create-documents', 'module' => 'documents', 'label' => ['en' => 'Create Documents', 'ar' => 'Create Documents'], 'description' => ['en' => 'Can create documents', 'ar' => 'Can create documents']],

            ['name' => 'edit-documents', 'module' => 'documents', 'label' => ['en' => 'Edit Documents', 'ar' => 'Edit Documents'], 'description' => ['en' => 'Can edit documents', 'ar' => 'Can edit documents']],

            ['name' => 'delete-documents', 'module' => 'documents', 'label' => ['en' => 'Delete Documents', 'ar' => 'Delete Documents'], 'description' => ['en' => 'Can delete documents', 'ar' => 'Can delete documents']],

            ['name' => 'download-documents', 'module' => 'documents', 'label' => ['en' => 'Download Documents', 'ar' => 'Download Documents'], 'description' => ['en' => 'Can download documents', 'ar' => 'Can download documents']],

            ['name' => 'toggle-status-documents', 'module' => 'documents', 'label' => ['en' => 'Toggle Status Documents', 'ar' => 'Toggle Status Documents'], 'description' => ['en' => 'Can toggle status of documents', 'ar' => 'Can toggle status of documents']],

            // Document Versions
            ['name' => 'manage-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage Document Versions', 'ar' => 'Manage Document Versions'], 'description' => ['en' => 'Can manage document versions', 'ar' => 'Can manage document versions']],

            ['name' => 'manage-any-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage All Document Versions', 'ar' => 'Manage All Document Versions'], 'description' => ['en' => 'Manage Any Document Versions', 'ar' => 'Manage Any Document Versions']],

            ['name' => 'manage-own-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Manage Own Document Versions', 'ar' => 'Manage Own Document Versions'], 'description' => ['en' => 'Manage Limited Document Versions that is created by own', 'ar' => 'Manage Limited Document Versions that is created by own']],

            ['name' => 'view-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'View Document Versions', 'ar' => 'View Document Versions'], 'description' => ['en' => 'View Document Versions', 'ar' => 'View Document Versions']],

            ['name' => 'create-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Create Document Versions', 'ar' => 'Create Document Versions'], 'description' => ['en' => 'Can create document versions', 'ar' => 'Can create document versions']],

            ['name' => 'delete-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Delete Document Versions', 'ar' => 'Delete Document Versions'], 'description' => ['en' => 'Can delete document versions', 'ar' => 'Can delete document versions']],

            ['name' => 'download-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Download Document Versions', 'ar' => 'Download Document Versions'], 'description' => ['en' => 'Can download document versions', 'ar' => 'Can download document versions']],

            ['name' => 'restore-document-versions', 'module' => 'document_versions', 'label' => ['en' => 'Restore Document Versions', 'ar' => 'Restore Document Versions'], 'description' => ['en' => 'Can restore document versions', 'ar' => 'Can restore document versions']],

            // Document Comments
            ['name' => 'manage-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage Document Comments', 'ar' => 'Manage Document Comments'], 'description' => ['en' => 'Can manage document comments', 'ar' => 'Can manage document comments']],

            ['name' => 'manage-any-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage All Document Comments', 'ar' => 'Manage All Document Comments'], 'description' => ['en' => 'Manage Any Document Comments', 'ar' => 'Manage Any Document Comments']],

            ['name' => 'manage-own-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Manage Own Document Comments', 'ar' => 'Manage Own Document Comments'], 'description' => ['en' => 'Manage Limited Document Comments that is created by own', 'ar' => 'Manage Limited Document Comments that is created by own']],

            ['name' => 'view-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'View Document Comments', 'ar' => 'View Document Comments'], 'description' => ['en' => 'View Document Comments', 'ar' => 'View Document Comments']],

            ['name' => 'create-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Create Document Comments', 'ar' => 'Create Document Comments'], 'description' => ['en' => 'Can create document comments', 'ar' => 'Can create document comments']],

            ['name' => 'edit-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Edit Document Comments', 'ar' => 'Edit Document Comments'], 'description' => ['en' => 'Can edit document comments', 'ar' => 'Can edit document comments']],

            ['name' => 'delete-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Delete Document Comments', 'ar' => 'Delete Document Comments'], 'description' => ['en' => 'Can delete document comments', 'ar' => 'Can delete document comments']],

            ['name' => 'resolve-document-comments', 'module' => 'document_comments', 'label' => ['en' => 'Resolve Document Comments', 'ar' => 'Resolve Document Comments'], 'description' => ['en' => 'Can resolve document comments', 'ar' => 'Can resolve document comments']],

            // Document Permissions
            ['name' => 'manage-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage Document Permissions', 'ar' => 'Manage Document Permissions'], 'description' => ['en' => 'Can manage document permissions', 'ar' => 'Can manage document permissions']],

            ['name' => 'manage-any-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage All Document Permissions', 'ar' => 'Manage All Document Permissions'], 'description' => ['en' => 'Manage Any Document Permissions', 'ar' => 'Manage Any Document Permissions']],

            ['name' => 'manage-own-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Manage Own Document Permissions', 'ar' => 'Manage Own Document Permissions'], 'description' => ['en' => 'Manage Limited Document Permissions that is created by own', 'ar' => 'Manage Limited Document Permissions that is created by own']],

            ['name' => 'view-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'View Document Permissions', 'ar' => 'View Document Permissions'], 'description' => ['en' => 'View Document Permissions', 'ar' => 'View Document Permissions']],

            ['name' => 'create-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Create Document Permissions', 'ar' => 'Create Document Permissions'], 'description' => ['en' => 'Can create document permissions', 'ar' => 'Can create document permissions']],

            ['name' => 'edit-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Edit Document Permissions', 'ar' => 'Edit Document Permissions'], 'description' => ['en' => 'Can edit document permissions', 'ar' => 'Can edit document permissions']],

            ['name' => 'delete-document-permissions', 'module' => 'document_permissions', 'label' => ['en' => 'Delete Document Permissions', 'ar' => 'Delete Document Permissions'], 'description' => ['en' => 'Can delete document permissions', 'ar' => 'Can delete document permissions']],

            // Research Projects
            ['name' => 'manage-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage Research Projects', 'ar' => 'Manage Research Projects'], 'description' => ['en' => 'Can manage research projects', 'ar' => 'Can manage research projects']],

            ['name' => 'manage-any-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage All Research Projects', 'ar' => 'Manage All Research Projects'], 'description' => ['en' => 'Manage Any Research Projects', 'ar' => 'Manage Any Research Projects']],

            ['name' => 'manage-own-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Manage Own Research Projects', 'ar' => 'Manage Own Research Projects'], 'description' => ['en' => 'Manage Limited Research Projects that is created by own', 'ar' => 'Manage Limited Research Projects that is created by own']],

            ['name' => 'view-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'View Research Projects', 'ar' => 'View Research Projects'], 'description' => ['en' => 'View Research Projects', 'ar' => 'View Research Projects']],

            ['name' => 'create-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Create Research Projects', 'ar' => 'Create Research Projects'], 'description' => ['en' => 'Can create research projects', 'ar' => 'Can create research projects']],

            ['name' => 'edit-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Edit Research Projects', 'ar' => 'Edit Research Projects'], 'description' => ['en' => 'Can edit research projects', 'ar' => 'Can edit research projects']],

            ['name' => 'delete-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Delete Research Projects', 'ar' => 'Delete Research Projects'], 'description' => ['en' => 'Can delete research projects', 'ar' => 'Can delete research projects']],

            ['name' => 'toggle-status-research-projects', 'module' => 'research_projects', 'label' => ['en' => 'Toggle Status Research Projects', 'ar' => 'Toggle Status Research Projects'], 'description' => ['en' => 'Can toggle status of research projects', 'ar' => 'Can toggle status of research projects']],

            // Research Sources
            ['name' => 'manage-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage Research Sources', 'ar' => 'Manage Research Sources'], 'description' => ['en' => 'Can manage research sources', 'ar' => 'Can manage research sources']],

            ['name' => 'manage-any-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage All Research Sources', 'ar' => 'Manage All Research Sources'], 'description' => ['en' => 'Manage Any Research Sources', 'ar' => 'Manage Any Research Sources']],

            ['name' => 'manage-own-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Manage Own Research Sources', 'ar' => 'Manage Own Research Sources'], 'description' => ['en' => 'Manage Limited Research Sources that is created by own', 'ar' => 'Manage Limited Research Sources that is created by own']],

            ['name' => 'view-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'View Research Sources', 'ar' => 'View Research Sources'], 'description' => ['en' => 'View Research Sources', 'ar' => 'View Research Sources']],

            ['name' => 'create-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Create Research Sources', 'ar' => 'Create Research Sources'], 'description' => ['en' => 'Can create research sources', 'ar' => 'Can create research sources']],

            ['name' => 'edit-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Edit Research Sources', 'ar' => 'Edit Research Sources'], 'description' => ['en' => 'Can edit research sources', 'ar' => 'Can edit research sources']],

            ['name' => 'delete-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Delete Research Sources', 'ar' => 'Delete Research Sources'], 'description' => ['en' => 'Can delete research sources', 'ar' => 'Can delete research sources']],

            ['name' => 'toggle-status-research-sources', 'module' => 'research_sources', 'label' => ['en' => 'Toggle Status Research Sources', 'ar' => 'Toggle Status Research Sources'], 'description' => ['en' => 'Can toggle status of research sources', 'ar' => 'Can toggle status of research sources']],

            // Research Categories
            ['name' => 'manage-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage Research Categories', 'ar' => 'Manage Research Categories'], 'description' => ['en' => 'Can manage research categories', 'ar' => 'Can manage research categories']],

            ['name' => 'manage-any-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage All Research Categories', 'ar' => 'Manage All Research Categories'], 'description' => ['en' => 'Manage Any Research Categories', 'ar' => 'Manage Any Research Categories']],

            ['name' => 'manage-own-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Manage Own Research Categories', 'ar' => 'Manage Own Research Categories'], 'description' => ['en' => 'Manage Limited Research Categories that is created by own', 'ar' => 'Manage Limited Research Categories that is created by own']],

            ['name' => 'view-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'View Research Categories', 'ar' => 'View Research Categories'], 'description' => ['en' => 'View Research Categories', 'ar' => 'View Research Categories']],

            ['name' => 'create-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Create Research Categories', 'ar' => 'Create Research Categories'], 'description' => ['en' => 'Can create research categories', 'ar' => 'Can create research categories']],

            ['name' => 'edit-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Edit Research Categories', 'ar' => 'Edit Research Categories'], 'description' => ['en' => 'Can edit research categories', 'ar' => 'Can edit research categories']],

            ['name' => 'delete-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Delete Research Categories', 'ar' => 'Delete Research Categories'], 'description' => ['en' => 'Can delete research categories', 'ar' => 'Can delete research categories']],

            ['name' => 'toggle-status-research-categories', 'module' => 'research_categories', 'label' => ['en' => 'Toggle Status Research Categories', 'ar' => 'Toggle Status Research Categories'], 'description' => ['en' => 'Can toggle status of research categories', 'ar' => 'Can toggle status of research categories']],

            // Knowledge Articles
            ['name' => 'manage-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage Knowledge Articles', 'ar' => 'Manage Knowledge Articles'], 'description' => ['en' => 'Can manage knowledge articles', 'ar' => 'Can manage knowledge articles']],

            ['name' => 'manage-any-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage All Knowledge Articles', 'ar' => 'Manage All Knowledge Articles'], 'description' => ['en' => 'Manage Any Knowledge Articles', 'ar' => 'Manage Any Knowledge Articles']],

            ['name' => 'manage-own-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Manage Own Knowledge Articles', 'ar' => 'Manage Own Knowledge Articles'], 'description' => ['en' => 'Manage Limited Knowledge Articles that is created by own', 'ar' => 'Manage Limited Knowledge Articles that is created by own']],

            ['name' => 'view-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'View Knowledge Articles', 'ar' => 'View Knowledge Articles'], 'description' => ['en' => 'View Knowledge Articles', 'ar' => 'View Knowledge Articles']],

            ['name' => 'create-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Create Knowledge Articles', 'ar' => 'Create Knowledge Articles'], 'description' => ['en' => 'Can create knowledge articles', 'ar' => 'Can create knowledge articles']],

            ['name' => 'edit-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Edit Knowledge Articles', 'ar' => 'Edit Knowledge Articles'], 'description' => ['en' => 'Can edit knowledge articles', 'ar' => 'Can edit knowledge articles']],

            ['name' => 'delete-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Delete Knowledge Articles', 'ar' => 'Delete Knowledge Articles'], 'description' => ['en' => 'Can delete knowledge articles', 'ar' => 'Can delete knowledge articles']],

            ['name' => 'publish-knowledge-articles', 'module' => 'knowledge_articles', 'label' => ['en' => 'Publish Knowledge Articles', 'ar' => 'Publish Knowledge Articles'], 'description' => ['en' => 'Can publish knowledge articles', 'ar' => 'Can publish knowledge articles']],

            // Legal Precedents
            ['name' => 'manage-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage Legal Precedents', 'ar' => 'Manage Legal Precedents'], 'description' => ['en' => 'Can manage legal precedents', 'ar' => 'Can manage legal precedents']],

            ['name' => 'manage-any-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage All Legal Precedents', 'ar' => 'Manage All Legal Precedents'], 'description' => ['en' => 'Manage Any Legal Precedents', 'ar' => 'Manage Any Legal Precedents']],

            ['name' => 'manage-own-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Manage Own Legal Precedents', 'ar' => 'Manage Own Legal Precedents'], 'description' => ['en' => 'Manage Limited Legal Precedents that is created by own', 'ar' => 'Manage Limited Legal Precedents that is created by own']],

            ['name' => 'view-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'View Legal Precedents', 'ar' => 'View Legal Precedents'], 'description' => ['en' => 'View Legal Precedents', 'ar' => 'View Legal Precedents']],

            ['name' => 'create-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Create Legal Precedents', 'ar' => 'Create Legal Precedents'], 'description' => ['en' => 'Can create legal precedents', 'ar' => 'Can create legal precedents']],

            ['name' => 'edit-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Edit Legal Precedents', 'ar' => 'Edit Legal Precedents'], 'description' => ['en' => 'Can edit legal precedents', 'ar' => 'Can edit legal precedents']],

            ['name' => 'delete-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Delete Legal Precedents', 'ar' => 'Delete Legal Precedents'], 'description' => ['en' => 'Can delete legal precedents', 'ar' => 'Can delete legal precedents']],

            ['name' => 'toggle-status-legal-precedents', 'module' => 'legal_precedents', 'label' => ['en' => 'Toggle Status Legal Precedents', 'ar' => 'Toggle Status Legal Precedents'], 'description' => ['en' => 'Can toggle status of legal precedents', 'ar' => 'Can toggle status of legal precedents']],

            // Research Notes
            ['name' => 'manage-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage Research Notes', 'ar' => 'Manage Research Notes'], 'description' => ['en' => 'Can manage research notes', 'ar' => 'Can manage research notes']],

            ['name' => 'manage-any-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage All Research Notes', 'ar' => 'Manage All Research Notes'], 'description' => ['en' => 'Manage Any Research Notes', 'ar' => 'Manage Any Research Notes']],

            ['name' => 'manage-own-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Manage Own Research Notes', 'ar' => 'Manage Own Research Notes'], 'description' => ['en' => 'Manage Limited Research Notes that is created by own', 'ar' => 'Manage Limited Research Notes that is created by own']],

            ['name' => 'view-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'View Research Notes', 'ar' => 'View Research Notes'], 'description' => ['en' => 'View Research Notes', 'ar' => 'View Research Notes']],

            ['name' => 'create-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Create Research Notes', 'ar' => 'Create Research Notes'], 'description' => ['en' => 'Can create research notes', 'ar' => 'Can create research notes']],

            ['name' => 'edit-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Edit Research Notes', 'ar' => 'Edit Research Notes'], 'description' => ['en' => 'Can edit research notes', 'ar' => 'Can edit research notes']],

            ['name' => 'delete-research-notes', 'module' => 'research_notes', 'label' => ['en' => 'Delete Research Notes', 'ar' => 'Delete Research Notes'], 'description' => ['en' => 'Can delete research notes', 'ar' => 'Can delete research notes']],

            // Research Citations
            ['name' => 'manage-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage Research Citations', 'ar' => 'Manage Research Citations'], 'description' => ['en' => 'Can manage research citations', 'ar' => 'Can manage research citations']],

            ['name' => 'manage-any-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage All Research Citations', 'ar' => 'Manage All Research Citations'], 'description' => ['en' => 'Manage Any Research Citations', 'ar' => 'Manage Any Research Citations']],

            ['name' => 'manage-own-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Manage Own Research Citations', 'ar' => 'Manage Own Research Citations'], 'description' => ['en' => 'Manage Limited Research Citations that is created by own', 'ar' => 'Manage Limited Research Citations that is created by own']],

            ['name' => 'view-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'View Research Citations', 'ar' => 'View Research Citations'], 'description' => ['en' => 'View Research Citations', 'ar' => 'View Research Citations']],

            ['name' => 'create-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Create Research Citations', 'ar' => 'Create Research Citations'], 'description' => ['en' => 'Can create research citations', 'ar' => 'Can create research citations']],

            ['name' => 'edit-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Edit Research Citations', 'ar' => 'Edit Research Citations'], 'description' => ['en' => 'Can edit research citations', 'ar' => 'Can edit research citations']],

            ['name' => 'delete-research-citations', 'module' => 'research_citations', 'label' => ['en' => 'Delete Research Citations', 'ar' => 'Delete Research Citations'], 'description' => ['en' => 'Can delete research citations', 'ar' => 'Can delete research citations']],

            // Research Types
            ['name' => 'manage-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage Research Types', 'ar' => 'Manage Research Types'], 'description' => ['en' => 'Can manage research types', 'ar' => 'Can manage research types']],

            ['name' => 'manage-any-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage All Research Types', 'ar' => 'Manage All Research Types'], 'description' => ['en' => 'Manage Any Research Types', 'ar' => 'Manage Any Research Types']],

            ['name' => 'manage-own-research-types', 'module' => 'research_types', 'label' => ['en' => 'Manage Own Research Types', 'ar' => 'Manage Own Research Types'], 'description' => ['en' => 'Manage Limited Research Types that is created by own', 'ar' => 'Manage Limited Research Types that is created by own']],

            ['name' => 'view-research-types', 'module' => 'research_types', 'label' => ['en' => 'View Research Types', 'ar' => 'View Research Types'], 'description' => ['en' => 'View Research Types', 'ar' => 'View Research Types']],

            ['name' => 'create-research-types', 'module' => 'research_types', 'label' => ['en' => 'Create Research Types', 'ar' => 'Create Research Types'], 'description' => ['en' => 'Can create research types', 'ar' => 'Can create research types']],

            ['name' => 'edit-research-types', 'module' => 'research_types', 'label' => ['en' => 'Edit Research Types', 'ar' => 'Edit Research Types'], 'description' => ['en' => 'Can edit research types', 'ar' => 'Can edit research types']],

            ['name' => 'delete-research-types', 'module' => 'research_types', 'label' => ['en' => 'Delete Research Types', 'ar' => 'Delete Research Types'], 'description' => ['en' => 'Can delete research types', 'ar' => 'Can delete research types']],

            ['name' => 'toggle-status-research-types', 'module' => 'research_types', 'label' => ['en' => 'Toggle Status Research Types', 'ar' => 'Toggle Status Research Types'], 'description' => ['en' => 'Can toggle status of research types', 'ar' => 'Can toggle status of research types']],

            // Compliance Requirements
            ['name' => 'manage-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage Compliance Requirements', 'ar' => 'Manage Compliance Requirements'], 'description' => ['en' => 'Can manage compliance requirements', 'ar' => 'Can manage compliance requirements']],

            ['name' => 'manage-any-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage All Compliance Requirements', 'ar' => 'Manage All Compliance Requirements'], 'description' => ['en' => 'Manage Any Compliance Requirements', 'ar' => 'Manage Any Compliance Requirements']],

            ['name' => 'manage-own-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Manage Own Compliance Requirements', 'ar' => 'Manage Own Compliance Requirements'], 'description' => ['en' => 'Manage Limited Compliance Requirements that is created by own', 'ar' => 'Manage Limited Compliance Requirements that is created by own']],

            ['name' => 'view-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'View Compliance Requirements', 'ar' => 'View Compliance Requirements'], 'description' => ['en' => 'View Compliance Requirements', 'ar' => 'View Compliance Requirements']],

            ['name' => 'create-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Create Compliance Requirements', 'ar' => 'Create Compliance Requirements'], 'description' => ['en' => 'Can create compliance requirements', 'ar' => 'Can create compliance requirements']],

            ['name' => 'edit-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Edit Compliance Requirements', 'ar' => 'Edit Compliance Requirements'], 'description' => ['en' => 'Can edit compliance requirements', 'ar' => 'Can edit compliance requirements']],

            ['name' => 'delete-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Delete Compliance Requirements', 'ar' => 'Delete Compliance Requirements'], 'description' => ['en' => 'Can delete compliance requirements', 'ar' => 'Can delete compliance requirements']],

            ['name' => 'toggle-status-compliance-requirements', 'module' => 'compliance_requirements', 'label' => ['en' => 'Toggle Status Compliance Requirements', 'ar' => 'Toggle Status Compliance Requirements'], 'description' => ['en' => 'Can toggle status of compliance requirements', 'ar' => 'Can toggle status of compliance requirements']],

            // Compliance Categories
            ['name' => 'manage-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage Compliance Categories', 'ar' => 'Manage Compliance Categories'], 'description' => ['en' => 'Can manage compliance categories', 'ar' => 'Can manage compliance categories']],

            ['name' => 'manage-any-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage All Compliance Categories', 'ar' => 'Manage All Compliance Categories'], 'description' => ['en' => 'Manage Any Compliance Categories', 'ar' => 'Manage Any Compliance Categories']],

            ['name' => 'manage-own-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Manage Own Compliance Categories', 'ar' => 'Manage Own Compliance Categories'], 'description' => ['en' => 'Manage Limited Compliance Categories that is created by own', 'ar' => 'Manage Limited Compliance Categories that is created by own']],

            ['name' => 'view-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'View Compliance Categories', 'ar' => 'View Compliance Categories'], 'description' => ['en' => 'View Compliance Categories', 'ar' => 'View Compliance Categories']],

            ['name' => 'create-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Create Compliance Categories', 'ar' => 'Create Compliance Categories'], 'description' => ['en' => 'Can create compliance categories', 'ar' => 'Can create compliance categories']],

            ['name' => 'edit-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Edit Compliance Categories', 'ar' => 'Edit Compliance Categories'], 'description' => ['en' => 'Can edit compliance categories', 'ar' => 'Can edit compliance categories']],

            ['name' => 'delete-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Delete Compliance Categories', 'ar' => 'Delete Compliance Categories'], 'description' => ['en' => 'Can delete compliance categories', 'ar' => 'Can delete compliance categories']],

            ['name' => 'toggle-status-compliance-categories', 'module' => 'compliance_categories', 'label' => ['en' => 'Toggle Status Compliance Categories', 'ar' => 'Toggle Status Compliance Categories'], 'description' => ['en' => 'Can toggle status of compliance categories', 'ar' => 'Can toggle status of compliance categories']],

            // Compliance Frequencies
            ['name' => 'manage-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage Compliance Frequencies', 'ar' => 'Manage Compliance Frequencies'], 'description' => ['en' => 'Can manage compliance frequencies', 'ar' => 'Can manage compliance frequencies']],

            ['name' => 'manage-any-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage All Compliance Frequencies', 'ar' => 'Manage All Compliance Frequencies'], 'description' => ['en' => 'Manage Any Compliance Frequencies', 'ar' => 'Manage Any Compliance Frequencies']],

            ['name' => 'manage-own-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Manage Own Compliance Frequencies', 'ar' => 'Manage Own Compliance Frequencies'], 'description' => ['en' => 'Manage Limited Compliance Frequencies that is created by own', 'ar' => 'Manage Limited Compliance Frequencies that is created by own']],

            ['name' => 'view-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'View Compliance Frequencies', 'ar' => 'View Compliance Frequencies'], 'description' => ['en' => 'View Compliance Frequencies', 'ar' => 'View Compliance Frequencies']],

            ['name' => 'create-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Create Compliance Frequencies', 'ar' => 'Create Compliance Frequencies'], 'description' => ['en' => 'Can create compliance frequencies', 'ar' => 'Can create compliance frequencies']],

            ['name' => 'edit-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Edit Compliance Frequencies', 'ar' => 'Edit Compliance Frequencies'], 'description' => ['en' => 'Can edit compliance frequencies', 'ar' => 'Can edit compliance frequencies']],

            ['name' => 'delete-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Delete Compliance Frequencies', 'ar' => 'Delete Compliance Frequencies'], 'description' => ['en' => 'Can delete compliance frequencies', 'ar' => 'Can delete compliance frequencies']],

            ['name' => 'toggle-status-compliance-frequencies', 'module' => 'compliance_frequencies', 'label' => ['en' => 'Toggle Status Compliance Frequencies', 'ar' => 'Toggle Status Compliance Frequencies'], 'description' => ['en' => 'Can toggle status of compliance frequencies', 'ar' => 'Can toggle status of compliance frequencies']],

            // Professional Licenses
            ['name' => 'manage-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage Professional Licenses', 'ar' => 'Manage Professional Licenses'], 'description' => ['en' => 'Can manage professional licenses', 'ar' => 'Can manage professional licenses']],

            ['name' => 'manage-any-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage All Professional Licenses', 'ar' => 'Manage All Professional Licenses'], 'description' => ['en' => 'Manage Any Professional Licenses', 'ar' => 'Manage Any Professional Licenses']],

            ['name' => 'manage-own-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Manage Own Professional Licenses', 'ar' => 'Manage Own Professional Licenses'], 'description' => ['en' => 'Manage Limited Professional Licenses that is created by own', 'ar' => 'Manage Limited Professional Licenses that is created by own']],

            ['name' => 'view-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'View Professional Licenses', 'ar' => 'View Professional Licenses'], 'description' => ['en' => 'View Professional Licenses', 'ar' => 'View Professional Licenses']],

            ['name' => 'create-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Create Professional Licenses', 'ar' => 'Create Professional Licenses'], 'description' => ['en' => 'Can create professional licenses', 'ar' => 'Can create professional licenses']],

            ['name' => 'edit-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Edit Professional Licenses', 'ar' => 'Edit Professional Licenses'], 'description' => ['en' => 'Can edit professional licenses', 'ar' => 'Can edit professional licenses']],

            ['name' => 'delete-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Delete Professional Licenses', 'ar' => 'Delete Professional Licenses'], 'description' => ['en' => 'Can delete professional licenses', 'ar' => 'Can delete professional licenses']],

            ['name' => 'toggle-status-professional-licenses', 'module' => 'professional_licenses', 'label' => ['en' => 'Toggle Status Professional Licenses', 'ar' => 'Toggle Status Professional Licenses'], 'description' => ['en' => 'Can toggle status of professional licenses', 'ar' => 'Can toggle status of professional licenses']],

            // Regulatory Bodies
            ['name' => 'manage-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage Regulatory Bodies', 'ar' => 'Manage Regulatory Bodies'], 'description' => ['en' => 'Can manage regulatory bodies', 'ar' => 'Can manage regulatory bodies']],

            ['name' => 'manage-any-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage All Regulatory Bodies', 'ar' => 'Manage All Regulatory Bodies'], 'description' => ['en' => 'Manage Any Regulatory Bodies', 'ar' => 'Manage Any Regulatory Bodies']],

            ['name' => 'manage-own-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Manage Own Regulatory Bodies', 'ar' => 'Manage Own Regulatory Bodies'], 'description' => ['en' => 'Manage Limited Regulatory Bodies that is created by own', 'ar' => 'Manage Limited Regulatory Bodies that is created by own']],

            ['name' => 'view-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'View Regulatory Bodies', 'ar' => 'View Regulatory Bodies'], 'description' => ['en' => 'View Regulatory Bodies', 'ar' => 'View Regulatory Bodies']],

            ['name' => 'create-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Create Regulatory Bodies', 'ar' => 'Create Regulatory Bodies'], 'description' => ['en' => 'Can create regulatory bodies', 'ar' => 'Can create regulatory bodies']],

            ['name' => 'edit-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Edit Regulatory Bodies', 'ar' => 'Edit Regulatory Bodies'], 'description' => ['en' => 'Can edit regulatory bodies', 'ar' => 'Can edit regulatory bodies']],

            ['name' => 'delete-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Delete Regulatory Bodies', 'ar' => 'Delete Regulatory Bodies'], 'description' => ['en' => 'Can delete regulatory bodies', 'ar' => 'Can delete regulatory bodies']],

            ['name' => 'toggle-status-regulatory-bodies', 'module' => 'regulatory_bodies', 'label' => ['en' => 'Toggle Status Regulatory Bodies', 'ar' => 'Toggle Status Regulatory Bodies'], 'description' => ['en' => 'Can toggle status of regulatory bodies', 'ar' => 'Can toggle status of regulatory bodies']],

            // Compliance Policies
            ['name' => 'manage-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage Compliance Policies', 'ar' => 'Manage Compliance Policies'], 'description' => ['en' => 'Can manage compliance policies', 'ar' => 'Can manage compliance policies']],

            ['name' => 'manage-any-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage All Compliance Policies', 'ar' => 'Manage All Compliance Policies'], 'description' => ['en' => 'Manage Any Compliance Policies', 'ar' => 'Manage Any Compliance Policies']],

            ['name' => 'manage-own-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Manage Own Compliance Policies', 'ar' => 'Manage Own Compliance Policies'], 'description' => ['en' => 'Manage Limited Compliance Policies that is created by own', 'ar' => 'Manage Limited Compliance Policies that is created by own']],

            ['name' => 'view-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'View Compliance Policies', 'ar' => 'View Compliance Policies'], 'description' => ['en' => 'View Compliance Policies', 'ar' => 'View Compliance Policies']],

            ['name' => 'create-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Create Compliance Policies', 'ar' => 'Create Compliance Policies'], 'description' => ['en' => 'Can create compliance policies', 'ar' => 'Can create compliance policies']],

            ['name' => 'edit-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Edit Compliance Policies', 'ar' => 'Edit Compliance Policies'], 'description' => ['en' => 'Can edit compliance policies', 'ar' => 'Can edit compliance policies']],

            ['name' => 'delete-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Delete Compliance Policies', 'ar' => 'Delete Compliance Policies'], 'description' => ['en' => 'Can delete compliance policies', 'ar' => 'Can delete compliance policies']],

            ['name' => 'toggle-status-compliance-policies', 'module' => 'compliance_policies', 'label' => ['en' => 'Toggle Status Compliance Policies', 'ar' => 'Toggle Status Compliance Policies'], 'description' => ['en' => 'Can toggle status of compliance policies', 'ar' => 'Can toggle status of compliance policies']],

            // CLE Tracking
            ['name' => 'manage-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage CLE Tracking', 'ar' => 'Manage CLE Tracking'], 'description' => ['en' => 'Can manage CLE tracking records', 'ar' => 'Can manage CLE tracking records']],

            ['name' => 'manage-any-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage All CLE Tracking', 'ar' => 'Manage All CLE Tracking'], 'description' => ['en' => 'Manage Any CLE Tracking', 'ar' => 'Manage Any CLE Tracking']],

            ['name' => 'manage-own-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Manage Own CLE Tracking', 'ar' => 'Manage Own CLE Tracking'], 'description' => ['en' => 'Manage Limited CLE Tracking that is created by own', 'ar' => 'Manage Limited CLE Tracking that is created by own']],

            ['name' => 'view-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'View CLE Tracking', 'ar' => 'View CLE Tracking'], 'description' => ['en' => 'View CLE Tracking', 'ar' => 'View CLE Tracking']],

            ['name' => 'create-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Create CLE Tracking', 'ar' => 'Create CLE Tracking'], 'description' => ['en' => 'Can create CLE tracking records', 'ar' => 'Can create CLE tracking records']],

            ['name' => 'edit-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Edit CLE Tracking', 'ar' => 'Edit CLE Tracking'], 'description' => ['en' => 'Can edit CLE tracking records', 'ar' => 'Can edit CLE tracking records']],

            ['name' => 'delete-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Delete CLE Tracking', 'ar' => 'Delete CLE Tracking'], 'description' => ['en' => 'Can delete CLE tracking records', 'ar' => 'Can delete CLE tracking records']],

            ['name' => 'download-cle-tracking', 'module' => 'cle_tracking', 'label' => ['en' => 'Download CLE Tracking', 'ar' => 'Download CLE Tracking'], 'description' => ['en' => 'Can download CLE certificate files', 'ar' => 'Can download CLE certificate files']],

            // Risk Categories
            ['name' => 'manage-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage Risk Categories', 'ar' => 'Manage Risk Categories'], 'description' => ['en' => 'Can manage risk categories', 'ar' => 'Can manage risk categories']],

            ['name' => 'manage-any-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage All Risk Categories', 'ar' => 'Manage All Risk Categories'], 'description' => ['en' => 'Manage Any Risk Categories', 'ar' => 'Manage Any Risk Categories']],

            ['name' => 'manage-own-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Manage Own Risk Categories', 'ar' => 'Manage Own Risk Categories'], 'description' => ['en' => 'Manage Limited Risk Categories that is created by own', 'ar' => 'Manage Limited Risk Categories that is created by own']],

            ['name' => 'view-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'View Risk Categories', 'ar' => 'View Risk Categories'], 'description' => ['en' => 'View Risk Categories', 'ar' => 'View Risk Categories']],

            ['name' => 'create-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Create Risk Categories', 'ar' => 'Create Risk Categories'], 'description' => ['en' => 'Can create risk categories', 'ar' => 'Can create risk categories']],

            ['name' => 'edit-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Edit Risk Categories', 'ar' => 'Edit Risk Categories'], 'description' => ['en' => 'Can edit risk categories', 'ar' => 'Can edit risk categories']],

            ['name' => 'delete-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Delete Risk Categories', 'ar' => 'Delete Risk Categories'], 'description' => ['en' => 'Can delete risk categories', 'ar' => 'Can delete risk categories']],

            ['name' => 'toggle-status-risk-categories', 'module' => 'risk_categories', 'label' => ['en' => 'Toggle Status Risk Categories', 'ar' => 'Toggle Status Risk Categories'], 'description' => ['en' => 'Can toggle status of risk categories', 'ar' => 'Can toggle status of risk categories']],

            // Risk Assessments
            ['name' => 'manage-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage Risk Assessments', 'ar' => 'Manage Risk Assessments'], 'description' => ['en' => 'Can manage risk assessments', 'ar' => 'Can manage risk assessments']],

            ['name' => 'manage-any-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage All Risk Assessments', 'ar' => 'Manage All Risk Assessments'], 'description' => ['en' => 'Manage Any Risk Assessments', 'ar' => 'Manage Any Risk Assessments']],

            ['name' => 'manage-own-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Manage Own Risk Assessments', 'ar' => 'Manage Own Risk Assessments'], 'description' => ['en' => 'Manage Limited Risk Assessments that is created by own', 'ar' => 'Manage Limited Risk Assessments that is created by own']],

            ['name' => 'view-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'View Risk Assessments', 'ar' => 'View Risk Assessments'], 'description' => ['en' => 'View Risk Assessments', 'ar' => 'View Risk Assessments']],

            ['name' => 'create-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Create Risk Assessments', 'ar' => 'Create Risk Assessments'], 'description' => ['en' => 'Can create risk assessments', 'ar' => 'Can create risk assessments']],

            ['name' => 'edit-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Edit Risk Assessments', 'ar' => 'Edit Risk Assessments'], 'description' => ['en' => 'Can edit risk assessments', 'ar' => 'Can edit risk assessments']],

            ['name' => 'delete-risk-assessments', 'module' => 'risk_assessments', 'label' => ['en' => 'Delete Risk Assessments', 'ar' => 'Delete Risk Assessments'], 'description' => ['en' => 'Can delete risk assessments', 'ar' => 'Can delete risk assessments']],

            // Audit Types
            ['name' => 'manage-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage Audit Types', 'ar' => 'Manage Audit Types'], 'description' => ['en' => 'Can manage audit types', 'ar' => 'Can manage audit types']],

            ['name' => 'manage-any-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage All Audit Types', 'ar' => 'Manage All Audit Types'], 'description' => ['en' => 'Manage Any Audit Types', 'ar' => 'Manage Any Audit Types']],

            ['name' => 'manage-own-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Manage Own Audit Types', 'ar' => 'Manage Own Audit Types'], 'description' => ['en' => 'Manage Limited Audit Types that is created by own', 'ar' => 'Manage Limited Audit Types that is created by own']],

            ['name' => 'view-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'View Audit Types', 'ar' => 'View Audit Types'], 'description' => ['en' => 'View Audit Types', 'ar' => 'View Audit Types']],

            ['name' => 'create-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Create Audit Types', 'ar' => 'Create Audit Types'], 'description' => ['en' => 'Can create audit types', 'ar' => 'Can create audit types']],

            ['name' => 'edit-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Edit Audit Types', 'ar' => 'Edit Audit Types'], 'description' => ['en' => 'Can edit audit types', 'ar' => 'Can edit audit types']],

            ['name' => 'delete-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Delete Audit Types', 'ar' => 'Delete Audit Types'], 'description' => ['en' => 'Can delete audit types', 'ar' => 'Can delete audit types']],

            ['name' => 'toggle-status-audit-types', 'module' => 'audit_types', 'label' => ['en' => 'Toggle Status Audit Types', 'ar' => 'Toggle Status Audit Types'], 'description' => ['en' => 'Can toggle status of audit types', 'ar' => 'Can toggle status of audit types']],

            // Compliance Audits
            ['name' => 'manage-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage Compliance Audits', 'ar' => 'Manage Compliance Audits'], 'description' => ['en' => 'Can manage compliance audits', 'ar' => 'Can manage compliance audits']],

            ['name' => 'manage-any-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage All Compliance Audits', 'ar' => 'Manage All Compliance Audits'], 'description' => ['en' => 'Manage Any Compliance Audits', 'ar' => 'Manage Any Compliance Audits']],

            ['name' => 'manage-own-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Manage Own Compliance Audits', 'ar' => 'Manage Own Compliance Audits'], 'description' => ['en' => 'Manage Limited Compliance Audits that is created by own', 'ar' => 'Manage Limited Compliance Audits that is created by own']],

            ['name' => 'view-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'View Compliance Audits', 'ar' => 'View Compliance Audits'], 'description' => ['en' => 'View Compliance Audits', 'ar' => 'View Compliance Audits']],

            ['name' => 'create-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Create Compliance Audits', 'ar' => 'Create Compliance Audits'], 'description' => ['en' => 'Can create compliance audits', 'ar' => 'Can create compliance audits']],

            ['name' => 'edit-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Edit Compliance Audits', 'ar' => 'Edit Compliance Audits'], 'description' => ['en' => 'Can edit compliance audits', 'ar' => 'Can edit compliance audits']],

            ['name' => 'delete-compliance-audits', 'module' => 'compliance_audits', 'label' => ['en' => 'Delete Compliance Audits', 'ar' => 'Delete Compliance Audits'], 'description' => ['en' => 'Can delete compliance audits', 'ar' => 'Can delete compliance audits']],

            // Time Entries
            ['name' => 'manage-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage Time Entries', 'ar' => 'Manage Time Entries'], 'description' => ['en' => 'Can manage time entries', 'ar' => 'Can manage time entries']],

            ['name' => 'manage-any-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage All Time Entries', 'ar' => 'Manage All Time Entries'], 'description' => ['en' => 'Manage Any Time Entries', 'ar' => 'Manage Any Time Entries']],

            ['name' => 'manage-own-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Manage Own Time Entries', 'ar' => 'Manage Own Time Entries'], 'description' => ['en' => 'Manage Limited Time Entries that is created by own', 'ar' => 'Manage Limited Time Entries that is created by own']],

            ['name' => 'view-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'View Time Entries', 'ar' => 'View Time Entries'], 'description' => ['en' => 'View Time Entries', 'ar' => 'View Time Entries']],

            ['name' => 'create-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Create Time Entries', 'ar' => 'Create Time Entries'], 'description' => ['en' => 'Can create time entries', 'ar' => 'Can create time entries']],

            ['name' => 'edit-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Edit Time Entries', 'ar' => 'Edit Time Entries'], 'description' => ['en' => 'Can edit time entries', 'ar' => 'Can edit time entries']],

            ['name' => 'delete-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Delete Time Entries', 'ar' => 'Delete Time Entries'], 'description' => ['en' => 'Can delete time entries', 'ar' => 'Can delete time entries']],

            ['name' => 'approve-time-entries', 'module' => 'time_entries', 'label' => ['en' => 'Approve Time Entries', 'ar' => 'Approve Time Entries'], 'description' => ['en' => 'Can approve time entries', 'ar' => 'Can approve time entries']],

            ['name' => 'start-timer', 'module' => 'time_entries', 'label' => ['en' => 'Start Timer', 'ar' => 'Start Timer'], 'description' => ['en' => 'Can start time tracking timer', 'ar' => 'Can start time tracking timer']],

            ['name' => 'stop-timer', 'module' => 'time_entries', 'label' => ['en' => 'Stop Timer', 'ar' => 'Stop Timer'], 'description' => ['en' => 'Can stop time tracking timer', 'ar' => 'Can stop time tracking timer']],

            // Billing Rates
            ['name' => 'manage-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage Billing Rates', 'ar' => 'Manage Billing Rates'], 'description' => ['en' => 'Can manage billing rates', 'ar' => 'Can manage billing rates']],

            ['name' => 'manage-any-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage All Billing Rates', 'ar' => 'Manage All Billing Rates'], 'description' => ['en' => 'Manage Any Billing Rates', 'ar' => 'Manage Any Billing Rates']],

            ['name' => 'manage-own-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Manage Own Billing Rates', 'ar' => 'Manage Own Billing Rates'], 'description' => ['en' => 'Manage Limited Billing Rates that is created by own', 'ar' => 'Manage Limited Billing Rates that is created by own']],

            ['name' => 'view-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'View Billing Rates', 'ar' => 'View Billing Rates'], 'description' => ['en' => 'View Billing Rates', 'ar' => 'View Billing Rates']],

            ['name' => 'create-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Create Billing Rates', 'ar' => 'Create Billing Rates'], 'description' => ['en' => 'Can create billing rates', 'ar' => 'Can create billing rates']],

            ['name' => 'edit-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Edit Billing Rates', 'ar' => 'Edit Billing Rates'], 'description' => ['en' => 'Can edit billing rates', 'ar' => 'Can edit billing rates']],

            ['name' => 'delete-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Delete Billing Rates', 'ar' => 'Delete Billing Rates'], 'description' => ['en' => 'Can delete billing rates', 'ar' => 'Can delete billing rates']],

            ['name' => 'toggle-status-billing-rates', 'module' => 'billing_rates', 'label' => ['en' => 'Toggle Status Billing Rates', 'ar' => 'Toggle Status Billing Rates'], 'description' => ['en' => 'Can toggle status of billing rates', 'ar' => 'Can toggle status of billing rates']],

            // Fee Types
            ['name' => 'manage-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage Fee Types', 'ar' => 'Manage Fee Types'], 'description' => ['en' => 'Can manage fee types', 'ar' => 'Can manage fee types']],

            ['name' => 'manage-any-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage All Fee Types', 'ar' => 'Manage All Fee Types'], 'description' => ['en' => 'Manage Any Fee Types', 'ar' => 'Manage Any Fee Types']],

            ['name' => 'manage-own-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Manage Own Fee Types', 'ar' => 'Manage Own Fee Types'], 'description' => ['en' => 'Manage Limited Fee Types that is created by own', 'ar' => 'Manage Limited Fee Types that is created by own']],

            ['name' => 'view-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'View Fee Types', 'ar' => 'View Fee Types'], 'description' => ['en' => 'View Fee Types', 'ar' => 'View Fee Types']],

            ['name' => 'create-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Create Fee Types', 'ar' => 'Create Fee Types'], 'description' => ['en' => 'Can create fee types', 'ar' => 'Can create fee types']],

            ['name' => 'edit-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Edit Fee Types', 'ar' => 'Edit Fee Types'], 'description' => ['en' => 'Can edit fee types', 'ar' => 'Can edit fee types']],

            ['name' => 'delete-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Delete Fee Types', 'ar' => 'Delete Fee Types'], 'description' => ['en' => 'Can delete fee types', 'ar' => 'Can delete fee types']],

            ['name' => 'toggle-status-fee-types', 'module' => 'fee_types', 'label' => ['en' => 'Toggle Status Fee Types', 'ar' => 'Toggle Status Fee Types'], 'description' => ['en' => 'Can toggle status of fee types', 'ar' => 'Can toggle status of fee types']],

            // Fee Structures
            ['name' => 'manage-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage Fee Structures', 'ar' => 'Manage Fee Structures'], 'description' => ['en' => 'Can manage fee structures', 'ar' => 'Can manage fee structures']],

            ['name' => 'manage-any-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage All Fee Structures', 'ar' => 'Manage All Fee Structures'], 'description' => ['en' => 'Manage Any Fee Structures', 'ar' => 'Manage Any Fee Structures']],

            ['name' => 'manage-own-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Manage Own Fee Structures', 'ar' => 'Manage Own Fee Structures'], 'description' => ['en' => 'Manage Limited Fee Structures that is created by own', 'ar' => 'Manage Limited Fee Structures that is created by own']],

            ['name' => 'view-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'View Fee Structures', 'ar' => 'View Fee Structures'], 'description' => ['en' => 'View Fee Structures', 'ar' => 'View Fee Structures']],

            ['name' => 'create-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Create Fee Structures', 'ar' => 'Create Fee Structures'], 'description' => ['en' => 'Can create fee structures', 'ar' => 'Can create fee structures']],

            ['name' => 'edit-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Edit Fee Structures', 'ar' => 'Edit Fee Structures'], 'description' => ['en' => 'Can edit fee structures', 'ar' => 'Can edit fee structures']],

            ['name' => 'delete-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Delete Fee Structures', 'ar' => 'Delete Fee Structures'], 'description' => ['en' => 'Can delete fee structures', 'ar' => 'Can delete fee structures']],

            ['name' => 'toggle-status-fee-structures', 'module' => 'fee_structures', 'label' => ['en' => 'Toggle Status Fee Structures', 'ar' => 'Toggle Status Fee Structures'], 'description' => ['en' => 'Can toggle status of fee structures', 'ar' => 'Can toggle status of fee structures']],

            // Expenses
            ['name' => 'manage-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage Expenses', 'ar' => 'Manage Expenses'], 'description' => ['en' => 'Can manage expenses', 'ar' => 'Can manage expenses']],

            ['name' => 'manage-any-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage All Expenses', 'ar' => 'Manage All Expenses'], 'description' => ['en' => 'Manage Any Expenses', 'ar' => 'Manage Any Expenses']],

            ['name' => 'manage-own-expenses', 'module' => 'expenses', 'label' => ['en' => 'Manage Own Expenses', 'ar' => 'Manage Own Expenses'], 'description' => ['en' => 'Manage Limited Expenses that is created by own', 'ar' => 'Manage Limited Expenses that is created by own']],

            ['name' => 'view-expenses', 'module' => 'expenses', 'label' => ['en' => 'View Expenses', 'ar' => 'View Expenses'], 'description' => ['en' => 'View Expenses', 'ar' => 'View Expenses']],

            ['name' => 'create-expenses', 'module' => 'expenses', 'label' => ['en' => 'Create Expenses', 'ar' => 'Create Expenses'], 'description' => ['en' => 'Can create expenses', 'ar' => 'Can create expenses']],

            ['name' => 'edit-expenses', 'module' => 'expenses', 'label' => ['en' => 'Edit Expenses', 'ar' => 'Edit Expenses'], 'description' => ['en' => 'Can edit expenses', 'ar' => 'Can edit expenses']],

            ['name' => 'delete-expenses', 'module' => 'expenses', 'label' => ['en' => 'Delete Expenses', 'ar' => 'Delete Expenses'], 'description' => ['en' => 'Can delete expenses', 'ar' => 'Can delete expenses']],

            ['name' => 'approve-expenses', 'module' => 'expenses', 'label' => ['en' => 'Approve Expenses', 'ar' => 'Approve Expenses'], 'description' => ['en' => 'Can approve expenses', 'ar' => 'Can approve expenses']],

            // Expense Categories
            ['name' => 'manage-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage Expense Categories', 'ar' => 'Manage Expense Categories'], 'description' => ['en' => 'Can manage expense categories', 'ar' => 'Can manage expense categories']],

            ['name' => 'manage-any-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage All Expense Categories', 'ar' => 'Manage All Expense Categories'], 'description' => ['en' => 'Manage Any Expense Categories', 'ar' => 'Manage Any Expense Categories']],

            ['name' => 'manage-own-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Manage Own Expense Categories', 'ar' => 'Manage Own Expense Categories'], 'description' => ['en' => 'Manage Limited Expense Categories that is created by own', 'ar' => 'Manage Limited Expense Categories that is created by own']],

            ['name' => 'view-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'View Expense Categories', 'ar' => 'View Expense Categories'], 'description' => ['en' => 'View Expense Categories', 'ar' => 'View Expense Categories']],

            ['name' => 'create-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Create Expense Categories', 'ar' => 'Create Expense Categories'], 'description' => ['en' => 'Can create expense categories', 'ar' => 'Can create expense categories']],

            ['name' => 'edit-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Edit Expense Categories', 'ar' => 'Edit Expense Categories'], 'description' => ['en' => 'Can edit expense categories', 'ar' => 'Can edit expense categories']],

            ['name' => 'delete-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Delete Expense Categories', 'ar' => 'Delete Expense Categories'], 'description' => ['en' => 'Can delete expense categories', 'ar' => 'Can delete expense categories']],

            ['name' => 'toggle-status-expense-categories', 'module' => 'expense_categories', 'label' => ['en' => 'Toggle Status Expense Categories', 'ar' => 'Toggle Status Expense Categories'], 'description' => ['en' => 'Can toggle status of expense categories', 'ar' => 'Can toggle status of expense categories']],

            // Invoices
            ['name' => 'manage-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage Invoices', 'ar' => 'Manage Invoices'], 'description' => ['en' => 'Can manage invoices', 'ar' => 'Can manage invoices']],

            ['name' => 'manage-any-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage All Invoices', 'ar' => 'Manage All Invoices'], 'description' => ['en' => 'Manage Any Invoices', 'ar' => 'Manage Any Invoices']],

            ['name' => 'manage-own-invoices', 'module' => 'invoices', 'label' => ['en' => 'Manage Own Invoices', 'ar' => 'Manage Own Invoices'], 'description' => ['en' => 'Manage Limited Invoices that is created by own', 'ar' => 'Manage Limited Invoices that is created by own']],

            ['name' => 'view-invoices', 'module' => 'invoices', 'label' => ['en' => 'View Invoices', 'ar' => 'View Invoices'], 'description' => ['en' => 'View Invoices', 'ar' => 'View Invoices']],

            ['name' => 'create-invoices', 'module' => 'invoices', 'label' => ['en' => 'Create Invoices', 'ar' => 'Create Invoices'], 'description' => ['en' => 'Can create invoices', 'ar' => 'Can create invoices']],

            ['name' => 'edit-invoices', 'module' => 'invoices', 'label' => ['en' => 'Edit Invoices', 'ar' => 'Edit Invoices'], 'description' => ['en' => 'Can edit invoices', 'ar' => 'Can edit invoices']],

            ['name' => 'delete-invoices', 'module' => 'invoices', 'label' => ['en' => 'Delete Invoices', 'ar' => 'Delete Invoices'], 'description' => ['en' => 'Can delete invoices', 'ar' => 'Can delete invoices']],

            ['name' => 'send-invoices', 'module' => 'invoices', 'label' => ['en' => 'Send Invoices', 'ar' => 'Send Invoices'], 'description' => ['en' => 'Can send invoices to clients', 'ar' => 'Can send invoices to clients']],

            // Payments
            ['name' => 'manage-payments', 'module' => 'payments', 'label' => ['en' => 'Manage Payments', 'ar' => 'Manage Payments'], 'description' => ['en' => 'Can manage payments', 'ar' => 'Can manage payments']],

            ['name' => 'manage-any-payments', 'module' => 'payments', 'label' => ['en' => 'Manage All Payments', 'ar' => 'Manage All Payments'], 'description' => ['en' => 'Manage Any Payments', 'ar' => 'Manage Any Payments']],

            ['name' => 'manage-own-payments', 'module' => 'payments', 'label' => ['en' => 'Manage Own Payments', 'ar' => 'Manage Own Payments'], 'description' => ['en' => 'Manage Limited Payments that is created by own', 'ar' => 'Manage Limited Payments that is created by own']],

            ['name' => 'view-payments', 'module' => 'payments', 'label' => ['en' => 'View Payments', 'ar' => 'View Payments'], 'description' => ['en' => 'View Payments', 'ar' => 'View Payments']],

            ['name' => 'create-payments', 'module' => 'payments', 'label' => ['en' => 'Create Payments', 'ar' => 'Create Payments'], 'description' => ['en' => 'Can create payments', 'ar' => 'Can create payments']],

            ['name' => 'edit-payments', 'module' => 'payments', 'label' => ['en' => 'Edit Payments', 'ar' => 'Edit Payments'], 'description' => ['en' => 'Can edit payments', 'ar' => 'Can edit payments']],

            ['name' => 'delete-payments', 'module' => 'payments', 'label' => ['en' => 'Delete Payments', 'ar' => 'Delete Payments'], 'description' => ['en' => 'Can delete payments', 'ar' => 'Can delete payments']],

            ['name' => 'approve-payments', 'module' => 'payments', 'label' => ['en' => 'Approve Payments', 'ar' => 'Approve Payments'], 'description' => ['en' => 'Can approve bank transfer payments', 'ar' => 'Can approve bank transfer payments']],

            ['name' => 'reject-payments', 'module' => 'payments', 'label' => ['en' => 'Reject Payments', 'ar' => 'Reject Payments'], 'description' => ['en' => 'Can reject bank transfer payments', 'ar' => 'Can reject bank transfer payments']],

            // Task Management
            ['name' => 'manage-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage Tasks', 'ar' => 'Manage Tasks'], 'description' => ['en' => 'Can manage tasks', 'ar' => 'Can manage tasks']],

            ['name' => 'manage-any-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage All Tasks', 'ar' => 'Manage All Tasks'], 'description' => ['en' => 'Manage Any Tasks', 'ar' => 'Manage Any Tasks']],

            ['name' => 'manage-own-tasks', 'module' => 'tasks', 'label' => ['en' => 'Manage Own Tasks', 'ar' => 'Manage Own Tasks'], 'description' => ['en' => 'Manage Limited Tasks that is created by own', 'ar' => 'Manage Limited Tasks that is created by own']],

            ['name' => 'view-tasks', 'module' => 'tasks', 'label' => ['en' => 'View Tasks', 'ar' => 'View Tasks'], 'description' => ['en' => 'View Tasks', 'ar' => 'View Tasks']],

            ['name' => 'create-tasks', 'module' => 'tasks', 'label' => ['en' => 'Create Tasks', 'ar' => 'Create Tasks'], 'description' => ['en' => 'Can create tasks', 'ar' => 'Can create tasks']],

            ['name' => 'edit-tasks', 'module' => 'tasks', 'label' => ['en' => 'Edit Tasks', 'ar' => 'Edit Tasks'], 'description' => ['en' => 'Can edit tasks', 'ar' => 'Can edit tasks']],

            ['name' => 'delete-tasks', 'module' => 'tasks', 'label' => ['en' => 'Delete Tasks', 'ar' => 'Delete Tasks'], 'description' => ['en' => 'Can delete tasks', 'ar' => 'Can delete tasks']],

            ['name' => 'assign-tasks', 'module' => 'tasks', 'label' => ['en' => 'Assign Tasks', 'ar' => 'Assign Tasks'], 'description' => ['en' => 'Can assign tasks to users', 'ar' => 'Can assign tasks to users']],

            ['name' => 'toggle-status-tasks', 'module' => 'tasks', 'label' => ['en' => 'Toggle Status Tasks', 'ar' => 'Toggle Status Tasks'], 'description' => ['en' => 'Can toggle status of tasks', 'ar' => 'Can toggle status of tasks']],

            // Task Types
            ['name' => 'manage-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage Task Types', 'ar' => 'Manage Task Types'], 'description' => ['en' => 'Can manage task types', 'ar' => 'Can manage task types']],

            ['name' => 'manage-any-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage All Task Types', 'ar' => 'Manage All Task Types'], 'description' => ['en' => 'Manage Any Task Types', 'ar' => 'Manage Any Task Types']],

            ['name' => 'manage-own-task-types', 'module' => 'task_types', 'label' => ['en' => 'Manage Own Task Types', 'ar' => 'Manage Own Task Types'], 'description' => ['en' => 'Manage Limited Task Types that is created by own', 'ar' => 'Manage Limited Task Types that is created by own']],

            ['name' => 'view-task-types', 'module' => 'task_types', 'label' => ['en' => 'View Task Types', 'ar' => 'View Task Types'], 'description' => ['en' => 'View Task Types', 'ar' => 'View Task Types']],

            ['name' => 'create-task-types', 'module' => 'task_types', 'label' => ['en' => 'Create Task Types', 'ar' => 'Create Task Types'], 'description' => ['en' => 'Can create task types', 'ar' => 'Can create task types']],

            ['name' => 'edit-task-types', 'module' => 'task_types', 'label' => ['en' => 'Edit Task Types', 'ar' => 'Edit Task Types'], 'description' => ['en' => 'Can edit task types', 'ar' => 'Can edit task types']],

            ['name' => 'delete-task-types', 'module' => 'task_types', 'label' => ['en' => 'Delete Task Types', 'ar' => 'Delete Task Types'], 'description' => ['en' => 'Can delete task types', 'ar' => 'Can delete task types']],

            ['name' => 'toggle-status-task-types', 'module' => 'task_types', 'label' => ['en' => 'Toggle Status Task Types', 'ar' => 'Toggle Status Task Types'], 'description' => ['en' => 'Can toggle status of task types', 'ar' => 'Can toggle status of task types']],

            // Task Statuses
            ['name' => 'manage-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage Task Statuses', 'ar' => 'Manage Task Statuses'], 'description' => ['en' => 'Can manage task statuses', 'ar' => 'Can manage task statuses']],

            ['name' => 'manage-any-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage All Task Statuses', 'ar' => 'Manage All Task Statuses'], 'description' => ['en' => 'Manage Any Task Statuses', 'ar' => 'Manage Any Task Statuses']],

            ['name' => 'manage-own-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Manage Own Task Statuses', 'ar' => 'Manage Own Task Statuses'], 'description' => ['en' => 'Manage Limited Task Statuses that is created by own', 'ar' => 'Manage Limited Task Statuses that is created by own']],

            ['name' => 'view-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'View Task Statuses', 'ar' => 'View Task Statuses'], 'description' => ['en' => 'View Task Statuses', 'ar' => 'View Task Statuses']],

            ['name' => 'create-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Create Task Statuses', 'ar' => 'Create Task Statuses'], 'description' => ['en' => 'Can create task statuses', 'ar' => 'Can create task statuses']],

            ['name' => 'edit-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Edit Task Statuses', 'ar' => 'Edit Task Statuses'], 'description' => ['en' => 'Can edit task statuses', 'ar' => 'Can edit task statuses']],

            ['name' => 'delete-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Delete Task Statuses', 'ar' => 'Delete Task Statuses'], 'description' => ['en' => 'Can delete task statuses', 'ar' => 'Can delete task statuses']],

            ['name' => 'toggle-status-task-statuses', 'module' => 'task_statuses', 'label' => ['en' => 'Toggle Status Task Statuses', 'ar' => 'Toggle Status Task Statuses'], 'description' => ['en' => 'Can toggle status of task statuses', 'ar' => 'Can toggle status of task statuses']],

            // Workflows
            ['name' => 'manage-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage Workflows', 'ar' => 'Manage Workflows'], 'description' => ['en' => 'Can manage workflows', 'ar' => 'Can manage workflows']],

            ['name' => 'manage-any-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage All Workflows', 'ar' => 'Manage All Workflows'], 'description' => ['en' => 'Manage Any Workflows', 'ar' => 'Manage Any Workflows']],

            ['name' => 'manage-own-workflows', 'module' => 'workflows', 'label' => ['en' => 'Manage Own Workflows', 'ar' => 'Manage Own Workflows'], 'description' => ['en' => 'Manage Limited Workflows that is created by own', 'ar' => 'Manage Limited Workflows that is created by own']],

            ['name' => 'view-workflows', 'module' => 'workflows', 'label' => ['en' => 'View Workflows', 'ar' => 'View Workflows'], 'description' => ['en' => 'View Workflows', 'ar' => 'View Workflows']],

            ['name' => 'create-workflows', 'module' => 'workflows', 'label' => ['en' => 'Create Workflows', 'ar' => 'Create Workflows'], 'description' => ['en' => 'Can create workflows', 'ar' => 'Can create workflows']],

            ['name' => 'edit-workflows', 'module' => 'workflows', 'label' => ['en' => 'Edit Workflows', 'ar' => 'Edit Workflows'], 'description' => ['en' => 'Can edit workflows', 'ar' => 'Can edit workflows']],

            ['name' => 'delete-workflows', 'module' => 'workflows', 'label' => ['en' => 'Delete Workflows', 'ar' => 'Delete Workflows'], 'description' => ['en' => 'Can delete workflows', 'ar' => 'Can delete workflows']],

            ['name' => 'toggle-status-workflows', 'module' => 'workflows', 'label' => ['en' => 'Toggle Status Workflows', 'ar' => 'Toggle Status Workflows'], 'description' => ['en' => 'Can toggle status of workflows', 'ar' => 'Can toggle status of workflows']],

            // Task Dependencies

            // Task Comments
            ['name' => 'manage-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage Task Comments', 'ar' => 'Manage Task Comments'], 'description' => ['en' => 'Can manage task comments', 'ar' => 'Can manage task comments']],

            ['name' => 'manage-any-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage All Task Comments', 'ar' => 'Manage All Task Comments'], 'description' => ['en' => 'Manage Any Task Comments', 'ar' => 'Manage Any Task Comments']],

            ['name' => 'manage-own-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Manage Own Task Comments', 'ar' => 'Manage Own Task Comments'], 'description' => ['en' => 'Manage Limited Task Comments that is created by own', 'ar' => 'Manage Limited Task Comments that is created by own']],

            ['name' => 'view-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'View Task Comments', 'ar' => 'View Task Comments'], 'description' => ['en' => 'View Task Comments', 'ar' => 'View Task Comments']],

            ['name' => 'create-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Create Task Comments', 'ar' => 'Create Task Comments'], 'description' => ['en' => 'Can create task comments', 'ar' => 'Can create task comments']],

            ['name' => 'edit-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Edit Task Comments', 'ar' => 'Edit Task Comments'], 'description' => ['en' => 'Can edit task comments', 'ar' => 'Can edit task comments']],

            ['name' => 'delete-task-comments', 'module' => 'task_comments', 'label' => ['en' => 'Delete Task Comments', 'ar' => 'Delete Task Comments'], 'description' => ['en' => 'Can delete task comments', 'ar' => 'Can delete task comments']],

            // Communication & Collaboration
            ['name' => 'manage-messages', 'module' => 'messages', 'label' => ['en' => 'Manage Messages', 'ar' => 'Manage Messages'], 'description' => ['en' => 'Can manage internal messages', 'ar' => 'Can manage internal messages']],

            ['name' => 'manage-any-messages', 'module' => 'messages', 'label' => ['en' => 'Manage All Messages', 'ar' => 'Manage All Messages'], 'description' => ['en' => 'Manage Any Messages', 'ar' => 'Manage Any Messages']],

            ['name' => 'manage-own-messages', 'module' => 'messages', 'label' => ['en' => 'Manage Own Messages', 'ar' => 'Manage Own Messages'], 'description' => ['en' => 'Manage Limited Messages that is created by own', 'ar' => 'Manage Limited Messages that is created by own']],

            ['name' => 'view-messages', 'module' => 'messages', 'label' => ['en' => 'View Messages', 'ar' => 'View Messages'], 'description' => ['en' => 'View Messages', 'ar' => 'View Messages']],

            ['name' => 'send-messages', 'module' => 'messages', 'label' => ['en' => 'Send Messages', 'ar' => 'Send Messages'], 'description' => ['en' => 'Can send messages', 'ar' => 'Can send messages']],

            ['name' => 'delete-messages', 'module' => 'messages', 'label' => ['en' => 'Delete Messages', 'ar' => 'Delete Messages'], 'description' => ['en' => 'Can delete messages', 'ar' => 'Can delete messages']],

            // Calender
            ['name' => 'manage-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage Calendar', 'ar' => 'Manage Calendar'], 'description' => ['en' => 'Can manage calendar', 'ar' => 'Can manage calendar']],

            ['name' => 'manage-any-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage All Calendar', 'ar' => 'Manage All Calendar'], 'description' => ['en' => 'Manage Any Calendar Events', 'ar' => 'Manage Any Calendar Events']],

            ['name' => 'manage-own-calendar', 'module' => 'calendar', 'label' => ['en' => 'Manage Own Calendar', 'ar' => 'Manage Own Calendar'], 'description' => ['en' => 'Manage Own Calendar Events', 'ar' => 'Manage Own Calendar Events']],

            ['name' => 'view-calendar', 'module' => 'calendar', 'label' => ['en' => 'View Calendar', 'ar' => 'View Calendar'], 'description' => ['en' => 'View Calendar', 'ar' => 'View Calendar']],

        ];

        // Add task permissions to company role permissions
        $taskPermissions = [
            'tasks',
            'task_types',
            'task_statuses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                [
                    'module' => $permission['module'],
                    'label' => $permission['label'],
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
