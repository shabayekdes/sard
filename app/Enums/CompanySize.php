<?php

namespace App\Enums;

enum CompanySize: string
{
    case Solo = 'solo';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
    case Corporate = 'corporate';

    /**
     * Translation key for the office size label (for use with __() or frontend t()).
     */
    public function labelKey(): string
    {
        return match ($this) {
            self::Solo => 'Solo Practice (1 Lawyer)',
            self::Small => 'Small Firm (2–5 Lawyers)',
            self::Medium => 'Medium Firm (6–20 Lawyers)',
            self::Large => 'Large Firm (21+ Lawyers)',
            self::Corporate => 'Corporate / Enterprise Law Firm',
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
