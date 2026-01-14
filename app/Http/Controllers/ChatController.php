<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\CaseModel;
use App\Services\AiService;
use App\Mcp\Tools\CaseCreateTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $conversationId = $request->get('conversation_id');
        
        // Get or create conversation
        $conversation = null;
        if ($conversationId) {
            $conversation = ChatConversation::where('id', $conversationId)
                ->where('user_id', $user->id)
                ->with(['messages' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->first();
        }

        // If no conversation, create a new one
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'title' => 'New Conversation',
                'created_by' => $user->id,
            ]);
        }

        // Get user's recent conversations
        $conversations = ChatConversation::where('user_id', $user->id)
            ->with('latestMessage')
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get available cases for selector (optional)
        $cases = CaseModel::withPermissionCheck()
            ->select('id', 'case_id', 'title', 'case_number')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return Inertia::render('chat/index', [
            'conversation' => $conversation->load('messages'),
            'conversations' => $conversations,
            'cases' => $cases,
        ]);
    }

    /**
     * Send a message and get AI response
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'required|string|max:10000',
            'case_id' => 'nullable|exists:cases,id',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::where('id', $request->conversation_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Update conversation case if provided
        if ($request->has('case_id') && $request->case_id) {
            $conversation->update(['case_id' => $request->case_id]);
        }

        try {
            // Save user message
            $userMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => ChatMessage::ROLE_USER,
                'content' => $request->message,
                'created_by' => $user->id,
            ]);

            // Update conversation timestamp
            $conversation->touchLastMessage();

            // Check if user wants to create a case
            $caseCreationKeywords = ['create case', 'new case', 'file case', 'open case', 'دعوى جديدة', 'إنشاء قضية', 'فتح قضية'];
            $isCaseCreationRequest = false;
            foreach ($caseCreationKeywords as $keyword) {
                if (stripos($request->message, $keyword) !== false) {
                    $isCaseCreationRequest = true;
                    break;
                }
            }

            $aiResponse = '';
            $caseCreated = null;

            if ($isCaseCreationRequest) {
                // Use MCP Case Create Tool
                try {
                    $caseCreateTool = new CaseCreateTool();
                    $result = $caseCreateTool->execute($request->message, [
                        'user_id' => $user->id,
                    ]);

                    if ($result['success']) {
                        $caseCreated = $result['case'];
                        $aiResponse = "✅ Case created successfully!\n\n";
                        $aiResponse .= "**Case Details:**\n";
                        $aiResponse .= "- Title: {$result['case']['title']}\n";
                        $aiResponse .= "- Case ID: {$result['case']['case_id']}\n";
                        if ($result['case']['case_number']) {
                            $aiResponse .= "- Case Number: {$result['case']['case_number']}\n";
                        }
                        $aiResponse .= "- Client: " . ($result['case']['client']['name'] ?? 'N/A') . "\n";
                        $aiResponse .= "- Court: " . ($result['case']['court']['name'] ?? 'N/A') . "\n";
                        $aiResponse .= "- Case Type: " . ($result['case']['case_type']['name'] ?? 'N/A') . "\n";
                        $aiResponse .= "- Priority: " . ucfirst($result['case']['priority'] ?? 'medium') . "\n\n";
                        $aiResponse .= "The case has been created and is now available in your case management system.";
                    }
                } catch (Exception $e) {
                    Log::error("Chat Case Creation Error: " . $e->getMessage());
                    $errorMessage = $e->getMessage();
                    
                    $aiResponse = "I understand you want to create a case, but I need more information:\n\n";
                    $aiResponse .= "❌ " . $errorMessage . "\n\n";
                    
                    // Add helpful suggestions based on error
                    if (strpos($errorMessage, 'Client') !== false) {
                        $aiResponse .= "**To fix this:**\n";
                        $aiResponse .= "• Make sure to mention the client name clearly (e.g., 'for client [Name]')\n";
                        $aiResponse .= "• Use the exact client name as it appears in your system\n";
                    }
                    
                    if (strpos($errorMessage, 'Court') !== false) {
                        $aiResponse .= "**To fix this:**\n";
                        $aiResponse .= "• Mention the court name (e.g., 'File in [Court Name]' or 'in [Court Name]')\n";
                        $aiResponse .= "• Use the exact court name as it appears in your system\n";
                    }
                    
                    if (strpos($errorMessage, 'Case type') !== false) {
                        $aiResponse .= "**To fix this:**\n";
                        $aiResponse .= "• Describe the type of case (e.g., 'Contract Dispute', 'Labor Case', 'Commercial Case')\n";
                    }
                    
                    $aiResponse .= "\n**Example format:**\n";
                    $aiResponse .= "'Create a case for client [Client Name]. [Case Type] against [Opposing Party]. File in [Court Name]. [Priority] priority.'\n\n";
                    $aiResponse .= "**Tip:** Make sure client and court names match exactly what's in your database.";
                }
            } else {
                // Regular chat response
                $aiService = new AiService();
                
                // Build context from conversation history (excluding the message we just added)
                $context = $this->buildConversationContext($conversation);
                
                // If case is associated, include case context
                $caseContext = '';
                if ($conversation->case_id) {
                    $case = CaseModel::find($conversation->case_id);
                    if ($case) {
                        try {
                            $caseSummary = $aiService->summarizeCase($case, [
                                'include_timeline' => false,
                                'include_team' => false,
                            ]);
                            $caseContext = "\n\nCase Context:\n" . $caseSummary;
                        } catch (Exception $e) {
                            // If case summary fails, continue without it
                            Log::warning('Failed to generate case summary for chat', [
                                'case_id' => $case->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                // Build the full prompt with context
                $fullPrompt = $context . $caseContext . "\n\nUser Question: " . $request->message;
                
                // Use summarize text for general chat responses
                $aiResponse = $aiService->summarizeText(
                    text: $fullPrompt,
                    maxLength: 1000,
                    focus: 'legal advice and assistance'
                );
            }

            // Save assistant response
            $assistantMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => ChatMessage::ROLE_ASSISTANT,
                'content' => $aiResponse,
                'created_by' => $user->id,
                'metadata' => $caseCreated ? ['case_created' => $caseCreated] : null,
            ]);

            // Update conversation timestamp
            $conversation->touchLastMessage();

            // Return updated conversation with messages
            $conversation->refresh();
            $conversation->load('messages');

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
                'case_created' => $caseCreated,
            ]);

        } catch (Exception $e) {
            Log::error('Chat message error', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific conversation
     */
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        
        $conversation = ChatConversation::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Build conversation context from message history
     */
    private function buildConversationContext(ChatConversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        $context = "Conversation History:\n\n";
        
        foreach ($messages as $message) {
            $role = $message->role === ChatMessage::ROLE_USER ? 'User' : 'Assistant';
            $context .= "{$role}: {$message->content}\n\n";
        }

        return $context;
    }
}

