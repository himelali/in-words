<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Contracts\DigitsInterface;
use Himelali\Contracts\LanguageInterface;
use Himelali\Exceptions\ConversionException;
use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Support\ScaleSystem;
use Himelali\Support\Segment;

/**
 * Shared grammar of most languages. A language only defines its words and overrides
 * the hooks where its grammar differs.
 */
abstract class Language implements LanguageInterface, DigitsInterface
{
    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * Words of 0 - n. Missing numbers below 100 are built as tens + separator + unit.
     *
     * @var string[]
     */
    protected $words = [];

    /**
     * Tens words keyed by the tens digit (2 => twenty).
     *
     * @var string[]
     */
    protected $tens = [];

    /**
     * @var string
     */
    protected $tensSeparator = ' ';

    /**
     * @var string
     */
    protected $hundred = '';

    /**
     * Scale name => word, or [singular, plural, ...] forms chosen by pluralForm().
     *
     * @var array
     */
    protected $scales = [];

    /**
     * Separator between words.
     *
     * @var string
     */
    protected $separator = ' ';

    /**
     * Separator between hundreds and the rest, null to use $separator.
     *
     * @var string|null
     */
    protected $hundredSeparator;

    /**
     * Separator between scale segments, null to use $separator.
     *
     * @var string|null
     */
    protected $segmentSeparator;

    /**
     * Word between the integer and the decimal part.
     *
     * @var string
     */
    protected $point = '';

    /**
     * Native digits 0 - 9, empty for ASCII digits.
     *
     * @var string[]
     */
    protected $digits = [];

    /**
     * @var string
     */
    protected $decimalSeparator = '.';

    /**
     * {@inheritdoc}
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidLanguageException
     */
    public function nativeSystem(): ScaleSystem
    {
        return ScaleSystem::international();
    }

    /**
     * {@inheritdoc}
     * @throws InvalidLanguageException
     */
    public function internationalSystem(): ScaleSystem
    {
        return ScaleSystem::international();
    }

    public function zero(): string
    {
        return $this->words[0];
    }

    /**
     * {@inheritdoc}
     */
    public function digit($digit): string
    {
        return $this->words[$digit];
    }

    /**
     * {@inheritdoc}
     */
    public function digits(): array
    {
        return $this->digits;
    }

    public function decimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function group($number): string
    {
        $hundreds = intdiv($number, 100);
        $rest = $number % 100;

        $words = [];
        if ($hundreds > 0) {
            $words[] = $this->hundreds($hundreds, $rest);
        }
        if ($rest > 0) {
            $words[] = $this->belowHundred($rest);
        }

        return implode($this->hundredSeparator ?? $this->separator, $words);
    }

    /**
     * {@inheritdoc}
     */
    public function combine($segments): string
    {
        $words = [];
        foreach ($segments as $segment) {
            if (!$segment->isZero()) {
                $words[] = $segment->scale() === null ? $segment->words() : $this->scale($segment);
            }
        }

        return implode($this->segmentSeparator ?? $this->separator, $words);
    }

    /**
     * {@inheritdoc}
     */
    public function decimal($integer, $digits): string
    {
        return $this->join([$integer, $this->point, $this->join($digits)]);
    }

    /**
     * Words of 1 - 99.
     *
     * @param int $number
     */
    protected function belowHundred($number): string
    {
        if (isset($this->words[$number])) {
            return $this->words[$number];
        }
        $tens = $this->tens[intdiv($number, 10)];
        $unit = $number % 10;

        return $unit === 0 ? $tens : $tens . $this->tensSeparator . $this->words[$unit];
    }

    /**
     * Words of the hundreds part, e.g. "two hundred".
     *
     * @param int $hundreds 1 - 9
     * @param int $rest     The tens and units that follow (0 - 99).
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $this->join([$this->belowHundred($hundreds), $this->hundred]);
    }

    /**
     * Words of a segment with its scale word, e.g. "two million".
     *
     * @param Segment $segment
     */
    protected function scale($segment): string
    {
        return $this->join([$segment->words(), $this->scaleWord($segment->scale(), $segment->value())]);
    }

    /**
     * @param string $scale
     * @param int $count
     * @throws ConversionException
     */
    protected function scaleWord($scale, $count): string
    {
        $forms = $this->scaleForms($scale);

        return $forms[$this->pluralForm($count)] ?? $forms[0];
    }

    /**
     * @param string $scale
     * @return string[]
     * @throws ConversionException
     */
    protected function scaleForms($scale): array
    {
        if (!isset($this->scales[$scale])) {
            throw ConversionException::missingScale($scale, $this->code);
        }

        return (array) $this->scales[$scale];
    }

    /**
     * Index of the scale word form for a count (0 = singular, 1 = plural).
     *
     * @param int $count
     */
    protected function pluralForm($count): int
    {
        return $count === 1 ? 0 : 1;
    }

    /**
     * @param string[] $words
     */
    protected function join($words): string
    {
        return implode($this->separator, $words);
    }
}
