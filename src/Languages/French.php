<?php

declare(strict_types=1);

namespace Himelali\Languages;

/**
 * French (Français). 10^9 is "milliard", 10^12 is "billion".
 */
class French extends Language
{
    protected $code = 'fr';
    protected $name = 'French';
    protected $words = [
        'zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize',
    ];
    protected $tens = [2 => 'vingt', 'trente', 'quarante', 'cinquante', 'soixante'];
    protected $scales = [
        'thousand' => 'mille',
        'million' => ['million', 'millions'],
        'billion' => ['milliard', 'milliards'],
        'trillion' => ['billion', 'billions'],
    ];
    protected $point = 'virgule';

    /**
     * {@inheritdoc}
     */
    protected function belowHundred($number): string
    {
        if ($number < 17) {
            return $this->words[$number];
        }
        if ($number < 20) {
            return 'dix-' . $this->words[$number - 10];
        }

        $tens = intdiv($number, 10);
        $unit = $number % 10;
        if ($tens === 7 || $tens === 9) {
            if ($number === 71) {
                return 'soixante et onze';
            }
            return ($tens === 7 ? 'soixante' : 'quatre-vingt') . '-' . $this->belowHundred(10 + $unit);
        }
        if ($tens === 8) {
            return $unit === 0 ? 'quatre-vingts' : 'quatre-vingt-' . $this->words[$unit];
        }
        if ($unit === 0) {
            return $this->tens[$tens];
        }

        return $this->tens[$tens] . ($unit === 1 ? ' et un' : '-' . $this->words[$unit]);
    }

    /**
     * "cent", "deux cents", "deux cent un".
     *
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        if ($hundreds === 1) {
            return 'cent';
        }

        return $this->words[$hundreds] . ' cent' . ($rest === 0 ? 's' : '');
    }

    /**
     * "mille", "deux mille", "quatre-vingt mille", "deux millions".
     *
     * {@inheritdoc}
     */
    protected function scale($segment): string
    {
        if ($segment->scale() !== 'thousand') {
            return parent::scale($segment);
        }
        if ($segment->value() === 1) {
            return 'mille';
        }

        return preg_replace('/(cent|vingt)s$/u', '$1', $segment->words()) . ' mille';
    }
}
