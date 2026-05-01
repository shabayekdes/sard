<?php

namespace App\Enums;

/**
 * Case referral pipeline stage (stored as string in `case_referrals.stage`).
 * Order matches UI display order.
 */
enum CaseReferralStage: string
{
    case AMICABLE_SETTLEMENT = 'amicable_settlement';
    case RECONCILIATION = 'reconciliation';
    case FIRST_INSTANCE = 'first_instance';
    case APPEAL = 'appeal';
    case SUPREME_COURT = 'supreme_court';
    case EXECUTION = 'execution';

    public function badgeClass(): string
    {
        return match ($this) {
            self::AMICABLE_SETTLEMENT => 'bg-blue-100 text-blue-700',
            self::RECONCILIATION => 'bg-emerald-100 text-emerald-700',
            self::FIRST_INSTANCE => 'bg-amber-100 text-amber-700',
            self::APPEAL => 'bg-purple-100 text-purple-700',
            self::SUPREME_COURT => 'bg-indigo-100 text-indigo-700',
            self::EXECUTION => 'bg-rose-100 text-rose-700',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * For Inertia (Tailwind badge classes are defined once on the backend).
     *
     * @return list<array{key: string, badgeClass: string}>
     */
    public static function definitions(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[] = [
                'key' => $case->value,
                'badgeClass' => $case->badgeClass(),
            ];
        }

        return $out;
    }
}
