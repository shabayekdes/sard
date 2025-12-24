<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        // Default document types with Arabic translations
        $documentTypes = [
            [
                'name' => [
                    'en' => 'Client Identity',
                    'ar' => 'هوية العميل',
                ],
                'description' => [
                    'en' => 'National ID or residence permit + passport',
                    'ar' => 'هوية وطنية أو إقامة + جواز سفر',
                ],
                'color' => '#3B82F6',
            ],
            [
                'name' => [
                    'en' => 'Commercial Registration',
                    'ar' => 'السجل التجاري',
                ],
                'description' => [
                    'en' => 'Valid commercial registration (for companies / establishments)',
                    'ar' => 'سجل تجاري ساري (للشركات / المؤسسات)',
                ],
                'color' => '#10B981',
            ],
            [
                'name' => [
                    'en' => 'Articles of Incorporation',
                    'ar' => 'عقد التأسيس',
                ],
                'description' => [
                    'en' => 'Company articles of incorporation',
                    'ar' => 'عقد تأسيس الشركة',
                ],
                'color' => '#F59E0B',
            ],
            [
                'name' => [
                    'en' => 'Authorized Signatory ID',
                    'ar' => 'هوية المفوض بالتوقيع',
                ],
                'description' => [
                    'en' => 'ID of the person authorized to sign',
                    'ar' => 'هوية الشخص المخوّل بالتوقيع',
                ],
                'color' => '#EF4444',
            ],
            [
                'name' => [
                    'en' => 'Power of Attorney / Authorization',
                    'ar' => 'التفويض / الوكالة',
                ],
                'description' => [
                    'en' => 'Legal power of attorney (Najiz) or official authorization',
                    'ar' => 'وكالة شرعية (ناجز) أو تفويض رسمي',
                ],
                'color' => '#8B5CF6',
            ],
            [
                'name' => [
                    'en' => 'Legal Services Contract',
                    'ar' => 'عقد الخدمات القانونية',
                ],
                'description' => [
                    'en' => 'Contract signed between the office and the client',
                    'ar' => 'عقد موقع بين المكتب والعميل',
                ],
                'color' => '#059669',
            ],
            [
                'name' => [
                    'en' => 'National Address',
                    'ar' => 'العنوان الوطني',
                ],
                'description' => [
                    'en' => 'National address for the client or establishment',
                    'ar' => 'عنوان وطني للعميل أو المنشأة',
                ],
                'color' => '#DC2626',
            ],
            [
                'name' => [
                    'en' => 'Contact Information',
                    'ar' => 'بيانات التواصل',
                ],
                'description' => [
                    'en' => 'Mobile number and email address',
                    'ar' => 'رقم الجوال والبريد الإلكتروني',
                ],
                'color' => '#6B7280',
            ],
            [
                'name' => [
                    'en' => 'Partners / Board of Directors Resolution',
                    'ar' => 'قرار الشركاء / مجلس الإدارة',
                ],
                'description' => [
                    'en' => 'Approved resolution to contract with the office',
                    'ar' => 'قرار معتمد بالتعاقد مع المكتب',
                ],
                'color' => '#F97316',
            ],
            [
                'name' => [
                    'en' => 'Additional Documents',
                    'ar' => 'مستندات إضافية',
                ],
                'description' => [
                    'en' => 'Any supporting documents for the file',
                    'ar' => 'أي مستندات داعمة للملف',
                ],
                'color' => '#84CC16',
            ],
        ];

        foreach ($companyUsers as $companyUser) {
            foreach ($documentTypes as $type) {
                // Check if document type already exists for this user
                $existing = DocumentType::where('created_by', $companyUser->id)
                    ->whereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$type['name']['ar']])
                    ->first();

                if (! $existing) {
                    DocumentType::create([
                        'name' => $type['name'],
                        'description' => $type['description'],
                        'color' => $type['color'],
                        'status' => 'active',
                        'created_by' => $companyUser->id,
                    ]);
                }
            }
        }
    }
}
