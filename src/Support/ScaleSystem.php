<?php

declare(strict_types=1);

namespace Himelali\Support;

use Himelali\Exceptions\InvalidLanguageException;

/**
 * Describes how a numbering system groups digits, e.g. thousand / million / billion
 * (groups of 3), a thousand / lakh / crore (3, 2, 2) or 万 / 亿 (groups of 4).
 */
final class ScaleSystem
{
    /**
     * @var int
     */
    private $groupDigits;

    /**
     * @var array
     */
    private $scales;

    /**
     * @param int   $groupDigits Digits of the lowest group, read without a scale word (3 = 0 - 999).
     * @param array $scales      Scale name => digits it holds, lowest first. The last one must be null,
     *                           it holds every remaining digit (and is read recursively when large).
     * @throws InvalidLanguageException
     */
    public function __construct($groupDigits, $scales)
    {
        if (!is_int($groupDigits) || $groupDigits < 1 || !is_array($scales) || !$scales || end($scales) !== null) {
            throw new InvalidLanguageException(
                'A scale system needs a positive group size and its last scale must be null'
            );
        }
        $this->groupDigits = $groupDigits;
        $this->scales = $scales;
    }

    /**
     * Thousand, million, billion, trillion.
     * @throws InvalidLanguageException
     */
    public static function international(): self
    {
        return new self(3, ['thousand' => 3, 'million' => 3, 'billion' => 3, 'trillion' => null]);
    }

    /**
     * Thousand, lakh, crore (South Asian system).
     * @throws InvalidLanguageException
     */
    public static function indian(): self
    {
        return new self(3, ['thousand' => 2, 'lakh' => 2, 'crore' => null]);
    }

    /**
     * Numbers below this are read by LanguageInterface::group().
     */
    public function groupLimit(): int
    {
        return 10 ** $this->groupDigits;
    }

    /**
     * Splits integer digits into segments, highest first. Zero segments are kept.
     *
     * @param string $digits
     * @return Segment[]
     */
    public function split($digits): array
    {
        $segments = [new Segment($this->take($digits, $this->groupDigits), null)];
        foreach ($this->scales as $scale => $size) {
            if ($digits === '') {
                break;
            }
            $segments[] = new Segment($this->take($digits, $size), $scale);
        }

        return array_reverse($segments);
    }

    /**
     * Removes and returns the last $size digits (all of them when $size is null).
     *
     * @param string   $digits
     * @param int|null $size
     */
    private function take(&$digits, $size): string
    {
        $length = strlen($digits);
        if ($size === null || $size >= $length) {
            $part = $digits;
            $digits = '';
            return $part;
        }
        $part = substr($digits, $length - $size);
        $digits = substr($digits, 0, $length - $size);

        return $part;
    }
}
