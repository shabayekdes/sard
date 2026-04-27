import { RichTextField } from '@/components/ui/rich-text-field';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { htmlPlainTextLength } from '@/lib/htmlPlainTextLength';
import { ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useLayout } from '@/contexts/LayoutContext';

const CASE_SUBJECT_MAX = 8000;

type Props = {
    caseSubject: string;
    plaintiffRequests: string;
    plaintiffEvidence: string;
    defendantRequests: string;
    defendantEvidence: string;
    onFieldChange: (field: PleadingField, value: string) => void;
    caseSubjectError?: string;
    errors?: Partial<Record<PleadingField, string>>;
    /** i18n */
    t: (key: string) => string;
};

export type PleadingField =
    | 'case_subject'
    | 'plaintiff_requests'
    | 'plaintiff_evidence'
    | 'defendant_requests'
    | 'defendant_evidence';

function CollapsibleRichSection({
    title,
    value,
    onChange,
    error,
    tPlaceholder,
    openDefault = false,
    isRtl,
}: {
    title: string;
    value: string;
    onChange: (v: string) => void;
    error?: string;
    tPlaceholder: string;
    openDefault?: boolean;
    isRtl: boolean;
}) {
    return (
        <Collapsible
            defaultOpen={openDefault}
            className="rounded-lg border border-slate-200 bg-white dark:border-gray-800 dark:bg-slate-950/30"
        >
            <CollapsibleTrigger
                className={cn(
                    'group flex w-full items-center justify-between gap-2 px-4 py-3 text-left text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-900',
                    isRtl && 'text-right',
                )}
            >
                <span>{title}</span>
                <ChevronDown className="h-4 w-4 shrink-0 transition-transform group-data-[state=open]:rotate-180" />
            </CollapsibleTrigger>
            <CollapsibleContent className="border-t border-slate-200 px-4 pb-4 pt-2 dark:border-gray-800">
                <RichTextField
                    value={value}
                    onChange={onChange}
                    placeholder={tPlaceholder}
                    error={error}
                />
            </CollapsibleContent>
        </Collapsible>
    );
}

export function CasePleadingFields({
    caseSubject,
    plaintiffRequests,
    plaintiffEvidence,
    defendantRequests,
    defendantEvidence,
    onFieldChange,
    caseSubjectError,
    errors = {},
    t,
}: Props) {
    const { isRtl } = useLayout();
    const subjectErr =
        caseSubjectError ||
        (htmlPlainTextLength(caseSubject) > CASE_SUBJECT_MAX
            ? t('The case subject may not exceed 8000 characters of text.')
            : undefined);

    return (
        <div className="space-y-4">
            <div>
                <RichTextField
                    label={t('Case subject (subject matter of the claim)')}
                    name="case_subject"
                    value={caseSubject}
                    onChange={(v) => onFieldChange('case_subject', v)}
                    placeholder={t('Enter the subject matter of the claim…')}
                    maxPlainTextLength={CASE_SUBJECT_MAX}
                    error={subjectErr}
                />
            </div>
            <CollapsibleRichSection
                title={t("Plaintiff's requests")}
                value={plaintiffRequests}
                onChange={(v) => onFieldChange('plaintiff_requests', v)}
                error={errors.plaintiff_requests}
                tPlaceholder={t("Plaintiff's requests…")}
                isRtl={isRtl}
            />
            <CollapsibleRichSection
                title={t("Plaintiff's evidence")}
                value={plaintiffEvidence}
                onChange={(v) => onFieldChange('plaintiff_evidence', v)}
                error={errors.plaintiff_evidence}
                tPlaceholder={t("Plaintiff's evidence…")}
                isRtl={isRtl}
            />
            <CollapsibleRichSection
                title={t("Defendant's requests")}
                value={defendantRequests}
                onChange={(v) => onFieldChange('defendant_requests', v)}
                error={errors.defendant_requests}
                tPlaceholder={t("Defendant's requests…")}
                isRtl={isRtl}
            />
            <CollapsibleRichSection
                title={t("Defendant's evidence")}
                value={defendantEvidence}
                onChange={(v) => onFieldChange('defendant_evidence', v)}
                error={errors.defendant_evidence}
                tPlaceholder={t("Defendant's evidence…")}
                isRtl={isRtl}
            />
        </div>
    );
}
