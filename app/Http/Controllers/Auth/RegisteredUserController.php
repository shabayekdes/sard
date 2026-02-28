<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\Referral;
use App\Models\ReferralSetting;
use App\Services\EmailTemplateService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Database\Models\Domain;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        $referralCode = $request->input('ref');
        $encryptedPlanId = $request->input('plan');
        $planId = null;
        $referrer = null;

        // Decrypt and validate plan ID
        if ($encryptedPlanId) {
            $planId = $this->decryptPlanId($encryptedPlanId);
            if ($planId && !Plan::find($planId)) {
                $planId = null; // Invalid plan ID
            }
        }

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)
                ->where('type', 'company')
                ->first();
        }

        $phoneCountries = Country::where('is_active', true)
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

        return Inertia::render('auth/register', [
            'referralCode' => $referralCode,
            'planId' => $planId,
            'phoneCountries' => $phoneCountries,
            'defaultCountry' => Settings::string('DEFAULT_COUNTRY', 'SA'),
            'referrer' => $referrer ? $referrer->name : null,
            'settings' => settings(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/',
            ],
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
        ]);

        $subdomain = strtolower(trim($request->domain));
        $fullDomain = $subdomain . '.' . config('app.domain');

        if (Domain::where('domain', $fullDomain)->exists()) {
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['domain' => __('This domain is already taken.')]);
        }

        $tenant = Tenant::create();
        $tenant->domains()->create(['domain' => $fullDomain]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'type' => 'company',
            'is_active' => 1,
            'is_enable_login' => 1,
            'plan_is_active' => 0,
            'tenant_id' => $tenant->id,
        ];

        // Handle referral code
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)
                ->where('type', 'company')
                ->first();

            if ($referrer) {
                $userData['used_referral_code'] = $request->referral_code;
            }
        }

        $user = User::create($userData);

        // $emailService = new EmailTemplateService();

        // $variables = [
        //     '{user_email}' => $user->email,
        //     '{user_name}' => $user->name,
        //     '{user_type}' => 'Company',
        //     '{app_name}' => config('app.name'),
        //     '{app_url}' => config('app.url'),
        //     '{theme_color}' => getSetting('theme_color', '#3b82f6')
        // ];

        // $emailService->sendTemplateEmailWithLanguage(
        //     templateName: 'Company Registration Welcome',
        //     variables: $variables,
        //     toEmail: $user->email,
        //     toName: $user->name,
        //     language: 'en'
        // );

        // Assign role and settings to the user
        //TODO: change default values to listener
        defaultRoleAndSetting($user);

        $companyRole = Role::where('name', 'company')->first();

        if ($companyRole) {
            $user->assignRole($companyRole);
        }

        // Note: Referral record will be created when user purchases a plan
        // This is handled in the PlanController or payment controllers

        Auth::login($user);

        // Redirect on current host only (avoid cross-origin redirect so session/cookies stay)
        $currentHost = $request->getSchemeAndHttpHost();

        $emailVerificationEnabled = Settings::boolean('EMAIL_VERIFICATION_ENABLED');
        if ($emailVerificationEnabled) {
            event(new Registered($user));
            return redirect()->away("{$currentHost}/verify-email");
        }

        $planId = $request->plan_id;
        if ($planId) {
            return redirect()->away("{$currentHost}/plans?selected={$planId}");
        }

        return redirect()->away("{$currentHost}/dashboard");
    }

    /**
     * Decrypt plan ID from encrypted string
     */
    private function decryptPlanId($encryptedPlanId)
    {
        try {
            $key = 'advocate2025'; // Use a secure key
            $encrypted = base64_decode($encryptedPlanId);
            $decrypted = '';

            for ($i = 0; $i < strlen($encrypted); $i++) {
                $decrypted .= chr(ord($encrypted[$i]) ^ ord($key[$i % strlen($key)]));
            }

            return is_numeric($decrypted) ? (int)$decrypted : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create referral record when user purchases a plan
     */
    private function createReferralRecord(User $user)
    {
        $settings = ReferralSetting::current();

        if (!$settings->is_enabled) {
            return;
        }

        $referrer = User::where('referral_code', $user->used_referral_code)->first();
        if (!$referrer || !$user->plan) {
            return;
        }

        // Calculate commission based on plan price
        $planPrice = $user->plan->price ?? 0;
        $commissionAmount = ($planPrice * $settings->commission_percentage) / 100;

        if ($commissionAmount > 0) {
            Referral::create([
                'user_id' => $user->id,
                'company_id' => $referrer->id,
                'commission_percentage' => $settings->commission_percentage,
                'amount' => $commissionAmount,
                'plan_id' => $user->plan_id,
            ]);
        }
    }
}
