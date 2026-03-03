<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            // Create 2-3 workflows per company
            $workflowCount = rand(8, 10);
            $availableWorkflows = [
                [
                    'name' => 'Case Preparation Workflow',
                    'description' => 'Standard workflow for preparing legal cases',
                    'trigger_event' => 'case_created',
                    'status' => 'active',
                ],
                [
                    'name' => 'Contract Review Process',
                    'description' => 'Workflow for reviewing client contracts',
                    'trigger_event' => 'document_uploaded',
                    'status' => 'active',
                ],
                [
                    'name' => 'Court Filing Workflow',
                    'description' => 'Process for court document filing',
                    'trigger_event' => 'hearing_scheduled',
                    'status' => 'active',
                ],
                [
                    'name' => 'Client Onboarding Process',
                    'description' => 'Automated workflow for new client onboarding',
                    'trigger_event' => 'client_created',
                    'status' => 'active',
                ],
                [
                    'name' => 'Invoice Generation Workflow',
                    'description' => 'Automated invoice generation and sending process',
                    'trigger_event' => 'time_entry_approved',
                    'status' => 'active',
                ],
                [
                    'name' => 'Document Review Workflow',
                    'description' => 'Systematic document review and approval process',
                    'trigger_event' => 'document_created',
                    'status' => 'active',
                ],
                [
                    'name' => 'Task Assignment Workflow',
                    'description' => 'Automated task assignment and tracking process',
                    'trigger_event' => 'task_created',
                    'status' => 'active',
                ],
                [
                    'name' => 'Deadline Reminder Workflow',
                    'description' => 'Automated deadline and reminder notification system',
                    'trigger_event' => 'deadline_approaching',
                    'status' => 'active',
                ],
                [
                    'name' => 'Compliance Tracking Workflow',
                    'description' => 'Automated compliance monitoring and reporting process',
                    'trigger_event' => 'compliance_due',
                    'status' => 'active',
                ],
                [
                    'name' => 'Research Project Workflow',
                    'description' => 'Structured legal research project management process',
                    'trigger_event' => 'research_initiated',
                    'status' => 'active',
                ],
                [
                    'name' => 'Payment Processing Workflow',
                    'description' => 'Automated payment processing and reconciliation workflow',
                    'trigger_event' => 'payment_received',
                    'status' => 'active',
                ],
            ];
            
            // Randomly select workflows for this company
            $selectedWorkflows = collect($availableWorkflows)->random($workflowCount);
            
            foreach ($selectedWorkflows as $workflowData) {
                Workflow::firstOrCreate([
                    'name' => $workflowData['name'],
                    'tenant_id' => $companyUser->tenant_id
                ], [
                    'description' => $workflowData['description'],
                    'trigger_event' => $workflowData['trigger_event'],
                    'status' => $workflowData['status'],
                    'tenant_id' => $companyUser->tenant_id,
                ]);
            }
        }
    }
}