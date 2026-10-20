<?php

declare(strict_types=1);

namespace Himelali\Contracts;

/**
 * Native digits of a language, used to write numbers like "৫২৭.৫৬".
 */
interface DigitsInterface
{
    /**
     * The ten native digits 0-9, or an empty array when the language uses ASCII digits.
     *
     * @return string[]
     */
    public function digits(): array;

    public function decimalSeparator(): string;
}
