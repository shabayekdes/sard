<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\CaseModel;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    const PLAN_STATUS_ON = 'on';
    const PLAN_STATUS_OFF = 'off';
    const BILLING_CYCLE_MONTHLY = 'monthly';
    const BILLING_CYCLE_YEARLY = 'yearly';
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Company users see only active plans
        if ($user->type !== 'superadmin') {
            return $this->companyPlansView($request);
        }
        
        // Admin view - validate billing cycle input
        $billingCycle = $request->input('billing_cycle', 'monthly');
        if (!in_array($billingCycle, ['monthly', 'yearly'])) {
            $billingCycle = 'monthly';
        }
        
        $dbPlans = Plan::all();
        $hasDefaultPlan = $dbPlans->where('is_default', true)->count() > 0;
        $hasMonthlyPlans = $dbPlans->contains(function (Plan $plan) {
            return $plan->supportsBillingCycle('monthly');
        });
        $hasYearlyPlans = $dbPlans->contains(function (Plan $plan) {
            return $plan->supportsBillingCycle('yearly');
        });

        if ($billingCycle === 'monthly' && !$hasMonthlyPlans && $hasYearlyPlans) {
            $billingCycle = 'yearly';
        }

        if ($billingCycle === 'yearly' && !$hasYearlyPlans && $hasMonthlyPlans) {
            $billingCycle = 'monthly';
        }
        
        $plans = $dbPlans->filter(function ($plan) use ($billingCycle) {
            return $plan->supportsBillingCycle($billingCycle);
        })->map(function ($plan) use ($billingCycle) {
            // Determine features based on plan attributes
            $features = [];
            if ($plan->enable_chatgpt === 'on') $features[] = 'AI Integration';
            
            // Get price based on billing cycle
            $price = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->price;
            
            // Format price with super admin currency settings
            $formattedPrice = formatCurrency($price, ['variant' => 'superadmin']);
            
            // Set duration based on billing cycle
            $duration = $billingCycle === 'yearly' ? 'Yearly' : 'Monthly';
            
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'name_translations' => $plan->getTranslations('name'),
                'price' => $price,
                'formatted_price' => $formattedPrice,
                'duration' => $duration,
                'description' => $plan->description,
                'description_translations' => $plan->getTranslations('description'),
                'billing_cycle' => $plan->billing_cycle ?: 'both',
                'trial_days' => $plan->trial_day,
                'features' => $features,
                'stats' => [
                    'users' => $plan->max_users,
                    'cases' => $plan->max_cases,
                    'clients' => $plan->max_clients,
                    'storage' => $plan->storage_limit,
                ],
                'status' => $plan->is_plan_enable === 'on',
                'is_default' => $plan->is_default,
                'has_users' => $plan->tenants()->count() > 0,
                'recommended' => false // Default to false
            ];
        })->values()->toArray();
        
        // Mark the plan with most subscribers as recommended
        $planSubscriberCounts = Plan::withCount('tenants')->get()->pluck('tenants_count', 'id');
        $mostSubscribedPlanId = $planSubscriberCounts->keys()->first();
        if ($planSubscriberCounts->isNotEmpty()) {
            $mostSubscribedPlanId = $planSubscriberCounts->keys()->sortByDesc(function($planId) use ($planSubscriberCounts) {
                return $planSubscriberCounts[$planId];
            })->first();
        }
        
        foreach ($plans as &$plan) {
            if ($plan['id'] == $mostSubscribedPlanId && $plan['price'] != '0') {
                $plan['recommended'] = true;
                break;
            }
        }

        return Inertia::render('plans/index', [
            'plans' => $plans,
            'billingCycle' => $billingCycle,
            'hasDefaultPlan' => $hasDefaultPlan,
            'hasMonthlyPlans' => $hasMonthlyPlans,
            'hasYearlyPlans' => $hasYearlyPlans,
            'isAdmin' => true
        ]);
    }
    
    /**
     * Toggle plan status
     */
    public function toggleStatus(Plan $plan)
    {
        $plan->is_plan_enable = $plan->is_plan_enable === 'on' ? 'off' : 'on';
        $plan->save();
        
        return back();
    }
    
    /**
     * Show the form for creating a new plan
     */
    public function create()
    {
        $hasDefaultPlan = Plan::where('is_default', true)->exists();
        
        return Inertia::render('plans/create', [
            'hasDefaultPlan' => $hasDefaultPlan
        ]);
    }
    
    /**
     * Store a newly created plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required_if:billing_cycle,monthly,both|nullable|numeric|min:0',
            'yearly_price' => 'required_if:billing_cycle,yearly|nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,both',
            'duration' => 'required|string',
            'description' => 'nullable',
            'max_users' => 'required|integer|min:-1',
            'max_cases' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'storage_limit' => 'required|numeric|min:-1',
            'enable_chatgpt' => 'nullable|in:on,off',
            'is_trial' => 'nullable|in:on,off',
            'trial_day' => 'nullable|integer|min:0',
            'is_plan_enable' => 'nullable|in:on,off',
            'is_default' => 'nullable|boolean',
            'name.en' => 'nullable|string|max:100',
            'name.ar' => 'required|string|max:100',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ]);
        
        // Set default values for nullable fields
        $validated['enable_chatgpt'] = $validated['enable_chatgpt'] ?? 'off';
        $validated['is_trial'] = $validated['is_trial'] ?? null;
        $validated['is_plan_enable'] = $validated['is_plan_enable'] ?? 'on';
        $validated['is_default'] = $validated['is_default'] ?? false;
        
        // If yearly_price is not provided for "both", calculate it as 80% of monthly price * 12
        if ($validated['billing_cycle'] === 'both' && (!isset($validated['yearly_price']) || $validated['yearly_price'] === null)) {
            $validated['yearly_price'] = $validated['price'] * 12 * 0.8;
        }
        
        try {
            // If this plan is set as default, remove default status from other plans
            if ($validated['is_default']) {
                Plan::where('is_default', true)->update(['is_default' => false]);
            }
            
            // Create the plan
            Plan::create($validated);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to create plan: ') . $e->getMessage()]);
        }
        
        return redirect()->route('plans.index')->with('success', __('Plan created successfully.'));
    }
    
    /**
     * Show the form for editing a plan
     */
    public function edit(Plan $plan)
    {
        $otherDefaultPlanExists = Plan::where('is_default', true)
            ->where('id', '!=', $plan->id)
            ->exists();
            
        return Inertia::render('plans/edit', [
            'plan' => array_merge($plan->toArray(), [
                'name_translations' => $plan->getTranslations('name'),
                'description_translations' => $plan->getTranslations('description'),
            ]),
            'otherDefaultPlanExists' => $otherDefaultPlanExists
        ]);
    }
    
    /**
     * Update a plan
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required_if:billing_cycle,monthly,both|nullable|numeric|min:0',
            'yearly_price' => 'required_if:billing_cycle,yearly|nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,both',
            'duration' => 'required|string',
            'description' => 'nullable',
            'max_users' => 'required|integer|min:-1',
            'max_cases' => 'required|integer|min:-1',
            'max_clients' => 'required|integer|min:-1',
            'storage_limit' => 'required|numeric|min:-1',
            'enable_chatgpt' => 'nullable|in:on,off',
            'is_trial' => 'nullable|in:on,off',
            'trial_day' => 'nullable|integer|min:0',
            'is_plan_enable' => 'nullable|in:on,off',
            'is_default' => 'nullable|boolean',
            'name.en' => 'nullable|string|max:100',
            'name.ar' => 'required|string|max:100',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ]);
        
        // Set default values for nullable fields
        $validated['enable_chatgpt'] = $validated['enable_chatgpt'] ?? 'off';
        $validated['is_trial'] = $validated['is_trial'] ?? null;
        $validated['is_plan_enable'] = $validated['is_plan_enable'] ?? 'on';
        $validated['is_default'] = $validated['is_default'] ?? false;
        
        // If yearly_price is not provided for "both", calculate it as 80% of monthly price * 12
        if ($validated['billing_cycle'] === 'both' && (!isset($validated['yearly_price']) || $validated['yearly_price'] === null)) {
            $validated['yearly_price'] = $validated['price'] * 12 * 0.8;
        }
        
        try {
            // If this plan is set as default, remove default status from other plans
            if ($validated['is_default'] && !$plan->is_default) {
                Plan::where('is_default', true)->update(['is_default' => false]);
            }
            
            // Update the plan
            $plan->update($validated);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to update plan: ') . $e->getMessage()]);
        }
        
        return redirect()->route('plans.index')->with('success', __('Plan updated successfully.'));
    }
    
    /**
     * Delete a plan
     */
    public function destroy(Plan $plan)
    {
        // Don't allow deleting the default plan
        if ($plan->is_default) {
            return back()->with('error', __('Cannot delete the default plan.'));
        }
        
        $plan->delete();
        
        return redirect()->route('plans.index')->with('success', __('Plan deleted successfully.'));
    }
    
    private function companyPlansView(Request $request)
    {
        $user = auth()->user();
        $billingCycle = $request->input('billing_cycle', 'monthly');
        if (!in_array($billingCycle, ['monthly', 'yearly'])) {
            $billingCycle = 'monthly';
        }
        
        $dbPlans = Plan::where('is_plan_enable', 'on')->get();
        $hasMonthlyPlans = $dbPlans->contains(function (Plan $plan) {
            return $plan->supportsBillingCycle('monthly');
        });
        $hasYearlyPlans = $dbPlans->contains(function (Plan $plan) {
            return $plan->supportsBillingCycle('yearly');
        });

        if ($billingCycle === 'monthly' && !$hasMonthlyPlans && $hasYearlyPlans) {
            $billingCycle = 'yearly';
        }

        if ($billingCycle === 'yearly' && !$hasYearlyPlans && $hasMonthlyPlans) {
            $billingCycle = 'monthly';
        }
        
        // Get user's pending plan requests
        $pendingRequests = \App\Models\PlanRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('plan_id')
            ->toArray();
            
        // Get user's pending subscription orders
        $pendingOrders = \App\Models\PlanOrder::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('plan_id')
            ->toArray();
        
        $plans = $dbPlans->filter(function ($plan) use ($billingCycle) {
            return $plan->supportsBillingCycle($billingCycle);
        })->map(function ($plan) use ($billingCycle, $user, $pendingRequests, $pendingOrders) {
            $price = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->price;
            
            $features = [];
            if ($plan->enable_chatgpt === 'on') $features[] = 'AI Integration';
            
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'name_translations' => $plan->getTranslations('name'),
                'price' => $price,
                'formatted_price' => formatCurrency($price, ['variant' => 'superadmin']),
                'duration' => $billingCycle === 'yearly' ? 'Yearly' : 'Monthly',
                'description' => $plan->description,
                'description_translations' => $plan->getTranslations('description'),
                'billing_cycle' => $plan->billing_cycle ?: 'both',
                'trial_days' => $plan->trial_day,
                'features' => $features,
                'stats' => [
                    'users' => $plan->max_users,
                    'cases' => $plan->max_cases,
                    'clients' => $plan->max_clients,
                    'storage' => $plan->storage_limit,
                ],
                'is_current' => $user->getTenantForPlan()?->plan_id == $plan->id,
                'is_trial_available' => $plan->is_trial === 'on' && !$user->getTenantForPlan()?->is_trial,
                'is_default' => $plan->is_default,
                'has_pending_request' => in_array($plan->id, $pendingRequests),
                'has_pending_order' => in_array($plan->id, $pendingOrders),
                'recommended' => false // Default to false
            ];
        })->values();
        
        // Mark the plan with most subscribers as recommended
        $planSubscriberCounts = Plan::withCount('tenants')->get()->pluck('tenants_count', 'id');
        if ($planSubscriberCounts->isNotEmpty()) {
            $mostSubscribedPlanId = $planSubscriberCounts->keys()->sortByDesc(function($planId) use ($planSubscriberCounts) {
                return $planSubscriberCounts[$planId];
            })->first();
            
            $plans = $plans->map(function($plan) use ($mostSubscribedPlanId) {
                if ($plan['id'] == $mostSubscribedPlanId) {
                    $plan['recommended'] = true;
                }
                return $plan;
            });
        }
        
        // Get pending request details for cancel functionality
        $pendingRequestsDetails = \App\Models\PlanRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy('plan_id');

        // Plan status (current usage vs limits) for company users
        $planStatus = null;
        $currentPlan = $user->plan;
        if ($currentPlan) {
            $tenant = $user->getTenantForPlan();
            $companyId = $user->tenant_id;
            $teamMembersUsed = (int) User::where('tenant_id', $companyId)
                ->where('type', 'team_member')
                ->count();
            $casesUsed = (int) CaseModel::where('tenant_id', $companyId)->count();
            $clientsUsed = (int) Client::where('tenant_id', $companyId)->count();
            $storageUsedBytes = $tenant ? (float) $tenant->storage_used : 0;
            $storageUsedGb = round($storageUsedBytes / (1024 ** 3), 2);
            $storageLimit = $currentPlan->storage_limit;
            $planStatus = [
                'name' => $currentPlan->name,
                'name_translations' => $currentPlan->getTranslations('name'),
                'usage' => [
                    'team_members' => ['used' => $teamMembersUsed, 'limit' => $currentPlan->max_users],
                    'storage' => ['used_gb' => $storageUsedGb, 'limit_gb' => $storageLimit],
                    'cases' => ['used' => $casesUsed, 'limit' => $currentPlan->max_cases],
                    'clients' => ['used' => $clientsUsed, 'limit' => $currentPlan->max_clients],
                ],
                'plan_details' => [
                    'team_members' => $currentPlan->max_users,
                    'cases' => $currentPlan->max_cases,
                    'clients' => $currentPlan->max_clients,
                    'storage_gb' => $storageLimit,
                ],
                'price_monthly' => $currentPlan->price,
                'formatted_price_monthly' => formatCurrency($currentPlan->price, ['variant' => 'superadmin']),
                'plan_expire_date' => $tenant?->plan_expire_date?->format('Y-m-d'),
                'trial_expire_date' => $tenant?->trial_expire_date?->format('Y-m-d'),
                'is_trial' => (bool) $tenant?->is_trial,
            ];
        }
        
        return Inertia::render('plans/index', [
            'plans' => $plans,
            'billingCycle' => $billingCycle,
            'currentPlan' => $currentPlan,
            'planStatus' => $planStatus,
            'userTrialUsed' => $user->getTenantForPlan()?->is_trial,
            'hasMonthlyPlans' => $hasMonthlyPlans,
            'hasYearlyPlans' => $hasYearlyPlans,
            'pendingRequests' => $pendingRequestsDetails
        ]);
    }
    
    public function requestPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);
        
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        if (!$plan->supportsBillingCycle($request->billing_cycle)) {
            return back()->withErrors(['error' => __('Selected billing cycle is not available for this plan')]);
        }
        
        // Check if user already has a pending request for this plan
        $existingRequest = \App\Models\PlanRequest::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            return back()->with('error', __('You already have a pending request for this plan'));
        }
        
        try {
            \App\Models\PlanRequest::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'duration' => $request->billing_cycle,
                'status' => 'pending'
            ]);
            
            return back()->with('success', __('Plan request submitted successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to submit plan request: ') . $e->getMessage()]);
        }
    }
    
    public function startTrial(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);
        
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        if ($request->billing_cycle && !$plan->supportsBillingCycle($request->billing_cycle)) {
            return back()->withErrors(['error' => __('Selected billing cycle is not available for this plan')]);
        }
        
        $tenant = $user->getTenantForPlan();
        if (!$tenant || $tenant->is_trial || $plan->is_trial !== 'on') {
            return back()->withErrors(['error' => 'Trial not available']);
        }

        try {
            $tenant->update([
                'plan_id' => $plan->id,
                'is_trial' => '1',
                'trial_day' => $plan->trial_day,
                'trial_expire_date' => now()->addDays($plan->trial_day)
            ]);
            
            return back()->with('success', __('Trial started successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to start trial: ') . $e->getMessage()]);
        }
    }
    
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);
        
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        
        // Use the plan's getPriceForCycle method for consistent pricing
        $price = $plan->getPriceForCycle($request->billing_cycle);
        
        // Validate that yearly price exists if yearly billing is selected
        if ($request->billing_cycle === 'yearly' && $plan->yearly_price === null) {
            return back()->withErrors(['error' => __('Yearly billing is not available for this plan')]);
        }
        
        try {
            \App\Models\PlanOrder::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'original_price' => $price,
                'final_price' => $price,
                'status' => 'pending'
            ]);
            
            return back()->with('success', __('Subscription request submitted successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to create subscription: ') . $e->getMessage()]);
        }
    }

    /**
     * Subscribe to a free (zero-price) plan without showing the payment modal.
     */
    public function subscribeFree(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        if (! $plan->supportsBillingCycle($request->billing_cycle)) {
            return back()->withErrors(['error' => __('Selected billing cycle is not available for this plan')]);
        }

        $price = (float) $plan->getPriceForCycle($request->billing_cycle);
        if ($price != 0) {
            return back()->withErrors(['error' => __('This plan requires payment. Please use the payment form.')]);
        }

        try {
            $order = \App\Models\PlanOrder::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'original_price' => 0,
                'final_price' => 0,
                'payment_method' => 'free',
                'status' => 'pending',
            ]);
            $order->activateSubscription();

            return back()->with('success', __('Plan activated successfully.'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Failed to activate plan: ') . $e->getMessage()]);
        }
    }
}