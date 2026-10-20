<?php

declare(strict_types=1);

namespace Himelali\Support;

/**
 * A validated, non-negative decimal number kept as a string, so it never overflows
 * 32-bit integers or loses float precision.
 */
final class Number
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $integer;

    /**
     * @var string
     */
    private $fraction;

    /**
     * @param string $value Plain decimal string such as "527.56", validated by NumberParser.
     */
    public function __construct($value)
    {
        $parts = explode('.', $value, 2);
        $integer = ltrim($parts[0], '0');

        $this->value = $value;
        $this->integer = $integer === '' ? '0' : $integer;
        $this->fraction = $parts[1] ?? '';
    }

    /**
     * The number as given, e.g. "527.50".
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Integer digits without leading zeros, e.g. "527".
     */
    public function integer(): string
    {
        return $this->integer;
    }

    /**
     * Decimal digits, e.g. "50", or an empty string.
     */
    public function fraction(): string
    {
        return $this->fraction;
    }

    public function hasFraction(): bool
    {
        return $this->fraction !== '';
    }
}
