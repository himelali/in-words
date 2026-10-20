<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * The given value is not a number this package can read (wrong type or format, NAN, INF).
 */
class InvalidNumberException extends Exception
{
    /**
     * @param mixed $value
     */
    public static function type($value): self
    {
        return new static(
            'Number must be an int, float, numeric string or stringable object, ' . static::describe($value) . ' given'
        );
    }

    /**
     * @param mixed $value
     */
    public static function format($value): self
    {
        return new static('Number ' . static::describe($value) . ' is not a valid decimal number');
    }
}
