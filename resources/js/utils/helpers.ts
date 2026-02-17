import { usePage } from "@inertiajs/react";

// Get base URL from page props or window
export function getBaseUrl(baseUrl?: string) {
    // priority: passed baseUrl → window origin → empty
    if (baseUrl) return baseUrl.replace(/\/$/, '');
    if (typeof window !== 'undefined') return window.location.origin;
    return '';
}

// Currency formatting: uses globalSettings.formatCurrency (decimal/thousands/symbol from app settings)
export const formatCurrency = (
  amount: string | number,
  optionsOrUseSuperAdmin?: boolean | { showSymbol?: boolean; showCode?: boolean }
) => {
  if (typeof window !== 'undefined' && window.appSettings?.formatCurrency) {
    const numericAmount = typeof amount === 'number' ? amount : parseFloat(String(amount));
    if (typeof optionsOrUseSuperAdmin === 'boolean' && optionsOrUseSuperAdmin && window.appSettings?.formatCurrencyWithSuperAdminSettings) {
      return window.appSettings.formatCurrencyWithSuperAdminSettings(numericAmount, { showSymbol: true });
    }
    const opts = typeof optionsOrUseSuperAdmin === 'object' && optionsOrUseSuperAdmin != null
      ? { showSymbol: true, showCode: false, ...optionsOrUseSuperAdmin }
      : { showSymbol: true, showCode: false };
    return window.appSettings.formatCurrency(numericAmount, opts);
  }
  return String(amount);
};

// Get full image path helper - consistent with reference project
export function getImagePath(path: string, baseUrl?: string) {
    if (!path) return '';

    // already absolute
    if (path.startsWith('http://') || path.startsWith('https://')) return path;

    const base = getBaseUrl(baseUrl);

    // normalize
    const normalized = path.startsWith('/') ? path : `/${path}`;
    return `${base}${normalized}`;
}

// Date formatting function
export const formatDate = (date: string | Date) => {
  const d = new Date(date);
  return d.toLocaleDateString();
};

// String capitalize function
export const capitalize = (str: string) => {
  if (!str) return '';

  return str
    .toLowerCase() // Convert everything to lowercase first
    .replace(/_/g, ' ') // Replace underscores with spaces
    .split(' ') // Split into words
    .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize first letter of each word
    .join(' '); // Join back with spaces
};


// Get status icon based on status value
export const getStatusIcon = (status: string | boolean | number) => {
  // Handle boolean values
  if (typeof status === 'boolean') {
    return status ? 'Unlock' : 'Lock';
  }

  // Handle string values
  if (typeof status === 'string') {
    const lowerStatus = status.toLowerCase();
    if (['active', 'enabled', 'open', 'published', 'approved', 'completed', 'paid'].includes(lowerStatus)) {
      return 'Unlock';
    }
    if (['inactive', 'disabled', 'closed', 'draft', 'pending', 'cancelled', 'unpaid'].includes(lowerStatus)) {
      return 'Lock';
    }
  }

  // Handle numeric values (1 = active, 0 = inactive)
  if (typeof status === 'number') {
    return status === 1 ? 'Unlock' : 'Lock';
  }

  // Default to Lock for unknown status
  return 'Lock';
};

// Get status label based on current status
export const getStatusLabel = (status: string | boolean | number, t: (key: string) => string) => {
  const icon = getStatusIcon(status);
  return icon === 'Unlock' ? t('Deactivate') : t('Activate');
};

// Get dynamic action config for status toggle
export const getStatusAction = (item: any, t: (key: string) => string, permission: string) => {
  const status = item.status;
  return {
    label: getStatusLabel(status, t),
    icon: getStatusIcon(status),
    action: 'toggle-status',
    className: 'text-green-500',
    requiredPermission: permission
  };
};

// Format currency using super admin settings for plans and referrals
export const formatCurrencyForPlansAndReferrals = (amount: string | number) => {
  if (typeof window !== 'undefined' && window.appSettings?.formatCurrencyWithSuperAdminSettings) {
    const numericAmount = typeof amount === 'number' ? amount : parseFloat(amount);
    return window.appSettings.formatCurrencyWithSuperAdminSettings(numericAmount, { showSymbol: true });
  }
  return formatCurrency(amount);
};


// Format currency using company settings (globalSettings.formatCurrency: decimal/thousands/symbol)
export const formatCurrencyForCompany = (
  amount: string | number,
  options: { showSymbol?: boolean; showCode?: boolean } = { showSymbol: true, showCode: false }
) => {
  if (typeof window !== 'undefined' && window.appSettings?.formatCurrency) {
    const numericAmount = typeof amount === 'number' ? amount : parseFloat(String(amount));
    return window.appSettings.formatCurrency(numericAmount, { showSymbol: true, showCode: false, ...options });
  }
  return formatCurrency(amount);
};
