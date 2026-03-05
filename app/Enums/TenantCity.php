<?php

namespace App\Enums;

enum TenantCity: string
{
    case AL_KHOBAR = 'AL_KHOBAR';
    case DAMMAM = 'DAMMAM';
    case RIYADH = 'RIYADH';
    case JEDDAH = 'JEDDAH';
    case SHAQRA = 'SHAQRA';
    case MADINAH = 'MADINAH';
    case ABHA = 'ABHA';
    case MAKKAH = 'MAKKAH';
    case AL_AHSA = 'AL_AHSA';
    case QATIF = 'QATIF';
    case BURAIDAH = 'BURAIDAH';
    case JUBAIL = 'JUBAIL';
    case ARAR = 'ARAR';
    case UMLAJJ = 'UMLAJJ';
    case HAIL = 'HAIL';
    case TAIFF = 'TAIFF';
    case SAYHAT = 'SAYHAT';
    case AL_MAJMAHA = 'AL_MAJMAHA';
    case SAKAKA = 'SAKAKA';
    case HAFAR_AL_BATIN = 'HAFAR_AL_BATIN';
    case AL_HOFUF = 'AL_HOFUF';
    case JAZAN = 'JAZAN';
    case AL_KHARJ = 'AL_KHARJ';
    case AD_DUWADIMI = 'AD_DUWADIMI';
    case NAJRAN = 'NAJRAN';
    case DUMAT_AL_JANDAL = 'DUMAT_AL_JANDAL';
    case KHAMIS_MUSHAIT = 'KHAMIS_MUSHAIT';
    case TABUK = 'TABUK';
    case SAMTAH = 'SAMTAH';
    case QURAYYAT = 'QURAYYAT';
    case UNAYZAH = 'UNAYZAH';
    case YANBU = 'YANBU';
    case KHULAIS = 'KHULAIS';
    case AL_BUKAYRIYAH = 'AL_BUKAYRIYAH';
    case BISHAH = 'BISHAH';
    case FARASAN = 'FARASAN';
    case AFIF = 'AFIF';
    case KHAFJI = 'KHAFJI';
    case MUHAYIL = 'MUHAYIL';
    case AL_QASSIM = 'AL_QASSIM';
    case DHAHRAN = 'DHAHRAN';
    case SABYA = 'SABYA';
    case AR_RASS = 'AR_RASS';
    case BUQAYQ = 'BUQAYQ';
    case MUHAYIL_ASIR = 'MUHAYIL_ASIR';
    case TABARJAL = 'TABARJAL';
    case AL_JAWF_REGION = 'AL_JAWF_REGION';
    case AL_QASSIM_REGION = 'AL_QASSIM_REGION';
    case AL_BAHA = 'AL_BAHA';
    case TURAIF = 'TURAIF';
    case AL_GHAT = 'AL_GHAT';
    case WADI_AD_DAWASIR = 'WADI_AD_DAWASIR';
    case HOTAT_BANI_TAMIM = 'HOTAT_BANI_TAMIM';
    case AL_JAWF = 'AL_JAWF';
    case AL_KHURMAH = 'AL_KHURMAH';
    case AL_BADAYA = 'AL_BADAYA';
    case RAFHA = 'RAFHA';
    case SHARURAH = 'SHARURAH';
    case AL_NAIRIYAH = 'AL_NAIRIYAH';
    case AL_AFLAJ = 'AL_AFLAJ';
    case BISH = 'BISH';
    case AL_QUNFUDHAH = 'AL_QUNFUDHAH';
    case AD_DAIR = 'AD_DAIR';
    case AL_NAMAS = 'AL_NAMAS';
    case ASH_SHIMLI = 'ASH_SHIMLI';
    case ABU_ARISH = 'ABU_ARISH';
    case RAS_TANURA = 'RAS_TANURA';
    case AL_IDABI = 'AL_IDABI';
    case DUBA = 'DUBA';
    case QALWA = 'QALWA';
    case KHAYBAR = 'KHAYBAR';
    case SAFWA = 'SAFWA';
    case AL_MIKHWAH = 'AL_MIKHWAH';
    case AZ_ZULFI = 'AZ_ZULFI';
    case YADAMAH = 'YADAMAH';
    case TAYMA = 'TAYMA';
    case DHAHRAN_AL_JANOUB = 'DHAHRAN_AL_JANOUB';
    case RANYAH = 'RANYAH';
    case TURBAH = 'TURBAH';
    case AL_BAHRAH = 'AL_BAHRAH';
    case AL_ARTAWIYAH = 'AL_ARTAWIYAH';
    case AL_HAIT = 'AL_HAIT';
    case AL_QUWAYIYAH = 'AL_QUWAYIYAH';
    case AL_MUZAHIMIYAH = 'AL_MUZAHIMIYAH';
    case AD_DILAM = 'AD_DILAM';

    /**
     * Translation key for the city label (for use with __() or frontend t()).
     */
    public function labelKey(): string
    {
        return 'TenantCity.' . $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<array{value: string, labelKey: string}>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[] = [
                'value' => $case->value,
                'labelKey' => $case->labelKey(),
            ];
        }
        return $options;
    }
}
