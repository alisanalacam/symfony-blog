<?php
namespace AppBundle\Utils;
/**
 * Bu Sınıf PHP tarih formatının to moment.js formatına çevirir
 * Class MomentFormatConverter
 * @package AppBundle\Utils
 */
class MomentFormatConverter
{
    /**
     * @var array
     */
    private static $formatConvertRules = array(
        // yıl
        'yyyy' => 'YYYY', 'yy' => 'YY', 'y' => 'YYYY',
        // gün
        'dd' => 'DD', 'd' => 'D',
        // haftanın günü
        'EE' => 'ddd', 'EEEEEE' => 'dd',
        // timezone
        'ZZZZZ' => 'Z', 'ZZZ' => 'ZZ'
    );
    /**
     * ilişkili moment.js formatı döndürür.
     *
     * @param string $format PHP tarih formatı
     *
     * @return string
     */
    public function convert($format)
    {
        return strtr($format, self::$formatConvertRules);
    }
}