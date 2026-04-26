<?php

namespace App\Support;

class CaseAuthorityType
{
    public const COURTS_GENERAL = 'courts_general';

    public const COURTS_ADMINISTRATIVE = 'courts_administrative';

    public const COMMITTEE = 'committee';

    public const PROSECUTION = 'prosecution';

    public const POLICE = 'police';

    public const PRISONS = 'prisons';

    public const RECONCILIATION = 'reconciliation';

    public const AMICABLE_SETTLEMENT = 'amicable_settlement';

    public const OTHER = 'other';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::COURTS_GENERAL,
            self::COURTS_ADMINISTRATIVE,
            self::COMMITTEE,
            self::PROSECUTION,
            self::POLICE,
            self::PRISONS,
            self::RECONCILIATION,
            self::AMICABLE_SETTLEMENT,
            self::OTHER,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $details
     * @return array<string, mixed>|null
     */
    public static function filterDetails(?string $type, ?array $details): ?array
    {
        if ($type === null || $type === '') {
            return null;
        }

        $details = is_array($details) ? $details : [];
        $out = null;

        switch ($type) {
            case self::COURTS_GENERAL:
            case self::COURTS_ADMINISTRATIVE:
                return null;
            case self::COMMITTEE:
            case self::PROSECUTION:
            case self::POLICE:
            case self::PRISONS:
            case self::OTHER:
                $v = $details['entity_name'] ?? null;
                if ($v !== null && $v !== '') {
                    $out = ['entity_name' => (string) $v];
                }
                break;
            case self::RECONCILIATION:
                $out = self::onlyNonEmpty([
                    'reconciliation_suit_number' => $details['reconciliation_suit_number'] ?? null,
                    'reconciliation_suit_date' => $details['reconciliation_suit_date'] ?? null,
                    'reconciliation_report_number' => $details['reconciliation_report_number'] ?? null,
                    'reconciliation_report_date' => $details['reconciliation_report_date'] ?? null,
                ]);
                $out = $out === [] ? null : $out;
                break;
            case self::AMICABLE_SETTLEMENT:
                $out = self::onlyNonEmpty([
                    'amicable_suit_number' => $details['amicable_suit_number'] ?? null,
                    'amicable_suit_date' => $details['amicable_suit_date'] ?? null,
                ]);
                $out = $out === [] ? null : $out;
                break;
        }

        return $out;
    }

    public static function clearsCourtId(?string $type): bool
    {
        return in_array($type, [
            self::COMMITTEE,
            self::PROSECUTION,
            self::POLICE,
            self::PRISONS,
            self::RECONCILIATION,
            self::AMICABLE_SETTLEMENT,
            self::OTHER,
        ], true);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private static function onlyNonEmpty(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            if ($v === null) {
                continue;
            }
            if (is_string($v) && $v === '') {
                continue;
            }
            $out[$k] = $v;
        }

        return $out;
    }
}
