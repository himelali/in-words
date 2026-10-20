<?php

declare(strict_types=1);

namespace Himelali\Support;

use Himelali\Contracts\ConverterInterface;
use Himelali\Contracts\LanguageInterface;

/**
 * Language independent algorithm: splits the number with the chosen scale system and
 * lets the language put each part into words.
 */
class NumberConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($number, $language, $international = false): string
    {
        $system = $international ? $language->internationalSystem() : $language->nativeSystem();
        $words = $this->integer($number->integer(), $language, $system);
        if (!$number->hasFraction()) {
            return $words;
        }

        $digits = [];
        foreach (str_split($number->fraction()) as $digit) {
            $digits[] = $language->digit((int) $digit);
        }

        return $language->decimal($words, $digits);
    }

    /**
     * @param string $digits
     * @param LanguageInterface $language
     * @param ScaleSystem $system
     */
    private function integer($digits, $language, $system): string
    {
        $digits = ltrim($digits, '0');
        if ($digits === '') {
            return $language->zero();
        }

        $segments = [];
        foreach ($system->split($digits) as $segment) {
            if (!$segment->isZero()) {
                $words = $segment->value() < $system->groupLimit()
                    ? $language->group($segment->value())
                    : $this->integer($segment->digits(), $language, $system);
                $segment = $segment->withWords($words);
            }
            $segments[] = $segment;
        }

        return $language->combine($segments);
    }
}
