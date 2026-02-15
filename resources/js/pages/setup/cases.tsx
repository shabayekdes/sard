import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupCases() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Case Categories'),
        description: t('Organize cases by classification'),
        href: route('cases.case-categories.index'),
        permissions: ['manage-case-categories', 'manage-any-case-categories', 'manage-own-case-categories'],
      },
      {
        title: t('Case Types'),
        description: t('Define legal case types'),
        href: route('cases.case-types.index'),
        permissions: ['manage-case-types', 'manage-any-case-types', 'manage-own-case-types'],
      },
      {
        title: t('Session Types'),
        description: t('Define legal session types'),
        href: route('hearing-types.index'),
        permissions: ['manage-hearing-types', 'manage-any-hearing-types', 'manage-own-hearing-types'],
      },
      {
        title: t('Event Types'),
        description: t('Categorize events within the system'),
        href: route('advocate.event-types.index'),
        permissions: ['manage-event-types', 'manage-any-event-types', 'manage-own-event-types'],
      },
      {
        title: t('Case Statuses'),
        description: t('Follow up on case status and progress'),
        href: route('cases.case-statuses.index'),
        permissions: ['manage-case-statuses', 'manage-any-case-statuses', 'manage-own-case-statuses'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Case Management')}
      url="/setup/cases"
      items={items}
    />
  );
}
