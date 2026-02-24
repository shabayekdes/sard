<?php

namespace App\Jobs;

use App\Models\DocumentCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default document categories for a company
 */
class SeedDocumentCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        public string $tenant_id
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $documentCategories = [
            [
                'category_id' => 'DOCTP0001',
                'name' => '{"ar":"العقود والاتفاقيات","en":"Contracts & Agreements"}',
                'description' => '{"ar":"جميع أنواع العقود التجارية والمدنية، اتفاقيات الشراكة، مذكرات التفاهم، واتفاقيات عدم الإفصاح","en":"Commercial and civil contracts, partnership agreements, MOUs, NDAs"}',
                'color' => '#3b82f6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0002',
                'name' => '{"ar":"المذكرات القانونية","en":"Legal Memorandums"}',
                'description' => '{"ar":"مذكرات الدعوى، اللوائح الجوابية، الاعتراضات، الاستئناف، والنقض","en":"Statements of claim, defense briefs, objections, appeals, cassation filings"}',
                'color' => '#10b981',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0003',
                'name' => '{"ar":"صحائف الدعوى والطلبات","en":"Claims & Petitions"}',
                'description' => '{"ar":"صحيفة الدعوى، الطلبات المستعجلة، الطلبات العارضة، طلبات التنفيذ","en":"Court claims, urgent petitions, incidental requests, enforcement requests"}',
                'color' => '#f59e0b',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0004',
                'name' => '{"ar":"مستندات المحكمة","en":"Court Documents"}',
                'description' => '{"ar":"الأحكام، محاضر الجلسات، القرارات القضائية، إشعارات التبليغ","en":"Judgments, hearing minutes, court decisions, service notices"}',
                'color' => '#ef4444',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0005',
                'name' => '{"ar":"مستندات التنفيذ","en":"Enforcement Documents"}',
                'description' => '{"ar":"السندات التنفيذية، أوامر التنفيذ، قرارات المادة 34، أوامر الحجز والإفصاح","en":"Executive titles, enforcement orders, Article 34 decisions, seizure and disclosure orders"}',
                'color' => '#8b5cf6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0006',
                'name' => '{"ar":"مستندات الشركات","en":"Corporate Documents"}',
                'description' => '{"ar":"عقود التأسيس، قرارات الشركاء، محاضر الاجتماعات، التعديلات النظامية","en":"Articles of association, shareholders\' resolutions, meeting minutes, amendments"}',
                'color' => '#059669',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0007',
                'name' => '{"ar":"الوكالات والتفويضات","en":"Powers of Attorney & Authorizations"}',
                'description' => '{"ar":"وكالات شرعية (ناجز)، تفويضات رسمية، تخويل التمثيل القانوني","en":"Legal POAs (Najiz), official authorizations, legal representation mandates"}',
                'color' => '#dc2626',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0008',
                'name' => '{"ar":"المستندات المالية","en":"Financial Documents"}',
                'description' => '{"ar":"الفواتير، سندات القبض، المطالبات المالية، كشوف الحساب","en":"Invoices, payment receipts, financial claims, account statements"}',
                'color' => '#6b7280',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0009',
                'name' => '{"ar":"مستندات الإثبات","en":"Evidence Documents"}',
                'description' => '{"ar":"البينات، المستندات الداعمة، تقارير الخبرة، المستندات الرسمية","en":"Evidence files, supporting documents, expert reports, official documents"}',
                'color' => '#f97316',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0010',
                'name' => '{"ar":"المراسلات القانونية","en":"Legal Correspondence"}',
                'description' => '{"ar":"خطابات الإنذار، المراسلات مع العملاء، إشعارات رسمية","en":"Legal notices, client correspondence, formal communications"}',
                'color' => '#84cc16',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0011',
                'name' => '{"ar":"الأبحاث والآراء القانونية","en":"Legal Research & Opinions"}',
                'description' => '{"ar":"الأبحاث القانونية، السوابق القضائية، الآراء والاستشارات","en":"Legal research, case law precedents, legal opinions and advisory memos"}',
                'color' => '#06b6d4',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0012',
                'name' => '{"ar":"مستندات القضايا العمالية","en":"Labor Case Documents"}',
                'description' => '{"ar":"عقود العمل، مطالبات الرواتب، شكاوى العمل، قرارات التسوية","en":"Employment contracts, wage claims, labor complaints, settlement decisions"}',
                'color' => '#ec4899',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0013',
                'name' => '{"ar":"مستندات القضايا الجزائية","en":"Criminal Case Documents"}',
                'description' => '{"ar":"لوائح الاتهام، مذكرات الدفاع، محاضر الضبط، تقارير الجهات المختصة","en":"Indictments, defense briefs, police reports, official investigation reports"}',
                'color' => '#a855f7',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0014',
                'name' => '{"ar":"مستندات القضايا الإدارية","en":"Administrative Case Documents"}',
                'description' => '{"ar":"دعاوى ديوان المظالم، الطعون الإدارية، مراسلات الجهات الحكومية","en":"Administrative court claims, government appeals, official correspondence"}',
                'color' => '#64748b',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 'DOCTP0015',
                'name' => '{"ar":"ملفات العميل","en":"Client Files"}',
                'description' => '{"ar":"الهوية، السجل التجاري، عقد الخدمات القانونية، العنوان الوطني","en":"ID copies, commercial registration, legal service agreements, national address"}',
                'color' => '#14b8a6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DocumentCategory::insert($documentCategories);

        Log::info('SeedDocumentCategories: Completed', [
            'company_id' => $this->tenant_id,
            'total' => count($documentCategories),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SeedDocumentCategories: Job failed', [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
