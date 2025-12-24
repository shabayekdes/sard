// pages/countries/index.tsx
import { PageCrudWrapper } from '@/components/PageCrudWrapper';
import { countriesConfig } from '@/config/crud/countries';
import { t } from '@/utils/i18n';

export default function CountriesPage() {
    return (
        <PageCrudWrapper
            config={countriesConfig}
            title={t('Countries')}
            url="/countries"
        />
    );
}

