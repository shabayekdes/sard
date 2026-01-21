import { CrudConfig } from '@/types/crud';
import { t } from '@/utils/i18n';

export const taxRatesConfig: CrudConfig = {
    entity: {
        name: 'taxRates',
        endpoint: route('tax-rates.index'),
        permissions: {
            view: 'manage-tax-rates',
            create: 'manage-tax-rates',
            edit: 'manage-tax-rates',
            delete: 'manage-tax-rates',
        },
    },
    table: {
        columns: [
            {
                key: 'name',
                label: t('Name'),
                sortable: true,
            },
            {
                key: 'rate',
                label: t('Rate (%)'),
                sortable: true,
            },
            {
                key: 'description',
                label: t('Description'),
            },
            {
                key: 'is_active',
                label: t('Active'),
                type: 'boolean',
            },
        ],
        actions: [
            {
                label: t('Edit'),
                icon: 'Edit',
                action: 'edit',
                className: 'text-amber-500',
                requiredPermission: 'manage-tax-rates',
            },
            {
                label: t('Delete'),
                icon: 'Trash2',
                action: 'delete',
                className: 'text-red-500',
                requiredPermission: 'manage-tax-rates',
            },
        ],
    },
    filters: [],
    form: {
        fields: [
            {
                name: 'name.en',
                label: t('Tax Rate Name (English)'),
                type: 'text',
                required: true,
            },
            {
                name: 'name.ar',
                label: t('Tax Rate Name (Arabic)'),
                type: 'text',
                required: true,
            },
            {
                name: 'rate',
                label: t('Rate (%)'),
                type: 'number',
                required: true,
                step: '0.01',
                min: '0',
                max: '100',
            },
            {
                name: 'description.en',
                label: t('Description (English)'),
                type: 'textarea',
            },
            {
                name: 'description.ar',
                label: t('Description (Arabic)'),
                type: 'textarea',
            },
            {
                name: 'is_active',
                label: t('Active'),
                type: 'checkbox',
            },
        ],
        transformData: (data: any) => {
            const transformed: any = { ...data };

            if (transformed['name.en'] || transformed['name.ar']) {
                transformed.name = {
                    en: transformed['name.en'] || '',
                    ar: transformed['name.ar'] || '',
                };
                delete transformed['name.en'];
                delete transformed['name.ar'];
            }

            if (transformed['description.en'] || transformed['description.ar']) {
                transformed.description = {
                    en: transformed['description.en'] || '',
                    ar: transformed['description.ar'] || '',
                };
                delete transformed['description.en'];
                delete transformed['description.ar'];
            }

            return transformed;
        },
    },
};
