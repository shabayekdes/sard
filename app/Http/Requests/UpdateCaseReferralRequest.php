<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'stage' => ['sometimes', Rule::in($this->stageKeys())],
            'referral_date' => ['sometimes', 'date'],
            'referral_date_is_hijri' => ['nullable', 'boolean'],
            'reminder_enabled' => ['nullable', 'boolean'],
            'reminder_duration' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'string'],
            'stage_data' => ['nullable', 'array'],
            'stage_data.request_status' => ['nullable', Rule::in(['new', 'in_progress', 'suspended', 'completed', 'rejected'])],
            'stage_data.requesters' => ['nullable', 'array'],
            'stage_data.requesters.*.name' => ['nullable', 'string', 'max:255'],
            'stage_data.requesters.*.national_id' => ['nullable', 'string', 'max:255'],
            'stage_data.respondents' => ['nullable', 'array'],
            'stage_data.respondents.*.name' => ['nullable', 'string', 'max:255'],
            'stage_data.respondents.*.national_id' => ['nullable', 'string', 'max:255'],
            'stage_data.court_id' => [
                'nullable',
                Rule::exists('courts', 'id')->where('tenant_id', createdBy()),
            ],
        ];
    }

    private function stageKeys(): array
    {
        return [
            'amicable_settlement',
            'reconciliation',
            'first_instance',
            'appeal',
            'supreme_court',
            'execution',
        ];
    }
}
