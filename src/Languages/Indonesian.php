<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Support\Segment;

/**
 * Indonesian (Bahasa Indonesia). "se-" replaces "satu" for ten, hundred and thousand.
 */
class Indonesian extends Language
{
    protected $code = 'id';
    protected $name = 'Indonesian';
    protected $words = [
        'nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
    ];
    protected $scales = [
        'thousand' => 'ribu',
        'million' => 'juta',
        'billion' => 'miliar',
        'trillion' => 'triliun',
    ];
    protected $point = 'koma';

    /**
     * "dua belas", "dua puluh satu".
     *
     * {@inheritdoc}
     */
    protected function belowHundred($number): string
    {
        if ($number < 12) {
            return $this->words[$number];
        }
        if ($number < 20) {
            return $this->words[$number - 10] . ' belas';
        }
        $unit = $number % 10;
        $tens = $this->words[intdiv($number, 10)] . ' puluh';

        return $unit === 0 ? $tens : $tens . ' ' . $this->words[$unit];
    }

    /**
     * "seratus", "dua ratus".
     *
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $hundreds === 1 ? 'seratus' : $this->words[$hundreds] . ' ratus';
    }

    /**
     * "seribu", "dua ribu", "satu juta".
     *
     * {@inheritdoc}
     */
    protected function scale($segment): string
    {
        if ($segment->scale() === 'thousand' && $segment->value() === 1) {
            return 'seribu';
        }

        return parent::scale($segment);
    }
}
