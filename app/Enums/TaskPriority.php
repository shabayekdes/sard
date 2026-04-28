<?php

namespace App\Enums;

/**
 * Task priority (stored as lowercase in the database).
 * Case names are UPPERCASE; backed values stay lowercase for API/frontend compatibility.
 */
enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    /**
     * i18n key (react-i18next / lang JSON), e.g. TASK_PRIORITY_LOW.
     * Uses underscores so keys are not split by i18next's default keySeparator (.).
     */
    public function labelKey(): string
    {
        return 'TASK_PRIORITY_' . $this->name;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
