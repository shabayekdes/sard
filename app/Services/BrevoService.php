<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected ?string $apiKey;

    protected string $baseUrl = 'https://api.brevo.com/v3';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.brevo.api_key', '');
    }

    /**
     * Create or update a contact in Brevo.
     * Returns true on success, false otherwise. Logs errors.
     */
    public function createContact(string $email): bool
    {
        if (! config('services.brevo.enabled', false)) {
            return false;
        }

        if (empty($this->apiKey)) {
            Log::debug('Brevo: API key not set, skipping contact creation', ['email' => $email]);

            return false;
        }

        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Brevo: Invalid email for contact', ['email' => $email]);

            return false;
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/contacts", [
            'email' => $email,
        ]);

        if ($response->successful()) {
            Log::info('Brevo: Contact created', ['email' => $email]);
            return true;
        }

        // 400 can mean contact already exists; Brevo may still consider it success for idempotency
        if ($response->status() === 400) {
            $body = $response->json();
            if (isset($body['code']) && $body['code'] === 'duplicate_parameter') {
                Log::info('Brevo: Contact already exists', ['email' => $email]);
                return true;
            }
        }

        Log::warning('Brevo: Failed to create contact', [
            'email' => $email,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }
}
