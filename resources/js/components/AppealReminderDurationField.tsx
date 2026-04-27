import { cn } from '@/lib/utils';
import { useLayout } from '@/contexts/LayoutContext';
import { useTranslation } from 'react-i18next';

const OPTION_VALUES = ['one_day_before', 'three_days_before', 'one_week_before', 'custom'] as const;

export type AppealReminderDurationValue = (typeof OPTION_VALUES)[number];

type Props = {
    value: string;
    onChange: (value: AppealReminderDurationValue) => void;
    disabled?: boolean;
    className?: string;
};

export function AppealReminderDurationField({ value, onChange, disabled, className }: Props) {
    const { t } = useTranslation();
    const { isRtl } = useLayout();

    const options: { value: AppealReminderDurationValue; label: string }[] = [
        { value: 'one_day_before', label: t('Judgment reminder 1 day before') },
        { value: 'three_days_before', label: t('Judgment reminder 3 days before') },
        { value: 'one_week_before', label: t('Judgment reminder 1 week before') },
        { value: 'custom', label: t('Judgment reminder custom manual') },
    ];

    const current = OPTION_VALUES.includes(value as AppealReminderDurationValue)
        ? (value as AppealReminderDurationValue)
        : 'one_day_before';

    return (
        <div className={cn('w-full bg-transparent p-0', className)} dir={isRtl ? 'rtl' : 'ltr'}>
            {/* With dir=rtl, flex-start is the right edge; use justify-start so pills align with the switch above */}
            <div className="flex w-full flex-wrap justify-start gap-1.5 sm:gap-2">
                {options.map((opt) => (
                    <button
                        key={opt.value}
                        type="button"
                        disabled={disabled}
                        onClick={() => onChange(opt.value)}
                        className={cn(
                            'min-h-9 shrink-0 rounded-full border px-3 py-1.5 text-sm font-medium transition-colors',
                            current === opt.value
                                ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                                : 'border-input bg-background text-foreground hover:bg-muted/60 dark:border-gray-600 dark:hover:bg-gray-800',
                            disabled && 'cursor-default opacity-70',
                            !disabled && current !== opt.value && 'cursor-pointer',
                        )}
                    >
                        {opt.label}
                    </button>
                ))}
            </div>
        </div>
    );
}
