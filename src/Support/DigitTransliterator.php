<?php

declare(strict_types=1);

namespace Himelali\Support;

use Himelali\Contracts\DigitsInterface;

/**
 * Writes a number with a language's native digits, e.g. "527.56" => "৫২৭.৫৬".
 */
final class DigitTransliterator
{
    /**
     * @param Number $number
     * @param DigitsInterface $language
     */
    public function transliterate($number, $language): string
    {
        $map = ['.' => $language->decimalSeparator()];
        foreach (array_values($language->digits()) as $digit => $native) {
            $map[(string) $digit] = $native;
        }

        return strtr($number->value(), $map);
    }
}
