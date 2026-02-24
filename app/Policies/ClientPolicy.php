<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any clients.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the client.
     */
    public function view(User $user, Client $client): bool
    {
        return $this->belongsToUserCompany($user, $client);
    }

    /**
     * Determine whether the user can create clients.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the client.
     */
    public function update(User $user, Client $client): bool
    {
        return $this->belongsToUserCompany($user, $client);
    }

    /**
     * Determine whether the user can delete the client.
     */
    public function delete(User $user, Client $client): bool
    {
        return $this->belongsToUserCompany($user, $client);
    }

    /**
     * Check if the client belongs to the current user's company (or is the user's own client record).
     */
    protected function belongsToUserCompany(User $user, Client $client): bool
    {
        if ($user->hasRole(['superadmin'])) {
            return true;
        }

        if ($user->hasRole(['company'])) {
            return $client->tenant_id && $user->tenant_id && $client->tenant_id === $user->tenant_id;
        }

        if ($user->hasRole(['team_member']) || $user->type === 'team_member') {
            return $client->cases()->whereHas('teamMembers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->exists();
        }

        if ($user->hasRole(['client'])) {
            return $client->email === $user->email;
        }

        return false;
    }
}
