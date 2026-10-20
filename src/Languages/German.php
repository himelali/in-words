<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Support\Segment;

/**
 * German (Deutsch). Numbers below a million are one word; 10^9 is "Milliarde", 10^12 is "Billion".
 */
class German extends Language
{
    protected $code = 'de';
    protected $name = 'German';
    protected $words = [
        'null', 'eins', 'zwei', 'drei', 'vier', 'fünf', 'sechs', 'sieben', 'acht', 'neun',
        'zehn', 'elf', 'zwölf', 'dreizehn', 'vierzehn', 'fünfzehn', 'sechzehn', 'siebzehn', 'achtzehn', 'neunzehn',
    ];
    protected $tens = [2 => 'zwanzig', 'dreißig', 'vierzig', 'fünfzig', 'sechzig', 'siebzig', 'achtzig', 'neunzig'];
    protected $scales = [
        'thousand' => 'tausend',
        'million' => ['Million', 'Millionen'],
        'billion' => ['Milliarde', 'Milliarden'],
        'trillion' => ['Billion', 'Billionen'],
    ];
    protected $point = 'Komma';

    /**
     * "einhundert", "zweihundertfünfundvierzig".
     *
     * {@inheritdoc}
     */
    public function group($number): string
    {
        $hundreds = intdiv($number, 100);
        $rest = $number % 100;

        $words = $hundreds > 0 ? $this->unit($hundreds) . 'hundert' : '';
        return $rest > 0 ? $words . $this->belowHundred($rest) : $words;
    }

    /**
     * Millions and above are separate words, everything below is one word.
     *
     * {@inheritdoc}
     */
    public function combine($segments): string
    {
        $words = [];
        $tail = '';
        foreach ($segments as $segment) {
            if ($segment->isZero()) {
                continue;
            }
            if ($segment->scale() === null || $segment->scale() === 'thousand') {
                $tail .= $segment->scale() === null ? $segment->words() : $this->scale($segment);
            } else {
                $words[] = $this->scale($segment);
            }
        }
        if ($tail !== '') {
            $words[] = $tail;
        }

        return implode(' ', $words);
    }

    /**
     * "einundzwanzig".
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

        return $unit === 0 ? $tens : $this->unit($unit) . 'und' . $tens;
    }

    /**
     * "eintausend", "eine Million", "zwei Millionen".
     *
     * {@inheritdoc}
     */
    protected function scale($segment): string
    {
        if ($segment->scale() === 'thousand') {
            return preg_replace('/eins$/u', 'ein', $segment->words()) . 'tausend';
        }
        $words = preg_replace('/eins$/u', 'eine', $segment->words());
        return $words . ' ' . $this->scaleWord($segment->scale(), $segment->value());
    }

    /**
     * Unit used inside a compound word ("ein" instead of "eins").
     *
     * @param int $unit
     */
    private function unit($unit): string
    {
        return $unit === 1 ? 'ein' : $this->words[$unit];
    }
}
