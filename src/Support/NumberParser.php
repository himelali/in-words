<?php

declare(strict_types=1);

namespace Himelali\Support;

use Himelali\Exceptions\InvalidNumberException;
use Himelali\Exceptions\NumberOutOfRangeException;

/**
 * Validates user input and turns it into a Number.
 */
final class NumberParser
{
    /**
     * Maximum digits of the integer part (999 trillion).
     */
    const MAX_DIGITS = 15;

    /**
     * @param mixed $input int, float, numeric string or object with __toString()
     * @throws InvalidNumberException
     * @throws NumberOutOfRangeException
     */
    public function parse($input): Number
    {
        $value = $this->toString($input);

        if (!preg_match('/^\d+(\.\d+)?$/D', $value)) {
            if (preg_match('/^-\d+(\.\d+)?$/D', $value)) {
                throw NumberOutOfRangeException::negative($input);
            }
            throw InvalidNumberException::format($input);
        }

        $number = new Number($value);
        if (strlen($number->integer()) > self::MAX_DIGITS) {
            throw NumberOutOfRangeException::tooLarge($input, self::MAX_DIGITS);
        }

        return $number;
    }

    /**
     * @param mixed $input
     * @throws InvalidNumberException
     */
    private function toString($input): string
    {
        if (is_int($input)) {
            return (string) $input;
        }
        if (is_float($input)) {
            return $this->floatToString($input);
        }
        if (is_string($input)) {
            return trim($input);
        }
        if (is_object($input) && method_exists($input, '__toString')) {
            return trim((string) $input);
        }

        throw InvalidNumberException::type($input);
    }

    /**
     * Converts a float the same way on every PHP version: no locale separator (PHP 7),
     * no scientific notation and no precision noise (0.1 + 0.2 => "0.3").
     *
     * @param float $input
     * @throws InvalidNumberException
     */
    private function floatToString($input): string
    {
        if (is_nan($input) || is_infinite($input)) {
            throw InvalidNumberException::format($input);
        }
        if ($input == 0) {
            return '0';
        }

        $digits = strlen(sprintf('%.0F', floor(abs($input))));
        $value = sprintf('%.' . max(0, self::MAX_DIGITS - $digits) . 'F', $input);

        return strpos($value, '.') === false ? $value : rtrim(rtrim($value, '0'), '.');
    }
}
