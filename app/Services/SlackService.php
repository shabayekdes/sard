<?php

namespace App\Services;

use App\Enum\EmailTemplateName;
use App\Models\TenantNotificationTemplate;

class SlackService
{
    public static function send($templateName, $data = [], $userId = null)
    {
        $userId = $userId ?: auth()->id();

        if (!$userId) {
            return false;
        }

        // Check if template exists and is of type 'slack'
        $template = \App\Models\NotificationTemplate::where('name', $templateName)
            ->where('type', 'slack')
            ->first();

        if (!$template) {
            return false;
        }

        $tenantId = $userId ? \App\Models\User::find($userId)?->tenant_id : (auth()->user()?->tenant_id ?? tenant('id'));
        if (!$tenantId || !TenantNotificationTemplate::isNotificationActive($templateName, $tenantId, 'slack')) {
            return false;
        }

        $webhookUrl = getSetting('slack_webhook_url', '', $userId);
        if (!$webhookUrl) {
            return false;
        }

        $message = self::formatMessage($templateName, $data);

        $payload = [
            'text' => $message,
            'username' => config('app.name', 'Advocate SaaS'),
            'icon_emoji' => ':bell:'
        ];

        $jsonPayload = json_encode($payload);
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return $httpCode === 200 && empty($curlError);
    }

    public function sendTemplateMessageWithLanguage(EmailTemplateName $templateName, $variables, $webhookUrl, $language = 'en')
    {
        if (!$webhookUrl) {
            return false;
        }

        // Get template content (you can extend this to use actual templates)
        $message = $this->getTemplateMessage($templateName, $variables, $language);

        $payload = [
            'text' => $message,
            'username' => config('app.name', 'Sard App'),
            'icon_emoji' => ':bell:'
        ];

        return $this->sendCurlRequest($webhookUrl, $payload);
    }

    private function getTemplateMessage(EmailTemplateName $templateName, $variables, $language)
    {
        // Get template from database with type check
        $template = \App\Models\NotificationTemplate::where('name', $templateName->value)
            ->where('type', 'slack')
            ->first();

        if (!$template) {
            return "*{$templateName->value} Notification*\n\nTemplate not found.";
        }

        $content = $template->getTranslation('content', $language, false) ?: $template->getTranslation('content', 'en', false);

        if ($content === null || $content === '') {
            return "*{$templateName->value} Notification*\n\nTemplate content not found.";
        }

        // Replace variables in template content
        return $this->replaceVariables($content, $variables);
    }
      private function replaceVariables(string $content, array $variables): string
    {
        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    private function sendCurlRequest($webhookUrl, $payload)
    {

        $jsonPayload = json_encode($payload);
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

// dd('Sending Slack notification via cURL', [
//             'webhook_url' => $webhookUrl,
//             'payload' => $payload
//         ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return $httpCode === 200 && empty($curlError);
    }

    private static function formatMessage($templateName, $data)
    {
        $title = $data['title'] ?? ucfirst(str_replace('_', ' ', $templateName));
        $message = $data['message'] ?? 'New notification from Advocate SaaS';

        $formatted = "*{$title}*\n\n{$message}";

        if (isset($data['url'])) {
            $formatted .= "\n\n<{$data['url']}|View Details>";
        }

        return $formatted;
    }

    public function sendTestMessage($webhookUrl)
    {
        $payload = [
            'text' => '*Test Message from Advocate SaaS*

This is a test message to verify your Slack integration is working correctly.

If you can see this message, your webhook configuration is successful! 🎉',
            'username' => config('app.name', 'Advocate SaaS'),
            'icon_emoji' => ':white_check_mark:'
        ];

        return $this->sendCurlRequest($webhookUrl, $payload);
    }
}
