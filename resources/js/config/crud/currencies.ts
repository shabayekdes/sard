// config/crud/currencies.ts
import { CrudConfig } from '@/types/crud';
import { t } from '@/utils/i18n';

export const currenciesConfig: CrudConfig = {
    entity: {
        name: 'currencies',
        endpoint: route('currencies.index'),
        permissions: {
            view: 'manage-currencies',
            create: 'manage-currencies',
            edit: 'manage-currencies',
            delete: 'manage-currencies',
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
                key: 'code',
                label: t('Code'),
                sortable: true,
            },
            {
                key: 'symbol',
                label: t('Symbol'),
                sortable: true,
            },
            {
                key: 'description',
                label: t('Description'),
            },
        ],
        actions: [
            {
                label: t('Edit'),
                icon: 'Edit',
                action: 'edit',
                className: 'text-amber-500',
                requiredPermission: 'manage-currencies',
            },
            {
                label: t('Delete'),
                icon: 'Trash2',
                action: 'delete',
                className: 'text-red-500',
                requiredPermission: 'manage-currencies',
                condition: (row) => !row.is_default, // Don't allow deleting default currency
            },
        ],
    },
    filters: [],
    form: {
        fields: [
            {
                name: 'name.en',
                label: t('Currency Name (English)'),
                type: 'text',
                required: true,
            },
            {
                name: 'name.ar',
                label: t('Currency Name (Arabic)'),
                type: 'text',
                required: true,
            },
            {
                name: 'code',
                label: t('Currency Code'),
                type: 'text',
                required: true,
                placeholder: 'e.g. USD, EUR, GBP',
            },
            {
                name: 'symbol',
                label: t('Currency Symbol'),
                type: 'text',
                required: true,
                placeholder: 'e.g. $, €, £',
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
        ],
        transformData: (data: any) => {
            // Transform flat structure to nested structure for translatable fields
            const transformed: any = { ...data };

            // Handle name field
            if (transformed['name.en'] || transformed['name.ar']) {
                transformed.name = {
                    en: transformed['name.en'] || '',
                    ar: transformed['name.ar'] || '',
                };
                delete transformed['name.en'];
                delete transformed['name.ar'];
            }

            // Handle description field
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