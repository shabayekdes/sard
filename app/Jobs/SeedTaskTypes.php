<?php

namespace App\Jobs;

use App\Models\TaskType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default task types for a company
 */
class SeedTaskTypes implements ShouldQueue
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
        $taskTypes = [
            [
                'name' => '{"en":"Meeting","ar":"اجتماع"}',
                'description' => '{"en":"Meeting with client, opponent, partner, or team","ar":"اجتماع مع عميل، خصم، شريك، أو فريق العمل"}',
                'color' => '#3B82F6',
                'default_duration' => 90,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Communication","ar":"اتصال / تواصل"}',
                'description' => '{"en":"Phone call, email, formal correspondence","ar":"مكالمة هاتفية، بريد إلكتروني، مراسلة رسمية"}',
                'color' => '#3B82F6',
                'default_duration' => 30,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Legal Research","ar":"بحث قانوني"}',
                'description' => '{"en":"Research in regulations, precedents, or bylaws","ar":"بحث في الأنظمة، السوابق، أو اللوائح"}',
                'color' => '#3B82F6',
                'default_duration' => 120,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Drafting","ar":"صياغة"}',
                'description' => '{"en":"Preparing memo, contract, application, or any legal document","ar":"إعداد مذكرة، عقد، طلب، أو أي مستند قانوني"}',
                'color' => '#3B82F6',
                'default_duration' => 180,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Review","ar":"مراجعة"}',
                'description' => '{"en":"Review documents, contracts, or memos before approval","ar":"مراجعة مستندات، عقود، أو مذكرات قبل اعتمادها"}',
                'color' => '#3B82F6',
                'default_duration' => 120,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Filing","ar":"تقديم"}',
                'description' => '{"en":"Filing documents or applications with competent authorities","ar":"رفع مستندات أو طلبات للجهات المختصة"}',
                'color' => '#3B82F6',
                'default_duration' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Appearance","ar":"حضور"}',
                'description' => '{"en":"Attending hearing, formal meeting, or arbitration","ar":"حضور جلسة، اجتماع رسمي، أو تحكيم"}',
                'color' => '#3B82F6',
                'default_duration' => 180,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Follow-up","ar":"متابعة"}',
                'description' => '{"en":"Follow-up on case, execution, or procedure with a specific entity","ar":"متابعة قضية، تنفيذ، أو إجراء مع جهة معينة"}',
                'color' => '#3B82F6',
                'default_duration' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Negotiation","ar":"تفاوض"}',
                'description' => '{"en":"Negotiating settlement or agreement","ar":"تفاوض على تسوية أو اتفاق"}',
                'color' => '#3B82F6',
                'default_duration' => 150,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Reporting","ar":"إعداد تقرير"}',
                'description' => '{"en":"Preparing internal report or case status update","ar":"إعداد تقرير داخلي أو تحديث حالة قضية"}',
                'color' => '#3B82F6',
                'default_duration' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Administrative","ar":"أعمال إدارية"}',
                'description' => '{"en":"Organizing files, updating data, archiving","ar":"تنظيم ملفات، تحديث بيانات، أرشفة"}',
                'color' => '#3B82F6',
                'default_duration' => 45,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Compliance","ar":"امتثال"}',
                'description' => '{"en":"Reviewing legal or regulatory compliance","ar":"مراجعة التزام قانوني أو تنظيمي"}',
                'color' => '#3B82F6',
                'default_duration' => 120,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Analysis","ar":"تحليل"}',
                'description' => '{"en":"Analyzing judgment, contract, or legal risk","ar":"تحليل حكم، عقد، مخاطرة قانونية"}',
                'color' => '#3B82F6',
                'default_duration' => 120,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Field Visit","ar":"زيارة ميدانية"}',
                'description' => '{"en":"Visit to government entity or case-related site","ar":"زيارة جهة حكومية أو موقع مرتبط بالقضية"}',
                'color' => '#3B82F6',
                'default_duration' => 120,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Financial Task","ar":"إعداد مالي"}',
                'description' => '{"en":"Preparing invoice, financial claim, or fee settlement","ar":"إعداد فاتورة، مطالبة مالية، تسوية أتعاب"}',
                'color' => '#3B82F6',
                'default_duration' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        TaskType::insert($taskTypes);

        Log::info('SeedTaskTypes: Completed', [
            'company_id' => $this->tenant_id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SeedTaskTypes: Job failed', [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
