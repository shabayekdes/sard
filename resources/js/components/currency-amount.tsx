import { SaudiRiyal } from 'lucide-react';

interface CurrencyAmountProps {
    amount: number | string;
    /** Use company settings (default) or super-admin settings (e.g. plans/referrals). */
    variant?: 'company' | 'superadmin';
    showSymbol?: boolean;
    showCode?: boolean;
    /** Icon size in pixels when default currency is SAR. Default 16 */
    iconSize?: number;
    className?: string;
}

/**
 * Format amount as string using app settings (same logic as CurrencyAmount).
 * Use when you need a string (e.g. itemName, tooltips). For JSX use <CurrencyAmount />.
 */
export function formatCurrencyAmount(
    numericAmount: number,
    variant: 'company' | 'superadmin',
    options: { showSymbol: boolean; showCode: boolean }
): string {
    if (typeof window === 'undefined') return String(numericAmount);
    if (variant === 'superadmin' && window.appSettings?.formatCurrencyWithSuperAdminSettings) {
        return window.appSettings.formatCurrencyWithSuperAdminSettings(numericAmount, options);
    }
    if (window.appSettings?.formatCurrency) {
        return window.appSettings.formatCurrency(numericAmount, options);
    }
    return String(numericAmount);
}

/**
 * Reusable component for displaying money with currency. Formatting is implemented here (company/superadmin settings).
 * When default currency in settings is SAR, shows SaudiRiyal from lucide-react next to the amount.
 * For plain-string formatting (e.g. formatters, tooltips), use formatCurrency / formatCurrencyForCompany from @/utils/helpers.
 */
export function CurrencyAmount({
    amount,
    variant = 'company',
    showSymbol = true,
    showCode = false,
    iconSize = 16,
    className = '',
}: CurrencyAmountProps) {
    const raw = typeof amount === 'number' ? amount : parseFloat(String(amount));
    const numericAmount = Number.isFinite(raw) ? raw : 0;
    const isSSR = typeof window === 'undefined';
    const defaultCurrency = !isSSR ? window.appSettings?.currencySettings?.defaultCurrency : 'USD';
    const showSarIcon = defaultCurrency === 'SAR';

    const formatOptions = { showSymbol, showCode };
    const formatted = formatCurrencyAmount(numericAmount, variant, formatOptions);

    if (showSarIcon) {
        const numPart = !isSSR && window.appSettings?.formatCurrency
            ? window.appSettings.formatCurrency(numericAmount, { showSymbol: false, showCode: false })
            : numericAmount.toFixed(2);
        return (
            <span className={`inline-flex items-center gap-1 whitespace-nowrap ${className}`}>
                <SaudiRiyal size={iconSize} className="shrink-0 inline-block" strokeWidth={2} aria-hidden />
                <span>{numPart}</span>
            </span>
        );
    }

    return <span className={className}>{formatted}</span>;
}
