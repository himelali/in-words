<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * The requested language is not registered.
 */
class UnsupportedLanguageException extends Exception
{
    /**
     * @param mixed    $language
     * @param string[] $supported
     */
    public static function language($language, $supported): self
    {
        return new static(
            'Language ' . static::describe($language) . ' is not supported. Supported languages: '
            . implode(', ', $supported)
        );
    }
}
