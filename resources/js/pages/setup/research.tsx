import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupResearch() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Research Type'),
        description: t('Determine available search methods'),
        href: route('legal-research.research-types.index'),
        permissions: ['manage-research-types', 'manage-any-research-types', 'manage-own-research-types'],
      },
      {
        title: t('Research Source'),
        description: t('Determine sources of search results'),
        href: route('legal-research.sources.index'),
        permissions: ['manage-research-sources', 'manage-any-research-sources', 'manage-own-research-sources'],
      },
      {
        title: t('Practice Area'),
        description: t('Categorize search by area'),
        href: route('advocate.practice-areas.index'),
        permissions: ['manage-practice-areas', 'manage-any-practice-areas', 'manage-own-practice-areas'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Search Settings')}
      url="/setup/research"
      items={items}
    />
  );
}
