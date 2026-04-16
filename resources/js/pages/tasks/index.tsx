import React, { useState, useEffect, useRef } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import TaskModal from './TaskModal';
import TaskPriority from '@/components/tasks/TaskPriority';
import TaskStatusChanger from '@/components/tasks/TaskStatusChanger';
import { CrudTable } from '@/components/CrudTable';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pagination } from '@/components/ui/pagination';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { Plus, Search, Filter, Eye, Edit, Copy, Trash2, LayoutGrid, List, User as UserIcon, CheckSquare, Columns, AlertTriangle } from 'lucide-react';
import { PageTemplate } from '@/components/page-template';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { Task, Project, TaskStatusOption, User, PaginatedData } from '@/types';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { localizedString } from '@/utils/i18n';
import { taskPriorityTranslationKey } from '@/utils/taskPriority';
import { CrudFormModal } from '@/components/CrudFormModal';
import { hasPermission } from '@/utils/authorization';
import { useInitials } from '@/hooks/use-initials';
import { normalizeInertiaValidationErrors } from '@/utils/inertiaErrors';
import {
  getTaskAssignee,
  getTaskAssigneeId,
  getTaskPriorityBadgeClassName,
  isTaskOverdue,
  taskHasAssigneeId,
} from '@/utils/taskTable';

declare global {
  interface Window {
    searchTimeout: any;
  }
}

function viewModeToQueryParam(mode: 'card' | 'table' | 'kanban'): 'grid' | 'list' | 'kanban' {
  if (mode === 'card') return 'grid';
  if (mode === 'table') return 'list';
  return 'kanban';
}

function queryParamToViewMode(v?: string): 'card' | 'table' | 'kanban' {
  if (v === 'grid') return 'card';
  if (v === 'list') return 'table';
  if (v === 'kanban') return 'kanban';
  return 'kanban';
}

/** CrudFormModal select sentinels (Radix Select disallows empty string values). */
const TASK_FORM_NO_CASE = '__no_case__';
const TASK_FORM_UNASSIGNED = '__unassigned__';

function transformTaskCrudFormData(data: Record<string, unknown>): Record<string, unknown> {
  const caseId = data.case_id;
  const assignedTo = data.assigned_to;
  return {
    ...data,
    case_id: caseId === TASK_FORM_NO_CASE || caseId === '' || caseId == null ? '' : caseId,
    assigned_to:
      assignedTo === TASK_FORM_UNASSIGNED || assignedTo === '' || assignedTo == null ? '' : assignedTo,
  };
}

/** Ensures priority matches Select values (lowercase) when API sends a string or enum-shaped object. */
function normalizeTaskFormPriority(p: unknown): string {
  if (p == null || p === '') return 'medium';
  if (typeof p === 'string') return p.toLowerCase();
  if (typeof p === 'object' && p !== null && 'value' in p) {
    return String((p as { value: string }).value).toLowerCase();
  }
  return 'medium';
}

interface Props {
  tasks: PaginatedData<Task>;
  projects: Project[];
  taskTypes: { id: number; name?: string | Record<string, string> }[];
  cases: { id: number; title?: string; case_id?: string }[];
  users: User[];
  taskStatuses: TaskStatusOption[];
  filters: {
    task_status_id?: string;
    task_type_id?: string | number;
    priority?: string;
    assigned_to?: string;
    search?: string;
    view?: string;
    sort_field?: string;
    sort_direction?: 'asc' | 'desc';
    per_page?: number;
  };
  userWorkspaceRole?: string;
  permissions?: any;
}

export default function TasksIndex({ tasks, taskTypes, cases, taskStatuses, projects, users, filters, userWorkspaceRole }: Props) {
  const { t, i18n } = useTranslation();
  const getInitials = useInitials();
  const { flash, auth } = usePage().props as any;
  const permissions: string[] = Array.isArray(auth?.permissions) ? auth.permissions : [];
  const [searchTerm, setSearchTerm] = useState(filters.search || '');
  const [selectedTaskStatus, setSelectedTaskStatus] = useState(filters.task_status_id || 'all');
  const [selectedTaskType, setSelectedTaskType] = useState(
    filters.task_type_id != null && filters.task_type_id !== '' ? String(filters.task_type_id) : 'all',
  );
  const [selectedPriority, setSelectedPriority] = useState(filters.priority || 'all');
  const [selectedAssignee, setSelectedAssignee] = useState(filters.assigned_to || 'all');
  const [showFilters, setShowFilters] = useState(false);
  const [selectedTask, setSelectedTask] = useState<Task | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [viewMode, setViewMode] = useState<'card' | 'table' | 'kanban'>(() => queryParamToViewMode(filters.view));
  const viewModeRef = useRef(viewMode);
  viewModeRef.current = viewMode;
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [taskToDelete, setTaskToDelete] = useState<Task | null>(null);
  const currentLocale = i18n.language || 'en';
  const [currentItem, setCurrentItem] = useState<any>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit' | 'view'>('create');
  const [taskFormErrors, setTaskFormErrors] = useState<Record<string, string>>({});

  const resolveTaskTypeName = (type: any) => {
      if (!type) return '-';
      const name = type.name ?? type.name_translations;
      if (typeof name === 'string') return name;
      if (name && typeof name === 'object') return name[currentLocale] || name.en || name.ar || '-';
      return '-';
  };
  useEffect(() => {
    if (filters.view === 'grid' || filters.view === 'list' || filters.view === 'kanban') {
      setViewMode(queryParamToViewMode(filters.view));
    }
  }, [filters.view]);

  useEffect(() => {
    setSelectedTaskType(
      filters.task_type_id != null && filters.task_type_id !== '' ? String(filters.task_type_id) : 'all',
    );
  }, [filters.task_type_id]);
  
  // Show flash messages
  useEffect(() => {
    if (flash?.success) {
      toast.success(flash.success);
    }
    if (flash?.error) {
      toast.error(flash.error);
    }
  }, [flash]);
  
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };
  
  const applyFilters = () => {
    const params: any = { page: 1 };
    
    if (searchTerm) params.search = searchTerm;
    if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
    if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
    if (selectedPriority !== 'all') params.priority = selectedPriority;
    if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
    params.view = viewModeToQueryParam(viewMode);
    
    router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
  };
  
  const handleFilter = (key: string, value: string) => {
    const params: any = { page: 1 };
    if (searchTerm) params.search = searchTerm;
    if (key === 'task_status_id') setSelectedTaskStatus(value);
    if (key === 'task_type_id') setSelectedTaskType(value);
    if (key === 'priority') setSelectedPriority(value);
    if (key === 'assigned_to') setSelectedAssignee(value);
    
    if (selectedTaskStatus !== 'all' && key !== 'task_status_id') params.task_status_id = selectedTaskStatus;
    if (selectedTaskType !== 'all' && key !== 'task_type_id') params.task_type_id = selectedTaskType;
    if (selectedPriority !== 'all' && key !== 'priority') params.priority = selectedPriority;
    if (selectedAssignee !== 'all' && key !== 'assigned_to') params.assigned_to = selectedAssignee;
    if (value !== 'all') params[key] = value;
    params.view = viewModeToQueryParam(viewMode);

    router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
  };
  
  const handleAction = (action: string, taskId: number) => {
    switch (action) {
      case 'view':
        handleViewTask(taskId);
        break;
      case 'edit':
        handleEditTask(taskId);
        break;
      // case 'duplicate':
      //   toast.loading('Duplicating task...');
      //   router.post(route('tasks.duplicate', taskId), {}, {
      //     onSuccess: () => {
      //       toast.dismiss();
      //     },
      //     onError: () => {
      //       toast.dismiss();
      //       toast.error('Failed to duplicate task');
      //     }
      //   });
      //   break;
      case 'delete':
        const task = (Array.isArray(tasks) ? tasks : tasks?.data || []).find(t => t.id === taskId);
        if (task) {
          setTaskToDelete(task);
          setIsDeleteModalOpen(true);
        }
        break;
    }
  };

  const handleAddNew = () => {
      setCurrentItem(null);
      setFormMode('create');
      setTaskFormErrors({});
      setIsFormModalOpen(true);
  };
  
  const handleViewTask = async (taskId: number) => {
    try {
      const response = await fetch(route('tasks.show', taskId));
      const data = await response.json();
      setSelectedTask(data.task);
      setIsModalOpen(true);
    } catch (error) {
      console.error('Failed to load task:', error);
    }
  };
  
  const handleEditTask = async (taskId: number) => {
    try {
      const response = await fetch(route('tasks.show', taskId));
      const data = await response.json();
      
      const taskWithProject = {
        ...data.task,
        project: projects.find(p => p.id === data.task.project_id) || data.task.project
      };
      
      setCurrentItem(taskWithProject);
      setFormMode('edit');
      setTaskFormErrors({});
      setIsFormModalOpen(true);
    } catch (error) {
      console.error('Failed to load task:', error);
    }
  };
  
  const hasActiveFilters = () => {
    return (
      selectedTaskStatus !== 'all' ||
      selectedTaskType !== 'all' ||
      selectedPriority !== 'all' ||
      selectedAssignee !== 'all' ||
      searchTerm !== ''
    );
  };
  
  const activeFilterCount = () => {
    return (
      (selectedTaskStatus !== 'all' ? 1 : 0) +
      (selectedTaskType !== 'all' ? 1 : 0) +
      (selectedPriority !== 'all' ? 1 : 0) +
      (selectedAssignee !== 'all' ? 1 : 0) +
      (searchTerm ? 1 : 0)
    );
  };
  
  const handleResetFilters = () => {
    setSelectedTaskStatus('all');
    setSelectedTaskType('all');
    setSelectedPriority('all');
    setSelectedAssignee('all');
    setSearchTerm('');
    setShowFilters(false);
    router.get(route('tasks.index'), { page: 1, view: 'kanban' }, { preserveState: true, preserveScroll: true });
  };
  
  const handleDeleteConfirm = () => {
    if (taskToDelete) {
      router.delete(route('tasks.destroy', taskToDelete.id), {
        onSuccess: () => {
          toast.dismiss();
          setIsDeleteModalOpen(false);
          setTaskToDelete(null);
        },
        onError: () => {
          toast.dismiss();
          toast.error('Failed to delete task');
          setIsDeleteModalOpen(false);
          setTaskToDelete(null);
        }
      });
    }
  };
  
  const buildTaskIndexQuery = (overrides: Record<string, string | number | boolean | undefined> = {}) => {
    const params: Record<string, string | number | boolean | undefined> = {
      view: viewMode === 'table' ? 'list' : viewMode === 'card' ? 'grid' : 'kanban',
    };
    if (searchTerm) params.search = searchTerm;
    if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
    if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
    if (selectedPriority !== 'all') params.priority = selectedPriority;
    if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
    if (filters.sort_field) {
      params.sort_field = filters.sort_field;
      params.sort_direction = filters.sort_direction ?? 'asc';
    }
    params.per_page = tasks?.per_page ?? filters.per_page ?? 20;
    Object.assign(params, overrides);
    return params;
  };

  const handleTableSort = (field: string) => {
    const direction =
      filters.sort_field === field && filters.sort_direction === 'asc' ? 'desc' : 'asc';
    router.get(
      route('tasks.index'),
      buildTaskIndexQuery({
        page: 1,
        sort_field: field,
        sort_direction: direction,
        view: 'list',
      }),
      { preserveState: true, preserveScroll: true },
    );
  };
  
  const handleFormSubmit = (formData: any) => {
      if (formMode === 'create') {
          router.post(route('tasks.store'), formData, {
              onSuccess: (page) => {
                  setTaskFormErrors({});
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
                  const normalized = normalizeInertiaValidationErrors(errors);
                  if (Object.keys(normalized).length > 0) {
                      setTaskFormErrors(normalized);
                  } else if (typeof errors === 'string') {
                      toast.error(errors);
                  }
              },
          });
      } else if (formMode === 'edit') {
          router.put(route('tasks.update', currentItem.id), formData, {
              onSuccess: (page) => {
                  setTaskFormErrors({});
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
                  const normalized = normalizeInertiaValidationErrors(errors);
                  if (Object.keys(normalized).length > 0) {
                      setTaskFormErrors(normalized);
                  } else if (typeof errors === 'string') {
                      toast.error(errors);
                  }
              },
          });
      }
  };

  const pageActions = [];
  if (hasPermission(permissions, 'create-tasks')) {
    pageActions.push({
      label: t('Add Task'),
      icon: <Plus className="h-4 w-4 mr-2" />,
      variant: 'default',
      onClick: () => handleAddNew()
    });
  }

  const breadcrumbs = [
      { title: t('Dashboard'), href: route('dashboard') },
      { title: t('Task & Workflow'), href: route('tasks.index') },
      { title: t('Tasks') },
  ];

  const taskTableColumns = [
    {
      key: 'task_id',
      label: t('Task ID'),
      sortable: true,
    },
    {
      key: 'case',
      label: t('Case'),
      render: (_: unknown, row: Task) => {
        const r = row as Task & {
          case_id?: number | null;
          case?: { id?: number; title?: string; case_id?: string } | null;
        };
        const caseItem = r.case;
        const caseId = caseItem?.id ?? r.case_id ?? null;
        const label = caseItem?.title || caseItem?.case_id || (caseId != null ? `#${caseId}` : '-');
        if (caseId == null) {
          return <span className="text-sm text-muted-foreground">{label}</span>;
        }
        return (
          <button
            type="button"
            className="text-left text-sm font-medium text-blue-600 transition-colors hover:text-blue-800 hover:underline"
            onClick={(e) => {
              e.stopPropagation();
              router.get(route('cases.show', caseId));
            }}
          >
            {label}
          </button>
        );
      },
    },
    {
      key: 'title',
      label: t('Task'),
      sortable: true,
      render: (_: unknown, row: Task) => (
        <div>
          <button
            type="button"
            className="cursor-pointer text-left text-sm font-medium text-gray-900 transition-colors hover:text-blue-600"
            onClick={() => handleAction('view', row.id)}
          >
            {row.title}
          </button>
          {row.description ? (
            <div className="max-w-xs truncate text-sm text-gray-500">{row.description}</div>
          ) : null}
        </div>
      ),
    },
    {
      key: 'priority',
      label: t('Priority'),
      sortable: true,
      render: (value: string) => (
        <Badge className={getTaskPriorityBadgeClassName(value)} variant="outline">
          {t(taskPriorityTranslationKey(value))}
        </Badge>
      ),
    },
    {
      key: 'task_status_id',
      label: t('Task Status'),
      render: (_: unknown, row: Task) => (
        <Badge
          variant="outline"
          style={{
            backgroundColor: (row.task_status?.color ?? '#ccc') + '20',
            borderColor: row.task_status?.color ?? '#ccc',
          }}
        >
          {localizedString(row.task_status?.name, i18n.language)}
        </Badge>
      ),
    },
    {
      key: 'assigned_to',
      label: t('Assignee'),
      render: (_: unknown, row: Task) => {
        const assignee = getTaskAssignee(row);
        if (!assignee) {
          return <span className="text-sm text-gray-400">{t('Unassigned')}</span>;
        }
        return (
          <div className="flex items-center">
            <div className="mx-2 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-medium text-white">
              {getInitials(assignee.name) || '?'}
            </div>
            <span className="text-sm">{assignee.name}</span>
          </div>
        );
      },
    },
    {
      key: 'progress',
      label: t('Progress'),
      render: (_: unknown, row: Task) => (
        <div className="flex items-center">
          <div className="mr-2 h-2 w-16 rounded-full bg-gray-200">
            <div className="h-2 rounded-full bg-green-600" style={{ width: `${row.progress}%` }} />
          </div>
          <span className="text-sm text-gray-900">{row.progress}%</span>
        </div>
      ),
    },
    {
      key: 'due_date',
      label: t('Due Date'),
      sortable: true,
      render: (_: unknown, row: Task) => {
        const due =
          (row as Task & { due_date?: string | null; due_date?: string | null }).due_date ??
          (row as Task & { due_date?: string | null }).due_date ??
          null;
        return (
          <div className="flex items-center gap-2 text-sm text-gray-900">
            {due && isTaskOverdue(due) && (
              <Badge variant="destructive" className="text-xs">
                <AlertTriangle className="mr-1 h-3 w-3" />
                {t('Overdue')}
              </Badge>
            )}
            <span>{due ? new Date(due).toLocaleDateString() : t('No due date')}</span>
          </div>
        );
      },
    },
    {
      key: 'task_type_id',
      label: t('Task Type'),
      render: (_: unknown, row: Task) => {
        const typeName = (row as Task & { task_type?: { name?: string | Record<string, string> } | null; taskType?: { name?: string | Record<string, string> } | null }).task_type?.name
          ?? (row as Task & { taskType?: { name?: string | Record<string, string> } | null }).taskType?.name;
        return <span className="text-sm">{localizedString(typeName, i18n.language) || '-'}</span>;
      },
    },
  ];

  const taskTableActions = [
    {
      label: t('View'),
      icon: 'Eye',
      action: 'view',
      className: 'text-blue-500',
      requiredPermission: 'view-tasks',
    },
    {
      label: t('Edit'),
      icon: 'Edit',
      action: 'edit',
      className: 'text-amber-500',
      requiredPermission: 'edit-tasks',
      condition: () => userWorkspaceRole !== 'client',
    },
    // {
    //   label: t('Duplicate'),
    //   icon: 'Copy',
    //   action: 'duplicate',
    //   className: 'text-green-500',
    //   condition: () => userWorkspaceRole !== 'client',
    // },
    {
      label: t('Delete'),
      icon: 'Trash2',
      action: 'delete',
      className: 'text-red-500',
      requiredPermission: 'delete-tasks',
      condition: () => userWorkspaceRole !== 'client',
    },
  ];
  
  return (
    <PageTemplate
      title={t('Tasks')}
      url="/tasks"
      actions={pageActions}
      breadcrumbs={breadcrumbs}
      noPadding
    >
      <Head title={t('Tasks')} />
      
      {/* Overview Row */}
      <Card className="mb-4 hover:shadow-md transition-shadow">
        <CardContent className="p-4">
          <div className="grid grid-cols-5 gap-4">
            <div className="text-center">
              <div className="text-xl font-bold text-blue-600">
                {Array.isArray(tasks) ? tasks.length : (tasks?.total || 0)}
              </div>
              <div className="text-xs text-gray-600">{t('Total Tasks')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-yellow-600">
                {(Array.isArray(tasks) ? tasks : tasks?.data || []).filter((task) => !taskHasAssigneeId(task)).length}
              </div>
              <div className="text-xs text-gray-600">{t('Unassigned')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-green-600">
                {(Array.isArray(tasks) ? tasks : tasks?.data || []).filter((task) => taskHasAssigneeId(task)).length}
              </div>
              <div className="text-xs text-gray-600">{t('Assigned')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-red-600">
                {(Array.isArray(tasks) ? tasks : tasks?.data || []).filter(task => task.due_date && isTaskOverdue(task.due_date)).length}
              </div>
              <div className="text-xs text-gray-600">{t('Overdue')}</div>
            </div>
            <div className="text-center">
              <div className="text-xl font-bold text-orange-600">
                {(Array.isArray(tasks) ? tasks : tasks?.data || []).filter(task => task.priority === 'high' || task.priority === 'critical').length}
              </div>
              <div className="text-xs text-gray-600">{t('High Priority')}</div>
            </div>
          </div>
        </CardContent>
      </Card>
      
      {/* Filters Row */}
      <div className="bg-white rounded-lg shadow mb-4">
        <div className="p-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center gap-2">
              <form onSubmit={handleSearch} className="flex gap-2">
                <div className="relative w-64">
                  <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                  <Input
                    placeholder={t('Search...')}
                    value={searchTerm}
                    onChange={(e) => {
                      setSearchTerm(e.target.value);
                      clearTimeout(window.searchTimeout);
                      window.searchTimeout = setTimeout(() => {
                        const params: any = { page: 1 };
                        if (e.target.value) params.search = e.target.value;
                        if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
                        if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
                        if (selectedPriority !== 'all') params.priority = selectedPriority;
                        if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
                        params.view = viewModeToQueryParam(viewModeRef.current);
                        router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
                      }, 500);
                    }}
                    className="w-full pl-9"
                  />
                </div>
                <Button type="submit" size="sm">
                  <Search className="h-4 w-4 mr-1.5" />
                  {t('Search')}
                </Button>
              </form>
              
              <Button
                variant={hasActiveFilters() ? "default" : "outline"}
                size="sm"
                onClick={() => setShowFilters(!showFilters)}
              >
                <Filter className="h-4 w-4 mr-1.5" />
                {showFilters ? t('Hide Filters') : t('Filters')}
                {hasActiveFilters() && (
                  <span className="ml-1 bg-primary-foreground text-primary rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                        {activeFilterCount()}
                                    </span>
                )}
              </Button>
            </div>
            
            <div className="flex items-center gap-2">
              <div className="flex items-center gap-1 border rounded-md p-1">
                <Button
                  variant={viewMode === 'card' ? 'default' : 'ghost'}
                  size="sm"
                  onClick={() => {
                    setViewMode('card');
                    const params: any = { page: 1, view: 'grid' };
                    if (searchTerm) params.search = searchTerm;
                    if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
                    if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
                    if (selectedPriority !== 'all') params.priority = selectedPriority;
                    if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
                    router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
                  }}
                  className="h-7 px-2"
                >
                  <LayoutGrid className="h-4 w-4" />
                </Button>
                <Button
                  variant={viewMode === 'table' ? 'default' : 'ghost'}
                  size="sm"
                  onClick={() => {
                    setViewMode('table');
                    const params: any = { page: 1, view: 'list' };
                    if (searchTerm) params.search = searchTerm;
                    if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
                    if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
                    if (selectedPriority !== 'all') params.priority = selectedPriority;
                    if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
                    router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
                  }}
                  className="h-7 px-2"
                >
                  <List className="h-4 w-4" />
                </Button>
                <Button
                  variant={viewMode === 'kanban' ? 'default' : 'ghost'}
                  size="sm"
                  onClick={() => {
                    setViewMode('kanban');
                    const params: any = { view: 'kanban' };
                    if (searchTerm) params.search = searchTerm;
                    if (selectedTaskStatus !== 'all') params.task_status_id = selectedTaskStatus;
                    if (selectedTaskType !== 'all') params.task_type_id = selectedTaskType;
                    if (selectedPriority !== 'all') params.priority = selectedPriority;
                    if (selectedAssignee !== 'all') params.assigned_to = selectedAssignee;
                    router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true });
                  }}
                  className="h-7 px-2"
                >
                  <Columns className="h-4 w-4" />
                </Button>
              </div>
            </div>
          </div>
          
          {showFilters && (
            <div className="p-4 bg-gray-50 border rounded-md">
              <div className="flex flex-wrap gap-4 items-end">
                <div className="space-y-2">
                  <Label>{t('Task Status')}</Label>
                  <Select value={selectedTaskStatus} onValueChange={(value) => handleFilter('task_status_id', value)}>
                    <SelectTrigger className="w-40">
                      <SelectValue placeholder={t('All')} />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">{t('All')}</SelectItem>
                      {taskStatuses.map((ts) => (
                        <SelectItem key={ts.id} value={ts.id.toString()}>
                          {localizedString(ts.name, i18n.language)}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label>{t('Task Type')}</Label>
                  <Select value={selectedTaskType} onValueChange={(value) => handleFilter('task_type_id', value)}>
                    <SelectTrigger className="w-44">
                      <SelectValue placeholder={t('All')} />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">{t('All')}</SelectItem>
                      {(taskTypes || []).map((type) => (
                        <SelectItem key={type.id} value={String(type.id)}>
                          {resolveTaskTypeName(type)}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                
                <div className="space-y-2">
                  <Label>{t('Priority')}</Label>
                  <Select value={selectedPriority} onValueChange={(value) => handleFilter('priority', value)}>
                    <SelectTrigger className="w-40">
                      <SelectValue placeholder="All Priority" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">{t('All Priority')}</SelectItem>
                      <SelectItem value="low">{t(taskPriorityTranslationKey('low'))}</SelectItem>
                      <SelectItem value="medium">{t(taskPriorityTranslationKey('medium'))}</SelectItem>
                      <SelectItem value="high">{t(taskPriorityTranslationKey('high'))}</SelectItem>
                      <SelectItem value="critical">{t(taskPriorityTranslationKey('critical'))}</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                
                <div className="space-y-2">
                  <Label>{t('Assignee')}</Label>
                  <Select value={selectedAssignee} onValueChange={(value) => handleFilter('assigned_to', value)}>
                    <SelectTrigger className="w-40">
                      <SelectValue placeholder="All Assignees" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">{t('All Assignees')}</SelectItem>
                      {users.map((member) => (
                        <SelectItem key={member.id} value={member.id.toString()}>
                          {member.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                
                <Button
                  variant="outline"
                  size="sm"
                  className="h-9"
                  onClick={handleResetFilters}
                  disabled={!hasActiveFilters()}
                >
                  {t('Reset Filters')}
                </Button>
              </div>
            </div>
          )}
        </div>
      </div>
      
      {/* Tasks Content */}
      <div className="bg-white rounded-lg shadow">
        {viewMode === 'kanban' ? (
          <div className="bg-gray-50 p-4 rounded-lg overflow-hidden">
            <style>{`
                            .kanban-scroll::-webkit-scrollbar {
                                height: 8px;
                            }
                            .kanban-scroll::-webkit-scrollbar-track {
                                background: #f1f5f9;
                                border-radius: 4px;
                            }
                            .kanban-scroll::-webkit-scrollbar-thumb {
                                background: #cbd5e1;
                                border-radius: 4px;
                            }
                            .kanban-scroll::-webkit-scrollbar-thumb:hover {
                                background: #94a3b8;
                            }
                            .column-scroll::-webkit-scrollbar {
                                width: 6px;
                            }
                            .column-scroll::-webkit-scrollbar-track {
                                background: #f8fafc;
                                border-radius: 3px;
                            }
                            .column-scroll::-webkit-scrollbar-thumb {
                                background: #e2e8f0;
                                border-radius: 3px;
                            }
                            .column-scroll::-webkit-scrollbar-thumb:hover {
                                background: #cbd5e1;
                            }
                        `}</style>
            <div className="flex gap-4 overflow-x-auto pb-4 kanban-scroll" style={{ height: 'calc(100vh - 280px)', width: '100%' }}>
              {taskStatuses.map((columnStatus) => {
                const columnTasks = (Array.isArray(tasks) ? tasks : tasks?.data || []).filter(
                  (task) => task.task_status?.id === columnStatus.id,
                );
                return (
                  <div
                    key={columnStatus.id}
                    className="flex-shrink-0"
                    style={{ minWidth: 'calc(20% - 16px)', width: 'calc(20% - 16px)' }}
                    onDrop={(e) => {
                      e.preventDefault();
                      e.currentTarget.classList.remove('bg-blue-50');
                      const taskId = e.dataTransfer.getData('taskId');
                      if (taskId) {
                        router.put(route('tasks.update-task-status', taskId), {
                          task_status_id: columnStatus.id,
                        }, {
                          onSuccess: () => {
                            toast.dismiss();
                          },
                          onError: () => {
                            toast.dismiss();
                            toast.error('Failed to update task status');
                          }
                        });
                      }
                    }}
                    onDragOver={(e) => {
                      e.preventDefault();
                      e.currentTarget.classList.add('bg-blue-50');
                    }}
                    onDragLeave={(e) => {
                      e.currentTarget.classList.remove('bg-blue-50');
                    }}
                  >
                    <div className="bg-gray-100 rounded-lg h-full flex flex-col">
                      <div className="p-3 border-b border-gray-200">
                        <div className="flex items-center justify-between">
                          <h3 className="font-semibold text-sm text-gray-700">{localizedString(columnStatus.name, i18n.language)}</h3>
                          <span className="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                                                        {columnTasks.length}
                                                    </span>
                        </div>
                      </div>
                      <div className="p-2 space-y-2 overflow-y-auto flex-1 column-scroll" style={{ maxHeight: 'calc(100vh - 350px)' }}>
                        {columnTasks.map((task) => (
                          <div
                            key={task.id}
                            draggable
                            onDragStart={(e) => {
                              e.dataTransfer.setData('taskId', task.id.toString());
                              e.currentTarget.classList.add('opacity-50', 'scale-95');
                            }}
                            onDragEnd={(e) => {
                              e.currentTarget.classList.remove('opacity-50', 'scale-95');
                            }}
                            className="cursor-move transition-all duration-200"
                          >
                            <Card className="hover:shadow-md transition-all duration-200 border-l-4 hover:scale-105" style={{ borderLeftColor: columnStatus.color }}>
                              <CardContent className="p-3">
                                <div className="space-y-2">
                                  <div className="flex items-start justify-between">
                                    <h4
                                      className="font-medium text-sm line-clamp-2 hover:text-blue-600 transition-colors cursor-pointer flex-1"
                                      onClick={() => handleAction('view', task.id)}
                                    >
                                      {task.title}
                                    </h4>
                                    <div className="flex gap-1">
                                      <Button
                                        variant="ghost"
                                        size="icon"
                                        onClick={(e) => {
                                          e.stopPropagation();
                                          handleAction('view', task.id);
                                        }}
                                        className="h-6 w-6 text-blue-500 hover:text-blue-700"
                                      >
                                        <Eye className="h-3 w-3" />
                                      </Button>
                                      {userWorkspaceRole !== 'client' && (
                                        <>
                                          <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={(e) => {
                                              e.stopPropagation();
                                              handleAction('edit', task.id);
                                            }}
                                            className="h-6 w-6 text-amber-500 hover:text-amber-700"
                                          >
                                            <Edit className="h-3 w-3" />
                                          </Button>
                                          <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={(e) => {
                                              e.stopPropagation();
                                              handleAction('delete', task.id);
                                            }}
                                            className="h-6 w-6 text-red-500 hover:text-red-700"
                                          >
                                            <Trash2 className="h-3 w-3" />
                                          </Button>
                                        </>
                                      )}
                                    </div>
                                  </div>
                                  
                                  {task.description && (
                                    <p className="text-xs text-gray-600 line-clamp-2">{task.description}</p>
                                  )}
                                  
                                  <div className="flex items-center justify-between">
                                    <TaskPriority priority={task.priority} showIcon />
                                    {getTaskAssignee(task) && (
                                      <div
                                        className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary text-[10px] font-medium text-white"
                                        title={getTaskAssignee(task)!.name}
                                        aria-label={`${t('Assignee')}: ${getTaskAssignee(task)!.name}`}
                                      >
                                        {getInitials(getTaskAssignee(task)!.name) || '?'}
                                      </div>
                                    )}
                                  </div>
                                  
                                  <div className="space-y-1">
                                    <div className="flex justify-between text-xs">
                                      <span>{t('Progress')}</span>
                                      <span>{task.progress}%</span>
                                    </div>
                                    <div className="h-1 w-full rounded-full bg-gray-200">
                                      <div
                                        className="h-1 rounded-full bg-green-600 transition-all"
                                        style={{ width: `${task.progress}%` }}
                                      />
                                    </div>
                                  </div>
                                  
                                  <div className="flex justify-between items-center text-xs text-gray-500">
                                    <div className="flex items-center gap-2">
                                      {task.due_date && isTaskOverdue(task.due_date) && (
                                        <Badge variant="destructive" className="text-xs">
                                          <AlertTriangle className="h-3 w-3 mr-1" />
                                          {t('Overdue')}
                                        </Badge>
                                      )}
                                      <span>{task.due_date ? new Date(task.due_date).toLocaleDateString() : t('No due date')}</span>
                                    </div>
                                  </div>
                                </div>
                              </CardContent>
                            </Card>
                          </div>
                        ))}
                        {columnTasks.length === 0 && (
                          <div className="text-center py-8 text-gray-400">
                            <CheckSquare className="h-8 w-8 mx-auto mb-2" />
                            <p className="text-sm">{t('No tasks')}</p>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        ) : viewMode === 'card' ? (
          <div className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              {tasks?.data?.map((task: Task) => (
                <Card key={`card-${task.id}`} className="overflow-hidden hover:shadow-md transition-shadow">
                  <CardHeader className="pb-2">
                    <div className="flex justify-between items-start">
                      <CardTitle
                        className="text-base line-clamp-1 cursor-pointer hover:text-blue-600 transition-colors"
                        onClick={() => handleAction('view', task.id)}
                      >
                        {task.title}
                      </CardTitle>
                      <div className="flex gap-1">
                        <TaskStatusChanger
                          task={task}
                          taskStatuses={taskStatuses}
                          variant="select"
                        />
                      </div>
                    </div>
                    <p className="text-sm text-muted-foreground line-clamp-2">{task.description || t('No description')}</p>
                  </CardHeader>
                  
                  <CardContent className="py-2">
                    <div className="space-y-3">
                      <div className="space-y-1">
                        <div className="flex justify-between text-xs">
                          <span>{t('Progress')}</span>
                          <span>{task.progress}%</span>
                        </div>
                        <div className="h-1 w-full rounded-full bg-gray-200">
                          <div
                            className="h-1 rounded-full bg-green-600 transition-all"
                            style={{ width: `${task.progress}%` }}
                          />
                        </div>
                      </div>
                      
                      <div className="flex justify-between items-center text-xs">
                        <TaskPriority priority={task.priority} showIcon />
                        <div className="flex items-center gap-2">
                          {task.due_date && isTaskOverdue(task.due_date) && (
                            <Badge variant="destructive" className="text-xs">
                              <AlertTriangle className="h-3 w-3 mr-1" />
                              {t('Overdue')}
                            </Badge>
                          )}
                          <span className="text-muted-foreground">
                              {task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No due date'}
                          </span>
                        </div>
                      </div>
                      
                      <div className="flex items-center">
                        <div className="flex items-center space-x-2">
                          {getTaskAssignee(task) ? (
                            <Tooltip>
                              <TooltipTrigger asChild>
                                <div className="flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-primary text-xs font-medium text-white">
                                  {getInitials(getTaskAssignee(task)!.name) || '?'}
                                </div>
                              </TooltipTrigger>
                              <TooltipContent>
                                {getTaskAssignee(task)!.name}
                              </TooltipContent>
                            </Tooltip>
                          ) : (
                            <div className="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center">
                              <UserIcon className="h-3 w-3 text-gray-400" />
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  </CardContent>
                  
                  <CardFooter className="flex justify-end gap-1 pt-0 pb-2">
                    <Tooltip>
                      <TooltipTrigger asChild>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => handleAction('view', task.id)}
                          className="text-blue-500 hover:text-blue-700 h-8 w-8"
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                      </TooltipTrigger>
                      <TooltipContent>View</TooltipContent>
                    </Tooltip>
                    {userWorkspaceRole !== 'client' && (
                      <>
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => handleAction('edit', task.id)}
                              className="text-amber-500 hover:text-amber-700 h-8 w-8"
                            >
                              <Edit className="h-4 w-4" />
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>Edit</TooltipContent>
                        </Tooltip>
                        {/*<Tooltip>*/}
                        {/*  <TooltipTrigger asChild>*/}
                        {/*    <Button*/}
                        {/*      variant="ghost"*/}
                        {/*      size="icon"*/}
                        {/*      onClick={() => handleAction('duplicate', task.id)}*/}
                        {/*      className="text-green-500 hover:text-green-700 h-8 w-8"*/}
                        {/*    >*/}
                        {/*      <Copy className="h-4 w-4" />*/}
                        {/*    </Button>*/}
                        {/*  </TooltipTrigger>*/}
                        {/*  <TooltipContent>Duplicate</TooltipContent>*/}
                        {/*</Tooltip>*/}
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => handleAction('delete', task.id)}
                              className="text-red-500 hover:text-red-700 h-8 w-8"
                            >
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent>Delete</TooltipContent>
                        </Tooltip>
                      </>
                    )}
                  </CardFooter>
                </Card>
              ))}
            </div>
          </div>
        ) : (
          <div className="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
            <CrudTable
              columns={taskTableColumns}
              actions={taskTableActions}
              data={tasks?.data || []}
              from={tasks?.from ?? 1}
              onAction={(action, row) => handleAction(action, row.id)}
              sortField={filters.sort_field}
              sortDirection={filters.sort_direction === 'desc' ? 'desc' : filters.sort_direction === 'asc' ? 'asc' : undefined}
              onSort={handleTableSort}
              permissions={permissions}
              entityPermissions={{
                view: 'view-tasks',
                edit: 'edit-tasks',
                delete: 'delete-tasks',
              }}
            />
            <Pagination
              from={tasks?.from || 0}
              to={tasks?.to || 0}
              total={tasks?.total || 0}
              links={tasks?.links}
              entityName={t('tasks')}
              onPageChange={(url) => router.get(url)}
              perPage={String(tasks?.per_page ?? filters.per_page ?? 20)}
              perPageOptions={[20, 50, 100]}
              onPerPageChange={(value) => {
                router.get(
                  route('tasks.index'),
                  buildTaskIndexQuery({
                    page: 1,
                    per_page: parseInt(value, 10),
                    view: 'list',
                  }),
                  { preserveState: true, preserveScroll: true },
                );
              }}
            />
          </div>
        )}
      </div>
      
      {/* Empty State */}
      {viewMode !== 'table' && tasks?.data?.length === 0 && (
        <div className="bg-white rounded-lg shadow p-8 text-center">
          <CheckSquare className="h-12 w-12 mx-auto mb-4 text-gray-400" />
          <h3 className="text-lg font-semibold mb-2">{t('No Tasks Found')}</h3>
          <p className="text-gray-500 mb-4">
            {hasActiveFilters() ? t('No tasks match your current filters.') : t('No tasks have been created yet.')}
          </p>
          {hasActiveFilters() ? (
            <Button variant="outline" onClick={handleResetFilters}>
              {t('Clear Filters')}
            </Button>
          ) : (
            <Button onClick={handleAddNew}>
              <Plus className="h-4 w-4 mr-2" />
              {t('Create your first task')}
            </Button>
          )}
        </div>
      )}
      
      {/* Pagination — card view only (table uses Pagination inside list layout) */}
      {tasks?.links && viewMode === 'card' && !Array.isArray(tasks) && (
        <div className="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-gray-800">
          <Pagination
            from={tasks?.from || 0}
            to={tasks?.to || 0}
            total={tasks?.total || 0}
            links={tasks?.links}
            entityName={t('tasks')}
            onPageChange={(url) => router.get(url)}
            perPage={String(tasks?.per_page ?? filters.per_page ?? 20)}
            perPageOptions={[20, 50, 100]}
            onPerPageChange={(value) => {
              router.get(
                route('tasks.index'),
                buildTaskIndexQuery({
                  page: 1,
                  per_page: parseInt(value, 10),
                  view: 'grid',
                }),
                { preserveState: true, preserveScroll: true },
              );
            }}
          />
        </div>
      )}
      
      {/* Modals */}
      {selectedTask && (
        <TaskModal
          task={selectedTask}
          isOpen={isModalOpen}
          onClose={() => {
            setIsModalOpen(false);
            setSelectedTask(null);
          }}
          members={users}
          taskStatuses={taskStatuses}
        />
      )}
      
      <CrudFormModal
        isOpen={isFormModalOpen}
        onClose={() => {
          setTaskFormErrors({});
          setIsFormModalOpen(false);
        }}
        onSubmit={handleFormSubmit}
        externalErrors={taskFormErrors}
        formConfig={{
          fields: [
            { name: 'title', label: t('Title'), type: 'text', required: true },
            { name: 'description', label: t('Description'), type: 'textarea' },
            {
              name: 'priority',
              label: t('Priority'),
              type: 'select',
              required: true,
              options: [
                { value: 'critical', label: t(taskPriorityTranslationKey('critical')) },
                { value: 'high', label: t(taskPriorityTranslationKey('high')) },
                { value: 'medium', label: t(taskPriorityTranslationKey('medium')) },
                { value: 'low', label: t(taskPriorityTranslationKey('low')) },
              ],
              defaultValue: 'medium',
            },
            { name: 'start_date', label: t('Start Date'), type: 'date' },
            { name: 'due_date', label: t('Due Date'), type: 'date' },
            { name: 'estimated_duration', label: t('Estimated Duration (hours)'), type: 'number' },
            {
              name: 'case_id',
              label: t('Case'),
              type: 'select',
              placeholder: t('Select Case'),
              defaultValue: TASK_FORM_NO_CASE,
              options: [
                { value: TASK_FORM_NO_CASE, label: t('No case') },
                ...(cases || []).map((c: { id: number; title?: string; case_id?: string }) => ({
                  value: String(c.id),
                  label: c.title || c.case_id || `Case ${c.id}`,
                })),
              ],
            },
            {
              name: 'assigned_to',
              label: t('Assigned To'),
              type: 'select',
              placeholder: t('Select assignee'),
              defaultValue: TASK_FORM_UNASSIGNED,
              options: [
                { value: TASK_FORM_UNASSIGNED, label: t('Unassigned') },
                ...(users || []).map((u) => ({
                  value: String(u.id),
                  label: u.name,
                })),
              ],
            },
            {
              name: 'task_type_id',
              label: t('Task Type'),
              type: 'select',
              placeholder: t('Select Task Type'),
              options: [
                ...(taskTypes || []).map((type: any) => ({
                  value: type.id.toString(),
                  label: resolveTaskTypeName(type),
                })),
              ],
            },
            {
              name: 'task_status_id',
              label: t('Task Status'),
              type: 'select',
              placeholder: t('Select Task Status'),
              options: [
                ...(taskStatuses || []).map((status: any) => ({
                  value: status.id.toString(),
                  label: status.name,
                })),
              ],
            },
          ],
          modalSize: 'xl',
          transformData: transformTaskCrudFormData,
        }}
        initialData={currentItem ? {
          ...currentItem,
          priority: normalizeTaskFormPriority(currentItem.priority),
          case_id: (() => {
            const row = currentItem as Task & { case_id?: number | null; case?: { id?: number } | null };
            const id = row.case?.id ?? row.case_id ?? null;
            return id != null ? String(id) : TASK_FORM_NO_CASE;
          })(),
          assigned_to: (() => {
            const id = getTaskAssigneeId(currentItem);
            return id != null ? String(id) : TASK_FORM_UNASSIGNED;
          })(),
          task_type_id: currentItem.task_type_id != null ? String(currentItem.task_type_id) : (currentItem.task_type?.id != null ? String(currentItem.task_type.id) : ''),
          task_status_id: currentItem.task_status_id != null ? String(currentItem.task_status_id) : (currentItem.task_status?.id != null ? String(currentItem.task_status.id) : ''),
        } : undefined}
        title={formMode === 'create' ? t('Add New Task') : formMode === 'edit' ? t('Edit Task') : t('View Task')}
        mode={formMode}
      />
      
      {/* Delete Modal */}
      <CrudDeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => {
          setIsDeleteModalOpen(false);
          setTaskToDelete(null);
        }}
        onConfirm={handleDeleteConfirm}
        itemName={taskToDelete?.title || ''}
        entityName={t('Task')}
      />
    </PageTemplate>
  );
}