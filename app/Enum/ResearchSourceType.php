<?php

namespace App\Enum;

enum ResearchSourceType: string
{
    case JUDICIAL_LAWS = 'JUDICIAL_LAWS';
    case E_JUDICIAL_PLATFORM = 'E_JUDICIAL_PLATFORM';
    case LAWS_REGULATIONS = 'LAWS_REGULATIONS';
    case ADMINISTRATIVE_JUDICIARY = 'ADMINISTRATIVE_JUDICIARY';
    case CORPORATE_COMMERCIAL = 'CORPORATE_COMMERCIAL';
    case TAX_COMPLIANCE = 'TAX_COMPLIANCE';
    case FINANCIAL_REGULATION = 'FINANCIAL_REGULATION';
    case CAPITAL_MARKET_REGULATION = 'CAPITAL_MARKET_REGULATION';
    case INTELLECTUAL_PROPERTY = 'INTELLECTUAL_PROPERTY';
    case LABOR_LAW = 'LABOR_LAW';
    case ARBITRATION = 'ARBITRATION';
    case LEGISLATIVE = 'LEGISLATIVE';
    case GOVERNMENT_TENDERS = 'GOVERNMENT_TENDERS';
    case DATA_PROTECTION = 'DATA_PROTECTION';
    case INTERNATIONAL_DATABASE = 'INTERNATIONAL_DATABASE';
    case ACADEMIC_DATABASE = 'ACADEMIC_DATABASE';
    case FREE_CASE_LAW = 'FREE_CASE_LAW';

    public function label(): string
    {
        return match ($this) {
            self::JUDICIAL_LAWS => 'Judicial / Laws',
            self::E_JUDICIAL_PLATFORM => 'E-Judicial Platform',
            self::LAWS_REGULATIONS => 'Laws & Regulations',
            self::ADMINISTRATIVE_JUDICIARY => 'Administrative Judiciary',
            self::CORPORATE_COMMERCIAL => 'Corporate & Commercial',
            self::TAX_COMPLIANCE => 'Tax & Compliance',
            self::FINANCIAL_REGULATION => 'Financial Regulation',
            self::CAPITAL_MARKET_REGULATION => 'Capital Market Regulation',
            self::INTELLECTUAL_PROPERTY => 'Intellectual Property',
            self::LABOR_LAW => 'Labor Law',
            self::ARBITRATION => 'Arbitration',
            self::LEGISLATIVE => 'Legislative',
            self::GOVERNMENT_TENDERS => 'Government Tenders',
            self::DATA_PROTECTION => 'Data Protection',
            self::INTERNATIONAL_DATABASE => 'International Database',
            self::ACADEMIC_DATABASE => 'Academic Database',
            self::FREE_CASE_LAW => 'Free Case Law',
        };
    }

    /**
     * @return array<string, string> [value => label]
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function optionsForFrontend(): array
    {
        return array_values(array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        ));
    }
}
