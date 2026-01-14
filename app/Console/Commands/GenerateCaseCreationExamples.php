<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Court;
use App\Models\CaseType;
use App\Models\CaseStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateCaseCreationExamples extends Command
{
    protected $signature = 'cases:generate-examples {--limit=5 : Number of examples to generate} {--user-id= : Specific user ID to filter data}';
    protected $description = 'Generate case creation examples using real data from database';

    public function handle()
    {
        $limit = (int) $this->option('limit') ?? 5;
        $userId = $this->option('user-id');
        
        $this->info('Generating case creation examples from real database data...');
        $this->newLine();

        // Build query for clients
        $clientsQuery = Client::where('status', 'active')
            ->whereNotNull('name')
            ->where('name', '!=', '');
        
        if ($userId) {
            $clientsQuery->where('created_by', $userId);
        }
        
        $clients = $clientsQuery->limit(10)->get(['id', 'name', 'client_type_id']);

        // Build query for courts
        $courtsQuery = Court::where('status', 'active')
            ->whereNotNull('name')
            ->where('name', '!=', '');
        
        if ($userId) {
            $courtsQuery->where('created_by', $userId);
        }
        
        $courts = $courtsQuery->limit(10)->get(['id', 'name']);

        // Build query for case types
        $caseTypesQuery = CaseType::where('status', 'active')
            ->whereNotNull('name');
        
        if ($userId) {
            $caseTypesQuery->where('created_by', $userId);
        }
        
        $caseTypes = $caseTypesQuery->limit(10)->get(['id', 'name']);

        if ($clients->isEmpty()) {
            $this->error('No active clients found in database. Please create some clients first.');
            return 1;
        }

        if ($courts->isEmpty()) {
            $this->error('No active courts found in database. Please create some courts first.');
            return 1;
        }

        if ($caseTypes->isEmpty()) {
            $this->error('No active case types found in database. Please create some case types first.');
            return 1;
        }

        $this->info("Found {$clients->count()} clients, {$courts->count()} courts, {$caseTypes->count()} case types");
        $this->newLine();

        $examples = [];
        $caseTypeNames = $caseTypes->pluck('name')->toArray();
        
        // Generate examples
        for ($i = 0; $i < $limit; $i++) {
            $client = $clients->random();
            $court = $courts->random();
            $caseType = $caseTypes->random();
            
            // Get client name (handle translations)
            $clientName = is_string($client->name) ? $client->name : ($client->name['en'] ?? $client->name['ar'] ?? 'Client');
            
            // Get court name (handle translations)
            $courtName = is_string($court->name) ? $court->name : ($court->name['en'] ?? $court->name['ar'] ?? 'Court');
            
            // Get case type name (handle translations)
            $caseTypeName = is_string($caseType->name) ? $caseType->name : ($caseType->name['en'] ?? $caseType->name['ar'] ?? 'Case Type');
            
            // Generate example based on case type
            $example = $this->generateExample($clientName, $courtName, $caseTypeName);
            
            $examples[] = [
                'client' => $clientName,
                'court' => $courtName,
                'case_type' => $caseTypeName,
                'example' => $example,
            ];
        }

        // Display examples
        $this->info('=== CASE CREATION EXAMPLES (English) ===');
        $this->newLine();
        
        foreach ($examples as $index => $example) {
            $this->line("Example " . ($index + 1) . ":");
            $this->line("Client: {$example['client']}");
            $this->line("Court: {$example['court']}");
            $this->line("Case Type: {$example['case_type']}");
            $this->line("Prompt:");
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line($example['example']);
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();
        }

        // Save to file
        $filePath = storage_path('app/case_creation_examples.txt');
        $content = "CASE CREATION EXAMPLES FROM REAL DATABASE DATA\n";
        $content .= "Generated: " . now()->toDateTimeString() . "\n\n";
        $content .= "Database Stats:\n";
        $content .= "- Clients: {$clients->count()}\n";
        $content .= "- Courts: {$courts->count()}\n";
        $content .= "- Case Types: {$caseTypes->count()}\n\n";
        $content .= str_repeat("=", 80) . "\n\n";

        foreach ($examples as $index => $example) {
            $content .= "EXAMPLE " . ($index + 1) . "\n";
            $content .= str_repeat("-", 80) . "\n";
            $content .= "Client: {$example['client']}\n";
            $content .= "Court: {$example['court']}\n";
            $content .= "Case Type: {$example['case_type']}\n";
            $content .= "\nPrompt:\n";
            $content .= $example['example'] . "\n\n";
            $content .= str_repeat("=", 80) . "\n\n";
        }

        file_put_contents($filePath, $content);
        $this->info("Examples saved to: {$filePath}");

        return 0;
    }

    private function generateExample(string $clientName, string $courtName, string $caseTypeName): string
    {
        $examples = [
            // Contract Dispute
            "Create a new case for client {$clientName}. Contract dispute case against ABC Corporation. 
We signed a service agreement on " . date('F j, Y', strtotime('-2 months')) . ". 
The contract value was 150,000 SAR and payment was due on " . date('F j, Y', strtotime('-1 month')) . ", 
but ABC Corporation has not paid. We need to file this in {$courtName}. 
This is high priority as we need to recover the funds quickly.",

            // Labor Case
            "Create a case for client {$clientName}. Labor dispute case. 
Employee was terminated from Tech Solutions Inc. on " . date('F j, Y', strtotime('-1 month')) . " 
without proper notice or severance. He worked there for 3 years. 
We need to file a wrongful termination claim in {$courtName}. 
This is medium priority. The case involves unpaid overtime and benefits.",

            // Commercial Case
            "New case for client {$clientName}. Commercial dispute with Global Imports Ltd 
regarding a shipment of goods that arrived damaged on " . date('F j, Y', strtotime('-3 weeks')) . ". 
The shipment value was 250,000 SAR. We need to file in {$courtName}. 
High priority due to financial impact. The goods were supposed to be delivered on " . date('F j, Y', strtotime('-2 months')) . ".",

            // Real Estate
            "Create a case for client {$clientName}. Real estate dispute. 
Client purchased a property from Property Developers LLC on " . date('F j, Y', strtotime('-3 months')) . ". 
The property has structural defects that were not disclosed. Purchase price was 2.5 million SAR. 
We need to file in {$courtName}. Medium priority. 
The defects were discovered on " . date('F j, Y', strtotime('-1 week')) . ".",

            // General
            "I need to file a case for my client {$clientName}. It's a {$caseTypeName} case 
against XYZ Corporation. The contract was signed on " . date('F j, Y', strtotime('-4 months')) . ". 
Payment of 100,000 SAR is overdue. File in {$courtName}. High priority.",
        ];

        // Select example based on case type keywords
        $selectedExample = $examples[array_rand($examples)];
        
        // Replace generic case type with actual one if it matches
        if (stripos($caseTypeName, 'contract') !== false || stripos($caseTypeName, 'عقد') !== false) {
            $selectedExample = $examples[0];
        } elseif (stripos($caseTypeName, 'labor') !== false || stripos($caseTypeName, 'عمل') !== false) {
            $selectedExample = $examples[1];
        } elseif (stripos($caseTypeName, 'commercial') !== false || stripos($caseTypeName, 'تجاري') !== false) {
            $selectedExample = $examples[2];
        } elseif (stripos($caseTypeName, 'real estate') !== false || stripos($caseTypeName, 'عقار') !== false) {
            $selectedExample = $examples[3];
        }

        return $selectedExample;
    }
}

