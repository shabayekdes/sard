import React, { useState, useEffect, useRef } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { PageTemplate } from '@/components/page-template';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Send, Loader2, MessageSquare, CheckCircle, FileText } from 'lucide-react';
import { toast } from 'sonner';
import { cn } from '@/lib/utils';
import { useTranslation } from 'react-i18next';

interface ChatMessage {
  id: number | string;
  role: 'user' | 'assistant' | 'system';
  content: string;
  created_at?: string;
}

interface ParsedCaseInfo {
  title: string | null;
  description: string | null;
  suggested_case_type: string | null;
  suggested_client: string | null;
  suggested_court: string | null;
  suggested_priority: string;
  key_facts: string[];
  opposing_party: string | null;
  important_dates: string[];
  missing_information: string[];
}

interface PageProps {
  clients: Array<{ id: number; name: string | { ar: string; en: string } }>;
  caseTypes: Array<{ id: number; name: string | { ar: string; en: string } }>;
  caseStatuses: Array<{ id: number; name: string | { ar: string; en: string } }>;
  courts: Array<{ id: number; name: string | { ar: string; en: string } }>;
}

// Helper function to get translated value
const getTranslatedValue = (value: string | { ar: string; en: string } | null | undefined, locale: string = 'en'): string => {
  if (!value) return '';
  if (typeof value === 'string') return value;
  if (typeof value === 'object' && value !== null) {
    return value[locale as keyof typeof value] || value.en || value.ar || '';
  }
  return '';
};

export default function CaseCreateChat() {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language || 'en';
  const { clients, caseTypes, caseStatuses, courts } = usePage<PageProps>().props;
  
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [message, setMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [parsedInfo, setParsedInfo] = useState<ParsedCaseInfo | null>(null);
  const [showCreateForm, setShowCreateForm] = useState(false);
  
  // Form fields
  const [selectedClient, setSelectedClient] = useState<string>('');
  const [selectedCaseType, setSelectedCaseType] = useState<string>('');
  const [selectedCaseStatus, setSelectedCaseStatus] = useState<string>('');
  const [selectedCourt, setSelectedCourt] = useState<string>('');
  const [isCreating, setIsCreating] = useState(false);
  
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const textareaRef = useRef<HTMLTextAreaElement>(null);

  // Auto-scroll to bottom
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

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

    // Add user message
    const tempUserMessage: ChatMessage = {
      id: Date.now(),
      role: 'user',
      content: userMessage,
    };

    setMessages(prev => [...prev, tempUserMessage]);

    try {
      const response = await fetch(route('cases.generate-from-prompt'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          prompt: userMessage,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to generate case information');
      }

      if (data.success) {
        // Add assistant response
        const assistantMessage: ChatMessage = {
          id: Date.now() + 1,
          role: 'assistant',
          content: data.data.raw_response || 'Case information extracted successfully.',
        };

        setMessages(prev => [...prev, assistantMessage]);
        
        // Store parsed information
        if (data.data.parsed) {
          setParsedInfo(data.data.parsed);
          
          // Show create form if we have enough info
          if (data.data.parsed.title && data.data.parsed.description) {
            setShowCreateForm(true);
          }
        }

        toast.success('Case information extracted successfully');
      }
    } catch (error) {
      console.error('Error generating case info:', error);
      toast.error(error instanceof Error ? error.message : 'Failed to generate case information');
      
      // Remove user message on error
      setMessages(prev => prev.filter(msg => msg.id !== tempUserMessage.id));
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

  const handleCreateCase = async () => {
    if (!selectedClient || !selectedCaseType || !selectedCaseStatus || !selectedCourt) {
      toast.error('Please fill in all required fields');
      return;
    }

    setIsCreating(true);

    try {
      const response = await fetch(route('cases.create-from-prompt'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          prompt: messages.find(m => m.role === 'user')?.content || '',
          client_id: parseInt(selectedClient),
          case_type_id: parseInt(selectedCaseType),
          case_status_id: parseInt(selectedCaseStatus),
          court_id: parseInt(selectedCourt),
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to create case');
      }

      if (data.success) {
        toast.success('Case created successfully!');
        router.visit(route('cases.show', data.case.id));
      }
    } catch (error) {
      console.error('Error creating case:', error);
      toast.error(error instanceof Error ? error.message : 'Failed to create case');
    } finally {
      setIsCreating(false);
    }
  };

  const formatMessageTime = (dateString?: string) => {
    if (!dateString) return 'Just now';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    return date.toLocaleTimeString();
  };

  return (
    <>
      <Head title="Create Case with AI" />
      <PageTemplate
        title="Create Case with AI Assistant"
        description="Describe your case in natural language and let AI help you create it"
      >
        <div className="flex flex-col h-[calc(100vh-12rem)] border rounded-lg overflow-hidden">
          {/* Messages Area */}
          <ScrollArea className="flex-1 p-4">
            <div className="space-y-4">
              {messages.length === 0 ? (
                <div className="flex items-center justify-center h-full text-muted-foreground">
                  <div className="text-center max-w-md">
                    <MessageSquare className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p className="font-medium mb-2">Describe your case</p>
                    <p className="text-sm">
                      Tell me about your case in natural language. For example:
                    </p>
                    <div className="mt-4 text-left space-y-2 text-sm bg-muted p-4 rounded-lg">
                      <p className="font-medium">Example prompts:</p>
                      <p>• "Contract dispute case for client ABC Company against XYZ Corp. Contract signed January 15, 2024. Payment of $50,000 due February 1 but not received. High priority."</p>
                      <p>• "دعوى نزاع عقدي للعميل أحمد محمد ضد شركة XYZ. تم التوقيع على العقد في 15 يناير 2024. المبلغ 200,000 ريال مستحق في 1 فبراير. أولوية عالية."</p>
                    </div>
                  </div>
                </div>
              ) : (
                messages.map((msg) => (
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
                      {msg.created_at && (
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
                      )}
                    </div>

                    {msg.role === 'user' && (
                      <div className="h-8 w-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0 text-primary-foreground text-sm font-medium">
                        U
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
                    <span className="text-sm text-muted-foreground">Analyzing case description...</span>
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
                placeholder="Describe your case... (Press Enter to send, Shift+Enter for new line)"
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

        {/* Create Case Dialog */}
        <Dialog open={showCreateForm} onOpenChange={setShowCreateForm}>
          <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>Create Case from Generated Information</DialogTitle>
              <DialogDescription>
                Review the extracted information and fill in required fields to create the case.
              </DialogDescription>
            </DialogHeader>

            {parsedInfo && (
              <div className="space-y-4 mt-4">
                {/* Extracted Information Display */}
                <div className="bg-muted p-4 rounded-lg space-y-3">
                  <div className="flex items-center gap-2 mb-3">
                    <FileText className="h-4 w-4" />
                    <h4 className="font-semibold">Extracted Information</h4>
                  </div>
                  
                  {parsedInfo.title && (
                    <div>
                      <span className="text-sm font-medium">Title: </span>
                      <span className="text-sm">{parsedInfo.title}</span>
                    </div>
                  )}
                  
                  {parsedInfo.description && (
                    <div>
                      <span className="text-sm font-medium">Description: </span>
                      <div className="text-sm mt-1 whitespace-pre-wrap">{parsedInfo.description}</div>
                    </div>
                  )}
                  
                  {parsedInfo.suggested_priority && (
                    <div>
                      <span className="text-sm font-medium">Suggested Priority: </span>
                      <span className="text-sm capitalize">{parsedInfo.suggested_priority}</span>
                    </div>
                  )}
                  
                  {parsedInfo.opposing_party && (
                    <div>
                      <span className="text-sm font-medium">Opposing Party: </span>
                      <span className="text-sm">{parsedInfo.opposing_party}</span>
                    </div>
                  )}
                  
                  {parsedInfo.key_facts && parsedInfo.key_facts.length > 0 && (
                    <div>
                      <span className="text-sm font-medium">Key Facts: </span>
                      <ul className="text-sm mt-1 list-disc list-inside">
                        {parsedInfo.key_facts.map((fact, idx) => (
                          <li key={idx}>{fact}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                </div>

                {/* Required Fields */}
                <div className="space-y-4">
                  <div>
                    <label className="text-sm font-medium mb-2 block">Client *</label>
                    <Select value={selectedClient} onValueChange={setSelectedClient} required>
                      <SelectTrigger>
                        <SelectValue placeholder="Select client" />
                      </SelectTrigger>
                      <SelectContent>
                        {clients.map((client) => (
                          <SelectItem key={client.id} value={client.id.toString()}>
                            {client.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div>
                    <label className="text-sm font-medium mb-2 block">
                      Case Type *
                      {parsedInfo.suggested_case_type && (
                        <span className="text-xs text-muted-foreground ml-2">
                          (Suggested: {parsedInfo.suggested_case_type})
                        </span>
                      )}
                    </label>
                    <Select value={selectedCaseType} onValueChange={setSelectedCaseType} required>
                      <SelectTrigger>
                        <SelectValue placeholder={parsedInfo.suggested_case_type ? `Select or search for: ${parsedInfo.suggested_case_type}` : "Select case type"} />
                      </SelectTrigger>
                      <SelectContent>
                        {caseTypes.map((type) => {
                          const typeName = getTranslatedValue(type.name, currentLocale);
                          const isSuggested = parsedInfo.suggested_case_type && 
                            typeName.toLowerCase().includes(parsedInfo.suggested_case_type.toLowerCase());
                          return (
                            <SelectItem key={type.id} value={type.id.toString()}>
                              {typeName}
                              {isSuggested && <span className="text-xs text-muted-foreground ml-2">(Suggested)</span>}
                            </SelectItem>
                          );
                        })}
                      </SelectContent>
                    </Select>
                  </div>

                  <div>
                    <label className="text-sm font-medium mb-2 block">Case Status *</label>
                    <Select value={selectedCaseStatus} onValueChange={setSelectedCaseStatus} required>
                      <SelectTrigger>
                        <SelectValue placeholder="Select case status" />
                      </SelectTrigger>
                      <SelectContent>
                        {caseStatuses.map((status) => (
                          <SelectItem key={status.id} value={status.id.toString()}>
                            {getTranslatedValue(status.name, currentLocale)}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div>
                    <label className="text-sm font-medium mb-2 block">
                      Court *
                      {parsedInfo.suggested_court && (
                        <span className="text-xs text-muted-foreground ml-2">
                          (Suggested: {parsedInfo.suggested_court})
                        </span>
                      )}
                    </label>
                    <Select value={selectedCourt} onValueChange={setSelectedCourt} required>
                      <SelectTrigger>
                        <SelectValue placeholder={parsedInfo.suggested_court ? `Select or search for: ${parsedInfo.suggested_court}` : "Select court"} />
                      </SelectTrigger>
                      <SelectContent>
                        {courts.map((court) => {
                          const courtName = getTranslatedValue(court.name, currentLocale);
                          const isSuggested = parsedInfo.suggested_court && 
                            courtName.toLowerCase().includes(parsedInfo.suggested_court.toLowerCase());
                          return (
                            <SelectItem key={court.id} value={court.id.toString()}>
                              {courtName}
                              {isSuggested && <span className="text-xs text-muted-foreground ml-2">(Suggested)</span>}
                            </SelectItem>
                          );
                        })}
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                {/* Missing Information Warning */}
                {parsedInfo.missing_information && parsedInfo.missing_information.length > 0 && (
                  <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                    <p className="text-sm font-medium mb-2">Missing Information:</p>
                    <ul className="text-sm list-disc list-inside space-y-1">
                      {parsedInfo.missing_information.map((info, idx) => (
                        <li key={idx}>{info}</li>
                      ))}
                    </ul>
                  </div>
                )}

                {/* Actions */}
                <div className="flex gap-2 justify-end pt-4 border-t">
                  <Button
                    variant="outline"
                    onClick={() => setShowCreateForm(false)}
                  >
                    Cancel
                  </Button>
                  <Button
                    onClick={handleCreateCase}
                    disabled={!selectedClient || !selectedCaseType || !selectedCaseStatus || !selectedCourt || isCreating}
                  >
                    {isCreating ? (
                      <>
                        <Loader2 className="h-4 w-4 animate-spin mr-2" />
                        Creating...
                      </>
                    ) : (
                      <>
                        <CheckCircle className="h-4 w-4 mr-2" />
                        Create Case
                      </>
                    )}
                  </Button>
                </div>
              </div>
            )}
          </DialogContent>
        </Dialog>
      </PageTemplate>
    </>
  );
}

