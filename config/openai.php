<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration
    |
    */

    'api_key' => env('OPENAI_API_KEY'),
    
    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4o-mini'),
    
    'models' => [
        'gpt-3.5-turbo' => [
            'name' => 'GPT-3.5 Turbo',
            'max_tokens' => 4096,
            'cost_per_1k_tokens' => 0.002,
        ],
        'gpt-4' => [
            'name' => 'GPT-4',
            'max_tokens' => 8192,
            'cost_per_1k_tokens' => 0.03,
        ],
        'gpt-4-turbo' => [
            'name' => 'GPT-4 Turbo',
            'max_tokens' => 128000,
            'cost_per_1k_tokens' => 0.01,
        ],
        'gpt-4o' => [
            'name' => 'GPT-4o',
            'max_tokens' => 128000,
            'cost_per_1k_tokens' => 0.005,
        ],
        'gpt-4o-mini' => [
            'name' => 'GPT-4o Mini',
            'max_tokens' => 128000,
            'cost_per_1k_tokens' => 0.00015,
        ],
    ],
    
    'timeout' => env('OPENAI_TIMEOUT', 30),
    
    'max_retries' => env('OPENAI_MAX_RETRIES', 3),
    
    'temperature' => [
        'low' => 0.3,
        'medium' => 0.7,
        'high' => 0.9,
    ],
    
    'supported_languages' => [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to legal use cases with low temperature
    | to minimize hallucinations and ensure factual accuracy.
    |
    */

    'legal' => [
        // Low temperature for factual, non-hallucinatory responses
        'temperature' => env('OPENAI_LEGAL_TEMPERATURE', 0.2),

        // Maximum tokens for different operations
        'max_tokens' => [
            'text_summary' => env('OPENAI_MAX_TOKENS_TEXT_SUMMARY', 500),
            'case_summary' => env('OPENAI_MAX_TOKENS_CASE_SUMMARY', 2000),
            'memo_draft' => env('OPENAI_MAX_TOKENS_MEMO_DRAFT', 3000),
        ],

        // System prompts for legal operations
        // These reference the centralized prompts in config/legal-prompts.php
        // You can also use LegalPrompts class constants: LegalPrompts::CASE_SUMMARIZATION
        'prompts' => [
            'summarize_text' => config('legal-prompts.document_summarization'),
            'summarize_case' => config('legal-prompts.case_summarization'),
            'draft_memo' => config('legal-prompts.drafting'),
            'document_summarization' => config('legal-prompts.document_summarization'),
            'drafting' => config('legal-prompts.drafting'),
            'timeline_extraction' => config('legal-prompts.timeline_extraction'),
        ],
    ],
];