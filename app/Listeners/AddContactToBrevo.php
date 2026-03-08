<?php

namespace App\Listeners;

use App\Services\BrevoService;
use Stancl\Tenancy\Events\TenantCreated;

class AddContactToBrevo
{
    public function __construct(
        private BrevoService $brevo
    ) {
    }

    /**
     * When a company (tenant) is created, add the company contact to Brevo.
     */
    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;
        $email = $tenant->email ?? null;

        if (empty($email)) {
            return;
        }

        $this->brevo->createContact($email);
    }
}
