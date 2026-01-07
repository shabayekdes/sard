<?php

namespace App\Jobs;

use App\Models\CircleType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default circle types for a company
 */
class SeedCircleTypes implements ShouldQueue
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
        // Color palette for circle types
        $colors = [
            '#3B82F6', '#10B981', '#EF4444', '#8B5CF6', '#F59E0B',
            '#DC2626', '#059669', '#F97316', '#84CC16', '#06B6D4',
            '#6B7280', '#EC4899', '#14B8A6', '#A855F7', '#F43F5E',
            '#0EA5E9', '#22C55E', '#EAB308', '#FB923C', '#9333EA',
            '#C026D3', '#6366F1', '#8B5CF6', '#EC4899', '#F43F5E',
            '#10B981', '#059669', '#0D9488', '#14B8A6', '#2DD4BF',
            '#06B6D4', '#0891B2', '#0EA5E9', '#0284C7', '#0369A1'
        ];

        $circleTypes = [
            [
                'name' => '{"en":"Final Court","ar":"الدائرة الانتهائية"}',
                'description' => null,
                'color' => $colors[0],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Final Court (Title Deeds)","ar":"الدائرة الانتهائية (حجج الإستحكام)"}',
                'description' => null,
                'color' => $colors[1],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Juvenile Court","ar":"الدائرة الأحداث"}',
                'description' => null,
                'color' => $colors[2],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Personal Status Court","ar":"الدائرة الأحوال الشخصية"}',
                'description' => null,
                'color' => $colors[3],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Execution Court","ar":"الدائرة التنفيذ"}',
                'description' => null,
                'color' => $colors[4],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court","ar":"الدائرة الجزائية"}',
                'description' => null,
                'color' => $colors[5],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Execution Court (Seizure and Enforcement Division)","ar":"الدائرة التنفيذ (دائرة الحجز والتنفيذ)"}',
                'description' => null,
                'color' => $colors[6],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (First Quintuple Division)","ar":"الدائرة الجزائية (الدائرة الخماسية الأولى)"}',
                'description' => null,
                'color' => $colors[7],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court","ar":"الدائرة القضائية"}',
                'description' => null,
                'color' => $colors[8],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Civil Court","ar":"الدائرة الحقوقية"}',
                'description' => null,
                'color' => $colors[9],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Criminal Division)","ar":"الدائرة القضائية (الدائرة الجزائية)"}',
                'description' => null,
                'color' => $colors[10],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Qisas and Hudud Divisions)","ar":"الدائرة القضائية (دوائر القصاص والحدود)"}',
                'description' => null,
                'color' => $colors[11],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Traffic Court","ar":"الدائرة المرورية"}',
                'description' => null,
                'color' => $colors[12],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Endowments and Wills Court","ar":"دائرة الأوقاف والوصايا"}',
                'description' => null,
                'color' => $colors[13],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Juvenile and Girls\' Courts","ar":"دوائر الأحداث والفتيات"}',
                'description' => null,
                'color' => $colors[14],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Personal Status Divisions","ar":"دوائر الأحوال الشخصية"}',
                'description' => null,
                'color' => $colors[15],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"First Notary Public","ar":"كتابة العدل الأولى"}',
                'description' => null,
                'color' => $colors[16],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court","ar":"الدائرة القضائية"}',
                'description' => null,
                'color' => $colors[17],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Traffic Court","ar":"الدائرة المرورية"}',
                'description' => null,
                'color' => $colors[18],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Endowments and Inheritance Division)","ar":"الدائرة القضائية (دائرة الأوقاف والمواريث)"}',
                'description' => null,
                'color' => $colors[19],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Second Notary Public","ar":"كتابة العدل الثانية"}',
                'description' => null,
                'color' => $colors[20],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (First Joint Division)","ar":"الدائرة الجزائية (المشتركة الأولى)"}',
                'description' => null,
                'color' => $colors[21],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (Second Joint Division)","ar":"الدائرة الجزائية (المشتركة الثانية)"}',
                'description' => null,
                'color' => $colors[22],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Third Joint Division)","ar":"الدائرة القضائية (المشتركة الثالثة)"}',
                'description' => null,
                'color' => $colors[23],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (First Individual Division)","ar":"الدائرة الجزائية (الفردية الأولى)"}',
                'description' => null,
                'color' => $colors[24],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (Second Individual Division)","ar":"الدائرة الجزائية (الفردية الثانية)"}',
                'description' => null,
                'color' => $colors[25],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (First Triple Division)","ar":"الدائرة الجزائية (الدائرة الثلاثية الأولى)"}',
                'description' => null,
                'color' => $colors[26],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (Third Triple Division)","ar":"الدائرة الجزائية (الدائرة الثلاثية الثالثة)"}',
                'description' => null,
                'color' => $colors[27],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court (Second Triple Division)","ar":"الدائرة الجزائية (الدائرة الثلاثية الثانية)"}',
                'description' => null,
                'color' => $colors[28],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Juvenile Division)","ar":"الدائرة القضائية (دائرة الأحداث)"}',
                'description' => null,
                'color' => $colors[29],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judicial Court (Women\'s Prison Division)","ar":"الدائرة القضائية (سجن النساء)"}',
                'description' => null,
                'color' => $colors[30],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Notary Public","ar":"كتابة العدل"}',
                'description' => null,
                'color' => $colors[31],
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        CircleType::insert($circleTypes);

        Log::info("SeedCircleTypes: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($circleTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCircleTypes: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

