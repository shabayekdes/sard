import { SearchableSelect, type SearchableSelectOption } from '@/components/forms/searchable-select';
import { GregorianHijriDateField } from '@/components/GregorianHijriDateField';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import {
    AUTHORITY_COURT_TYPES,
    AUTHORITY_TYPE_KEYS,
    type AuthorityTypeDetails,
} from '@/lib/case-authority-type';
import { useLayout } from '@/contexts/LayoutContext';
import { Check, ChevronDown, Plus } from 'lucide-react';
import { useMemo } from 'react';
import { useTranslation } from 'react-i18next';

const COURT_SET = new Set(AUTHORITY_COURT_TYPES);

type CaseAuthorityFieldsProps = {
    authorityType: string;
    courtId: string;
    details: AuthorityTypeDetails;
    onAuthorityTypeChange: (v: string) => void;
    onCourtIdChange: (v: string) => void;
    onDetailsChange: (next: AuthorityTypeDetails) => void;
    /** Courts from server: [id, name] tuples with optional null first row. */
    courts: (string | number | [string | number | null, string] | { id: unknown; name: unknown })[];
    canQuickCreateCourt: boolean;
    onAddCourtClick: () => void;
    errors?: { authority_type?: string; court_id?: string; authority_type_details?: string };
};

function toSearchableOptions(
    courts: CaseAuthorityFieldsProps['courts'],
): SearchableSelectOption[] {
    const list = Array.isArray(courts) ? courts : [];
    const out: SearchableSelectOption[] = [];
    for (const row of list) {
        if (Array.isArray(row)) {
            const [id, name] = row;
            out.push({ id: id != null && id !== '' ? String(id) : '', name: String(name ?? '') });
        } else if (row && typeof row === 'object' && 'id' in row && 'name' in row) {
            const id = (row as { id: unknown }).id;
            out.push({ id: id != null && id !== '' ? String(id) : '', name: String((row as { name: unknown }).name ?? '') });
        } else {
            out.push({ id: '', name: String(row ?? '') });
        }
    }
    return out;
}

export function CaseAuthorityFields({
    authorityType,
    courtId,
    details,
    onAuthorityTypeChange,
    onCourtIdChange,
    onDetailsChange,
    courts,
    canQuickCreateCourt,
    onAddCourtClick,
    errors,
}: CaseAuthorityFieldsProps) {
    const { t } = useTranslation();
    const { isRtl } = useLayout();
    const dir = isRtl ? 'rtl' : 'ltr';

    const courtOptions = useMemo(() => toSearchableOptions(courts), [courts]);
    const selectedKey = authorityType || '';

    const setDetail = (k: keyof AuthorityTypeDetails, v: string) => {
        onDetailsChange({ ...details, [k]: v });
    };

    const typeButtonLabel = selectedKey
        ? t(`authority_type_label_${selectedKey}`)
        : t('Select entity type');

    return (
        <div className="col-span-full w-full min-w-0 space-y-0 md:col-span-3">
            <div
                className={cn(
                    'w-full min-w-0 rounded-lg border border-slate-200 bg-slate-50/40 p-4 dark:border-gray-800 dark:bg-gray-900/20',
                )}
            >
                <h3 className="mb-3 text-base font-semibold text-foreground">{t('Entity type')}</h3>
                <div className="w-full min-w-0 space-y-2">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                type="button"
                                variant="outline"
                                className={cn(
                                    'h-10 w-full min-w-0 justify-between font-normal',
                                    !selectedKey && 'text-muted-foreground',
                                    errors?.authority_type && 'border-destructive',
                                )}
                                dir={dir}
                                aria-label={t('Select entity type')}
                            >
                                <span className="min-w-0 flex-1 truncate text-start">{typeButtonLabel}</span>
                                <ChevronDown className="h-4 w-4 shrink-0 opacity-60" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            className={cn(
                                'max-h-72 w-[var(--radix-dropdown-menu-trigger-width)] min-w-0 overflow-y-auto p-1',
                                isRtl && '[direction:rtl]',
                            )}
                            align={isRtl ? 'end' : 'start'}
                            sideOffset={4}
                        >
                            <DropdownMenuItem
                                className="cursor-pointer"
                                onSelect={() => onAuthorityTypeChange('')}
                            >
                                <span className="flex w-full min-w-0 items-center justify-between gap-2">
                                    <span className="min-w-0 text-start text-muted-foreground">
                                        {t('Select entity type')}
                                    </span>
                                    {!selectedKey ? <Check className="h-4 w-4 shrink-0" /> : null}
                                </span>
                            </DropdownMenuItem>
                            {AUTHORITY_TYPE_KEYS.map((k) => (
                                <DropdownMenuItem
                                    key={k}
                                    className="cursor-pointer"
                                    onSelect={() => onAuthorityTypeChange(k)}
                                >
                                    <span className="flex w-full min-w-0 items-center justify-between gap-2">
                                        <span className="min-w-0 break-words text-start">{t(`authority_type_label_${k}`)}</span>
                                        {selectedKey === k ? <Check className="h-4 w-4 shrink-0" /> : null}
                                    </span>
                                </DropdownMenuItem>
                            ))}
                        </DropdownMenuContent>
                    </DropdownMenu>
                    {errors?.authority_type ? (
                        <p className="text-sm text-destructive">{errors.authority_type}</p>
                    ) : null}
                </div>

                {selectedKey ? (
                    <div className="mt-4 space-y-4 border-t border-border pt-4">
                        {COURT_SET.has(selectedKey as (typeof AUTHORITY_COURT_TYPES)[number]) && (
                            <div className="grid w-full min-w-0 max-w-2xl grid-cols-1 items-end gap-2 md:grid-cols-[minmax(0,1fr)_auto]">
                                <div className="min-w-0 space-y-2">
                                    <Label>{t('The court')}</Label>
                                    <SearchableSelect
                                        value={courtId ?? ''}
                                        onValueChange={onCourtIdChange}
                                        placeholder={t('Select Court')}
                                        searchPlaceholder={t('Search...')}
                                        emptyMessage={t('No results')}
                                        options={courtOptions}
                                        error={errors?.court_id}
                                    />
                                </div>
                                {canQuickCreateCourt && (
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        className="h-10 w-10 shrink-0"
                                        title={t('Add Court')}
                                        aria-label={t('Add Court')}
                                        onClick={onAddCourtClick}
                                    >
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                )}
                            </div>
                        )}

                        {['committee', 'prosecution', 'police', 'prisons', 'other'].includes(selectedKey) && (
                            <div className="max-w-2xl space-y-2">
                                <Label htmlFor="authority_entity_name">{t('Entity name')}</Label>
                                <Input
                                    id="authority_entity_name"
                                    value={details.entity_name}
                                    onChange={(e) => setDetail('entity_name', e.target.value)}
                                    placeholder={t('Enter entity name')}
                                />
                            </div>
                        )}

                        {selectedKey === 'reconciliation' && (
                            <div className="grid max-w-4xl grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="reconciliation_suit_number">{t('Reconciliation claim number')}</Label>
                                    <Input
                                        id="reconciliation_suit_number"
                                        value={details.reconciliation_suit_number}
                                        onChange={(e) => setDetail('reconciliation_suit_number', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>{t('Reconciliation claim date')}</Label>
                                    <GregorianHijriDateField
                                        id="reconciliation_suit_date"
                                        value={details.reconciliation_suit_date}
                                        onChange={(v) => setDetail('reconciliation_suit_date', v)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="reconciliation_report_number">{t('Reconciliation report number')}</Label>
                                    <Input
                                        id="reconciliation_report_number"
                                        value={details.reconciliation_report_number}
                                        onChange={(e) => setDetail('reconciliation_report_number', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>{t('Reconciliation report date')}</Label>
                                    <GregorianHijriDateField
                                        id="reconciliation_report_date"
                                        value={details.reconciliation_report_date}
                                        onChange={(v) => setDetail('reconciliation_report_date', v)}
                                    />
                                </div>
                            </div>
                        )}

                        {selectedKey === 'amicable_settlement' && (
                            <div className="grid max-w-2xl grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="amicable_suit_number">{t('Claim number')}</Label>
                                    <Input
                                        id="amicable_suit_number"
                                        value={details.amicable_suit_number}
                                        onChange={(e) => setDetail('amicable_suit_number', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>{t('Claim date')}</Label>
                                    <GregorianHijriDateField
                                        id="amicable_suit_date"
                                        value={details.amicable_suit_date}
                                        onChange={(v) => setDetail('amicable_suit_date', v)}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                ) : null}
            </div>
        </div>
    );
}
