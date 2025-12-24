// config/crud/countries.ts
import { CrudConfig } from '@/types/crud';
import { t } from '@/utils/i18n';

export const countriesConfig: CrudConfig = {
    entity: {
        name: 'countries',
        endpoint: route('countries.index'),
        permissions: {
            view: 'manage-countries',
            create: 'manage-countries',
            edit: 'manage-countries',
            delete: 'manage-countries',
        },
        breadcrumbs: [
            {
                title: t('Countries'),
            },
        ],
    },
    table: {
        columns: [
            {
                key: 'name',
                label: t('Name'),
                sortable: true,
            },
            {
                key: 'nationality_name',
                label: t('Nationality Name'),
                sortable: true,
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
                requiredPermission: 'manage-countries',
            },
            {
                label: t('Delete'),
                icon: 'Trash2',
                action: 'delete',
                className: 'text-red-500',
                requiredPermission: 'manage-countries',
            },
        ],
    },
    filters: [],
    form: {
        fields: [
            {
                name: 'name.en',
                label: t('Country Name (English)'),
                type: 'text',
                required: true,
            },
            {
                name: 'name.ar',
                label: t('Country Name (Arabic)'),
                type: 'text',
                required: true,
            },
            {
                name: 'nationality_name.en',
                label: t('Nationality Name (English)'),
                type: 'text',
            },
            {
                name: 'nationality_name.ar',
                label: t('Nationality Name (Arabic)'),
                type: 'text',
            },
            {
                name: 'is_active',
                label: t('Active'),
                type: 'checkbox',
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

            // Handle nationality_name field
            if (transformed['nationality_name.en'] || transformed['nationality_name.ar']) {
                transformed.nationality_name = {
                    en: transformed['nationality_name.en'] || '',
                    ar: transformed['nationality_name.ar'] || '',
                };
                delete transformed['nationality_name.en'];
                delete transformed['nationality_name.ar'];
            }

            return transformed;
        },
    },
};

