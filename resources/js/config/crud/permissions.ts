// config/crud/permissions.ts
import { CrudConfig } from '@/types/crud';
import { columnRenderers } from '@/utils/columnRenderers';
import { t } from '@/utils/i18n';

export const permissionsConfig: CrudConfig = {
  entity: {
    name: 'permissions',
    endpoint: route('permissions.index'),
    permissions: {
      view: 'view-permissions',
      create: 'create-permissions',
      edit: 'edit-permissions',
      delete: 'delete-permissions'
    }
  },
  description: t('Manage system permissions for different modules'),
  table: {
    columns: [
      { 
        key: 'module', 
        label: t('Module'), 
        sortable: true,
        render: columnRenderers.status({
          [t('Products')]: 'bg-blue-100 text-blue-800',
          [t('Categories')]: 'bg-green-100 text-green-800',
          [t('Contacts')]: 'bg-purple-100 text-purple-800',
          [t('Permissions')]: 'bg-amber-100 text-amber-800',
          [t('Roles')]: 'bg-red-100 text-red-800',
          [t('Users')]: 'bg-indigo-100 text-indigo-800'
        })
      },
      { key: 'name', label: t('Name'), sortable: true },
      { key: 'label', label: t('Label'), sortable: true },
      { key: 'description', label: t('Description') },
      { 
        key: 'created_at', 
        label: t('Created At'), 
        sortable: true, 
        type: 'date',
      }
    ],
    actions: [
      { 
        label: t('View'), 
        icon: 'Eye', 
        action: 'view', 
        className: 'text-blue-500',
        requiredPermission: 'view-permissions'
      },
      { 
        label: t('Edit'), 
        icon: 'Edit', 
        action: 'edit', 
        className: 'text-amber-500',
        requiredPermission: 'edit-permissions'
      },
      { 
        label: t('Delete'), 
        icon: 'Trash2', 
        action: 'delete', 
        className: 'text-red-500',
        requiredPermission: 'delete-permissions'
      }
    ]
  },
  filters: [
    {
      key: 'module',
      label: t('Module'),
      type: 'select',
      options: []
    }
  ],
  form: {
    fields: [
      { name: 'module', label: t('Module'), type: 'text', required: true },
      {
        name: 'label.en',
        label: t('Label (English)'),
        type: 'text',
        required: true,
        description: t('The name field will be automatically generated from this label')
      },
      {
        name: 'label.ar',
        label: t('Label (Arabic)'),
        type: 'text',
        required: true
      },
      {
        name: 'description.en',
        label: t('Description (English)'),
        type: 'textarea'
      },
      {
        name: 'description.ar',
        label: t('Description (Arabic)'),
        type: 'textarea'
      }
    ],
    transformData: (data: any) => {
      const transformed: any = { ...data };
      if (transformed['label.en'] != null || transformed['label.ar'] != null) {
        transformed.label = {
          en: transformed['label.en'] ?? '',
          ar: transformed['label.ar'] ?? ''
        };
        delete transformed['label.en'];
        delete transformed['label.ar'];
      }
      if (transformed['description.en'] != null || transformed['description.ar'] != null) {
        transformed.description = {
          en: transformed['description.en'] ?? '',
          ar: transformed['description.ar'] ?? ''
        };
        delete transformed['description.en'];
        delete transformed['description.ar'];
      }
      return transformed;
    }
  }
};