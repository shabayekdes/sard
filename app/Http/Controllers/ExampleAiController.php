<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Example Controller demonstrating AiService usage
 * 
 * This is an example file showing how to use the AiService
 * in your controllers. You can adapt these methods to your
 * actual controller structure.
 */
class ExampleAiController extends Controller
{
    /**
     * Example: Summarize text from a request
     */
    public function summarizeText(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:50000',
            'max_length' => 'sometimes|integer|min:50|max:1000',
            'focus' => 'sometimes|string|max:200',
        ]);

        try {
            $aiService = new AiService();
            
            $summary = $aiService->summarizeText(
                text: $request->input('text'),
                maxLength: $request->input('max_length', 150),
                focus: $request->input('focus')
            );

            return response()->json([
                'success' => true,
                'summary' => $summary,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to summarize text', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to summarize text: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Example: Summarize a case
     */
    public function summarizeCase(Request $request, int $caseId): JsonResponse
    {
        $request->validate([
            'include_documents' => 'sometimes|boolean',
            'include_timeline' => 'sometimes|boolean',
            'include_team' => 'sometimes|boolean',
        ]);

        try {
            $case = CaseModel::findOrFail($caseId);
            
            // Check permissions (adapt to your permission system)
            // $this->authorize('view', $case);

            $aiService = new AiService();
            
            $summary = $aiService->summarizeCase($case, [
                'include_documents' => $request->boolean('include_documents', false),
                'include_timeline' => $request->boolean('include_timeline', false),
                'include_team' => $request->boolean('include_team', false),
            ]);

            return response()->json([
                'success' => true,
                'case_id' => $case->id,
                'summary' => $summary,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to summarize case', [
                'case_id' => $caseId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to summarize case: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Example: Draft a memo
     */
    public function draftMemo(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:500',
            'context' => 'sometimes|string|max:10000',
            'format' => 'sometimes|string|in:standard,executive,detailed',
            'tone' => 'sometimes|string|in:professional,formal,concise',
            'length' => 'sometimes|string|in:short,medium,long',
            'include_recommendations' => 'sometimes|boolean',
        ]);

        try {
            $aiService = new AiService();
            
            $memo = $aiService->draftMemo(
                subject: $request->input('subject'),
                context: $request->input('context', ''),
                options: [
                    'format' => $request->input('format', 'standard'),
                    'tone' => $request->input('tone', 'professional'),
                    'length' => $request->input('length', 'medium'),
                    'include_recommendations' => $request->boolean('include_recommendations', true),
                ]
            );

            return response()->json([
                'success' => true,
                'memo' => $memo,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to draft memo', [
                'error' => $e->getMessage(),
                'subject' => $request->input('subject'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to draft memo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Example: Using custom model and temperature
     */
    public function summarizeWithCustomSettings(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string',
            'model' => 'sometimes|string',
            'temperature' => 'sometimes|numeric|min:0|max:2',
        ]);

        try {
            $aiService = new AiService();
            
            // Override default model if provided
            if ($request->has('model')) {
                $aiService->setModel($request->input('model'));
            }
            
            // Override default temperature if provided
            if ($request->has('temperature')) {
                $aiService->setTemperature((float) $request->input('temperature'));
            }
            
            $summary = $aiService->summarizeText($request->input('text'));

            return response()->json([
                'success' => true,
                'summary' => $summary,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}


