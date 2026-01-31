<?php

namespace Database\Seeders;

use App\EmailTemplateName;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateLang;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $supportedLanguages = ['en', 'ar'];
        $langCodes = $supportedLanguages;

        $templates = [
            [
                'name' => EmailTemplateName::NEW_CASE->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Case Created: {case_number}',
                        'content' => 'A new case "{case_number}" has been created for client {client_name}. Case type: {case_type}. Created by: {created_by}.'
                    ],
                    'ar' => [
                        'title' => 'تم إنشاء قضية جديدة: {case_number}',
                        'content' => 'تم إنشاء قضية جديدة "{case_number}" للعميل {client_name}. نوع القضية: {case_type}. تم الإنشاء بواسطة: {created_by}.'
                    ],
                ]
            ],

            [
                'name' => EmailTemplateName::NEW_CLIENT->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Client Added: {client_name}',
                        'content' => 'A new client "{client_name}" has been added to the system. Client type: {client_type}. Email: {email}.'
                    ],
                    'ar' => [
                        'title' => 'تمت إضافة عميل جديد: {client_name}',
                        'content' => 'تمت إضافة عميل جديد "{client_name}" إلى النظام. نوع العميل: {client_type}. البريد الإلكتروني: {email}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_TASK->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Task Assigned: {task_title}',
                        'content' => 'You have been assigned a new task "{task_title}". Priority: {priority}. Due date: {due_date}. Assigned to: {assigned_to}.'
                    ],
                    'ar' => [
                        'title' => 'تم تعيين مهمة جديدة: {task_title}',
                        'content' => 'تم تعيين مهمة جديدة لك بعنوان "{task_title}". الأولوية: {priority}. تاريخ الاستحقاق: {due_date}. تم التعيين إلى: {assigned_to}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_HEARING->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Hearing Scheduled: {case_number}',
                        'content' => 'A new hearing has been scheduled for case {case_number}. Date: {hearing_date}. Court: {court}. Judge: {judge}.'
                    ],
                    'ar' => [
                        'title' => 'تم تحديد جلسة جديدة: {case_number}',
                        'content' => 'تم تحديد جلسة جديدة للقضية {case_number}. التاريخ: {hearing_date}. المحكمة: {court}. القاضي: {judge}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_INVOICE->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Invoice Created: {invoice_number}',
                        'content' => 'A new invoice {invoice_number} has been created for {client_name}. Amount: {amount}. Due date: {due_date}.'
                    ],
                    'ar' => [
                        'title' => 'تم إنشاء فاتورة جديدة: {invoice_number}',
                        'content' => 'تم إنشاء فاتورة جديدة {invoice_number} للعميل {client_name}. المبلغ: {amount}. تاريخ الاستحقاق: {due_date}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::INVOICE_SENT->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'Invoice Sent: {invoice_number}',
                        'content' => 'Invoice {invoice_number} has been sent to {client_name}. Amount: {amount}. Sent on: {sent_date}.'
                    ],
                    'ar' => [
                        'title' => 'تم إرسال الفاتورة: {invoice_number}',
                        'content' => 'تم إرسال الفاتورة {invoice_number} إلى {client_name}. المبلغ: {amount}. تم الإرسال في: {sent_date}.'
                    ],
                ]
            ],

            [
                'name' => EmailTemplateName::NEW_COURT->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Court Added: {court_name}',
                        'content' => 'A new court "{court_name}" has been added to the system. Type: {court_type}. Location: {location}.'
                    ],
                    'ar' => [
                        'title' => 'تمت إضافة محكمة جديدة: {court_name}',
                        'content' => 'تمت إضافة محكمة جديدة "{court_name}" إلى النظام. النوع: {court_type}. الموقع: {location}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_JUDGE->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Judge Added: {judge_name}',
                        'content' => 'A new judge "{judge_name}" has been added to the system. Court: {court}. Specialization: {specialization}.'
                    ],
                    'ar' => [
                        'title' => 'تمت إضافة قاضٍ جديد: {judge_name}',
                        'content' => 'تمت إضافة القاضي الجديد "{judge_name}" إلى النظام. المحكمة: {court}. التخصص: {specialization}.'
                    ],
                ]
            ],

            // [
            //     'name' => 'New License',
            //     'type' => 'slack',
            //     'translations' => [
            //         'en' => [
            //             'title' => 'New Professional License: {license_number}',
            //             'content' => 'A new professional license "{license_number}" has been added. Type: {license_type}. Issuing Authority: {issuing_authority}. Expiry: {expiry_date}.'
            //         ],
            //         'es' => [
            //             'title' => 'Nueva licencia profesional: {license_number}',
            //             'content' => 'Se ha agregado una nueva licencia profesional "{license_number}". Tipo: {license_type}. Autoridad emisora: {issuing_authority}. Vencimiento: {expiry_date}.'
            //         ],
            //         'ar' => [
            //             'title' => 'تمت إضافة ترخيص مهني جديد: {license_number}',
            //             'content' => 'تمت إضافة الترخيص المهني الجديد "{license_number}". النوع: {license_type}. جهة الإصدار: {issuing_authority}. تاريخ الانتهاء: {expiry_date}.'
            //         ],
            //         'da' => [
            //             'title' => 'Ny professionel licens: {license_number}',
            //             'content' => 'En ny professionel licens "{license_number}" er blevet tilføjet. Type: {license_type}. Udstedende myndighed: {issuing_authority}. Udløbsdato: {expiry_date}.'
            //         ],
            //         'de' => [
            //             'title' => 'Neue Berufslizenz: {license_number}',
            //             'content' => 'Eine neue Berufslizenz "{license_number}" wurde hinzugefügt. Typ: {license_type}. Ausstellende Behörde: {issuing_authority}. Ablaufdatum: {expiry_date}.'
            //         ],
            //         'fr' => [
            //             'title' => 'Nouvelle licence professionnelle : {license_number}',
            //             'content' => 'Une nouvelle licence professionnelle "{license_number}" a été ajoutée. Type : {license_type}. Autorité émettrice : {issuing_authority}. Expiration : {expiry_date}.'
            //         ],
            //         'he' => [
            //             'title' => 'נוספה רישיון מקצועי חדש: {license_number}',
            //             'content' => 'הרישיון המקצועי החדש "{license_number}" נוסף למערכת. סוג: {license_type}. רשות מנפיקה: {issuing_authority}. תוקף: {expiry_date}.'
            //         ],
            //         'it' => [
            //             'title' => 'Nuova licenza professionale: {license_number}',
            //             'content' => 'È stata aggiunta una nuova licenza professionale "{license_number}". Tipo: {license_type}. Autorità emittente: {issuing_authority}. Scadenza: {expiry_date}.'
            //         ],
            //         'ja' => [
            //             'title' => '新しい職業ライセンスが追加されました: {license_number}',
            //             'content' => '新しい職業ライセンス「{license_number}」が追加されました。種類: {license_type}。発行機関: {issuing_authority}。有効期限: {expiry_date}。'
            //         ],
            //         'nl' => [
            //             'title' => 'Nieuwe professionele licentie: {license_number}',
            //             'content' => 'Een nieuwe professionele licentie "{license_number}" is toegevoegd. Type: {license_type}. Uitgevende autoriteit: {issuing_authority}. Vervaldatum: {expiry_date}.'
            //         ],
            //         'pl' => [
            //             'title' => 'Dodano nową licencję zawodową: {license_number}',
            //             'content' => 'Nowa licencja zawodowa "{license_number}" została dodana. Typ: {license_type}. Organ wydający: {issuing_authority}. Data wygaśnięcia: {expiry_date}.'
            //         ],
            //         'pt' => [
            //             'title' => 'Nova licença profissional: {license_number}',
            //             'content' => 'Uma nova licença profissional "{license_number}" foi adicionada. Tipo: {license_type}. Autoridade emissora: {issuing_authority}. Validade: {expiry_date}.'
            //         ],
            //         'pt-br' => [
            //             'title' => 'Nova licença profissional: {license_number}',
            //             'content' => 'Uma nova licença profissional "{license_number}" foi adicionada. Tipo: {license_type}. Autoridade emissora: {issuing_authority}. Validade: {expiry_date}.'
            //         ],
            //         'ru' => [
            //             'title' => 'Добавлена новая профессиональная лицензия: {license_number}',
            //             'content' => 'Добавлена новая профессиональная лицензия "{license_number}". Тип: {license_type}. Выдавший орган: {issuing_authority}. Срок действия: {expiry_date}.'
            //         ],
            //         'tr' => [
            //             'title' => 'Yeni Profesyonel Lisans: {license_number}',
            //             'content' => 'Yeni bir profesyonel lisans "{license_number}" eklendi. Tür: {license_type}. Veren Otorite: {issuing_authority}. Bitiş Tarihi: {expiry_date}.'
            //         ],
            //         'zh' => [
            //             'title' => '已添加新职业许可证: {license_number}',
            //             'content' => '新的职业许可证 "{license_number}" 已添加。类型：{license_type}。签发机构：{issuing_authority}。到期日：{expiry_date}。'
            //         ],
            //     ]
            // ],
            //
            // [
            //     'name' => 'New Regulatory Body',
            //     'type' => 'slack',
            //     'translations' => [
            //         'en' => [
            //             'title' => 'New Regulatory Body: {body_name}',
            //             'content' => 'A new regulatory body "{body_name}" has been added. Jurisdiction: {jurisdiction}. Contact: {contact_info}.'
            //         ],
            //         'es' => [
            //             'title' => 'Nuevo organismo regulador: {body_name}',
            //             'content' => 'Se ha agregado un nuevo organismo regulador "{body_name}". Jurisdicción: {jurisdiction}. Contacto: {contact_info}.'
            //         ],
            //         'ar' => [
            //             'title' => 'تمت إضافة جهة تنظيمية جديدة: {body_name}',
            //             'content' => 'تمت إضافة الجهة التنظيمية الجديدة "{body_name}". الولاية القضائية: {jurisdiction}. جهة الاتصال: {contact_info}.'
            //         ],
            //         'da' => [
            //             'title' => 'Ny tilsynsmyndighed tilføjet: {body_name}',
            //             'content' => 'En ny tilsynsmyndighed "{body_name}" er blevet tilføjet. Jurisdiktion: {jurisdiction}. Kontakt: {contact_info}.'
            //         ],
            //         'de' => [
            //             'title' => 'Neue Aufsichtsbehörde hinzugefügt: {body_name}',
            //             'content' => 'Eine neue Aufsichtsbehörde "{body_name}" wurde hinzugefügt. Zuständigkeit: {jurisdiction}. Kontakt: {contact_info}.'
            //         ],
            //         'fr' => [
            //             'title' => 'Nouvel organisme de régulation : {body_name}',
            //             'content' => 'Un nouvel organisme de régulation "{body_name}" a été ajouté. Juridiction : {jurisdiction}. Contact : {contact_info}.'
            //         ],
            //         'he' => [
            //             'title' => 'נוסף גוף רגולטורי חדש: {body_name}',
            //             'content' => 'הגוף הרגולטורי החדש "{body_name}" נוסף למערכת. סמכות שיפוט: {jurisdiction}. איש קשר: {contact_info}.'
            //         ],
            //         'it' => [
            //             'title' => 'Nuovo ente regolatore: {body_name}',
            //             'content' => 'È stato aggiunto un nuovo ente regolatore "{body_name}". Giurisdizione: {jurisdiction}. Contatto: {contact_info}.'
            //         ],
            //         'ja' => [
            //             'title' => '新しい規制機関が追加されました: {body_name}',
            //             'content' => '新しい規制機関「{body_name}」が追加されました。管轄: {jurisdiction}。連絡先: {contact_info}。'
            //         ],
            //         'nl' => [
            //             'title' => 'Nieuwe regelgevende instantie toegevoegd: {body_name}',
            //             'content' => 'Een nieuwe regelgevende instantie "{body_name}" is toegevoegd. Rechtsgebied: {jurisdiction}. Contact: {contact_info}.'
            //         ],
            //         'pl' => [
            //             'title' => 'Dodano nowy organ regulacyjny: {body_name}',
            //             'content' => 'Nowy organ regulacyjny "{body_name}" został dodany. Jurysdykcja: {jurisdiction}. Kontakt: {contact_info}.'
            //         ],
            //         'pt' => [
            //             'title' => 'Novo órgão regulador: {body_name}',
            //             'content' => 'Um novo órgão regulador "{body_name}" foi adicionado. Jurisdição: {jurisdiction}. Contato: {contact_info}.'
            //         ],
            //         'pt-br' => [
            //             'title' => 'Novo órgão regulador: {body_name}',
            //             'content' => 'Um novo órgão regulador "{body_name}" foi adicionado. Jurisdição: {jurisdiction}. Contato: {contact_info}.'
            //         ],
            //         'ru' => [
            //             'title' => 'Добавлен новый регулирующий орган: {body_name}',
            //             'content' => 'Новый регулирующий орган "{body_name}" был добавлен. Юрисдикция: {jurisdiction}. Контакт: {contact_info}.'
            //         ],
            //         'tr' => [
            //             'title' => 'Yeni Düzenleyici Kurum: {body_name}',
            //             'content' => 'Yeni bir düzenleyici kurum "{body_name}" eklendi. Yargı Yetkisi: {jurisdiction}. İletişim: {contact_info}.'
            //         ],
            //         'zh' => [
            //             'title' => '已添加新监管机构: {body_name}',
            //             'content' => '新的监管机构 "{body_name}" 已添加。管辖权：{jurisdiction}。联系方式：{contact_info}。'
            //         ],
            //     ]
            // ],
            //
            // [
            //     'name' => 'New CLE Record',
            //     'type' => 'slack',
            //     'translations' => [
            //         'en' => [
            //             'title' => 'New CLE Record: {course_title}',
            //             'content' => 'A new CLE record "{course_title}" has been added. Credits: {credits_earned}. Completion Date: {completion_date}. Provider: {provider}.'
            //         ],
            //         'es' => [
            //             'title' => 'Nuevo registro CLE: {course_title}',
            //             'content' => 'Se ha agregado un nuevo registro CLE "{course_title}". Créditos: {credits_earned}. Fecha de finalización: {completion_date}. Proveedor: {provider}.'
            //         ],
            //         'ar' => [
            //             'title' => 'تمت إضافة سجل CLE جديد: {course_title}',
            //             'content' => 'تمت إضافة سجل CLE الجديد "{course_title}". الاعتمادات: {credits_earned}. تاريخ الإكمال: {completion_date}. المزود: {provider}.'
            //         ],
            //         'da' => [
            //             'title' => 'Ny CLE-post tilføjet: {course_title}',
            //             'content' => 'En ny CLE-post "{course_title}" er blevet tilføjet. Point: {credits_earned}. Afslutningsdato: {completion_date}. Udbyder: {provider}.'
            //         ],
            //         'de' => [
            //             'title' => 'Neuer CLE-Eintrag: {course_title}',
            //             'content' => 'Ein neuer CLE-Eintrag "{course_title}" wurde hinzugefügt. Punkte: {credits_earned}. Abschlussdatum: {completion_date}. Anbieter: {provider}.'
            //         ],
            //         'fr' => [
            //             'title' => 'Nouvel enregistrement CLE : {course_title}',
            //             'content' => 'Un nouvel enregistrement CLE "{course_title}" a été ajouté. Crédits : {credits_earned}. Date d\'achèvement : {completion_date}. Fournisseur : {provider}.'
            //         ],
            //         'he' => [
            //             'title' => 'נוסף רישום CLE חדש: {course_title}',
            //             'content' => 'נוסף רישום CLE חדש "{course_title}". נקודות זכות: {credits_earned}. תאריך השלמה: {completion_date}. ספק: {provider}.'
            //         ],
            //         'it' => [
            //             'title' => 'Nuovo record CLE: {course_title}',
            //             'content' => 'È stato aggiunto un nuovo record CLE "{course_title}". Crediti: {credits_earned}. Data di completamento: {completion_date}. Fornitore: {provider}.'
            //         ],
            //         'ja' => [
            //             'title' => '新しいCLE記録が追加されました: {course_title}',
            //             'content' => '新しいCLE記録「{course_title}」が追加されました。単位: {credits_earned}。修了日: {completion_date}。提供者: {provider}。'
            //         ],
            //         'nl' => [
            //             'title' => 'Nieuw CLE-record toegevoegd: {course_title}',
            //             'content' => 'Een nieuw CLE-record "{course_title}" is toegevoegd. Punten: {credits_earned}. Voltooiingsdatum: {completion_date}. Aanbieder: {provider}.'
            //         ],
            //         'pl' => [
            //             'title' => 'Dodano nowy wpis CLE: {course_title}',
            //             'content' => 'Nowy wpis CLE "{course_title}" został dodany. Punkty: {credits_earned}. Data ukończenia: {completion_date}. Dostawca: {provider}.'
            //         ],
            //         'pt' => [
            //             'title' => 'Novo registro CLE: {course_title}',
            //             'content' => 'Um novo registro CLE "{course_title}" foi adicionado. Créditos: {credits_earned}. Data de conclusão: {completion_date}. Provedor: {provider}.'
            //         ],
            //         'pt-br' => [
            //             'title' => 'Novo registro CLE: {course_title}',
            //             'content' => 'Um novo registro CLE "{course_title}" foi adicionado. Créditos: {credits_earned}. Data de conclusão: {completion_date}. Provedor: {provider}.'
            //         ],
            //         'ru' => [
            //             'title' => 'Новая запись CLE: {course_title}',
            //             'content' => 'Добавлена новая запись CLE "{course_title}". Кредиты: {credits_earned}. Дата завершения: {completion_date}. Провайдер: {provider}.'
            //         ],
            //         'tr' => [
            //             'title' => 'Yeni CLE Kaydı: {course_title}',
            //             'content' => 'Yeni bir CLE kaydı "{course_title}" eklendi. Krediler: {credits_earned}. Tamamlama Tarihi: {completion_date}. Sağlayıcı: {provider}.'
            //         ],
            //         'zh' => [
            //             'title' => '已添加新的CLE记录: {course_title}',
            //             'content' => '新的CLE记录 "{course_title}" 已添加。学分：{credits_earned}。完成日期：{completion_date}。提供者：{provider}。'
            //         ],
            //     ]
            // ],
            [
                'name' => EmailTemplateName::NEW_TEAM_MEMBER->value,
                'type' => 'slack',
                'translations' => [
                    'en' => [
                        'title' => 'New Team Member: {member_name}',
                        'content' => 'A new team member "{member_name}" has been added to the system. Email: {email}. Role: {role}.'
                    ],
                    'ar' => [
                        'title' => 'عضو فريق جديد: {member_name}',
                        'content' => 'تمت إضافة عضو فريق جديد "{member_name}" إلى النظام. البريد الإلكتروني: {email}. الدور: {role}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_CASE->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Case: {case_number}',
                        'content' => 'A new case ({case_number}) has been opened for you. Type: {case_type}. Assigned by {created_by}.'
                    ],
                    'ar' => [
                        'title' => 'قضية جديدة: {case_number}',
                        'content' => 'تم فتح قضية جديدة ({case_number}) لك. النوع: {case_type}. تم التعيين بواسطة {created_by}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_CLIENT->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Client: {client_name}',
                        'content' => 'Welcome {client_name}! Your profile has been added successfully. Type: {client_type}.'
                    ],
                    'ar' => [
                        'title' => 'عميل جديد: {client_name}',
                        'content' => 'مرحبًا {client_name}! تم إضافة ملفك الشخصي بنجاح. النوع: {client_type}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_HEARING->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Hearing: {case_number}',
                        'content' => 'Your hearing for case {case_number} is scheduled on {hearing_date} at {court}. Judge: {judge}.'
                    ],
                    'ar' => [
                        'title' => 'جلسة جديدة: {case_number}',
                        'content' => 'تم تحديد جلستك للقضية {case_number} في {court} بتاريخ {hearing_date}. القاضي: {judge}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_INVOICE->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Invoice: {invoice_number}',
                        'content' => 'Invoice {invoice_number} issued for {client_name}. Amount: {amount}. Due on {due_date}.'
                    ],
                    'ar' => [
                        'title' => 'فاتورة جديدة: {invoice_number}',
                        'content' => 'تم إصدار الفاتورة {invoice_number} للعميل {client_name}. المبلغ: {amount}. تاريخ الاستحقاق: {due_date}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::INVOICE_SENT->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'Invoice Sent: {invoice_number}',
                        'content' => 'Your invoice {invoice_number} has been sent. Please review and complete payment by {due_date}.'
                    ],
                    'ar' => [
                        'title' => 'تم إرسال الفاتورة: {invoice_number}',
                        'content' => 'تم إرسال فاتورتك {invoice_number}. يرجى المراجعة وإكمال الدفع قبل {due_date}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_COURT->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Court: {court_name}',
                        'content' => 'A new court "{court_name}" has been added to our records. Location: {location}.'
                    ],
                    'ar' => [
                        'title' => 'محكمة جديدة: {court_name}',
                        'content' => 'تمت إضافة محكمة جديدة "{court_name}" إلى سجلاتنا. الموقع: {location}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_JUDGE->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Judge: {judge_name}',
                        'content' => 'Judge {judge_name} ({specialization}) has been assigned to court {court}.'
                    ],
                    'ar' => [
                        'title' => 'قاضٍ جديد: {judge_name}',
                        'content' => 'تم تعيين القاضي {judge_name} ({specialization}) في المحكمة {court}.'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_REGULATORY_BODY->value,
                'type' => 'twilio',
                'translations' => [
                    'en' => [
                        'title' => 'New Regulatory Body: {body_name}',
                        'content' => 'Regulatory body "{body_name}" has been added under {jurisdiction}. Contact: {contact_info}.'
                    ],
                    'ar' => [
                        'title' => 'هيئة تنظيمية جديدة: {body_name}',
                        'content' => 'تمت إضافة الهيئة التنظيمية "{body_name}" ضمن {jurisdiction}. جهة الاتصال: {contact_info}.'
                    ],
                ]
            ],
        ];

        $companies = \App\Models\User::where('type', 'company')->get();

        foreach ($templates as $templateData) {
            // FIXED: Check both name AND type to prevent duplicates
            $template = NotificationTemplate::updateOrCreate(
                [
                    'name' => $templateData['name'],
                    'type' => $templateData['type']
                ],
                [
                    'name' => $templateData['name'],
                    'type' => $templateData['type']
                ]
            );

            // Create content for each company
            foreach ($companies as $company) {
                foreach ($langCodes as $langCode) {
                    $existingContent = NotificationTemplateLang::where('parent_id', $template->id)
                        ->where('lang', $langCode)
                        ->where('created_by', $company->id)
                        ->first();

                    if ($existingContent) {
                        continue;
                    }

                    $translation = $templateData['translations'][$langCode] ?? $templateData['translations']['en'];

                    NotificationTemplateLang::updateOrCreate([
                        'parent_id' => $template->id,
                        'lang' => $langCode,
                        'created_by' => $company->id
                    ], [
                        'title' => $translation['title'],
                        'content' => $translation['content']
                    ]);
                }
            }

            // Create content for global template
            foreach ($langCodes as $langCode) {
                $existingContent = NotificationTemplateLang::where('parent_id', $template->id)
                    ->where('lang', $langCode)
                    ->where('created_by', $company->id)
                    ->first();

                if ($existingContent) {
                    continue;
                }

                $translation = $templateData['translations'][$langCode] ?? $templateData['translations']['en'];

                NotificationTemplateLang::updateOrCreate([
                    'parent_id' => $template->id,
                    'lang' => $langCode,
                    'created_by' => $company->id
                ], [
                    'title' => $translation['title'],
                    'content' => $translation['content']
                ]);
            }
        }
    }
}
