<?php

namespace App\Providers;

use App\Models\CaseDocument;
use App\Models\CaseJudgment;
use App\Models\CaseModel;
use App\Models\CaseNote;
use App\Models\CaseReferral;
use App\Models\CaseTeamMember;
use App\Models\Hearing;
use App\Models\Plan;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\CaseDocumentCaseActivityObserver;
use App\Observers\CaseJudgmentCaseActivityObserver;
use App\Observers\CaseModelCaseActivityObserver;
use App\Observers\CaseNoteCaseActivityObserver;
use App\Observers\CaseReferralCaseActivityObserver;
use App\Observers\CaseTeamMemberCaseActivityObserver;
use App\Observers\HearingCaseActivityObserver;
use App\Observers\PlanObserver;
use App\Observers\TaskCaseActivityObserver;
use App\Observers\UserObserver;
use App\Policies\CasePolicy;
use App\Providers\AssetServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\WebhookService::class);
        
        // Register our AssetServiceProvider
        $this->app->register(AssetServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the UserObserver
        User::observe(UserObserver::class);

        // Redirect unauthenticated users to login on the same host (tenant or central)
        Authenticate::redirectUsing(function (Request $request): string {
            return $request->getSchemeAndHttpHost() . '/login';
        });

        VerifyEmail::createUrlUsing(function (User $notifiable): string {
            $expires = Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60));
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());

            if (! empty($notifiable->tenant_id)) {
                /** @var Tenant|null $tenant */
                $tenant = Tenant::find($notifiable->tenant_id);
                $domain = $tenant?->domains()->first()?->domain;
                if (! empty($domain)) {
                    $scheme = parse_url(Config::get('app.url'), PHP_URL_SCHEME) ?: 'https';
                    $baseUrl = rtrim("{$scheme}://{$domain}", '/');
                    $path = "/verify-email/{$id}/{$hash}";
                    $expiresTs = $expires->getTimestamp();
                    $urlToSign = $baseUrl . $path . '?expires=' . $expiresTs;
                    $key = Config::get('app.key');
                    $signature = hash_hmac('sha256', $urlToSign, $key);

                    return $urlToSign . '&signature=' . $signature;
                }
            }

            return URL::temporarySignedRoute('verification.verify', $expires, [
                'id' => $id,
                'hash' => $hash,
            ]);
        });

        // Register the PlanObserver
        Plan::observe(PlanObserver::class);

        CaseModel::observe(CaseModelCaseActivityObserver::class);
        CaseReferral::observe(CaseReferralCaseActivityObserver::class);
        Hearing::observe(HearingCaseActivityObserver::class);
        CaseJudgment::observe(CaseJudgmentCaseActivityObserver::class);
        CaseDocument::observe(CaseDocumentCaseActivityObserver::class);
        Task::observe(TaskCaseActivityObserver::class);
        CaseTeamMember::observe(CaseTeamMemberCaseActivityObserver::class);
        CaseNote::observe(CaseNoteCaseActivityObserver::class);

        // Register CasePolicy for CaseModel (Laravel would look for CaseModelPolicy by convention)
        Gate::policy(CaseModel::class, CasePolicy::class);

        /**
         * Bypass Spatie permission checks for workspace owners and super-admins (see Spatie "Defining a Super-Admin").
         * Registered permission names are checked via Gate / $user->can().
         */
        Gate::before(function (?User $user, $ability) {
            if ($user === null || ! is_string($ability)) {
                return null;
            }

            if (in_array($user->type, ['superadmin', 'super admin', 'company'], true)) {
                return true;
            }

            if ($user->hasRole('superadmin')) {
                return true;
            }

            return null;
        });
    }
}