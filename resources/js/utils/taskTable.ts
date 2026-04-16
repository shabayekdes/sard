import type { Task } from '@/types';

export type TaskAssigneeRow = { id: number; name: string; avatar?: string };

/** Inertia sends `assigned_user` from `assignedUser()`; `assigned_to` may be a numeric FK only. */
export function getTaskAssignee(task: Task): TaskAssigneeRow | null {
  const u = task.assigned_user;
  if (u && typeof u === 'object' && typeof u.name === 'string' && u.name.trim() !== '') {
    return u;
  }
  const embed = task.assigned_to;
  if (embed && typeof embed === 'object' && typeof embed.name === 'string' && embed.name.trim() !== '') {
    return embed;
  }
  return null;
}

/** Resolves assignee user id for forms and selects (FK-only `assigned_to` or `assigned_user.id`). */
export function getTaskAssigneeId(task: Task): number | null {
  const fromUser = task.assigned_user?.id;
  if (typeof fromUser === 'number' && fromUser > 0) {
    return fromUser;
  }
  const at = task.assigned_to;
  if (typeof at === 'number' && at > 0) {
    return at;
  }
  if (at && typeof at === 'object' && 'id' in at) {
    const id = Number((at as { id: number }).id);
    return Number.isFinite(id) && id > 0 ? id : null;
  }
  return null;
}

export function taskHasAssigneeId(task: Task): boolean {
  if (getTaskAssignee(task)) return true;
  if (task.assigned_to == null) return false;
  if (typeof task.assigned_to === 'number') return task.assigned_to > 0;
  if (typeof task.assigned_to === 'object' && 'id' in task.assigned_to) {
    return Number((task.assigned_to as { id: number }).id) > 0;
  }
  return true;
}

export function isTaskOverdue(endDate: string | null | undefined): boolean {
  if (!endDate) return false;
  const today = new Date();
  const dueDate = new Date(endDate);
  return dueDate < today;
}

export function getTaskPriorityBadgeClassName(priority: string): string {
  const colors: Record<string, string> = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800',
  };
  return colors[priority] || 'bg-gray-100 text-gray-800';
}
