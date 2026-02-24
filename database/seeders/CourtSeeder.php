<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\CourtType;
use App\Models\CircleType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get company users
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            // Get court types for this specific company
            $courtTypes = CourtType::where('tenant_id', $companyUser->tenant_id)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
            if (empty($courtTypes)) {
                continue; // Skip if no court types available for this company
            }
            
            // Get circle types for this specific company
            $circleTypes = CircleType::where('tenant_id', $companyUser->tenant_id)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
            if (empty($circleTypes)) {
                continue; // Skip if no circle types available for this company
            }
            
            // Create 2-3 courts per company
            $courtCount = rand(8, 10);
            $courtNames = [
                'District Court Central',
                'Family Court North', 
                'Commercial Court Plaza',
                'Criminal Court East',
                'Appeals Court West',
                'Municipal Court South',
                'Superior Court Main',
                'Juvenile Court Center'
            ];

            $facilitiesOptions = [
                ['parking', 'wheelchair_access', 'security', 'cafeteria'],
                ['child_care', 'mediation_rooms', 'parking', 'wheelchair_access'],
                ['video_conferencing', 'presentation_equipment', 'parking', 'security'],
                ['holding_cells', 'security', 'victim_services', 'parking'],
                ['law_library', 'research_facilities', 'parking', 'security']
            ];
            
            for ($i = 1; $i <= $courtCount; $i++) {
                $courtData = [
                    'name' => $courtNames[($companyUser->id + $i - 1) % count($courtNames)] . ' #' . $i,
                    'address' => ($i * 100) . ' Justice Avenue, District ' . $i . ', NY 1000' . $i,
                    'court_type_id' => $courtTypes[($i - 1) % count($courtTypes)],
                    'circle_type_id' => $circleTypes[($i - 1) % count($circleTypes)],
                    'status' => rand(1, 10) > 9 ? 'inactive' : 'active', // 10% chance inactive
                    'facilities' => $facilitiesOptions[($i - 1) % count($facilitiesOptions)],
                    'notes' => 'Court #' . $i . ' for ' . $companyUser->name . '. Handles various legal matters with modern facilities and procedures.',
                    'tenant_id' => $companyUser->tenant_id,
                ];
                
                Court::firstOrCreate([
                    'name' => $courtData['name'],
                    'tenant_id' => $companyUser->tenant_id
                ], $courtData);
            }
        }
    }
}