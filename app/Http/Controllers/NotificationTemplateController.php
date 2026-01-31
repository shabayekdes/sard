<?php

namespace App\Http\Controllers;

use App\EmailTemplateName;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateLang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationTemplate::with('notificationTemplateLangs');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('type', 'like', '%' . $request->search . '%');
        }

        $type = $request->get('type', 'slack');
        $query->where('type', $type);

        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->get('per_page', 10);
        $templates = $query->paginate($perPage);

        return Inertia::render('notification-templates/index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction', 'per_page', 'type'])
        ]);
    }

    public function show(NotificationTemplate $notificationTemplate)
    {
        $template = $notificationTemplate->load('notificationTemplateLangs');
        $languages = json_decode(file_get_contents(resource_path('lang/language.json')), true);

        $variables = $this->getVariablesByNameAndType(EmailTemplateName::from($template->name), $template->type);

        return Inertia::render('notification-templates/show', [
            'template' => $template,
            'languages' => $languages,
            'variables' => $variables
        ]);
    }

    private function getVariablesByNameAndType(EmailTemplateName $name, $type)
    {
        $key = $name->value . '_' . $type;

        return match ($key) {
            'New Case_slack' => [
                '{case_number}' => 'Case Number',
                '{client_name}' => 'Client Name',
                '{case_type}' => 'Case Type',
                '{created_by}' => 'Created By User',
                '{channel}' => 'Slack Channel'
            ],
            'New Case_twilio' => [
                '{case_number}' => 'Case Number',
                '{case_type}' => 'Case Type',
                '{created_by}' => 'Created By User',

            ],
            'New Court_slack' => [
                '{court_name}' => 'Court Name',
                '{court_type}' => 'Court Type',
                '{location}' => 'Court Location'
            ],
            'New Court_twilio' => [
                '{court_name}' => 'Court Name',
                '{location}' => 'Court Location'
            ],
            'Invoice Sent_slack' => [
                '{invoice_number}' => 'Invoice Number',
                '{client_name}' => 'Client Name',
                '{amount}' => 'Invoice Amount',
                '{sent_date}' => 'Date Sent'
            ],
            'Invoice Sent_twilio' => [
                '{invoice_number}' => 'Invoice Number',
                '{due_date}' => 'Due Sent'
            ],
            'New Client_slack' => [
                '{client_name}' => 'Client Name',
                '{client_type}' => 'Client Type',
                '{email}' => 'Client Email',
                '{created_by}' => 'Created By User'
            ],
            'New Client_twilio' => [
                '{client_name}' => 'Client Name',
                '{client_type}' => 'Client Type',

            ],
            default => $this->getDefaultVariablesByName($name),
        };
    }

    private function getDefaultVariablesByName(EmailTemplateName $name)
    {
        return match ($name) {
            EmailTemplateName::NEW_CASE => [
                '{case_number}' => 'Case Number',
                '{client_name}' => 'Client Name',
                '{case_type}' => 'Case Type',
                '{created_by}' => 'Created By User'
            ],
            EmailTemplateName::NEW_CLIENT => [
                '{client_name}' => 'Client Name',
                '{client_type}' => 'Client Type',
                '{email}' => 'Client Email',
                '{created_by}' => 'Created By User'
            ],
            EmailTemplateName::NEW_TASK => [
                '{task_title}' => 'Task Title',
                '{priority}' => 'Task Priority',
                '{due_date}' => 'Task Due Date',
                '{assigned_to}' => 'Assigned To User'
            ],
            EmailTemplateName::NEW_HEARING => [
                '{case_number}' => 'Case Number',
                '{hearing_date}' => 'Hearing Date',
                '{court}' => 'Court Name',
                '{judge}' => 'Judge Name'
            ],
            EmailTemplateName::NEW_INVOICE => [
                '{invoice_number}' => 'Invoice Number',
                '{client_name}' => 'Client Name',
                '{amount}' => 'Invoice Amount',
                '{due_date}' => 'Invoice Due Date'
            ],
            EmailTemplateName::INVOICE_SENT => [
                '{invoice_number}' => 'Invoice Number',
                '{client_name}' => 'Client Name',
                '{amount}' => 'Invoice Amount',
                '{sent_date}' => 'Date Sent'
            ],
            EmailTemplateName::NEW_COURT => [
                '{court_name}' => 'Court Name',
                '{court_type}' => 'Court Type',
                '{circle_type}' => 'Circle Type',
                '{location}' => 'Court Location'
            ],
            EmailTemplateName::NEW_JUDGE => [
                '{judge_name}' => 'Judge Name',
                '{court}' => 'Court Name',
                '{specialization}' => 'Judge Specialization'
            ],
            EmailTemplateName::NEW_LICENSE => [
                '{license_number}' => 'License Number',
                '{license_type}' => 'License Type',
                '{issuing_authority}' => 'Issuing Authority',
                '{expiry_date}' => 'Expiry Date'
            ],
            EmailTemplateName::NEW_REGULATORY_BODY => [
                '{body_name}' => 'Regulatory Body Name',
                '{jurisdiction}' => 'Jurisdiction',
                '{contact_info}' => 'Contact Information'
            ],
            EmailTemplateName::NEW_CLE_RECORD => [
                '{course_title}' => 'Course Title',
                '{credits_earned}' => 'Credits Earned',
                '{completion_date}' => 'Completion Date',
                '{provider}' => 'Course Provider'
            ],
            'Team Member Created' => [
                '{member_name}' => 'Team Member Name',
                '{email}' => 'Team Member Email',
                '{role}' => 'Team Member Role'
            ],
            default => [],
        };
    }

    public function updateSettings(NotificationTemplate $notificationTemplate, Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|max:255'
            ]);

            $notificationTemplate->update([
                'type' => $request->type
            ]);

            return redirect()->back()->with('success', __('Template settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update template settings: :error', ['error' => $e->getMessage()]));
        }
    }

    public function updateContent(NotificationTemplate $notificationTemplate, Request $request)
    {
        try {
            $request->validate([
                'lang' => 'required|string|max:10',
                'title' => 'required|string|max:255',
                'content' => 'required|string'
            ]);

            NotificationTemplateLang::updateOrCreate(
                [
                    'parent_id' => $notificationTemplate->id,
                    'lang' => $request->lang
                ],
                [
                    'title' => $request->title,
                    'content' => $request->get('content')
                ]
            );

            return redirect()->back()->with('success', __('Notification content updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update notification content: :error', ['error' => $e->getMessage()]));
        }
    }
}
