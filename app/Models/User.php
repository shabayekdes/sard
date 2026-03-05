<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lab404\Impersonate\Models\Impersonate;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Referral;
use App\Models\PayoutRequest;
use App\Models\Tenant;
use App\Traits\AutoApplyPermissionCheck;

class User extends BaseAuthenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable, AutoApplyPermissionCheck;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'type',
        'avatar',
        'lang',
        'delete_status',
        'is_enable_login',
        'mode',
        'tenant_id',
        'referral_code',
        'used_referral_code',
        'google2fa_enable',
        'google2fa_secret',
        'status',
        'active_module',
        'commission_amount'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_enable_login' => 'integer',
            'google2fa_enable' => 'integer',
        ];
    }

    /**
     * Tenant (company) - plan/city live on tenant for company users.
     */
    public function tenantRelation(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Tenant for company users (plan/city live on tenant). Use tenantRelation->plan_id, etc.
     */
    public function getTenantForPlan(): ?Tenant
    {
        if ($this->type === 'company' && $this->tenant_id) {
            return $this->tenantRelation;
        }
        return null;
    }

    /**
     * Plan instance: for company users read from tenant.
     */
    public function getPlanAttribute()
    {
        $tenant = $this->getTenantForPlan();
        return $tenant?->plan;
    }

    /**
     * Get the creator ID based on user type (tenant_id for tenant-scoped users, can be null for SAAS)
     */
    public function creatorId()
    {
        if ($this->type == 'superadmin' || $this->type == 'super admin' || $this->type == 'admin') {
            return $this->id;
        }
        return $this->tenant_id;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->type === 'superadmin' || $this->type === 'super admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    // Businesses relationship removed

    /**
     * Get the company user (creator) for this user's tenant.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id')->where('type', 'company');
    }

    /**
     * Get all plan orders for the user.
     */
    public function planOrders(): HasMany
    {
        return $this->hasMany(PlanOrder::class, 'user_id');
    }

    /**
     * Get the latest plan order for the user.
     */
    public function latestPlanOrder(): HasOne
    {
        return $this->hasOne(PlanOrder::class, 'user_id')->latestOfMany('ordered_at');
    }

    /**
     * Check if user is on free plan
     */
    public function isOnFreePlan()
    {
        return $this->plan && $this->plan->is_default;
    }

    /**
     * Get current plan or default plan
     */
    public function getCurrentPlan()
    {
        $plan = $this->plan;
        if ($plan) {
            return $plan;
        }

        return Plan::getDefaultPlan();
    }

    /**
     * Check if user has an active plan subscription (reads from tenant for company users).
     */
    public function hasActivePlan()
    {
        $t = $this->getTenantForPlan();
        if (!$t) {
            return false;
        }
        return $t->plan_id
            && $t->plan_is_active
            && ($t->plan_expire_date === null || $t->plan_expire_date > now());
    }

    /**
     * Check if user's plan has expired (reads from tenant for company users).
     */
    public function isPlanExpired()
    {
        $t = $this->getTenantForPlan();
        return $t && $t->plan_expire_date && $t->plan_expire_date < now();
    }

    /**
     * Check if user's trial has expired (reads from tenant for company users).
     */
    public function isTrialExpired()
    {
        $t = $this->getTenantForPlan();
        return $t && $t->is_trial && $t->trial_expire_date && $t->trial_expire_date < now();
    }

    /**
     * Check if user needs to subscribe to a plan (reads from tenant for company users).
     */
    public function needsPlanSubscription()
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($this->type !== 'company') {
            return false;
        }

        $t = $this->getTenantForPlan();
        if (!$t) {
            return !Plan::getDefaultPlan();
        }

        if (!$t->plan_id) {
            return !Plan::getDefaultPlan();
        }

        if ($this->isTrialExpired()) {
            return true;
        }

        if (!$t->is_trial && $this->isPlanExpired()) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated()
    {
        return $this->type === 'company';
    }

    /**
     * Check if user can impersonate others
     */
    public function canImpersonate()
    {
        return $this->isSuperAdmin();
    }

    /**
     * Get referrals made by this company
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'company_id');
    }

    /**
     * Get payout requests made by this company
     */
    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'company_id');
    }

    /**
     * Get referral balance for company
     */
    public function getReferralBalance()
    {
        $totalEarned = $this->referrals()->sum('amount');
        $totalRequested = $this->payoutRequests()->whereIn('status', ['pending', 'approved'])->sum('amount');
        return $totalEarned - $totalRequested;
    }

    /**
     * Send the email verification notification with dynamic config.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Send the password reset notification with dynamic config.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->type === 'company' && !$user->referral_code) {
                do {
                    $code = rand(100000, 999999);
                } while (User::where('referral_code', $code)->exists());
                $user->referral_code = $code;
                $user->save();
            }
        });

    }

}
