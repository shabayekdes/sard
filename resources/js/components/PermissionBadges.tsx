// components/PermissionBadges.tsx
import React from 'react';
import { useTranslation } from 'react-i18next';

interface Permission {
  id: number | string;
  name: string;
  label: string | Record<string, string>;
}

interface PermissionBadgesProps {
  permissions: Permission[];
  maxDisplay?: number;
}

function resolveLabel(label: string | Record<string, string> | undefined, name: string, locale: string): string {
  if (label == null) return name || '';
  if (typeof label === 'string') return label;
  if (typeof label === 'object' && label !== null && ('en' in label || 'ar' in label)) {
    const o = label as Record<string, string>;
    return o[locale] || o.en || o.ar || name || '';
  }
  return name || '';
}

export function PermissionBadges({ permissions = [], maxDisplay = 3 }: PermissionBadgesProps) {
  const { t, i18n } = useTranslation();
  const locale = i18n.language || 'en';
  if (!permissions || !Array.isArray(permissions) || permissions.length === 0) {
    return <span className="text-sm text-gray-500">-</span>;
  }

  return (
    <div className="flex flex-wrap gap-1">
      {permissions.slice(0, maxDisplay).map((permission, index) => (
        <span 
          key={index} 
          className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
        >
          {resolveLabel(permission.label, permission.name, locale)}
        </span>
      ))}
      {permissions.length > maxDisplay && (
        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
          +{permissions.length - maxDisplay} {t("more")}
        </span>
      )}
    </div>
  );
}