// pages/currencies/index.tsx
import { PageCrudWrapper } from '@/components/PageCrudWrapper';
import { currenciesConfig } from '@/config/crud/currencies';
import { t } from '@/utils/i18n';

export default function CurrenciesPage() {
  return (
    <PageCrudWrapper
      config={currenciesConfig}
      title={t('Currencies')}
      url="/currencies"
    />
  );
}