<?php

namespace App\Jobs;

use App\Models\ClientType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Job to create company roles and default data
 */
class SeedCompanyRoles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $companyUserId
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyUser = User::find($this->companyUserId);
        
        if (!$companyUser || $companyUser->type !== 'company') {
            Log::warning("SeedCompanyRoles: Company user not found or invalid", [
                'user_id' => $this->companyUserId
            ]);
            return;
        }

        // Create client role
        $clientRole = Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => 'web',
            'created_by' => $companyUser->id
        ], [
            'label' => 'Client',
            'description' => 'Client with limited access to their cases and documents'
        ]);

        $clientRole->syncPermissions([
            // Dashboard
            'manage-dashboard',

            // Calender
            'manage-calendar',
            'view-calendar',

            // Cases
            'manage-cases',
            'view-cases',

            // Case Documents
            'manage-case-documents',
            'view-case-documents',
            'download-case-documents',

            // Case Notes
            'manage-case-notes',
            'view-case-notes',

            // Case Timelines
            'manage-case-timelines',
            'view-case-timelines',

            // Expenses
            'manage-expenses',
            'view-expenses',

            // Client Documents
            'manage-client-documents',
            'view-client-documents',
            'download-client-documents',

            // Client Billing
            'manage-client-billing',
            'view-client-billing',

            // Hearings
            'manage-hearings',
            'view-hearings',

            // Documents
            'manage-documents',
            'view-documents',
            'download-documents',

            // Document Comments (limited access)
            'manage-document-comments',
            'view-document-comments',
            'create-document-comments',

            // Messages
            'manage-messages',
            'view-messages',
            'send-messages',

            // Invoices
            'manage-invoices',
            'view-invoices',

            // Payments
            'manage-payments',
            'view-payments',
            'create-payments',

            // Time Entries
            'manage-time-entries',
            'view-time-entries',

            // Knowledge Articles
            'manage-knowledge-articles',
            'view-knowledge-articles',

            // Legal Precedents
            'manage-legal-precedents',
            'view-legal-precedents'
        ]);

        // Create team_member role with permissions
        $teamRole = Role::firstOrCreate([
            'name' => 'team_member',
            'guard_name' => 'web',
            'created_by' => $companyUser->id
        ], [
            'label' => 'Team Member',
            'description' => 'Team member with limited access to company modules'
        ]);

        $teamPermissions = [
            // Dashboard
            'manage-dashboard',

            // Calender
            'manage-calendar',
            'view-calendar',
            'manage-own-calendar',

            // Tasks
            'manage-tasks',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'assign-tasks',
            'toggle-status-tasks',

            // Time Entries
            'manage-time-entries',
            'view-time-entries',
            'create-time-entries',
            'edit-time-entries',
            'start-timer',
            'stop-timer',

            // Cases
            'manage-cases',
            'view-cases',
            'create-cases',
            'edit-cases',
            'toggle-status-cases',

            // Case Documents
            'manage-case-documents',
            'view-case-documents',
            'create-case-documents',
            'edit-case-documents',
            'download-case-documents',

            // Case Notes
            'manage-case-notes',
            'view-case-notes',
            'create-case-notes',
            'edit-case-notes',

            // Case Timelines
            'manage-case-timelines',
            'view-case-timelines',
            'create-case-timelines',
            'edit-case-timelines',

            // Clients
            'manage-clients',
            'view-clients',
            'create-clients',
            'edit-clients',

            // Documents
            'manage-documents',
            'view-documents',
            'create-documents',
            'edit-documents',
            'download-documents',

            // Document Comments (limited access)
            'manage-document-comments',
            'view-document-comments',
            'create-document-comments',
            'edit-document-comments',

            // Document Permissions (view only)
            'manage-document-permissions',
            'view-document-permissions',

            // Hearings
            'manage-hearings',
            'view-hearings',
            'create-hearings',
            'edit-hearings',

            'manage-media',
            'manage-own-media',
            // Research
            'manage-research-projects',
            'view-research-projects',
            'create-research-projects',
            'edit-research-projects',
            'manage-knowledge-articles',
            'view-knowledge-articles',
            'create-knowledge-articles',
            'edit-knowledge-articles',
            'manage-legal-precedents',
            'view-legal-precedents',
            'create-legal-precedents',
            'edit-legal-precedents',

            // Messages
            'manage-messages',
            'view-messages',
            'send-messages'
        ];

        $teamRole->syncPermissions($teamPermissions);

        // Create default team member user
        $teamMember = User::firstOrCreate([
            'email' => 'teammember' . $companyUser->id . '@company.com',
            'created_by' => $companyUser->id
        ], [
            'name' => 'John Doe',
            'password' => Hash::make('password'),
            'type' => 'team_member',
            'lang' => $companyUser->lang ?? 'en',
            'status' => 'active',
            'referral_code' => 0
        ]);

        $teamMember->roles()->sync([$teamRole->id]);

        // Create client billing currencies
        // $companyUser->clientBillingCurrencies()->createMany(config('currencies.available_currencies', []));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCompanyRoles: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

