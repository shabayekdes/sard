import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import {
    type HijriParts,
    gregorianYmdToHijriYmd,
    hijriFirstOfMonthWeekday0Sun,
    hijriMonthLength,
    hijriPartsToGregorianYmd,
    parseHijriYmd,
} from '@/utils/hijriGregorian';
import { toHijri } from 'hijri-converter';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

const HIJRI_MONTHS_SHORT_EN = [
    'Muh.',
    'Saf.',
    'Rab. I',
    'Rab. II',
    'Jum. I',
    'Jum. II',
    'Raj.',
    'Sha.',
    'Ram.',
    'Shaw.',
    'Dhu Q.',
    'Dhu H.',
];
const HIJRI_MONTHS_SHORT_AR = [
    'محرم',
    'صفر',
    'ربيع الأول',
    'ربيع الآخر',
    'جمادى الأولى',
    'جمادى الآخرة',
    'رجب',
    'شعبان',
    'رمضان',
    'شوال',
    'ذو القعدة',
    'ذو الحجة',
];

function todayHijriParts(): HijriParts {
    const n = new Date();
    return toHijri(n.getFullYear(), n.getMonth() + 1, n.getDate()) as HijriParts;
}

function shiftMonth(hy: number, hm: number, delta: number): { hy: number; hm: number } {
    let y = hy;
    let m = hm + delta;
    while (m > 12) {
        m -= 12;
        y += 1;
    }
    while (m < 1) {
        m += 12;
        y -= 1;
    }
    return { hy: y, hm: m };
}

function findAdjacentValidMonth(hy: number, hm: number, dir: -1 | 1): { hy: number; hm: number } | null {
    let y = hy;
    let m = hm;
    for (let i = 0; i < 48; i++) {
        const next = shiftMonth(y, m, dir);
        y = next.hy;
        m = next.hm;
        if (hijriMonthLength(y, m) > 0) return { hy: y, hm: m };
    }
    return null;
}

function initialViewMonth(gregorianYmd: string, hijriYmdText: string): { hy: number; hm: number } {
    const fromText = parseHijriYmd(hijriYmdText.trim());
    if (fromText && hijriPartsToGregorianYmd(fromText) && hijriMonthLength(fromText.hy, fromText.hm) > 0) {
        return { hy: fromText.hy, hm: fromText.hm };
    }
    const hStr = gregorianYmd ? gregorianYmdToHijriYmd(gregorianYmd) : '';
    const fromG = hStr ? parseHijriYmd(hStr) : null;
    if (fromG && hijriMonthLength(fromG.hy, fromG.hm) > 0) {
        return { hy: fromG.hy, hm: fromG.hm };
    }
    const t = todayHijriParts();
    if (hijriMonthLength(t.hy, t.hm) > 0) return { hy: t.hy, hm: t.hm };
    const adj = findAdjacentValidMonth(t.hy, t.hm, -1) || findAdjacentValidMonth(t.hy, t.hm, 1);
    if (adj) return adj;
    return { hy: t.hy, hm: t.hm };
}

export interface HijriDateCalendarProps {
    /** Current Gregorian `YYYY-MM-DD` (date part only). */
    gregorianYmd: string;
    /** Current Hijri text `YYYY-MM-DD` (may be partial while typing). */
    hijriYmdText: string;
    onSelectHijri: (parts: HijriParts) => void;
    disabled?: boolean;
}

export function HijriDateCalendar({ gregorianYmd, hijriYmdText, onSelectHijri, disabled }: HijriDateCalendarProps) {
    const { t, i18n } = useTranslation();
    const isAr = (i18n.language || 'en').startsWith('ar');
    const monthNames = isAr ? HIJRI_MONTHS_SHORT_AR : HIJRI_MONTHS_SHORT_EN;

    const [viewHy, setViewHy] = useState(() => initialViewMonth(gregorianYmd, hijriYmdText).hy);
    const [viewHm, setViewHm] = useState(() => initialViewMonth(gregorianYmd, hijriYmdText).hm);

    const selected = useMemo(() => {
        const p = parseHijriYmd(hijriYmdText.trim());
        if (!p || !hijriPartsToGregorianYmd(p)) return null;
        return p;
    }, [hijriYmdText]);

    const today = useMemo(() => todayHijriParts(), []);

    const weekdayNarrow = useMemo(() => {
        const labels: string[] = [];
        const base = new Date(2024, 0, 7);
        const loc = isAr ? 'ar-u-nu-latn' : 'en';
        for (let i = 0; i < 7; i++) {
            const d = new Date(base);
            d.setDate(base.getDate() + i);
            labels.push(new Intl.DateTimeFormat(loc, { weekday: 'narrow' }).format(d));
        }
        return labels;
    }, [isAr]);

    const monthLen = hijriMonthLength(viewHy, viewHm);
    const startDow = hijriFirstOfMonthWeekday0Sun(viewHy, viewHm);

    const cells = useMemo(() => {
        const out: ({ type: 'pad' } | { type: 'day'; d: number })[] = [];
        if (monthLen === 0 || startDow === null) return out;
        for (let i = 0; i < startDow; i++) out.push({ type: 'pad' });
        for (let d = 1; d <= monthLen; d++) out.push({ type: 'day', d });
        while (out.length % 7 !== 0) out.push({ type: 'pad' });
        while (out.length < 42) out.push({ type: 'pad' });
        return out;
    }, [monthLen, startDow]);

    const goPrev = () => {
        const n = findAdjacentValidMonth(viewHy, viewHm, -1);
        if (n) {
            setViewHy(n.hy);
            setViewHm(n.hm);
        }
    };

    const goNext = () => {
        const n = findAdjacentValidMonth(viewHy, viewHm, 1);
        if (n) {
            setViewHy(n.hy);
            setViewHm(n.hm);
        }
    };

    const canPrev = findAdjacentValidMonth(viewHy, viewHm, -1) != null;
    const canNext = findAdjacentValidMonth(viewHy, viewHm, 1) != null;

    const title = `${monthNames[viewHm - 1] ?? viewHm} ${viewHy}`;

    return (
        <div className="w-[min(100vw-2rem,280px)] select-none" dir="ltr">
            <div className="mb-2 flex items-center justify-between gap-1">
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="h-8 w-8 shrink-0"
                    onClick={goPrev}
                    disabled={disabled || !canPrev}
                    aria-label={t('Hijri previous month')}
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
                    disabled={disabled || !canNext}
                    aria-label={t('Hijri next month')}
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
                    const parts: HijriParts = { hy: viewHy, hm: viewHm, hd: d };
                    const valid = hijriPartsToGregorianYmd(parts) != null;
                    const isSelected = selected?.hy === viewHy && selected?.hm === viewHm && selected?.hd === d;
                    const isToday = today.hy === viewHy && today.hm === viewHm && today.hd === d;
                    return (
                        <Button
                            key={`d-${d}`}
                            type="button"
                            variant="ghost"
                            disabled={disabled || !valid}
                            onClick={() => onSelectHijri(parts)}
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
