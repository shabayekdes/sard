import { GregorianDateCalendar } from '@/components/GregorianDateCalendar';
import { HijriDateCalendar } from '@/components/HijriDateCalendar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverAnchor, PopoverContent } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import {
    formatGregorianYmd,
    formatHijriYmd,
    gregorianYmdToHijriYmd,
    type HijriParts,
    hijriPartsToGregorianYmd,
    joinDatetimeLocal,
    normalizeGregorianYmd,
    parseGregorianYmd,
    parseHijriYmd,
    splitDatetimeLocal,
} from '@/utils/hijriGregorian';
import { CalendarDays } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

export type GregorianHijriDateFieldMode = 'date' | 'datetime-local';

export interface GregorianHijriDateFieldProps {
    id?: string;
    name?: string;
    /** Gregorian `YYYY-MM-DD`, or full `datetime-local` string when mode is `datetime-local`. */
    value: string;
    onChange: (value: string) => void;
    mode?: GregorianHijriDateFieldMode;
    disabled?: boolean;
    required?: boolean;
    min?: string;
    max?: string;
    className?: string;
    /** Highlights Gregorian control (e.g. server-side field error). */
    error?: boolean;
    /** Optional message (e.g. server error) shown under the Gregorian column when no local Gregorian error. */
    helperText?: string;
}

function ymdFromFieldValue(value: string, mode: GregorianHijriDateFieldMode): string {
    if (mode === 'datetime-local') {
        return splitDatetimeLocal(value).date;
    }
    return normalizeGregorianYmd(value);
}

export function GregorianHijriDateField({
    id,
    name,
    value,
    onChange,
    mode = 'date',
    disabled = false,
    required = false,
    min,
    max,
    className,
    error = false,
    helperText,
}: GregorianHijriDateFieldProps) {
    const { t } = useTranslation();
    const [gregorianText, setGregorianText] = useState('');
    const [gregorianErr, setGregorianErr] = useState('');
    const [gregorianCalOpen, setGregorianCalOpen] = useState(false);
    const [gregorianCalKey, setGregorianCalKey] = useState(0);

    const [hijriText, setHijriText] = useState('');
    const [hijriErr, setHijriErr] = useState('');
    const [hijriCalOpen, setHijriCalOpen] = useState(false);
    const [hijriCalKey, setHijriCalKey] = useState(0);

    const gregorianYmd = ymdFromFieldValue(value, mode);
    const timePart = mode === 'datetime-local' ? splitDatetimeLocal(value).time : '';
    const minYmd = min != null ? String(min).split('T')[0] : undefined;
    const maxYmd = max != null ? String(max).split('T')[0] : undefined;

    useEffect(() => {
        const ymd = ymdFromFieldValue(value, mode);
        setGregorianText(ymd);
        setGregorianErr('');
        setHijriText(ymd ? gregorianYmdToHijriYmd(ymd) : '');
        setHijriErr('');
    }, [value, mode]);

    const publishGregorianDate = useCallback(
        (ymd: string) => {
            setHijriText(ymd ? gregorianYmdToHijriYmd(ymd) : '');
            setHijriErr('');
            if (mode === 'datetime-local') {
                const tp = timePart && /^\d{2}:\d{2}$/.test(timePart) ? timePart : '00:00';
                onChange(ymd ? joinDatetimeLocal(ymd, tp) : '');
            } else {
                onChange(ymd);
            }
        },
        [mode, onChange, timePart],
    );

    const commitGregorianYmd = useCallback(
        (raw: string) => {
            const p = parseGregorianYmd(raw.trim());
            if (!p) {
                setGregorianErr(t('Invalid Gregorian date'));
                return;
            }
            const normalized = formatGregorianYmd(p.gy, p.gm, p.gd);
            if ((minYmd && normalized < minYmd) || (maxYmd && normalized > maxYmd)) {
                setGregorianErr(t('Invalid Gregorian date'));
                return;
            }
            setGregorianErr('');
            setGregorianText(normalized);
            publishGregorianDate(normalized);
        },
        [publishGregorianDate, t, minYmd, maxYmd],
    );

    const onGregorianTextChange = (raw: string) => {
        setGregorianText(raw);
        const trimmed = raw.trim();
        if (!trimmed) {
            setGregorianErr('');
            publishGregorianDate('');
            return;
        }
        const parts = parseGregorianYmd(trimmed);
        if (!parts) {
            if (trimmed.length >= 10) {
                setGregorianErr(t('Invalid Gregorian date'));
            } else {
                setGregorianErr('');
            }
            return;
        }
        commitGregorianYmd(trimmed);
    };

    const onGregorianBlur = () => {
        if (!gregorianText.trim() && gregorianYmd) {
            setGregorianText(gregorianYmd);
        }
    };

    const openGregorianCalendar = useCallback(() => {
        if (disabled) return;
        setGregorianCalOpen((wasOpen) => {
            if (!wasOpen) {
                setGregorianCalKey((k) => k + 1);
            }
            return true;
        });
    }, [disabled]);

    const onTimeChange = (tp: string) => {
        const datePart = parseGregorianYmd(gregorianText.trim()) ? gregorianText.trim() : gregorianYmd;
        if (!datePart) return;
        onChange(joinDatetimeLocal(datePart, tp || '00:00'));
    };

    const commitHijriParts = useCallback(
        (parts: HijriParts) => {
            const g = hijriPartsToGregorianYmd(parts);
            if (!g) {
                setHijriErr(t('Invalid Hijri date'));
                return;
            }
            setHijriErr('');
            setHijriText(formatHijriYmd(parts));
            setGregorianText(g);
            setGregorianErr('');
            if (mode === 'datetime-local') {
                const tPart = timePart && /^\d{2}:\d{2}$/.test(timePart) ? timePart : '00:00';
                onChange(joinDatetimeLocal(g, tPart));
            } else {
                onChange(g);
            }
        },
        [mode, onChange, timePart, t],
    );

    const onHijriChange = (raw: string) => {
        setHijriText(raw);
        const trimmed = raw.trim();
        if (!trimmed) {
            setHijriErr('');
            return;
        }
        const parts = parseHijriYmd(trimmed);
        if (!parts) {
            if (trimmed.length >= 10) {
                setHijriErr(t('Invalid Hijri date'));
            } else {
                setHijriErr('');
            }
            return;
        }
        commitHijriParts(parts);
    };

    const onHijriBlur = () => {
        if (!hijriText.trim() && gregorianYmd) {
            setHijriText(gregorianYmdToHijriYmd(gregorianYmd));
        }
    };

    const openHijriCalendar = useCallback(() => {
        if (disabled) return;
        setHijriCalOpen((wasOpen) => {
            if (!wasOpen) {
                setHijriCalKey((k) => k + 1);
            }
            return true;
        });
    }, [disabled]);

    const errClass = error ? 'border-red-500' : '';
    const gregorianInputClass = cn(
        'min-w-0 flex-1 text-start',
        !disabled && 'cursor-pointer',
        gregorianErr ? 'border-red-500' : errClass,
    );

    return (
        <div className={cn('space-y-2', className)}>
            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:items-end">
                <div className="space-y-1">
                    <Label htmlFor={id} className="text-xs text-muted-foreground">
                        {t('Gregorian date')}
                    </Label>
                    <Popover open={gregorianCalOpen} onOpenChange={setGregorianCalOpen}>
                        <PopoverAnchor asChild>
                            <div className="flex flex-col gap-2">
                                <div className="flex gap-2">
                                    <Input
                                        id={id}
                                        name={name}
                                        type="text"
                                        inputMode="numeric"
                                        placeholder={t('Gregorian date placeholder')}
                                        value={gregorianText}
                                        onChange={(e) => onGregorianTextChange(e.target.value)}
                                        onClick={openGregorianCalendar}
                                        onBlur={onGregorianBlur}
                                        required={required}
                                        disabled={disabled}
                                        className={gregorianInputClass}
                                        autoComplete="off"
                                        aria-haspopup="dialog"
                                        aria-expanded={gregorianCalOpen}
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        className="h-9 w-9 shrink-0"
                                        disabled={disabled}
                                        aria-label={t('Open Gregorian calendar')}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            openGregorianCalendar();
                                        }}
                                    >
                                        <CalendarDays className="h-4 w-4" />
                                    </Button>
                                </div>
                                {mode === 'datetime-local' && (
                                    <div className="space-y-1">
                                        <Label htmlFor={id ? `${id}_time` : undefined} className="text-xs text-muted-foreground">
                                            {t('Time')}
                                        </Label>
                                        <Input
                                            id={id ? `${id}_time` : undefined}
                                            type="time"
                                            value={timePart}
                                            onChange={(e) => onTimeChange(e.target.value)}
                                            disabled={disabled}
                                            className={cn('w-full max-w-[12rem] text-start', errClass)}
                                        />
                                    </div>
                                )}
                            </div>
                        </PopoverAnchor>
                        <PopoverContent className="w-auto p-2" align="start" sideOffset={6}>
                            <GregorianDateCalendar
                                key={gregorianCalKey}
                                gregorianYmd={gregorianYmd}
                                gregorianText={gregorianText}
                                onSelectGregorian={(ymd) => {
                                    commitGregorianYmd(ymd);
                                    setGregorianCalOpen(false);
                                }}
                                disabled={disabled}
                                min={minYmd}
                                max={maxYmd}
                            />
                        </PopoverContent>
                    </Popover>
                    {(gregorianErr || helperText) && (
                        <p className={cn('text-xs', gregorianErr ? 'text-red-600' : 'text-muted-foreground')}>
                            {gregorianErr || helperText}
                        </p>
                    )}
                </div>
                <div className="space-y-1">
                    <Label htmlFor={id ? `${id}_hijri` : undefined} className="text-xs text-muted-foreground">
                        {t('Hijri date (Umm al-Qura)')}
                    </Label>
                    <Popover open={hijriCalOpen} onOpenChange={setHijriCalOpen}>
                        <PopoverAnchor asChild>
                            <div className="flex gap-2">
                                <Input
                                    id={id ? `${id}_hijri` : undefined}
                                    type="text"
                                    inputMode="numeric"
                                    placeholder={t('Hijri date placeholder')}
                                    value={hijriText}
                                    onChange={(e) => onHijriChange(e.target.value)}
                                    onClick={openHijriCalendar}
                                    onBlur={onHijriBlur}
                                    disabled={disabled}
                                    className={cn(
                                        'min-w-0 flex-1 text-start',
                                        !disabled && 'cursor-pointer',
                                        hijriErr ? 'border-red-500' : '',
                                    )}
                                    autoComplete="off"
                                    aria-haspopup="dialog"
                                    aria-expanded={hijriCalOpen}
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    className="h-9 w-9 shrink-0"
                                    disabled={disabled}
                                    aria-label={t('Open Hijri calendar')}
                                    onClick={(e) => {
                                        e.preventDefault();
                                        openHijriCalendar();
                                    }}
                                >
                                    <CalendarDays className="h-4 w-4" />
                                </Button>
                            </div>
                        </PopoverAnchor>
                        <PopoverContent className="w-auto p-2" align="start" sideOffset={6}>
                            <HijriDateCalendar
                                key={hijriCalKey}
                                gregorianYmd={gregorianYmd}
                                hijriYmdText={hijriText}
                                onSelectHijri={(p) => {
                                    commitHijriParts(p);
                                    setHijriCalOpen(false);
                                }}
                                disabled={disabled}
                            />
                        </PopoverContent>
                    </Popover>
                    {hijriErr && <p className="text-xs text-red-600">{hijriErr}</p>}
                </div>
            </div>
        </div>
    );
}
