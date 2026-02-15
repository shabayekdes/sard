import { NestedSetupPage, type NestedSetupItem } from './NestedSetupPage';
import { useTranslation } from 'react-i18next';
import { useMemo } from 'react';

export default function SetupBilling() {
  const { t } = useTranslation();

  const items: NestedSetupItem[] = useMemo(
    () => [
      {
        title: t('Expense Category'),
        description: t('Categorize and organize expense items'),
        href: route('billing.expense-categories.index'),
        permissions: ['manage-expense-categories', 'manage-any-expense-categories', 'manage-own-expense-categories'],
      },
    ],
    [t]
  );

  return (
    <NestedSetupPage
      title={t('Billing')}
      url="/setup/billing"
      items={items}
    />
  );
}
