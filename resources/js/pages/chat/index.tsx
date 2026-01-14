import React, { useState, useEffect, useRef } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { PageTemplate } from '@/components/page-template';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Send, Loader2, MessageSquare, Briefcase } from 'lucide-react';
import { toast } from 'sonner';
import { cn } from '@/lib/utils';

interface ChatMessage {
  id: number;
  role: 'user' | 'assistant' | 'system';
  content: string;
  created_at: string;
  metadata?: {
    case_created?: {
      id: number;
      case_id: string;
      case_number?: string;
      title: string;
      client?: { id: number; name: string };
      court?: { id: number; name: string };
      case_type?: { id: number; name: string };
      priority: string;
    };
  };
}

interface ChatConversation {
  id: number;
  title: string | null;
  case_id: number | null;
  messages: ChatMessage[];
  last_message_at: string | null;
}

interface Case {
  id: number;
  case_id: string;
  title: string;
  case_number: string | null;
}

interface PageProps {
  conversation: ChatConversation;
  conversations: ChatConversation[];
  cases: Case[];
}

export default function ChatIndex() {
  const { conversation: initialConversation, conversations: initialConversations, cases } = usePage<PageProps>().props;
  
  const [conversation, setConversation] = useState<ChatConversation>(initialConversation);
  const [conversations, setConversations] = useState<ChatConversation[]>(initialConversations);
  const [message, setMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [selectedCaseId, setSelectedCaseId] = useState<number | null>(conversation.case_id);
  
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const textareaRef = useRef<HTMLTextAreaElement>(null);

  // Auto-scroll to bottom when new messages arrive
  useEffect(() => {
    scrollToBottom();
  }, [conversation.messages]);

  // Auto-resize textarea
  useEffect(() => {
    if (textareaRef.current) {
      textareaRef.current.style.height = 'auto';
      textareaRef.current.style.height = `${Math.min(textareaRef.current.scrollHeight, 200)}px`;
    }
  }, [message]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const handleSendMessage = async (e?: React.FormEvent) => {
    e?.preventDefault();
    
    if (!message.trim() || isLoading) {
      return;
    }

    const userMessage = message.trim();
    setMessage('');
    setIsLoading(true);

    // Optimistically add user message
    const tempUserMessage: ChatMessage = {
      id: Date.now(), // Temporary ID
      role: 'user',
      content: userMessage,
      created_at: new Date().toISOString(),
    };

    setConversation(prev => ({
      ...prev,
      messages: [...prev.messages, tempUserMessage],
    }));

    try {
      const response = await fetch(route('chat.store'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          conversation_id: conversation.id,
          message: userMessage,
          case_id: selectedCaseId,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to send message');
      }

      if (data.success) {
        // Update conversation with real messages from server
        setConversation(data.conversation);
        
        // Show success message if case was created
        if (data.case_created) {
          toast.success(`Case created: ${data.case_created.title}`);
        }
        
        // Update conversations list
        setConversations(prev => {
          const updated = prev.map(conv => 
            conv.id === data.conversation.id ? data.conversation : conv
          );
          
          // If conversation not in list, add it
          if (!updated.find(c => c.id === data.conversation.id)) {
            return [data.conversation, ...updated];
          }
          
          // Sort by last_message_at
          return updated.sort((a, b) => {
            const aTime = a.last_message_at ? new Date(a.last_message_at).getTime() : 0;
            const bTime = b.last_message_at ? new Date(b.last_message_at).getTime() : 0;
            return bTime - aTime;
          });
        });
      }
    } catch (error) {
      console.error('Error sending message:', error);
      toast.error(error instanceof Error ? error.message : 'Failed to send message');
      
      // Remove optimistic message on error
      setConversation(prev => ({
        ...prev,
        messages: prev.messages.filter(msg => msg.id !== tempUserMessage.id),
      }));
    } finally {
      setIsLoading(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
  };

  const handleConversationSelect = (conversationId: number) => {
    router.get(route('chat.index'), { conversation_id: conversationId }, {
      preserveState: false,
    });
  };

  const formatMessageTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    
    return date.toLocaleDateString();
  };

  return (
    <>
      <Head title="AI Chat" />
      <PageTemplate
        title="AI Legal Assistant"
        description="Chat with AI assistant for legal advice and case analysis"
      >
        <div className="flex h-[calc(100vh-12rem)] border rounded-lg overflow-hidden">
          {/* Sidebar - Conversations List */}
          <div className="w-64 border-r bg-muted/30 flex flex-col">
            <div className="p-4 border-b">
              <h3 className="font-semibold text-sm">Conversations</h3>
            </div>
            <ScrollArea className="flex-1">
              <div className="p-2 space-y-1">
                {conversations.map((conv) => (
                  <button
                    key={conv.id}
                    onClick={() => handleConversationSelect(conv.id)}
                    className={cn(
                      "w-full text-left p-3 rounded-md text-sm transition-colors",
                      "hover:bg-accent",
                      conversation.id === conv.id && "bg-accent font-medium"
                    )}
                  >
                    <div className="truncate">
                      {conv.title || `Conversation ${conv.id}`}
                    </div>
                    {conv.last_message_at && (
                      <div className="text-xs text-muted-foreground mt-1">
                        {formatMessageTime(conv.last_message_at)}
                      </div>
                    )}
                  </button>
                ))}
              </div>
            </ScrollArea>
          </div>

          {/* Main Chat Area */}
          <div className="flex-1 flex flex-col">
            {/* Header */}
            <div className="p-4 border-b flex items-center justify-between">
              <div className="flex items-center gap-2">
                <MessageSquare className="h-5 w-5" />
                <h2 className="font-semibold">
                  {conversation.title || 'New Conversation'}
                </h2>
              </div>
              
              {/* Case Selector */}
              <div className="flex items-center gap-2">
                <Briefcase className="h-4 w-4 text-muted-foreground" />
                <Select
                  value={selectedCaseId?.toString() || undefined}
                  onValueChange={(value) => setSelectedCaseId(value ? parseInt(value) : null)}
                >
                  <SelectTrigger className="w-48">
                    <SelectValue placeholder="Select case (optional)" />
                  </SelectTrigger>
                  <SelectContent>
                    {cases.map((caseItem) => (
                      <SelectItem key={caseItem.id} value={caseItem.id.toString()}>
                        {caseItem.case_number || caseItem.case_id} - {caseItem.title}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>

            {/* Messages Area */}
            <ScrollArea className="flex-1 p-4">
              <div className="space-y-4">
                {conversation.messages.length === 0 ? (
                  <div className="flex items-center justify-center h-full text-muted-foreground">
                    <div className="text-center">
                      <MessageSquare className="h-12 w-12 mx-auto mb-4 opacity-50" />
                      <p>Start a conversation with the AI assistant</p>
                      <p className="text-sm mt-2">Ask questions about your cases, request summaries, or draft memos</p>
                    </div>
                  </div>
                ) : (
                  conversation.messages.map((msg) => (
                    <div
                      key={msg.id}
                      className={cn(
                        "flex gap-3",
                        msg.role === 'user' ? 'justify-end' : 'justify-start'
                      )}
                    >
                      {msg.role === 'assistant' && (
                        <div className="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                          <MessageSquare className="h-4 w-4 text-primary" />
                        </div>
                      )}
                      
                      <div
                        className={cn(
                          "max-w-[80%] rounded-lg px-4 py-2",
                          msg.role === 'user'
                            ? "bg-primary text-primary-foreground"
                            : "bg-muted"
                        )}
                      >
                        <div className="whitespace-pre-wrap break-words">{msg.content}</div>
                        
                        {/* Show case creation info if available */}
                        {msg.metadata?.case_created && (
                          <div className="mt-3 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded text-sm">
                            <p className="font-medium text-green-800 dark:text-green-200 mb-1">
                              ✓ Case Created Successfully
                            </p>
                            <p className="text-green-700 dark:text-green-300">
                              <strong>Case ID:</strong> {msg.metadata.case_created.case_id}
                              {msg.metadata.case_created.case_number && (
                                <> | <strong>Case Number:</strong> {msg.metadata.case_created.case_number}</>
                              )}
                            </p>
                            <p className="text-green-700 dark:text-green-300">
                              <strong>Title:</strong> {msg.metadata.case_created.title}
                            </p>
                            {msg.metadata.case_created.client && (
                              <p className="text-green-700 dark:text-green-300">
                                <strong>Client:</strong> {msg.metadata.case_created.client.name}
                              </p>
                            )}
                            <button
                              onClick={() => router.visit(`/cases/${msg.metadata.case_created.id}`)}
                              className="mt-2 text-xs text-green-700 dark:text-green-300 hover:underline"
                            >
                              View Case →
                            </button>
                          </div>
                        )}
                        
                        <div
                          className={cn(
                            "text-xs mt-1",
                            msg.role === 'user'
                              ? "text-primary-foreground/70"
                              : "text-muted-foreground"
                          )}
                        >
                          {formatMessageTime(msg.created_at)}
                        </div>
                      </div>

                      {msg.role === 'user' && (
                        <div className="h-8 w-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0 text-primary-foreground text-sm font-medium">
                          {msg.role === 'user' ? 'U' : 'A'}
                        </div>
                      )}
                    </div>
                  ))
                )}
                
                {isLoading && (
                  <div className="flex gap-3 justify-start">
                    <div className="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                      <MessageSquare className="h-4 w-4 text-primary" />
                    </div>
                    <div className="bg-muted rounded-lg px-4 py-2 flex items-center gap-2">
                      <Loader2 className="h-4 w-4 animate-spin" />
                      <span className="text-sm text-muted-foreground">Thinking...</span>
                    </div>
                  </div>
                )}
                
                <div ref={messagesEndRef} />
              </div>
            </ScrollArea>

            {/* Input Area */}
            <div className="p-4 border-t">
              <form onSubmit={handleSendMessage} className="flex gap-2">
                <Textarea
                  ref={textareaRef}
                  value={message}
                  onChange={(e) => setMessage(e.target.value)}
                  onKeyDown={handleKeyDown}
                  placeholder="Type your message... (Press Enter to send, Shift+Enter for new line)"
                  className="min-h-[60px] max-h-[200px] resize-none"
                  disabled={isLoading}
                />
                <Button
                  type="submit"
                  disabled={!message.trim() || isLoading}
                  className="self-end"
                >
                  {isLoading ? (
                    <Loader2 className="h-4 w-4 animate-spin" />
                  ) : (
                    <Send className="h-4 w-4" />
                  )}
                </Button>
              </form>
            </div>
          </div>
        </div>
      </PageTemplate>
    </>
  );
}

