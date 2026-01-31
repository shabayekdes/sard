<?php

namespace Database\Seeders;

use App\EmailTemplateName;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\UserEmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $langCodes = ['en', 'ar'];

        $templates = [
            [
                'name' => EmailTemplateName::NEW_INVOICE->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'Your Invoice Created: {client}',
                        'content' => '<p>Hello {client},</p>
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
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'تم إنشاء فاتورة جديدة: {client}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {client}،</p>
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
            </div>'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::INVOICE_SENT->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'Your Invoice Sent: {client}',
                        'content' => '<p>Hello {client},</p>
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
            </div>'
                    ],
                    'es' => [
                        'subject' => 'Tu factura enviada: {client}',
                        'content' => '<p>Hola {client},</p>
            <p>Tu factura ha sido enviada a ti.</p>
            <h3>Detalles</h3>
            <p><strong>Cliente:</strong> {client}</p>
            <p><strong>Caso:</strong> {case}</p>
            <p><strong>Fecha de factura:</strong> {invoice_date}</p>
            <p><strong>Fecha de vencimiento:</strong> {due_date}</p>
            <p><strong>Monto total:</strong> {total_amount}</p>
            <p>Por favor, revisa y procesa el pago antes de la fecha de vencimiento.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atentamente,<br>
                {user_name}
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'تم إرسال فاتورتك: {client}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {client}،</p>
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
            </div>'
                    ],
                    'da' => [
                        'subject' => 'Din faktura sendt: {client}',
                        'content' => '<p>Hej {client},</p>
            <p>Din faktura er blevet sendt til dig.</p>
            <h3>Detaljer</h3>
            <p><strong>Klient:</strong> {client}</p>
            <p><strong>Sag:</strong> {case}</p>
            <p><strong>Fakturadato:</strong> {invoice_date}</p>
            <p><strong>Forfaldsdato:</strong> {due_date}</p>
            <p><strong>Samlede beløb:</strong> {total_amount}</p>
            <p>Venligst gennemgå og udfør betalingen før forfaldsdatoen.</p>
            <div style="text-align: right; margin-top: 30px;">
                Med venlig hilsen,<br>
                {user_name}
            </div>'
                    ],
                    'de' => [
                        'subject' => 'Deine Rechnung gesendet: {client}',
                        'content' => '<p>Hallo {client},</p>
            <p>Deine Rechnung wurde an dich gesendet.</p>
            <h3>Details</h3>
            <p><strong>Kunde:</strong> {client}</p>
            <p><strong>Fall:</strong> {case}</p>
            <p><strong>Rechnungsdatum:</strong> {invoice_date}</p>
            <p><strong>Fälligkeitsdatum:</strong> {due_date}</p>
            <p><strong>Gesamtbetrag:</strong> {total_amount}</p>
            <p>Bitte überprüfe und bearbeite die Zahlung bis zum Fälligkeitsdatum.</p>
            <div style="text-align: right; margin-top: 30px;">
                Mit freundlichen Grüßen,<br>
                {user_name}
            </div>'
                    ],
                    'fr' => [
                        'subject' => 'Votre facture envoyée : {client}',
                        'content' => '<p>Bonjour {client},</p>
            <p>Votre facture a été envoyée à vous.</p>
            <h3>Détails</h3>
            <p><strong>Client :</strong> {client}</p>
            <p><strong>Cas :</strong> {case}</p>
            <p><strong>Date de facture :</strong> {invoice_date}</p>
            <p><strong>Date d’échéance :</strong> {due_date}</p>
            <p><strong>Montant total :</strong> {total_amount}</p>
            <p>Veuillez vérifier et traiter le paiement avant la date d’échéance.</p>
            <div style="text-align: right; margin-top: 30px;">
                Cordialement,<br>
                {user_name}
            </div>'
                    ],
                    'he' => [
                        'subject' => 'חשבונית שלך נשלחה: {client}',
                        'content' => '<p style="direction: rtl; text-align: right;">שלום {client},</p>
            <p style="direction: rtl; text-align: right;">חשבונית שלך נשלחה אליך.</p>
            <h3 style="direction: rtl; text-align: right;">פרטים</h3>
            <p style="direction: rtl; text-align: right;"><strong>לקוח:</strong> {client}</p>
            <p style="direction: rtl; text-align: right;"><strong>תיק:</strong> {case}</p>
            <p style="direction: rtl; text-align: right;"><strong>תאריך חשבונית:</strong> {invoice_date}</p>
            <p style="direction: rtl; text-align: right;"><strong>תאריך תשלום:</strong> {due_date}</p>
            <p style="direction: rtl; text-align: right;"><strong>סכום כולל:</strong> {total_amount}</p>
            <p style="direction: rtl; text-align: right;">אנא בדוק וטפל בתשלום עד לתאריך התשלום.</p>
            <div style="text-align: left; margin-top: 30px;">
                בברכה,<br>
                {user_name}
            </div>'
                    ],
                    'it' => [
                        'subject' => 'La tua fattura inviata: {client}',
                        'content' => '<p>Ciao {client},</p>
            <p>La tua fattura è stata inviata a te.</p>
            <h3>Dettagli</h3>
            <p><strong>Cliente:</strong> {client}</p>
            <p><strong>Caso:</strong> {case}</p>
            <p><strong>Data fattura:</strong> {invoice_date}</p>
            <p><strong>Scadenza:</strong> {due_date}</p>
            <p><strong>Importo totale:</strong> {total_amount}</p>
            <p>Si prega di rivedere e processare il pagamento entro la data di scadenza.</p>
            <div style="text-align: right; margin-top: 30px;">
                Cordiali saluti,<br>
                {user_name}
            </div>'
                    ],
                    'ja' => [
                        'subject' => 'あなたの請求書が送信されました: {client}',
                        'content' => '<p>こんにちは {client} 様、</p>
            <p>あなたの請求書があなたに送信されました。</p>
            <h3>詳細</h3>
            <p><strong>クライアント:</strong> {client}</p>
            <p><strong>案件:</strong> {case}</p>
            <p><strong>請求日:</strong> {invoice_date}</p>
            <p><strong>期日:</strong> {due_date}</p>
            <p><strong>合計金額:</strong> {total_amount}</p>
            <p>期日までにお支払いを確認してください。</p>
            <div style="text-align: right; margin-top: 30px;">
                よろしくお願いいたします、<br>
                {user_name}
            </div>'
                    ],
                    'nl' => [
                        'subject' => 'Jouw factuur verzonden: {client}',
                        'content' => '<p>Hallo {client},</p>
            <p>Jouw factuur is naar je verzonden.</p>
            <h3>Details</h3>
            <p><strong>Klant:</strong> {client}</p>
            <p><strong>Zaak:</strong> {case}</p>
            <p><strong>Factuurdatum:</strong> {invoice_date}</p>
            <p><strong>Vervaldatum:</strong> {due_date}</p>
            <p><strong>Totaalbedrag:</strong> {total_amount}</p>
            <p>Controleer en verwerk de betaling vóór de vervaldatum.</p>
            <div style="text-align: right; margin-top: 30px;">
                Met vriendelijke groet,<br>
                {user_name}
            </div>'
                    ],
                    'pl' => [
                        'subject' => 'Twoja faktura wysłana: {client}',
                        'content' => '<p>Witaj {client},</p>
            <p>Twoja faktura została wysłana do Ciebie.</p>
            <h3>Szczegóły</h3>
            <p><strong>Klient:</strong> {client}</p>
            <p><strong>Sprawa:</strong> {case}</p>
            <p><strong>Data faktury:</strong> {invoice_date}</p>
            <p><strong>Termin płatności:</strong> {due_date}</p>
            <p><strong>Kwota całkowita:</strong> {total_amount}</p>
            <p>Proszę przejrzyj i dokonaj płatności przed terminem.</p>
            <div style="text-align: right; margin-top: 30px;">
                Z poważaniem,<br>
                {user_name}
            </div>'
                    ],
                    'pt' => [
                        'subject' => 'Sua fatura enviada: {client}',
                        'content' => '<p>Olá {client},</p>
            <p>Sua fatura foi enviada para você.</p>
            <h3>Detalhes</h3>
            <p><strong>Cliente:</strong> {client}</p>
            <p><strong>Caso:</strong> {case}</p>
            <p><strong>Data da fatura:</strong> {invoice_date}</p>
            <p><strong>Data de vencimento:</strong> {due_date}</p>
            <p><strong>Valor total:</strong> {total_amount}</p>
            <p>Por favor, revise e processe o pagamento até a data de vencimento.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atenciosamente,<br>
                {user_name}
            </div>'
                    ],
                    'pt-br' => [
                        'subject' => 'Sua fatura enviada: {client}',
                        'content' => '<p>Olá {client},</p>
            <p>Sua fatura foi enviada para você.</p>
            <h3>Detalhes</h3>
            <p><strong>Cliente:</strong> {client}</p>
            <p><strong>Caso:</strong> {case}</p>
            <p><strong>Data da fatura:</strong> {invoice_date}</p>
            <p><strong>Data de vencimento:</strong> {due_date}</p>
            <p><strong>Valor total:</strong> {total_amount}</p>
            <p>Por favor, revise e processe o pagamento até a data de vencimento.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atenciosamente,<br>
                {user_name}
            </div>'
                    ],
                    'ru' => [
                        'subject' => 'Ваш счет отправлен: {client}',
                        'content' => '<p>Здравствуйте, {client},</p>
            <p>Ваш счет был отправлен вам.</p>
            <h3>Детали</h3>
            <p><strong>Клиент:</strong> {client}</p>
            <p><strong>Дело:</strong> {case}</p>
            <p><strong>Дата счета:</strong> {invoice_date}</p>
            <p><strong>Дата платежа:</strong> {due_date}</p>
            <p><strong>Общая сумма:</strong> {total_amount}</p>
            <p>Пожалуйста, проверьте и оплатите счет до указанной даты.</p>
            <div style="text-align: right; margin-top: 30px;">
                С наилучшими пожеланиями,<br>
                {user_name}
            </div>'
                    ],
                    'tr' => [
                        'subject' => 'Faturanız gönderildi: {client}',
                        'content' => '<p>Merhaba {client},</p>
            <p>Faturanız size gönderildi.</p>
            <h3>Ayrıntılar</h3>
            <p><strong>Müşteri:</strong> {client}</p>
            <p><strong>Dava:</strong> {case}</p>
            <p><strong>Fatura tarihi:</strong> {invoice_date}</p>
            <p><strong>Vade tarihi:</strong> {due_date}</p>
            <p><strong>Toplam tutar:</strong> {total_amount}</p>
            <p>Lütfen faturanızı gözden geçirip vade tarihine kadar ödeme yapın.</p>
            <div style="text-align: right; margin-top: 30px;">
                Saygılarımla,<br>
                {user_name}
            </div>'
                    ],
                    'zh' => [
                        'subject' => '您的发票已发送：{client}',
                        'content' => '<p>您好 {client}，</p>
            <p>您的发票已发送给您。</p>
            <h3>详情</h3>
            <p><strong>客户：</strong> {client}</p>
            <p><strong>案件：</strong> {case}</p>
            <p><strong>发票日期：</strong> {invoice_date}</p>
            <p><strong>到期日期：</strong> {due_date}</p>
            <p><strong>总金额：</strong> {total_amount}</p>
            <p>请审查并在到期日期前处理付款。</p>
            <div style="text-align: right; margin-top: 30px;">
                此致敬礼，<br>
                {user_name}
            </div>'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_TEAM_MEMBER->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Team Member Profile Created: {name}',
                        'content' => '<p>Hello {name},</p>
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
            </div>'
                    ],
                    'es' => [
                        'subject' => 'Nuevo Perfil de Miembro del Equipo Creado: {name}',
                        'content' => '<p>Hola {name},</p>
            <p>Tu nuevo perfil de miembro del equipo ha sido creado en el sistema.</p>
            <h3>Detalles</h3>
            <p><strong>Nombre:</strong> {name}</p>
            <p><strong>Correo electrónico:</strong> {email}</p>
            <p><strong>Contraseña:</strong> {password}</p>
            <p><strong>Rol:</strong> {role}</p>
            <p>Por favor, revisa y confirma tu perfil de miembro del equipo en el sistema.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atentamente,<br>
                {user_name}
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'تم إنشاء ملف تعريف عضو فريق جديد: {name}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {name}،</p>
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
            </div>'
                    ],
                    'da' => [
                        'subject' => 'Ny Teammedlemsprofil Oprettet: {name}',
                        'content' => '<p>Hej {name},</p>
            <p>Din nye teammedlemsprofil er blevet oprettet i systemet.</p>
            <h3>Detaljer</h3>
            <p><strong>Navn:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Adgangskode:</strong> {password}</p>
            <p><strong> Rolle:</strong> {role}</p>
            <p>Venligst gennemgå og bekræft din teammedlemsprofil i systemet.</p>
            <div style="text-align: right; margin-top: 30px;">
                Med venlig hilsen,<br>
                {user_name}
            </div>'
                    ],
                    'de' => [
                        'subject' => 'Neues Teammitgliedsprofil Erstellt: {name}',
                        'content' => '<p>Hallo {name},</p>
            <p>Dein neues Teammitgliedsprofil wurde im System erstellt.</p>
            <h3>Details</h3>
            <p><strong>Name:</strong> {name}</p>
            <p><strong>E-Mail:</strong> {email}</p>
            <p><strong>Passwort:</strong> {password}</p>
            <p><strong>Rolle:</strong> {role}</p>
            <p>Bitte überprüfe und bestätige dein Teammitgliedsprofil im System.</p>
            <div style="text-align: right; margin-top: 30px;">
                Mit freundlichen Grüßen,<br>
                {user_name}
            </div>'
                    ],
                    'fr' => [
                        'subject' => 'Nouveau Profil de Membre d\'Équipe Créé: {name}',
                        'content' => '<p>Bonjour {name},</p>
            <p>Votre nouveau profil de membre d\'équipe a été créé dans le système.</p>
            <h3>Détails</h3>
            <p><strong>Nom:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Mot de passe:</strong> {password}</p>
            <p><strong>Rôle:</strong> {role}</p>
            <p>Veuillez vérifier et confirmer votre profil de membre d\'équipe dans le système.</p>
            <div style="text-align: right; margin-top: 30px;">
                Cordialement,<br>
                {user_name}
            </div>'
                    ],
                    'he' => [
                        'subject' => 'פרופיל חבר צוות חדש נוצר: {name}',
                        'content' => '<p style="direction: rtl; text-align: right;">שלום {name},</p>
            <p style="direction: rtl; text-align: right;">פרופיל חבר צוות חדש נוצר עבורך במערכת.</p>
            <h3 style="direction: rtl; text-align: right;">פרטים</h3>
            <p style="direction: rtl; text-align: right;"><strong>שם:</strong> {name}</p>
            <p style="direction: rtl; text-align: right;"><strong>דוא"ל:</strong> {email}</p>
            <p style="direction: rtl; text-align: right;"><strong>סיסמה:</strong> {password}</p>
            <p style="direction: rtl; text-align: right;"><strong>תפקיד:</strong> {role}</p>
            <p style="direction: rtl; text-align: right;">אנא בדוק ואשר את פרופיל חבר הצוות שלך במערכת.</p>
            <div style="text-align: left; margin-top: 30px;">
                בברכה,<br>
                {user_name}
            </div>'
                    ],
                    'it' => [
                        'subject' => 'Nuovo Profilo Membro del Team Creato: {name}',
                        'content' => '<p>Ciao {name},</p>
            <p>Il tuo nuovo profilo membro del team è stato creato nel sistema.</p>
            <h3>Dettagli</h3>
            <p><strong>Nome:</strong> {name}</p>
            <p><strong>Email:</strong> {email}</p>
            <p><strong>Password:</strong> {password}</p>
            <p><strong>Ruolo:</strong> {role}</p>
            <p>Per favore, rivedi e conferma il tuo profilo membro del team nel sistema.</p>
            <div style="text-align: right; margin-top: 30px;">
                Cordiali saluti,<br>
                {user_name}
            </div>'
                    ],
                    'ja' => [
                        'subject' => '新しいチームメンバープロファイル作成: {name}',
                        'content' => '<p>こんにちは {name} 様,</p>
            <p>新しいチームメンバープロファイルがシステムに作成されました。</p>
            <h3>詳細</h3>
            <p><strong>名前:</strong> {name}</p>
            <p><strong>メール:</strong> {email}</p>
            <p><strong>パスワード:</strong> {password}</p>
            <p><strong>役割:</strong> {role}</p>
            <p>システムでチームメンバープロファイルを確認してください。</p>
            <div style="text-align: right; margin-top: 30px;">
                よろしくお願いいたします,<br>
                {user_name}
            </div>'
                    ],
                    'nl' => [
                        'subject' => 'Nieuw Teamlid Profiel Aangemaakt: {name}',
                        'content' => '<p>Hallo {name},</p>
            <p>Je nieuwe teamlid profiel is aangemaakt in het systeem.</p>
            <h3>Details</h3>
            <p><strong>Naam:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Wachtwoord:</strong> {password}</p>
            <p><strong>Rol:</strong> {role}</p>
            <p>Controleer en bevestig je teamlid profiel in het systeem.</p>
            <div style="text-align: right; margin-top: 30px;">
                Met vriendelijke groet,<br>
                {user_name}
            </div>'
                    ],
                    'pl' => [
                        'subject' => 'Nowy Profil Członka Zespołu Utworzony: {name}',
                        'content' => '<p>Witaj {name},</p>
            <p>Twój nowy profil członka zespołu został utworzony w systemie.</p>
            <h3>Szczegóły</h3>
            <p><strong>Imię:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Hasło:</strong> {password}</p>
            <p><strong>Rola:</strong> {role}</p>
            <p>Proszę przejrzyj i potwierdź swój profil członka zespołu w systemie.</p>
            <div style="text-align: right; margin-top: 30px;">
                Z poważaniem,<br>
                {user_name}
            </div>'
                    ],
                    'pt' => [
                        'subject' => 'Novo Perfil de Membro da Equipe Criado: {name}',
                        'content' => '<p>Olá {name},</p>
            <p>Seu novo perfil de membro da equipe foi criado no sistema.</p>
            <h3>Detalhes</h3>
            <p><strong>Nome:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Senha:</strong> {password}</p>
            <p><strong>Função:</strong> {role}</p>
            <p>Por favor, revise e confirme seu perfil de membro da equipe no sistema.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atenciosamente,<br>
                {user_name}
            </div>'
                    ],
                    'pt-br' => [
                        'subject' => 'Novo Perfil de Membro da Equipe Criado: {name}',
                        'content' => '<p>Olá {name},</p>
            <p>Seu novo perfil de membro da equipe foi criado no sistema.</p>
            <h3>Detalhes</h3>
            <p><strong>Nome:</strong> {name}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Senha:</strong> {password}</p>
            <p><strong>Função:</strong> {role}</p>
            <p>Por favor, revise e confirme seu perfil de membro da equipe no sistema.</p>
            <div style="text-align: right; margin-top: 30px;">
                Atenciosamente,<br>
                {user_name}
            </div>'
                    ],
                    'ru' => [
                        'subject' => 'Новый Профиль Члена Команды Создан: {name}',
                        'content' => '<p>Здравствуйте {name},</p>
            <p>Ваш новый профиль члена команды создан в системе.</p>
            <h3>Детали</h3>
            <p><strong>Имя:</strong> {name}</p>
            <p><strong>Электронная почта:</strong> {email}</p>
            <p><strong>Пароль:</strong> {password}</p>
            <p><strong>Роль:</strong> {role}</p>
            <p>Пожалуйста, проверьте и подтвердите свой профиль члена команды в системе.</p>
            <div style="text-align: right; margin-top: 30px;">
                С наилучшими пожеланиями,<br>
                {user_name}
            </div>'
                    ],
                    'tr' => [
                        'subject' => 'Yeni Takım Üyesi Profili Oluşturuldu: {name}',
                        'content' => '<p>Merhaba {name},</p>
            <p>Yeni takım üyesi profiliniz sistemde oluşturuldu.</p>
            <h3>Ayrıntılar</h3>
            <p><strong>İsim:</strong> {name}</p>
            <p><strong>E-posta:</strong> {email}</p>
            <p><strong>Şifre:</strong> {password}</p>
            <p><strong>Rol:</strong> {role}</p>
            <p>Lütfen takım üyesi profilinizi sistemde gözden geçirip onaylayın.</p>
            <div style="text-align: right; margin-top: 30px;">
                Saygılarımla,<br>
                {user_name}
            </div>'
                    ],
                    'zh' => [
                        'subject' => '新团队成员配置文件创建: {name}',
                        'content' => '<p>您好 {name}，</p>
            <p>您的新的团队成员配置文件已在系统中创建。</p>
            <h3>详情</h3>
            <p><strong>名称:</strong> {name}</p>
            <p><strong>电子邮件:</strong> {email}</p>
            <p><strong>密码:</strong> {password}</p>
            <p><strong>角色:</strong> {role}</p>
            <p>请审查并确认您的团队成员配置文件于系统中。</p>
            <div style="text-align: right; margin-top: 30px;">
                此致敬礼,<br>
                {user_name}
            </div>'
                    ],
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_CLIENT->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'Your Profile Created: {name}',
                        'content' => '<p>Hello {name},</p>
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
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'تم إنشاء ملفك الشخصي: {name}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {name}،</p>
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
            </div>'
                    ],
                ],
            ],
            [
                'name' => EmailTemplateName::NEW_CASE->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Case Created: {case_id}',
                        'content' => '<p>Dear {client},</p>
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
                        </div>'
                    ],
                    'ar' => [
                        'subject' => 'تم إنشاء قضية جديدة: {case_id}',
                        'content' => '<p>عزيزي/عزيزتي {client}،</p>
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
                        </div>'
                    ]
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_HEARING->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Hearing Scheduled for Case {case_number}',
                        'content' => '<p>Hello {client_name},</p>
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
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'جلسة استماع جديدة مجدولة للقضية {case_number}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {client_name}،</p>
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
            </div>'
                    ]
                ],
            ],

            [
                'name' => EmailTemplateName::NEW_JUDGE->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Judge Appointed: {judge_name}',
                        'content' => '<p>Hello {judge_name},</p>
            <p><strong>Judge Name:</strong> {judge_name}</p>
            <p><strong>Email:</strong> {email}</p>
            <p><strong>Court Name:</strong> {court_name}</p>
            <p><strong>Contact Number:</strong> {contact_no}</p>
            <p>Please log into the system to view full details about your appointment and related case information.</p>
            <div style="text-align: right; margin-top: 30px;">
                Best regards,<br>
                {user_name}
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'تعيين قاضٍ جديد: {judge_name}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {judge_name}،</p>
            <p style="direction: rtl; text-align: right;"><strong>اسم القاضي:</strong> {judge_name}</p>
            <p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
            <p style="direction: rtl; text-align: right;"><strong>اسم المحكمة:</strong> {court_name}</p>
            <p style="direction: rtl; text-align: right;"><strong>رقم الاتصال:</strong> {contact_no}</p>
            <p style="direction: rtl; text-align: right;">يرجى تسجيل الدخول إلى النظام لعرض التفاصيل الكاملة حول تعيينك ومعلومات القضية ذات الصلة.</p>
            <div style="text-align: left; margin-top: 30px;">
                مع أطيب التحيات،<br>
                {user_name}
            </div>'
                    ]
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_COURT->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Court Established: {name}',
                        'content' => '<p>Hello,</p>
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
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'إنشاء محكمة جديدة: {name}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا،</p>
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
            </div>'
                    ]
                ]
            ],
            [
                'name' => EmailTemplateName::NEW_TASK->value,
                'from' => config('app.name'),
                'translations' => [
                    'en' => [
                        'subject' => 'New Task Assigned: {title}',
                        'content' => '<p>Hello {assigned_to},</p>
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
            </div>'
                    ],
                    'ar' => [
                        'subject' => 'مهمة جديدة تم تعيينها: {title}',
                        'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {assigned_to}،</p>
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
            </div>'
                    ],
                ]
            ],

            // [
            //     'name' => 'New License',
            //     'from' => env('APP_NAME'),
            //     'translations' => [
            //         'en' => [
            //             'subject' => 'New License Issued: {license_number}',
            //             'content' => '<p>Hello,</p>
            // <p><strong>Team Member:</strong> {team_member}</p>
            // <p><strong>License Type:</strong> {license_type}</p>
            // <p><strong>License Number:</strong> {license_number}</p>
            // <p><strong>Issuing Authority:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdiction:</strong> {jurisdiction}</p>
            // <p><strong>Issue Date:</strong> {issue_date}</p>
            // <p><strong>Expiry Date:</strong> {expiry_date}</p>
            // <p>Please log into the system to view full {license_type} details and manage this license.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Best regards,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'es' => [
            //             'subject' => 'Nueva licencia emitida: {license_number}',
            //             'content' => '<p>Hola,</p>
            // <p><strong>Miembro del equipo:</strong> {team_member}</p>
            // <p><strong>Tipo de licencia:</strong> {license_type}</p>
            // <p><strong>Número de licencia:</strong> {license_number}</p>
            // <p><strong>Autoridad emisora:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdicción:</strong> {jurisdiction}</p>
            // <p><strong>Fecha de emisión:</strong> {issue_date}</p>
            // <p><strong>Fecha de caducidad:</strong> {expiry_date}</p>
            // <p>Por favor, inicia sesión en el sistema para ver los detalles completos de {license_type} y gestionar esta licencia.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atentamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ar' => [
            //             'subject' => 'رخصة جديدة صادرة: {license_number}',
            //             'content' => '<p style="direction: rtl; text-align: right;">مرحبًا،</p>
            // <p style="direction: rtl; text-align: right;"><strong>عضو الفريق:</strong> {team_member}</p>
            // <p style="direction: rtl; text-align: right;"><strong>نوع الرخصة:</strong> {license_type}</p>
            // <p style="direction: rtl; text-align: right;"><strong>رقم الرخصة:</strong> {license_number}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الجهة المصدرة:</strong> {issuing_authority}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الاختصاص:</strong> {jurisdiction}</p>
            // <p style="direction: rtl; text-align: right;"><strong>تاريخ الإصدار:</strong> {issue_date}</p>
            // <p style="direction: rtl; text-align: right;"><strong>تاريخ الانتهاء:</strong> {expiry_date}</p>
            // <p style="direction: rtl; text-align: right;">يرجى تسجيل الدخول إلى النظام لعرض تفاصيل {license_type} الكاملة وإدارة هذه الرخصة.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     مع أطيب التحيات،<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'da' => [
            //             'subject' => 'Ny licens udstedt: {license_number}',
            //             'content' => '<p>Hej,</p>
            // <p><strong>Holdmedlem:</strong> {team_member}</p>
            // <p><strong>Licenstype:</strong> {license_type}</p>
            // <p><strong>Licensnummer:</strong> {license_number}</p>
            // <p><strong>Udstedende myndighed:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdiktion:</strong> {jurisdiction}</p>
            // <p><strong>Udstedelsesdato:</strong> {issue_date}</p>
            // <p><strong>Udløbsdato:</strong> {expiry_date}</p>
            // <p>Log venligst ind i systemet for at se fulde {license_type} detaljer og administrere denne licens.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Med venlig hilsen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'de' => [
            //             'subject' => 'Neue Lizenz ausgestellt: {license_number}',
            //             'content' => '<p>Hallo,</p>
            // <p><strong>Teammitglied:</strong> {team_member}</p>
            // <p><strong>Lizenztyp:</strong> {license_type}</p>
            // <p><strong>Lizenznummer:</strong> {license_number}</p>
            // <p><strong>Ausstellende Behörde:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdiktion:</strong> {jurisdiction}</p>
            // <p><strong>Ausstellungsdatum:</strong> {issue_date}</p>
            // <p><strong>Ablaufdatum:</strong> {expiry_date}</p>
            // <p>Bitte melde dich im System an, um alle {license_type} Details einzusehen und diese Lizenz zu verwalten.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Mit freundlichen Grüßen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'fr' => [
            //             'subject' => 'Nouvelle licence émise : {license_number}',
            //             'content' => '<p>Bonjour,</p>
            // <p><strong>Membre de l\'équipe :</strong> {team_member}</p>
            // <p><strong>Type de licence :</strong> {license_type}</p>
            // <p><strong>Numéro de licence :</strong> {license_number}</p>
            // <p><strong>Autorité émettrice :</strong> {issuing_authority}</p>
            // <p><strong>Juridiction :</strong> {jurisdiction}</p>
            // <p><strong>Date d\'émission :</strong> {issue_date}</p>
            // <p><strong>Date d\'expiration :</strong> {expiry_date}</p>
            // <p>Veuillez vous connecter au système pour consulter les détails complets de {license_type} et gérer cette licence.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordialement,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'he' => [
            //             'subject' => 'רישיון חדש הונפק: {license_number}',
            //             'content' => '<p style="direction: rtl; text-align: right;">שלום,</p>
            // <p style="direction: rtl; text-align: right;"><strong>חבר צוות:</strong> {team_member}</p>
            // <p style="direction: rtl; text-align: right;"><strong>סוג רישיון:</strong> {license_type}</p>
            // <p style="direction: rtl; text-align: right;"><strong>מספר רישיון:</strong> {license_number}</p>
            // <p style="direction: rtl; text-align: right;"><strong>רשות מנפיקה:</strong> {issuing_authority}</p>
            // <p style="direction: rtl; text-align: right;"><strong>תחום שיפוט:</strong> {jurisdiction}</p>
            // <p style="direction: rtl; text-align: right;"><strong>תאריך הנפקה:</strong> {issue_date}</p>
            // <p style="direction: rtl; text-align: right;"><strong>תאריך תפוגה:</strong> {expiry_date}</p>
            // <p style="direction: rtl; text-align: right;">אנא התחבר למערכת כדי לצפות בפרטי {license_type} המלאים ולנהל רישיון זה.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     בברכה,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'it' => [
            //             'subject' => 'Nuova licenza emessa: {license_number}',
            //             'content' => '<p>Ciao,</p>
            // <p><strong>Membro del team:</strong> {team_member}</p>
            // <p><strong>Tipo di licenza:</strong> {license_type}</p>
            // <p><strong>Numero di licenza:</strong> {license_number}</p>
            // <p><strong>Autorità emittente:</strong> {issuing_authority}</p>
            // <p><strong>Giurisdizione:</strong> {jurisdiction}</p>
            // <p><strong>Data di emissione:</strong> {issue_date}</p>
            // <p><strong>Data di scadenza:</strong> {expiry_date}</p>
            // <p>Accedi al sistema per visualizzare i dettagli completi di {license_type} e gestire questa licenza.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordiali saluti,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ja' => [
            //             'subject' => '新しいライセンス発行: {license_number}',
            //             'content' => '<p>こんにちは、</p>
            // <p><strong>チームメンバー:</strong> {team_member}</p>
            // <p><strong>ライセンスの種類:</strong> {license_type}</p>
            // <p><strong>ライセンス番号:</strong> {license_number}</p>
            // <p><strong>発行機関:</strong> {issuing_authority}</p>
            // <p><strong>管轄:</strong> {jurisdiction}</p>
            // <p><strong>発行日:</strong> {issue_date}</p>
            // <p><strong>有効期限:</strong> {expiry_date}</p>
            // <p>システムにログインして、{license_type} の詳細を確認し、このライセンスを管理してください。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     よろしくお願いいたします、<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'nl' => [
            //             'subject' => 'Nieuwe licentie uitgegeven: {license_number}',
            //             'content' => '<p>Hallo,</p>
            // <p><strong>Teamlid:</strong> {team_member}</p>
            // <p><strong>Licentie type:</strong> {license_type}</p>
            // <p><strong>Licentienummer:</strong> {license_number}</p>
            // <p><strong>Uitgevende autoriteit:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdictie:</strong> {jurisdiction}</p>
            // <p><strong>Utgiftedatum:</strong> {issue_date}</p>
            // <p><strong>Vervaldatum:</strong> {expiry_date}</p>
            // <p>Log in op het systeem om de volledige {license_type} details te bekijken en deze licentie te beheren.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Met vriendelijke groet,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pl' => [
            //             'subject' => 'Nowa licencja wydana: {license_number}',
            //             'content' => '<p>Witaj,</p>
            // <p><strong>Członek zespołu:</strong> {team_member}</p>
            // <p><strong>Typ licencji:</strong> {license_type}</p>
            // <p><strong>Numer licencji:</strong> {license_number}</p>
            // <p><strong>Organ wydający:</strong> {issuing_authority}</p>
            // <p><strong>Jurysdykcja:</strong> {jurisdiction}</p>
            // <p><strong>Data wydania:</strong> {issue_date}</p>
            // <p><strong>Data wygaśnięcia:</strong> {expiry_date}</p>
            // <p>Zaloguj się do systemu, aby zobaczyć pełne szczegóły {license_type} i zarządzać tą licencją.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Z poważaniem,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt' => [
            //             'subject' => 'Nova licença emitida: {license_number}',
            //             'content' => '<p>Olá,</p>
            // <p><strong>Membro da equipe:</strong> {team_member}</p>
            // <p><strong>Tipo de licença:</strong> {license_type}</p>
            // <p><strong>Número da licença:</strong> {license_number}</p>
            // <p><strong>Autoridade emissora:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdição:</strong> {jurisdiction}</p>
            // <p><strong>Data de emissão:</strong> {issue_date}</p>
            // <p><strong>Data de expiração:</strong> {expiry_date}</p>
            // <p>Por favor, faça login no sistema para visualizar os detalhes completos de {license_type} e gerenciar esta licença.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt-br' => [
            //             'subject' => 'Nova licença emitida: {license_number}',
            //             'content' => '<p>Olá,</p>
            // <p><strong>Membro da equipe:</strong> {team_member}</p>
            // <p><strong>Tipo de licença:</strong> {license_type}</p>
            // <p><strong>Número da licença:</strong> {license_number}</p>
            // <p><strong>Autoridade emissora:</strong> {issuing_authority}</p>
            // <p><strong>Jurisdição:</strong> {jurisdiction}</p>
            // <p><strong>Data de emissão:</strong> {issue_date}</p>
            // <p><strong>Data de expiração:</strong> {expiry_date}</p>
            // <p>Por favor, faça login no sistema para visualizar os detalhes completos de {license_type} e gerenciar esta licença.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ru' => [
            //             'subject' => 'Новая лицензия выдана: {license_number}',
            //             'content' => '<p>Здравствуйте,</p>
            // <p><strong>Член команды:</strong> {team_member}</p>
            // <p><strong>Тип лицензии:</strong> {license_type}</p>
            // <p><strong>Номер лицензии:</strong> {license_number}</p>
            // <p><strong>Орган, выдавший лицензию:</strong> {issuing_authority}</p>
            // <p><strong>Юрисдикция:</strong> {jurisdiction}</p>
            // <p><strong>Дата выдачи:</strong> {issue_date}</p>
            // <p><strong>Дата истечения:</strong> {expiry_date}</p>
            // <p>Пожалуйста, войдите в систему, чтобы просмотреть полные детали {license_type} и управлять этой лицензией.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     С наилучшими пожеланиями,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'tr' => [
            //             'subject' => 'Yeni lisans verildi: {license_number}',
            //             'content' => '<p>Merhaba,</p>
            // <p><strong>Takım Üyesi:</strong> {team_member}</p>
            // <p><strong>Lisans Türü:</strong> {license_type}</p>
            // <p><strong>Lisans Numarası:</strong> {license_number}</p>
            // <p><strong>Veren Yetkili:</strong> {issuing_authority}</p>
            // <p><strong>Yargı Yetkisi:</strong> {jurisdiction}</p>
            // <p><strong>Veriliş Tarihi:</strong> {issue_date}</p>
            // <p><strong>Son Kullanma Tarihi:</strong> {expiry_date}</p>
            // <p>Lütfen sisteme giriş yaparak {license_type} detaylarını görüntüleyin ve bu lisansı yönetin.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Saygılarımla,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'zh' => [
            //             'subject' => '新许可证颁发：{license_number}',
            //             'content' => '<p>您好，</p>
            // <p><strong>团队成员：</strong> {team_member}</p>
            // <p><strong>许可证类型：</strong> {license_type}</p>
            // <p><strong>许可证编号：</strong> {license_number}</p>
            // <p><strong>颁发机构：</strong> {issuing_authority}</p>
            // <p><strong>管辖权：</strong> {jurisdiction}</p>
            // <p><strong>颁发日期：</strong> {issue_date}</p>
            // <p><strong>到期日期：</strong> {expiry_date}</p>
            // <p>请登录系统查看 {license_type} 的完整详情并管理此许可证。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     此致敬礼，<br>
            //     {user_name}
            // </div>'
            //         ],
            //     ],
            // ],
            // [
            //     'name' => 'New CLE Record',
            //     'from' => env('APP_NAME'),
            //     'translations' => [
            //         'en' => [
            //             'subject' => 'Training Update for {team_member}',
            //             'content' => '<p>Hello {team_member},</p>
            // <p>A new training record has been updated for you.</p>
            // <h3>Details</h3>
            // <p><strong>Team Member:</strong> {team_member}</p>
            // <p><strong>Course Name:</strong> {course_name}</p>
            // <p><strong>Provider:</strong> {provider}</p>
            // <p><strong>Credit Earned:</strong> {credit_earned}</p>
            // <p><strong>Credit Required:</strong> {credit_required}</p>
            // <p><strong>Certificate Number:</strong> {certificate_num}</p>
            // <p>Please review and confirm this training record in the system.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Best regards,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'es' => [
            //             'subject' => 'Actualización de capacitación para {team_member}',
            //             'content' => '<p>Hola {team_member},</p>
            // <p>Se ha actualizado un nuevo registro de capacitación para ti.</p>
            // <h3>Detalles</h3>
            // <p><strong>Miembro del equipo:</strong> {team_member}</p>
            // <p><strong>Nombre del curso:</strong> {course_name}</p>
            // <p><strong>Proveedor:</strong> {provider}</p>
            // <p><strong>Créditos obtenidos:</strong> {credit_earned}</p>
            // <p><strong>Créditos requeridos:</strong> {credit_required}</p>
            // <p><strong>Número de certificado:</strong> {certificate_num}</p>
            // <p>Por favor, revisa y confirma este registro de capacitación en el sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atentamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ar' => [
            //             'subject' => 'تحديث تدريب لـ {team_member}',
            //             'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {team_member}،</p>
            // <p style="direction: rtl; text-align: right;">تم تحديث سجل تدريب جديد لك.</p>
            // <h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
            // <p style="direction: rtl; text-align: right;"><strong>عضو الفريق:</strong> {team_member}</p>
            // <p style="direction: rtl; text-align: right;"><strong>اسم الكورس:</strong> {course_name}</p>
            // <p style="direction: rtl; text-align: right;"><strong>المزود:</strong> {provider}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الاعتمادات المكتسبة:</strong> {credit_earned}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الاعتمادات المطلوبة:</strong> {credit_required}</p>
            // <p style="direction: rtl; text-align: right;"><strong>رقم الشهادة:</strong> {certificate_num}</p>
            // <p style="direction: rtl; text-align: right;">يرجى مراجعة وتأكيد هذا السجل التدريبي في النظام.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     مع أطيب التحيات،<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'da' => [
            //             'subject' => 'Træningsopdatering for {team_member}',
            //             'content' => '<p>Hej Team Medlem,</p>
            // <p>En ny træningsoptegnelse er blevet opdateret for dig.</p>
            // <h3>Detaljer</h3>
            // <p><strong>Teammedlem:</strong> {team_member}</p>
            // <p><strong>Kursusnavn:</strong> {course_name}</p>
            // <p><strong>Leverandør:</strong> {provider}</p>
            // <p><strong>Tjent kredit:</strong> {credit_earned}</p>
            // <p><strong>Krævet kredit:</strong> {credit_required}</p>
            // <p><strong>Certifikatnummer:</strong> {certificate_num}</p>
            // <p>Venligst gennemgå og bekræft denne træningsoptegnelse i systemet.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Med venlig hilsen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'de' => [
            //             'subject' => 'Trainingsupdate für {team_member}',
            //             'content' => '<p>Hallo Teammitglied,</p>
            // <p>Ein neuer Trainingsdatensatz wurde für dich aktualisiert.</p>
            // <h3>Details</h3>
            // <p><strong>Teammitglied:</strong> {team_member}</p>
            // <p><strong>Kursname:</strong> {course_name}</p>
            // <p><strong>Anbieter:</strong> {provider}</p>
            // <p><strong>Gewonnene Credits:</strong> {credit_earned}</p>
            // <p><strong>Erforderliche Credits:</strong> {credit_required}</p>
            // <p><strong>Zertifikatsnummer:</strong> {certificate_num}</p>
            // <p>Bitte überprüfe und bestätige diesen Trainingsdatensatz im System.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Mit freundlichen Grüßen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'fr' => [
            //             'subject' => 'Mise à jour de la formation pour {team_member}',
            //             'content' => '<p>Bonjour Membre de l\'équipe,</p>
            // <p>Un nouveau dossier de formation a été mis à jour pour vous.</p>
            // <h3>Détails</h3>
            // <p><strong>Membre de l\'équipe:</strong> {team_member}</p>
            // <p><strong>Nom du cours:</strong> {course_name}</p>
            // <p><strong>Fournisseur:</strong> {provider}</p>
            // <p><strong>Crédits gagnés:</strong> {credit_earned}</p>
            // <p><strong>Crédits requis:</strong> {credit_required}</p>
            // <p><strong>Numéro de certificat:</strong> {certificate_num}</p>
            // <p>Veuillez vérifier et confirmer ce dossier de formation dans le système.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordialement,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'he' => [
            //             'subject' => 'עדכון אימון עבור {team_member}',
            //             'content' => '<p style="direction: rtl; text-align: right;">שלום חבר צוות,</p>
            // <p style="direction: rtl; text-align: right;">רשומת אימון חדשה עודכנה עבורך.</p>
            // <h3 style="direction: rtl; text-align: right;">פרטים</h3>
            // <p style="direction: rtl; text-align: right;"><strong>חבר צוות:</strong> {team_member}</p>
            // <p style="direction: rtl; text-align: right;"><strong>שם הקורס:</strong> {course_name}</p>
            // <p style="direction: rtl; text-align: right;"><strong>ספק:</strong> {provider}</p>
            // <p style="direction: rtl; text-align: right;"><strong>נקודות זכות שנצברו:</strong> {credit_earned}</p>
            // <p style="direction: rtl; text-align: right;"><strong>נקודות זכות נדרשות:</strong> {credit_required}</p>
            // <p style="direction: rtl; text-align: right;"><strong>מספר תעודה:</strong> {certificate_num}</p>
            // <p style="direction: rtl; text-align: right;">אנא בדוק ואשר את רשומת האימון הזו במערכת.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     בברכה,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'it' => [
            //             'subject' => 'Aggiornamento formazione per {team_member}',
            //             'content' => '<p>Ciao Membro del Team,</p>
            // <p>Un nuovo record di formazione è stato aggiornato per te.</p>
            // <h3>Dettagli</h3>
            // <p><strong>Membro del team:</strong> {team_member}</p>
            // <p><strong>Nome del corso:</strong> {course_name}</p>
            // <p><strong>Fornitore:</strong> {provider}</p>
            // <p><strong>Crediti guadagnati:</strong> {credit_earned}</p>
            // <p><strong>Crediti richiesti:</strong> {credit_required}</p>
            // <p><strong>Numero del certificato:</strong> {certificate_num}</p>
            // <p>Si prega di rivedere e confermare questo record di formazione nel sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordiali saluti,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ja' => [
            //             'subject' => '{team_member} 向けのトレーニング更新',
            //             'content' => '<p>こんにちは、チームメンバー様、</p>
            // <p>新しいトレーニング記録があなたのために更新されました。</p>
            // <h3>詳細</h3>
            // <p><strong>チームメンバー:</strong> {team_member}</p>
            // <p><strong>コース名:</strong> {course_name}</p>
            // <p><strong>提供者:</strong> {provider}</p>
            // <p><strong>獲得クレジット:</strong> {credit_earned}</p>
            // <p><strong>必要なクレジット:</strong> {credit_required}</p>
            // <p><strong>証明書番号:</strong> {certificate_num}</p>
            // <p>このトレーニング記録をシステムで確認してください。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     よろしくお願いいたします、<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'nl' => [
            //             'subject' => 'Trainingsupdate voor {team_member}',
            //             'content' => '<p>Hallo Teamlid,</p>
            // <p>Een nieuw trainingsrecord is voor jou bijgewerkt.</p>
            // <h3>Details</h3>
            // <p><strong>Teamlid:</strong> {team_member}</p>
            // <p><strong>Cursusnaam:</strong> {course_name}</p>
            // <p><strong>Leverancier:</strong> {provider}</p>
            // <p><strong>Verdiende credits:</strong> {credit_earned}</p>
            // <p><strong>Vereiste credits:</strong> {credit_required}</p>
            // <p><strong>Certificaatnummer:</strong> {certificate_num}</p>
            // <p>Controleer en bevestig dit trainingsrecord in het systeem.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Met vriendelijke groet,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pl' => [
            //             'subject' => 'Aktualizacja szkolenia dla {team_member}',
            //             'content' => '<p>Witaj Członku Zespołu,</p>
            // <p>Nowy rekord szkolenia został zaktualizowany dla Ciebie.</p>
            // <h3>Szczegóły</h3>
            // <p><strong>Członek zespołu:</strong> {team_member}</p>
            // <p><strong>Nazwa kursu:</strong> {course_name}</p>
            // <p><strong>Dostawca:</strong> {provider}</p>
            // <p><strong>Zdobyte kredyty:</strong> {credit_earned}</p>
            // <p><strong>Wymagane kredyty:</strong> {credit_required}</p>
            // <p><strong>Numer certyfikatu:</strong> {certificate_num}</p>
            // <p>Proszę przejrzyj i potwierdź ten rekord szkolenia w systemie.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Z poważaniem,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt' => [
            //             'subject' => 'Atualização de treinamento para {team_member}',
            //             'content' => '<p>Olá Membro da Equipe,</p>
            // <p>Um novo registro de treinamento foi atualizado para você.</p>
            // <h3>Detalhes</h3>
            // <p><strong>Membro da equipe:</strong> {team_member}</p>
            // <p><strong>Nome do curso:</strong> {course_name}</p>
            // <p><strong>Fornecedor:</strong> {provider}</p>
            // <p><strong>Créditos ganhos:</strong> {credit_earned}</p>
            // <p><strong>Créditos requeridos:</strong> {credit_required}</p>
            // <p><strong>Número do certificado:</strong> {certificate_num}</p>
            // <p>Por favor, revise e confirme este registro de treinamento no sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt-br' => [
            //             'subject' => 'Atualização de treinamento para {team_member}',
            //             'content' => '<p>Olá Membro da Equipe,</p>
            // <p>Um novo registro de treinamento foi atualizado para você.</p>
            // <h3>Detalhes</h3>
            // <p><strong>Membro da equipe:</strong> {team_member}</p>
            // <p><strong>Nome do curso:</strong> {course_name}</p>
            // <p><strong>Fornecedor:</strong> {provider}</p>
            // <p><strong>Créditos ganhos:</strong> {credit_earned}</p>
            // <p><strong>Créditos requeridos:</strong> {credit_required}</p>
            // <p><strong>Número do certificado:</strong> {certificate_num}</p>
            // <p>Por favor, revise e confirme este registro de treinamento no sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ru' => [
            //             'subject' => 'Обновление тренинга для {team_member}',
            //             'content' => '<p>Здравствуйте, Член команды,</p>
            // <p>Новый тренировочный рекорд был обновлен для вас.</p>
            // <h3>Детали</h3>
            // <p><strong>Член команды:</strong> {team_member}</p>
            // <p><strong>Название курса:</strong> {course_name}</p>
            // <p><strong>Поставщик:</strong> {provider}</p>
            // <p><strong>Заработанные кредиты:</strong> {credit_earned}</p>
            // <p><strong>Требуемые кредиты:</strong> {credit_required}</p>
            // <p><strong>Номер сертификата:</strong> {certificate_num}</p>
            // <p>Пожалуйста, проверьте и подтвердите этот тренировочный рекорд в системе.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     С наилучшими пожеланиями,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'tr' => [
            //             'subject' => '{team_member} için eğitim güncellemesi',
            //             'content' => '<p>Merhaba Takım Üyesi,</p>
            // <p>Senin için yeni bir eğitim kaydı güncellendi.</p>
            // <h3>Ayrıntılar</h3>
            // <p><strong>Takım üyesi:</strong> {team_member}</p>
            // <p><strong>Kurs adı:</strong> {course_name}</p>
            // <p><strong>Sağlayıcı:</strong> {provider}</p>
            // <p><strong>Kazanılan kredi:</strong> {credit_earned}</p>
            // <p><strong>Gereken kredi:</strong> {credit_required}</p>
            // <p><strong>Sertifika numarası:</strong> {certificate_num}</p>
            // <p>Lütfen bu eğitim kaydını sistemde gözden geçirip onaylayın.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Saygılarımla,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'zh' => [
            //             'subject' => '{team_member} 的培训更新',
            //             'content' => '<p>您好，团队成员，</p>
            // <p>您的培训记录已更新。</p>
            // <h3>详情</h3>
            // <p><strong>团队成员：</strong> {team_member}</p>
            // <p><strong>课程名称：</strong> {course_name}</p>
            // <p><strong>提供者：</strong> {provider}</p>
            // <p><strong>获得的学分：</strong> {credit_earned}</p>
            // <p><strong>所需的学分：</strong> {credit_required}</p>
            // <p><strong>证书编号：</strong> {certificate_num}</p>
            // <p>请查看并确认此培训记录于系统中。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     此致敬礼，<br>
            //     {user_name}
            // </div>'
            //         ],
            //     ]
            // ],

            // [
            //     'name' => 'New Regulatory Body',
            //     'from' => env('APP_NAME'),
            //     'translations' => [
            //         'en' => [
            //             'subject' => 'New Regulatory Body Added: {name}',
            //             'content' => '<p>Hello {name},</p>
            // <p>A new regulatory body has been added to the system.</p>
            // <h3>Details</h3>
            // <p><strong>Name:</strong> {name}</p>
            // <p><strong>Jurisdiction:</strong> {jurisdiction}</p>
            // <p><strong>Email:</strong> {email}</p>
            // <p><strong>Phone Number:</strong> {phoneno}</p>
            // <p><strong>Address:</strong> {address}</p>
            // <p><strong>Website:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Please review and confirm this regulatory body in the system.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Best regards,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'es' => [
            //             'subject' => 'Nuevo organismo regulador añadido: {name}',
            //             'content' => '<p>Hola {name},</p>
            // <p>Se ha añadido un nuevo organismo regulador al sistema.</p>
            // <h3>Detalles</h3>
            // <p><strong>Nombre:</strong> {name}</p>
            // <p><strong>Jurisdicción:</strong> {jurisdiction}</p>
            // <p><strong>Correo electrónico:</strong> {email}</p>
            // <p><strong>Número de teléfono:</strong> {phoneno}</p>
            // <p><strong>Dirección:</strong> {address}</p>
            // <p><strong>Sitio web:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Por favor, revisa y confirma este organismo regulador en el sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atentamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ar' => [
            //             'subject' => 'تم إضافة هيئة تنظيمية جديدة: {name}',
            //             'content' => '<p style="direction: rtl; text-align: right;">مرحبًا {name}،</p>
            // <p style="direction: rtl; text-align: right;">تم إضافة هيئة تنظيمية جديدة إلى النظام.</p>
            // <h3 style="direction: rtl; text-align: right;">التفاصيل</h3>
            // <p style="direction: rtl; text-align: right;"><strong>الاسم:</strong> {name}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الاختصاص:</strong> {jurisdiction}</p>
            // <p style="direction: rtl; text-align: right;"><strong>البريد الإلكتروني:</strong> {email}</p>
            // <p style="direction: rtl; text-align: right;"><strong>رقم الهاتف:</strong> {phoneno}</p>
            // <p style="direction: rtl; text-align: right;"><strong>العنوان:</strong> {address}</p>
            // <p style="direction: rtl; text-align: right;"><strong>الموقع الإلكتروني:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p style="direction: rtl; text-align: right;">يرجى مراجعة وتأكيد هذه الهيئة التنظيمية في النظام.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     مع أطيب التحيات،<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'da' => [
            //             'subject' => 'Ny regulerende myndighed tilføjet: {name}',
            //             'content' => '<p>Hej {name},</p>
            // <p>En ny regulerende myndighed er blevet tilføjet til systemet.</p>
            // <h3>Detaljer</h3>
            // <p><strong>Navn:</strong> {name}</p>
            // <p><strong>Jurisdiktion:</strong> {jurisdiction}</p>
            // <p><strong>E-mail:</strong> {email}</p>
            // <p><strong>Telefonnummer:</strong> {phoneno}</p>
            // <p><strong>Adresse:</strong> {address}</p>
            // <p><strong>Hjemmeside:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Venligst gennemgå og bekræft denne regulerende myndighed i systemet.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Med venlig hilsen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'de' => [
            //             'subject' => 'Neue Regulierungsbehörde hinzugefügt: {name}',
            //             'content' => '<p>Hallo {name},</p>
            // <p>Eine neue Regulierungsbehörde wurde dem System hinzugefügt.</p>
            // <h3>Details</h3>
            // <p><strong>Name:</strong> {name}</p>
            // <p><strong>Jurisdiktion:</strong> {jurisdiction}</p>
            // <p><strong>E-Mail:</strong> {email}</p>
            // <p><strong>Telefonnummer:</strong> {phoneno}</p>
            // <p><strong>Adresse:</strong> {address}</p>
            // <p><strong>Website:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Bitte überprüfe und bestätige diese Regulierungsbehörde im System.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Mit freundlichen Grüßen,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'fr' => [
            //             'subject' => 'Nouvel organisme de régulation ajouté : {name}',
            //             'content' => '<p>Bonjour {name},</p>
            // <p>Un nouvel organisme de régulation a été ajouté au système.</p>
            // <h3>Détails</h3>
            // <p><strong>Nom :</strong> {name}</p>
            // <p><strong>Juridiction :</strong> {jurisdiction}</p>
            // <p><strong>Courriel :</strong> {email}</p>
            // <p><strong>Numéro de téléphone :</strong> {phoneno}</p>
            // <p><strong>Adresse :</strong> {address}</p>
            // <p><strong>Site web :</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Veuillez vérifier et confirmer cet organisme de régulation dans le système.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordialement,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'he' => [
            //             'subject' => 'התווספה רשות רגולטורית חדשה: {name}',
            //             'content' => '<p style="direction: rtl; text-align: right;">שלום {name},</p>
            // <p style="direction: rtl; text-align: right;">רשות רגולטורית חדשה התווספה למערכת.</p>
            // <h3 style="direction: rtl; text-align: right;">פרטים</h3>
            // <p style="direction: rtl; text-align: right;"><strong>שם:</strong> {name}</p>
            // <p style="direction: rtl; text-align: right;"><strong>סמכות שיפוט:</strong> {jurisdiction}</p>
            // <p style="direction: rtl; text-align: right;"><strong>דוא"ל:</strong> {email}</p>
            // <p style="direction: rtl; text-align: right;"><strong>מספר טלפון:</strong> {phoneno}</p>
            // <p style="direction: rtl; text-align: right;"><strong>כתובת:</strong> {address}</p>
            // <p style="direction: rtl; text-align: right;"><strong>אתר אינטרנט:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p style="direction: rtl; text-align: right;">אנא בדוק ואשר את רשות זו במערכת.</p>
            // <div style="text-align: left; margin-top: 30px;">
            //     בברכה,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'it' => [
            //             'subject' => 'Nuovo organismo di regolamentazione aggiunto: {name}',
            //             'content' => '<p>Ciao {name},</p>
            // <p>Un nuovo organismo di regolamentazione è stato aggiunto al sistema.</p>
            // <h3>Dettagli</h3>
            // <p><strong>Nome:</strong> {name}</p>
            // <p><strong>Giurisdizione:</strong> {jurisdiction}</p>
            // <p><strong>Email:</strong> {email}</p>
            // <p><strong>Numero di telefono:</strong> {phoneno}</p>
            // <p><strong>Indirizzo:</strong> {address}</p>
            // <p><strong>Sito web:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Si prega di rivedere e confermare questo organismo di regolamentazione nel sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Cordiali saluti,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ja' => [
            //             'subject' => '新しい規制機関が追加されました: {name}',
            //             'content' => '<p>こんにちは {name} 様、</p>
            // <p>新しい規制機関がシステムに追加されました。</p>
            // <h3>詳細</h3>
            // <p><strong>名前:</strong> {name}</p>
            // <p><strong>管轄区域:</strong> {jurisdiction}</p>
            // <p><strong>メール:</strong> {email}</p>
            // <p><strong>電話番号:</strong> {phoneno}</p>
            // <p><strong>住所:</strong> {address}</p>
            // <p><strong>ウェブサイト:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>この規制機関をシステムで確認してください。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     よろしくお願いいたします、<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'nl' => [
            //             'subject' => 'Nieuwe regelgevende instantie toegevoegd: {name}',
            //             'content' => '<p>Hallo {name},</p>
            // <p>Een nieuwe regelgevende instantie is toegevoegd aan het systeem.</p>
            // <h3>Details</h3>
            // <p><strong>Naam:</strong> {name}</p>
            // <p><strong>Jurisdictie:</strong> {jurisdiction}</p>
            // <p><strong>E-mail:</strong> {email}</p>
            // <p><strong>Telefoonnummer:</strong> {phoneno}</p>
            // <p><strong>Adres:</strong> {address}</p>
            // <p><strong>Website:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Controleer en bevestig deze regelgevende instantie in het systeem.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Met vriendelijke groet,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pl' => [
            //             'subject' => 'Dodano nowy organ regulacyjny: {name}',
            //             'content' => '<p>Witaj {name},</p>
            // <p>Do systemu dodano nowy organ regulacyjny.</p>
            // <h3>Szczegóły</h3>
            // <p><strong>Nazwa:</strong> {name}</p>
            // <p><strong>Jurysdykcja:</strong> {jurisdiction}</p>
            // <p><strong>E-mail:</strong> {email}</p>
            // <p><strong>Numer telefonu:</strong> {phoneno}</p>
            // <p><strong>Adres:</strong> {address}</p>
            // <p><strong>Strona internetowa:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Proszę przejrzyj i potwierdź ten organ regulacyjny w systemie.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Z poważaniem,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt' => [
            //             'subject' => 'Novo órgão regulador adicionado: {name}',
            //             'content' => '<p>Olá {name},</p>
            // <p>Um novo órgão regulador foi adicionado ao sistema.</p>
            // <h3>Detalhes</h3>
            // <p><strong>Nome:</strong> {name}</p>
            // <p><strong>Jurisdição:</strong> {jurisdiction}</p>
            // <p><strong>E-mail:</strong> {email}</p>
            // <p><strong>Número de telefone:</strong> {phoneno}</p>
            // <p><strong>Endereço:</strong> {address}</p>
            // <p><strong>Site:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Por favor, revise e confirme este órgão regulador no sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'pt-br' => [
            //             'subject' => 'Novo órgão regulador adicionado: {name}',
            //             'content' => '<p>Olá {name},</p>
            // <p>Um novo órgão regulador foi adicionado ao sistema.</p>
            // <h3>Detalhes</h3>
            // <p><strong>Nome:</strong> {name}</p>
            // <p><strong>Jurisdição:</strong> {jurisdiction}</p>
            // <p><strong>E-mail:</strong> {email}</p>
            // <p><strong>Número de telefone:</strong> {phoneno}</p>
            // <p><strong>Endereço:</strong> {address}</p>
            // <p><strong>Site:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Por favor, revise e confirme este órgão regulador no sistema.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Atenciosamente,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'ru' => [
            //             'subject' => 'Добавлен новый регулирующий орган: {name}',
            //             'content' => '<p>Здравствуйте, {name},</p>
            // <p>В систему добавлен новый регулирующий орган.</p>
            // <h3>Детали</h3>
            // <p><strong>Название:</strong> {name}</p>
            // <p><strong>Юрисдикция:</strong> {jurisdiction}</p>
            // <p><strong>Электронная почта:</strong> {email}</p>
            // <p><strong>Номер телефона:</strong> {phoneno}</p>
            // <p><strong>Адрес:</strong> {address}</p>
            // <p><strong>Веб-сайт:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Пожалуйста, проверьте и подтвердите этот регулирующий орган в системе.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     С наилучшими пожеланиями,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'tr' => [
            //             'subject' => 'Yeni düzenleyici kurum eklendi: {name}',
            //             'content' => '<p>Merhaba {name},</p>
            // <p>Sisteme yeni bir düzenleyici kurum eklendi.</p>
            // <h3>Ayrıntılar</h3>
            // <p><strong>İsim:</strong> {name}</p>
            // <p><strong>Yargı Yetkisi:</strong> {jurisdiction}</p>
            // <p><strong>E-posta:</strong> {email}</p>
            // <p><strong>Telefon Numarası:</strong> {phoneno}</p>
            // <p><strong>Adres:</strong> {address}</p>
            // <p><strong>Web Sitesi:</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>Lütfen bu düzenleyici kurumu sistemde gözden geçirip onaylayın.</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     Saygılarımla,<br>
            //     {user_name}
            // </div>'
            //         ],
            //         'zh' => [
            //             'subject' => '新增监管机构：{name}',
            //             'content' => '<p>您好 {name}，</p>
            // <p>系统中新增了一个监管机构。</p>
            // <h3>详情</h3>
            // <p><strong>名称：</strong> {name}</p>
            // <p><strong>管辖范围：</strong> {jurisdiction}</p>
            // <p><strong>电子邮件：</strong> {email}</p>
            // <p><strong>电话号码：</strong> {phoneno}</p>
            // <p><strong>地址：</strong> {address}</p>
            // <p><strong>网站：</strong> <a href="{website}" target="_blank">{website}</a></p>
            // <p>请审查并确认此监管机构于系统中。</p>
            // <div style="text-align: right; margin-top: 30px;">
            //     此致敬礼，<br>
            //     {user_name}
            // </div>'
            //         ],
            //     ],
            // ],


        ];

        $allowedLangs = array_flip($langCodes);
        $templates = array_map(function ($template) use ($allowedLangs) {
            if (isset($template['translations'])) {
                $template['translations'] = array_intersect_key($template['translations'], $allowedLangs);
            }

            return $template;
        }, $templates);

        foreach ($templates as $templateData) {
            $template = EmailTemplate::updateOrCreate([
                'name' => $templateData['name'],
                'from' => $templateData['from'],
                'user_id' => 1
            ]);

            EmailTemplateLang::where('parent_id', $template->id)
                ->whereNotIn('lang', $langCodes)
                ->delete();

            foreach ($langCodes as $langCode) {
                $translation = $templateData['translations'][$langCode] ?? $templateData['translations']['en'];

                EmailTemplateLang::updateOrCreate([
                    'parent_id' => $template->id,
                    'lang' => $langCode,
                    'subject' => $translation['subject'],
                    'content' => $translation['content']
                ]);
            }
        }
    }
}
