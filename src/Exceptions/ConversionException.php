<?php

declare(strict_types=1);

namespace Himelali\Exceptions;

use Himelali\Exception;

/**
 * Converting failed unexpectedly, e.g. a custom language is missing a word.
 * The original error (exception, PHP error or warning) is available via getPrevious().
 */
class ConversionException extends Exception
{
    /**
     * @param \Throwable $previous
     */
    public static function fromError($previous): self
    {
        return new static('Conversion failed: ' . $previous->getMessage(), 0, $previous);
    }

    /**
     * @param string $scale
     * @param string $language
     */
    public static function missingScale($scale, $language): self
    {
        return new static("Scale word '" . $scale . "' is not defined for language '" . $language . "'");
    }
}
