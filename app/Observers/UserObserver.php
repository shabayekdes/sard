<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     * Assign default plan on tenant for new company users (plan lives on tenant).
     */
    public function creating(User $user): void
    {
        if ($user->type !== 'company' || !$user->tenant_id) {
            return;
        }

        $tenant = Tenant::find($user->tenant_id);
        if (!$tenant || $tenant->plan_id) {
            return;
        }

        $defaultPlan = Plan::getDefaultPlan();
        if ($defaultPlan) {
            $tenant->update([
                'plan_id' => $defaultPlan->id,
                'plan_is_active' => 1,
            ]);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->type === 'company' && empty($user->referral_code)) {
            do {
                $code = rand(100000, 999999);
            } while (User::where('referral_code', $code)->exists());

            $user->referral_code = $code;
            $user->saveQuietly();
        }
    }
}