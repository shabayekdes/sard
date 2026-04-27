<?php

namespace App\Http\Controllers;

use App\Models\CaseJudgment;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaseJudgmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'judgment_number' => 'required|string|max:500',
            'judgment_date' => 'required|date',
            'receipt_date' => 'nullable|date',
            'appeal_deadline_date' => 'nullable|date',
            'appeal_reminder_enabled' => 'nullable',
            'appeal_reminder_duration' => [
                'nullable',
                Rule::requiredIf(fn () => filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN)),
                Rule::in(['one_day_before', 'three_days_before', 'one_week_before', 'custom']),
            ],
            'appeal_reminder_custom_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:366',
                Rule::requiredIf(fn () => filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN)
                    && $request->string('appeal_reminder_duration')->toString() === 'custom'),
            ],
            'status' => ['required', Rule::in(['pending_issuance', 'issued', 'appealed', 'final', 'executed'])],
            'attachments' => 'nullable|string',
            'grounds' => 'nullable|string',
            'summary' => 'nullable|string',
        ]);

        $this->assertCaseInTenant($validated['case_id']);

        $appealOn = filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN);
        if ($appealOn && empty($validated['appeal_deadline_date'])) {
            return back()->withErrors([
                'appeal_deadline_date' => __('Appeal deadline date is required when the reminder is enabled.'),
            ]);
        }

        $validated = $this->normalizeAppealReminderFields($validated, $appealOn);

        $attachmentPaths = $this->normalizeAttachmentUrls($validated['attachments'] ?? null);
        unset($validated['attachments']);

        CaseJudgment::create(array_merge($validated, [
            'appeal_reminder_enabled' => $appealOn,
            'attachment_paths' => $attachmentPaths,
            'tenant_id' => createdBy(),
        ]));

        return redirect()->back()->with('success', __('Judgment record created successfully.'));
    }

    public function update(Request $request, int $id)
    {
        $judgment = CaseJudgment::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (! $judgment) {
            return redirect()->back()->with('error', __('Record not found.'));
        }

        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'judgment_number' => 'required|string|max:500',
            'judgment_date' => 'required|date',
            'receipt_date' => 'nullable|date',
            'appeal_deadline_date' => 'nullable|date',
            'appeal_reminder_enabled' => 'nullable',
            'appeal_reminder_duration' => [
                'nullable',
                Rule::requiredIf(fn () => filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN)),
                Rule::in(['one_day_before', 'three_days_before', 'one_week_before', 'custom']),
            ],
            'appeal_reminder_custom_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:366',
                Rule::requiredIf(fn () => filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN)
                    && $request->string('appeal_reminder_duration')->toString() === 'custom'),
            ],
            'status' => ['required', Rule::in(['pending_issuance', 'issued', 'appealed', 'final', 'executed'])],
            'attachments' => 'nullable|string',
            'grounds' => 'nullable|string',
            'summary' => 'nullable|string',
        ]);

        $this->assertCaseInTenant($validated['case_id']);

        $appealOn = filter_var($request->input('appeal_reminder_enabled'), FILTER_VALIDATE_BOOLEAN);
        if ($appealOn && empty($validated['appeal_deadline_date'])) {
            return back()->withErrors([
                'appeal_deadline_date' => __('Appeal deadline date is required when the reminder is enabled.'),
            ]);
        }

        $validated = $this->normalizeAppealReminderFields($validated, $appealOn);

        $attachmentPaths = $this->normalizeAttachmentUrls($validated['attachments'] ?? null);
        unset($validated['attachments']);

        $judgment->fill($validated);
        $judgment->appeal_reminder_enabled = $appealOn;
        $judgment->attachment_paths = $attachmentPaths;
        $judgment->save();

        return redirect()->back()->with('success', __('Judgment record updated successfully.'));
    }

    public function destroy(int $id)
    {
        $judgment = CaseJudgment::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (! $judgment) {
            return redirect()->back()->with('error', __('Record not found.'));
        }

        $judgment->delete();

        return redirect()->back()->with('success', __('Judgment record deleted successfully.'));
    }

    private function normalizeAppealReminderFields(array $validated, bool $appealReminderEnabled): array
    {
        if (! $appealReminderEnabled) {
            $validated['appeal_reminder_duration'] = 'one_day_before';
            $validated['appeal_reminder_custom_days'] = null;

            return $validated;
        }

        if (($validated['appeal_reminder_duration'] ?? 'one_day_before') !== 'custom') {
            $validated['appeal_reminder_custom_days'] = null;
        } else {
            $validated['appeal_reminder_custom_days'] = isset($validated['appeal_reminder_custom_days'])
                ? (int) $validated['appeal_reminder_custom_days']
                : null;
        }

        return $validated;
    }

    private function assertCaseInTenant(int $caseId): void
    {
        $exists = CaseModel::where('id', $caseId)
            ->where('tenant_id', createdBy())
            ->exists();
        if (! $exists) {
            abort(403);
        }
    }

    private function normalizeAttachmentUrls(?string $value): array
    {
        if (empty($value)) {
            return [];
        }
        $parts = collect(preg_split('/\s*,\s*/', $value))
            ->map(fn (string $s) => trim($s))
            ->filter()
            ->map(fn (string $url) => $this->convertToRelativePath($url))
            ->values()
            ->all();

        return $parts;
    }

    private function convertToRelativePath(string $url): string
    {
        if (! str_starts_with($url, 'http')) {
            return $url;
        }
        $storageIndex = strpos($url, '/storage/');
        if ($storageIndex !== false) {
            return substr($url, $storageIndex);
        }

        return $url;
    }
}
