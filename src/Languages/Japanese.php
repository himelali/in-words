<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Support\ScaleSystem;

/**
 * Japanese (日本語), myriad system: 万 = 10^4, 億 = 10^8, 兆 = 10^12.
 */
class Japanese extends Language
{
    protected $code = 'ja';
    protected $name = 'Japanese';
    protected $words = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    protected $scales = ['man' => '万', 'oku' => '億', 'cho' => '兆'];
    protected $separator = '';
    protected $point = '点';

    /**
     * Places of a four digit group, highest first.
     *
     * @var string[]
     */
    protected $places = ['千', '百', '十', ''];

    /**
     * {@inheritdoc}
     * @throws InvalidLanguageException
     */
    public function nativeSystem(): ScaleSystem
    {
        return new ScaleSystem(4, ['man' => 4, 'oku' => 4, 'cho' => null]);
    }

    /**
     * Japanese counts millions natively (百万 = one million), so both systems are the same.
     *
     * {@inheritdoc}
     * @throws InvalidLanguageException
     */
    public function internationalSystem(): ScaleSystem
    {
        return $this->nativeSystem();
    }

    /**
     * 0 - 9999 without zeros and without 一 before 十, 百 and 千: 1101 => 千百一.
     *
     * {@inheritdoc}
     */
    public function group($number): string
    {
        $digits = str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        $words = '';
        foreach ($this->places as $index => $place) {
            $digit = (int) $digits[$index];
            if ($digit !== 0) {
                $words .= ($digit === 1 && $place !== '' ? '' : $this->words[$digit]) . $place;
            }
        }

        return $words;
    }

    /**
     * 千 keeps its 一 unless it starts the number: 千 (1000), 一千万, 二万一千.
     *
     * {@inheritdoc}
     */
    public function combine($segments): string
    {
        $words = '';
        foreach ($segments as $segment) {
            if ($segment->isZero()) {
                continue;
            }
            $part = $segment->words();
            if (strpos($part, '千') === 0 && ($words !== '' || $segment->scale() !== null)) {
                $part = '一' . $part;
            }
            $words .= $segment->scale() === null ? $part : $this->scale($segment->withWords($part));
        }

        return $words;
    }
}
