export const AUTHORITY_COURT_TYPES = ['courts_general', 'courts_administrative'] as const;

export const AUTHORITY_TYPE_KEYS = [
    'courts_general',
    'courts_administrative',
    'committee',
    'prosecution',
    'police',
    'prisons',
    'reconciliation',
    'amicable_settlement',
    'other',
] as const;

export type AuthorityTypeKey = (typeof AUTHORITY_TYPE_KEYS)[number];

export const emptyAuthorityTypeDetails = () => ({
    entity_name: '',
    reconciliation_suit_number: '',
    reconciliation_suit_date: '',
    reconciliation_report_number: '',
    reconciliation_report_date: '',
    amicable_suit_number: '',
    amicable_suit_date: '',
});

export type AuthorityTypeDetails = ReturnType<typeof emptyAuthorityTypeDetails>;

export function mergeAuthorityTypeDetails(
    raw: unknown,
): AuthorityTypeDetails {
    const b = emptyAuthorityTypeDetails();
    if (raw && typeof raw === 'object' && !Array.isArray(raw)) {
        const o = raw as Record<string, string>;
        for (const k of Object.keys(b) as (keyof AuthorityTypeDetails)[]) {
            if (o[k] != null && o[k] !== undefined) b[k] = String(o[k]);
        }
    }
    return b;
}
