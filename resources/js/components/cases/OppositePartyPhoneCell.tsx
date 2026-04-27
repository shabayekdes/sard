import { PhoneInput, defaultCountries } from 'react-international-phone';

export type OppositePartyPhoneCountry = { value: number; label: string; code?: string };

type OppositePartyPhoneCellProps = {
    value: string;
    onChange: (value: string) => void;
    itemIndex: number;
    phoneCountries?: OppositePartyPhoneCountry[];
    defaultCountry?: string;
};

export function OppositePartyPhoneCell({
    value,
    onChange,
    itemIndex,
    phoneCountries = [],
    defaultCountry = '',
}: OppositePartyPhoneCellProps) {
    const phoneCountriesByCode = new Map(phoneCountries.map((c) => [String(c.code || '').toLowerCase(), c]));
    const phoneCountryCodes = phoneCountries.map((c) => String(c.code || '').toLowerCase()).filter(Boolean);
    const allowedPhoneCountries = phoneCountryCodes.length
        ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
        : defaultCountries;
    const defaultPhoneCountry =
        phoneCountriesByCode.get(String(defaultCountry).toLowerCase()) || phoneCountriesByCode.get('sa') || phoneCountries[0];

    return (
        <div className="phone-left-selector min-w-[12rem]">
            <PhoneInput
                defaultCountry={(defaultPhoneCountry?.code || '').toLowerCase() || undefined}
                value={value || ''}
                countries={allowedPhoneCountries}
                inputProps={{ name: `opposite_parties.${itemIndex}.phone`, autoComplete: 'tel' }}
                className="w-full"
                inputClassName="w-full !h-10 !border !border-input !bg-background !text-sm !text-foreground"
                countrySelectorStyleProps={{
                    buttonClassName: '!h-10 !border !border-input !bg-background',
                    dropdownStyleProps: {
                        className: '!bg-background !text-foreground phone-country-dropdown z-[10000]',
                    },
                }}
                onChange={(v) => onChange(v || '')}
            />
        </div>
    );
}
