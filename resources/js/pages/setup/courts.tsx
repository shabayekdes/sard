import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupCourts() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Court Types'),
        description: t('Categorize courts by degree'),
        href: route('advocate.court-types.index'),
        permissions: ['manage-court-types', 'manage-any-court-types', 'manage-own-court-types'],
      },
      {
        title: t('Circle Types'),
        description: t('Organize departments within courts'),
        href: route('advocate.circle-types.index'),
        permissions: ['manage-circle-types', 'manage-any-circle-types', 'manage-own-circle-types'],
      },
      {
        title: t('Judges'),
        description: t('Manage judge data and records'),
        href: route('judges.index'),
        permissions: ['manage-judges', 'manage-any-judges', 'manage-own-judges'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Courts & Judiciary')}
      url="/setup/courts"
      items={items}
    />
  );
}
