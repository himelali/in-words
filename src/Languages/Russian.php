<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\ConversionException;
use Himelali\Support\Segment;

/**
 * Russian (Русский). Feminine "тысяча" and the three plural forms.
 */
class Russian extends Language
{
    protected $code = 'ru';
    protected $name = 'Russian';
    protected $words = [
        'ноль', 'один', 'два', 'три', 'четыре',
        'пять', 'шесть', 'семь', 'восемь', 'девять',
        'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать',
        'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать',
    ];
    protected $tens = [
        2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто',
    ];
    protected $hundredWords = [
        1 => 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот',
    ];
    protected $scales = [
        'thousand' => ['тысяча', 'тысячи', 'тысяч'],
        'million' => ['миллион', 'миллиона', 'миллионов'],
        'billion' => ['миллиард', 'миллиарда', 'миллиардов'],
        'trillion' => ['триллион', 'триллиона', 'триллионов'],
    ];
    protected $point = 'запятая';

    /**
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $this->hundredWords[$hundreds];
    }

    /**
     * "одна тысяча", "две тысячи", "пять тысяч".
     *
     * {@inheritdoc}
     * @throws ConversionException
     */
    protected function scale($segment): string
    {
        $words = $segment->words();
        if ($segment->scale() === 'thousand') {
            $words = preg_replace(['/один$/u', '/два$/u'], ['одна', 'две'], $words);
        }

        return $words . ' ' . $this->scaleWord($segment->scale(), $segment->value());
    }

    /**
     * 0: 1, 21, 31...; 1: 2-4, 22-24...; 2: 5-20, 25-30...
     *
     * {@inheritdoc}
     */
    protected function pluralForm($count): int
    {
        $lastTwo = $count % 100;
        $last = $count % 10;
        if ($lastTwo >= 11 && $lastTwo <= 14) {
            return 2;
        }
        if ($last === 1) {
            return 0;
        }

        return $last >= 2 && $last <= 4 ? 1 : 2;
    }
}
