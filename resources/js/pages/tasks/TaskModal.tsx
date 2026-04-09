import React, { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Calendar, User, MessageSquare, CheckSquare, Paperclip, Edit, Save, X } from 'lucide-react';
import { Task, User as UserType, TaskStatusOption, ProjectMilestone } from '@/types';
import TaskComments from '@/components/tasks/TaskComments';
import TaskChecklist from '@/components/tasks/TaskChecklist';
import TaskAttachments from '@/components/tasks/TaskAttachments';
import { toast } from '@/components/custom-toast';
import { localizedString } from '@/utils/i18n';
import { taskPriorityTranslationKey } from '@/utils/taskPriority';

interface Props {
    task: Task;
    isOpen: boolean;
    onClose: () => void;
    members?: UserType[];
    taskStatuses: TaskStatusOption[];
    milestones: ProjectMilestone[];
    permissions?: any;
}

export default function TaskModal({ task, isOpen, onClose, members = [], taskStatuses, milestones, permissions }: Props) {
    const { t, i18n } = useTranslation();
    const [currentTask, setCurrentTask] = useState(task);
    const [taskPermissions, setTaskPermissions] = useState(permissions);

    useEffect(() => {
        setCurrentTask(task);
        setTaskPermissions(permissions);
    }, [task, permissions]);

    const refreshTask = async () => {
        try {
            const response = await fetch(route('tasks.show', task.id));
            const data = await response.json();
            setCurrentTask(data.task);
            setTaskPermissions(data.permissions);
        } catch (error) {
            console.error('Failed to refresh task:', error);
        }
    };



    const handleTaskStatusChange = (taskStatusId: string) => {
        router.put(route('tasks.update-task-status', task.id), {
            task_status_id: taskStatusId,
        }, {
            onSuccess: () => {
                refreshTask();
            },
            onError: () => {
                toast.error(t('Failed to update task status'));
            }
        });
    };

    const handlePriorityChange = (priority: string) => {
        router.put(route('tasks.update', task.id), {
            title: currentTask.title,
            description: currentTask.description || '',
            priority: priority,
            start_date: currentTask.start_date,
            end_date: currentTask.end_date,
            assigned_to: currentTask.assigned_to?.id,
            milestone_id: currentTask.milestone_id
        }, {
            onSuccess: () => {
                refreshTask();
            },
            onError: () => {
                toast.error(t('Failed to update priority'));
            }
        });
    };

    const handleAssigneeChange = (assigneeId: string) => {
        const assignedUserId = assigneeId === 'unassigned' ? null : parseInt(assigneeId);
        
        router.put(route('tasks.update', task.id), {
            title: currentTask.title,
            description: currentTask.description || '',
            priority: currentTask.priority,
            start_date: currentTask.start_date,
            end_date: currentTask.end_date,
            assigned_to: assignedUserId,
            milestone_id: currentTask.milestone_id
        }, {
            onSuccess: () => {
                refreshTask();
            },
            onError: () => {
                toast.error(t('Failed to update assignee'));
            }
        });
    };

    const handleDateChange = (field: string, value: string) => {
        router.put(route('tasks.update', task.id), {
            title: currentTask.title,
            description: currentTask.description || '',
            priority: currentTask.priority,
            start_date: field === 'start_date' ? (value || null) : currentTask.start_date,
            end_date: field === 'end_date' ? (value || null) : currentTask.end_date,
            assigned_to: currentTask.assigned_to?.id,
            milestone_id: currentTask.milestone_id
        }, {
            onSuccess: () => {
                refreshTask();
            },
            onError: () => {
                toast.error(t('Failed to update date'));
            }
        });
    };

    const getPriorityColor = (priority: string) => {
        switch (priority) {
            case 'critical': return 'bg-red-100 text-red-800';
            case 'high': return 'bg-orange-100 text-orange-800';
            case 'medium': return 'bg-yellow-100 text-yellow-800';
            case 'low': return 'bg-green-100 text-green-800';
            default: return 'bg-gray-100 text-gray-800';
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
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Description')}</h3>
                            <div className="text-sm text-gray-600">
                                {currentTask.description || t('No description provided')}
                            </div>
                        </div>

                        {/* Tabs for Comments, Checklist, Attachments */}
                        <Tabs defaultValue="comments" className="w-full" dir={i18n.dir()}>
                            <TabsList>
                                <TabsTrigger value="comments" className="gap-2">
                                    <MessageSquare className="h-4 w-4 shrink-0" />
                                    <span className="text-start">{t('Comments')} ({task.comments?.length || 0})</span>
                                </TabsTrigger>
                                <TabsTrigger value="checklist" className="gap-2">
                                    <CheckSquare className="h-4 w-4 shrink-0" />
                                    <span className="text-start">{t('Checklist')} ({task.checklists?.length || 0})</span>
                                </TabsTrigger>
                                <TabsTrigger value="attachments" className="gap-2">
                                    <Paperclip className="h-4 w-4 shrink-0" />
                                    <span className="text-start">{t('Files')} ({currentTask.attachments?.length || 0})</span>
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
                                    members={currentTask.project?.members?.filter(m => m.user?.type !== 'client').map(m => m.user) || members} 
                                    onUpdate={refreshTask}
                                />
                            </TabsContent>

                            <TabsContent value="attachments" className="space-y-4">
                                <TaskAttachments 
                                    task={currentTask} 
                                    attachments={currentTask.attachments || []} 
                                    availableMedia={currentTask.project?.workspace?.media || []}
                                    onUpdate={refreshTask}
                                />
                            </TabsContent>
                        </Tabs>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Task status */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Task Status')}</h3>
                            {taskPermissions?.change_status ? (
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
                                                    <div
                                                        className="h-3 w-3 rounded-full"
                                                        style={{ backgroundColor: ts.color }}
                                                    />
                                                    <span>{localizedString(ts.name, i18n.language)}</span>
                                                </div>
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            ) : (
                                <div className="flex items-center space-x-2 rounded bg-gray-50 p-2">
                                    <div
                                        className="h-3 w-3 rounded-full"
                                        style={{ backgroundColor: currentTask.task_status?.color }}
                                    />
                                    <span>{localizedString(currentTask.task_status?.name, i18n.language)}</span>
                                </div>
                            )}
                        </div>

                        {/* Priority */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Priority')}</h3>
                            {taskPermissions?.update ? (
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
                                <div className={`inline-flex px-2 py-1 rounded text-sm ${getPriorityColor(currentTask.priority)}`}>
                                    {getPriorityLabel(currentTask.priority)}
                                </div>
                            )}
                        </div>

                        {/* Assignee */}
                            <div>
                                <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Assignee')}</h3>
                                <Select value={currentTask.assigned_to?.id?.toString() || 'unassigned'} onValueChange={handleAssigneeChange}>
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select assignee')} />
                                    </SelectTrigger>
                                    <SelectContent className="z-[9999]">
                                        <SelectItem value="unassigned">{t('Unassigned')}</SelectItem>
                                        {(() => {
                                            const projectMembers = currentTask.project?.members?.filter(m => m.user?.type !== 'client').map(m => m.user) || [];
                                            return projectMembers.length > 0 ? projectMembers : members;
                                        })().map((member) => (
                                            <SelectItem key={member.id} value={member.id.toString()}>
                                                {member.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        

                        {/* Dates */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Dates')}</h3>
                            <div className="space-y-2">
                                <div>
                                    <label className="text-xs text-gray-500">{t('Start Date')}</label>
                                    {taskPermissions?.update ? (
                                        <Input
                                            type="date"
                                            value={currentTask.start_date?.split('T')[0] || ''}
                                            onChange={(e) => handleDateChange('start_date', e.target.value)}
                                            className="mt-1"
                                        />
                                    ) : (
                                        <div className="text-sm text-gray-600 mt-1">
                                            {currentTask.start_date ? new Date(currentTask.start_date).toLocaleDateString() : t('Not set')}
                                        </div>
                                    )}
                                </div>
                                <div>
                                    <label className="text-xs text-gray-500">{t('Due Date')}</label>
                                    {taskPermissions?.update ? (
                                        <Input
                                            type="date"
                                            value={currentTask.end_date?.split('T')[0] || ''}
                                            onChange={(e) => handleDateChange('end_date', e.target.value)}
                                            className="mt-1"
                                        />
                                    ) : (
                                        <div className="text-sm text-gray-600 mt-1">
                                            {currentTask.end_date ? new Date(currentTask.end_date).toLocaleDateString() : t('Not set')}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Progress */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Progress')}</h3>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">{currentTask.progress}%</span>
                                </div>
                                <div className="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        className="bg-blue-600 h-2 rounded-full transition-all" 
                                        style={{ width: `${currentTask.progress}%` }}
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Project & Milestone */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 mb-2">{t('Case')}</h3>
                            <span className="text-sm text-gray-600">{currentTask.case?.title}</span>
                            
                            {currentTask.milestone && (
                                <div className="mt-2">
                                    <h3 className="text-sm font-medium text-gray-900 mb-1">{t('Milestone')}</h3>
                                    <span className="text-sm text-gray-600">{currentTask.milestone.title}</span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}