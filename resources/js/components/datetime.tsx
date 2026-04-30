import { Calendar, Clock } from 'lucide-react';
import { cn } from '@/lib/utils';

export interface DatetimeProps {
    /** Main calendar date (ISO string or Date). */
    value?: string | Date | null;
    /** `date`: one row. `datetime`: date row plus optional time row. */
    variant?: 'date' | 'datetime';
    /**
     * Optional time shown on the second row when `variant` is `datetime`.
     * Use for separate fields (e.g. SQL `HH:mm:ss`); passed through `formatTime` like `2000-01-01T${time}`.
     */
    timeValue?: string | Date | number | null;
    /**
     * When `variant` is `datetime` and `timeValue` is not set, show a second line with time parsed from `value`
     * (full ISO datetime). Matches `formatDateTime(..., true)` style.
     */
    showInlineTime?: boolean;
    showIcons?: boolean;
    /** Shown when `value` is null, undefined, or empty string. */
    emptyLabel?: React.ReactNode;
    className?: string;
    dateRowClassName?: string;
    timeRowClassName?: string;
    iconClassName?: string;
}

/**
 * Format a date using app settings (`formatDate` → same table style as globalSettings TABLE_DATE_FORMAT / hijri).
 * For plain strings (tooltips, aria), use this. For JSX use {@link Datetime}.
 */
export function formatAppDate(value: string | Date): string {
    if (typeof window !== 'undefined' && window.appSettings?.formatDate) {
        const s = window.appSettings.formatDate(value);
        if (s) return s;
    }
    const d = typeof value === 'string' ? new Date(value) : value;
    return Number.isNaN(d.getTime()) ? String(value) : d.toLocaleDateString();
}

/**
 * Format a time value using app `formatTime`. Accepts ISO-like strings or `HH:mm` / `HH:mm:ss` fragments.
 */
export function formatAppTimeDisplay(time: string | Date | number): string {
    const t: string | Date = typeof time === 'number' ? String(time) : time;
    const toParse: string | Date =
        typeof t === 'string' && /^\d{1,2}:\d{2}/.test(t.trim()) && !t.includes('T')
            ? `2000-01-01T${t}`
            : t;

    const isArabicUi =
        typeof document !== 'undefined' &&
        ((document.documentElement.lang || '').toLowerCase().startsWith('ar') || document.documentElement.dir === 'rtl');
    const amLabel = isArabicUi ? 'ص' : 'AM';
    const pmLabel = isArabicUi ? 'م' : 'PM';
    const localizeMeridiem = (value: string): string => {
        if (!isArabicUi) return value;
        return value.replace(/\bAM\b/gi, amLabel).replace(/\bPM\b/gi, pmLabel);
    };

    const withMeridiem = (base: string, date: Date): string => {
        if (/\b(am|pm)\b/i.test(base) || /[صم]/.test(base)) return localizeMeridiem(base);
        return `${base} ${date.getHours() >= 12 ? pmLabel : amLabel}`;
    };

    if (typeof window !== 'undefined' && window.appSettings?.formatTime) {
        const s = window.appSettings.formatTime(toParse);
        if (s) {
            const dt = typeof toParse === 'string' ? new Date(toParse) : toParse;
            if (!Number.isNaN(dt.getTime())) return withMeridiem(String(s), dt);
            return localizeMeridiem(String(s));
        }
    }

    const d = typeof toParse === 'string' ? new Date(toParse) : toParse;
    if (Number.isNaN(d.getTime())) return String(t);
    return localizeMeridiem(
        d.toLocaleTimeString('en-US', {
              hour: 'numeric',
              minute: '2-digit',
              hour12: true,
          }),
    );
}

/**
 * Renders a date (and optionally a separate time) using tenant date/time settings.
 * Same formatting pipeline as `initializeGlobalSettings` (`formatDate` / `formatTime`).
 */
export function Datetime({
    value,
    variant = 'date',
    timeValue,
    showInlineTime = false,
    showIcons = true,
    emptyLabel = '-',
    className,
    dateRowClassName,
    timeRowClassName,
    iconClassName = 'h-3 w-3 shrink-0',
}: DatetimeProps) {
    if (value == null || value === '') {
        return <span className={className}>{emptyLabel}</span>;
    }

    const dateLabel = formatAppDate(value);

    if (variant !== 'datetime') {
        return (
            <div className={cn('flex items-center gap-1', className, dateRowClassName)}>
                {showIcons ? <Calendar className={iconClassName} aria-hidden /> : null}
                <span>{dateLabel}</span>
            </div>
        );
    }

    const hasSeparateTime = timeValue != null && timeValue !== '';
    const timeStr = hasSeparateTime
        ? formatAppTimeDisplay(timeValue)
        : showInlineTime
          ? formatAppTimeDisplay(value)
          : null;

    return (
        <div className={cn('flex flex-col', className)}>
            <div className={cn('flex items-center gap-1', dateRowClassName)}>
                {showIcons ? <Calendar className={iconClassName} aria-hidden /> : null}
                <span>{dateLabel}</span>
            </div>
            {timeStr ? (
                <div className={cn('flex items-center gap-1 text-xs text-gray-500', timeRowClassName)}>
                    {showIcons ? <Clock className={iconClassName} aria-hidden /> : null}
                    <span>{timeStr}</span>
                </div>
            ) : null}
        </div>
    );
}
