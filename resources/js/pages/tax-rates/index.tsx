import { PageCrudWrapper } from '@/components/PageCrudWrapper';
import { taxRatesConfig } from '@/config/crud/tax-rates';
import { t } from '@/utils/i18n';

export default function TaxRatesPage() {
    return (
        <PageCrudWrapper
            config={taxRatesConfig}
            title={t('Tax Rates')}
            url="/tax-rates"
        />
    );
}
