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
            ['name' => '{"en":"Saudi Arabia","ar":"المملكة العربية السعودية"}', 'nationality_name' => '{"en":"Saudi","ar":"سعودي"}', 'country_code' => 'SA', 'phone_code' => '966', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United Arab Emirates","ar":"الإمارات العربية المتحدة"}', 'nationality_name' => '{"en":"Emirati","ar":"إماراتي"}', 'country_code' => 'AE', 'phone_code' => '971', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Kuwait","ar":"الكويت"}', 'nationality_name' => '{"en":"Kuwaiti","ar":"كويتي"}', 'country_code' => 'KW', 'phone_code' => '965', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Qatar","ar":"قطر"}', 'nationality_name' => '{"en":"Qatari","ar":"قطري"}', 'country_code' => 'QA', 'phone_code' => '974', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bahrain","ar":"البحرين"}', 'nationality_name' => '{"en":"Bahraini","ar":"بحريني"}', 'country_code' => 'BH', 'phone_code' => '973', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Oman","ar":"سلطنة عُمان"}', 'nationality_name' => '{"en":"Omani","ar":"عُماني"}', 'country_code' => 'OM', 'phone_code' => '968', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Egypt","ar":"مصر"}', 'nationality_name' => '{"en":"Egyptian","ar":"مصري"}', 'country_code' => 'EG', 'phone_code' => '20', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Jordan","ar":"الأردن"}', 'nationality_name' => '{"en":"Jordanian","ar":"أردني"}', 'country_code' => 'JO', 'phone_code' => '962', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Lebanon","ar":"لبنان"}', 'nationality_name' => '{"en":"Lebanese","ar":"لبناني"}', 'country_code' => 'LB', 'phone_code' => '961', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Syria","ar":"سوريا"}', 'nationality_name' => '{"en":"Syrian","ar":"سوري"}', 'country_code' => 'SY', 'phone_code' => '963', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Iraq","ar":"العراق"}', 'nationality_name' => '{"en":"Iraqi","ar":"عراقي"}', 'country_code' => 'IQ', 'phone_code' => '964', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Palestine","ar":"فلسطين"}', 'nationality_name' => '{"en":"Palestinian","ar":"فلسطيني"}', 'country_code' => 'PS', 'phone_code' => '970', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Yemen","ar":"اليمن"}', 'nationality_name' => '{"en":"Yemeni","ar":"يمني"}', 'country_code' => 'YE', 'phone_code' => '967', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Morocco","ar":"المغرب"}', 'nationality_name' => '{"en":"Moroccan","ar":"مغربي"}', 'country_code' => 'MA', 'phone_code' => '212', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Algeria","ar":"الجزائر"}', 'nationality_name' => '{"en":"Algerian","ar":"جزائري"}', 'country_code' => 'DZ', 'phone_code' => '213', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Tunisia","ar":"تونس"}', 'nationality_name' => '{"en":"Tunisian","ar":"تونسي"}', 'country_code' => 'TN', 'phone_code' => '216', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Libya","ar":"ليبيا"}', 'nationality_name' => '{"en":"Libyan","ar":"ليبي"}', 'country_code' => 'LY', 'phone_code' => '218', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sudan","ar":"السودان"}', 'nationality_name' => '{"en":"Sudanese","ar":"سوداني"}', 'country_code' => 'SD', 'phone_code' => '249', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mauritania","ar":"موريتانيا"}', 'nationality_name' => '{"en":"Mauritanian","ar":"موريتاني"}', 'country_code' => 'MR', 'phone_code' => '222', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Somalia","ar":"الصومال"}', 'nationality_name' => '{"en":"Somali","ar":"صومالي"}', 'country_code' => 'SO', 'phone_code' => '252', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Djibouti","ar":"جيبوتي"}', 'nationality_name' => '{"en":"Djiboutian","ar":"جيبوتي"}', 'country_code' => 'DJ', 'phone_code' => '253', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Comoros","ar":"جزر القمر"}', 'nationality_name' => '{"en":"Comorian","ar":"قمري"}', 'country_code' => 'KM', 'phone_code' => '269', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"India","ar":"الهند"}', 'nationality_name' => '{"en":"Indian","ar":"هندي"}', 'country_code' => 'IN', 'phone_code' => '91', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Pakistan","ar":"باكستان"}', 'nationality_name' => '{"en":"Pakistani","ar":"باكستاني"}', 'country_code' => 'PK', 'phone_code' => '92', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bangladesh","ar":"بنغلاديش"}', 'nationality_name' => '{"en":"Bangladeshi","ar":"بنغلاديشي"}', 'country_code' => 'BD', 'phone_code' => '880', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sri Lanka","ar":"سريلانكا"}', 'nationality_name' => '{"en":"Sri Lankan","ar":"سريلانكي"}', 'country_code' => 'LK', 'phone_code' => '94', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Nepal","ar":"نيبال"}', 'nationality_name' => '{"en":"Nepali","ar":"نيبالي"}', 'country_code' => 'NP', 'phone_code' => '977', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Afghanistan","ar":"أفغانستان"}', 'nationality_name' => '{"en":"Afghan","ar":"أفغاني"}', 'country_code' => 'AF', 'phone_code' => '93', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Maldives","ar":"جزر المالديف"}', 'nationality_name' => '{"en":"Maldivian","ar":"مالديفي"}', 'country_code' => 'MV', 'phone_code' => '960', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Philippines","ar":"الفلبين"}', 'nationality_name' => '{"en":"Filipino","ar":"فلبيني"}', 'country_code' => 'PH', 'phone_code' => '63', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Indonesia","ar":"إندونيسيا"}', 'nationality_name' => '{"en":"Indonesian","ar":"إندونيسي"}', 'country_code' => 'ID', 'phone_code' => '62', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Malaysia","ar":"ماليزيا"}', 'nationality_name' => '{"en":"Malaysian","ar":"ماليزي"}', 'country_code' => 'MY', 'phone_code' => '60', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Thailand","ar":"تايلاند"}', 'nationality_name' => '{"en":"Thai","ar":"تايلاندي"}', 'country_code' => 'TH', 'phone_code' => '66', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Vietnam","ar":"فيتنام"}', 'nationality_name' => '{"en":"Vietnamese","ar":"فيتنامي"}', 'country_code' => 'VN', 'phone_code' => '84', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Myanmar","ar":"ميانمار"}', 'nationality_name' => '{"en":"Burmese","ar":"بورمي"}', 'country_code' => 'MM', 'phone_code' => '95', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Cambodia","ar":"كمبوديا"}', 'nationality_name' => '{"en":"Cambodian","ar":"كمبودي"}', 'country_code' => 'KH', 'phone_code' => '855', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Laos","ar":"لاوس"}', 'nationality_name' => '{"en":"Laotian","ar":"لاوسي"}', 'country_code' => 'LA', 'phone_code' => '856', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Singapore","ar":"سنغافورة"}', 'nationality_name' => '{"en":"Singaporean","ar":"سنغافوري"}', 'country_code' => 'SG', 'phone_code' => '65', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Brunei","ar":"بروناي"}', 'nationality_name' => '{"en":"Bruneian","ar":"بروناوي"}', 'country_code' => 'BN', 'phone_code' => '673', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"China","ar":"الصين"}', 'nationality_name' => '{"en":"Chinese","ar":"صيني"}', 'country_code' => 'CN', 'phone_code' => '86', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Japan","ar":"اليابان"}', 'nationality_name' => '{"en":"Japanese","ar":"ياباني"}', 'country_code' => 'JP', 'phone_code' => '81', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"South Korea","ar":"كوريا الجنوبية"}', 'nationality_name' => '{"en":"Korean","ar":"كوري"}', 'country_code' => 'KR', 'phone_code' => '82', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Taiwan","ar":"تايوان"}', 'nationality_name' => '{"en":"Taiwanese","ar":"تايواني"}', 'country_code' => 'TW', 'phone_code' => '886', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mongolia","ar":"منغوليا"}', 'nationality_name' => '{"en":"Mongolian","ar":"منغولي"}', 'country_code' => 'MN', 'phone_code' => '976', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ethiopia","ar":"إثيوبيا"}', 'nationality_name' => '{"en":"Ethiopian","ar":"إثيوبي"}', 'country_code' => 'ET', 'phone_code' => '251', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Kenya","ar":"كينيا"}', 'nationality_name' => '{"en":"Kenyan","ar":"كيني"}', 'country_code' => 'KE', 'phone_code' => '254', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Nigeria","ar":"نيجيريا"}', 'nationality_name' => '{"en":"Nigerian","ar":"نيجيري"}', 'country_code' => 'NG', 'phone_code' => '234', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ghana","ar":"غانا"}', 'nationality_name' => '{"en":"Ghanaian","ar":"غاني"}', 'country_code' => 'GH', 'phone_code' => '233', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Uganda","ar":"أوغندا"}', 'nationality_name' => '{"en":"Ugandan","ar":"أوغندي"}', 'country_code' => 'UG', 'phone_code' => '256', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Tanzania","ar":"تنزانيا"}', 'nationality_name' => '{"en":"Tanzanian","ar":"تنزاني"}', 'country_code' => 'TZ', 'phone_code' => '255', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Rwanda","ar":"رواندا"}', 'nationality_name' => '{"en":"Rwandan","ar":"رواندي"}', 'country_code' => 'RW', 'phone_code' => '250', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"South Africa","ar":"جنوب أفريقيا"}', 'nationality_name' => '{"en":"South African","ar":"جنوب أفريقي"}', 'country_code' => 'ZA', 'phone_code' => '27', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Zimbabwe","ar":"زيمبابوي"}', 'nationality_name' => '{"en":"Zimbabwean","ar":"زيمبابوي"}', 'country_code' => 'ZW', 'phone_code' => '263', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Senegal","ar":"السنغال"}', 'nationality_name' => '{"en":"Senegalese","ar":"سنغالي"}', 'country_code' => 'SN', 'phone_code' => '221', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mali","ar":"مالي"}', 'nationality_name' => '{"en":"Malian","ar":"مالي"}', 'country_code' => 'ML', 'phone_code' => '223', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Niger","ar":"النيجر"}', 'nationality_name' => '{"en":"Nigerien","ar":"نيجري"}', 'country_code' => 'NE', 'phone_code' => '227', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Chad","ar":"تشاد"}', 'nationality_name' => '{"en":"Chadian","ar":"تشادي"}', 'country_code' => 'TD', 'phone_code' => '235', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United Kingdom","ar":"المملكة المتحدة"}', 'nationality_name' => '{"en":"British","ar":"بريطاني"}', 'country_code' => 'GB', 'phone_code' => '44', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"France","ar":"فرنسا"}', 'nationality_name' => '{"en":"French","ar":"فرنسي"}', 'country_code' => 'FR', 'phone_code' => '33', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Germany","ar":"ألمانيا"}', 'nationality_name' => '{"en":"German","ar":"ألماني"}', 'country_code' => 'DE', 'phone_code' => '49', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Italy","ar":"إيطاليا"}', 'nationality_name' => '{"en":"Italian","ar":"إيطالي"}', 'country_code' => 'IT', 'phone_code' => '39', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Spain","ar":"إسبانيا"}', 'nationality_name' => '{"en":"Spanish","ar":"إسباني"}', 'country_code' => 'ES', 'phone_code' => '34', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Netherlands","ar":"هولندا"}', 'nationality_name' => '{"en":"Dutch","ar":"هولندي"}', 'country_code' => 'NL', 'phone_code' => '31', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Belgium","ar":"بلجيكا"}', 'nationality_name' => '{"en":"Belgian","ar":"بلجيكي"}', 'country_code' => 'BE', 'phone_code' => '32', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Switzerland","ar":"سويسرا"}', 'nationality_name' => '{"en":"Swiss","ar":"سويسري"}', 'country_code' => 'CH', 'phone_code' => '41', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Sweden","ar":"السويد"}', 'nationality_name' => '{"en":"Swedish","ar":"سويدي"}', 'country_code' => 'SE', 'phone_code' => '46', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Norway","ar":"النرويج"}', 'nationality_name' => '{"en":"Norwegian","ar":"نرويجي"}', 'country_code' => 'NO', 'phone_code' => '47', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Denmark","ar":"الدنمارك"}', 'nationality_name' => '{"en":"Danish","ar":"دنماركي"}', 'country_code' => 'DK', 'phone_code' => '45', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ireland","ar":"أيرلندا"}', 'nationality_name' => '{"en":"Irish","ar":"أيرلندي"}', 'country_code' => 'IE', 'phone_code' => '353', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Poland","ar":"بولندا"}', 'nationality_name' => '{"en":"Polish","ar":"بولندي"}', 'country_code' => 'PL', 'phone_code' => '48', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Romania","ar":"رومانيا"}', 'nationality_name' => '{"en":"Romanian","ar":"روماني"}', 'country_code' => 'RO', 'phone_code' => '40', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Bulgaria","ar":"بلغاريا"}', 'nationality_name' => '{"en":"Bulgarian","ar":"بلغاري"}', 'country_code' => 'BG', 'phone_code' => '359', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Russia","ar":"روسيا"}', 'nationality_name' => '{"en":"Russian","ar":"روسي"}', 'country_code' => 'RU', 'phone_code' => '7', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Ukraine","ar":"أوكرانيا"}', 'nationality_name' => '{"en":"Ukrainian","ar":"أوكراني"}', 'country_code' => 'UA', 'phone_code' => '380', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"United States","ar":"الولايات المتحدة"}', 'nationality_name' => '{"en":"American","ar":"أمريكي"}', 'country_code' => 'US', 'phone_code' => '1', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Canada","ar":"كندا"}', 'nationality_name' => '{"en":"Canadian","ar":"كندي"}', 'country_code' => 'CA', 'phone_code' => '1-CA', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Mexico","ar":"المكسيك"}', 'nationality_name' => '{"en":"Mexican","ar":"مكسيكي"}', 'country_code' => 'MX', 'phone_code' => '52', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Brazil","ar":"البرازيل"}', 'nationality_name' => '{"en":"Brazilian","ar":"برازيلي"}', 'country_code' => 'BR', 'phone_code' => '55', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Argentina","ar":"الأرجنتين"}', 'nationality_name' => '{"en":"Argentine","ar":"أرجنتيني"}', 'country_code' => 'AR', 'phone_code' => '54', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Colombia","ar":"كولومبيا"}', 'nationality_name' => '{"en":"Colombian","ar":"كولومبي"}', 'country_code' => 'CO', 'phone_code' => '57', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"Australia","ar":"أستراليا"}', 'nationality_name' => '{"en":"Australian","ar":"أسترالي"}', 'country_code' => 'AU', 'phone_code' => '61', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '{"en":"New Zealand","ar":"نيوزيلندا"}', 'nationality_name' => '{"en":"New Zealander","ar":"نيوزيلندي"}', 'country_code' => 'NZ', 'phone_code' => '64', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        Country::insert($countries);
    }
}
