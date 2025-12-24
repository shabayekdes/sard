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
            ['name' => json_encode(['en' => 'United States', 'ar' => 'الولايات المتحدة']), 'nationality_name' => json_encode(['en' => 'American', 'ar' => 'أمريكي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'United Kingdom', 'ar' => 'المملكة المتحدة']), 'nationality_name' => json_encode(['en' => 'British', 'ar' => 'بريطاني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Canada', 'ar' => 'كندا']), 'nationality_name' => json_encode(['en' => 'Canadian', 'ar' => 'كندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Australia', 'ar' => 'أستراليا']), 'nationality_name' => json_encode(['en' => 'Australian', 'ar' => 'أسترالي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Germany', 'ar' => 'ألمانيا']), 'nationality_name' => json_encode(['en' => 'German', 'ar' => 'ألماني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'France', 'ar' => 'فرنسا']), 'nationality_name' => json_encode(['en' => 'French', 'ar' => 'فرنسي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Italy', 'ar' => 'إيطاليا']), 'nationality_name' => json_encode(['en' => 'Italian', 'ar' => 'إيطالي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Spain', 'ar' => 'إسبانيا']), 'nationality_name' => json_encode(['en' => 'Spanish', 'ar' => 'إسباني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Netherlands', 'ar' => 'هولندا']), 'nationality_name' => json_encode(['en' => 'Dutch', 'ar' => 'هولندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Belgium', 'ar' => 'بلجيكا']), 'nationality_name' => json_encode(['en' => 'Belgian', 'ar' => 'بلجيكي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Switzerland', 'ar' => 'سويسرا']), 'nationality_name' => json_encode(['en' => 'Swiss', 'ar' => 'سويسري']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Austria', 'ar' => 'النمسا']), 'nationality_name' => json_encode(['en' => 'Austrian', 'ar' => 'نمساوي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Sweden', 'ar' => 'السويد']), 'nationality_name' => json_encode(['en' => 'Swedish', 'ar' => 'سويدي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Norway', 'ar' => 'النرويج']), 'nationality_name' => json_encode(['en' => 'Norwegian', 'ar' => 'نرويجي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Denmark', 'ar' => 'الدنمارك']), 'nationality_name' => json_encode(['en' => 'Danish', 'ar' => 'دنماركي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Finland', 'ar' => 'فنلندا']), 'nationality_name' => json_encode(['en' => 'Finnish', 'ar' => 'فنلندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Poland', 'ar' => 'بولندا']), 'nationality_name' => json_encode(['en' => 'Polish', 'ar' => 'بولندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Portugal', 'ar' => 'البرتغال']), 'nationality_name' => json_encode(['en' => 'Portuguese', 'ar' => 'برتغالي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Greece', 'ar' => 'اليونان']), 'nationality_name' => json_encode(['en' => 'Greek', 'ar' => 'يوناني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Ireland', 'ar' => 'أيرلندا']), 'nationality_name' => json_encode(['en' => 'Irish', 'ar' => 'أيرلندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Japan', 'ar' => 'اليابان']), 'nationality_name' => json_encode(['en' => 'Japanese', 'ar' => 'ياباني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'South Korea', 'ar' => 'كوريا الجنوبية']), 'nationality_name' => json_encode(['en' => 'South Korean', 'ar' => 'كوري جنوبي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'China', 'ar' => 'الصين']), 'nationality_name' => json_encode(['en' => 'Chinese', 'ar' => 'صيني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'India', 'ar' => 'الهند']), 'nationality_name' => json_encode(['en' => 'Indian', 'ar' => 'هندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Singapore', 'ar' => 'سنغافورة']), 'nationality_name' => json_encode(['en' => 'Singaporean', 'ar' => 'سنغافوري']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Malaysia', 'ar' => 'ماليزيا']), 'nationality_name' => json_encode(['en' => 'Malaysian', 'ar' => 'ماليزي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Thailand', 'ar' => 'تايلاند']), 'nationality_name' => json_encode(['en' => 'Thai', 'ar' => 'تايلندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Indonesia', 'ar' => 'إندونيسيا']), 'nationality_name' => json_encode(['en' => 'Indonesian', 'ar' => 'إندونيسي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Philippines', 'ar' => 'الفلبين']), 'nationality_name' => json_encode(['en' => 'Filipino', 'ar' => 'فلبيني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Vietnam', 'ar' => 'فيتنام']), 'nationality_name' => json_encode(['en' => 'Vietnamese', 'ar' => 'فيتنامي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية']), 'nationality_name' => json_encode(['en' => 'Saudi', 'ar' => 'سعودي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة']), 'nationality_name' => json_encode(['en' => 'Emirati', 'ar' => 'إماراتي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Qatar', 'ar' => 'قطر']), 'nationality_name' => json_encode(['en' => 'Qatari', 'ar' => 'قطري']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Kuwait', 'ar' => 'الكويت']), 'nationality_name' => json_encode(['en' => 'Kuwaiti', 'ar' => 'كويتي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Bahrain', 'ar' => 'البحرين']), 'nationality_name' => json_encode(['en' => 'Bahraini', 'ar' => 'بحريني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Oman', 'ar' => 'عمان']), 'nationality_name' => json_encode(['en' => 'Omani', 'ar' => 'عماني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Jordan', 'ar' => 'الأردن']), 'nationality_name' => json_encode(['en' => 'Jordanian', 'ar' => 'أردني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Lebanon', 'ar' => 'لبنان']), 'nationality_name' => json_encode(['en' => 'Lebanese', 'ar' => 'لبناني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Egypt', 'ar' => 'مصر']), 'nationality_name' => json_encode(['en' => 'Egyptian', 'ar' => 'مصري']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'South Africa', 'ar' => 'جنوب أفريقيا']), 'nationality_name' => json_encode(['en' => 'South African', 'ar' => 'جنوب أفريقي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Brazil', 'ar' => 'البرازيل']), 'nationality_name' => json_encode(['en' => 'Brazilian', 'ar' => 'برازيلي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Mexico', 'ar' => 'المكسيك']), 'nationality_name' => json_encode(['en' => 'Mexican', 'ar' => 'مكسيكي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Argentina', 'ar' => 'الأرجنتين']), 'nationality_name' => json_encode(['en' => 'Argentine', 'ar' => 'أرجنتيني']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Chile', 'ar' => 'تشيلي']), 'nationality_name' => json_encode(['en' => 'Chilean', 'ar' => 'تشيلي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Colombia', 'ar' => 'كولومبيا']), 'nationality_name' => json_encode(['en' => 'Colombian', 'ar' => 'كولومبي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'Peru', 'ar' => 'بيرو']), 'nationality_name' => json_encode(['en' => 'Peruvian', 'ar' => 'بيروفي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => json_encode(['en' => 'New Zealand', 'ar' => 'نيوزيلندا']), 'nationality_name' => json_encode(['en' => 'New Zealander', 'ar' => 'نيوزيلندي']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        Country::insert($countries);
    }
}
