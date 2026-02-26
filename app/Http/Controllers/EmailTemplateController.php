<?php

namespace App\Http\Controllers;

use App\Enum\EmailTemplateName;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('from', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $templates = $query->paginate($perPage);

        return Inertia::render('email-templates/index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction', 'per_page'])
        ]);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        $template = $emailTemplate;
        $template->setAttribute('name', $template->getTranslations('name'));
        $template->setAttribute('from', $template->getTranslations('from'));
        $template->setAttribute('subject', $template->getTranslations('subject'));
        $template->setAttribute('content', $template->getTranslations('content'));
        $languages = json_decode(file_get_contents(lang_path('language.json')), true);
        $templateType = $template->type instanceof EmailTemplateName ? $template->type->value : $template->type;

        // Template-specific variables
        $variables = [];

        if ($templateType === EmailTemplateName::INVOICE_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{client}' => 'Client Name',
                '{case}' => 'Case Name',
                '{invoice_date}' => 'Invoice Date',
                '{due_date}' => 'Due Date',
                '{total_amount}' => 'Total Amount',
                '{invoice_number}' => 'Invoice Number',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::INVOICE_SENT->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{client}' => 'Client Name',
                '{case}' => 'Case Name',
                '{invoice_date}' => 'Invoice Date',
                '{due_date}' => 'Due Date',
                '{total_amount}' => 'Total Amount',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::TEAM_MEMBER_CREATED->value) {
            $variables = [
                '{name}' => 'Member Name',
                '{email}' => 'Member Email',
                '{password}' => 'Password',
                '{role}' => 'Role',
                '{phone_no}' => 'Phone Number',
                '{user_name}' => 'User Name',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::CLIENT_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{name}' => 'Client Name',
                '{client_name}' => 'Client Name',
                '{email}' => 'Client Email',
                '{password}' => 'Client Password',
                '{phone_no}' => 'Phone Number',
                '{dob}' => 'Date of Birth',
                '{client_type}' => 'Client Type',
                '{tax_id}' => 'Tax ID',
                '{tax_rate}' => 'Tax Rate',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::CASE_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{client}' => 'Client Name',
                '{title}' => 'Case Title',
                '{type}' => 'Case Type',
                '{case_id}' => 'Case Id',
                '{filling_date}' => 'Filling Date',
                '{expected_complete_date}' => 'Expected Completion Date',
                '{app_name}' => 'App Name',

            ];
        } elseif ($templateType === EmailTemplateName::HEARING_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{hearing_number}' => 'Hearing Number',
                '{type}' => 'Hearing Type',
                '{hearing_date}' => 'Hearing Date',
                '{hearing_time}' => 'Hearing Time',
                '{court_name}' => 'Court Name',
                '{duration}' => 'Duration',
                '{case_number}' => 'Case Number',
                '{client_name}' => 'Client Name',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::COURT_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{name}' => 'Court Name',
                '{type}' => 'Court Type',
                '{phoneno}' => 'Phone Number',
                '{email}' => 'Email',
                '{circle_type}' => 'Circle Type',
                '{address}' => 'Address',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::TASK_CREATED->value) {
            $variables = [
                '{user_name}' => 'Assigned By',
                '{assigned_to}' => 'Assigned To',
                '{title}' => 'Task Title',
                '{priority}' => 'Priority',
                '{due_date}' => 'Due Date',
                '{case}' => 'Case',
                '{task_type}' => 'Task Type',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::LICENSE_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{team_member}' => 'Team Member',
                '{license_number}' => 'License Number',
                '{license_type}' => 'License Type',
                '{issuing_authority}' => 'Issuing Authority',
                '{jurisdiction}' => 'Jurisdiction',
                '{issue_date}' => 'Issue Date',
                '{expiry_date}' => 'Expiry Date',
                '{status}' => 'Status',
                '{notes}' => 'Notes',
                '{license_holder_name}' => 'License Holder Name',
                '{license_holder_email}' => 'License Holder Email',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::CLE_RECORD_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{team_member}' => 'Team Member',
                '{course_name}' => 'Course Name',
                '{provider}' => 'Provider',
                '{credit_earned}' => 'Credit Earned',
                '{credit_required}' => 'Credit Required',
                '{certificate_num}' => 'Certificate Number',
                '{app_name}' => 'App Name'
            ];
        } elseif ($templateType === EmailTemplateName::REGULATORY_BODY_CREATED->value) {
            $variables = [
                '{user_name}' => 'User Name',
                '{name}' => 'Regulatory Body Name',
                '{jurisdiction}' => 'Jurisdiction',
                '{email}' => 'Contact Email',
                '{phoneno}' => 'Phone Number',
                '{address}' => 'Address',
                '{website}' => 'Website',
                '{app_name}' => 'App Name'
            ];
        }

        return Inertia::render('email-templates/show', [
            'template' => $template,
            'templateTypes' => array_map(fn (EmailTemplateName $case) => $case->value, EmailTemplateName::cases()),
            'languages' => $languages,
            'variables' => $variables
        ]);
    }

    public function updateSettings(EmailTemplate $emailTemplate, Request $request)
    {
        try {
            $request->validate([
                'name.en' => 'required|string|max:255',
                'name.ar' => 'required|string|max:255',
                'from.en' => 'required|string|max:255',
                'from.ar' => 'required|string|max:255',
                'type' => ['required', 'string', Rule::in(array_map(fn (EmailTemplateName $case) => $case->value, EmailTemplateName::cases()))],
            ]);

            $emailTemplate->setTranslations('name', [
                'en' => $request->input('name.en'),
                'ar' => $request->input('name.ar'),
            ]);
            $emailTemplate->setTranslations('from', [
                'en' => $request->input('from.en'),
                'ar' => $request->input('from.ar'),
            ]);
            $emailTemplate->type = $request->input('type');
            $emailTemplate->save();

            return redirect()->back()->with('success', __('Template settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update template settings: :error', ['error' => $e->getMessage()]));
        }
    }

    public function updateContent(EmailTemplate $emailTemplate, Request $request)
    {
        try {
            $request->validate([
                'lang' => 'required|string|max:10',
                'subject' => 'required|string|max:255',
                'content' => 'required|string'
            ]);

            $emailTemplate->setTranslation('subject', $request->lang, $request->subject);
            $emailTemplate->setTranslation('content', $request->lang, $request->get('content'));
            $emailTemplate->save();

            return redirect()->back()->with('success', __('Email content updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update email content: :error', ['error' => $e->getMessage()]));
        }
    }
}
