<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\ConversionException;
use Himelali\Support\Segment;

/**
 * Arabic (العربية), Modern Standard masculine forms with dual and plural scale words.
 */
class Arabic extends Language
{
    protected $code = 'ar';
    protected $name = 'Arabic';
    protected $words = [
        'صفر', 'واحد', 'اثنان', 'ثلاثة', 'أربعة',
        'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر',
        'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر',
    ];
    protected $tens = [2 => 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
    protected $hundredWords = [
        1 => 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة',
    ];
    protected $hundredSeparator = ' و';
    protected $segmentSeparator = ' و';

    /**
     * Forms: singular, dual, plural (3 - 10), accusative singular (11 - 99).
     *
     * @var array
     */
    protected $scales = [
        'thousand' => ['ألف', 'ألفان', 'آلاف', 'ألفاً'],
        'million' => ['مليون', 'مليونان', 'ملايين', 'مليوناً'],
        'billion' => ['مليار', 'ملياران', 'مليارات', 'ملياراً'],
        'trillion' => ['تريليون', 'تريليونان', 'تريليونات', 'تريليوناً'],
    ];
    protected $point = 'فاصلة';
    protected $digits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    protected $decimalSeparator = '٫';

    /**
     * "خمسة وعشرون": the unit comes first.
     *
     * {@inheritdoc}
     */
    protected function belowHundred($number): string
    {
        if ($number < 20) {
            return $this->words[$number];
        }
        $unit = $number % 10;
        $tens = $this->tens[intdiv($number, 10)];

        return $unit === 0 ? $tens : $this->words[$unit] . ' و' . $tens;
    }

    /**
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $this->hundredWords[$hundreds];
    }

    /**
     * The scale word form depends on the last two digits of the count:
     * "ألف", "ألفان", "ثلاثة آلاف", "أحد عشر ألفاً", "مائة ألف", "مائتا ألف".
     *
     * {@inheritdoc}
     * @throws ConversionException
     */
    protected function scale($segment): string
    {
        $forms = $this->scaleForms($segment->scale());
        $count = $segment->value();
        $rest = $count % 100;

        if ($rest === 0) {
            return preg_replace('/مائتان$/u', 'مائتا', $segment->words()) . ' ' . $forms[0];
        }

        if ($rest > 10) {
            return $segment->words() . ' ' . $forms[3];
        }

        $prefix = $count > 100 ? $this->group($count - $rest) . ' و' : '';
        if ($rest <= 2) {
            return $prefix . $forms[$rest - 1];
        }

        return $prefix . $this->group($rest) . ' ' . $forms[2];
    }
}
