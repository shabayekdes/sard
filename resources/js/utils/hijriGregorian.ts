import { toGregorian, toHijri } from 'hijri-converter';

export type HijriParts = { hy: number; hm: number; hd: number };

/** Normalize API / form value to `YYYY-MM-DD` for `<input type="date" />`. */
export function normalizeGregorianYmd(value: unknown): string {
    if (value == null || value === '') return '';
    if (value instanceof Date) {
        if (Number.isNaN(value.getTime())) return '';
        return formatGregorianYmd(value.getFullYear(), value.getMonth() + 1, value.getDate());
    }
    const s = String(value);
    if (s.includes('T')) return s.split('T')[0] ?? '';
    if (/^\d{4}-\d{2}-\d{2}/.test(s)) return s.slice(0, 10);
    const d = new Date(s);
    if (Number.isNaN(d.getTime())) return '';
    return formatGregorianYmd(d.getFullYear(), d.getMonth() + 1, d.getDate());
}

export function formatGregorianYmd(gy: number, gm: number, gd: number): string {
    return `${gy}-${String(gm).padStart(2, '0')}-${String(gd).padStart(2, '0')}`;
}

export type GregorianParts = { gy: number; gm: number; gd: number };

/** Parse Gregorian `YYYY-MM-DD`; returns null if not a real calendar day. */
export function parseGregorianYmd(input: string): GregorianParts | null {
    const t = input.trim();
    const m = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(t);
    if (!m) return null;
    const gy = parseInt(m[1]!, 10);
    const gm = parseInt(m[2]!, 10);
    const gd = parseInt(m[3]!, 10);
    if (!Number.isFinite(gy) || !Number.isFinite(gm) || !Number.isFinite(gd)) return null;
    if (gm < 1 || gm > 12 || gd < 1 || gd > 31) return null;
    const dt = new Date(gy, gm - 1, gd);
    if (dt.getFullYear() !== gy || dt.getMonth() !== gm - 1 || dt.getDate() !== gd) return null;
    return { gy, gm, gd };
}

export function formatHijriYmd(h: HijriParts): string {
    return `${h.hy}-${String(h.hm).padStart(2, '0')}-${String(h.hd).padStart(2, '0')}`;
}

/** Parse Hijri `YYYY-MM-DD` (year-month-day). Returns null if incomplete or invalid pattern. */
export function parseHijriYmd(input: string): HijriParts | null {
    const t = input.trim();
    const m = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(t);
    if (!m) return null;
    const hy = parseInt(m[1]!, 10);
    const hm = parseInt(m[2]!, 10);
    const hd = parseInt(m[3]!, 10);
    if (!Number.isFinite(hy) || !Number.isFinite(hm) || !Number.isFinite(hd)) return null;
    if (hy < 1 || hm < 1 || hm > 12 || hd < 1 || hd > 30) return null;
    return { hy, hm, hd };
}

function hijriPartsEqual(a: HijriParts, b: HijriParts): boolean {
    return a.hy === b.hy && a.hm === b.hm && a.hd === b.hd;
}

/**
 * Convert Hijri (Umm al-Qura) to Gregorian `YYYY-MM-DD`.
 * Returns null if out of range or round-trip does not match (invalid calendar date).
 */
export function hijriPartsToGregorianYmd(parts: HijriParts): string | null {
    try {
        const g = toGregorian(parts.hy, parts.hm, parts.hd) as { gy: number; gm: number; gd: number };
        if (
            !Number.isFinite(g.gy) ||
            !Number.isFinite(g.gm) ||
            !Number.isFinite(g.gd) ||
            g.gm < 1 ||
            g.gm > 12 ||
            g.gd < 1 ||
            g.gd > 31
        ) {
            return null;
        }
        const ymd = formatGregorianYmd(g.gy, g.gm, g.gd);
        const back = toHijri(g.gy, g.gm, g.gd) as HijriParts;
        if (!hijriPartsEqual(back, parts)) return null;
        return ymd;
    } catch {
        return null;
    }
}

/** Gregorian `YYYY-MM-DD` → Hijri `YYYY-MM-DD` string for display, or '' if invalid / empty. */
export function gregorianYmdToHijriYmd(ymd: string): string {
    const t = ymd.trim();
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(t);
    if (!m) return '';
    const gy = parseInt(m[1]!, 10);
    const gm = parseInt(m[2]!, 10);
    const gd = parseInt(m[3]!, 10);
    try {
        const h = toHijri(gy, gm, gd) as HijriParts;
        if (!h || !Number.isFinite(h.hy)) return '';
        return formatHijriYmd(h);
    } catch {
        return '';
    }
}

/** Split `datetime-local` value into date and time segments. */
export function splitDatetimeLocal(value: string): { date: string; time: string } {
    if (!value || !value.includes('T')) return { date: '', time: '' };
    const [date, time = ''] = value.split('T');
    return { date: date ?? '', time: time.slice(0, 5) };
}

export function joinDatetimeLocal(dateYmd: string, timeHm: string): string {
    if (!dateYmd) return '';
    const t = timeHm && /^\d{2}:\d{2}$/.test(timeHm) ? timeHm : '00:00';
    return `${dateYmd}T${t}`;
}

/** Number of days in Hijri month (Umm al-Qura), or 0 if month is outside the table. */
export function hijriMonthLength(hy: number, hm: number): number {
    for (let d = 30; d >= 1; d--) {
        if (hijriPartsToGregorianYmd({ hy, hm, hd: d })) return d;
    }
    return 0;
}

/** First Gregorian weekday (0 = Sunday) for Hijri `hy-hm-01`. */
export function hijriFirstOfMonthWeekday0Sun(hy: number, hm: number): number | null {
    const ymd = hijriPartsToGregorianYmd({ hy, hm, hd: 1 });
    if (!ymd) return null;
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(ymd);
    if (!m) return null;
    const gy = parseInt(m[1]!, 10);
    const gmo = parseInt(m[2]!, 10) - 1;
    const gd = parseInt(m[3]!, 10);
    return new Date(gy, gmo, gd).getDay();
}
