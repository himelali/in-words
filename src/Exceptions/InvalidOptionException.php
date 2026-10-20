<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * An option or dependency passed to InWord has the wrong type.
 */
class InvalidOptionException extends Exception
{
    /**
     * @param string $option
     * @param string $expected
     * @param mixed $value
     */
    public static function type($option, $expected, $value): self
    {
        return new static(ucfirst($option) . ' must be ' . $expected . ', ' . static::describe($value) . ' given');
    }
}
