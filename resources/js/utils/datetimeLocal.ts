/** Format API/ISO datetime for `<input type="datetime-local" />` (local timezone, no seconds). */
export function toDatetimeLocalInputValue(value: unknown): string {
    if (value == null || value === '') {
        return '';
    }
    const d = value instanceof Date ? value : new Date(String(value));
    if (Number.isNaN(d.getTime())) {
        return '';
    }
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
