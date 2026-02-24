<?php

namespace Database\Seeders;

use App\Models\CaseTimeline;
use App\Models\CaseModel;
use App\Models\User;
use App\Models\EventType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTimelineSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        $timelineTemplates = [
            [
                'title' => 'Case Filed',
                'description' => 'Initial case filing completed',
                'event_date' => now()->subDays(30),
                'is_completed' => true,
            ],
            [
                'title' => 'First Hearing',
                'description' => 'Initial court hearing scheduled',
                'event_date' => now()->subDays(15),
                'is_completed' => true,
            ],
            [
                'title' => 'Document Submission',
                'description' => 'Submit required documents to court',
                'event_date' => now()->addDays(7),
                'is_completed' => false,
            ],
            [
                'title' => 'Final Hearing',
                'description' => 'Final court hearing scheduled',
                'event_date' => now()->addDays(30),
                'is_completed' => false,
            ],
        ];
        
        foreach ($companyUsers as $companyUser) {
            // Get event types for this company user
            $eventTypes = EventType::where('tenant_id', $companyUser->tenant_id)
                ->where('status', 'active')
                ->get();
            
            // Skip if no event types exist
            if ($eventTypes->isEmpty()) {
                continue;
            }
            
            $cases = CaseModel::where('tenant_id', $companyUser->tenant_id)->get();
            $allTimelineRecords = [];
            
            foreach ($cases as $case) {
                foreach ($timelineTemplates as $timelineData) {
                    // Select a random event type
                    $randomEventType = $eventTypes->random();
                    
                    $allTimelineRecords[] = [
                        ...$timelineData,
                        'event_type_id' => $randomEventType->id,
                        'case_id' => $case->id,
                        'tenant_id' => $companyUser->tenant_id,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            if (!empty($allTimelineRecords)) {
                CaseTimeline::insert($allTimelineRecords);
            }
        }
    }
}