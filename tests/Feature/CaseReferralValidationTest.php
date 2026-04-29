<?php

use App\Http\Requests\StoreCaseReferralRequest;
use Illuminate\Support\Facades\Validator;

it('accepts valid referral payload', function () {
    $request = new StoreCaseReferralRequest;
    $rules = $request->rules();

    $validator = Validator::make([
        'stage' => 'execution',
        'referral_date' => '2026-04-28',
        'reminder_enabled' => true,
        'reminder_duration' => 7,
        'stage_data' => [
            'request_status' => 'in_progress',
            'requesters' => [['name' => 'أحمد', 'national_id' => '123']],
            'respondents' => [['name' => 'شركة', 'national_id' => '456']],
        ],
    ], $rules);

    expect($validator->fails())->toBeFalse();
});

it('rejects invalid execution request status', function () {
    $request = new StoreCaseReferralRequest;
    $rules = $request->rules();

    $validator = Validator::make([
        'stage' => 'execution',
        'referral_date' => '2026-04-28',
        'stage_data' => [
            'request_status' => 'unknown',
        ],
    ], $rules);

    expect($validator->fails())->toBeTrue();
});
