<?php

namespace App\Services;

use App\Models\CaseModel;
use App\Models\Client;
use App\Models\Court;
use App\Services\AiService;
use App\Services\LegalPrompts;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Case Creation Service
 * 
 * Uses AI prompts to assist in creating cases from natural language descriptions.
 * This service helps generate case details, descriptions, and structured information
 * from user prompts.
 */
class CaseCreationService
{
    private AiService $aiService;

    public function __construct(?AiService $aiService = null)
    {
        $this->aiService = $aiService ?? new AiService();
    }

    /**
     * Generate case details from a natural language prompt
     *
     * @param string $prompt User's natural language description of the case
     * @param array $context Optional context (client_id, court_id, etc.)
     * @return array Generated case information
     * @throws Exception
     */
    public function generateFromPrompt(string $prompt, array $context = []): array
    {
        if (empty(trim($prompt))) {
            throw new Exception('Prompt cannot be empty');
        }

        try {
            // Build context information
            $contextInfo = $this->buildContextInfo($context);
            
            // Create the user prompt
            $userPrompt = $this->buildCaseCreationPrompt($prompt, $contextInfo);
            
            // Use drafting prompt for structured output
            $systemPrompt = LegalPrompts::DRAFTING . "\n\n" . 
                "SPECIFIC INSTRUCTIONS FOR CASE CREATION:\n" .
                "Extract and structure the following information from the user's description:\n" .
                "• Case Title: A clear, concise title\n" .
                "• Description: Detailed case description\n" .
                "• Client: CRITICAL - Extract client name if mentioned. Look for patterns like:\n" .
                "  - 'for client [Name]'\n" .
                "  - 'client [Name]'\n" .
                "  - 'my client [Name]'\n" .
                "  - '[Name]' when mentioned as the client\n" .
                "  Extract ONLY the client name, not additional words.\n" .
                "• Court: Extract court name if mentioned. Look for patterns like:\n" .
                "  - 'in [Court Name]'\n" .
                "  - 'File in [Court Name]'\n" .
                "  - '[Court Name]'\n" .
                "  Extract the complete court name.\n" .
                "• Case Type: Suggested case type based on description (e.g., 'Contract Dispute', 'Labor Case', 'Commercial Case')\n" .
                "• Priority: Suggested priority level (low, medium, high)\n" .
                "• Key Facts: Important facts mentioned\n" .
                "• Opposing Party: If mentioned\n" .
                "• Important Dates: Any dates mentioned (filing, deadlines, etc.)\n" .
                "• Missing Information: What additional information is needed\n\n" .
                "Format your response as structured bullet points with clear labels:\n" .
                "Client: [extracted client name]\n" .
                "Court: [extracted court name]\n" .
                "Case Type: [suggested type]\n" .
                "If information is not provided, explicitly state it is missing.";

            $client = $this->aiService->getClient();
            $model = $this->aiService->getModel();

            $response = $client->chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => config('openai.legal.temperature', 0.2),
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from AI service');
            }

            // Parse the AI response into structured data
            $context['original_prompt'] = $prompt; // Store original prompt for fallback extraction
            $parsed = $this->parseCaseInformation($content, $context);

            return [
                'success' => true,
                'raw_response' => trim($content),
                'parsed' => $parsed,
                'suggestions' => $this->generateSuggestions($parsed, $context),
            ];

        } catch (Exception $e) {
            Log::error('Case Creation Service Error', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);
            throw new Exception('Failed to generate case from prompt: ' . $e->getMessage());
        }
    }

    /**
     * Build context information for the prompt
     */
    private function buildContextInfo(array $context): string
    {
        $info = [];

        if (isset($context['client_id'])) {
            $client = Client::find($context['client_id']);
            if ($client) {
                $info[] = "Client: {$client->name} (ID: {$client->id})";
            }
        }

        if (isset($context['court_id'])) {
            $court = Court::find($context['court_id']);
            if ($court) {
                $info[] = "Court: {$court->name}";
                if ($court->jurisdiction) {
                    $info[] = "Jurisdiction: {$court->jurisdiction}";
                }
            }
        }

        if (isset($context['case_type_id'])) {
            $caseType = \App\Models\CaseType::find($context['case_type_id']);
            if ($caseType) {
                $info[] = "Case Type: {$caseType->name}";
            }
        }

        return !empty($info) ? "Context:\n" . implode("\n", $info) . "\n\n" : '';
    }

    /**
     * Build the case creation prompt
     */
    private function buildCaseCreationPrompt(string $prompt, string $contextInfo): string
    {
        $fullPrompt = "Please analyze the following case description and extract structured information for case creation.\n\n";
        
        if (!empty($contextInfo)) {
            $fullPrompt .= $contextInfo;
        }
        
        $fullPrompt .= "User's Case Description:\n{$prompt}\n\n";
        $fullPrompt .= "Please extract and structure the following information:\n";
        $fullPrompt .= "• Case Title (clear, concise)\n";
        $fullPrompt .= "• Description (detailed case description)\n";
        $fullPrompt .= "• Suggested Case Type (if identifiable from description)\n";
        $fullPrompt .= "• Suggested Priority (low, medium, high based on urgency)\n";
        $fullPrompt .= "• Key Facts (bullet points of important facts)\n";
        $fullPrompt .= "• Opposing Party (if mentioned)\n";
        $fullPrompt .= "• Important Dates (filing dates, deadlines, etc. if mentioned)\n";
        $fullPrompt .= "• Missing Information (what additional details are needed)\n\n";
        $fullPrompt .= "If any information is not provided in the description, explicitly state it is missing. " .
                      "Do not invent or assume any information not explicitly mentioned.";

        return $fullPrompt;
    }

    /**
     * Parse AI response into structured data
     */
    private function parseCaseInformation(string $content, array $context): array
    {
        $parsed = [
            'title' => null,
            'description' => null,
            'suggested_case_type' => null,
            'suggested_priority' => 'medium',
            'key_facts' => [],
            'opposing_party' => null,
            'important_dates' => [],
            'missing_information' => [],
        ];

        // Extract title (look for "Case Title:" or "Title:")
        if (preg_match('/Case Title:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $parsed['title'] = trim($matches[1]);
        } elseif (preg_match('/Title:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $parsed['title'] = trim($matches[1]);
        }

        // Extract description
        if (preg_match('/Description:?\s*(.+?)(?=Key Facts|Opposing Party|Important Dates|Missing Information|$)/is', $content, $matches)) {
            $parsed['description'] = trim($matches[1]);
        }

        // Extract suggested client - try multiple patterns from AI response
        if (preg_match('/Client:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $extracted = trim($matches[1]);
            // Remove common suffixes that AI might add
            $extracted = preg_replace('/\s*(is required|is missing|not provided|not mentioned).*$/i', '', $extracted);
            if (!empty($extracted) && stripos($extracted, 'not') === false && stripos($extracted, 'missing') === false) {
                $parsed['suggested_client'] = $extracted;
            }
        }
        
        // Also try to extract directly from original prompt (more reliable)
        if (empty($parsed['suggested_client']) && isset($context['original_prompt'])) {
            $originalPrompt = $context['original_prompt'];
            
            // Pattern 1: "for client [Name]"
            if (preg_match('/for client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $originalPrompt, $matches)) {
                $parsed['suggested_client'] = trim($matches[1]);
            }
            // Pattern 2: "client [Name]"
            elseif (preg_match('/client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $originalPrompt, $matches)) {
                $parsed['suggested_client'] = trim($matches[1]);
            }
            // Pattern 3: "my client [Name]"
            elseif (preg_match('/my client\s+([A-Z][a-zA-Z\s]+?)(?:\.|,|against|in|$)/i', $originalPrompt, $matches)) {
                $parsed['suggested_client'] = trim($matches[1]);
            }
        }

        // Extract suggested court - try multiple patterns
        if (preg_match('/Court:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $parsed['suggested_court'] = trim($matches[1]);
        } elseif (preg_match('/in\s+([A-Z][a-zA-Z\s]+Court[^\.]*?)(?:\.|,|$)/i', $content, $matches)) {
            $parsed['suggested_court'] = trim($matches[1]);
        } elseif (preg_match('/file in\s+(.+?)(?:\.|,|$)/i', $content, $matches)) {
            $parsed['suggested_court'] = trim($matches[1]);
        }
        
        // Also try to extract directly from original prompt
        if (empty($parsed['suggested_court']) && isset($context['original_prompt'])) {
            $originalPrompt = $context['original_prompt'];
            if (preg_match('/in\s+([A-Z][a-zA-Z\s]+Court[^\.]*?)(?:\.|,|$)/i', $originalPrompt, $matches)) {
                $parsed['suggested_court'] = trim($matches[1]);
            } elseif (preg_match('/file in\s+(.+?)(?:\.|,|$)/i', $originalPrompt, $matches)) {
                $parsed['suggested_court'] = trim($matches[1]);
            }
        }

        // Extract suggested case type
        if (preg_match('/Case Type:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $parsed['suggested_case_type'] = trim($matches[1]);
        }

        // Extract priority
        if (preg_match('/Priority:?\s*(low|medium|high)/i', $content, $matches)) {
            $parsed['suggested_priority'] = strtolower($matches[1]);
        }

        // Extract opposing party
        if (preg_match('/Opposing Party:?\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $parsed['opposing_party'] = trim($matches[1]);
        }

        // Extract key facts (bullet points)
        if (preg_match('/Key Facts:?\s*(.+?)(?=Opposing Party|Important Dates|Missing Information|$)/is', $content, $matches)) {
            $factsText = $matches[1];
            $facts = preg_split('/[•\-\*]\s*/', $factsText);
            $parsed['key_facts'] = array_filter(array_map('trim', $facts));
        }

        // Extract important dates
        if (preg_match('/Important Dates:?\s*(.+?)(?=Missing Information|$)/is', $content, $matches)) {
            $datesText = $matches[1];
            $dates = preg_split('/[•\-\*]\s*/', $datesText);
            $parsed['important_dates'] = array_filter(array_map('trim', $dates));
        }

        // Extract missing information
        if (preg_match('/Missing Information:?\s*(.+?)$/is', $content, $matches)) {
            $missingText = $matches[1];
            $missing = preg_split('/[•\-\*]\s*/', $missingText);
            $parsed['missing_information'] = array_filter(array_map('trim', $missing));
        }

        // If title not found, try to extract from first line
        if (!$parsed['title'] && preg_match('/^(.+?)(?:\n|$)/', $content, $matches)) {
            $firstLine = trim($matches[1]);
            if (strlen($firstLine) < 200 && !str_contains($firstLine, ':')) {
                $parsed['title'] = $firstLine;
            }
        }

        // If description not found, use the full content
        if (!$parsed['description']) {
            $parsed['description'] = $content;
        }

        return $parsed;
    }

    /**
     * Generate suggestions based on parsed information
     */
    private function generateSuggestions(array $parsed, array $context): array
    {
        $suggestions = [];

        if (empty($parsed['title'])) {
            $suggestions[] = 'Case title is missing - please provide a clear title';
        }

        if (empty($parsed['description'])) {
            $suggestions[] = 'Case description is missing - please provide case details';
        }

        if (!isset($context['client_id'])) {
            $suggestions[] = 'Client selection is required';
        }

        if (!isset($context['case_type_id'])) {
            $suggestions[] = 'Case type selection is required';
        }

        if (!isset($context['court_id'])) {
            $suggestions[] = 'Court selection is required';
        }

        return $suggestions;
    }

    /**
     * Create a case from parsed information
     *
     * @param array $parsed Parsed case information
     * @param array $requiredFields Required fields (client_id, case_type_id, etc.)
     * @return CaseModel
     * @throws Exception
     */
    public function createCase(array $parsed, array $requiredFields): CaseModel
    {
        // Validate required fields
        $required = ['client_id', 'case_type_id', 'case_status_id', 'created_by'];
        foreach ($required as $field) {
            if (!isset($requiredFields[$field])) {
                throw new Exception("Required field missing: {$field}");
            }
        }

        // Build case data
        $caseData = array_merge([
            'title' => $parsed['title'] ?? 'Untitled Case',
            'description' => $parsed['description'] ?? '',
            'priority' => $parsed['suggested_priority'] ?? 'medium',
            'opposing_party' => $parsed['opposing_party'] ?? null,
            'status' => 'active',
        ], $requiredFields);

        // Create the case
        $case = CaseModel::create($caseData);

        return $case;
    }
}

