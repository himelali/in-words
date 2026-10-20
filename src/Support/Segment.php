<?php

declare(strict_types=1);

namespace Himelali\Support;

/**
 * One part of a number split by a ScaleSystem, e.g. "215" with the scale "a thousand".
 */
final class Segment
{
    /**
     * @var string
     */
    private $digits;

    /**
     * @var string|null
     */
    private $scale;

    /**
     * @var string
     */
    private $words;

    /**
     * @param string      $digits
     * @param string|null $scale Scale name, null for the lowest group.
     * @param string      $words Words of the digits (without the scale word), empty until converted.
     */
    public function __construct($digits, $scale, $words = '')
    {
        $this->digits = $digits;
        $this->scale = $scale;
        $this->words = $words;
    }

    public function digits(): string
    {
        return $this->digits;
    }

    public function value(): int
    {
        return (int) $this->digits;
    }

    /**
     * @return string|null
     */
    public function scale()
    {
        return $this->scale;
    }

    public function words(): string
    {
        return $this->words;
    }

    public function isZero(): bool
    {
        return ltrim($this->digits, '0') === '';
    }

    /**
     * @param string $words
     */
    public function withWords($words): self
    {
        return new self($this->digits, $this->scale, $words);
    }
}
