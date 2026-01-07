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
        public int $companyUserId
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
                'name' => '{"en":"Client Identity","ar":"هوية العميل"}',
                'description' => '{"en":"National ID or residence permit + passport","ar":"هوية وطنية أو إقامة + جواز سفر"}',
                'color' => '#3B82F6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Commercial Registration","ar":"السجل التجاري"}',
                'description' => '{"en":"Valid commercial registration (for companies / establishments)","ar":"سجل تجاري ساري (للشركات / المؤسسات)"}',
                'color' => '#10B981',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Articles of Incorporation","ar":"عقد التأسيس"}',
                'description' => '{"en":"Company articles of incorporation","ar":"عقد تأسيس الشركة"}',
                'color' => '#F59E0B',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Authorized Signatory ID","ar":"هوية المفوض بالتوقيع"}',
                'description' => '{"en":"ID of the person authorized to sign","ar":"هوية الشخص المخوّل بالتوقيع"}',
                'color' => '#EF4444',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Power of Attorney / Authorization","ar":"التفويض / الوكالة"}',
                'description' => '{"en":"Legal power of attorney (Najiz) or official authorization","ar":"وكالة شرعية (ناجز) أو تفويض رسمي"}',
                'color' => '#8B5CF6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Services Contract","ar":"عقد الخدمات القانونية"}',
                'description' => '{"en":"Contract signed between the office and the client","ar":"عقد موقع بين المكتب والعميل"}',
                'color' => '#059669',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"National Address","ar":"العنوان الوطني"}',
                'description' => '{"en":"National address for the client or establishment","ar":"عنوان وطني للعميل أو المنشأة"}',
                'color' => '#DC2626',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Contact Information","ar":"بيانات التواصل"}',
                'description' => '{"en":"Mobile number and email address","ar":"رقم الجوال والبريد الإلكتروني"}',
                'color' => '#6B7280',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Partners / Board of Directors Resolution","ar":"قرار الشركاء / مجلس الإدارة"}',
                'description' => '{"en":"Approved resolution to contract with the office","ar":"قرار معتمد بالتعاقد مع المكتب"}',
                'color' => '#F97316',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Additional Documents","ar":"مستندات إضافية"}',
                'description' => '{"en":"Any supporting documents for the file","ar":"أي مستندات داعمة للملف"}',
                'color' => '#84CC16',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DocumentType::insert($documentTypes);

        Log::info("SeedDocumentTypes: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($documentTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedDocumentTypes: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

