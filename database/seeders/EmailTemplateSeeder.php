<?php

namespace Database\Seeders;

use App\Enum\EmailTemplateName;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => EmailTemplateName::INVOICE_CREATED,
                'name' => ['ar' => 'تم إنشاء الفاتورة', 'en' => 'Invoice Created'],
                'from' => ['ar' => 'Laravel', 'en' => 'Sard'],
                'subject' => ['ar' => 'تم إنشاء فاتورتك رقم {invoice_number} – {client}', 'en' => 'Your Invoice Created: {client}'],
                'content' => [
                    'ar' => '<p style="text-align: right">مرحبًا {client}،</p><p style="text-align: right">تم إنشاء فاتورتك رقم <strong>{invoice_number}</strong> في النظام.</p><h3 style="text-align: right">التفاصيل</h3><p style="text-align: right"><strong>رقم الفاتورة:</strong> {invoice_number}</p><p style="text-align: right"><strong>العميل:</strong> {client}</p><p style="text-align: right"><strong>القضية:</strong> {case}</p><p style="text-align: right"><strong>تاريخ الفاتورة:</strong> {invoice_date}</p><p style="text-align: right"><strong>تاريخ الاستحقاق:</strong> {due_date}</p><p style="text-align: right"><strong>إجمالي المبلغ:</strong> {total_amount}</p><p style="text-align: right">يرجى مراجعة الفاتورة وتأكيدها عبر النظام.</p><p style="text-align: right"> مع خالص التحية،<br>{user_name}</p>',
                    'en' => "<p>Hello {client},</p>\n<p>Your invoice has been created in the system.</p>\n<h3>Details</h3>\n<p><strong>Client:</strong> {client}</p>\n<p><strong>Case:</strong> {case}</p>\n<p><strong>Invoice Date:</strong> {invoice_date}</p>\n<p><strong>Due Date:</strong> {due_date}</p>\n<p><strong>Total Amount:</strong> {total_amount}</p>\n<p>Please review and confirm your invoice in the system.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::INVOICE_SENT,
                'name' => ['ar' => 'تم إرسال الفاتورة', 'en' => 'Invoice Sent'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'تم إرسال فاتورتك رقم {invoice_number} – {client}', 'en' => 'Your Invoice Sent: {client}'],
                'content' => [
                    'ar' => '<p style="text-align: right">مرحبًا {client}،</p><p style="text-align: right">تم إرسال فاتورتك رقم <strong>{invoice_number}</strong> إليك.</p><h3 style="text-align: right">التفاصيل</h3><p style="text-align: right"><strong>رقم الفاتورة:</strong> {invoice_number}</p><p style="text-align: right"><strong>العميل:</strong> {client}</p><p style="text-align: right"><strong>القضية:</strong> {case}</p><p style="text-align: right"><strong>تاريخ الفاتورة:</strong> {invoice_date}</p><p style="text-align: right"><strong>تاريخ الاستحقاق:</strong> {due_date}</p><p style="text-align: right"><strong>المبلغ الإجمالي:</strong> {total_amount}</p><p style="text-align: right">يرجى المراجعة ومعالجة الدفع قبل تاريخ الاستحقاق.</p><p style="text-align: right"> مع أطيب التحيات،<br>{user_name}</p>',
                    'en' => "<p>Hello {client},</p>\n<p>Your invoice has been sent to you.</p>\n<h3>Details</h3>\n<p><strong>Client:</strong> {client}</p>\n<p><strong>Case:</strong> {case}</p>\n<p><strong>Invoice Date:</strong> {invoice_date}</p>\n<p><strong>Due Date:</strong> {due_date}</p>\n<p><strong>Total Amount:</strong> {total_amount}</p>\n<p>Please review and process the payment by the due date.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::TEAM_MEMBER_CREATED,
                'name' => ['ar' => 'تم إنشاء ملف تعريف عضو الفريق', 'en' => 'Team Member Profile Created'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'تم إنشاء ملف تعريف عضو فريق جديد: {name}', 'en' => 'New Team Member Profile Created: {name}'],
                'content' => [
                    'ar' => '<p style="text-align: right">مرحبًا {name}،</p><p style="text-align: right">تم إنشاء ملفك كعضو فريق جديد في النظام.</p><h3 style="text-align: right">التفاصيل</h3><p style="text-align: right"><strong>الاسم:</strong> {name}</p><p style="text-align: right"><strong>البريد الإلكتروني:</strong> {email}</p><p style="text-align: right"><strong>كلمة المرور:</strong> {password}</p><p style="text-align: right"><strong>الدور:</strong> {role}</p><p style="text-align: right">يرجى مراجعة وتأكيد ملف عضو الفريق في النظام.</p><p style="text-align: right"> مع خالص التحية،<br>{user_name}</p>',
                    'en' => "<p>Hello {name},</p>\n<p>Your new team member profile has been created in the system.</p>\n<h3>Details</h3>\n<p><strong>Name:</strong> {name}</p>\n<p><strong>Email:</strong> {email}</p>\n<p><strong>Password:</strong> {password}</p>\n<p><strong>Role:</strong> {role}</p>\n<p>Please review and confirm your team member profile in the system.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::CLIENT_CREATED,
                'name' => ['ar' => 'تم إنشاء ملف تعريف العميل', 'en' => 'Client Profile Created'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'تم إنشاء ملفك الشخصي: {name}', 'en' => 'Your Profile Created: {name}'],
                'content' => [
                    'ar' => "<p style=\"direction: rtl; text-align: right;\">مرحبًا {name}،</p>\n<p style=\"direction: rtl; text-align: right;\">تم إنشاء ملفك الشخصي في النظام.</p>\n<h3 style=\"direction: rtl; text-align: right;\">التفاصيل</h3>\n<p style=\"direction: rtl; text-align: right;\"><strong>الاسم:</strong> {name}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>البريد الإلكتروني:</strong> {email}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>كلمة المرور:</strong> {password}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>رقم الهاتف:</strong> {phone_no}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>تاريخ الميلاد:</strong> {dob}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>نوع العميل:</strong> {client_type}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>المعرف الضريبي:</strong> {tax_id}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>معدل الضريبة:</strong> {tax_rate}</p>\n<p style=\"direction: rtl; text-align: right;\">يرجى مراجعة وتأكيد ملفك الشخصي في النظام.</p>\n<div style=\"text-align: left; margin-top: 30px;\">\n    مع أطيب التحيات،<br>\n    {user_name}\n</div>",
                    'en' => '<p>Hello {name},</p><p>Your profile has been created in the system.</p><h3>Details</h3><p><strong>Name:</strong> {name}</p><p><strong>Email:</strong> {email}</p><p><strong>Password:</strong> {password}</p><p><strong>Phone Number:</strong> {phone_no}</p><p><strong>Client Type:</strong> {client_type}</p><p></p><p>Please review and confirm your profile in the system.</p><p> Best regards,<br>{user_name}</p>',
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::CASE_CREATED,
                'name' => ['ar' => 'تم إنشاء القضية', 'en' => 'Case Created'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'تم إنشاء قضية جديدة: {case_id}', 'en' => 'New Case Created: {case_id}'],
                'content' => [
                    'ar' => '<p style="text-align: right">عزيزي/عزيزتي {client}،</p><p style="text-align: right">يسعدنا إبلاغك بأنه تم إنشاء قضية جديدة في نظامنا نيابة عنك.</p><h3 style="text-align: right">تفاصيل القضية</h3><p style="text-align: right"><strong>رقم القضية:</strong> {case_id}</p><p style="text-align: right"><strong>العنوان:</strong> {title}</p><p style="text-align: right"><strong>العميل:</strong> {client}</p><p style="text-align: right"><strong>النوع:</strong> {type}</p><p style="text-align: right"><strong>تاريخ التقديم:</strong> {filling_date}</p><p style="text-align: right"><strong>تاريخ الإكمال المتوقع:</strong> {expected_complete_date}</p><p style="text-align: right">يمكنك عرض التفاصيل الكاملة ورفع أي وثائق ذات صلة بالدخول إلى حسابك على {app_name}. سيتواصل فريقنا معك لتقديم التحديثات مع تقدم القضية.</p><p style="text-align: right"> مع أطيب التحيات،<br>{user_name}</p>',
                    'en' => "<div style=\"text-align: left; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.7;\">\n    <p>Dear {client},</p>\n\n    <p>We are pleased to inform you that a new case has been created in our system on your behalf.</p>\n\n    <h3>Case Details</h3>\n\n    <p><strong>Case ID:</strong> {case_id}</p>\n    <p><strong>Title:</strong> {title}</p>\n    <p><strong>Client:</strong> {client}</p>\n    <p><strong>Type:</strong> {type}</p>\n    <p><strong>Filing Date:</strong> {filling_date}</p>\n    <p><strong>Expected Completion Date:</strong> {expected_complete_date}</p>\n\n    <p>You can view the full details and upload any relevant documents by logging into your {app_name} account. Our team will contact you with updates as the case progresses.</p>\n\n    <div style=\"margin-top: 30px;\">\n        Best regards,<br>\n        {user_name}\n    </div>\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::HEARING_CREATED,
                'name' => ['ar' => 'تم جدولة جلسة', 'en' => 'Session Scheduled'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'جلسة استماع جديدة مجدولة للقضية {case_number}', 'en' => 'New Hearing Scheduled for Case {case_number}'],
                'content' => [
                    'ar' => '<p style="text-align: right">مرحبًا {client_name}،</p><h3 style="text-align: right">تفاصيل الجلسة</h3><p style="text-align: right"><strong>رقم الجلسة:</strong> {hearing_number}</p><p style="text-align: right"><strong>نوع الجلسة:</strong> {type}</p><p style="text-align: right"><strong>الوقت:</strong> {hearing_time}</p><p style="text-align: right"><strong>التاريخ:</strong> {hearing_date}</p><p style="text-align: right"><strong>اسم المحكمة:</strong> {court_name}</p><p style="text-align: right"><strong>المدة:</strong> {duration}</p><p style="text-align: right">يرجى تسجيل الدخول إلى النظام لعرض تفاصيل {type} الكاملة والبدء في العمل على هذا {type}.</p><p style="text-align: right"> مع أطيب التحيات،<br>{user_name}</p>',
                    'en' => "<p>Hello {client_name},</p>\n<p><strong>Opportunity Name:</strong> {hearing_number}</p>\n<p><strong>Hearing Type:</strong> {type}</p>\n<p><strong>Time:</strong> {hearing_time}</p>\n<p><strong>Date:</strong> {hearing_date}</p>\n<p><strong>Court Name:</strong> {court_name}</p>\n<p><strong>Duration:</strong> {duration}</p>\n<p>Please log into the system to view full {type} details and begin working on this {type}.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::JUDGE_CREATED,
                'name' => ['ar' => 'تم إنشاء ملف تعريف القاضي', 'en' => 'Judge Profile Created'],
                'from' => ['ar' => 'Laravel', 'en' => 'Sard'],
                'subject' => ['ar' => 'تعيين قاضٍ جديد: {judge_name}', 'en' => 'New Judge Appointed: {judge_name}'],
                'content' => [
                    'ar' => "<p style=\"direction: rtl; text-align: right;\">مرحبًا {judge_name}،</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>اسم القاضي:</strong> {judge_name}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>البريد الإلكتروني:</strong> {email}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>اسم المحكمة:</strong> {court_name}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>رقم الاتصال:</strong> {contact_no}</p>\n<p style=\"direction: rtl; text-align: right;\">يرجى تسجيل الدخول إلى النظام لعرض التفاصيل الكاملة حول تعيينك ومعلومات القضية ذات الصلة.</p>\n<div style=\"text-align: left; margin-top: 30px;\">\n    مع أطيب التحيات،<br>\n    {user_name}\n</div>",
                    'en' => "<p>Hello {judge_name},</p>\n<p><strong>Judge Name:</strong> {judge_name}</p>\n<p><strong>Email:</strong> {email}</p>\n<p><strong>Court Name:</strong> {court_name}</p>\n<p><strong>Contact Number:</strong> {contact_no}</p>\n<p>Please log into the system to view full details about your appointment and related case information.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::COURT_CREATED,
                'name' => ['ar' => 'تم إنشاء المحكمة', 'en' => 'Court Established'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'إنشاء محكمة جديدة: {name}', 'en' => 'New Court Established: {name}'],
                'content' => [
                    'ar' => "<div dir=\"rtl\" style=\"text-align: right; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.8;\">\n    <p>مرحبًا،</p>\n\n    <h3>التفاصيل</h3>\n\n    <p><strong>اسم المحكمة:</strong> {name}</p>\n    <p><strong>النوع:</strong> {type}</p>\n    <p><strong>البريد الإلكتروني:</strong> {email}</p>\n    <p><strong>العنوان:</strong> {address}</p>\n\n    <p>يرجى تسجيل الدخول إلى النظام لعرض التفاصيل الكاملة حول المحكمة الجديدة ومعلومات القضية ذات الصلة.</p>\n\n    <div style=\"margin-top: 30px;\">\n        مع أطيب التحيات،<br>\n        {user_name}\n    </div>\n</div>",
                    'en' => '<p>Hello,</p><p><strong>Court Name:</strong> {name}</p><p><strong>Type:</strong> {type}</p><p><strong>Email:</strong> {email}</p><p><strong>Address:</strong> {address}</p><p>Please log into the system to view full details about the new court and related case information.</p><p>Best regards,<br>{user_name}</p>',
                ],
                'user_id' => 1,
            ],
            [
                'type' => EmailTemplateName::TASK_CREATED,
                'name' => ['ar' => 'تم تعيين المهمة', 'en' => 'Task Assigned'],
                'from' => ['ar' => 'سرد', 'en' => 'Sard'],
                'subject' => ['ar' => 'مهمة جديدة تم تعيينها: {title}', 'en' => 'New Task Assigned: {title}'],
                'content' => [
                    'ar' => "<p style=\"direction: rtl; text-align: right;\">مرحبًا {assigned_to}،</p>\n<p style=\"direction: rtl; text-align: right;\">تم تعيين مهمة جديدة لك.</p>\n<h3 style=\"direction: rtl; text-align: right;\">التفاصيل</h3>\n<p style=\"direction: rtl; text-align: right;\"><strong>عنوان المهمة:</strong> {title}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>الأولوية:</strong> {priority}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>تاريخ الاستحقاق:</strong> {due_date}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>القضية:</strong> {case}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>تم التعيين لـ:</strong> {assigned_to}</p>\n<p style=\"direction: rtl; text-align: right;\"><strong>نوع المهمة:</strong> {task_type}</p>\n<p style=\"direction: rtl; text-align: right;\">يرجى تأكيد استلام هذه المهمة في النظام.</p>\n<div style=\"text-align: left; margin-top: 30px;\">\n    مع أطيب التحيات،<br>\n    {user_name}\n</div>",
                    'en' => "<p>Hello {assigned_to},</p>\n<p>A new task has been assigned to you.</p>\n<h3>Details</h3>\n<p><strong>Task Title:</strong> {title}</p>\n<p><strong>Priority:</strong> {priority}</p>\n<p><strong>Due Date:</strong> {due_date}</p>\n<p><strong>Case:</strong> {case}</p>\n<p><strong>Assigned To:</strong> {assigned_to}</p>\n<p><strong>Task Type:</strong> {task_type}</p>\n<p>Please confirm receipt of this task in the system.</p>\n<div style=\"text-align: right; margin-top: 30px;\">\n    Best regards,<br>\n    {user_name}\n</div>",
                ],
                'user_id' => 1,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}
