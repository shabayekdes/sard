<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'description'];
    protected $fillable = [
        'name',
        'price',
        'yearly_price',
        'billing_cycle',
        'duration',
        'description',
        'max_users',
        'max_cases',
        'max_clients',
        'enable_branding',
        'enable_chatgpt',
        'storage_limit',
        'is_trial',
        'trial_day',
        'is_plan_enable',
        'is_default',
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'price' => 'float',
        'yearly_price' => 'float',
    ];
    
    /**
     * Get the default plan
     *
     * @return Plan|null
     */
    public static function getDefaultPlan()
    {
        return self::where('is_default', true)->first();
    }
    
    /**
     * Check if the plan is the default plan
     *
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }
    
    /**
     * Get the price based on billing cycle
     *
     * @param string $cycle 'monthly' or 'yearly'
     * @return float
     */
    public function getPriceForCycle($cycle = 'monthly')
    {
        if ($cycle === 'yearly' && $this->yearly_price !== null) {
            return $this->yearly_price;
        }
        
        return $this->price;
    }

    /**
     * Check if a limit value represents unlimited.
     *
     * @param mixed $value
     * @return bool
     */
    public function isUnlimitedLimit($value)
    {
        return (int) $value === -1;
    }

    /**
     * Determine if the plan supports a billing cycle.
     *
     * @param string $cycle
     * @return bool
     */
    public function supportsBillingCycle($cycle)
    {
        $billingCycle = $this->billing_cycle ?: 'both';

        return $billingCycle === 'both' || $billingCycle === $cycle;
    }
    
    /**
     * Get users subscribed to this plan
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}