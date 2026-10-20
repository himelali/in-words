<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

/**
 * The number is valid but outside the supported range (negative or too many digits).
 */
class NumberOutOfRangeException extends InvalidNumberException
{
    /**
     * @param mixed $value
     */
    public static function negative($value): self
    {
        return new static('Negative numbers are not supported, ' . static::describe($value) . ' given');
    }

    /**
     * @param mixed $value
     * @param int   $maxDigits
     */
    public static function tooLarge($value, $maxDigits): self
    {
        return new static(
            'Number ' . static::describe($value) . ' is too large, the integer part may have at most '
            . $maxDigits . ' digits'
        );
    }
}
