<?php

namespace Database\Seeders;

use App\Enum\EmailTemplateName;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => EmailTemplateName::CASE_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Case Created: {case_number}',
                    'ar' => 'تم إنشاء قضية جديدة: {case_number}',
                ],
                'content' => [
                    'en' => 'A new case "{case_number}" has been created for client {client_name}. Case type: {case_type}. Created by: {created_by}.',
                    'ar' => 'تم إنشاء قضية جديدة "{case_number}" للعميل {client_name}. نوع القضية: {case_type}. تم الإنشاء بواسطة: {created_by}.',
                ],
            ],
            [
                'name' => EmailTemplateName::CLIENT_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Client Added: {client_name}',
                    'ar' => 'تمت إضافة عميل جديد: {client_name}',
                ],
                'content' => [
                    'en' => 'A new client "{client_name}" has been added to the system. Client type: {client_type}. Email: {email}.',
                    'ar' => 'تمت إضافة عميل جديد "{client_name}" إلى النظام. نوع العميل: {client_type}. البريد الإلكتروني: {email}.',
                ],
            ],
            [
                'name' => EmailTemplateName::TASK_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Task Assigned: {task_title}',
                    'ar' => 'تم تعيين مهمة جديدة: {task_title}',
                ],
                'content' => [
                    'en' => 'You have been assigned a new task "{task_title}". Priority: {priority}. Due date: {due_date}. Assigned to: {assigned_to}.',
                    'ar' => 'تم تعيين مهمة جديدة لك بعنوان "{task_title}". الأولوية: {priority}. تاريخ الاستحقاق: {due_date}. تم التعيين إلى: {assigned_to}.',
                ],
            ],
            [
                'name' => EmailTemplateName::HEARING_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Hearing Scheduled: {case_number}',
                    'ar' => 'تم تحديد جلسة جديدة: {case_number}',
                ],
                'content' => [
                    'en' => 'A new hearing has been scheduled for case {case_number}. Date: {hearing_date}. Court: {court}. Judge: {judge}.',
                    'ar' => 'تم تحديد جلسة جديدة للقضية {case_number}. التاريخ: {hearing_date}. المحكمة: {court}. القاضي: {judge}.',
                ],
            ],
            [
                'name' => EmailTemplateName::INVOICE_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Invoice Created: {invoice_number}',
                    'ar' => 'تم إنشاء فاتورة جديدة: {invoice_number}',
                ],
                'content' => [
                    'en' => 'A new invoice {invoice_number} has been created for {client_name}. Amount: {amount}. Due date: {due_date}.',
                    'ar' => 'تم إنشاء فاتورة جديدة {invoice_number} للعميل {client_name}. المبلغ: {amount}. تاريخ الاستحقاق: {due_date}.',
                ],
            ],
            [
                'name' => EmailTemplateName::INVOICE_SENT->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'Invoice Sent: {invoice_number}',
                    'ar' => 'تم إرسال الفاتورة: {invoice_number}',
                ],
                'content' => [
                    'en' => 'Invoice {invoice_number} has been sent to {client_name}. Amount: {amount}. Sent on: {sent_date}.',
                    'ar' => 'تم إرسال الفاتورة {invoice_number} إلى {client_name}. المبلغ: {amount}. تم الإرسال في: {sent_date}.',
                ],
            ],
            [
                'name' => EmailTemplateName::COURT_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Court Added: {court_name}',
                    'ar' => 'تمت إضافة محكمة جديدة: {court_name}',
                ],
                'content' => [
                    'en' => 'A new court "{court_name}" has been added to the system. Type: {court_type}. Location: {location}.',
                    'ar' => 'تمت إضافة محكمة جديدة "{court_name}" إلى النظام. النوع: {court_type}. الموقع: {location}.',
                ],
            ],
            [
                'name' => EmailTemplateName::JUDGE_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Judge Added: {judge_name}',
                    'ar' => 'تمت إضافة قاضٍ جديد: {judge_name}',
                ],
                'content' => [
                    'en' => 'A new judge "{judge_name}" has been added to the system. Court: {court}. Specialization: {specialization}.',
                    'ar' => 'تمت إضافة القاضي الجديد "{judge_name}" إلى النظام. المحكمة: {court}. التخصص: {specialization}.',
                ],
            ],
            [
                'name' => EmailTemplateName::TEAM_MEMBER_CREATED->value,
                'type' => 'slack',
                'title' => [
                    'en' => 'New Team Member: {member_name}',
                    'ar' => 'عضو فريق جديد: {member_name}',
                ],
                'content' => [
                    'en' => 'A new team member "{member_name}" has been added to the system. Email: {email}. Role: {role}.',
                    'ar' => 'تمت إضافة عضو فريق جديد "{member_name}" إلى النظام. البريد الإلكتروني: {email}. الدور: {role}.',
                ],
            ],
            [
                'name' => EmailTemplateName::CASE_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Case: {case_number}',
                    'ar' => 'قضية جديدة: {case_number}',
                ],
                'content' => [
                    'en' => 'A new case ({case_number}) has been opened for you. Type: {case_type}. Assigned by {created_by}.',
                    'ar' => 'تم فتح قضية جديدة ({case_number}) لك. النوع: {case_type}. تم التعيين بواسطة {created_by}.',
                ],
            ],
            [
                'name' => EmailTemplateName::CLIENT_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Client: {client_name}',
                    'ar' => 'عميل جديد: {client_name}',
                ],
                'content' => [
                    'en' => 'Welcome {client_name}! Your profile has been added successfully. Type: {client_type}.',
                    'ar' => 'مرحبًا {client_name}! تم إضافة ملفك الشخصي بنجاح. النوع: {client_type}.',
                ],
            ],
            [
                'name' => EmailTemplateName::HEARING_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Hearing: {case_number}',
                    'ar' => 'جلسة جديدة: {case_number}',
                ],
                'content' => [
                    'en' => 'Your hearing for case {case_number} is scheduled on {hearing_date} at {court}. Judge: {judge}.',
                    'ar' => 'تم تحديد جلستك للقضية {case_number} في {court} بتاريخ {hearing_date}. القاضي: {judge}.',
                ],
            ],
            [
                'name' => EmailTemplateName::INVOICE_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Invoice: {invoice_number}',
                    'ar' => 'فاتورة جديدة: {invoice_number}',
                ],
                'content' => [
                    'en' => 'Invoice {invoice_number} issued for {client_name}. Amount: {amount}. Due on {due_date}.',
                    'ar' => 'تم إصدار الفاتورة {invoice_number} للعميل {client_name}. المبلغ: {amount}. تاريخ الاستحقاق: {due_date}.',
                ],
            ],
            [
                'name' => EmailTemplateName::INVOICE_SENT->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'Invoice Sent: {invoice_number}',
                    'ar' => 'تم إرسال الفاتورة: {invoice_number}',
                ],
                'content' => [
                    'en' => 'Your invoice {invoice_number} has been sent. Please review and complete payment by {due_date}.',
                    'ar' => 'تم إرسال فاتورتك {invoice_number}. يرجى المراجعة وإكمال الدفع قبل {due_date}.',
                ],
            ],
            [
                'name' => EmailTemplateName::COURT_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Court: {court_name}',
                    'ar' => 'محكمة جديدة: {court_name}',
                ],
                'content' => [
                    'en' => 'A new court "{court_name}" has been added to our records. Location: {location}.',
                    'ar' => 'تمت إضافة محكمة جديدة "{court_name}" إلى سجلاتنا. الموقع: {location}.',
                ],
            ],
            [
                'name' => EmailTemplateName::JUDGE_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Judge: {judge_name}',
                    'ar' => 'قاضٍ جديد: {judge_name}',
                ],
                'content' => [
                    'en' => 'Judge {judge_name} ({specialization}) has been assigned to court {court}.',
                    'ar' => 'تم تعيين القاضي {judge_name} ({specialization}) في المحكمة {court}.',
                ],
            ],
            [
                'name' => EmailTemplateName::REGULATORY_BODY_CREATED->value,
                'type' => 'twilio',
                'title' => [
                    'en' => 'New Regulatory Body: {body_name}',
                    'ar' => 'هيئة تنظيمية جديدة: {body_name}',
                ],
                'content' => [
                    'en' => 'Regulatory body "{body_name}" has been added under {jurisdiction}. Contact: {contact_info}.',
                    'ar' => 'تمت إضافة الهيئة التنظيمية "{body_name}" ضمن {jurisdiction}. جهة الاتصال: {contact_info}.',
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            NotificationTemplate::create([
                    'name' => $templateData['name'],
                    'type' => $templateData['type'],
                    'title' => $templateData['title'],
                    'content' => $templateData['content'],
                ]
            );
        }
    }
}
