// components/CrudTable.tsx
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import { TableAction, TableColumn } from '@/types/crud';
import { hasPermission } from '@/utils/authorization';
import { CurrencyAmount } from '@/components/currency-amount';
import { capitalize, getStatusIcon, getStatusLabel } from '@/utils/helpers';
import { Link } from '@inertiajs/react';
import {
    ChevronDown,
    ChevronUp,
    ChevronsUpDown,
    Check,
    CheckCircle,
    DollarSign,
    Download,
    Edit,
    Eye,
    Globe,
    Key,
    KeyRound,
    Link as LinkIcon,
    Lock,
    RotateCcw,
    Send,
    Trash2,
    ToggleLeft,
    Unlock,
    X,
    XCircle,
} from 'lucide-react';
import { useTranslation } from 'react-i18next';

const CRUD_ICONS: Record<string, React.ComponentType<{ size?: number }>> = {
    Check,
    CheckCircle,
    DollarSign,
    Download,
    Edit,
    Eye,
    Globe,
    Key,
    KeyRound,
    Link: LinkIcon,
    Lock,
    RotateCcw,
    Send,
    Trash2,
    ToggleLeft,
    Unlock,
    X,
    XCircle,
};

/** Resolve translatable API value (e.g. { en: '...', ar: '...' }) to a string for display. */
function resolveTranslatable(value: unknown, locale: string): string {
    if (value == null) return '';
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null && ('en' in value || 'ar' in value)) {
        const o = value as Record<string, string>;
        return o[locale] || o.en || o.ar || '';
    }
    return String(value);
}

interface CrudTableProps {
    columns: TableColumn[];
    actions: TableAction[];
    data: any[];
    from: number;
    onAction: (action: string, row: any) => void;
    sortField?: string;
    sortDirection?: 'asc' | 'desc';
    onSort?: (field: string) => void;
    statusColors?: Record<string, string>;
    permissions: string[];
    entityPermissions?: {
        view: string;
        edit: string;
        delete: string;
    };
    showActionsAsIcons?: boolean;
    showActions?: boolean;
}

export function CrudTable({
    columns,
    actions,
    data,
    from,
    onAction,
    sortField,
    sortDirection,
    onSort,
    statusColors = {},
    permissions,
    entityPermissions,
    showActions = true,
}: CrudTableProps) {
    const { t, i18n } = useTranslation();
    const locale = i18n.language || 'en';
    const isRtl = document.documentElement.dir === 'rtl';
    const renderSortIcon = (column: TableColumn) => {
        if (!column.sortable) return null;

        if (sortField === column.key) {
            return sortDirection === 'asc' ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />;
        }

        return <ChevronsUpDown className="h-4 w-4 opacity-50" />;
    };

    const handleSort = (column: TableColumn) => {
        if (!column.sortable || !onSort) return;
        onSort(column.key);
    };

    // Check if any actions have permissions
    const hasAnyActionPermission = actions.some((action) => {
        const permissionKey =
            action.requiredPermission ||
            (entityPermissions &&
                (action.action === 'view'
                    ? entityPermissions.view
                    : action.action === 'edit'
                        ? entityPermissions.edit
                        : action.action === 'delete'
                            ? entityPermissions.delete
                            : action.permission));

        return !permissionKey || hasPermission(permissions, permissionKey);
    });

    const renderActionButtons = (row: any) => {
        return (
            <div
                className={cn(
                    'flex items-center',
                    isRtl ? 'justify-start space-x-reverse space-x-2' : 'justify-end space-x-2'
                )}
            >
                {actions.map((action, index) => {
                    // Skip if user doesn't have permission
                    const permissionKey =
                        action.requiredPermission ||
                        (entityPermissions &&
                            (action.action === 'view'
                                ? entityPermissions.view
                                : action.action === 'edit'
                                    ? entityPermissions.edit
                                    : action.action === 'delete'
                                        ? entityPermissions.delete
                                        : action.permission));

                    if (permissionKey && !hasPermission(permissions, permissionKey)) {
                        return null;
                    }

                    // Skip if condition function returns false
                    if (action.condition && !action.condition(row)) {
                        return null;
                    }

                    // Handle dynamic icon and label for toggle-status actions
                    const iconName =
                        action.action === 'toggle-status'
                            ? getStatusIcon(row.status)
                            : typeof action.icon === 'function'
                                ? action.icon(row)
                                : action.icon;
                    const actionLabel =
                        action.action === 'toggle-status'
                            ? getStatusLabel(row.status, t)
                            : typeof action.label === 'function'
                                ? action.label(row)
                                : action.label;
                    const IconComponent = CRUD_ICONS[iconName] ?? Eye;

                    // Handle link actions
                    if (action.href) {
                        const href = typeof action.href === 'function' ? action.href(row) : action.href.replace(':id', row.id);

                        return (
                            <TooltipProvider key={index}>
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Link href={href} target={action.openInNewTab ? '_blank' : undefined}>
                                            <Button variant="ghost" size="icon" className={cn('h-8 w-8', action.className)}>
                                                <IconComponent size={16} />
                                            </Button>
                                        </Link>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{resolveTranslatable(actionLabel, locale)}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        );
                    }

                    // Handle regular action buttons
                    return (
                        <TooltipProvider key={index}>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className={cn('h-8 w-8', action.className)}
                                        onClick={() => {
                                            if (action.action) {
                                                onAction(action.action, row);
                                            }
                                        }}
                                    >
                                        <IconComponent size={16} />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{resolveTranslatable(actionLabel, locale)}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    );
                })}
            </div>
        );
    };

    // Helper function to get nested property value using dot notation
    const getNestedValue = (obj: any, path: string) => {
        if (!obj || !path) return null;

        const keys = path.split('.');
        return keys.reduce((acc, key) => {
            return acc && acc[key] !== undefined ? acc[key] : null;
        }, obj);
    };

    const renderCellContent = (row: any, col: TableColumn) => {
        // Get value using dot notation for nested properties
        const value = getNestedValue(row, col.key);

        // If column has custom render function, use it
        if (col.render) {
            return col.render(value, row);
        }

        // Handle different column types
        switch (col.type) {
            case 'badge':
                const badgeLabel = typeof value === 'object' && value !== null && ('en' in value || 'ar' in value)
                    ? resolveTranslatable(value, locale)
                    : value;
                return <Badge className={cn('capitalize', statusColors[value])}>{badgeLabel}</Badge>;

            case 'image':
                if (!value) {
                    return <div className="text-center text-gray-400">{t('No image')}</div>;
                }
                return (
                    <div className="flex justify-center">
                        <img
                            src={value.startsWith && value.startsWith('http') ? value : `/storage/${value}`}
                            alt={row.name || 'Image'}
                            className={col.className || 'h-16 w-20 rounded-md object-cover shadow-sm'}
                            onError={(e) => {
                                e.currentTarget.src = 'https://placehold.co/200x150?text=Image+Not+Found';
                            }}
                        />
                    </div>
                );

            case 'date':
                return value ? <span className="text-sm">{window.appSettings?.formatDateTime(value, false)}</span> : <span>-</span>;

            case 'currency':
                return <span className="text-sm"><CurrencyAmount amount={value ?? 0} /></span>;

            case 'boolean':
                return <span className="text-sm">{value ? 'Yes' : 'No'}</span>;

            case 'link':
                if (!value) return <span>-</span>;

                const href = col.href ? (typeof col.href === 'function' ? col.href(row) : col.href.replace(':id', row.id)) : '#';
                const linkText = typeof value === 'object' && value !== null && ('en' in value || 'ar' in value)
                    ? resolveTranslatable(value, locale)
                    : value;

                return (
                    <Link
                        href={href}
                        className={col.linkClassName || 'text-blue-600 hover:underline'}
                        target={col.openInNewTab ? '_blank' : undefined}
                    >
                        {linkText}
                    </Link>
                );

            default:
                // Avoid rendering objects (e.g. translatable { en, ar }) as React children
                const displayValue = typeof value === 'object' && value !== null && ('en' in value || 'ar' in value)
                    ? resolveTranslatable(value, locale)
                    : value;
                return <span className="text-sm font-medium">{displayValue ?? '-'}</span>;
        }
    };

    return (
        <div className="overflow-hidden rounded-xl bg-white  dark:bg-gray-900">
            <Table className="text-sm">
                <TableHeader>
                    <TableRow className="border-b border-slate-200 bg-[#f9f9f9] dark:border-gray-800 dark:bg-gray-900">
                        <TableHead className="w-12 px-4 py-3 text-sm font-semibold text-slate-400 dark:text-gray-400">
                            <div className={cn('flex items-center', isRtl ? 'justify-end text-right' : 'justify-start text-left')}>#</div>
                        </TableHead>
                        {columns.map((column) => (
                            <TableHead
                                key={column.key}
                                className={cn(
                                    'px-4 py-3 text-sm font-semibold text-slate-400 dark:text-gray-400',
                                    column.sortable && 'cursor-pointer select-none',
                                    column.className
                                )}
                                onClick={() => handleSort(column)}
                            >
                                <div className={cn('flex items-center gap-1', isRtl ? 'justify-end text-right flex-row-reverse' : 'justify-start text-left')}>
                                    {resolveTranslatable(column.label, locale)}
                                    {renderSortIcon(column)}
                                </div>
                            </TableHead>
                        ))}
                        {showActions && hasAnyActionPermission && (
                            <TableHead className="w-24 px-4 py-3 text-sm font-semibold text-slate-400 dark:text-gray-400">
                                <div className={cn('flex items-center', isRtl ? 'justify-start text-left' : 'justify-end text-right')}>
                                    {t('Actions')}
                                </div>
                            </TableHead>
                        )}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {data.length > 0 ? (
                        data.map((row, index) => (
                            <TableRow
                                key={row.id || index}
                                className="border-b border-slate-100 odd:bg-white even:bg-white hover:bg-[#EFF2F1] data-[state=selected]:bg-[#EFF2F1] dark:border-gray-800 dark:odd:bg-gray-900 dark:even:bg-gray-900 dark:hover:bg-gray-800/60"
                            >
                                <TableCell
                                    className={cn(
                                        'px-4 py-3 text-sm font-medium text-slate-700 dark:text-gray-200',
                                        isRtl ? 'text-right' : 'text-left'
                                    )}
                                >
                                    {from + index}
                                </TableCell>
                                {columns.map((col) => (
                                    <TableCell
                                        key={col.key}
                                        className={cn(
                                            'px-4 py-3 text-sm text-slate-700 dark:text-gray-200',
                                            isRtl ? 'text-right' : 'text-left',
                                            col.className
                                        )}
                                    >
                                        {renderCellContent(row, col)}
                                    </TableCell>
                                ))}
                                {showActions && hasAnyActionPermission && (
                                    <TableCell className={cn('px-4 py-3', isRtl ? 'text-left' : 'text-right')}>
                                        {renderActionButtons(row)}
                                    </TableCell>
                                )}
                            </TableRow>
                        ))
                    ) : (
                        <TableRow>
                            <TableCell
                                colSpan={columns.length + (showActions && hasAnyActionPermission ? 2 : 1)}
                                className="text-muted-foreground h-24 text-center dark:text-gray-400"
                            >
                                {t('No results found.')}
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
