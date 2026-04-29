<?php

return [
    'referral_stage' => [
        'amicable_settlement' => 'تسوية ودية',
        'reconciliation' => 'مصالحة',
        'first_instance' => 'ابتدائي',
        'appeal' => 'استئناف',
        'supreme_court' => 'تمييز',
        'execution' => 'تنفيذ',
    ],

    'entity' => [
        'case' => 'قضية',
        'hearing' => 'جلسة',
        'judgment' => 'حكم',
        'referral' => 'إحالة',
        'document' => 'مستند',
        'task' => 'مهمة',
        'note' => 'ملاحظة',
        'assignee' => 'مكلف',
        'timeline' => 'حدث',
    ],

    'msg' => [
        'case_created' => [
            'title' => 'إنشاء القضية',
            'description' => 'تم إنشاء القضية — :case_title',
        ],
        'case_updated' => [
            'title' => 'تعديل القضية',
            'description' => 'تم تعديل بيانات القضية',
        ],
        'case_status_changed' => [
            'title' => 'تغيير حالة القضية',
            'description' => 'تغيرت الحالة من :old إلى :new',
        ],
        'case_activated' => [
            'title' => 'تغيير حالة التفعيل',
            'description' => 'تم تفعيل القضية',
        ],
        'case_deactivated' => [
            'title' => 'تغيير حالة التفعيل',
            'description' => 'تم إيقاف القضية',
        ],
        'referral_created' => [
            'title' => 'إضافة إحالة',
            'description' => 'إحالة القضية إلى :stage',
        ],
        'referral_updated' => [
            'title' => 'تعديل إحالة',
            'description' => 'تم تعديل الإحالة — :stage',
        ],
        'referral_deleted' => [
            'title' => 'حذف إحالة',
            'description' => 'تم حذف الإحالة — :stage',
        ],
        'hearing_created' => [
            'title' => 'جلسة جديدة',
            'description' => 'جلسة جديدة — :hearing_title',
        ],
        'hearing_updated' => [
            'title' => 'تعديل جلسة',
            'description' => 'تم تعديل الجلسة — :hearing_title',
        ],
        'hearing_deleted' => [
            'title' => 'حذف جلسة',
            'description' => 'تم حذف الجلسة — :hearing_title',
        ],
        'judgment_created' => [
            'title' => 'إضافة حكم قضائي',
            'description' => 'تم إضافة حكم قضائي — :judgment_number',
        ],
        'judgment_updated' => [
            'title' => 'تعديل حكم',
            'description' => 'تم تعديل الحكم — :judgment_number',
        ],
        'judgment_deleted' => [
            'title' => 'حذف حكم',
            'description' => 'تم حذف الحكم — :judgment_number',
        ],
        'document_created' => [
            'title' => 'إضافة مستند',
            'description' => 'تم إضافة مستند — :document_name',
        ],
        'document_deleted' => [
            'title' => 'حذف مستند',
            'description' => 'تم حذف المستند — :document_name',
        ],
        'task_created' => [
            'title' => 'إضافة مهمة',
            'description' => 'تم إنشاء مهمة — :task_title',
        ],
        'task_completed' => [
            'title' => 'إغلاق مهمة',
            'description' => 'تم إغلاق المهمة — :task_title',
        ],
        'task_updated' => [
            'title' => 'تعديل مهمة',
            'description' => 'تم تعديل المهمة — :task_title',
        ],
        'task_deleted' => [
            'title' => 'حذف مهمة',
            'description' => 'تم حذف المهمة — :task_title',
        ],
        'assignee_added' => [
            'title' => 'إضافة مكلف',
            'description' => 'تم تعيين :user_name على القضية',
        ],
        'assignee_removed' => [
            'title' => 'حذف مكلف',
            'description' => 'تم إزالة :user_name من القضية',
        ],
        'note_created' => [
            'title' => 'إضافة ملاحظة',
            'description' => 'تم إضافة ملاحظة — :preview',
        ],
        'note_updated' => [
            'title' => 'تعديل ملاحظة',
            'description' => 'تم تعديل الملاحظة',
        ],
    ],
];
