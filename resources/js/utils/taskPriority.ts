/**
 * i18n keys match App\Enums\TaskPriority case names: TASK_PRIORITY_LOW, etc.
 * Stored/API values remain lowercase (low, medium, high, critical).
 */
export function taskPriorityTranslationKey(priority: string | undefined | null): string {
    const normalized = (priority ?? 'medium').toLowerCase();
    const suffixes: Record<string, string> = {
        low: 'LOW',
        medium: 'MEDIUM',
        high: 'HIGH',
        critical: 'CRITICAL',
    };
    const suffix = suffixes[normalized] ?? normalized.toUpperCase();
    return `TASK_PRIORITY_${suffix}`;
}
