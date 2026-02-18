<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\User;

class CasePolicy
{
    /**
     * Determine whether the user can view any cases.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the case.
     */
    public function view(User $user, CaseModel $case): bool
    {
        return $this->belongsToUserCompany($user, $case);
    }

    /**
     * Determine whether the user can create cases.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the case.
     */
    public function update(User $user, CaseModel $case): bool
    {
        return $this->belongsToUserCompany($user, $case);
    }

    /**
     * Determine whether the user can delete the case.
     */
    public function delete(User $user, CaseModel $case): bool
    {
        return $this->belongsToUserCompany($user, $case);
    }

    /**
     * Check if the case belongs to the current user's company (or is the user's client case).
     */
    protected function belongsToUserCompany(User $user, CaseModel $case): bool
    {
        if ($user->hasRole(['superadmin'])) {
            return true;
        }

        if ($user->hasRole(['company'])) {
            return in_array((int) $case->created_by, getCompanyAndUsersId(), true);
        }

        if ($user->hasRole(['team_member']) || $user->type === 'team_member') {
            return $case->teamMembers()->where('user_id', $user->id)->exists();
        }

        if ($user->hasRole(['client'])) {
            $client = \App\Models\Client::where('email', $user->email)->first();

            return $client && (int) $case->client_id === (int) $client->id;
        }

        // Users with view-cases permission (e.g. custom roles) can view cases in their company
        if ($user->can('view-cases')) {
            return in_array((int) $case->created_by, getCompanyAndUsersId(), true);
        }

        return false;
    }
}
