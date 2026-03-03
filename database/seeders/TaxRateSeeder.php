<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaxRate::create([
            'name' => [
                'en' => 'Zero VAT',
                'ar' => 'ضريبة القيمة المضافة صفر',
            ],
            'description' => [
                'en' => 'Zero rated VAT.',
                'ar' => 'ضريبة قيمة مضافة بنسبة صفر.',
            ],
            'rate' => 0,
        ]);

        TaxRate::create([
                'name' => [
                    'en' => 'Saudi VAT',
                    'ar' => 'ضريبة القيمة المضافة السعودية',
                ],
                'description' => [
                    'en' => 'Standard VAT rate in Saudi Arabia.',
                    'ar' => 'نسبة ضريبة القيمة المضافة القياسية في السعودية.',
                ],
            'rate' => 15.00,
        ]);

    }
}
