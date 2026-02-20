// components/PageCrudWrapper.tsx
import { useState, useEffect, ReactNode } from 'react';
import { PageTemplate, PageAction } from '@/components/page-template';
import { PlusIcon } from 'lucide-react';
import { router, usePage } from '@inertiajs/react';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from './CrudTable';
import { CrudFormModal } from './CrudFormModal';
import { CrudDeleteModal } from './CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { CrudConfig } from '@/types/crud';
import { BreadcrumbItem } from '@/types';
import { useTranslation } from 'react-i18next';

export interface CrudButton {
  label: string;
  icon?: ReactNode;
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
  onClick?: () => void;
  permission?: string;
  className?: string;
  showAddButton?: boolean;
}

interface PageCrudWrapperProps {
  config: CrudConfig;
  title?: string;
  url: string;
  buttons?: CrudButton[];
  breadcrumbs?: BreadcrumbItem[];
}

export function PageCrudWrapper({
  config,
  title,
  url,
  buttons = [],
  breadcrumbs
}: PageCrudWrapperProps) {
  const { t } = useTranslation();
  const { entity, table, filters = [], form, hooks } = config;
  const { auth, ...pageProps } = usePage().props as any;
  const permissions = auth?.permissions || [];

  // Get data from page props using entity name
  const data = pageProps[entity.name] || { data: [], links: [] };
  const pageFilters = pageProps.filters || {};

  // State
  const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
  const [filterValues, setFilterValues] = useState<Record<string, any>>({});
  const [showFilters, setShowFilters] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

  // Initialize filter values from URL
  useEffect(() => {
    const initialFilters: Record<string, any> = {};
    filters.forEach(filter => {
      const filterKey = filter.key;
      initialFilters[filterKey] = pageFilters[filterKey] || '';
    });
    setFilterValues(initialFilters);
  }, []);

  // Reload data when language changes
  useEffect(() => {
    const handleLanguageChange = () => {
      // Reload the current page with current filters to get translated data
      const params: any = { page: pageFilters.page || 1 };

      if (searchTerm) {
        params.search = searchTerm;
      }

      // Add filter values to params
      Object.entries(filterValues).forEach(([key, value]) => {
        if (value && value !== '') {
          params[key] = value;
        }
      });

      // Add sorting params
      if (pageFilters.sort_field) {
        params.sort_field = pageFilters.sort_field;
      }
      if (pageFilters.sort_direction) {
        params.sort_direction = pageFilters.sort_direction;
      }

      // Add per_page if it exists
      if (pageFilters.per_page) {
        params.per_page = pageFilters.per_page;
      }

      router.get(entity.endpoint, params, {
        preserveState: false,
        preserveScroll: false,
        only: [entity.name, 'filters']
      });
    };

    // Listen for language change event
    window.addEventListener('languageChanged', handleLanguageChange);

    // Cleanup listener on unmount
    return () => {
      window.removeEventListener('languageChanged', handleLanguageChange);
    };
  }, [searchTerm, filterValues, pageFilters, entity.endpoint, entity.name, filters]);

  // Check if any filters are active
  const hasActiveFilters = () => {
    return Object.entries(filterValues).some(([key, value]) => {
      return value && value !== '';
    }) || searchTerm !== '';
  };

  // Count active filters
  const activeFilterCount = () => {
    return Object.entries(filterValues).filter(([key, value]) => {
      return value && value !== '';
    }).length + (searchTerm ? 1 : 0);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const applyFilters = () => {
    const params: any = { page: 1 };

    if (searchTerm) {
      params.search = searchTerm;
    }

    // Add filter values to params
    Object.entries(filterValues).forEach(([key, value]) => {
      if (value && value !== '') {
        params[key] = value;
      }
    });

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(entity.endpoint, params, { preserveState: true, preserveScroll: true });
  };

  const handleFilterChange = (key: string, value: any) => {
    setFilterValues(prev => ({ ...prev, [key]: value }));

    const params: any = { page: 1 };

    if (searchTerm) {
      params.search = searchTerm;
    }

    // Add all current filter values
    const newFilters = { ...filterValues, [key]: value };
    Object.entries(newFilters).forEach(([k, v]) => {
      if (v && v !== '') {
        params[k] = v;
      }
    });

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(entity.endpoint, params, { preserveState: true, preserveScroll: true });
  };

  const handleSort = (field: string) => {
    const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

    const params: any = {
      sort_field: field,
      sort_direction: direction,
      page: 1
    };

    // Add search and filters
    if (searchTerm) {
      params.search = searchTerm;
    }

    Object.entries(filterValues).forEach(([key, value]) => {
      if (value && value !== '') {
        params[key] = value;
      }
    });

    // Add per_page if it exists
    if (pageFilters.per_page) {
      params.per_page = pageFilters.per_page;
    }

    router.get(entity.endpoint, params, { preserveState: true, preserveScroll: true });
  };

  const handleAction = (action: string, item: any) => {
    // Transform translatable fields for currencies, countries, and tax rates
    let transformedItem = { ...item };
    if (entity.name === 'currencies') {
      // Convert translation objects to flat structure for form fields
      if (item.name_translations) {
        transformedItem['name.en'] = item.name_translations.en || '';
        transformedItem['name.ar'] = item.name_translations.ar || '';
      }
      if (item.description_translations) {
        transformedItem['description.en'] = item.description_translations.en || '';
        transformedItem['description.ar'] = item.description_translations.ar || '';
      }
    }
    if (entity.name === 'taxRates') {
      if (item.name_translations) {
        transformedItem['name.en'] = item.name_translations.en || '';
        transformedItem['name.ar'] = item.name_translations.ar || '';
      }
      if (item.description_translations) {
        transformedItem['description.en'] = item.description_translations.en || '';
        transformedItem['description.ar'] = item.description_translations.ar || '';
      }
    }
    if (entity.name === 'countries') {
      // Convert translation objects to flat structure for form fields
      if (item.name_translations) {
        transformedItem['name.en'] = item.name_translations.en || '';
        transformedItem['name.ar'] = item.name_translations.ar || '';
      }
      if (item.nationality_name_translations) {
        transformedItem['nationality_name.en'] = item.nationality_name_translations.en || '';
        transformedItem['nationality_name.ar'] = item.nationality_name_translations.ar || '';
      }
    }

    setCurrentItem(transformedItem);

    switch (action) {
      case 'view':
        setFormMode('view');
        setIsFormModalOpen(true);
        break;
      case 'edit':
        setFormMode('edit');
        setIsFormModalOpen(true);
        break;
      case 'delete':
        setIsDeleteModalOpen(true);
        break;
      default:
        break;
    }
  };

  const handleAddNew = () => {
    setCurrentItem(null);
    setFormMode('create');
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    // Make a copy of the form data to avoid modifying the original
    const processedFormData = { ...formData };

    // For roles, create a simplified object with only the required fields
    if (entity.name === 'roles') {
      // Extract permission names from the permissions array if they're objects
      if (processedFormData.permissions && Array.isArray(processedFormData.permissions)) {
        const permissionNames = processedFormData.permissions.map(p => {
          if (typeof p === 'object' && p !== null && p.name) {
            return p.name;
          }
          return String(p);
        });
        processedFormData.permissions = permissionNames;
      }

      // Reset the object with only the fields we need
      const cleanData = {
        label: processedFormData.label,
        description: processedFormData.description || '',
        permissions: processedFormData.permissions || []
      };

      // Replace all properties
      Object.keys(processedFormData).forEach(key => {
        delete processedFormData[key];
      });

      Object.assign(processedFormData, cleanData);
    }
    // Fix permissions format for other entities
    else if (processedFormData.permissions && Array.isArray(processedFormData.permissions)) {
      const permissionsObj = {};
      processedFormData.permissions.forEach((id, index) => {
        permissionsObj[index] = String(id);
      });
      processedFormData.permissions = permissionsObj;
    }

    // Ensure we're not sending the name field for permissions as it's auto-generated
    if (entity.name === 'permissions' && formMode === 'edit') {
      delete processedFormData.name;
    }

    // Check if this entity has file uploads
    const hasFileFields = form.fields.some(field => field.type === 'file');

    if (hasFileFields) {
      // Get file field names
      const fileFields = form.fields
        .filter(field => field.type === 'file')
        .map(field => field.name);

      // Use FormData for file uploads
      const formDataObj = new FormData();

      // Add all fields to FormData
      Object.keys(processedFormData).forEach(key => {
        // For file fields in edit mode
        if (fileFields.includes(key) && formMode === 'edit') {
          // Only include the file if a new one was selected
          if (processedFormData[key] && typeof processedFormData[key] === 'object') {
            formDataObj.append(key, processedFormData[key]);
          }
          // Otherwise skip this field - don't send empty file fields
          return;
        }
        formDataObj.append(key, processedFormData[key]);
      });

      if (formMode === 'create') {
        // Show loading toast
        toast.loading(t('Creating...'));

        router.post(entity.endpoint, formDataObj, {
          onSuccess: (page) => {
            setIsFormModalOpen(false);
            toast.dismiss();
            // toast.success(t(`${entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)} created successfully`));
            if (hooks?.afterCreate) {
              hooks.afterCreate(formData, page.props[entity.name]);
            }
          },
          onError: (errors) => {
            toast.dismiss();
            toast.error(t(`Failed to create ${entity.name.slice(0, -1)}: ${Object.values(errors).join(', ')}`));
          }
        });
      } else if (formMode === 'edit') {
        // Show loading toast
        toast.loading(t('Updating...'));

        router.post(`${entity.endpoint}/${currentItem.id}?_method=PUT`, formDataObj, {
          onSuccess: (page) => {
            setIsFormModalOpen(false);
            toast.dismiss();
            // toast.success(t(`${entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)} updated successfully`));
            if (hooks?.afterUpdate) {
              hooks.afterUpdate(formData, page.props[entity.name]);
            }
          },
          onError: (errors) => {
            toast.dismiss();
            toast.error(t(`Failed to update ${entity.name.slice(0, -1)}: ${Object.values(errors).join(', ')}`));
          }
        });
      }
      return;
    }

    if (formMode === 'create') {
      // Show loading toast
      toast.loading(t('Creating...'));

      router.post(entity.endpoint, processedFormData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          // toast.success(t(`${entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)} created successfully`));
          if (hooks?.afterCreate) {
            hooks.afterCreate(formData, page.props[entity.name]);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t(`Failed to create ${entity.name.slice(0, -1)}: ${Object.values(errors).join(', ')}`));
        }
      });
    } else if (formMode === 'edit') {
      // Show loading toast
      toast.loading(t('Updating...'));

      router.put(`${entity.endpoint}/${currentItem.id}`, processedFormData, {
        onSuccess: (page) => {
          setIsFormModalOpen(false);
          toast.dismiss();
          // toast.success(t(`${entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)} updated successfully`));
          if (hooks?.afterUpdate) {
            hooks.afterUpdate(formData, page.props[entity.name]);
          }
        },
        onError: (errors) => {
          toast.dismiss();
          toast.error(t(`Failed to update ${entity.name.slice(0, -1)}: ${Object.values(errors).join(', ')}`));
        }
      });
    }
  };

  const handleDeleteConfirm = () => {
    // Show loading toast
    toast.loading(t('Deleting...'));

    router.delete(`${entity.endpoint}/${currentItem.id}`, {
      onSuccess: () => {
        setIsDeleteModalOpen(false);
        toast.dismiss();
        // toast.success(t(`${entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)} deleted successfully`));
        if (hooks?.afterDelete) {
          hooks.afterDelete(currentItem.id);
        }
      },
      onError: (errors) => {
        toast.dismiss();
        toast.error(t(`Failed to delete ${entity.name.slice(0, -1)}: ${Object.values(errors).join(', ')}`));
      }
    });
  };

  const handleResetFilters = () => {
    // Reset all filters to default values
    const resetFilters: Record<string, any> = {};
    filters.forEach(filter => {
      resetFilters[filter.key] = filter.type === 'select' ? 'all' : '';
    });

    setFilterValues(resetFilters);
    setSearchTerm('');
    setShowFilters(false);

    router.get(entity.endpoint, {
      page: 1,
      per_page: pageFilters.per_page
    }, { preserveState: true, preserveScroll: true });
  };

  // Check if we should show the add button
  const showAddButton = buttons.every(button => button.showAddButton !== false);

  // Define page actions
  const pageActions: PageAction[] = [];

  // Add custom buttons with permission check
  buttons.forEach(button => {
    if (!button.permission || hasPermission(permissions, button.permission)) {
      pageActions.push({
        label: button.label,
        icon: button.icon,
        variant: button.variant,
        onClick: button.onClick
      });
    }
  });

  // Add the default "Add New" button if allowed and user has permission
  if (showAddButton && hasPermission(permissions, entity.permissions.create)) {
    const singularName = entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1);
    pageActions.push({
      label: t('Add New {{name}}', { name: t(singularName) }),
      icon: <PlusIcon className="h-4 w-4" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const pageTitle = title || entity.name.charAt(0).toUpperCase() + entity.name.slice(1);

  // Generate default breadcrumbs if not provided
  const defaultBreadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: pageTitle }
  ];

  const pageBreadcrumbs = breadcrumbs || defaultBreadcrumbs;

  return (
    <PageTemplate
      title={pageTitle}
      url={url}
      actions={pageActions}
      breadcrumbs={pageBreadcrumbs}
      noPadding
    >
      {/* Search and filters section */}
      <div className="mb-4 rounded-lg bg-white dark:bg-gray-900">
        <SearchAndFilterBar
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          onSearch={handleSearch}
          filters={filters.map((filter) => {
            const filterKey = filter.key;
            return {
              name: filterKey,
              label: filter.label,
              type: filter.type as 'select' | 'date',
              value: filterValues[filterKey] ?? '',
              onChange: (value: any) => handleFilterChange(filterKey, value),
              options: filter.options,
            };
          })}
          showFilters={showFilters}
          setShowFilters={setShowFilters}
          hasActiveFilters={hasActiveFilters}
          activeFilterCount={activeFilterCount}
          onResetFilters={handleResetFilters}
          onApplyFilters={applyFilters}
        />
      </div>

      {/* Content section */}
      <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <CrudTable
          columns={table.columns}
          actions={table.actions}
          data={data.data}
          from={data.from || 1}
          onAction={handleAction}
          sortField={pageFilters.sort_field}
          sortDirection={pageFilters.sort_direction}
          onSort={handleSort}
          statusColors={table.statusColors}
          permissions={permissions}
          entityPermissions={entity.permissions}
        />

        {/* Pagination section */}
        <Pagination
          from={data.from || 0}
          to={data.to || 0}
          total={data.total}
          links={data.links}
          entityName={t(entity.name)}
          onPageChange={(url) => router.get(url)}
          currentPerPage={pageFilters.per_page?.toString() || '10'}
          onPerPageChange={(value) => {
            const params: any = { page: 1, per_page: parseInt(value) };
            if (searchTerm) params.search = searchTerm;
            Object.entries(filterValues).forEach(([key, val]) => {
              if (val && val !== '') params[key] = val;
            });
            router.get(entity.endpoint, params, { preserveState: true, preserveScroll: true });
          }}
        />
      </div>

      <CrudFormModal
        isOpen={isFormModalOpen}
        onClose={() => setIsFormModalOpen(false)}
        onSubmit={handleFormSubmit}
        formConfig={{
          ...form,
          modalSize: config.modalSize || form.modalSize
        }}
        initialData={currentItem}
        title={
          formMode === 'create'
            ? t('Add New {{name}}', { name: t(entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)) })
            : formMode === 'edit'
              ? t('Edit {{name}}', { name: t(entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)) })
              : t('View {{name}}', { name: t(entity.name.slice(0, -1).charAt(0).toUpperCase() + entity.name.slice(0, -1).slice(1)) })
        }
        mode={formMode}
        description={config.description}
      />

      <CrudDeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
        itemName={currentItem?.name || ''}
        entityName={entity.name.slice(0, -1)}
      />
    </PageTemplate>
  );
}