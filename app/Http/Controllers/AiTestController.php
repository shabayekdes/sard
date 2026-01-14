<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AiTestController extends Controller
{
    public function showForm()
    {
        return view('ai.summarize_test');
    }

    public function submit(Request $request, AiService $aiService)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'max_length' => 'nullable|integer|min:10|max:2000',
            'focus' => 'nullable|string',
        ]);

        try {
            $maxLength = $data['max_length'] ?? 150;
            $focus = $data['focus'] ?? null;

            $summary = $aiService->summarizeText($data['text'], (int) $maxLength, $focus);

            return view('ai.summarize_test', [
                'summary' => $summary,
                'input_text' => $data['text'],
                'max_length' => $maxLength,
                'focus' => $focus,
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['ai_error' => $e->getMessage()])->withInput();
        }
    }
}

