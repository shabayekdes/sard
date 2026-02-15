import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupDocuments() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Document Categories'),
        description: t('Organize documents into categories'),
        href: route('document-management.categories.index'),
        permissions: ['manage-document-categories', 'manage-any-document-categories', 'manage-own-document-categories'],
      },
      {
        title: t('Document Types'),
        description: t('Define and categorize document types'),
        href: route('advocate.document-types.index'),
        permissions: ['manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Document Management')}
      url="/setup/documents"
      items={items}
    />
  );
}
