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
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Invoice Created',
                    'ar' => 'تم إنشاء الفاتورة',
                ],
                'subject' => [
                    'en' => 'Your Invoice Created: {client}',
                    'ar' => 'تم إنشاء فاتورة جديدة: {client}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {client},</p>
<p>Your invoice has been created in the system.</p>
<h3>Details</h3>
<p><strong>Client:</strong> {client}</p>
<p><strong>Case:</strong> {case}</p>
<p><strong>Invoice Date:</strong> {invoice_date}</p>
<p><strong>Due Date:</strong> {due_date}</p>
<p><strong>Total Amount:</strong> {total_amount}</p>
<p>Please review and confirm your invoice in the system.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {client}،</p>
<p style="direction: rtl; text-align: right;">تم إنشاء فاتورتك في النظام.</p>
<h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
<p style="direction: rtl; text-align: right;"><strong>العميل:</strong> {client}</p>
<p style="direction: rtl; text-align: right;"><strong>القضية:</strong> {case}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الفاتورة:</strong> {invoice_date}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الاستحقاق:</strong> {due_date}</p>
<p style="direction: rtl; text-align: right;"><strong>المبلغ الإجمالي:</strong> {total_amount}</p>
<p style="direction: rtl; text-align: right;">يرجى مراجعة وتأكيد فاتورتك في النظام.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::INVOICE_SENT,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Invoice Sent',
                    'ar' => 'تم إرسال الفاتورة',
                ],
                'subject' => [
                    'en' => 'Your Invoice Sent: {client}',
                    'ar' => 'تم إرسال فاتورتك: {client}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {client},</p>
<p>Your invoice has been sent to you.</p>
<h3>Details</h3>
<p><strong>Client:</strong> {client}</p>
<p><strong>Case:</strong> {case}</p>
<p><strong>Invoice Date:</strong> {invoice_date}</p>
<p><strong>Due Date:</strong> {due_date}</p>
<p><strong>Total Amount:</strong> {total_amount}</p>
<p>Please review and process the payment by the due date.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {client}،</p>
<p style="direction: rtl; text-align: right;">تم إرسال فاتورتك إليك.</p>
<h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
<p style="direction: rtl; text-align: right;"><strong>العميل:</strong> {client}</p>
<p style="direction: rtl; text-align: right;"><strong>القضية:</strong> {case}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الفاتورة:</strong> {invoice_date}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الاستحقاق:</strong> {due_date}</p>
<p style="direction: rtl; text-align: right;"><strong>المبلغ الإجمالي:</strong> {total_amount}</p>
<p style="direction: rtl; text-align: right;">يرجى المراجعة ومعالجة الدفع قبل تاريخ الاستحقاق.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::TEAM_MEMBER_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Team Member Profile Created',
                    'ar' => 'تم إنشاء ملف تعريف عضو الفريق',
                ],
                'subject' => [
                    'en' => 'New Team Member Profile Created: {name}',
                    'ar' => 'تم إنشاء ملف تعريف عضو فريق جديد: {name}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {name},</p>
<p>Your new team member profile has been created in the system.</p>
<h3>Details</h3>
<p><strong>Name:</strong> {name}</p>
<p><strong>Email:</strong> {email}</p>
<p><strong>Password:</strong> {password}</p>
<p><strong>Role:</strong> {role}</p>
<p>Please review and confirm your team member profile in the system.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {name}،</p>
<p style="direction: rtl; text-align: right;">تم إنشاء ملف تعريف عضو فريق جديد لك في النظام.</p>
<h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
<p style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {name}</p>
<p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
<p style="direction: rtl; text-align: right;"><strong>كلمة المرور:</strong> {password}</p>
<p style="direction: rtl; text-align: right;"><strong>الدور:</strong> {role}</p>
<p style="direction: rtl; text-align: right;">يرجى مراجعة وتأكيد ملف تعريف عضو الفريق الخاص بك في النظام.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                ],
            ],
            [
                'type' => EmailTemplateName::CLIENT_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Client Profile Created',
                    'ar' => 'تم إنشاء ملف تعريف العميل',
                ],
                'subject' => [
                    'en' => 'Your Profile Created: {name}',
                    'ar' => 'تم إنشاء ملفك الشخصي: {name}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {name},</p>
<p>Your profile has been created in the system.</p>
<h3>Details</h3>
<p><strong>Name:</strong> {name}</p>
<p><strong>Email:</strong> {email}</p>
<p><strong>Password:</strong> {password}</p>
<p><strong>Phone Number:</strong> {phone_no}</p>
<p><strong>Date of Birth:</strong> {dob}</p>
<p><strong>Client Type:</strong> {client_type}</p>
<p><strong>Tax ID:</strong> {tax_id}</p>
<p><strong>Tax Rate:</strong> {tax_rate}</p>
<p>Please review and confirm your profile in the system.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {name}،</p>
<p style="direction: rtl; text-align: right;">تم إنشاء ملفك الشخصي في النظام.</p>
<h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
<p style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {name}</p>
<p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
<p style="direction: rtl; text-align: right;"><strong>كلمة المرور:</strong> {password}</p>
<p style="direction: rtl; text-align: right;"><strong>رقم الهاتف:</strong> {phone_no}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الميلاد:</strong> {dob}</p>
<p style="direction: rtl; text-align: right;"><strong>نوع العميل:</strong> {client_type}</p>
<p style="direction: rtl; text-align: right;"><strong>المعرف الضريبي:</strong> {tax_id}</p>
<p style="direction: rtl; text-align: right;"><strong>معدل الضريبة:</strong> {tax_rate}</p>
<p style="direction: rtl; text-align: right;">يرجى مراجعة وتأكيد ملفك الشخصي في النظام.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::CASE_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Case Created',
                    'ar' => 'تم إنشاء القضية',
                ],
                'subject' => [
                    'en' => 'New Case Created: {case_id}',
                    'ar' => 'تم إنشاء قضية جديدة: {case_id}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Dear {client},</p>
<p>We are pleased to inform you that a new case has been created in our system on your behalf.</p>
<h3>Case Details</h3>
<p><strong>Case ID:</strong> {case_id}</p>
<p><strong>Title:</strong> {title}</p>
<p><strong>Client:</strong> {client}</p>
<p><strong>Type:</strong> {type}</p>
<p><strong>Filing Date:</strong> {filling_date}</p>
<p><strong>Expected Completion Date:</strong> {expected_complete_date}</p>
<p>You can view the full details and upload any relevant documents by logging into your {app_name} account. Our team will contact you with updates as the case progresses.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p>عزيزي/عزيزتي {client}،</p>
<p>يسعدنا إبلاغك بأنه تم إنشاء قضية جديدة في نظامنا نيابة عنك.</p>
<h3>تفاصيل القضية</h3>
<p><strong>رقم القضية:</strong> {case_id}</p>
<p><strong>العنوان:</strong> {title}</p>
<p><strong>العميل:</strong> {client}</p>
<p><strong>النوع:</strong> {type}</p>
<p><strong>تاريخ التقديم:</strong> {filling_date}</p>
<p><strong>تاريخ الإكمال المتوقع:</strong> {expected_complete_date}</p>
<p>يمكنك عرض التفاصيل الكاملة ورفع أي وثائق ذات صلة بالدخول إلى حسابك على {app_name}. سيتواصل فريقنا معك لتقديم التحديثات مع تقدم القضية.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::HEARING_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Hearing Scheduled',
                    'ar' => 'تم جدولة جلسة استماع',
                ],
                'subject' => [
                    'en' => 'New Hearing Scheduled for Case {case_number}',
                    'ar' => 'جلسة استماع جديدة مجدولة للقضية {case_number}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {client_name},</p>
<p><strong>Opportunity Name:</strong> {hearing_number}</p>
<p><strong>Hearing Type:</strong> {type}</p>
<p><strong>Time:</strong> {hearing_time}</p>
<p><strong>Date:</strong> {hearing_date}</p>
<p><strong>Court Name:</strong> {court_name}</p>
<p><strong>Duration:</strong> {duration}</p>
<p>Please log into the system to view full {type} details and begin working on this {type}.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {client_name}،</p>
<p style="direction: rtl; text-align: right;"><strong>اسم الفرصة:</strong> {hearing_number}</p>
<p style="direction: rtl; text-align: right;"><strong>نوع الجلسة:</strong> {type}</p>
<p style="direction: rtl; text-align: right;"><strong>الوقت:</strong> {hearing_time}</p>
<p style="direction: rtl; text-align: right;"><strong>التاريخ:</strong> {hearing_date}</p>
<p style="direction: rtl; text-align: right;"><strong>اسم المحكمة:</strong> {court_name}</p>
<p style="direction: rtl; text-align: right;"><strong>المدة:</strong> {duration}</p>
<p style="direction: rtl; text-align: right;">يرجى تسجيل الدخول إلى النظام لعرض تفاصيل {type} الكاملة والبدء في العمل على هذا {type}.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::JUDGE_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Judge Profile Created',
                    'ar' => 'تم إنشاء ملف تعريف القاضي',
                ],
                'subject' => [
                    'en' => 'New Judge Appointed: {judge_name}',
                    'ar' => 'تعيين قاضٍ جديد: {judge_name}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {judge_name},</p>
<p><strong>Judge Name:</strong> {judge_name}</p>
<p><strong>Email:</strong> {email}</p>
<p><strong>Court Name:</strong> {court_name}</p>
<p><strong>Contact Number:</strong> {contact_no}</p>
<p>Please log into the system to view full details about your appointment and related case information.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {judge_name}،</p>
<p style="direction: rtl; text-align: right;"><strong>اسم القاضي:</strong> {judge_name}</p>
<p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
<p style="direction: rtl; text-align: right;"><strong>اسم المحكمة:</strong> {court_name}</p>
<p style="direction: rtl; text-align: right;"><strong>رقم الاتصال:</strong> {contact_no}</p>
<p style="direction: rtl; text-align: right;">يرجى تسجيل الدخول إلى النظام لعرض التفاصيل الكاملة حول تعيينك ومعلومات القضية ذات الصلة.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                ],
            ],
            [
                'type' => EmailTemplateName::COURT_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Court Established',
                    'ar' => 'تم إنشاء المحكمة',
                ],
                'subject' => [
                    'en' => 'New Court Established: {name}',
                    'ar' => 'إنشاء محكمة جديدة: {name}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello,</p>
<p><strong>Court Name:</strong> {name}</p>
<p><strong>Type:</strong> {type}</p>
<p><strong>Phone Number:</strong> {phoneno}</p>
<p><strong>Email:</strong> {email}</p>
<p><strong>Jurisdiction:</strong> {jurisdiction}</p>
<p><strong>Address:</strong> {address}</p>
<p>Please log into the system to view full details about the new court and related case information.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا،</p>
<p style="direction: rtl; text-align: right;"><strong>اسم المحكمة:</strong> {name}</p>
<p style="direction: rtl; text-align: right;"><strong>النوع:</strong> {type}</p>
<p style="direction: rtl; text-align: right;"><strong>رقم الهاتف:</strong> {phoneno}</p>
<p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
<p style="direction: rtl; text-align: right;"><strong>الاختصاص:</strong> {jurisdiction}</p>
<p style="direction: rtl; text-align: right;"><strong>العنوان:</strong> {address}</p>
<p style="direction: rtl; text-align: right;">يرجى تسجيل الدخول إلى النظام لعرض التفاصيل الكاملة حول المحكمة الجديدة ومعلومات القضية ذات الصلة.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
            [
                'type' => EmailTemplateName::TASK_CREATED,
                'from' => [
                    'en' => config('app.name'),
                    'ar' => config('app.name_ar')
                ],
                'name' => [
                    'en' => 'Task Assigned',
                    'ar' => 'تم تعيين المهمة',
                ],
                'subject' => [
                    'en' => 'New Task Assigned: {title}',
                    'ar' => 'مهمة جديدة تم تعيينها: {title}',
                ],
                'content' => [
                    'en' => <<<'HTML'
<p>Hello {assigned_to},</p>
<p>A new task has been assigned to you.</p>
<h3>Details</h3>
<p><strong>Task Title:</strong> {title}</p>
<p><strong>Priority:</strong> {priority}</p>
<p><strong>Due Date:</strong> {due_date}</p>
<p><strong>Case:</strong> {case}</p>
<p><strong>Assigned To:</strong> {assigned_to}</p>
<p><strong>Task Type:</strong> {task_type}</p>
<p>Please confirm receipt of this task in the system.</p>
<div style="text-align: right; margin-top: 30px;">
    Best regards,<br>
    {user_name}
</div>
HTML
                    ,
                    'ar' => <<<'HTML'
<p style="direction: rtl; text-align: right;">مرحبًا {assigned_to}،</p>
<p style="direction: rtl; text-align: right;">تم تعيين مهمة جديدة لك.</p>
<h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
<p style="direction: rtl; text-align: right;"><strong>عنوان المهمة:</strong> {title}</p>
<p style="direction: rtl; text-align: right;"><strong>الأولوية:</strong> {priority}</p>
<p style="direction: rtl; text-align: right;"><strong>تاريخ الاستحقاق:</strong> {due_date}</p>
<p style="direction: rtl; text-align: right;"><strong>القضية:</strong> {case}</p>
<p style="direction: rtl; text-align: right;"><strong>تم التعيين لـ:</strong> {assigned_to}</p>
<p style="direction: rtl; text-align: right;"><strong>نوع المهمة:</strong> {task_type}</p>
<p style="direction: rtl; text-align: right;">يرجى تأكيد استلام هذه المهمة في النظام.</p>
<div style="text-align: left; margin-top: 30px;">
    مع أطيب التحيات،<br>
    {user_name}
</div>
HTML
                    ,
                ],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
