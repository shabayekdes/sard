<?php

namespace App\Http\Controllers;

use App\Enums\CompanySize;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyProfileController extends Controller
{
    public function index(Request $request)
    {
        // Get single advocate profile for current user
        $companyProfile = CompanyProfile::withPermissionCheck()->where('tenant_id', createdBy())->first();

        return Inertia::render('advocate/company-profiles/index', [
            'companyProfile' => $companyProfile,
            'officeSizeOptions' => CompanySize::options(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Contact Details
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            
            // Business Details
            'consultation_fees' => 'nullable|numeric|min:0',
            'office_hours' => 'nullable|string|max:255',
            'success_rate' => 'nullable|integer|min:0|max:100',
            
            // Company Details
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date',
            'cr' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'company_size' => ['required', 'in:' . implode(',', CompanySize::values())],
            'business_type' => 'required|in:law_firm,corporate_legal,government,other',
            'default_setup' => 'nullable|string|max:255',
            
            // Services
            'services_offered' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = createdBy();

        // Check if company profile already exists for this user
        $exists = CompanyProfile::where('tenant_id', createdBy())->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Company profile already exists for this user.');
        }

        CompanyProfile::create($validated);

        return redirect()->back()->with('success', 'Company profile created successfully.');
    }

    public function update(Request $request, $profileId = null)
    {
        // Get existing profile or prepare for creation
        $profile = CompanyProfile::where('tenant_id', createdBy())->first();
        
        $validated = $request->validate([
            // Contact Details
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            
            // Business Details
            'consultation_fees' => 'nullable|numeric|min:0',
            'office_hours' => 'nullable|string|max:255',
            'success_rate' => 'nullable|integer|min:0|max:100',
            
            // Company Details
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date',
            'cr' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'company_size' => ['required', 'in:' . implode(',', CompanySize::values())],
            'business_type' => 'required|in:law_firm,corporate_legal,government,other',
            'default_setup' => 'nullable|string|max:255',
            
            // Services
            'services_offered' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = createdBy();

        if ($profile) {
            $profile->update($validated);
            return redirect()->back()->with('success', 'Advocate profile updated successfully');
        } else {
            CompanyProfile::create($validated);
            return redirect()->back()->with('success', 'Advocate profile created successfully');
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