<?php

namespace App\Http\Controllers;

use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\TenantCity;
use App\Facades\Settings;
use App\Models\CompanyProfile;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Stancl\Tenancy\Database\Models\Domain;

class CompanyProfileController extends Controller
{
    public function index(Request $request)
    {
        // Get single advocate profile for current user
        $companyProfile = CompanyProfile::withPermissionCheck()->where('tenant_id', createdBy())->first();

        $phoneCountries = Country::where('status', 'active')
            ->whereNotNull('country_code')
            ->get(['id', 'name', 'country_code'])
            ->map(function ($country) {
                return [
                    'value' => $country->id,
                    'label' => $country->name,
                    'code' => $country->country_code,
                ];
            })
            ->values();

        $tenantId = createdBy();
        $registrationDomain = $tenantId
            ? (Domain::where('tenant_id', (string) $tenantId)->orderBy('id')->value('domain') ?? '')
            : '';

        $companyOwner = $tenantId
            ? User::where('tenant_id', $tenantId)->where('type', 'company')->first()
            : null;
        $accountSubject = $companyOwner ?? $request->user();

        return Inertia::render('advocate/company-profiles/index', [
            'companyProfile' => $companyProfile,
            'officeSizeOptions' => CompanySize::options(),
            'businessTypeOptions' => BusinessType::options(),
            'tenantCityOptions' => TenantCity::options(),
            'phoneCountries' => $phoneCountries,
            'defaultCountry' => Settings::string('DEFAULT_COUNTRY', 'SA'),
            'registrationDomain' => $registrationDomain,
            'tenantCity' => tenant()?->city ?? '',
            'accountUser' => $accountSubject ? [
                'name' => $accountSubject->name,
                'email' => $accountSubject->email,
                'phone' => $accountSubject->phone ?? '',
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeAccountRequest($request);

        $accountUser = $this->resolveAccountSubjectUser($request);

        $validated = $request->validate(array_merge($this->accountFieldRules($accountUser), [
            'address' => 'nullable|string',

            'consultation_fees' => 'nullable|numeric|min:0',
            'office_hours' => 'nullable|string|max:255',
            'success_rate' => 'nullable|integer|min:0|max:100',

            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date',
            'cr' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'company_size' => ['required', 'in:'.implode(',', CompanySize::values())],
            'business_type' => ['required', 'string', 'in:'.implode(',', BusinessType::values())],
            'default_setup' => 'nullable|string|max:255',

            'services_offered' => 'nullable|string',
            'description' => 'nullable|string',
        ]));

        $this->applyAccountUpdates($validated, $accountUser);

        $validated['tenant_id'] = createdBy();
        $validated['email'] = $accountUser->fresh()->email;
        $validated['phone'] = $accountUser->fresh()->phone;

        $profilePayload = $this->onlyCompanyProfileAttributes($validated);

        // Check if company profile already exists for this user
        $exists = CompanyProfile::where('tenant_id', createdBy())->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Company profile already exists for this user.');
        }

        CompanyProfile::create($profilePayload);

        return redirect()->back()->with('success', 'Company profile created successfully.');
    }

    public function update(Request $request, $profileId = null)
    {
        $this->normalizeAccountRequest($request);

        // Get existing profile or prepare for creation
        $profile = CompanyProfile::where('tenant_id', createdBy())->first();

        $accountUser = $this->resolveAccountSubjectUser($request);

        $validated = $request->validate(array_merge($this->accountFieldRules($accountUser), [
            'address' => 'nullable|string',

            'consultation_fees' => 'nullable|numeric|min:0',
            'office_hours' => 'nullable|string|max:255',
            'success_rate' => 'nullable|integer|min:0|max:100',

            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date',
            'cr' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'company_size' => ['required', 'in:'.implode(',', CompanySize::values())],
            'business_type' => ['required', 'string', 'in:'.implode(',', BusinessType::values())],
            'default_setup' => 'nullable|string|max:255',

            'services_offered' => 'nullable|string',
            'description' => 'nullable|string',
        ]));

        $this->applyAccountUpdates($validated, $accountUser);

        $validated['tenant_id'] = createdBy();
        $validated['email'] = $accountUser->fresh()->email;
        $validated['phone'] = $accountUser->fresh()->phone;

        $profilePayload = $this->onlyCompanyProfileAttributes($validated);

        if ($profile) {
            $profile->update($profilePayload);

            return redirect()->back()->with('success', 'Advocate profile updated successfully');
        } else {
            CompanyProfile::create($profilePayload);

            return redirect()->back()->with('success', 'Advocate profile created successfully');
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function onlyCompanyProfileAttributes(array $validated): array
    {
        foreach (['full_name', 'account_email', 'account_phone', 'account_city'] as $key) {
            unset($validated[$key]);
        }

        return $validated;
    }

    /**
     * Company login account (owner) for this tenant — used for Section 1 account fields.
     */
    private function resolveAccountSubjectUser(Request $request): User
    {
        $tenantId = createdBy();
        if ($tenantId) {
            $owner = User::where('tenant_id', $tenantId)->where('type', 'company')->first();
            if ($owner) {
                return $owner;
            }
        }

        return $request->user();
    }

    /**
     * @return array<string, mixed>
     */
    private function accountFieldRules(User $accountUser): array
    {
        $userId = $accountUser->id;

        return [
            'full_name' => 'nullable|string|max:255',
            'account_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'account_phone' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'phone')->where(fn ($q) => $q->where('tenant_id', createdBy()))->ignore($userId),
            ],
            'account_city' => ['nullable', 'string', 'in:'.implode(',', TenantCity::values())],
        ];
    }

    private function normalizeAccountRequest(Request $request): void
    {
        if ($request->input('account_phone') === '') {
            $request->merge(['account_phone' => null]);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyAccountUpdates(array $validated, User $accountUser): void
    {
        $userPayload = [];
        if (array_key_exists('full_name', $validated) && $validated['full_name'] !== null && $validated['full_name'] !== '') {
            $userPayload['name'] = $validated['full_name'];
        }
        if (array_key_exists('account_email', $validated) && $validated['account_email'] !== null && $validated['account_email'] !== '') {
            $userPayload['email'] = $validated['account_email'];
        }
        if (array_key_exists('account_phone', $validated)) {
            $userPayload['phone'] = $validated['account_phone'] ?: null;
        }
        if ($userPayload !== []) {
            $accountUser->update($userPayload);
        }

        $tenant = tenant();
        if ($tenant && array_key_exists('account_city', $validated)) {
            $tenant->update([
                'city' => $validated['account_city'] ?: null,
            ]);
        }
    }

    public function destroy($profileId)
    {
        $profile = CompanyProfile::where('id', $profileId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($profile) {
            try {
                $profile->delete();

                return redirect()->back()->with('success', 'Company profile deleted successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to delete company profile');
            }
        } else {
            return redirect()->back()->with('error', 'Company profile not found.');
        }
    }
}
