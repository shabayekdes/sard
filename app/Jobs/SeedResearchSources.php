<?php

namespace App\Jobs;

use App\Enum\ResearchSourceType;
use App\Models\ResearchSource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default research sources for a company (AR/EN)
 */
class SeedResearchSources implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        public int $companyUserId
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $now = now();
        $sources = [
            [
                'source_name' => '{"ar":"وزارة العدل السعودية","en":"Saudi Ministry of Justice"}',
                'source_type' => ResearchSourceType::JUDICIAL_LAWS->value,
                'url' => 'https://www.moj.gov.sa',
                'description' => '{"ar":"نشر الأحكام والخدمات العدلية","en":"Publishes judgments and judicial services"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"منصة ناجز","en":"Najiz Platform"}',
                'source_type' => ResearchSourceType::E_JUDICIAL_PLATFORM->value,
                'url' => 'https://najiz.sa',
                'description' => '{"ar":"إدارة القضايا والتنفيذ والوكالات","en":"Case management, enforcement, POAs"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"هيئة الخبراء بمجلس الوزراء","en":"Bureau of Experts (KSA)"}',
                'source_type' => ResearchSourceType::LAWS_REGULATIONS->value,
                'url' => 'https://laws.boe.gov.sa',
                'description' => '{"ar":"المصدر الرسمي للأنظمة المحدثة","en":"Official source of updated Saudi laws"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"ديوان المظالم","en":"Board of Grievances"}',
                'source_type' => ResearchSourceType::ADMINISTRATIVE_JUDICIARY->value,
                'url' => 'https://www.bog.gov.sa',
                'description' => '{"ar":"أحكام القضاء الإداري","en":"Administrative court judgments"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"وزارة التجارة","en":"Ministry of Commerce (KSA)"}',
                'source_type' => ResearchSourceType::CORPORATE_COMMERCIAL->value,
                'url' => 'https://mc.gov.sa',
                'description' => '{"ar":"نظام الشركات والسجلات التجارية","en":"Corporate law and CR services"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"هيئة الزكاة والضريبة والجمارك","en":"ZATCA"}',
                'source_type' => ResearchSourceType::TAX_COMPLIANCE->value,
                'url' => 'https://zatca.gov.sa',
                'description' => '{"ar":"الأنظمة الضريبية والفوترة الإلكترونية","en":"Tax laws and e-invoicing regulations"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"البنك المركزي السعودي","en":"Saudi Central Bank (SAMA)"}',
                'source_type' => ResearchSourceType::FINANCIAL_REGULATION->value,
                'url' => 'https://www.sama.gov.sa',
                'description' => '{"ar":"لوائح القطاع المصرفي والتمويلي","en":"Banking and financial regulations"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"هيئة السوق المالية","en":"Capital Market Authority (CMA)"}',
                'source_type' => ResearchSourceType::CAPITAL_MARKET_REGULATION->value,
                'url' => 'https://cma.org.sa',
                'description' => '{"ar":"لوائح الاستثمار والشركات المدرجة","en":"Investment and listed companies regulations"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"الهيئة السعودية للملكية الفكرية","en":"Saudi Authority for IP (SAIP)"}',
                'source_type' => ResearchSourceType::INTELLECTUAL_PROPERTY->value,
                'url' => 'https://www.saip.gov.sa',
                'description' => '{"ar":"العلامات التجارية وبراءات الاختراع","en":"Trademarks and patent laws"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"وزارة الموارد البشرية","en":"Ministry of Human Resources (KSA)"}',
                'source_type' => ResearchSourceType::LABOR_LAW->value,
                'url' => 'https://hrsd.gov.sa',
                'description' => '{"ar":"نظام العمل ولوائحه","en":"Labor law and executive regulations"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"المركز السعودي للتحكيم التجاري","en":"Saudi Center for Commercial Arbitration"}',
                'source_type' => ResearchSourceType::ARBITRATION->value,
                'url' => 'https://sadr.org',
                'description' => '{"ar":"قواعد وأحكام التحكيم التجاري","en":"Commercial arbitration rules"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"مجلس الشورى","en":"Shura Council (KSA)"}',
                'source_type' => ResearchSourceType::LEGISLATIVE->value,
                'url' => 'https://www.shura.gov.sa',
                'description' => '{"ar":"مشاريع الأنظمة والتعديلات","en":"Draft laws and amendments"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"منصة اعتماد","en":"Etimad Platform"}',
                'source_type' => ResearchSourceType::GOVERNMENT_TENDERS->value,
                'url' => 'https://www.etimad.sa',
                'description' => '{"ar":"العقود والمشتريات الحكومية","en":"Government procurement platform"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"هيئة البيانات والذكاء الاصطناعي","en":"SDAIA"}',
                'source_type' => ResearchSourceType::DATA_PROTECTION->value,
                'url' => 'https://sdaia.gov.sa',
                'description' => '{"ar":"نظام حماية البيانات الشخصية","en":"Personal data protection law"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"ويستلو","en":"Westlaw"}',
                'source_type' => ResearchSourceType::INTERNATIONAL_DATABASE->value,
                'url' => 'https://www.westlaw.com',
                'description' => '{"ar":"أبحاث وسوابق قضائية عالمية","en":"Global legal research platform"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"ليكسيس نيكسيس","en":"LexisNexis"}',
                'source_type' => ResearchSourceType::INTERNATIONAL_DATABASE->value,
                'url' => 'https://www.lexisnexis.com',
                'description' => '{"ar":"قاعدة بيانات قانونية عالمية","en":"Global legal research database"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"هاين أونلاين","en":"HeinOnline"}',
                'source_type' => ResearchSourceType::ACADEMIC_DATABASE->value,
                'url' => 'https://heinonline.org',
                'description' => '{"ar":"مجلات قانونية تاريخية","en":"Legal journals and archives"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'source_name' => '{"ar":"الباحث العلمي من جوجل","en":"Google Scholar"}',
                'source_type' => ResearchSourceType::FREE_CASE_LAW->value,
                'url' => 'https://scholar.google.com',
                'description' => '{"ar":"بحث مجاني في الأحكام والمقالات","en":"Free case law and article search"}',
                'access_info' => null,
                'credentials' => null,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        ResearchSource::insert($sources);

        Log::info('SeedResearchSources: Completed', [
            'company_id' => $this->companyUserId,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SeedResearchSources: Job failed', [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage(),
        ]);
    }
}
