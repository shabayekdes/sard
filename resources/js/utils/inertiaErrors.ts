/** Normalize Inertia / Laravel validation errors to flat string map for inline fields. */
export function normalizeInertiaValidationErrors(errors: unknown): Record<string, string> {
    if (!errors || typeof errors !== 'object' || Array.isArray(errors)) {
        return {};
    }
    const out: Record<string, string> = {};
    Object.entries(errors as Record<string, unknown>).forEach(([k, v]) => {
        if (Array.isArray(v)) {
            out[k] = String(v[0] ?? '');
        } else if (typeof v === 'string') {
            out[k] = v;
        }
    });
    return out;
}
