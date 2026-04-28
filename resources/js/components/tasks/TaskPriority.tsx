import React from 'react';
import { useTranslation } from 'react-i18next';
import { Badge } from '@/components/ui/badge';
import { AlertTriangle, ArrowUp, Minus, ArrowDown } from 'lucide-react';
import { taskPriorityTranslationKey } from '@/utils/taskPriority';

interface Props {
    priority: string;
    showIcon?: boolean;
}

export default function TaskPriority({ priority, showIcon = false }: Props) {
    const { t } = useTranslation();

    const getPriorityConfig = (p: string) => {
        switch (p) {
            case 'critical':
                return {
                    color: 'bg-red-100 text-red-800 border-red-200',
                    icon: AlertTriangle,
                };
            case 'high':
                return {
                    color: 'bg-orange-100 text-orange-800 border-orange-200',
                    icon: ArrowUp,
                };
            case 'medium':
                return {
                    color: 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    icon: Minus,
                };
            case 'low':
                return {
                    color: 'bg-green-100 text-green-800 border-green-200',
                    icon: ArrowDown,
                };
            default:
                return {
                    color: 'bg-gray-100 text-gray-800 border-gray-200',
                    icon: Minus,
                };
        }
    };

    const p = (priority || 'medium').toLowerCase();
    const config = getPriorityConfig(p);
    const Icon = config.icon;

    return (
        <Badge className={config.color}>
            {showIcon && <Icon className="h-3 w-3 mr-1" />}
            {t(taskPriorityTranslationKey(p))}
        </Badge>
    );
}