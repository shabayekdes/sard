<?php

namespace App\Services;

use App\Models\CaseActivityLog;
use App\Models\CaseJudgment;
use App\Models\CaseModel;
use App\Models\CaseReferral;
use App\Models\CaseTimeline;
use App\Models\CaseDocument;
use App\Models\CaseTeamMember;
use App\Models\CaseNote;
use App\Models\CaseStatus;
use App\Models\Hearing;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CaseActivityLogger
{
    public static function referralStageLabel(string $stage): string
    {
        $key = 'case_activity.referral_stage.'.$stage;

        return __($key) !== $key ? __($key) : $stage;
    }

    public static function syncManualTimeline(CaseTimeline $timeline): void
    {
        $occurredAt = self::timelineOccurredAt($timeline);

        CaseActivityLog::updateOrCreate(
            ['case_timeline_id' => $timeline->id],
            [
                'case_id' => $timeline->case_id,
                'tenant_id' => $timeline->tenant_id,
                'occurred_at' => $occurredAt,
                'source' => 'manual',
                'category' => 'timeline',
                'event_key' => 'manual_timeline',
                'title' => $timeline->title,
                'description' => $timeline->description ? Str::limit(strip_tags($timeline->description), 240) : null,
                'meta' => null,
                'subject_type' => CaseTimeline::class,
                'subject_id' => $timeline->id,
            ]
        );
    }

    public static function deleteManualTimeline(int $timelineId): void
    {
        CaseActivityLog::where('case_timeline_id', $timelineId)->delete();
    }

    public static function timelineOccurredAt(CaseTimeline $timeline): Carbon
    {
        $d = $timeline->event_date instanceof Carbon
            ? $timeline->event_date->copy()
            : Carbon::parse($timeline->event_date);

        if ($timeline->event_time) {
            $t = is_string($timeline->event_time)
                ? $timeline->event_time
                : (string) $timeline->event_time;
            if (strlen($t) === 5) {
                $t .= ':00';
            }
            $parts = explode(':', $t);
            $h = (int) ($parts[0] ?? 0);
            $m = (int) ($parts[1] ?? 0);
            $s = (int) ($parts[2] ?? 0);
            $d->setTime($h, $m, $s);
        }

        return $d;
    }

    public static function logAutomatic(
        int $caseId,
        string $tenantId,
        string $category,
        string $eventKey,
        string $title,
        ?string $description,
        Carbon $occurredAt,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $meta = null,
    ): CaseActivityLog {
        return CaseActivityLog::create([
            'case_id' => $caseId,
            'tenant_id' => $tenantId,
            'occurred_at' => $occurredAt,
            'source' => 'automatic',
            'category' => $category,
            'event_key' => $eventKey,
            'title' => $title,
            'description' => $description,
            'meta' => $meta,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
        ]);
    }

    protected static function statusName(?int $id): string
    {
        if (! $id) {
            return '—';
        }
        $row = CaseStatus::find($id);

        return $row ? (string) $row->name : '—';
    }

    public static function caseCreated(CaseModel $case): void
    {
        self::logAutomatic(
            $case->id,
            $case->tenant_id,
            'case',
            'case_created',
            __('case_activity.msg.case_created.title'),
            __('case_activity.msg.case_created.description', ['case_title' => $case->title ?? '']),
            $case->created_at ?? now(),
            CaseModel::class,
            $case->id,
            ['case_title' => $case->title]
        );
    }

    public static function caseDataUpdated(CaseModel $case): void
    {
        self::logAutomatic(
            $case->id,
            $case->tenant_id,
            'case',
            'case_updated',
            __('case_activity.msg.case_updated.title'),
            __('case_activity.msg.case_updated.description'),
            now(),
            CaseModel::class,
            $case->id,
        );
    }

    public static function caseWorkflowStatusChanged(CaseModel $case, ?int $oldId, ?int $newId): void
    {
        self::logAutomatic(
            $case->id,
            $case->tenant_id,
            'case',
            'case_status_changed',
            __('case_activity.msg.case_status_changed.title'),
            __('case_activity.msg.case_status_changed.description', [
                'old' => self::statusName($oldId),
                'new' => self::statusName($newId),
            ]),
            now(),
            CaseModel::class,
            $case->id,
            ['old_status_id' => $oldId, 'new_status_id' => $newId]
        );
    }

    public static function caseActivationToggled(CaseModel $case, string $newStatus): void
    {
        $active = $newStatus === 'active';
        self::logAutomatic(
            $case->id,
            $case->tenant_id,
            'case',
            $active ? 'case_activated' : 'case_deactivated',
            $active
                ? __('case_activity.msg.case_activated.title')
                : __('case_activity.msg.case_deactivated.title'),
            $active
                ? __('case_activity.msg.case_activated.description')
                : __('case_activity.msg.case_deactivated.description'),
            now(),
            CaseModel::class,
            $case->id,
        );
    }

    public static function referralCreated(CaseReferral $referral): void
    {
        $stage = self::referralStageLabel($referral->stage);
        self::logAutomatic(
            $referral->case_id,
            $referral->tenant_id,
            'referral',
            'referral_created',
            __('case_activity.msg.referral_created.title'),
            __('case_activity.msg.referral_created.description', ['stage' => $stage]),
            $referral->created_at ?? now(),
            CaseReferral::class,
            $referral->id,
            ['stage' => $referral->stage]
        );
    }

    public static function referralUpdated(CaseReferral $referral): void
    {
        $stage = self::referralStageLabel($referral->stage);
        self::logAutomatic(
            $referral->case_id,
            $referral->tenant_id,
            'referral',
            'referral_updated',
            __('case_activity.msg.referral_updated.title'),
            __('case_activity.msg.referral_updated.description', ['stage' => $stage]),
            now(),
            CaseReferral::class,
            $referral->id,
            ['stage' => $referral->stage]
        );
    }

    public static function referralDeleted(CaseReferral $referral): void
    {
        $stage = self::referralStageLabel($referral->stage);
        self::logAutomatic(
            $referral->case_id,
            $referral->tenant_id,
            'referral',
            'referral_deleted',
            __('case_activity.msg.referral_deleted.title'),
            __('case_activity.msg.referral_deleted.description', ['stage' => $stage]),
            now(),
            CaseReferral::class,
            $referral->id,
            ['stage' => $referral->stage]
        );
    }

    public static function hearingCreated(Hearing $hearing): void
    {
        $title = $hearing->title ?? $hearing->hearing_id ?? '';
        self::logAutomatic(
            $hearing->case_id,
            $hearing->tenant_id,
            'hearing',
            'hearing_created',
            __('case_activity.msg.hearing_created.title'),
            __('case_activity.msg.hearing_created.description', ['hearing_title' => $title]),
            $hearing->created_at ?? now(),
            Hearing::class,
            $hearing->id,
            ['hearing_title' => $title]
        );
    }

    public static function hearingUpdated(Hearing $hearing): void
    {
        $title = $hearing->title ?? $hearing->hearing_id ?? '';
        self::logAutomatic(
            $hearing->case_id,
            $hearing->tenant_id,
            'hearing',
            'hearing_updated',
            __('case_activity.msg.hearing_updated.title'),
            __('case_activity.msg.hearing_updated.description', ['hearing_title' => $title]),
            now(),
            Hearing::class,
            $hearing->id,
        );
    }

    public static function hearingDeleted(Hearing $hearing): void
    {
        $title = $hearing->title ?? $hearing->hearing_id ?? '';
        self::logAutomatic(
            $hearing->case_id,
            $hearing->tenant_id,
            'hearing',
            'hearing_deleted',
            __('case_activity.msg.hearing_deleted.title'),
            __('case_activity.msg.hearing_deleted.description', ['hearing_title' => $title]),
            now(),
            Hearing::class,
            $hearing->id,
        );
    }

    public static function judgmentCreated(CaseJudgment $judgment): void
    {
        $num = $judgment->judgment_number ?? '';
        self::logAutomatic(
            $judgment->case_id,
            $judgment->tenant_id,
            'judgment',
            'judgment_created',
            __('case_activity.msg.judgment_created.title'),
            __('case_activity.msg.judgment_created.description', ['judgment_number' => $num]),
            $judgment->created_at ?? now(),
            CaseJudgment::class,
            $judgment->id,
        );
    }

    public static function judgmentUpdated(CaseJudgment $judgment): void
    {
        $num = $judgment->judgment_number ?? '';
        self::logAutomatic(
            $judgment->case_id,
            $judgment->tenant_id,
            'judgment',
            'judgment_updated',
            __('case_activity.msg.judgment_updated.title'),
            __('case_activity.msg.judgment_updated.description', ['judgment_number' => $num]),
            now(),
            CaseJudgment::class,
            $judgment->id,
        );
    }

    public static function judgmentDeleted(CaseJudgment $judgment): void
    {
        $num = $judgment->judgment_number ?? '';
        self::logAutomatic(
            $judgment->case_id,
            $judgment->tenant_id,
            'judgment',
            'judgment_deleted',
            __('case_activity.msg.judgment_deleted.title'),
            __('case_activity.msg.judgment_deleted.description', ['judgment_number' => $num]),
            now(),
            CaseJudgment::class,
            $judgment->id,
        );
    }

    public static function documentCreated(CaseDocument $doc): void
    {
        $name = $doc->document_name ?? '';
        self::logAutomatic(
            $doc->case_id,
            $doc->tenant_id,
            'document',
            'document_created',
            __('case_activity.msg.document_created.title'),
            __('case_activity.msg.document_created.description', ['document_name' => $name]),
            $doc->created_at ?? now(),
            CaseDocument::class,
            $doc->id,
        );
    }

    public static function documentDeleted(CaseDocument $doc): void
    {
        $name = $doc->document_name ?? '';
        self::logAutomatic(
            $doc->case_id,
            $doc->tenant_id,
            'document',
            'document_deleted',
            __('case_activity.msg.document_deleted.title'),
            __('case_activity.msg.document_deleted.description', ['document_name' => $name]),
            now(),
            CaseDocument::class,
            $doc->id,
        );
    }

    public static function taskCreated(Task $task): void
    {
        $t = $task->title ?? '';
        self::logAutomatic(
            (int) $task->case_id,
            $task->tenant_id,
            'task',
            'task_created',
            __('case_activity.msg.task_created.title'),
            __('case_activity.msg.task_created.description', ['task_title' => $t]),
            $task->created_at ?? now(),
            Task::class,
            $task->id,
        );
    }

    public static function taskUpdated(Task $task): void
    {
        $t = $task->title ?? '';
        self::logAutomatic(
            (int) $task->case_id,
            $task->tenant_id,
            'task',
            'task_updated',
            __('case_activity.msg.task_updated.title'),
            __('case_activity.msg.task_updated.description', ['task_title' => $t]),
            now(),
            Task::class,
            $task->id,
        );
    }

    public static function taskCompleted(Task $task): void
    {
        $t = $task->title ?? '';
        self::logAutomatic(
            (int) $task->case_id,
            $task->tenant_id,
            'task',
            'task_completed',
            __('case_activity.msg.task_completed.title'),
            __('case_activity.msg.task_completed.description', ['task_title' => $t]),
            now(),
            Task::class,
            $task->id,
        );
    }

    public static function taskDeleted(Task $task): void
    {
        $t = $task->title ?? '';
        self::logAutomatic(
            (int) $task->case_id,
            $task->tenant_id,
            'task',
            'task_deleted',
            __('case_activity.msg.task_deleted.title'),
            __('case_activity.msg.task_deleted.description', ['task_title' => $t]),
            now(),
            Task::class,
            $task->id,
        );
    }

    public static function assigneeAdded(CaseTeamMember $member): void
    {
        $member->loadMissing('user');
        $name = $member->user?->name ?? '';
        self::logAutomatic(
            $member->case_id,
            $member->tenant_id,
            'assignee',
            'assignee_added',
            __('case_activity.msg.assignee_added.title'),
            __('case_activity.msg.assignee_added.description', ['user_name' => $name]),
            $member->created_at ?? now(),
            CaseTeamMember::class,
            $member->id,
        );
    }

    public static function assigneeRemoved(CaseTeamMember $member): void
    {
        $member->loadMissing('user');
        $name = $member->user?->name ?? '';
        self::logAutomatic(
            $member->case_id,
            $member->tenant_id,
            'assignee',
            'assignee_removed',
            __('case_activity.msg.assignee_removed.title'),
            __('case_activity.msg.assignee_removed.description', ['user_name' => $name]),
            now(),
            CaseTeamMember::class,
            $member->id,
        );
    }

    public static function noteCreatedForCase(CaseNote $note, int $caseId): void
    {
        $preview = Str::limit(strip_tags((string) $note->content), 120);
        self::logAutomatic(
            $caseId,
            $note->tenant_id,
            'note',
            'note_created',
            __('case_activity.msg.note_created.title'),
            __('case_activity.msg.note_created.description', ['preview' => $preview]),
            $note->created_at ?? now(),
            CaseNote::class,
            $note->id,
        );
    }

    public static function noteUpdatedForCase(CaseNote $note, int $caseId): void
    {
        self::logAutomatic(
            $caseId,
            $note->tenant_id,
            'note',
            'note_updated',
            __('case_activity.msg.note_updated.title'),
            __('case_activity.msg.note_updated.description'),
            now(),
            CaseNote::class,
            $note->id,
        );
    }

    public static function taskStatusIsCompleted(?int $taskStatusId): bool
    {
        if ($taskStatusId === null || $taskStatusId === 0) {
            return false;
        }
        $s = TaskStatus::find($taskStatusId);

        return $s && $s->is_completed;
    }
}
