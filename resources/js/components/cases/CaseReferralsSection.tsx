import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { CrudTable } from '@/components/CrudTable';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { TableColumn } from '@/types/crud';
import { hasPermission } from '@/utils/authorization';
import { router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { CaseReferralFormModal } from '@/components/cases/CaseReferralFormModal';
import type { PageProps } from '@/types/page-props';

type ReferralRow = any;

export function CaseReferralsSection({
    caseId,
    referrals,
    courts,
    courtTypes = [],
    circleTypes = [],
    permissions,
}: {
    caseId: number;
    referrals: any;
    courts: any[];
    courtTypes?: unknown[];
    circleTypes?: unknown[];
    permissions: string[];
}) {
    const { t } = useTranslation();
    const { caseReferralStageDefs } = usePage<PageProps>().props;

    const referralStages = useMemo(
        () =>
            caseReferralStageDefs.map((s) => ({
                ...s,
                label: t(`case_referral.stage.${s.key}`),
            })),
        [caseReferralStageDefs, t],
    );

    const [isOpen, setIsOpen] = useState(false);
    const [isViewOpen, setIsViewOpen] = useState(false);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [mode, setMode] = useState<'create' | 'edit'>('create');
    const [current, setCurrent] = useState<ReferralRow | null>(null);

    const stageMeta = useMemo(() => Object.fromEntries(referralStages.map((s) => [s.key, s])), [referralStages]);
    const rows = referrals?.data || [];

    const openCreate = () => {
        setMode('create');
        setCurrent(null);
        setIsOpen(true);
    };

    const openEdit = (row: any) => {
        setMode('edit');
        setCurrent(row);
        setIsOpen(true);
    };

    const submitDelete = () => {
        if (!current) return;
        router.delete(route('cases.referrals.destroy', [caseId, current.id]), {
            preserveScroll: true,
            onSuccess: () => {
                setIsDeleteOpen(false);
                toast.success(t('case_referral.toast.deleted'));
            },
        });
    };

    const referralTableColumns = useMemo<TableColumn[]>(
        () => [
            {
                key: 'stage',
                label: t('case_referral.table.stage'),
                render: (_value: unknown, row: any) => (
                    <Badge className={stageMeta[row.stage]?.badgeClass || ''}>{stageMeta[row.stage]?.label || row.stage}</Badge>
                ),
            },
            {
                key: 'referral_date',
                label: t('case_referral.table.referral_date'),
                type: 'date',
            },
            {
                key: 'stage_case_number',
                label: t('case_referral.table.case_number'),
                render: (value: unknown) => (value == null || value === '' ? <span className="text-muted-foreground">—</span> : String(value)),
            },
            {
                key: 'stage_court_name',
                label: t('case_referral.table.court_name'),
                render: (value: unknown) => (value == null || value === '' ? <span className="text-muted-foreground">—</span> : String(value)),
            },
        ],
        [stageMeta, t],
    );

    const handleReferralTableAction = (action: string, row: any) => {
        if (action === 'view') {
            setCurrent(row);
            setIsViewOpen(true);
            return;
        }
        if (action === 'edit') {
            openEdit(row);
            return;
        }
        if (action === 'delete') {
            setCurrent(row);
            setIsDeleteOpen(true);
        }
    };

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{t('case_referral.section_title')}</h3>
                {hasPermission(permissions, 'manage-cases') && (
                    <button
                        type="button"
                        onClick={openCreate}
                        className="flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                    >
                        <Plus className="h-4 w-4" />
                        {t('case_referral.add_referral')}
                    </button>
                )}
            </div>

            <CrudTable
                columns={referralTableColumns}
                actions={[
                    { label: t('View'), icon: 'Eye', action: 'view', className: 'text-primary' },
                    { label: t('Edit'), icon: 'Edit', action: 'edit', className: 'text-amber-500' },
                    { label: t('Delete'), icon: 'Trash2', action: 'delete', className: 'text-red-500' },
                ]}
                data={rows}
                from={referrals?.from ?? 1}
                onAction={handleReferralTableAction}
                permissions={permissions}
                entityPermissions={{
                    view: 'manage-cases',
                    edit: 'manage-cases',
                    delete: 'manage-cases',
                }}
            />

            <CaseReferralFormModal
                caseId={caseId}
                open={isOpen}
                onClose={() => setIsOpen(false)}
                mode={mode}
                editRow={mode === 'edit' ? current : null}
                courts={courts}
                courtTypes={courtTypes}
                circleTypes={circleTypes}
                permissions={permissions}
            />

            <Dialog open={isViewOpen} onOpenChange={setIsViewOpen}>
                <DialogContent className="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>{t('case_referral.view_title')}</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-2 text-sm">
                        {current?.stage && (
                            <p>
                                <strong>{t('case_referral.label.stage')}</strong> {stageMeta[current.stage]?.label}
                            </p>
                        )}
                        {current?.referral_date && (
                            <p>
                                <strong>{t('case_referral.label.referral_date_row')}</strong>{' '}
                                {window.appSettings?.formatDate?.(current.referral_date) || current.referral_date}
                            </p>
                        )}
                        {current?.notes && (
                            <p>
                                <strong>{t('case_referral.label.notes_row')}</strong> {current.notes}
                            </p>
                        )}
                    </div>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setIsViewOpen(false)}>
                            {t('Close')}
                        </Button>
                        {current && (
                            <Button
                                onClick={() => {
                                    setIsViewOpen(false);
                                    openEdit(current);
                                }}
                            >
                                {t('Edit')}
                            </Button>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <CrudDeleteModal
                isOpen={isDeleteOpen}
                onClose={() => setIsDeleteOpen(false)}
                onConfirm={submitDelete}
                itemName={current?.id ? `#${current.id}` : t('case_referral.delete.item_fallback')}
                entityName={t('case_referral.delete.entity')}
                warningMessage={t('case_referral.delete.confirm')}
            />

        </div>
    );
}
