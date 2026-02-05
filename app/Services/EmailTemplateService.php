<?php

namespace App\Services;

use App\Enum\EmailTemplateName;
use App\Mail\CommonTemplateMail;
use App\Models\Business;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    private static $sentEmails = [];

    /**
     * @param string $templateName
     * @param array $variables
     * @param string $toEmail
     * @param Business|null $business
     * @param string|null $toName
     * @return bool
     *
     * @deprecated not used anymore, use sendTemplateEmailWithLanguage instead
     */
    public function sendTemplateEmail(string $templateName, array $variables, string $toEmail, Business $business = null, string $toName = null)
    {
        // Skip email sending in demo mode
        if (config('app.is_demo', true)) {
            \Log::info('Email sending skipped - demo mode enabled');
            return true;
        }

        // Prevent duplicate emails within same request
        $emailKey = md5($templateName . $toEmail . serialize($variables));
        if (isset(self::$sentEmails[$emailKey])) {
            \Log::info('Duplicate email prevented', ['template' => $templateName, 'email' => $toEmail]);
            return true;
        }
        self::$sentEmails[$emailKey] = true;

        try {
            // Get email template
            $template = EmailTemplate::where('type', $templateName)->first();

            if (!$template) {
                throw new Exception("Email template '{$templateName}' not found");
            }

            // Get user's language or default to 'en'
            $language = 'en'; // default
            if ($business && $business->user) {
                $language = $business->user->lang ?? 'en';
            }

            $subjectTemplate = $template->getTranslation('subject', $language, false);
            $contentTemplate = $template->getTranslation('content', $language, false);
            $fromTemplate = $template->getTranslation('from', $language, false);

            if (!$subjectTemplate) {
                $subjectTemplate = $template->getTranslation('subject', 'en', false);
            }
            if (!$contentTemplate) {
                $contentTemplate = $template->getTranslation('content', 'en', false);
            }
            if (!$fromTemplate) {
                $fromTemplate = $template->getTranslation('from', 'en', false);
            }

            if (!$subjectTemplate && !$contentTemplate) {
                throw new Exception("No content found for template '{$templateName}'");
            }

            // Replace variables in subject and content
            $subject = $this->replaceVariables($subjectTemplate, $variables);
            $content = $this->replaceVariables($contentTemplate, $variables);
            $fromName = $this->replaceVariables($fromTemplate, $variables);

            // Configure SMTP settings
            $this->configureBusinessSMTP();

            // Get final email settings
            $fromEmail = getSetting('email_from_address') ?: config('mail.from.address');
            $finalFromName = getSetting('email_from_name') ? $this->replaceVariables(getSetting('email_from_name'), $variables) : $fromName;

            // Send email using Mail class
            $mail = new CommonTemplateMail($subject, $content, $variables, $language);
            $mail->from($fromEmail, $finalFromName);
            Mail::to($toEmail, $toName)->send($mail);

            return true;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Email sending failed: ' . $errorMessage);

            // Handle rate limiting gracefully
            if (str_contains($errorMessage, 'Too many emails per second')) {
                \Log::warning('Email rate limit exceeded');
                return false;
            }

            // Handle SMTP authentication errors
            if (str_contains($errorMessage, 'Authentication failed') || str_contains($errorMessage, 'Invalid credentials')) {
                \Log::error('SMTP Authentication failed - check email credentials');
                return false;
            }

            // Handle connection errors
            if (str_contains($errorMessage, 'Connection refused') || str_contains($errorMessage, 'Connection timed out')) {
                \Log::error('SMTP Connection failed - check host and port');
                return false;
            }

            // For other errors, return false to prevent retries
            return false;
        }
    }

    private function replaceVariables(?string $content, array $variables): string
    {
        if ($content === null) {
            return '';
        }
        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    public function sendTemplateEmailWithLanguage(EmailTemplateName $templateName, array $variables, string $toEmail, string $toName = null, string $language = 'en')
    {
        // Prevent duplicate emails within same request
        $emailKey = md5($templateName->value . $toEmail . serialize($variables));
        if (isset(self::$sentEmails[$emailKey])) {
            \Log::info('Duplicate email prevented', ['template' => $templateName->value, 'email' => $toEmail]);
            return true;
        }
        self::$sentEmails[$emailKey] = true;

        try {
            \Log::info('=== EMAIL TEMPLATE LANGUAGE DEBUG ===', [
                'template_name' => $templateName->value,
                'requested_language' => $language,
                'to_email' => $toEmail
            ]);

            // Get email template
            $template = EmailTemplate::where('type', $templateName->value)->first();

            if (!$template) {
                throw new Exception("Email template '{$templateName->value}' not found");
            }

            $subjectTemplate = $template->getTranslation('subject', $language, false);
            $contentTemplate = $template->getTranslation('content', $language, false);
            $fromTemplate = $template->getTranslation('from', $language, false);

            if (!$subjectTemplate) {
                $subjectTemplate = $template->getTranslation('subject', 'en', false);
            }
            if (!$contentTemplate) {
                $contentTemplate = $template->getTranslation('content', 'en', false);
            }
            if (!$fromTemplate) {
                $fromTemplate = $template->getTranslation('from', 'en', false);
            }

            \Log::info('Template language lookup', [
                'requested_lang' => $language,
                'has_subject' => !empty($subjectTemplate),
                'has_content' => !empty($contentTemplate),
                'template_id' => $template->id ?? null
            ]);

            // Log template content before replacement
            \Log::info('Template content before replacement:', [
                'subject' => $subjectTemplate ?? 'N/A',
                'content_preview' => $contentTemplate ? substr($contentTemplate, 0, 200) . '...' : 'N/A'
            ]);

            \Log::info('Variables for replacement:', $variables);


            if (!$subjectTemplate && !$contentTemplate) {
                throw new Exception("No content found for template '{$templateName->value}'");
            }

            // Replace variables in subject and content
            $subject = $this->replaceVariables($subjectTemplate, $variables);
            $content = $this->replaceVariables($contentTemplate, $variables);
            $fromName = $this->replaceVariables($fromTemplate, $variables);

            // Configure SMTP settings
            $this->configureBusinessSMTP();

            // Get final email settings
            $fromEmail = getSetting('email_from_address') ?: config('mail.from.address');
            $finalFromName = getSetting('email_from_name') ? $this->replaceVariables(getSetting('email_from_name'), $variables) : $fromName;

            // Send email using Mail class
            $mail = new CommonTemplateMail($subject, $content, $variables, $language);
            $mail->from($fromEmail, $finalFromName);
            Mail::to($toEmail, $toName)->send($mail);

            return true;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Email sending failed: ' . $errorMessage);

            // Handle rate limiting gracefully
            if (str_contains($errorMessage, 'Too many emails per second')) {
                \Log::warning('Email rate limit exceeded, email will be retried later');
                return false;
            }

            // Handle SMTP authentication errors
            if (str_contains($errorMessage, 'Authentication failed') || str_contains($errorMessage, 'Invalid credentials')) {
                \Log::error('SMTP Authentication failed - check email credentials');
                session()->flash('email_error', 'SMTP Authentication failed. Please check your email credentials.');
                return false;
            }

            // Handle connection errors
            if (str_contains($errorMessage, 'Connection refused') || str_contains($errorMessage, 'Connection timed out')) {
                \Log::error('SMTP Connection failed - check host and port');
                session()->flash('email_error', 'SMTP Connection failed. Please check your email host and port settings.');
                return false;
            }

            // Store error in session for display
            session()->flash('email_error', $errorMessage);
            throw $e;
        }
    }

    private function configureBusinessSMTP(?Business $business = null)
    {
        // Get email settings from settings table
        $emailDriver = getSetting('email_driver', config('mail.default', 'smtp'));
        $emailHost = getSetting('email_host') ?: config('mail.mailers.smtp.host');
        $emailUsername = getSetting('email_username') ?: config('mail.mailers.smtp.username');
        $emailPassword = getSetting('email_password') ?: config('mail.mailers.smtp.password');
        $emailPort = getSetting('email_port', config('mail.mailers.smtp.port', 587));
        $emailEncryption = getSetting('email_encryption', config('mail.mailers.smtp.encryption', 'tls'));

        // Check if email settings are configured
        if (!$emailHost) {
            throw new Exception("Email host not configured. Please set SMTP host in email settings.");
        }
        if (!$emailUsername) {
            throw new Exception("Email username not configured. Please set SMTP username in email settings.");
        }
        if (!$emailPassword) {
            throw new Exception("Email password not configured. Please set SMTP password in email settings.");
        }

        // Configure mail settings
        Config::set([
            'mail.default' => $emailDriver,
            'mail.mailers.smtp.host' => $emailHost,
            'mail.mailers.smtp.port' => $emailPort,
            'mail.mailers.smtp.username' => $emailUsername,
            'mail.mailers.smtp.password' => $emailPassword,
            'mail.mailers.smtp.encryption' => $emailEncryption,
        ]);

    }
}
