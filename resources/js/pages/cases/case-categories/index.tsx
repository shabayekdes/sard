import { useState } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { hasPermission } from '@/utils/authorization';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';

export default function CaseCategories() {
    const { t, i18n } = useTranslation();
    const { auth, caseCategories, parentCategories, filters: pageFilters = {} } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const currentLocale = i18n.language || 'en';

    const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
    const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
    const [showFilters, setShowFilters] = useState(false);
    const [isFormModalOpen, setIsFormModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [currentItem, setCurrentItem] = useState<any>(null);
    const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');

    const hasActiveFilters = () => {
        return searchTerm !== '' || selectedStatus !== 'all';
    };

    const activeFilterCount = () => {
        return (searchTerm ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
    };

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        applyFilters();
    };

    const applyFilters = () => {
        router.get(route('cases.case-categories.index'), {
            page: 1,
            search: searchTerm || undefined,
            status: selectedStatus !== 'all' ? selectedStatus : undefined,
            per_page: pageFilters.per_page
        }, { preserveState: true, preserveScroll: true });
    };

    const handleSort = (field: string) => {
        const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

        router.get(route('cases.case-categories.index'), {
            sort_field: field,
            sort_direction: direction,
            page: 1,
            search: searchTerm || undefined,
            status: selectedStatus !== 'all' ? selectedStatus : undefined,
            per_page: pageFilters.per_page
        }, { preserveState: true, preserveScroll: true });
    };

    const handleAction = (action: string, item: any) => {
        setCurrentItem(item);

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
            case 'toggle-status':
                handleToggleStatus(item);
                break;
        }
    };

    const handleAddNew = () => {
        setCurrentItem(null);
        setFormMode('create');
        setIsFormModalOpen(true);
    };

    const handleFormSubmit = (formData: any) => {
        // Convert 'none' or empty string to null for parent_id
        if (formData.parent_id === 'none' || formData.parent_id === '') {
            formData.parent_id = null;
        }

        if (formMode === 'create') {
            toast.loading(t('Creating case category...'));

            router.post(route('cases.case-categories.store'), formData, {
                onSuccess: (page) => {
                    setIsFormModalOpen(false);
                    toast.dismiss();
                    if (page.props.flash.success) {
                        toast.success(page.props.flash.success);
                    } else if (page.props.flash.error) {
                        toast.error(page.props.flash.error);
                    }
                },
                onError: (errors) => {
                    toast.dismiss();
                    toast.error(`Failed to create case category: ${Object.values(errors).join(', ')}`);
                }
            });
        } else if (formMode === 'edit') {
            toast.loading(t('Updating case category...'));

            router.put(route('cases.case-categories.update', currentItem.id), formData, {
                onSuccess: (page) => {
                    setIsFormModalOpen(false);
                    toast.dismiss();
                    if (page.props.flash.success) {
                        toast.success(page.props.flash.success);
                    } else if (page.props.flash.error) {
                        toast.error(page.props.flash.error);
                    }
                },
                onError: (errors) => {
                    toast.dismiss();
                    toast.error(`Failed to update case category: ${Object.values(errors).join(', ')}`);
                }
            });
        }
    };

    const handleDeleteConfirm = () => {
        toast.loading(t('Deleting case category...'));

        router.delete(route('cases.case-categories.destroy', currentItem.id), {
            onSuccess: (page) => {
                setIsDeleteModalOpen(false);
                toast.dismiss();
                if (page.props.flash.success) {
                    toast.success(page.props.flash.success);
                } else if (page.props.flash.error) {
                    toast.error(page.props.flash.error);
                }
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error(`Failed to delete case category: ${Object.values(errors).join(', ')}`);
            }
        });
    };

    const handleToggleStatus = (caseCategory: any) => {
        const newStatus = caseCategory.status === 'active' ? 'inactive' : 'active';
        toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} case category...`);

        router.put(route('cases.case-categories.toggle-status', caseCategory.id), {}, {
            onSuccess: (page) => {
                toast.dismiss();
                if (page.props.flash.success) {
                    toast.success(page.props.flash.success);
                } else if (page.props.flash.error) {
                    toast.error(page.props.flash.error);
                }
            },
            onError: (errors) => {
                toast.dismiss();
                toast.error(`Failed to update case category status: ${Object.values(errors).join(', ')}`);
            }
        });
    };

    const handleResetFilters = () => {
        setSearchTerm('');
        setSelectedStatus('all');
        setShowFilters(false);

        router.get(route('cases.case-categories.index'), {
            page: 1,
            per_page: pageFilters.per_page
        }, { preserveState: true, preserveScroll: true });
    };

    const pageActions = [];

    if (hasPermission(permissions, 'create-case-categories')) {
        pageActions.push({
            label: t('Add Case Category'),
            icon: <Plus className="h-4 w-4 mr-2" />,
            variant: 'default',
            onClick: () => handleAddNew()
        });
    }

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Case Management'), href: route('cases.index') },
        { title: t('Case Categories') }
    ];

    // Filter parent categories for dropdown (exclude current item when editing)
    const getParentOptions = () => {
        const options = [
            { value: 'none', label: t('None') }
        ];

        if (parentCategories) {
            const filtered = formMode === 'edit' && currentItem
                ? parentCategories.filter((cat: any) => cat.id !== currentItem.id)
                : parentCategories;

            options.push(...filtered.map((cat: any) => {
                // Handle translatable name
                let displayName = cat.name;
                if (typeof cat.name === 'object' && cat.name !== null) {
                    displayName = cat.name[currentLocale] || cat.name.en || cat.name.ar || '';
                } else if (cat.name_translations && typeof cat.name_translations === 'object') {
                    displayName = cat.name_translations[currentLocale] || cat.name_translations.en || cat.name_translations.ar || '';
                }
                return {
                    value: cat.id.toString(),
                    label: displayName
                };
            }));
        }

        return options;
    };

    const columns = [
        {
            key: 'name',
            label: t('Name'),
            sortable: true,
            render: (value: any, row: any) => {
                // Use name_translations if available (full translations object)
                const translations = row.name_translations || (typeof value === 'object' ? value : null);
                if (translations && typeof translations === 'object') {
                    return translations[currentLocale] || translations.en || translations.ar || '-';
                }
                // Fallback to value if it's a string
                return value || '-';
            }
        },
        {
            key: 'parent',
            label: t('Parent'),
            render: (value: any, row: any) => {
                if (row.parent) {
                    const parentTranslations = row.parent.name_translations || (typeof row.parent.name === 'object' ? row.parent.name : null);
                    const parentName = parentTranslations && typeof parentTranslations === 'object'
                        ? (parentTranslations[currentLocale] || parentTranslations.en || parentTranslations.ar || '')
                        : row.parent.name || '-';
                    return (
                        <span className="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                            {parentName}
                        </span>
                    );
                }
                return '-';
            }
        },
        {
            key: 'description',
            label: t('Description'),
            render: (value: any, row: any) => {
                // Use description_translations if available (full translations object)
                const translations = row.description_translations || (typeof value === 'object' ? value : null);
                if (translations && typeof translations === 'object') {
                    return translations[currentLocale] || translations.en || translations.ar || '-';
                }
                // Fallback to value if it's a string
                return value || '-';
            }
        },
        {
            key: 'color',
            label: t('Color'),
            render: (value: string) => (
                <div className="flex items-center gap-2">
                    <div
                        className="w-4 h-4 rounded border"
                        style={{ backgroundColor: value }}
                    ></div>
                    <span className="text-sm ">{value}</span>
                </div>
            )
        },
        {
            key: 'status',
            label: t('Status'),
            render: (value: string) => (
                <span className={`inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ${value === 'active'
                    ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'
                    : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20'
                    }`}>
                    {value === 'active' ? t('Active') : t('Inactive')}
                </span>
            )
        }
    ];

    const actions = [
        {
            label: t('View'),
            icon: 'Eye',
            action: 'view',
            className: 'text-blue-500',
            requiredPermission: 'view-case-categories'
        },
        {
            label: t('Edit'),
            icon: 'Edit',
            action: 'edit',
            className: 'text-amber-500',
            requiredPermission: 'edit-case-categories'
        },
        {
            label: t('Toggle Status'),
            icon: 'Lock',
            action: 'toggle-status',
            className: 'text-amber-500',
            requiredPermission: 'edit-case-categories'
        },
        {
            label: t('Delete'),
            icon: 'Trash2',
            action: 'delete',
            className: 'text-red-500',
            requiredPermission: 'delete-case-categories'
        }
    ];

    return (
        <PageTemplate
            title={t("Case Categories")}
            url="/cases/case-categories"
            actions={pageActions}
            breadcrumbs={breadcrumbs}
            noPadding
        >
            <div className="bg-white dark:bg-gray-900 rounded-lg shadow mb-4 p-4">
                <SearchAndFilterBar
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onSearch={handleSearch}
                    filters={[
                        {
                            name: 'status',
                            label: t('Status'),
                            type: 'select',
                            value: selectedStatus,
                            onChange: setSelectedStatus,
                            options: [
                                { value: 'all', label: t('All Statuses') },
                                { value: 'active', label: t('Active') },
                                { value: 'inactive', label: t('Inactive') }
                            ]
                        }
                    ]}
                    showFilters={showFilters}
                    setShowFilters={setShowFilters}
                    hasActiveFilters={hasActiveFilters}
                    activeFilterCount={activeFilterCount}
                    onResetFilters={handleResetFilters}
                    onApplyFilters={applyFilters}
                    currentPerPage={pageFilters.per_page?.toString() || "10"}
                    onPerPageChange={(value) => {
                        router.get(route('cases.case-categories.index'), {
                            page: 1,
                            per_page: parseInt(value),
                            search: searchTerm || undefined,
                            status: selectedStatus !== 'all' ? selectedStatus : undefined
                        }, { preserveState: true, preserveScroll: true });
                    }}
                />
            </div>

            <div className="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                <CrudTable
                    columns={columns}
                    actions={actions}
                    data={caseCategories?.data || []}
                    from={caseCategories?.from || 1}
                    onAction={handleAction}
                    sortField={pageFilters.sort_field}
                    sortDirection={pageFilters.sort_direction}
                    onSort={handleSort}
                    permissions={permissions}
                    entityPermissions={{
                        view: 'view-case-categories',
                        create: 'create-case-categories',
                        edit: 'edit-case-categories',
                        delete: 'delete-case-categories'
                    }}
                />

                <Pagination
                    from={caseCategories?.from || 0}
                    to={caseCategories?.to || 0}
                    total={caseCategories?.total || 0}
                    links={caseCategories?.links}
                    entityName={t("case categories")}
                    onPageChange={(url) => router.get(url)}
                />
            </div>

            <CrudFormModal
                isOpen={isFormModalOpen}
                onClose={() => setIsFormModalOpen(false)}
                onSubmit={handleFormSubmit}
                formConfig={{
                    fields: [
                        {
                            name: 'name.en',
                            label: t('Name (English)'),
                            type: 'text',
                            required: true
                        },
                        {
                            name: 'name.ar',
                            label: t('Name (Arabic)'),
                            type: 'text',
                            required: true
                        },
                        {
                            name: 'parent_id',
                            label: t('Parent'),
                            type: 'select',
                            options: getParentOptions()
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
                        },
                        { name: 'color', label: t('Color'), type: 'color', defaultValue: '#3B82F6' },
                        {
                            name: 'status',
                            label: t('Status'),
                            type: 'select',
                            options: [
                                { value: 'active', label: 'Active' },
                                { value: 'inactive', label: 'Inactive' }
                            ],
                            defaultValue: 'active'
                        }
                    ],
                    modalSize: 'lg',
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
                    }
                }}
                initialData={
                    currentItem
                        ? {
                            ...currentItem,
                            'name.en': currentItem.name_translations?.en ||
                                (typeof currentItem.name === 'object' ? currentItem.name?.en : '') || '',
                            'name.ar': currentItem.name_translations?.ar ||
                                (typeof currentItem.name === 'object' ? currentItem.name?.ar : '') || '',
                            'description.en': currentItem.description_translations?.en ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.en : '') || '',
                            'description.ar': currentItem.description_translations?.ar ||
                                (typeof currentItem.description === 'object' ? currentItem.description?.ar : '') || '',
                            'parent_id': currentItem.parent_id ? currentItem.parent_id.toString() : 'none',
                        }
                        : { parent_id: 'none' }
                }
                title={
                    formMode === 'create'
                        ? t('Add New Case Category')
                        : formMode === 'edit'
                            ? t('Edit Case Category')
                            : t('View Case Category')
                }
                mode={formMode}
            />

            <CrudDeleteModal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
                onConfirm={handleDeleteConfirm}
                itemName={currentItem?.name || ''}
                entityName="case category"
            />
        </PageTemplate>
    );
}

