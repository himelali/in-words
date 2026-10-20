<?php

declare(strict_types=1);

namespace Himelali\Contracts;

use Himelali\Support\ScaleSystem;
use Himelali\Support\Segment;

/**
 * Number words and grammar of one language.
 */
interface LanguageInterface
{
    /**
     * Language code, e.g. "en" or "en_IN".
     */
    public function code(): string;

    /**
     * Language name in English, e.g. "Bengali".
     */
    public function name(): string;

    /**
     * Numbering system the language uses natively (e.g. lakh/crore or 万/亿).
     */
    public function nativeSystem(): ScaleSystem;

    /**
     * Million / billion / trillion numbering system.
     */
    public function internationalSystem(): ScaleSystem;

    public function zero(): string;

    /**
     * Words for a number below the scale system's group limit (e.g. 1 - 999).
     *
     * @param int $number
     */
    public function group($number): string;

    /**
     * Joins the segments (highest first, zero segments included) into the words of the whole number.
     *
     * @param Segment[] $segments
     */
    public function combine($segments): string;

    /**
     * Word for a single digit of the decimal part.
     *
     * @param int $digit
     */
    public function digit($digit): string;

    /**
     * Joins the integer words and the decimal digit words.
     *
     * @param string $integer
     * @param string[] $digits
     */
    public function decimal($integer, $digits): string;
}
