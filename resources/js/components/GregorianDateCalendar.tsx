import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { formatGregorianYmd, parseGregorianYmd } from '@/utils/hijriGregorian';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

function daysInGregorianMonth(year: number, month1to12: number): number {
    return new Date(year, month1to12, 0).getDate();
}

function firstOfMonthWeekday0Sun(year: number, month1to12: number): number {
    return new Date(year, month1to12 - 1, 1).getDay();
}

function initialViewFromStrings(gregorianYmd: string, gregorianText: string): { y: number; m: number } {
    const fromText = parseGregorianYmd(gregorianText.trim());
    if (fromText) return { y: fromText.gy, m: fromText.gm };
    const fromVal = parseGregorianYmd(gregorianYmd.trim());
    if (fromVal) return { y: fromVal.gy, m: fromVal.gm };
    const n = new Date();
    return { y: n.getFullYear(), m: n.getMonth() + 1 };
}

function isYmdInMinMax(ymd: string, min?: string, max?: string): boolean {
    if (min && ymd < min) return false;
    if (max && ymd > max) return false;
    return true;
}

export interface GregorianDateCalendarProps {
    gregorianYmd: string;
    gregorianText: string;
    onSelectGregorian: (ymd: string) => void;
    disabled?: boolean;
    min?: string;
    max?: string;
}

export function GregorianDateCalendar({
    gregorianYmd,
    gregorianText,
    onSelectGregorian,
    disabled,
    min,
    max,
}: GregorianDateCalendarProps) {
    const { t, i18n } = useTranslation();
    const isAr = (i18n.language || 'en').startsWith('ar');
    const loc = isAr ? 'ar-u-nu-latn' : undefined;

    const init = initialViewFromStrings(gregorianYmd, gregorianText);
    const [viewY, setViewY] = useState(init.y);
    const [viewM, setViewM] = useState(init.m);

    const selected = useMemo(() => {
        const p = parseGregorianYmd(gregorianYmd.trim());
        return p;
    }, [gregorianYmd]);

    const today = useMemo(() => {
        const n = new Date();
        return { y: n.getFullYear(), m: n.getMonth() + 1, d: n.getDate() };
    }, []);

    const weekdayNarrow = useMemo(() => {
        const labels: string[] = [];
        const base = new Date(2024, 0, 7);
        for (let i = 0; i < 7; i++) {
            const d = new Date(base);
            d.setDate(base.getDate() + i);
            labels.push(new Intl.DateTimeFormat(loc, { weekday: 'narrow' }).format(d));
        }
        return labels;
    }, [loc]);

    const monthLen = daysInGregorianMonth(viewY, viewM);
    const startDow = firstOfMonthWeekday0Sun(viewY, viewM);

    const cells = useMemo(() => {
        const out: ({ type: 'pad' } | { type: 'day'; d: number })[] = [];
        for (let i = 0; i < startDow; i++) out.push({ type: 'pad' });
        for (let d = 1; d <= monthLen; d++) out.push({ type: 'day', d });
        while (out.length % 7 !== 0) out.push({ type: 'pad' });
        while (out.length < 42) out.push({ type: 'pad' });
        return out;
    }, [monthLen, startDow]);

    const title = useMemo(
        () =>
            new Intl.DateTimeFormat(loc, { month: 'short', year: 'numeric' }).format(new Date(viewY, viewM - 1, 1)),
        [loc, viewY, viewM],
    );

    const goPrev = () => {
        let y = viewY;
        let m = viewM - 1;
        if (m < 1) {
            m = 12;
            y -= 1;
        }
        setViewY(y);
        setViewM(m);
    };

    const goNext = () => {
        let y = viewY;
        let m = viewM + 1;
        if (m > 12) {
            m = 1;
            y += 1;
        }
        setViewY(y);
        setViewM(m);
    };

    return (
        <div className="w-[min(100vw-2rem,280px)] select-none" dir="ltr">
            <div className="mb-2 flex items-center justify-between gap-1">
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="h-8 w-8 shrink-0"
                    onClick={goPrev}
                    disabled={disabled || viewY < 1000}
                    aria-label={t('Gregorian previous month')}
                >
                    <ChevronLeft className="h-4 w-4" />
                </Button>
                <div className="min-w-0 flex-1 text-center text-sm font-medium tabular-nums">{title}</div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="h-8 w-8 shrink-0"
                    onClick={goNext}
                    disabled={disabled || viewY > 9999}
                    aria-label={t('Gregorian next month')}
                >
                    <ChevronRight className="h-4 w-4" />
                </Button>
            </div>
            <div className="grid grid-cols-7 gap-0.5 text-center text-[10px] font-medium text-muted-foreground sm:text-xs">
                {weekdayNarrow.map((w, i) => (
                    <div key={i} className="py-1">
                        {w}
                    </div>
                ))}
            </div>
            <div className="mt-1 grid grid-cols-7 gap-0.5">
                {cells.map((cell, idx) => {
                    if (cell.type === 'pad') {
                        return <div key={`p-${idx}`} className="h-8" />;
                    }
                    const d = cell.d;
                    const ymd = formatGregorianYmd(viewY, viewM, d);
                    const inRange = isYmdInMinMax(ymd, min, max);
                    const isSelected = selected?.gy === viewY && selected?.gm === viewM && selected?.gd === d;
                    const isToday = today.y === viewY && today.m === viewM && today.d === d;
                    return (
                        <Button
                            key={`d-${d}`}
                            type="button"
                            variant="ghost"
                            disabled={disabled || !inRange}
                            onClick={() => onSelectGregorian(ymd)}
                            className={cn(
                                'h-8 w-full min-w-0 p-0 text-xs font-normal tabular-nums sm:text-sm',
                                isSelected && 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground',
                                !isSelected && isToday && 'border border-primary/60',
                            )}
                        >
                            {d}
                        </Button>
                    );
                })}
            </div>
        </div>
    );
}
