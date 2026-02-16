import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { CrudTable } from '@/components/CrudTable';
import { toast } from '@/components/custom-toast';
import { PageTemplate } from '@/components/page-template';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pagination } from '@/components/ui/pagination';
import { SearchAndFilterBar } from '@/components/ui/search-and-filter-bar';
import { Switch } from '@/components/ui/switch';
import { hasPermission } from '@/utils/authorization';
import { router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function Clients() {
    const { t, i18n } = useTranslation();
    const { auth, clients, clientTypes, planLimits, filters: pageFilters = {} } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const currentLocale = i18n.language || 'en';

    // State
    const [searchTerm, setSearchTerm] = useState(pageFilters.search || '');
    const [selectedClientType, setSelectedClientType] = useState(pageFilters.client_type_id || 'all');
    const [selectedStatus, setSelectedStatus] = useState(pageFilters.status || 'all');
    const [showFilters, setShowFilters] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [isResetPasswordModalOpen, setIsResetPasswordModalOpen] = useState(false);
    const [resetPasswordData, setResetPasswordData] = useState({ password: '', password_confirmation: '' });
    const [currentItem, setCurrentItem] = useState<any>(null);

    // Reload data when language changes to refresh translations
    useEffect(() => {
        const handleLanguageChange = () => {
            router.get(
                route('clients.index'),
                {
                    page: pageFilters.page || 1,
                    search: searchTerm || undefined,
                    client_type_id: selectedClientType !== 'all' ? selectedClientType : undefined,
                    status: selectedStatus !== 'all' ? selectedStatus : undefined,
                    sort_field: pageFilters.sort_field,
                    sort_direction: pageFilters.sort_direction,
                    per_page: pageFilters.per_page,
                },
                { preserveState: false, preserveScroll: false },
            );
        };

        window.addEventListener('languageChanged', handleLanguageChange);
        return () => {
            window.removeEventListener('languageChanged', handleLanguageChange);
        };
    }, [pageFilters, searchTerm, selectedClientType, selectedStatus]);

    // Check if any filters are active
    const hasActiveFilters = () => {
        return searchTerm !== '' || selectedClientType !== 'all' || selectedStatus !== 'all';
    };

    // Count active filters
    const activeFilterCount = () => {
        return (searchTerm ? 1 : 0) + (selectedClientType !== 'all' ? 1 : 0) + (selectedStatus !== 'all' ? 1 : 0);
    };

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        applyFilters();
    };

    const applyFilters = () => {
        router.get(
            route('clients.index'),
            {
                page: 1,
                search: searchTerm || undefined,
                client_type_id: selectedClientType !== 'all' ? selectedClientType : undefined,
                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                per_page: pageFilters.per_page,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const handleSort = (field: string) => {
        const direction = pageFilters.sort_field === field && pageFilters.sort_direction === 'asc' ? 'desc' : 'asc';

        router.get(
            route('clients.index'),
            {
                sort_field: field,
                sort_direction: direction,
                page: 1,
                search: searchTerm || undefined,
                client_type_id: selectedClientType !== 'all' ? selectedClientType : undefined,
                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                per_page: pageFilters.per_page,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const handleAction = (action: string, item: any) => {
        setCurrentItem(item);

        switch (action) {
            case 'edit':
                router.get(route('clients.edit', item.id));
                break;
            case 'delete':
                setIsDeleteModalOpen(true);
                break;
            case 'toggle-status':
                handleToggleStatus(item);
                break;
            case 'reset-password':
                if (item.email) {
                    setIsResetPasswordModalOpen(true);
                } else {
                    toast.error(t('Client must have an email address to reset password'));
                }
                break;
        }
    };

    const handleAddNew = () => {
        router.get(route('clients.create'));
    };

    const handleDeleteConfirm = () => {
        toast.loading(t('Deleting client...'));

        router.delete(route('clients.destroy', currentItem.id), {
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
                if (typeof errors === 'string') {
                    toast.error(errors);
                } else {
                    toast.error(`Failed to delete client: ${Object.values(errors).join(', ')}`);
                }
            },
        });
    };

    const handleToggleStatus = (client: any) => {
        const newStatus = client.status === 'active' ? 'inactive' : 'active';
        toast.loading(`${newStatus === 'active' ? t('Activating') : t('Deactivating')} client...`);

        router.put(
            route('clients.toggle-status', client.id),
            {},
            {
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
                    if (typeof errors === 'string') {
                        toast.error(errors);
                    } else {
                        toast.error(`Failed to update client status: ${Object.values(errors).join(', ')}`);
                    }
                },
            },
        );
    };

    const handleResetPasswordSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (resetPasswordData.password !== resetPasswordData.password_confirmation) {
            toast.error(t('Passwords do not match'));
            return;
        }

        if (resetPasswordData.password.length < 6) {
            toast.error(t('Password must be at least 6 characters'));
            return;
        }

        toast.loading(t('Resetting password...'));

        router.put(route('clients.reset-password', currentItem.id), resetPasswordData, {
            onSuccess: (page) => {
                setIsResetPasswordModalOpen(false);
                setResetPasswordData({ password: '', password_confirmation: '' });
                toast.dismiss();
                if (page.props.flash.success) {
                    toast.success(page.props.flash.success);
                } else if (page.props.flash.error) {
                    toast.error(page.props.flash.error);
                }
            },
            onError: (errors) => {
                toast.dismiss();
                if (typeof errors === 'string') {
                    toast.error(errors);
                } else {
                    toast.error(`Failed to reset password: ${Object.values(errors).join(', ')}`);
                }
            },
        });
    };

    const handleResetFilters = () => {
        setSearchTerm('');
        setSelectedClientType('all');
        setSelectedStatus('all');
        setShowFilters(false);

        router.get(
            route('clients.index'),
            {
                page: 1,
                per_page: pageFilters.per_page,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    // Define page actions
    const pageActions = [];

    // Add the "Add New Client" button if user has permission and within limits
    if (hasPermission(permissions, 'create-clients')) {
        const canCreate = !planLimits || planLimits.can_create;
        pageActions.push({
            label:
                planLimits && !canCreate
                    ? t('Client Limit Reached ({{current}}/{{max}})', { current: planLimits.current_clients, max: planLimits.max_clients })
                    : t('Add Client'),
            icon: <Plus className="mr-2 h-4 w-4" />,
            variant: canCreate ? 'default' : 'outline',
            onClick: canCreate
                ? () => handleAddNew()
                : () =>
                    toast.error(
                        t('Client limit exceeded. Your plan allows maximum {{max}} clients. Please upgrade your plan.', {
                            max: planLimits.max_clients,
                        }),
                    ),
            disabled: !canCreate,
        });
    }

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Client Management'), href: route('clients.index') },
        { title: t('Clients') },
    ];

    // Define table columns
    const columns = [
        {
            key: 'name',
            label: t('Name'),
            sortable: true,
        },

        {
            key: 'phone',
            label: t('Phone'),
            render: (value: number) => value || '-',
        },
        {
            key: 'email',
            label: t('Email'),
            render: (value: string) => value || '-',
        },
        {
            key: 'client_type',
            label: t('Type'),
            render: (value: any, row: any) => {
                const clientType = row.client_type;
                if (!clientType) return '-';
                // Use name_translations if available (full translations object)
                const translations = clientType.name_translations || (typeof clientType.name === 'object' ? clientType.name : null);
                if (translations && typeof translations === 'object') {
                    return translations[currentLocale] || translations.en || translations.ar || '-';
                }
                // Fallback to name if it's a string
                return clientType.name || '-';
            },
        },
        {
            key: 'cases_count',
            label: t('Cases Count'),
            render: (value: string) => value || 0,
        },
        {
            key: 'status',
            label: t('Status'),
            render: (value: string, row: any) => {
                const canToggleStatus = hasPermission(permissions, 'edit-clients');
                return (
                    <div className="flex items-center gap-2">
                        <Switch
                            checked={value === 'active'}
                            disabled={!canToggleStatus}
                            onCheckedChange={() => {
                                if (!canToggleStatus) return;
                                handleToggleStatus(row);
                            }}
                            aria-label={value === 'active' ? t('Deactivate client') : t('Activate client')}
                        />
                        <span className="text-muted-foreground text-xs">{value === 'active' ? t('Active') : t('Inactive')}</span>
                    </div>
                );
            },
        },
    ];

    // Define table actions
    const actions = [
        {
            label: t('View'),
            icon: 'Eye',
            action: 'view',
            className: 'text-blue-500',
            requiredPermission: 'view-clients',
            href: (row: any) => route('clients.show', row.id),
        },
        {
            label: t('Edit'),
            icon: 'Edit',
            action: 'edit',
            className: 'text-amber-500',
            requiredPermission: 'edit-clients',
        },
        {
            label: t('Reset Password'),
            icon: 'Key',
            action: 'reset-password',
            className: 'text-purple-500',
            requiredPermission: 'reset-client-password',
        },
        {
            label: t('Delete'),
            icon: 'Trash2',
            action: 'delete',
            className: 'text-red-500',
            requiredPermission: 'delete-clients',
        },
    ];

    // Prepare client type options for filter and form
    const clientTypeOptions = [
        { value: 'all', label: t('All Types') },
        ...(clientTypes || []).map((type: any) => ({
            value: type.id.toString(),
            label: type.name,
        })),
    ];

    // Prepare status options for filter
    const statusOptions = [
        { value: 'all', label: t('All Statuses') },
        { value: 'active', label: t('Active') },
        { value: 'inactive', label: t('Inactive') },
    ];

    return (
        <PageTemplate title={t('Client Management')} url="/clients" actions={pageActions} breadcrumbs={breadcrumbs} noPadding>
            {/* Search and filters section */}
            <div className="mb-4 rounded-lg bg-white">
                <SearchAndFilterBar
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onSearch={handleSearch}
                    filters={[
                        {
                            name: 'client_type_id',
                            label: t('Client Type'),
                            type: 'select',
                            value: selectedClientType,
                            onChange: setSelectedClientType,
                            options: clientTypeOptions,
                        },
                        {
                            name: 'status',
                            label: t('Status'),
                            type: 'select',
                            value: selectedStatus,
                            onChange: setSelectedStatus,
                            options: statusOptions,
                        },
                    ]}
                    showFilters={showFilters}
                    setShowFilters={setShowFilters}
                    hasActiveFilters={hasActiveFilters}
                    activeFilterCount={activeFilterCount}
                    onResetFilters={handleResetFilters}
                    onApplyFilters={applyFilters}
                />
            </div>

            {/* Content section */}
            <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
                <CrudTable
                    columns={columns}
                    actions={actions}
                    data={clients?.data || []}
                    from={clients?.from || 1}
                    onAction={handleAction}
                    sortField={pageFilters.sort_field}
                    sortDirection={pageFilters.sort_direction}
                    onSort={handleSort}
                    permissions={permissions}
                    entityPermissions={{
                        view: 'view-clients',
                        create: 'create-clients',
                        edit: 'edit-clients',
                        delete: 'delete-clients',
                    }}
                />

                {/* Pagination section */}
                <Pagination
                    from={clients?.from || 0}
                    to={clients?.to || 0}
                    total={clients?.total || 0}
                    links={clients?.links}
                    entityName={t('clients')}
                    onPageChange={(url) => router.get(url)}
                    perPage={pageFilters.per_page?.toString() || '10'}
                    onPerPageChange={(value) => {
                        router.get(
                            route('clients.index'),
                            {
                                page: 1,
                                per_page: parseInt(value),
                                search: searchTerm || undefined,
                                client_type_id: selectedClientType !== 'all' ? selectedClientType : undefined,
                                status: selectedStatus !== 'all' ? selectedStatus : undefined,
                            },
                            { preserveState: true, preserveScroll: true },
                        );
                    }}
                />
            </div>

            {/* Delete Modal */}
            <CrudDeleteModal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
                onConfirm={handleDeleteConfirm}
                itemName={currentItem?.name || ''}
                entityName="client"
            />

            {/* Reset Password Modal */}
            <Dialog open={isResetPasswordModalOpen} onOpenChange={setIsResetPasswordModalOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>{t('Reset Client Password')}</DialogTitle>
                        <div className="text-muted-foreground text-sm">
                            <p>
                                <strong>{t('Client')}:</strong> {currentItem?.name}
                            </p>
                            <p>
                                <strong>{t('Email')}:</strong> {currentItem?.email}
                            </p>
                        </div>
                    </DialogHeader>
                    <form onSubmit={handleResetPasswordSubmit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="password">{t('New Password')}</Label>
                            <Input
                                id="password"
                                type="password"
                                value={resetPasswordData.password}
                                onChange={(e) => setResetPasswordData((prev) => ({ ...prev, password: e.target.value }))}
                                required
                                minLength={6}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="password_confirmation">{t('Confirm Password')}</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                value={resetPasswordData.password_confirmation}
                                onChange={(e) => setResetPasswordData((prev) => ({ ...prev, password_confirmation: e.target.value }))}
                                required
                                minLength={6}
                            />
                        </div>
                        <div className="flex justify-end space-x-2">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    setIsResetPasswordModalOpen(false);
                                    setResetPasswordData({ password: '', password_confirmation: '' });
                                }}
                            >
                                {t('Cancel')}
                            </Button>
                            <Button type="submit">{t('Reset Password')}</Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>
        </PageTemplate>
    );
}
