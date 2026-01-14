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
            ['name' => '{"en":"Saudi Arabia","ar":"المملكة العربية السعودية"}', 'nationality_name' => '{"en":"Saudi","ar":"سعودي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United Arab Emirates","ar":"الإمارات العربية المتحدة"}', 'nationality_name' => '{"en":"Emirati","ar":"إماراتي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Kuwait","ar":"الكويت"}', 'nationality_name' => '{"en":"Kuwaiti","ar":"كويتي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Qatar","ar":"قطر"}', 'nationality_name' => '{"en":"Qatari","ar":"قطري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bahrain","ar":"البحرين"}', 'nationality_name' => '{"en":"Bahraini","ar":"بحريني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Oman","ar":"سلطنة عُمان"}', 'nationality_name' => '{"en":"Omani","ar":"عُماني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Egypt","ar":"مصر"}', 'nationality_name' => '{"en":"Egyptian","ar":"مصري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Jordan","ar":"الأردن"}', 'nationality_name' => '{"en":"Jordanian","ar":"أردني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Lebanon","ar":"لبنان"}', 'nationality_name' => '{"en":"Lebanese","ar":"لبناني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Syria","ar":"سوريا"}', 'nationality_name' => '{"en":"Syrian","ar":"سوري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Iraq","ar":"العراق"}', 'nationality_name' => '{"en":"Iraqi","ar":"عراقي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Palestine","ar":"فلسطين"}', 'nationality_name' => '{"en":"Palestinian","ar":"فلسطيني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Yemen","ar":"اليمن"}', 'nationality_name' => '{"en":"Yemeni","ar":"يمني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Morocco","ar":"المغرب"}', 'nationality_name' => '{"en":"Moroccan","ar":"مغربي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Algeria","ar":"الجزائر"}', 'nationality_name' => '{"en":"Algerian","ar":"جزائري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Tunisia","ar":"تونس"}', 'nationality_name' => '{"en":"Tunisian","ar":"تونسي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Libya","ar":"ليبيا"}', 'nationality_name' => '{"en":"Libyan","ar":"ليبي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sudan","ar":"السودان"}', 'nationality_name' => '{"en":"Sudanese","ar":"سوداني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mauritania","ar":"موريتانيا"}', 'nationality_name' => '{"en":"Mauritanian","ar":"موريتاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Somalia","ar":"الصومال"}', 'nationality_name' => '{"en":"Somali","ar":"صومالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Djibouti","ar":"جيبوتي"}', 'nationality_name' => '{"en":"Djiboutian","ar":"جيبوتي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Comoros","ar":"جزر القمر"}', 'nationality_name' => '{"en":"Comorian","ar":"قمري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"India","ar":"الهند"}', 'nationality_name' => '{"en":"Indian","ar":"هندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Pakistan","ar":"باكستان"}', 'nationality_name' => '{"en":"Pakistani","ar":"باكستاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bangladesh","ar":"بنغلاديش"}', 'nationality_name' => '{"en":"Bangladeshi","ar":"بنغلاديشي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sri Lanka","ar":"سريلانكا"}', 'nationality_name' => '{"en":"Sri Lankan","ar":"سريلانكي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Nepal","ar":"نيبال"}', 'nationality_name' => '{"en":"Nepali","ar":"نيبالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Afghanistan","ar":"أفغانستان"}', 'nationality_name' => '{"en":"Afghan","ar":"أفغاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Maldives","ar":"جزر المالديف"}', 'nationality_name' => '{"en":"Maldivian","ar":"مالديفي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Philippines","ar":"الفلبين"}', 'nationality_name' => '{"en":"Filipino","ar":"فلبيني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Indonesia","ar":"إندونيسيا"}', 'nationality_name' => '{"en":"Indonesian","ar":"إندونيسي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Malaysia","ar":"ماليزيا"}', 'nationality_name' => '{"en":"Malaysian","ar":"ماليزي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Thailand","ar":"تايلاند"}', 'nationality_name' => '{"en":"Thai","ar":"تايلاندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Vietnam","ar":"فيتنام"}', 'nationality_name' => '{"en":"Vietnamese","ar":"فيتنامي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Myanmar","ar":"ميانمار"}', 'nationality_name' => '{"en":"Burmese","ar":"بورمي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Cambodia","ar":"كمبوديا"}', 'nationality_name' => '{"en":"Cambodian","ar":"كمبودي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Laos","ar":"لاوس"}', 'nationality_name' => '{"en":"Laotian","ar":"لاوسي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Singapore","ar":"سنغافورة"}', 'nationality_name' => '{"en":"Singaporean","ar":"سنغافوري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Brunei","ar":"بروناي"}', 'nationality_name' => '{"en":"Bruneian","ar":"بروناوي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"China","ar":"الصين"}', 'nationality_name' => '{"en":"Chinese","ar":"صيني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Japan","ar":"اليابان"}', 'nationality_name' => '{"en":"Japanese","ar":"ياباني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"South Korea","ar":"كوريا الجنوبية"}', 'nationality_name' => '{"en":"Korean","ar":"كوري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Taiwan","ar":"تايوان"}', 'nationality_name' => '{"en":"Taiwanese","ar":"تايواني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mongolia","ar":"منغوليا"}', 'nationality_name' => '{"en":"Mongolian","ar":"منغولي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ethiopia","ar":"إثيوبيا"}', 'nationality_name' => '{"en":"Ethiopian","ar":"إثيوبي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Kenya","ar":"كينيا"}', 'nationality_name' => '{"en":"Kenyan","ar":"كيني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Nigeria","ar":"نيجيريا"}', 'nationality_name' => '{"en":"Nigerian","ar":"نيجيري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ghana","ar":"غانا"}', 'nationality_name' => '{"en":"Ghanaian","ar":"غاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Uganda","ar":"أوغندا"}', 'nationality_name' => '{"en":"Ugandan","ar":"أوغندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Tanzania","ar":"تنزانيا"}', 'nationality_name' => '{"en":"Tanzanian","ar":"تنزاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Rwanda","ar":"رواندا"}', 'nationality_name' => '{"en":"Rwandan","ar":"رواندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"South Africa","ar":"جنوب أفريقيا"}', 'nationality_name' => '{"en":"South African","ar":"جنوب أفريقي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Zimbabwe","ar":"زيمبابوي"}', 'nationality_name' => '{"en":"Zimbabwean","ar":"زيمبابوي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Senegal","ar":"السنغال"}', 'nationality_name' => '{"en":"Senegalese","ar":"سنغالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mali","ar":"مالي"}', 'nationality_name' => '{"en":"Malian","ar":"مالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Niger","ar":"النيجر"}', 'nationality_name' => '{"en":"Nigerien","ar":"نيجري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Chad","ar":"تشاد"}', 'nationality_name' => '{"en":"Chadian","ar":"تشادي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United Kingdom","ar":"المملكة المتحدة"}', 'nationality_name' => '{"en":"British","ar":"بريطاني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"France","ar":"فرنسا"}', 'nationality_name' => '{"en":"French","ar":"فرنسي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Germany","ar":"ألمانيا"}', 'nationality_name' => '{"en":"German","ar":"ألماني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Italy","ar":"إيطاليا"}', 'nationality_name' => '{"en":"Italian","ar":"إيطالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Spain","ar":"إسبانيا"}', 'nationality_name' => '{"en":"Spanish","ar":"إسباني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Netherlands","ar":"هولندا"}', 'nationality_name' => '{"en":"Dutch","ar":"هولندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Belgium","ar":"بلجيكا"}', 'nationality_name' => '{"en":"Belgian","ar":"بلجيكي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Switzerland","ar":"سويسرا"}', 'nationality_name' => '{"en":"Swiss","ar":"سويسري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sweden","ar":"السويد"}', 'nationality_name' => '{"en":"Swedish","ar":"سويدي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Norway","ar":"النرويج"}', 'nationality_name' => '{"en":"Norwegian","ar":"نرويجي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Denmark","ar":"الدنمارك"}', 'nationality_name' => '{"en":"Danish","ar":"دنماركي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ireland","ar":"أيرلندا"}', 'nationality_name' => '{"en":"Irish","ar":"أيرلندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Poland","ar":"بولندا"}', 'nationality_name' => '{"en":"Polish","ar":"بولندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Romania","ar":"رومانيا"}', 'nationality_name' => '{"en":"Romanian","ar":"روماني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bulgaria","ar":"بلغاريا"}', 'nationality_name' => '{"en":"Bulgarian","ar":"بلغاري"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Russia","ar":"روسيا"}', 'nationality_name' => '{"en":"Russian","ar":"روسي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ukraine","ar":"أوكرانيا"}', 'nationality_name' => '{"en":"Ukrainian","ar":"أوكراني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United States","ar":"الولايات المتحدة"}', 'nationality_name' => '{"en":"American","ar":"أمريكي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Canada","ar":"كندا"}', 'nationality_name' => '{"en":"Canadian","ar":"كندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mexico","ar":"المكسيك"}', 'nationality_name' => '{"en":"Mexican","ar":"مكسيكي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Brazil","ar":"البرازيل"}', 'nationality_name' => '{"en":"Brazilian","ar":"برازيلي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Argentina","ar":"الأرجنتين"}', 'nationality_name' => '{"en":"Argentine","ar":"أرجنتيني"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Colombia","ar":"كولومبيا"}', 'nationality_name' => '{"en":"Colombian","ar":"كولومبي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Australia","ar":"أستراليا"}', 'nationality_name' => '{"en":"Australian","ar":"أسترالي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"New Zealand","ar":"نيوزيلندا"}', 'nationality_name' => '{"en":"New Zealander","ar":"نيوزيلندي"}', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        Country::insert($countries);
    }
}
