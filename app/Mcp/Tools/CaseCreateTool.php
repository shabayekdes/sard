<?php

namespace App\Mcp\Tools;

use App\Models\CaseModel;
use App\Models\Client;
use App\Models\CaseType;
use App\Models\CaseStatus;
use App\Models\Court;
use App\Services\CaseCreationService;
use App\Services\AiService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * MCP Tool: Case Create
 * 
 * Creates a legal case from natural language description by:
 * - Extracting structured information using AI
 * - Matching client, court, and case type from suggestions
 * - Creating the case with extracted information
 * - Returning case details and creation status
 */
class CaseCreateTool
{
    private CaseCreationService $caseCreationService;
    private AiService $aiService;

    public function __construct(?CaseCreationService $caseCreationService = null, ?AiService $aiService = null)
    {
        $this->caseCreationService = $caseCreationService ?? new CaseCreationService();
        $this->aiService = $aiService ?? new AiService();
    }

    /**
     * Execute the case creation tool
     *
     * @param string $prompt Natural language description of the case
     * @param array $options Optional parameters (client_id, court_id, case_type_id, case_status_id, user_id)
     * @return array Case creation result with case details
     * @throws Exception
     */
    public function execute(string $prompt, array $options = []): array
    {
        try {
            $userId = $options['user_id'] ?? auth()->id();
            if (!$userId) {
                throw new Exception('User ID is required for case creation');
            }

            // Generate case information from prompt
            $result = $this->caseCreationService->generateFromPrompt($prompt, []);

            $parsed = $result['parsed'];

            // Try to extract client directly from prompt if AI didn't extract it
            // Multiple patterns to catch different ways of mentioning client
            if (empty($parsed['suggested_client'])) {
                // Pattern 1: "for client [Name]"
                if (preg_match('/for client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $prompt, $matches)) {
                    $parsed['suggested_client'] = trim($matches[1]);
                }
                // Pattern 2: "client [Name]"
                elseif (preg_match('/client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $prompt, $matches)) {
                    $parsed['suggested_client'] = trim($matches[1]);
                }
                // Pattern 3: "my client [Name]"
                elseif (preg_match('/my client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $prompt, $matches)) {
                    $parsed['suggested_client'] = trim($matches[1]);
                }
                // Pattern 4: "for [Name]" (if Name looks like a person/company name)
                elseif (preg_match('/for\s+([A-Z][a-zA-Z\s]{2,30}?)(?:\.|,|against|in|$)/i', $prompt, $matches)) {
                    $potentialClient = trim($matches[1]);
                    // Check if it's not a common word
                    $commonWords = ['case', 'client', 'court', 'file', 'new', 'the'];
                    if (!in_array(strtolower($potentialClient), $commonWords)) {
                        $parsed['suggested_client'] = $potentialClient;
                    }
                }
            }
            
            // Log what was extracted for debugging
            Log::info('Case Creation - Client Extraction', [
                'original_prompt' => $prompt,
                'ai_extracted_client' => $result['parsed']['suggested_client'] ?? null,
                'final_suggested_client' => $parsed['suggested_client'] ?? null,
            ]);

            // Try to extract court directly from prompt if AI didn't extract it
            if (empty($parsed['suggested_court'])) {
                if (preg_match('/in\s+([A-Z][a-zA-Z\s]+Court[^\.]*?)(?:\.|,|$)/i', $prompt, $matches)) {
                    $parsed['suggested_court'] = trim($matches[1]);
                } elseif (preg_match('/file in\s+(.+?)(?:\.|,|$)/i', $prompt, $matches)) {
                    $parsed['suggested_court'] = trim($matches[1]);
                }
            }

            // Fallback: If no client extracted, try to find capitalized names that match clients
            if (empty($parsed['suggested_client'])) {
                $availableClients = Client::where('created_by', $userId)
                    ->where('status', 'active')
                    ->get();
                
                // Look for capitalized words/phrases in prompt that might be client names
                if (preg_match_all('/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)\b/', $prompt, $matches)) {
                    foreach ($matches[1] as $potentialName) {
                        // Skip common words
                        $skipWords = ['Create', 'Case', 'File', 'Court', 'Client', 'New', 'High', 'Medium', 'Low', 'Priority', 'Against', 'Corporation', 'Company', 'Ltd', 'Inc'];
                        if (in_array($potentialName, $skipWords)) {
                            continue;
                        }
                        
                        // Check if this matches any client
                        foreach ($availableClients as $client) {
                            $clientName = is_string($client->name) ? $client->name : ($client->name['en'] ?? $client->name['ar'] ?? '');
                            if (stripos($clientName, $potentialName) !== false || stripos($potentialName, $clientName) !== false) {
                                $parsed['suggested_client'] = $potentialName;
                                Log::info('Case Creation - Client Found via Fallback', [
                                    'extracted' => $potentialName,
                                    'matched_client' => $clientName,
                                ]);
                                break 2; // Break out of both loops
                            }
                        }
                    }
                }
            }

            // Try to match client, court, and case type from suggestions
            $clientId = $this->matchClient($parsed['suggested_client'] ?? null, $userId, $options['client_id'] ?? null);
            $courtId = $this->matchCourt($parsed['suggested_court'] ?? null, $userId, $options['court_id'] ?? null);
            $caseTypeId = $this->matchCaseType($parsed['suggested_case_type'] ?? null, $userId, $options['case_type_id'] ?? null);
            $caseStatusId = $options['case_status_id'] ?? $this->getDefaultCaseStatus($userId);

            // Validate required fields with helpful suggestions
            if (!$clientId) {
                $availableClients = Client::where('created_by', 2)
                    ->where('status', 'active')
                    ->limit(10)
                    ->get()
                    ->map(function ($c) {
                        return is_string($c->name) ? $c->name : ($c->name['en'] ?? $c->name['ar'] ?? 'Unknown');
                    })
                    ->toArray();
                
                $clientList = !empty($availableClients) 
                    ? "\n\nAvailable clients: " . implode(', ', $availableClients)
                    : '';
                
                $extractedClient = $parsed['suggested_client'] ?? null;
                $extractionInfo = $extractedClient 
                    ? "\n\nNote: I extracted '{$extractedClient}' as the client name, but couldn't find a matching client in your database. Please use one of the available client names above."
                    : "\n\nNote: I couldn't extract a client name from your message. Please include the client name clearly (e.g., 'for client [Name]').";
                
                throw new Exception(
                    'Client is required. Please specify client in your description (e.g., "for client [Client Name]").' . 
                    $clientList . $extractionInfo
                );
            }

            if (!$caseTypeId) {
                $availableCaseTypes = CaseType::where('created_by', 2)
                    ->where('status', 'active')
                    ->limit(5)
                    ->get()
                    ->map(function ($t) {
                        return is_string($t->name) ? $t->name : ($t->name['en'] ?? $t->name['ar'] ?? 'Unknown');
                    })
                    ->toArray();
                
                $typeList = !empty($availableCaseTypes)
                    ? "\n\nAvailable case types: " . implode(', ', $availableCaseTypes)
                    : '';
                
                throw new Exception(
                    'Case type is required. Please specify case type in your description (e.g., "Contract Dispute", "Labor Case").' .
                    $typeList
                );
            }

            if (!$caseStatusId) {
                throw new Exception('Case status is required. Please provide case_status_id.');
            }

            if (!$courtId) {
                $availableCourts = Court::where('created_by', 2)
                    ->where('status', 'active')
                    ->limit(10)
                    ->get()
                    ->map(function ($c) {
                        return is_string($c->name) ? $c->name : ($c->name['en'] ?? $c->name['ar'] ?? 'Unknown');
                    })
                    ->toArray();
                
                $courtList = !empty($availableCourts)
                    ? "\n\nAvailable courts: " . implode(', ', $availableCourts)
                    : '';
                
                $extractedCourt = $parsed['suggested_court'] ?? null;
                $extractionInfo = $extractedCourt
                    ? "\n\nNote: I extracted '{$extractedCourt}' as the court name, but couldn't find a matching court in your database. Please use one of the available court names above."
                    : "\n\nNote: I couldn't extract a court name from your message. Please include the court name clearly (e.g., 'File in [Court Name]').";
                
                throw new Exception(
                    'Court is required. Please specify court in your description (e.g., "File in [Court Name]").' .
                    $courtList . $extractionInfo
                );
            }

            // Create the case
            $case = $this->caseCreationService->createCase($parsed, [
                'client_id' => $clientId,
                'case_type_id' => $caseTypeId,
                'case_status_id' => $caseStatusId,
                'court_id' => $courtId,
                'created_by' => $userId,
            ]);

            // Load relationships for response
            $case->load(['client', 'caseType', 'caseStatus', 'court']);

            return [
                'success' => true,
                'case' => [
                    'id' => $case->id,
                    'case_id' => $case->case_id,
                    'case_number' => $case->case_number,
                    'title' => $case->title,
                    'description' => $case->description,
                    'client' => $case->client ? [
                        'id' => $case->client->id,
                        'name' => $case->client->name,
                    ] : null,
                    'court' => $case->court ? [
                        'id' => $case->court->id,
                        'name' => $case->court->name,
                    ] : null,
                    'case_type' => $case->caseType ? [
                        'id' => $case->caseType->id,
                        'name' => $case->caseType->name,
                    ] : null,
                    'case_status' => $case->caseStatus ? [
                        'id' => $case->caseStatus->id,
                        'name' => $case->caseStatus->name,
                    ] : null,
                    'priority' => $case->priority,
                ],
                'extracted_info' => $parsed,
                'message' => "Case created successfully: {$case->title}",
            ];

        } catch (Exception $e) {
            Log::error('MCP Case Create Tool Error', [
                'prompt' => $prompt,
                'options' => $options,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Failed to create case: ' . $e->getMessage());
        }
    }

    /**
     * Match client from suggestion or use provided ID
     */
    private function matchClient(?string $suggestedClient, int $userId, ?int $providedClientId): ?int
    {
        if ($providedClientId) {
            $client = Client::where('id', $providedClientId)
                ->where('created_by', $userId)
                ->first();
            if ($client) {
                return $client->id;
            }
        }

        if (!$suggestedClient) {
            return null;
        }

        // Clean up the suggested client name
        $suggestedClient = trim($suggestedClient);
        $suggestedClient = preg_replace('/^(for|client|the|my)\s+/i', '', $suggestedClient); // Remove common prefixes
        $suggestedClient = preg_replace('/[\.\,\;]+$/', '', $suggestedClient); // Remove trailing punctuation
        $suggestedClient = trim($suggestedClient);
        
        if (empty($suggestedClient)) {
            return null;
        }
        
        // Get all clients for fuzzy matching
        $clients = Client::where('created_by', $userId)
            ->where('status', 'active')
            ->get();

        // Try exact match first (case-insensitive, trimmed)
        foreach ($clients as $client) {
            $clientName = is_string($client->name) ? $client->name : ($client->name['en'] ?? $client->name['ar'] ?? '');
            $clientName = trim($clientName);
            if (strcasecmp($clientName, $suggestedClient) === 0) {
                Log::info('Case Creation - Client Matched (Exact)', [
                    'suggested' => $suggestedClient,
                    'matched' => $clientName,
                    'client_id' => $client->id,
                ]);
                return $client->id;
            }
        }

        // Try partial match (contains) - more lenient
        foreach ($clients as $client) {
            $clientName = is_string($client->name) ? $client->name : ($client->name['en'] ?? $client->name['ar'] ?? '');
            $clientName = trim($clientName);
            
            // Check if suggested contains client name or vice versa
            if (stripos($clientName, $suggestedClient) !== false || stripos($suggestedClient, $clientName) !== false) {
                Log::info('Case Creation - Client Matched (Partial)', [
                    'suggested' => $suggestedClient,
                    'matched' => $clientName,
                    'client_id' => $client->id,
                ]);
                return $client->id;
            }
        }

        // Try word-by-word matching (for multi-word names like "Michael Brown")
        $suggestedWords = array_filter(array_map('trim', explode(' ', $suggestedClient)));
        if (count($suggestedWords) > 0) {
            foreach ($clients as $client) {
                $clientName = is_string($client->name) ? $client->name : ($client->name['en'] ?? $client->name['ar'] ?? '');
                $clientName = trim($clientName);
                $clientWords = array_filter(array_map('trim', explode(' ', $clientName)));
                
                $matchCount = 0;
                foreach ($suggestedWords as $word) {
                    if (empty($word)) continue;
                    foreach ($clientWords as $clientWord) {
                        if (empty($clientWord)) continue;
                        // Exact word match (case-insensitive)
                        if (strcasecmp($word, $clientWord) === 0) {
                            $matchCount++;
                            break;
                        }
                        // Partial word match
                        if (stripos($clientWord, $word) !== false || stripos($word, $clientWord) !== false) {
                            $matchCount++;
                            break;
                        }
                    }
                }
                
                // If at least 50% of words match, consider it a match
                if ($matchCount > 0 && count($suggestedWords) > 0 && ($matchCount / count($suggestedWords)) >= 0.5) {
                    Log::info('Case Creation - Client Matched (Word-by-word)', [
                        'suggested' => $suggestedClient,
                        'matched' => $clientName,
                        'client_id' => $client->id,
                        'match_ratio' => $matchCount / count($suggestedWords),
                    ]);
                    return $client->id;
                }
            }
        }

        Log::warning('Case Creation - Client Not Matched', [
            'suggested' => $suggestedClient,
            'available_clients' => $clients->map(function ($c) {
                return is_string($c->name) ? $c->name : ($c->name['en'] ?? $c->name['ar'] ?? 'Unknown');
            })->toArray(),
        ]);

        return null;
    }

    /**
     * Match court from suggestion or use provided ID
     */
    private function matchCourt(?string $suggestedCourt, int $userId, ?int $providedCourtId): ?int
    {
        if ($providedCourtId) {
            $court = Court::where('id', $providedCourtId)
                ->where('created_by', $userId)
                ->first();
            if ($court) {
                return $court->id;
            }
        }

        if (!$suggestedCourt) {
            return null;
        }

        // Clean up the suggested court name
        $suggestedCourt = trim($suggestedCourt);
        $suggestedCourt = preg_replace('/^(in|file in|at|the)\s+/i', '', $suggestedCourt); // Remove common prefixes
        
        // Get all courts for fuzzy matching
        $courts = Court::where('created_by', $userId)
            ->where('status', 'active')
            ->get();

        // Try exact match first
        foreach ($courts as $court) {
            $courtName = is_string($court->name) ? $court->name : ($court->name['en'] ?? $court->name['ar'] ?? '');
            if (strcasecmp(trim($courtName), trim($suggestedCourt)) === 0) {
                return $court->id;
            }
        }

        // Try partial match (contains)
        foreach ($courts as $court) {
            $courtName = is_string($court->name) ? $court->name : ($court->name['en'] ?? $court->name['ar'] ?? '');
            if (stripos($courtName, $suggestedCourt) !== false || stripos($suggestedCourt, $courtName) !== false) {
                return $court->id;
            }
        }

        // Try word-by-word matching
        $suggestedWords = array_filter(explode(' ', $suggestedCourt));
        foreach ($courts as $court) {
            $courtName = is_string($court->name) ? $court->name : ($court->name['en'] ?? $court->name['ar'] ?? '');
            $courtWords = array_filter(explode(' ', $courtName));
            
            $matchCount = 0;
            foreach ($suggestedWords as $word) {
                foreach ($courtWords as $courtWord) {
                    if (stripos($courtWord, $word) !== false || stripos($word, $courtWord) !== false) {
                        $matchCount++;
                        break;
                    }
                }
            }
            
            // If at least 50% of words match, consider it a match
            if ($matchCount > 0 && ($matchCount / count($suggestedWords)) >= 0.5) {
                return $court->id;
            }
        }

        return null;
    }

    /**
     * Match case type from suggestion or use provided ID
     */
    private function matchCaseType(?string $suggestedCaseType, int $userId, ?int $providedCaseTypeId): ?int
    {
        if ($providedCaseTypeId) {
            $caseType = CaseType::where('id', $providedCaseTypeId)
                ->where('created_by', $userId)
                ->first();
            if ($caseType) {
                return $caseType->id;
            }
        }

        if (!$suggestedCaseType) {
            return null;
        }

        // Try to find matching case type by name (checking translations)
        $caseTypes = CaseType::where('created_by', $userId)
            ->where('status', 'active')
            ->get();

        foreach ($caseTypes as $caseType) {
            $name = is_string($caseType->name) ? $caseType->name : ($caseType->name['en'] ?? $caseType->name['ar'] ?? '');
            if (stripos($name, $suggestedCaseType) !== false || stripos($suggestedCaseType, $name) !== false) {
                return $caseType->id;
            }
        }

        return null;
    }

    /**
     * Get default case status for user
     */
    private function getDefaultCaseStatus(int $userId): ?int
    {
        $status = CaseStatus::where('created_by', $userId)
            ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->first();

        return $status?->id;
    }
}

