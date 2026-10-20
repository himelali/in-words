<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * A language (or its numbering system) definition is invalid, e.g. a registered class
 * that does not exist or does not implement LanguageInterface.
 */
class InvalidLanguageException extends Exception
{
    /**
     * @param mixed  $class
     * @param string $interface
     */
    public static function invalidClass($class, $interface): self
    {
        return new static('Language class ' . static::describe($class) . ' must exist and implement ' . $interface);
    }

    /**
     * @param mixed $code
     */
    public static function invalidCode($code): self
    {
        return new static('Language code must be a non-empty string, ' . static::describe($code) . ' given');
    }
}
