<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => ['en' => 'United States', 'ar' => 'الولايات المتحدة'], 'is_active' => true],
            ['name' => ['en' => 'United Kingdom', 'ar' => 'المملكة المتحدة'], 'is_active' => true],
            ['name' => ['en' => 'Canada', 'ar' => 'كندا'], 'is_active' => true],
            ['name' => ['en' => 'Australia', 'ar' => 'أستراليا'], 'is_active' => true],
            ['name' => ['en' => 'Germany', 'ar' => 'ألمانيا'], 'is_active' => true],
            ['name' => ['en' => 'France', 'ar' => 'فرنسا'], 'is_active' => true],
            ['name' => ['en' => 'Italy', 'ar' => 'إيطاليا'], 'is_active' => true],
            ['name' => ['en' => 'Spain', 'ar' => 'إسبانيا'], 'is_active' => true],
            ['name' => ['en' => 'Netherlands', 'ar' => 'هولندا'], 'is_active' => true],
            ['name' => ['en' => 'Belgium', 'ar' => 'بلجيكا'], 'is_active' => true],
            ['name' => ['en' => 'Switzerland', 'ar' => 'سويسرا'], 'is_active' => true],
            ['name' => ['en' => 'Austria', 'ar' => 'النمسا'], 'is_active' => true],
            ['name' => ['en' => 'Sweden', 'ar' => 'السويد'], 'is_active' => true],
            ['name' => ['en' => 'Norway', 'ar' => 'النرويج'], 'is_active' => true],
            ['name' => ['en' => 'Denmark', 'ar' => 'الدنمارك'], 'is_active' => true],
            ['name' => ['en' => 'Finland', 'ar' => 'فنلندا'], 'is_active' => true],
            ['name' => ['en' => 'Poland', 'ar' => 'بولندا'], 'is_active' => true],
            ['name' => ['en' => 'Portugal', 'ar' => 'البرتغال'], 'is_active' => true],
            ['name' => ['en' => 'Greece', 'ar' => 'اليونان'], 'is_active' => true],
            ['name' => ['en' => 'Ireland', 'ar' => 'أيرلندا'], 'is_active' => true],
            ['name' => ['en' => 'Japan', 'ar' => 'اليابان'], 'is_active' => true],
            ['name' => ['en' => 'South Korea', 'ar' => 'كوريا الجنوبية'], 'is_active' => true],
            ['name' => ['en' => 'China', 'ar' => 'الصين'], 'is_active' => true],
            ['name' => ['en' => 'India', 'ar' => 'الهند'], 'is_active' => true],
            ['name' => ['en' => 'Singapore', 'ar' => 'سنغافورة'], 'is_active' => true],
            ['name' => ['en' => 'Malaysia', 'ar' => 'ماليزيا'], 'is_active' => true],
            ['name' => ['en' => 'Thailand', 'ar' => 'تايلاند'], 'is_active' => true],
            ['name' => ['en' => 'Indonesia', 'ar' => 'إندونيسيا'], 'is_active' => true],
            ['name' => ['en' => 'Philippines', 'ar' => 'الفلبين'], 'is_active' => true],
            ['name' => ['en' => 'Vietnam', 'ar' => 'فيتنام'], 'is_active' => true],
            ['name' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'], 'is_active' => true],
            ['name' => ['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة'], 'is_active' => true],
            ['name' => ['en' => 'Qatar', 'ar' => 'قطر'], 'is_active' => true],
            ['name' => ['en' => 'Kuwait', 'ar' => 'الكويت'], 'is_active' => true],
            ['name' => ['en' => 'Bahrain', 'ar' => 'البحرين'], 'is_active' => true],
            ['name' => ['en' => 'Oman', 'ar' => 'عمان'], 'is_active' => true],
            ['name' => ['en' => 'Jordan', 'ar' => 'الأردن'], 'is_active' => true],
            ['name' => ['en' => 'Lebanon', 'ar' => 'لبنان'], 'is_active' => true],
            ['name' => ['en' => 'Egypt', 'ar' => 'مصر'], 'is_active' => true],
            ['name' => ['en' => 'South Africa', 'ar' => 'جنوب أفريقيا'], 'is_active' => true],
            ['name' => ['en' => 'Brazil', 'ar' => 'البرازيل'], 'is_active' => true],
            ['name' => ['en' => 'Mexico', 'ar' => 'المكسيك'], 'is_active' => true],
            ['name' => ['en' => 'Argentina', 'ar' => 'الأرجنتين'], 'is_active' => true],
            ['name' => ['en' => 'Chile', 'ar' => 'تشيلي'], 'is_active' => true],
            ['name' => ['en' => 'Colombia', 'ar' => 'كولومبيا'], 'is_active' => true],
            ['name' => ['en' => 'Peru', 'ar' => 'بيرو'], 'is_active' => true],
            ['name' => ['en' => 'New Zealand', 'ar' => 'نيوزيلندا'], 'is_active' => true],
        ];

        foreach ($countries as $country) {
            // For translatable fields, we need to check by the English name
            $existing = Country::whereRaw("JSON_EXTRACT(name, '$.en') = ?", [$country['name']['en']])->first();

            if (! $existing) {
                Country::create($country);
            }
        }
    }
}
