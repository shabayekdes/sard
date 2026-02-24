<?php

namespace App\Jobs;

use App\Models\DocumentType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default document types for a company
 */
class SeedDocumentTypes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tenant_id
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $documentTypes = [
            [
                'name' => '{"en":"Litigation Document","ar":"مستند دعوى"}',
                'description' => '{"en":"Any document related to filing or managing a lawsuit","ar":"أي مستند متعلق برفع أو متابعة دعوى قضائية"}',
                'color' => '#3B82F6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Memorandum","ar":"مذكرة قانونية"}',
                'description' => '{"en":"Legal pleadings, briefs, and submissions","ar":"مذكرات ومرافعات ودفوع قانونية"}',
                'color' => '#10B981',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Court Judgment / Decision","ar":"حكم أو قرار قضائي"}',
                'description' => '{"en":"Judgments and court decisions","ar":"أحكام وقرارات صادرة من الجهات القضائية"}',
                'color' => '#F59E0B',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Enforcement Document","ar":"مستند تنفيذ"}',
                'description' => '{"en":"Documents related to enforcement procedures","ar":"مستندات مرتبطة بتنفيذ حكم أو سند"}',
                'color' => '#EF4444',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Contract","ar":"عقد قانوني"}',
                'description' => '{"en":"Any commercial, civil, or administrative contract","ar":"أي عقد تجاري أو مدني أو إداري"}',
                'color' => '#8B5CF6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Agreement","ar":"اتفاقية"}',
                'description' => '{"en":"Agreements regulating legal relationships","ar":"اتفاقيات بين أطراف لتنظيم علاقة قانونية"}',
                'color' => '#059669',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Corporate Document","ar":"مستند شركة"}',
                'description' => '{"en":"Corporate formation and governance documents","ar":"مستندات تأسيس أو قرارات أو تعديلات شركات"}',
                'color' => '#DC2626',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Power of Attorney","ar":"وكالة أو تفويض"}',
                'description' => '{"en":"Official authorization for representation","ar":"مستند تفويض رسمي بالتمثيل"}',
                'color' => '#6B7280',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Financial Legal Document","ar":"مستند مالي"}',
                'description' => '{"en":"Financial documents related to legal matters","ar":"مستندات مالية مرتبطة بقضية أو عقد"}',
                'color' => '#F97316',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Evidence Document","ar":"مستند إثبات"}',
                'description' => '{"en":"Supporting documents and expert reports","ar":"مستندات داعمة أو تقارير خبرة"}',
                'color' => '#84CC16',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Correspondence","ar":"مراسلة قانونية"}',
                'description' => '{"en":"Formal legal communications","ar":"خطابات ومخاطبات رسمية"}',
                'color' => '#06B6D4',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Opinion","ar":"رأي أو استشارة قانونية"}',
                'description' => '{"en":"Legal advisory or analytical memorandum","ar":"مذكرة تحليل أو استشارة قانونية"}',
                'color' => '#EC4899',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Labor Document","ar":"مستند عمالي"}',
                'description' => '{"en":"Employment-related legal documents","ar":"مستندات مرتبطة بقضايا العمل"}',
                'color' => '#6366F1',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Case Document","ar":"مستند جزائي"}',
                'description' => '{"en":"Criminal case-related documents","ar":"مستندات مرتبطة بقضايا جنائية"}',
                'color' => '#14B8A6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Administrative Case Document","ar":"مستند إداري"}',
                'description' => '{"en":"Administrative court-related documents","ar":"مستندات مرتبطة بالقضاء الإداري"}',
                'color' => '#F43F5E',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Report","ar":"تقرير قانوني"}',
                'description' => '{"en":"Internal or external legal reports","ar":"تقارير داخلية أو خارجية متعلقة بالقضية"}',
                'color' => '#EAB308',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Arbitration Document","ar":"مستند تحكيم"}',
                'description' => '{"en":"Arbitration-related documents","ar":"مستندات مرتبطة بإجراءات التحكيم"}',
                'color' => '#A855F7',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Compliance Document","ar":"مستند امتثال"}',
                'description' => '{"en":"Regulatory compliance documents","ar":"مستندات مرتبطة بالالتزام بالأنظمة"}',
                'color' => '#0EA5E9',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Client Document","ar":"مستند عميل"}',
                'description' => '{"en":"Client identification or contractual documents","ar":"مستندات تعريفية أو تعاقدية للعميل"}',
                'color' => '#22C55E',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"General Legal Document","ar":"مستند عام"}',
                'description' => '{"en":"Any legal document not falling under specific category","ar":"أي مستند قانوني لا يندرج تحت تصنيف محدد"}',
                'color' => '#78716C',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DocumentType::insert($documentTypes);

        Log::info("SeedDocumentTypes: Completed", [
            'company_id' => $this->tenant_id,
            'created' => count($documentTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedDocumentTypes: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage()
        ]);
    }
}

