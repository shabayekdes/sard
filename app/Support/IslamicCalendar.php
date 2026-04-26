<?php

namespace App\Support;

use DateTimeInterface;
use IntlDateFormatter;

final class IslamicCalendar
{
    /**
     * Hijri (Umm al-Qura) date as YYYY-MM-DD using the Gregorian instant.
     */
    public static function hijriYmdFromGregorian(DateTimeInterface $gregorian): string
    {
        if (! class_exists(IntlDateFormatter::class)) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            'en_US@calendar=islamic-umalqura',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            $gregorian->getTimezone()->getName(),
            IntlDateFormatter::TRADITIONAL,
            'yyyy-MM-dd'
        );

        $formatted = $formatter->format($gregorian);

        return $formatted !== false ? $formatted : '';
    }

    /**
     * Long-form Hijri date for display (Arabic).
     */
    public static function hijriLongArabic(DateTimeInterface $gregorian): string
    {
        if (! class_exists(IntlDateFormatter::class)) {
            return self::hijriYmdFromGregorian($gregorian);
        }

        $formatter = new IntlDateFormatter(
            'ar_SA@calendar=islamic-umalqura',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            $gregorian->getTimezone()->getName(),
            IntlDateFormatter::TRADITIONAL,
            'd MMMM y'
        );

        $formatted = $formatter->format($gregorian);

        return $formatted !== false ? $formatted : '';
    }
}
