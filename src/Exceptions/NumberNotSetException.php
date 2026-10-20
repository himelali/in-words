<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * A result was requested before any number was set.
 */
class NumberNotSetException extends Exception
{
    public static function create(): self
    {
        return new static('No number set, call setNumber() first');
    }
}
