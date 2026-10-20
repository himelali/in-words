<?php

declare(strict_types=1);

namespace Himelali\Contracts;

use Himelali\Support\Number;

/**
 * Converts a number to words in a language.
 */
interface ConverterInterface
{
    /**
     * @param Number $number
     * @param LanguageInterface $language
     * @param bool $international Use million / billion / trillion instead of the native system.
     */
    public function convert($number, $language, $international = false): string;
}
