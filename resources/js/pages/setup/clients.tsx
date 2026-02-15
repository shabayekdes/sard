import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupClients() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Client Type'),
        description: t('Categorize clients by type'),
        href: route('clients.client-types.index'),
        permissions: ['manage-client-types', 'manage-any-client-types', 'manage-own-client-types'],
      },
      {
        title: t('Document Type'),
        description: t('Define and categorize document types'),
        href: route('advocate.document-types.index'),
        permissions: ['manage-document-types', 'manage-any-document-types', 'manage-own-document-types'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Client Management')}
      url="/setup/clients"
      items={items}
    />
  );
}
