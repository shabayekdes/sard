<?php

namespace App\Enums;

enum BusinessType: string
{
    case PROFESSIONAL_COMPANY_MULTIPLE = 'PROFESSIONAL_COMPANY_MULTIPLE';
    case INDIVIDUAL_OFFICE = 'INDIVIDUAL_OFFICE';
    case PROFESSIONAL_COMPANY_ONE = 'PROFESSIONAL_COMPANY_ONE';
    case PROFESSIONAL_COMPANY = 'PROFESSIONAL_COMPANY';

    /**
     * Translation key for the business type label (for use with __() or frontend t()).
     */
    public function labelKey(): string
    {
        return match ($this) {
            self::PROFESSIONAL_COMPANY_MULTIPLE => 'Professional Company (Multiple Persons)',
            self::INDIVIDUAL_OFFICE => 'Individual Office',
            self::PROFESSIONAL_COMPANY_ONE => 'Professional Company (One Person)',
            self::PROFESSIONAL_COMPANY => 'Professional Company',
        };
    }

    /**
     * All values for validation rules.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Options for forms: value + label key (for frontend i18n).
     *
     * @return array<array{value: string, labelKey: string}>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[] = [
                'value' => $case->value,
                'labelKey' => $case->labelKey(),
            ];
        }
        return $options;
    }
}
