<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClientType extends BaseModel
{
    use BelongsToTenant, HasFactory, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'status',
        'tenant_id'
    ];

    /**
     * Get the company user for this tenant (for backward-compat creator display).
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    /**
     * Get the clients assigned to this type.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}