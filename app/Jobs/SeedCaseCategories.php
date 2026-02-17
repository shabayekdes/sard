<?php

namespace App\Jobs;

use App\Models\CaseCategory;
use App\Models\CaseType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default case categories for a company
 */
class SeedCaseCategories implements ShouldQueue
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
    )
    {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (CaseCategory::where('created_by', $this->companyUserId)->exists()) {
            Log::info('SeedCaseCategories: Categories already exist, skipping', [
                'company_id' => $this->companyUserId,
            ]);
            return;
        }

        $seedData = [
            [
                "name" => [
                    "ar" => "أحوال شخصية",
                    "en" => "Personal Status",
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => "التصنيف العام",
                            "en" => "General Classification",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "إثبات رضاعة أو مصاهرة",
                                    "en" => "Proof of Breastfeeding or Affinity",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إقامة حارس قضائي",
                                    "en" => "Appointment of a Judicial Guardian",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التعويض عن أضرار التقاضي",
                                    "en" => "Compensation for Litigation Damages",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "مطالبة بمستندات",
                                    "en" => "Request for Documents",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "معارضة على صك إنهائي",
                                    "en" => "Objection to Final Deed",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "منع من السفر",
                                    "en" => "Travel Ban",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات هبة بين زوجين أو رجوع عنها",
                                    "en" => "Proof of Gift Between Spouses or Revocation",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات هبة لوارث أو نقضها",
                                    "en" => "Proof of Gift to Heir or Revocation",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "دعاوى الأوقاف والوصايا",
                            "en" => "Endowment and Will Claims",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "إبطال وقف أو وصية (ضد واقف)",
                                    "en" => "Annulment of Endowment or Will (Against Endower)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إبطال وقف أو وصية ( ضد ورثة الواقف)",
                                    "en" => "Annulment of Endowment or Will (Against Heirs of Endower)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات وقف أو وصية (إثبات وصيه)",
                                    "en" => "Proof of Endowment or Will (Proof of Executor)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات استحقاق في (وقف / وصية)",
                                    "en" => "Proof of Entitlement in (Endowment/Will)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "تسليم استحقاق في (وقف / وصية)",
                                    "en" => "Delivery of Entitlement in (Endowment/Will)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "منازعة في عقد استثمار أو تعمير",
                                    "en" => "Dispute in Investment or Development Contract",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "عزل ناظر (وقف / وصية)",
                                    "en" => "Removal of Trustee (Endowment/Will)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "محاسبة ناظر وقف أو وصية",
                                    "en" => "Accounting of Trustee (Endowment/Will)",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "دعاوى الحضانة والزيارة والنفقة",
                            "en" => "Custody, Visitation, and Alimony Claims",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "أجرة رضاع أو حضانة",
                                    "en" => "Nursing or Custody Fees",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "تسليم صغير لحاضنه",
                                    "en" => "Delivery of Child to Custodian",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "حضانة (الأولاد / غير الأولاد)",
                                    "en" => "Custody (Children/Non-Children)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "رؤية صغير",
                                    "en" => "Child Visitation",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "زيادة نفقة أو إنقاصها أو إلغائها",
                                    "en" => "Increase, Decrease, or Cancellation of Alimony",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "زيارة أولاد أو غيرهم",
                                    "en" => "Visitation of Children or Others",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "نفقة (ماضية / مستقبلية) للزوجة والأولاد",
                                    "en" => "Alimony (Past/Future) for Wife and Children",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "نفقة (ماضية / مستقبلية) لغير الزوجة والأولاد",
                                    "en" => "Alimony (Past/Future) for Non-Wife and Non-Children",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "نفقة (ماضية / مستقبلية) السكن",
                                    "en" => "Housing Alimony (Past/Future)",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "دعاوى النكاح والفرقة",
                            "en" => "Marriage and Separation Claims",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "إثبات طلاق",
                                    "en" => "Proof of Divorce",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات نكاح",
                                    "en" => "Proof of Marriage",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "خلع",
                                    "en" => "Khul' (Divorce Initiated by Wife)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "صداق",
                                    "en" => "Mahr (Bridal Gift)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "عفش الزوجية",
                                    "en" => "Marital Furniture",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "فسخ نكاح",
                                    "en" => "Annulment of Marriage",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "دعاوى الولاية",
                            "en" => "Guardianship Claims",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "إثبات نسب أو نفيه ضد زوج أو زوج سابق",
                                    "en" => "Proof or Denial of Lineage Against Spouse or Ex-Spouse",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إثبات نسب أو نفيه ضد أحد الأبوين أو الأقارب",
                                    "en" => "Proof or Denial of Lineage Against One Parent or Relatives",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "إذن سفر",
                                    "en" => "Travel Permission",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "حجر أو رفعه",
                                    "en" => "Guardianship or Lifting of Guardianship",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "عزل ولي",
                                    "en" => "Removal of Guardian",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "عضل",
                                    "en" => "Prevention of Marriage",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "محاسبة ولي",
                                    "en" => "Accounting of Guardian",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "دعاوى قسمة التركات",
                            "en" => "Inheritance Division Claims",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "دعوى قسمة تركة أكثر من خمسين مليون ريال",
                                    "en" => "Inheritance Division Claim over 50 Million SAR",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "قسمة تركة (عقارية / مالية)",
                                    "en" => "Division of Inheritance (Real Estate/Financial)",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "محاسبة في تركة",
                                    "en" => "Accounting in Inheritance",
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "name" => [
                    "ar" => "تجاري",
                    "en" => "Commercial",
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => "الاستئناف",
                            "en" => "Appeal",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "الاستئناف",
                                    "en" => "Appeal",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التحكيم (تجاري)",
                                    "en" => "Arbitration (Commercial)",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "الأنظمة التجارية",
                            "en" => "Commercial Laws",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "الاستثمار الأجنبي",
                                    "en" => "Foreign Investment",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "البيانات التجارية",
                                    "en" => "Commercial Data",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التجارة الإلكترونية",
                                    "en" => "E-Commerce",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التجارة البحرية",
                                    "en" => "Maritime Commerce",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الامتياز التجاري",
                                    "en" => "Franchise",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الرهن التجاري",
                                    "en" => "Commercial Mortgage",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "السجل التجاري",
                                    "en" => "Commercial Register",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "مكافحة الغش التجاري",
                                    "en" => "Anti-Commercial Fraud",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الأوراق التجارية",
                                    "en" => "Commercial Papers",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "المنافسة غير المشروعة",
                                    "en" => "Unfair Competition",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الوكالات التجارية",
                                    "en" => "Commercial Agencies",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الأسماء التجارية",
                                    "en" => "Trade Names",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "حماية الأسرار التجارية",
                                    "en" => "Protection of Trade Secrets",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "الشركات",
                            "en" => "Companies",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "شركات المضاربة",
                                    "en" => "Speculative Companies",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الشركات النظامية",
                                    "en" => "Systemic Companies",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "الطلبات القضائيـة",
                            "en" => "Judicial Requests",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "دعوى الضرر بين التجار بسبب المسؤولية التقصيرية",
                                    "en" => "Tort Liability Claims between Merchants",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "المعينين في القضايا التجارية",
                                    "en" => "Appointees in Commercial Cases",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "أوامر الأداء",
                                    "en" => "Orders of Performance",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التعويض عن مصاريف التقاضي",
                                    "en" => "Compensation for Litigation Expenses",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "العقود التجارية",
                            "en" => "Commercial Contracts",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "إجارة",
                                    "en" => "Lease",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الحوالة التجارية",
                                    "en" => "Commercial Transfer",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الدعاية والإعلان والتسويق",
                                    "en" => "Advertising and Marketing",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الكفالة التجارية",
                                    "en" => "Commercial Guarantee",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "المقاولات",
                                    "en" => "Contracting",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "المكاتب والمحلات التجارية",
                                    "en" => "Offices and Commercial Shops",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "النقل",
                                    "en" => "Transportation",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "بيع وتوريد",
                                    "en" => "Sale and Supply",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "سمسرة",
                                    "en" => "Brokerage",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "الملكية الفكرية",
                            "en" => "Intellectual Property",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "الملكية الفكرية",
                                    "en" => "Intellectual Property",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "حماية حقوق المؤلف",
                                    "en" => "Copyright Protection",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "براءات الاختراع",
                                    "en" => "Patents",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "العلامات التجارية",
                                    "en" => "Trademarks",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => "الدعاوى المستعجلـة",
                            "en" => "Urgent Cases",
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => "حجز تحفظي",
                                    "en" => "Precautionary Seizure",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "حراسة قضائية",
                                    "en" => "Judicial Custody",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "منع من السفر",
                                    "en" => "Travel Ban",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "وقف الأعمال الجديدة",
                                    "en" => "Stop New Works",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "استيثاق لإثبات الحالة أو شهادة يخشى فواتها",
                                    "en" => "Verification to Establish Case or Testimony Fearing Loss",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "وقف تنفيذ",
                                    "en" => "Suspension of Execution",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "الحصول على عينة من منتج",
                                    "en" => "Obtain a Sample of Product",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "التحفظ على مستندات معينة",
                                    "en" => "Seizure of Specific Documents",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "(المنع / الإذن) من التصرف",
                                    "en" => "(Prohibition/Permission) of Disposal",
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => "طلبات عاجلة أخرى",
                                    "en" => "Other Urgent Requests",
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($seedData as $item) {
            $category = CaseCategory::create([
                'name' => $item['name'],
                'parent_id' => null,
                'created_by' => $this->companyUserId,
                'status' => 'active',
            ]);

            foreach ($item['subcategories'] as $subcategory) {
                $subCategoryRecord = CaseCategory::create([
                    'name' => $subcategory['name'],
                    'parent_id' => $category->id,
                    'created_by' => $this->companyUserId,
                    'status' => 'active',
                ]);

                foreach ($subcategory['types'] as $type) {
                    CaseType::create([
                        'name' => $type['name'],
                        'case_category_id' => $subCategoryRecord->id,
                        'created_by' => $this->companyUserId,
                        'status' => 'active',
                    ]);
                }
            }
        }

        Log::info('SeedCaseCategories: Completed', [
            'company_id' => $this->companyUserId,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SeedCaseCategories: Job failed', [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage(),
        ]);
    }
}
