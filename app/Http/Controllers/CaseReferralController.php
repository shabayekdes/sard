<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseReferralRequest;
use App\Http\Requests\UpdateCaseReferralRequest;
use App\Http\Resources\CaseReferralResource;
use App\Models\CaseModel;
use App\Models\CaseReferral;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CaseReferralController extends Controller
{
    /**
     * Merge full stage_data from the request after validation.
     * Laravel's validated() only returns keys with rules; dynamic stage-specific fields would otherwise be dropped.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function mergeStageDataFromRequest(FormRequest $request, array $validated): array
    {
        if ($request->has('stage_data')) {
            $validated['stage_data'] = $request->input('stage_data');
        }

        return $validated;
    }

    public function index(CaseModel $case): AnonymousResourceCollection
    {
        $this->authorize('view', $case);

        $referrals = CaseReferral::withPermissionCheck()
            ->where('case_id', $case->id)
            ->latest('id')
            ->get();

        return CaseReferralResource::collection($referrals);
    }

    public function store(StoreCaseReferralRequest $request, CaseModel $case): RedirectResponse
    {
        $this->authorize('update', $case);

        $validated = $this->mergeStageDataFromRequest($request, $request->validated());
        $validated['case_id'] = $case->id;
        $validated['tenant_id'] = createdBy();
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        CaseReferral::create($validated);

        return redirect()->back()->with('success', __('Referral added successfully.'));
    }

    public function show(CaseModel $case, CaseReferral $referral): CaseReferralResource
    {
        $this->authorize('view', $case);
        abort_if((int) $referral->case_id !== (int) $case->id, 404);

        return new CaseReferralResource($referral);
    }

    public function update(UpdateCaseReferralRequest $request, CaseModel $case, CaseReferral $referral): RedirectResponse
    {
        $this->authorize('update', $case);
        abort_if((int) $referral->case_id !== (int) $case->id, 404);

        $validated = $this->mergeStageDataFromRequest($request, $request->validated());
        unset($validated['stage']);
        $validated['updated_by'] = auth()->id();

        $referral->update($validated);

        return redirect()->back()->with('success', __('Referral updated successfully.'));
    }

    public function destroy(CaseModel $case, CaseReferral $referral): RedirectResponse
    {
        $this->authorize('update', $case);
        abort_if((int) $referral->case_id !== (int) $case->id, 404);

        $referral->delete();

        return redirect()->back()->with('success', __('Referral deleted successfully.'));
    }
}
