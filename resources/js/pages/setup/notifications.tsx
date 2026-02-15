import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupNotifications() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Notification Template'),
        description: t('Manage and customize notification templates'),
        href: route('notification-templates.index'),
        permissions: [],
        roleCondition: 'company',
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Notifications')}
      url="/setup/notifications"
      items={items}
    />
  );
}
