<?php

namespace App\Http\Controllers;

use App\Services\CaseCreationService;
use App\Models\Client;
use App\Models\CaseType;
use App\Models\CaseStatus;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Example Controller demonstrating Case Creation from Prompts
 * 
 * This shows how to use AI prompts to assist in creating cases
 * from natural language descriptions.
 */
class ExampleCaseCreationController extends Controller
{
    /**
     * Generate case information from a prompt
     */
    public function generateFromPrompt(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
            'client_id' => 'nullable|exists:clients,id',
            'court_id' => 'nullable|exists:courts,id',
            'case_type_id' => 'nullable|exists:case_types,id',
        ]);

        try {
            $service = new CaseCreationService();
            
            $context = [];
            if ($request->has('client_id')) {
                $context['client_id'] = $request->client_id;
            }
            if ($request->has('court_id')) {
                $context['court_id'] = $request->court_id;
            }
            if ($request->has('case_type_id')) {
                $context['case_type_id'] = $request->case_type_id;
            }

            $result = $service->generateFromPrompt(
                $request->input('prompt'),
                $context
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (Exception $e) {
            Log::error('Case Creation from Prompt Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate case from prompt: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a case from generated information
     */
    public function createFromPrompt(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
            'client_id' => 'required|exists:clients,id',
            'case_type_id' => 'required|exists:case_types,id',
            'case_status_id' => 'required|exists:case_statuses,id',
            'court_id' => 'required|exists:courts,id',
        ]);

        try {
            $service = new CaseCreationService();
            
            // Generate case information from prompt
            $result = $service->generateFromPrompt($request->input('prompt'), [
                'client_id' => $request->client_id,
                'court_id' => $request->court_id,
                'case_type_id' => $request->case_type_id,
            ]);

            // Create the case
            $case = $service->createCase($result['parsed'], [
                'client_id' => $request->client_id,
                'case_type_id' => $request->case_type_id,
                'case_status_id' => $request->case_status_id,
                'court_id' => $request->court_id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'case' => $case->load(['client', 'caseType', 'caseStatus', 'court']),
                'generated_info' => $result['parsed'],
            ]);

        } catch (Exception $e) {
            Log::error('Case Creation Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create case: ' . $e->getMessage(),
            ], 500);
        }
    }
}

