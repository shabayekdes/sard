import { toast } from '@/components/custom-toast';
import TaskAttachments from '@/components/tasks/TaskAttachments';
import TaskChecklist from '@/components/tasks/TaskChecklist';
import TaskComments from '@/components/tasks/TaskComments';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Task, TaskStatusOption, User as UserType } from '@/types';
import { hasPermission } from '@/utils/authorization';
import { localizedString } from '@/utils/i18n';
import { taskPriorityTranslationKey } from '@/utils/taskPriority';
import { normalizeInertiaValidationErrors } from '@/utils/inertiaErrors';
import { getTaskAssignee, getTaskAssigneeId } from '@/utils/taskTable';
import { cn } from '@/lib/utils';
import { router, usePage } from '@inertiajs/react';
import { CheckSquare, MessageSquare, Paperclip } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

interface Props {
    task: Task;
    isOpen: boolean;
    onClose: () => void;
    members?: UserType[];
    taskStatuses: TaskStatusOption[];
}

export default function TaskModal({ task, isOpen, onClose, members = [], taskStatuses }: Props) {
    const { t, i18n } = useTranslation();
    const { auth } = usePage().props as { auth?: { permissions?: string[] } };
    const permissions: string[] = Array.isArray(auth?.permissions) ? auth.permissions : [];
    const [currentTask, setCurrentTask] = useState(task);
    const [dateFieldErrors, setDateFieldErrors] = useState<Record<string, string>>({});

    const canEditTask = hasPermission(permissions, 'edit-tasks');
    const canAssignTask = hasPermission(permissions, 'edit-tasks') || hasPermission(permissions, 'assign-tasks');
    const canChangeTaskStatus = hasPermission(permissions, 'task_change_status') || hasPermission(permissions, 'toggle-status-tasks');

    useEffect(() => {
        setCurrentTask(task);
        setDateFieldErrors({});
    }, [task]);

    const refreshTask = async () => {
        try {
            const response = await fetch(route('tasks.show', task.id));
            const data = await response.json();
            setCurrentTask(data.task);
        } catch (error) {
            console.error('Failed to refresh task:', error);
        }
    };

    const handleTaskStatusChange = (taskStatusId: string) => {
        router.put(
            route('tasks.update-task-status', task.id),
            {
                task_status_id: taskStatusId,
            },
            {
                onSuccess: () => {
                    refreshTask();
                },
                onError: () => {
                    toast.error(t('Failed to update task status'));
                },
            },
        );
    };

    const handlePriorityChange = (priority: string) => {
        router.put(
            route('tasks.update', task.id),
            {
                title: currentTask.title,
                description: currentTask.description || '',
                priority: priority,
                start_date: currentTask.start_date,
                due_date: currentTask.due_date,
                assigned_to: getTaskAssigneeId(currentTask),
            },
            {
                onSuccess: () => {
                    refreshTask();
                },
                onError: () => {
                    toast.error(t('Failed to update priority'));
                },
            },
        );
    };

    const handleAssigneeChange = (assigneeId: string) => {
        const assignedUserId = assigneeId === 'unassigned' ? null : parseInt(assigneeId);

        router.put(
            route('tasks.update', task.id),
            {
                title: currentTask.title,
                description: currentTask.description || '',
                priority: currentTask.priority,
                start_date: currentTask.start_date,
                due_date: currentTask.due_date,
                assigned_to: assignedUserId,
            },
            {
                onSuccess: () => {
                    refreshTask();
                },
                onError: () => {
                    toast.error(t('Failed to update assignee'));
                },
            },
        );
    };

    const handleDateChange = (field: string, value: string) => {
        setDateFieldErrors((prev) => {
            const next = { ...prev };
            delete next.start_date;
            delete next.due_date;
            return next;
        });
        router.put(
            route('tasks.update', task.id),
            {
                title: currentTask.title,
                description: currentTask.description || '',
                priority: currentTask.priority,
                start_date: field === 'start_date' ? value || null : currentTask.start_date,
                due_date: field === 'due_date' ? value || null : currentTask.due_date,
                assigned_to: getTaskAssigneeId(currentTask),
            },
            {
                onSuccess: () => {
                    setDateFieldErrors({});
                    refreshTask();
                },
                onError: (errors) => {
                    setDateFieldErrors((prev) => ({ ...prev, ...normalizeInertiaValidationErrors(errors) }));
                },
            },
        );
    };

    const getPriorityColor = (priority: string) => {
        switch (priority) {
            case 'critical':
                return 'bg-red-100 text-red-800';
            case 'high':
                return 'bg-orange-100 text-orange-800';
            case 'medium':
                return 'bg-yellow-100 text-yellow-800';
            case 'low':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getPriorityLabel = (priority: string) => t(taskPriorityTranslationKey(priority));

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="max-h-[95vh] max-w-7xl overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{currentTask.title}</DialogTitle>
                </DialogHeader>

                <div className="grid grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="col-span-2 space-y-6">
                        {/* Description */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Description')}</h3>
                            <div className="text-sm text-gray-600">{currentTask.description || t('No description provided')}</div>
                        </div>

                        {/* Tabs for Comments, Checklist, Attachments */}
                        <Tabs defaultValue="comments" className="w-full" dir={i18n.dir()}>
                            <TabsList>
                                <TabsTrigger value="comments" className="gap-2">
                                    <MessageSquare className="h-4 w-4 shrink-0" />
                                    <span className="text-start">
                                        {t('Comments')} ({task.comments?.length || 0})
                                    </span>
                                </TabsTrigger>
                                <TabsTrigger value="checklist" className="gap-2">
                                    <CheckSquare className="h-4 w-4 shrink-0" />
                                    <span className="text-start">
                                        {t('Checklist')} ({task.checklists?.length || 0})
                                    </span>
                                </TabsTrigger>
                                <TabsTrigger value="attachments" className="gap-2">
                                    <Paperclip className="h-4 w-4 shrink-0" />
                                    <span className="text-start">
                                        {t('Files')} ({currentTask.attachments?.length || 0})
                                    </span>
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent value="comments" className="space-y-4">
                                <TaskComments
                                    task={currentTask}
                                    comments={currentTask.comments || []}
                                    currentUser={members[0]}
                                    onUpdate={refreshTask}
                                />
                            </TabsContent>

                            <TabsContent value="checklist" className="space-y-4">
                                <TaskChecklist
                                    task={currentTask}
                                    checklist={currentTask.checklists || []}
                                    onUpdate={refreshTask}
                                />
                            </TabsContent>

                            <TabsContent value="attachments" className="space-y-4">
                                <TaskAttachments
                                    task={currentTask}
                                    attachments={currentTask.attachments || []}
                                    availableMedia={[]}
                                    onUpdate={refreshTask}
                                />
                            </TabsContent>
                        </Tabs>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Task status */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Task Status')}</h3>
                            {canChangeTaskStatus ? (
                                <Select
                                    value={
                                        currentTask.task_status_id != null && currentTask.task_status_id !== undefined
                                            ? String(currentTask.task_status_id)
                                            : undefined
                                    }
                                    onValueChange={handleTaskStatusChange}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select')} />
                                    </SelectTrigger>
                                    <SelectContent className="z-[9999]">
                                        {taskStatuses.map((ts) => (
                                            <SelectItem key={ts.id} value={ts.id.toString()}>
                                                <div className="flex items-center space-x-2">
                                                    <div className="h-3 w-3 rounded-full" style={{ backgroundColor: ts.color }} />
                                                    <span>{localizedString(ts.name, i18n.language)}</span>
                                                </div>
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            ) : (
                                <div className="flex items-center space-x-2 rounded bg-gray-50 p-2">
                                    <div className="h-3 w-3 rounded-full" style={{ backgroundColor: currentTask.task_status?.color }} />
                                    <span>{localizedString(currentTask.task_status?.name, i18n.language)}</span>
                                </div>
                            )}
                        </div>

                        {/* Priority */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Priority')}</h3>
                            {canEditTask ? (
                                <Select value={currentTask.priority} onValueChange={handlePriorityChange}>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent className="z-[9999]">
                                        <SelectItem value="low">{t(taskPriorityTranslationKey('low'))}</SelectItem>
                                        <SelectItem value="medium">{t(taskPriorityTranslationKey('medium'))}</SelectItem>
                                        <SelectItem value="high">{t(taskPriorityTranslationKey('high'))}</SelectItem>
                                        <SelectItem value="critical">{t(taskPriorityTranslationKey('critical'))}</SelectItem>
                                    </SelectContent>
                                </Select>
                            ) : (
                                <div className={`inline-flex rounded px-2 py-1 text-sm ${getPriorityColor(currentTask.priority)}`}>
                                    {getPriorityLabel(currentTask.priority)}
                                </div>
                            )}
                        </div>

                        {/* Assignee */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Assignee')}</h3>
                            {canAssignTask ? (
                                <Select
                                    value={getTaskAssigneeId(currentTask)?.toString() ?? 'unassigned'}
                                    onValueChange={handleAssigneeChange}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select assignee')} />
                                    </SelectTrigger>
                                    <SelectContent className="z-[9999]">
                                        <SelectItem value="unassigned">{t('Unassigned')}</SelectItem>
                                        {(() => {
                                            const projectMembers =
                                                currentTask.project?.members?.filter((m) => m.user?.type !== 'client').map((m) => m.user) || [];
                                            const base = projectMembers.length > 0 ? projectMembers : members;
                                            const currentAssignee = getTaskAssignee(currentTask);
                                            const ids = new Set(base.map((m) => m.id));
                                            if (currentAssignee && !ids.has(currentAssignee.id)) {
                                                return [
                                                    { id: currentAssignee.id, name: currentAssignee.name },
                                                    ...base,
                                                ];
                                            }
                                            return base;
                                        })().map((member) => (
                                            <SelectItem key={member.id} value={member.id.toString()}>
                                                {member.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            ) : (
                                <div className="text-sm text-gray-600">
                                    {getTaskAssignee(currentTask)?.name ?? t('Unassigned')}
                                </div>
                            )}
                        </div>
                      
                        {/* Dates */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Dates')}</h3>
                            <div className="space-y-2">
                                <div>
                                    <label className="text-xs text-gray-500">{t('Start Date')}</label>
                                    {canEditTask ? (
                                        <Input
                                            type="date"
                                            value={currentTask.start_date?.split('T')[0] || ''}
                                            onChange={(e) => handleDateChange('start_date', e.target.value)}
                                            className={cn('mt-1', dateFieldErrors.start_date && 'border-red-500')}
                                        />
                                    ) : (
                                        <div className="mt-1 text-sm text-gray-600">
                                            {currentTask.start_date ? new Date(currentTask.start_date).toLocaleDateString() : t('Not set')}
                                        </div>
                                    )}
                                    {dateFieldErrors.start_date && (
                                        <p className="mt-1 text-xs text-red-600">{dateFieldErrors.start_date}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-xs text-gray-500">{t('Due Date')}</label>
                                    {canEditTask ? (
                                        <Input
                                            type="date"
                                            value={currentTask.due_date?.split('T')[0] || ''}
                                            onChange={(e) => handleDateChange('due_date', e.target.value)}
                                            className={cn('mt-1', dateFieldErrors.due_date && 'border-red-500')}
                                        />
                                    ) : (
                                        <div className="mt-1 text-sm text-gray-600">
                                            {currentTask.due_date ? new Date(currentTask.due_date).toLocaleDateString() : t('Not set')}
                                        </div>
                                    )}
                                    {dateFieldErrors.due_date && (
                                        <p className="mt-1 text-xs text-red-600">{dateFieldErrors.due_date}</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Progress */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Progress')}</h3>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">{currentTask.progress}%</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-gray-200">
                                    <div className="h-2 rounded-full bg-green-600 transition-all" style={{ width: `${currentTask.progress}%` }} />
                                </div>
                            </div>
                        </div>

                        {/* Project & Milestone */}
                        <div>
                            <h3 className="mb-2 text-sm font-medium text-gray-900">{t('Case')}</h3>
                            {(() => {
                                const taskWithCase = currentTask as Task & {
                                    case_id?: number | null;
                                    case?: { id?: number; title?: string; case_id?: string } | null;
                                };
                                const caseItem = taskWithCase.case;
                                const caseId = caseItem?.id ?? taskWithCase.case_id ?? null;
                                const label = caseItem?.title || caseItem?.case_id || (caseId != null ? `#${caseId}` : '-');

                                if (caseId == null) {
                                    return <span className="text-sm text-gray-600">{label}</span>;
                                }

                                return (
                                    <button
                                        type="button"
                                        className="text-left text-sm font-medium text-blue-600 transition-colors hover:text-blue-800 hover:underline"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            onClose();
                                            router.get(route('cases.show', caseId));
                                        }}
                                    >
                                        {label}
                                    </button>
                                );
                            })()}
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
