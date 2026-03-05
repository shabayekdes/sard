<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckPlanAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->type !== 'company') {
            $company = User::where('tenant_id', $user->tenant_id)->where('type', 'company')->first();
            if ($company && $company->type === 'company' && $company->isPlanExpired()) {
                auth()->logout();
                return redirect()->route('login')->with('error', __('Access denied. Only company users can access this area.'));
            }
        }

        if ($user->needsPlanSubscription()) {
            $message = __('Please subscribe to a plan to continue.');

            $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;

            if ($user->isTrialExpired()) {
                $message = __('Your trial period has expired. Please subscribe to a plan to continue.');
                if ($tenant) {
                    $tenant->update([
                        'plan_id' => null,
                        'is_trial' => null,
                        'trial_expire_date' => null,
                    ]);
                }
            } elseif ($user->isPlanExpired()) {
                $message = __('Your plan has expired. Please renew your subscription.');
                if ($tenant) {
                    $tenant->update([
                        'plan_id' => null,
                        'plan_expire_date' => null,
                    ]);
                }
            }

            return redirect()->route('plans.index')->with('error', $message);
        }

        return $next($request);
    }
}