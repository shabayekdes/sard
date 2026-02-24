<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any invoices.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the invoice.
     * Companies see only their own invoices; team members see company invoices; clients see invoices for their client.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $this->belongsToUserCompany($user, $invoice);
    }

    /**
     * Determine whether the user can create invoices.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $this->belongsToUserCompany($user, $invoice);
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->belongsToUserCompany($user, $invoice);
    }

    /**
     * Determine whether the user can send the invoice.
     */
    public function send(User $user, Invoice $invoice): bool
    {
        return $this->belongsToUserCompany($user, $invoice);
    }

    /**
     * Check if the invoice belongs to the current user's company (or client).
     */
    protected function belongsToUserCompany(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole(['superadmin'])) {
            return true;
        }

        if ($user->hasRole(['company'])) {
            return $invoice->tenant_id && $user->tenant_id && $invoice->tenant_id === $user->tenant_id;
        }

        if ($user->hasRole(['team_member']) || $user->type === 'team_member') {
            return $invoice->tenant_id && $user->tenant_id && $invoice->tenant_id === $user->tenant_id;
        }

        if ($user->hasRole(['client'])) {
            $client = \App\Models\Client::where('email', $user->email)->first();

            return $client && (int) $invoice->client_id === (int) $client->id;
        }

        return false;
    }
}
