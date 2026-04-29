import { CrudFormModal } from '@/components/CrudFormModal';
import { toast } from '@/components/custom-toast';
import type { FormField } from '@/types/crud';
import { router } from '@inertiajs/react';
import { useMemo } from 'react';
import { useTranslation } from 'react-i18next';

export type QuickCourtFormModalProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    courtTypes: unknown[];
    circleTypes: unknown[];
    /** Called with the new court id after a successful create (from flash). */
    onCreated?: (courtId: string) => void;
    /**
     * Optional partial Inertia reload after success (e.g. `['courts']` on case show).
     * Omit on case create/edit so the page keeps current form state.
     */
    reloadOnly?: string[];
    title?: string;
    columns?: number;
};

function resolveTypeLabel(type: any, currentLocale: string): string {
    if (!type) return '';
    let displayName = type.name;
    if (typeof type.name === 'object' && type.name !== null) {
        displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
    } else if (type.name_translations && typeof type.name_translations === 'object') {
        displayName =
            type.name_translations[currentLocale] || type.name_translations.en || type.name_translations.ar || '';
    }
    return String(displayName || '');
}

/**
 * Reusable “quick add court” modal (same fields as case create/edit).
 * Posts to `courts.store` and reads `created_court_id` from Inertia flash.
 */
export function QuickCourtFormModal({
    open,
    onOpenChange,
    courtTypes,
    circleTypes,
    onCreated,
    reloadOnly,
    title,
    columns = 2,
}: QuickCourtFormModalProps) {
    const { t, i18n } = useTranslation();
    const currentLocale = i18n.language || 'en';

    const formConfig = useMemo(() => {
        const typeOptions = (courtTypes || []).map((type: any) => ({
            value: type.id.toString(),
            label: resolveTypeLabel(type, currentLocale),
        }));
        const circleOptions = (circleTypes || []).map((type: any) => ({
            value: type.id.toString(),
            label: resolveTypeLabel(type, currentLocale),
        }));
        const fields: FormField[] = [
            { name: 'name', label: t('Court Name'), type: 'text', required: true },
            {
                name: 'court_type_id',
                label: t('Court Type'),
                type: 'select',
                required: true,
                options: typeOptions,
            },
            {
                name: 'circle_type_id',
                label: t('Circle Type'),
                type: 'select',
                required: true,
                options: circleOptions,
            },
            { name: 'address', label: t('Address'), type: 'textarea' },
            { name: 'notes', label: t('Notes'), type: 'textarea' },
            {
                name: 'status',
                label: t('Status'),
                type: 'select',
                options: [
                    { value: 'active', label: t('Active') },
                    { value: 'inactive', label: t('Inactive') },
                ],
                defaultValue: 'active',
            },
        ];
        return {
            fields,
            modalSize: 'xl' as const,
        };
    }, [courtTypes, circleTypes, currentLocale, t]);

    const handleSubmit = (courtForm: Record<string, unknown>) => {
        router.post(route('courts.store'), courtForm, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                onOpenChange(false);
                toast.dismiss();
                const flash = (page as any)?.props?.flash;
                if (flash?.created_court_id != null && onCreated) {
                    onCreated(String(flash.created_court_id));
                }
                if (flash?.success) {
                    toast.success(flash.success);
                }
                if (flash?.warning) {
                    toast.message(flash.warning);
                }
                if (flash?.error) {
                    toast.error(flash.error);
                }
                if (reloadOnly?.length) {
                    router.reload({ only: reloadOnly });
                }
            },
            onError: (formErrors) => {
                toast.dismiss();
                if (typeof formErrors === 'string') {
                    toast.error(formErrors);
                } else if (Object.values(formErrors).length > 0) {
                    toast.error(
                        t('Failed to create {{model}}: {{errors}}', {
                            model: t('Court'),
                            errors: Object.values(formErrors).join(', '),
                        }),
                    );
                }
            },
        });
    };

    return (
        <CrudFormModal
            isOpen={open}
            onClose={() => onOpenChange(false)}
            onSubmit={handleSubmit}
            formConfig={{ ...formConfig, columns }}
            initialData={{}}
            title={title ?? t('Add New Court')}
            mode="create"
        />
    );
}
