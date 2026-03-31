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
        public \App\Models\Tenant $tenant
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        tenancy()->initialize($this->tenant);

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
            [
                "name" => [
                    "ar" => 'تنفيذ',
                    "en" => 'Execution',
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => 'الامتناع عن قبول السند',
                            "en" => 'Refusal to Accept Bond',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'الامتناع عن قبول السند',
                                    "en" => 'Refusal to Accept Bond',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'دعوى الإعسار أو الملاءة',
                            "en" => 'Insolvency or Solvency Claim',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'دعوى الإعسار أو الملاءة',
                                    "en" => 'Insolvency or Solvency Claim',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'منازعات شكلية',
                            "en" => 'Formal Disputes',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'عدم توفر شرط شكلي للسند أو تزويره أو إنكار التوقيع',
                                    "en" => 'Lack of Formal Condition for Bond, Forgery, or Signature Denial',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عدم الصفة',
                                    "en" => 'Lack of Capacity',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'منازعات غير شكلية',
                            "en" => 'Substantive Disputes',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'الإبراء بعد صدور السند التنفيذي',
                                    "en" => 'Discharge after Issuance of Execution Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التأجيل بعد صدور السند التنفيذي',
                                    "en" => 'Postponement after Issuance of Execution Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الحوالة بعد صدور السند التنفيذي',
                                    "en" => 'Transfer after Issuance of Execution Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الصلح بعد صدور السند التنفيذي',
                                    "en" => 'Settlement after Issuance of Execution Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'المال المحجوز يفوق مقدار الدين المطالب به',
                                    "en" => 'Seized Funds Exceed Claimed Debt',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'المقاصة بموجب سند تنفيذي',
                                    "en" => 'Offset under Executive Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الوفاء بعد صدور السند التنفيذي',
                                    "en" => 'Payment after Issuance of Execution Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'امتناع شاغل العقار عن الاخلاء لحمله سند تنفيذي',
                                    "en" => 'Property Occupant Refusal to Vacate due to Executive Bond',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تواطؤ أثناء المزاد أو التأثير على سعر المزاد',
                                    "en" => 'Collusion during Auction or Price Manipulation',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'رد ما استوفي خطأ',
                                    "en" => 'Refund of Incorrectly Collected Amount',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'صحة تقرير المحجوز لديه بما في ذمته',
                                    "en" => 'Accuracy of Seizure Report',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عيب في العين المباعة',
                                    "en" => 'Defect in Sold Item',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'دعوى التعويض',
                                    "en" => 'Compensation Claim',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'المنازعة في أجرة الحارس القضائي أو محاسبته أو استبداله',
                                    "en" => 'Dispute over Judicial Custodian\'s Fees, Accounting, or Replacement',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'المحاصة',
                                    "en" => 'Pooling of Funds',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أتعاب المحاماة أو الوكلاء',
                                    "en" => 'Attorney or Agent Fees',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التعويض عن أضرار التقاضي',
                                    "en" => 'Compensation for Litigation Damages',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "name" => [
                    "ar" => 'جزائية',
                    "en" => 'Criminal',
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => 'الحق الخاص',
                            "en" => 'Private Right',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'المطالبة بالحق الخاص',
                                    "en" => 'Private Right Claim',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الطلبات القضائية',
                            "en" => 'Judicial Requests',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'منع من السفر',
                                    "en" => 'Travel Ban',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات تنازل',
                                    "en" => 'Proof of Waiver',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تسليم مضبوطات',
                                    "en" => 'Delivery of Seized Items',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الحراسة القضائية',
                                    "en" => 'Judicial Custody',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الحجز التحفظي',
                                    "en" => 'Precautionary Seizure',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استيثاق لإثبات الحالة أو شهادة يخشى فواتها',
                                    "en" => 'Verification to Establish Case or Testimony Fearing Loss',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وقف الأعمال الجديدة',
                                    "en" => 'Stop New Works',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'حدود',
                            "en" => 'Hudud (Islamic Penalties)',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'قذف',
                                    "en" => 'Defamation',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'قصاص',
                            "en" => 'Retribution',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => '(نفس / ما دون النفس)',
                                    "en" => '(Life/Non-Life)',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'مطالبة مالية',
                            "en" => 'Financial Claim',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => '(أرش / دية / رد العين)',
                                    "en" => '(Arsh/Blood Money/Return of Property)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التعويض عن السجن',
                                    "en" => 'Compensation for Imprisonment',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التعويض عن أضرار التقاضي',
                                    "en" => 'Compensation for Litigation Damages',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "name" => [
                    "ar" => 'عامة',
                    "en" => 'General',
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => 'عامة أخرى',
                            "en" => 'General Other',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'إثبات عقد',
                                    "en" => 'Proof of Contract',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'رفع ضرر أو التعويض عنه',
                                    "en" => 'Removal of Harm or Compensation',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'رد العين',
                                    "en" => 'Return of Property',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عقد استصناع',
                                    "en" => 'Manufacturing Contract',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'فسخ عقد أو بطلانه',
                                    "en" => 'Contract Annulment or Invalidity',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'قسمة منافع مهايأة',
                                    "en" => 'Division of Usufruct',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مطالبة بمستندات (سند لأمر صك شيك كمبيالة عقد أخرى)',
                                    "en" => 'Request for Documents (Promissory Note, Deed, Check, Bill of Exchange, Contract, etc.)',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الدعاوى المستعجلة',
                            "en" => 'Urgent Cases',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'منع من السفر',
                                    "en" => 'Travel Ban',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وقف الأعمال الجديدة (تشمل ما تختص المحكمة العامة بنظره)',
                                    "en" => 'Stop New Works (Under General Court Jurisdiction)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وقف الأعمال الجديدة (تقام كدعوى مستعجلة قبل الانتهاء من الأعمال)',
                                    "en" => 'Stop New Works (Filed as Urgent Case Before Work Completion)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وقف الأعمال الجديدة (إذا تم الانتهاء من العمل فترفع "دعوى رفع ضرر)',
                                    "en" => 'Stop New Works (If Work is Completed, File "Harm Removal" Case)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة الأجير اليومية أو الأسبوعية',
                                    "en" => 'Daily or Weekly Worker’s Wage',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استرداد حيازة عقار',
                                    "en" => 'Recovery of Property Possession',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استيثاق لإثبات الحالة أو شهادة يخشى فواتها',
                                    "en" => 'Verification to Establish Case or Testimony Fearing Loss',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'منع التعرض للحيازة',
                                    "en" => 'Prevent Interference with Possession',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حجز تحفظي',
                                    "en" => 'Precautionary Seizure',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'عقارية',
                            "en" => 'Real Estate',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'تداخل عقارات',
                                    "en" => 'Overlapping Properties',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق الشفعة',
                                    "en" => 'Right of Preemption',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ملكية عقار (تسليم عقار مشترى من المدعى عليه)',
                                    "en" => 'Property Ownership (Delivery of Purchased Property from Defendant)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ملكية عقار (سبق تملك العقار)',
                                    "en" => 'Property Ownership (Previous Ownership)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ملكية عقار (ملكية عقار مسجل باسم المدعى عليه صوريا)',
                                    "en" => 'Property Ownership (Property Fictitiously Registered in Defendant\'s Name)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ملكية عقار (هبة / وصية)',
                                    "en" => 'Property Ownership (Gift/Will)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'قسمة عقارات مشتركة',
                                    "en" => 'Division of Joint Properties',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مساهمة عقارية',
                                    "en" => 'Real Estate Contribution',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وضع اليد على المسيل أو الحمى (بتملك / أو بدون)',
                                    "en" => 'Taking Possession of Watercourse or Sanctuary (With or Without Ownership)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مسايل أو حمى إغلاق مسيل المياه عن المدعي عليا أو جزئيا (المشاحة في الماء)',
                                    "en" => 'Watercourse or Sanctuary - Closing Watercourse Against Plaintiff Fully or Partially (Water Dispute)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مسايل أو حمى تحويل المياه إلى ملك المدعي ومضارته بذلك',
                                    "en" => 'Watercourse or Sanctuary - Diverting Water to Plaintiff’s Property and Causing Harm',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التعدي على المسيل أو الحمى (كالجرف أو أخذ الأطيان والرمال منه)',
                                    "en" => 'Trespassing on Watercourse or Sanctuary (E.g., Excavation or Taking Soil/Sand)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مقاولات إنشاء مباني',
                                    "en" => 'Building Construction Contracts',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'مالية',
                            "en" => 'Financial',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'قرض أو سلف',
                                    "en" => 'Loan or Advance',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'هبة في غير عقار',
                                    "en" => 'Non-Real Estate Gift',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة أعمال',
                                    "en" => 'Work Fees',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة عقار (طلب فاتورة خدمات)',
                                    "en" => 'Property Rent (Request for Service Invoice)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة عقار (طلب أجرة)',
                                    "en" => 'Property Rent (Rent Request)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة عقار (طلب تلفيات)',
                                    "en" => 'Property Rent (Damage Request)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة عين منقول',
                                    "en" => 'Movable Property Rent',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ثمن مبيع',
                                    "en" => 'Sale Price',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حوالة الدين من ذمة شخص لآخر',
                                    "en" => 'Debt Transfer from One Person to Another',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'شراكة في أملاك غير عقارية',
                                    "en" => 'Partnership in Non-Real Estate Assets',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ضمان (كفالة)',
                                    "en" => 'Guarantee (Bail)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'محاسبة وكيل',
                                    "en" => 'Agent Accounting',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مطالبة الضامن للمضمون عنه كفيل لمكفوله',
                                    "en" => 'Guarantor’s Claim Against Debtor (Bailor Claiming from Bailee)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أرش إصابة أو دية المطالبة بدية القتل (في غير حادث مروري)',
                                    "en" => 'Injury Compensation or Blood Money - Claiming Blood Money for Homicide (Non-Traffic Incident)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'وديعة',
                                    "en" => 'Deposit',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'مروري',
                            "en" => 'Traffic',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'حق خاص (الدية المقدرة شرعًا)',
                                    "en" => 'Private Right (Legally Prescribed Blood Money)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (أرش التلفيات)',
                                    "en" => 'Private Right (Compensation for Damages)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (أجرة شحن المركبة)',
                                    "en" => 'Private Right (Vehicle Shipping Fee)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (تعديل تقدير التلفيات)',
                                    "en" => 'Private Right (Adjustment of Damage Estimation)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (المطالبة بالتعويض عن مدة توقف السيارة)',
                                    "en" => 'Private Right (Compensation for Vehicle Downtime)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (تعديل النسبة في الحادث)',
                                    "en" => 'Private Right (Adjustment of Accident Liability Percentage)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (إثبات صحة الحادث وعدم صوريته)',
                                    "en" => 'Private Right (Proof of Accident Authenticity)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (أرش الجروح والشجاج الإصابات)',
                                    "en" => 'Private Right (Injury and Wound Compensation)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (إلزام العاقلة بالدية)',
                                    "en" => 'Private Right (Obligation of Family to Pay Blood Money)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (إلزام بيت المال بالدية)',
                                    "en" => 'Private Right (Obligation of Public Treasury to Pay Blood Money)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (دعوى شركات التأجير ضد المستأجرين حال عدم تغطية وثيقة التأمين)',
                                    "en" => 'Private Right (Rental Companies\' Claims Against Renters in Case of Insufficient Insurance Coverage)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (الدعوى ضد الكفيل في الحادث المروري)',
                                    "en" => 'Private Right (Claim Against Guarantor in Traffic Accident)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حق خاص (دعوى الكفيل على مكفوله)',
                                    "en" => 'Private Right (Guarantor’s Claim Against Debtor)',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'طبي',
                            "en" => 'Medical',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'طبي',
                                    "en" => 'Medical',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الاستئناف',
                            "en" => 'Appeal',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'الاستئناف',
                                    "en" => 'Appeal',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التحكيم (عامة)',
                                    "en" => 'Arbitration (General)',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "name" => [
                    "ar" => 'عمالية',
                    "en" => 'Labor',
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => 'الطلبات العارضة والعاجلة',
                            "en" => 'Incidental and Urgent Requests',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'منع من السفر',
                                    "en" => 'Travel Ban',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أجرة الأجير اليومية أو الأسبوعية',
                                    "en" => 'Daily or Weekly Worker’s Wage',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إيقاف التنفيذ',
                                    "en" => 'Suspension of Execution',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استيثاق لإثبات الحالة أو شهادة يخشى فواتها',
                                    "en" => 'Verification to Establish Case or Testimony Fearing Loss',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'حجز تحفظي',
                                    "en" => 'Precautionary Seizure',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'حقوق مالية',
                            "en" => 'Financial Rights',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => '(مكافأة / أجر / بدل أخرى)',
                                    "en" => '(Bonus/Wage/Other Allowance)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'دفع أو استرداد الرسوم',
                                    "en" => 'Payment or Refund of Fees',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'قيمة متلف',
                                    "en" => 'Value of Damaged Property',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'مبالغ مالية أنفقها العامل لصالح العمل',
                                    "en" => 'Money Spent by Worker for Work-Related Purposes',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'العمولات',
                                    "en" => 'Commissions',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'حقوق وظيفية',
                            "en" => 'Employment Rights',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'تأمين (سكن / سفر)',
                                    "en" => 'Insurance (Housing/Travel)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ترقية أو علاوة',
                                    "en" => 'Promotion or Raise',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تسكين على وظيفة أو تعديل أو مساواة في المرتبة',
                                    "en" => 'Job Placement, Adjustment, or Rank Equalization',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تسليم عهدة أو استرداد سلفة',
                                    "en" => 'Delivery of Custody or Recovery of Advance',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تمكين من العمل',
                                    "en" => 'Enabling Employment',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'مستندات ووثائق',
                            "en" => 'Documents',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'نسخة من عقد العمل',
                                    "en" => 'Copy of Employment Contract',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'شهادة الخدمة',
                                    "en" => 'Service Certificate',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'أخرى',
                                    "en" => 'Other',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'طلبات تعويض',
                            "en" => 'Compensation Requests',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'التعويض عن عدم التسجيل في التأمينات',
                                    "en" => 'Compensation for Non-Registration in Insurance',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إنهاء العلاقة العمالية من صاحب العمل',
                                    "en" => 'Termination of Employment by Employer',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إنهاء العلاقة العمالية من العامل',
                                    "en" => 'Termination of Employment by Worker',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عدم إلتزام صاحب العمل بمهلة الإشعار',
                                    "en" => 'Employer’s Failure to Comply with Notice Period',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عدم إلتزام العامل بمهلة الإشعار',
                                    "en" => 'Worker’s Failure to Comply with Notice Period',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التعويض عن أضرار التقاضي',
                                    "en" => 'Compensation for Litigation Damages',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'رصيد الإجازات',
                                    "en" => 'Vacation Balance',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إصابة العمل',
                                    "en" => 'Work Injury',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عدم المنافسة وحماية الأسرار',
                                    "en" => 'Non-Competition and Trade Secrets Protection',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الاستئناف',
                            "en" => 'Appeal',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'الاستئناف',
                                    "en" => 'Appeal',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'التحكيم (عمالي)',
                                    "en" => 'Arbitration (Labor)',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "name" => [
                    "ar" => 'إنهاءات',
                    "en" => 'Terminations',
                ],
                "subcategories" => [
                    [
                        "name" => [
                            "ar" => 'الولايات',
                            "en" => 'Guardianships',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'إقامة ولاية على ( قاصر سناً / قاصر عقلاً)',
                                    "en" => 'Appointment of Guardianship over (Minor by Age/Minor by Mental Capacity)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استمرار ولاية على قاصر عقلاً',
                                    "en" => 'Continuation of Guardianship over Mentally Incapacitated Minor',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات رشد من كان قاصراً عقلاً',
                                    "en" => 'Proof of Maturity for Mentally Incapable',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'فسخ ولاية بطلب من الولي',
                                    "en" => 'Guardianship Annulment at Guardian\'s Request',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تقدير نفقة قاصر',
                                    "en" => 'Estimation of Minor’s Alimony',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'فسخ الولاية لموت الولي',
                                    "en" => 'Guardianship Annulment Due to Guardian’s Death',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => '(إقامة / فسخ) ولاية على مال مفقود',
                                    "en" => '(Appointment/Annulment) of Guardianship over Lost Property',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'ولاية ورعاية وحضانة يتيم أو ذوي احتياجات خاصة',
                                    "en" => 'Guardianship and Custody of Orphan or Special Needs Individual',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'إثباتات اجتماعية',
                            "en" => 'Social Proofs',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'تنازل عن أرش إصابة أو دية غير العمد وشبه العمد',
                                    "en" => 'Waiver of Injury Compensation or Blood Money for Unintentional or Quasi-Intentional Acts',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الموافقة على الزواج المبكر',
                                    "en" => 'Early Marriage Approval',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات انقطاع الأولياء عن المرأة لغرض الزواج',
                                    "en" => 'Proof of Guardians\' Absence for Woman’s Marriage',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'عقد زواج بولاية القاضي',
                                    "en" => 'Marriage Contract Under Judge’s Guardianship',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات أقرب ولي لتزويج',
                                    "en" => 'Proof of Closest Guardian for Marriage',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات فقد وغيبة',
                                    "en" => 'Proof of Absence and Disappearance',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات تبين حال مفقود بالسلامة والحضور',
                                    "en" => 'Proof of Missing Person’s Safety and Presence',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الأوقاف والوصايا',
                            "en" => 'Endowments and Wills',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'إقامة ناظر على وقف',
                                    "en" => 'Appointment of Trustee for Endowment',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إذن تصرف بأملاك وقف أو وصية',
                                    "en" => 'Permission to Dispose of Endowment or Will Property',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تعديل وإضافة لصك الوقف أو الوصية',
                                    "en" => 'Amendment and Addition to Endowment or Will Deed',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'طلب المستثمر فسخ أو تعديل على عقد الاستثمار المأذون به من المحكمة',
                                    "en" => 'Investor’s Request to Annul or Amend Investment Contract Authorized by Court',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تسليم أموال الوقف أو الوصية المودعة لدى الهيئة',
                                    "en" => 'Delivery of Endowment or Will Funds Deposited with the Authority',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إقامة مشرف على وقف أو وصية',
                                    "en" => 'Appointment of Supervisor for Endowment or Will',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'استقالة ناظر على وقف أو وصية',
                                    "en" => 'Resignation of Trustee for Endowment or Will',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات وقف أو وصية لمتوفى ومن ضمن الورثة غائب أو قاصر أو مفقود',
                                    "en" => 'Proof of Endowment or Will for Deceased with Absent, Minor, or Missing Heir',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'الأذونات',
                            "en" => 'Authorizations',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'إذن التصرف بأملاك القاصر أو المفقود أو الغائب',
                                    "en" => 'Permission to Dispose of Minor, Missing, or Absent Person’s Property',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'تعديلات الصكوك',
                            "en" => 'Deed Amendments',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'تعديل وتهميش على صك إنهائي',
                                    "en" => 'Amendment and Marginal Note on Final Deed',
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => [
                            "ar" => 'إنهاءات الأوقاف والمواريث في الأحساء والقطيف',
                            "en" => 'Endowments and Inheritance Terminations in Al-Ahsa and Al-Qatif',
                        ],
                        "types" => [
                            [
                                "name" => [
                                    "ar" => 'إثبات (عقد زواج / زواج سابق)',
                                    "en" => 'Proof of (Marriage Contract/Previous Marriage)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'تصديق عقد زواج',
                                    "en" => 'Marriage Contract Certification',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات (رجعة / طلاق / خلع / حضانة)',
                                    "en" => 'Proof of (Return/Divorce/Khul’/Custody)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات زواج أحد الزوجين غير سعودي',
                                    "en" => 'Proof of Marriage with Non-Saudi Spouse',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات ورثة متوفى',
                                    "en" => 'Proof of Heirs of Deceased',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'الموافقة على الزواج المبكر',
                                    "en" => 'Early Marriage Approval',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'إثبات (وقف / وصية)',
                                    "en" => 'Proof of (Endowment/Will)',
                                ],
                            ],
                            [
                                "name" => [
                                    "ar" => 'قسمة تركة بالتراضي مع وجود قاصر أو غائب أو مفقود أو وقف أو وصية',
                                    "en" => 'Amicable Inheritance Division with Minor, Absent, or Missing Heir or Endowment/Will',
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
                'tenant_id' => $this->tenant->id,
                'status' => 'active',
            ]);

            foreach ($item['subcategories'] as $subcategory) {
                $subCategoryRecord = CaseCategory::create([
                    'name' => $subcategory['name'],
                    'parent_id' => $category->id,
                    'tenant_id' => $this->tenant->id,
                    'status' => 'active',
                ]);

                foreach ($subcategory['types'] as $type) {
                    CaseType::create([
                        'name' => $type['name'],
                        'case_category_id' => $subCategoryRecord->id,
                        'tenant_id' => $this->tenant->id,
                        'status' => 'active',
                    ]);
                }
            }
        }

        Log::info('SeedCaseCategories: Completed', [
            'company_id' => $this->tenant->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SeedCaseCategories: Job failed', [
            'company_id' => $this->tenant->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
