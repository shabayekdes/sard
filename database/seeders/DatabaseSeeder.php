<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\NotificationSender;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.is_demo')) {
            // Demo mode - run all seeders with full demo data
            $this->call([
                // Core system seeders
                PermissionSeeder::class,
                RoleSeeder::class,
                PlanSeeder::class,
                UserSeeder::class,
                CompanySeeder::class,
                CurrencySeeder::class,
                CountrySeeder::class,
                TaxRateSeeder::class,
                EmailTemplateSeeder::class,
                NotificationTemplateSeeder::class,
                LandingPageCustomPageSeeder::class,
                PaymentSettingSeeder::class,
                LoginHistorySeeder::class,

                // Client Management module seeders
                ClientSeeder::class,
                ClientBillingInfoSeeder::class,

                // Advocate module seeders
                CompanyProfileSeeder::class,
                PracticeAreaSeeder::class,

                // Client documents
                ClientDocumentSeeder::class,

                // Case Management module seeders
                TeamMemberRoleSeeder::class,

                // Court Schedule module seeders
                CourtSeeder::class,
                CaseSeeder::class,
                CaseTimelineSeeder::class,
                CaseTeamMemberSeeder::class,
                CaseNoteSeeder::class,
                CaseDocumentSeeder::class,
                HearingSeeder::class,

                // Document Management module seeders
                DocumentCategorySeeder::class,
                DocumentSeeder::class,
                DocumentVersionSeeder::class,
                DocumentCommentSeeder::class,
                DocumentPermissionSeeder::class,

                // Legal Research module seeders
                ResearchTypeSeeder::class,
                ResearchProjectSeeder::class,
                ResearchSourceSeeder::class,
                ResearchCategorySeeder::class,
                KnowledgeArticleSeeder::class,
                LegalPrecedentSeeder::class,
                ResearchNoteSeeder::class,
                ResearchCitationSeeder::class,

                // Compliance & Regulatory module seeders
                ComplianceCategorySeeder::class,
                ComplianceFrequencySeeder::class,
                ComplianceRequirementSeeder::class,
                RegulatoryBodySeeder::class,
                ProfessionalLicenseSeeder::class,
                CleTrackingSeeder::class,
                RiskCategorySeeder::class,
                RiskAssessmentSeeder::class,
                AuditTypeSeeder::class,
                ComplianceAuditSeeder::class,

                // Billing & Invoicing module seeders
                FeeTypeSeeder::class,
                TimeEntrySeeder::class,
                BillingRateSeeder::class,
                ExpenseSeeder::class,
                FeeStructureSeeder::class,
                InvoiceSeeder::class,
                PaymentSeeder::class,

                // Task & Workflow Management module seeders
                TaskTypeSeeder::class,
                TaskStatusSeeder::class,
                TaskSeeder::class,
                WorkflowSeeder::class,
                TaskCommentSeeder::class,

                MessageSeeder::class,
                ContactSeeder::class,
                CouponSeeder::class,
                PlanOrderSeeder::class,
                PlanRequestSeeder::class,
                NewsletterSeeder::class,
                ReferralSettingSeeder::class,
                ReferralSeeder::class,
            ]);
        } else {
            // Main/Production mode - run minimal seeders with basic data
            $this->call([
                // Essential system seeders
                PermissionSeeder::class,
                RoleSeeder::class,
                PlanSeeder::class,
                UserSeeder::class,
                CurrencySeeder::class,
                CountrySeeder::class,
                TaxRateSeeder::class,
                EmailTemplateSeeder::class,
                NotificationTemplateSeeder::class,
                LandingPageCustomPageSeeder::class,
                TeamMemberRoleSeeder::class,
            ]);
        }
    }
}
