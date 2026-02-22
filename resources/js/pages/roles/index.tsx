import { PageCrudWrapper } from '@/components/PageCrudWrapper';
import { rolesConfig } from '@/config/crud/roles';
import { RolePermissionCheckboxGroup } from '@/components/RolePermissionCheckboxGroup';
import { PermissionBadges } from '@/components/PermissionBadges';
import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

function resolveTranslatable(val: unknown, locale: string): string {
  if (val == null) return '';
  if (typeof val === 'string') return val;
  if (typeof val === 'object' && val !== null && ('en' in val || 'ar' in val)) {
    const o = val as Record<string, string>;
    return o[locale] || o.en || o.ar || '';
  }
  return String(val);
}

export default function RolesPage() {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language || 'en';
  const { permissions, flash, auth } = usePage().props as any;
  const [config, setConfig] = useState(rolesConfig);

  // Customize the form fields to handle permissions properly and resolve translatable columns
  useEffect(() => {
    if (permissions) {
      const baseColumns = rolesConfig.table.columns.map((col) => {
        if (col.key === 'label' || col.key === 'description') {
          return {
            ...col,
            render: (value: unknown) => resolveTranslatable(value, currentLocale) || '-'
          };
        }
        return col;
      });
      setConfig({
        ...rolesConfig,
        table: {
          ...rolesConfig.table,
          columns: [
            ...baseColumns,
            {
              key: 'permissions',
              label: t('Permissions'),
              render: (value, row) => <PermissionBadges permissions={value || []} />
            }
          ]
        },
        form: {
          ...rolesConfig.form,
          fields: [
            ...rolesConfig.form.fields.filter(field => field.name !== 'permissions'),
            {
              name: 'permissions',
              label: t('Role Permissions'),
              type: 'custom',
              colSpan: 12,
              render: (field, formData, onChange) => {
                return (
                  <div className="mt-4" id="permissions">
                    <h3 className="text-lg font-medium mb-2">{t("Manage Permissions")}</h3>
                    <p className="text-sm text-gray-500 mb-4">
                      {t("Select permissions for this role. You can select all permissions at once or manage them by module.")}
                      {auth.user?.type !== 'superadmin' && (
                        <span className="block mt-1 text-amber-600">
                          {t("Note: Only permissions for modules available to your role are shown.")}
                        </span>
                      )}
                    </p>
                    <RolePermissionCheckboxGroup
                      permissions={permissions}
                      selectedPermissions={formData.permissions || []}
                      onChange={(selected) => {
                        onChange('permissions', selected);
                      }}
                    />
                  </div>
                );
              }
            }
          ]
        },

      });
    }
  }, [permissions, t, currentLocale]);

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('User Management'), href: route('roles.index') },
    { title: t('Roles') }
  ];

  return (
    <PageCrudWrapper
      config={config}
      title={t('Roles')}
      url="/roles" 
      breadcrumbs={breadcrumbs}
    />
  );
}