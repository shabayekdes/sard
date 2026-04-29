import { Datetime } from '@/components/datetime';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { useLayout } from '@/contexts/LayoutContext';
import {
    ArrowRightLeft,
    Briefcase,
    CheckSquare,
    FileText,
    Gavel,
    Pencil,
    Plus,
    Scale,
    StickyNote,
    Trash2,
    User,
    Users,
    Zap,
    ArrowDownUp,
} from 'lucide-react';
import type { TFunction } from 'i18next';

export type CaseActivityRow = {
    id: number;
    source: 'automatic' | 'manual';
    category: string;
    event_key: string;
    title: string;
    description?: string | null;
    occurred_at: string;
    case_timeline_id?: number | null;
    case_timeline?: Record<string, unknown> | null;
    /** Optional initials for avatar chip (same screen design) */
    meta?: { actor_initials?: string; [key: string]: unknown } | null;
};

export type TimelineCategory =
    | 'all'
    | 'automatic'
    | 'manual'
    | 'case'
    | 'hearing'
    | 'judgment'
    | 'referral'
    | 'document'
    | 'task'
    | 'note'
    | 'assignee'
    | 'timeline';

function iconFor(category: string, eventKey: string) {
    if (category === 'hearing') return Gavel;
    if (category === 'document') return FileText;
    if (category === 'judgment') return Scale;
    if (category === 'referral') return ArrowRightLeft;
    if (category === 'task') return CheckSquare;
    if (category === 'note') return StickyNote;
    if (category === 'assignee') return Users;
    if (category === 'case') return Briefcase;
    if (category === 'timeline' || eventKey === 'manual_timeline') return Zap;
    return FileText;
}

function iconColors(category: string, eventKey: string): string {
    if (category === 'hearing') return 'bg-sky-600 text-white';
    if (category === 'document') return 'bg-emerald-600 text-white';
    if (category === 'judgment') return 'bg-amber-700 text-white';
    if (category === 'referral') return 'bg-orange-500 text-white';
    if (category === 'task') return 'bg-violet-600 text-white';
    if (category === 'note') return 'bg-slate-600 text-white';
    if (category === 'assignee') return 'bg-teal-600 text-white';
    if (category === 'case') return 'bg-indigo-600 text-white';
    if (category === 'timeline' || eventKey === 'manual_timeline') return 'bg-cyan-500 text-white';
    return 'bg-gray-500 text-white';
}

function badgeClass(source: 'automatic' | 'manual'): string {
    return source === 'manual'
        ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200'
        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
}

function entityBadgeClass(category: string): string {
    const map: Record<string, string> = {
        case: 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200',
        hearing: 'bg-sky-50 text-sky-800 dark:bg-sky-900/30 dark:text-sky-200',
        judgment: 'bg-amber-50 text-amber-900 dark:bg-amber-900/30 dark:text-amber-100',
        referral: 'bg-orange-50 text-orange-900 dark:bg-orange-900/30 dark:text-orange-100',
        document: 'bg-emerald-50 text-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-100',
        task: 'bg-violet-50 text-violet-900 dark:bg-violet-900/30 dark:text-violet-100',
        note: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200',
        assignee: 'bg-teal-50 text-teal-900 dark:bg-teal-900/30 dark:text-teal-100',
        timeline: 'bg-cyan-50 text-cyan-900 dark:bg-cyan-900/30 dark:text-cyan-100',
    };
    return map[category] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
}

export interface CaseActivityTimelineProps {
    t: TFunction;
    totalCount: number;
    rows: CaseActivityRow[];
    timelineCategory: TimelineCategory;
    onCategoryChange: (c: TimelineCategory) => void;
    timelineSort: 'newest_first' | 'oldest_first';
    onSortToggle: () => void;
    canCreate: boolean;
    canEdit: boolean;
    canDelete: boolean;
    onAdd: () => void;
    onEdit: (row: CaseActivityRow) => void;
    onDelete: (row: CaseActivityRow) => void;
}

export function CaseActivityTimeline({
    t,
    totalCount,
    rows,
    timelineCategory,
    onCategoryChange,
    timelineSort,
    onSortToggle,
    canCreate,
    canEdit,
    canDelete,
    onAdd,
    onEdit,
    onDelete,
}: CaseActivityTimelineProps) {
    const { isRtl } = useLayout();
    const dir = isRtl ? 'rtl' : 'ltr';

    const chips: { key: TimelineCategory; label: string }[] = [
        { key: 'all', label: t('Timeline filter all') },
        { key: 'automatic', label: t('Timeline filter automatic') },
        { key: 'manual', label: t('Timeline filter manual') },
        { key: 'case', label: t('Timeline filter case') },
        { key: 'hearing', label: t('Timeline filter hearings') },
        { key: 'judgment', label: t('Timeline filter judgments') },
        { key: 'referral', label: t('Timeline filter referrals') },
        { key: 'assignee', label: t('Timeline filter assignees') },
        { key: 'document', label: t('Timeline filter documents') },
        { key: 'task', label: t('Timeline filter tasks') },
        { key: 'note', label: t('Timeline filter notes') },
        { key: 'timeline', label: t('Timeline filter events') },
    ];

    return (
        <section className="space-y-4" dir={dir}>
            {/* Header: with dir=rtl title is on the right, toolbar on the left (same screen as design) */}
            <div className="flex flex-wrap items-center justify-between gap-3">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                    {t('Timeline with count', { count: totalCount })}
                </h3>
                {/* Physical order: primary Add on browser-left, Sort beside it — force LTR row for the pair */}
                <div className="flex flex-row flex-wrap items-center gap-2" dir="ltr">
                    {canCreate && (
                        <Button type="button" size="sm" className="gap-1 bg-emerald-600 hover:bg-emerald-700" onClick={onAdd}>
                            <Plus className="h-4 w-4" />
                            {t('Timeline add event')}
                        </Button>
                    )}
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        className="gap-1 border-gray-200 bg-gray-50 text-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        onClick={onSortToggle}
                    >
                        <ArrowDownUp className="h-4 w-4" />
                        {timelineSort === 'newest_first' ? t('Timeline sort newest first') : t('Timeline sort oldest first')}
                    </Button>
                </div>
            </div>

            {/* Filter chips — inherit dir so chips flow right-to-left in RTL */}
            <div className="flex flex-wrap gap-2">
                {chips.map(({ key, label }) => (
                    <button
                        key={key}
                        type="button"
                        onClick={() => onCategoryChange(key)}
                        className={cn(
                            'rounded-full px-3 py-1.5 text-xs font-medium transition-colors',
                            timelineCategory === key
                                ? 'bg-emerald-700 text-white dark:bg-emerald-600'
                                : 'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
                        )}
                    >
                        {label}
                    </button>
                ))}
            </div>

            {/* Feed — [date per app settings] | [icons + spine] | [story]; section dir=rtl mirrors column order */}
            <div className="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                {rows.length === 0 ? (
                    <p className="p-6 text-sm text-gray-500 dark:text-gray-400">{t('Timeline empty')}</p>
                ) : (
                    rows.map((row, index) => {
                        const Icon = iconFor(row.category, row.event_key);
                        const ic = iconColors(row.category, row.event_key);
                        const showActions = row.source === 'manual' && row.case_timeline_id && (canEdit || canDelete);
                        const isFirst = index === 0;
                        const isLast = index === rows.length - 1;
                        const onlyOne = isFirst && isLast;
                        const initials =
                            typeof row.meta?.actor_initials === 'string' && row.meta.actor_initials.trim()
                                ? row.meta.actor_initials.trim().slice(0, 3)
                                : null;

                        const spine =
                            !onlyOne && (
                                <div
                                    className={cn(
                                        'pointer-events-none absolute left-1/2 z-0 w-px -translate-x-1/2 bg-gray-200 dark:bg-gray-600',
                                        isFirst && !isLast && 'top-1/2 bottom-0',
                                        !isFirst && isLast && 'top-0 bottom-1/2',
                                        !isFirst && !isLast && 'top-0 bottom-0',
                                    )}
                                    aria-hidden
                                />
                            );

                        const avatarChip = initials ? (
                            <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-200 text-[10px] font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {initials}
                            </span>
                        ) : (
                            <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                                <User className="h-3.5 w-3.5" aria-hidden />
                            </span>
                        );

                        const entityChip = (
                            <span
                                className={cn(
                                    'inline-flex shrink-0 rounded-md px-2 py-0.5 text-xs font-medium',
                                    entityBadgeClass(row.category),
                                )}
                            >
                                {t(`Timeline entity ${row.category}`, { defaultValue: row.category })}
                            </span>
                        );

                        return (
                            <div
                                key={row.id}
                                className="group/row flex w-full flex-row items-stretch transition-colors hover:bg-gray-50/60 dark:border-gray-800 dark:hover:bg-gray-800/30"
                            >
                                {/* 1 — Occurred date (Hijri vs Gregorian from tenant app settings); centered with icon rail */}
                                <div className="flex min-w-[5.5rem] max-w-[11rem] shrink-0 flex-col items-center justify-center bg-gray-50/40 px-2 py-3 dark:bg-gray-900/40">
                                    <Datetime
                                        value={row.occurred_at}
                                        variant="date"
                                        showIcons={false}
                                        emptyLabel=""
                                        className="text-balance text-center text-xs leading-snug text-gray-400 dark:text-gray-500 ltr:font-sans"
                                    />
                                </div>

                                {/* 2 — Timeline rail between date and story */}
                                <div className="relative flex w-14 shrink-0 flex-col items-center justify-center bg-gray-50/20 py-3 dark:bg-gray-900/20">
                                    {spine}
                                    <div
                                        className={cn(
                                            'relative z-10 flex h-11 w-11 shrink-0 items-center justify-center rounded-full shadow-sm ring-[4px] ring-white dark:ring-gray-900',
                                            ic,
                                        )}
                                    >
                                        <Icon className="h-5 w-5" aria-hidden />
                                    </div>
                                </div>

                                {/* 3 — Story (visual end; text dir follows locale, aligned toward rail) */}
                                <div
                                    className={cn(
                                        'relative min-w-0 flex-1 px-4 py-3 border-b border-gray-100',
                                        isRtl && 'text-start',
                                    )}
                                    dir={isRtl ? 'rtl' : 'ltr'}
                                >
                                    {showActions ? (
                                        <div
                                            className="absolute end-3 top-2 z-20 flex gap-0.5 rounded-md bg-white/95 p-0.5 opacity-0 shadow-sm ring-1 ring-gray-100 transition-opacity group-hover/row:opacity-100 dark:bg-gray-900/95 dark:ring-gray-700"
                                            dir="ltr"
                                        >
                                            {canEdit && row.case_timeline ? (
                                                <button
                                                    type="button"
                                                    className="rounded p-1 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20"
                                                    aria-label={t('Edit')}
                                                    onClick={() => onEdit(row)}
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </button>
                                            ) : null}
                                            {canDelete ? (
                                                <button
                                                    type="button"
                                                    className="rounded p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                    aria-label={t('Delete')}
                                                    onClick={() => onDelete(row)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            ) : null}
                                        </div>
                                    ) : null}

                                    <div className="flex flex-wrap items-center justify-start gap-2">
                                        <span className="text-base font-bold leading-snug text-gray-900 dark:text-white">{row.title}</span>
                                        <span className={cn('shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium', badgeClass(row.source))}>
                                            {row.source === 'manual' ? t('Timeline source manual') : t('Timeline source automatic')}
                                        </span>
                                    </div>

                                    {row.description ? (
                                        <p className="mt-1.5 line-clamp-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                            {row.description}
                                        </p>
                                    ) : null}

                                    <div className="mt-2 flex flex-wrap items-center justify-start gap-2">
                                        {avatarChip}
                                        {entityChip}
                                    </div>
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </section>
    );
}
