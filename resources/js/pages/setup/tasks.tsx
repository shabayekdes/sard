import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupTasks() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Task Types'),
        description: t('Categorize tasks by type'),
        href: route('tasks.task-types.index'),
        permissions: ['manage-task-types', 'manage-any-task-types', 'manage-own-task-types'],
      },
      {
        title: t('Task Statuses'),
        description: t('Follow up on task progress'),
        href: route('tasks.task-statuses.index'),
        permissions: ['manage-task-statuses', 'manage-any-task-statuses', 'manage-own-task-statuses'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Task Management')}
      url="/setup/tasks"
      items={items}
    />
  );
}
