<?php

namespace App\Http\Controllers;

use App\Mcp\Tools\CaseSummarizeTool;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Example Controller demonstrating CaseSummarizeTool usage
 * 
 * This is an example file showing how to use the MCP Case Summarize Tool.
 * You can adapt this to your actual controller structure.
 */
class ExampleMcpCaseSummarizeController extends Controller
{
    /**
     * Example: Summarize a case using MCP tool
     */
    public function summarize(Request $request, int $caseId): JsonResponse
    {
        $request->validate([
            'force_refresh' => 'sometimes|boolean',
        ]);

        try {
            // Check if case exists and user has access
            $case = CaseModel::findOrFail($caseId);
            
            // Check permissions (adapt to your permission system)
            // $this->authorize('view', $case);

            // Initialize and execute the tool
            $tool = new CaseSummarizeTool();
            $result = $tool->execute($caseId, $request->boolean('force_refresh', false));

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (Exception $e) {
            Log::error('MCP Case Summarize Error', [
                'case_id' => $caseId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to summarize case: ' . $e->getMessage(),
            ], 500);
        }
    }
}


