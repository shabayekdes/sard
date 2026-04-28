import React from 'react';
import { router } from '@inertiajs/react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { useTranslation } from 'react-i18next';
import { Task, TaskStatusOption } from '@/types';
import { localizedString } from '@/utils/i18n';

type Props = {
    task: Task;
    taskStatuses: TaskStatusOption[];
    variant?: 'select' | 'badge';
};

export default function TaskStatusChanger({ task, taskStatuses, variant = 'select' }: Props) {
    const { i18n } = useTranslation();

    const handleChange = (statusId: string) => {
        router.put(route('tasks.update-task-status', task.id), {
            task_status_id: statusId,
        });
    };

    const current = taskStatuses.find((s) => s.id === task.task_status_id);
    const value =
        task.task_status_id != null && task.task_status_id !== undefined
            ? String(task.task_status_id)
            : '';

    if (variant === 'badge') {
        return (
            <Badge
                variant="outline"
                className="cursor-pointer text-xs"
                style={{
                    backgroundColor: (current?.color ?? task.task_status?.color ?? '#ccc') + '20',
                    borderColor: current?.color ?? task.task_status?.color ?? '#ccc',
                }}
            >
                {localizedString(current?.name ?? task.task_status?.name, i18n.language)}
            </Badge>
        );
    }

    return (
        <Select value={value || undefined} onValueChange={handleChange}>
            <SelectTrigger className="h-8 w-[140px] text-xs">
                <SelectValue placeholder="—" />
            </SelectTrigger>
            <SelectContent>
                {taskStatuses.map((s) => (
                    <SelectItem key={s.id} value={String(s.id)}>
                        <div className="flex items-center gap-2">
                            <div className="h-2.5 w-2.5 rounded-full" style={{ backgroundColor: s.color }} />
                            <span>{localizedString(s.name, i18n.language)}</span>
                        </div>
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}
