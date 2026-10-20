<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Support\ScaleSystem;

/**
 * Chinese (中文, Simplified Mandarin), myriad system: 万 = 10^4, 亿 = 10^8.
 */
class Chinese extends Language
{
    protected $code = 'zh';
    protected $name = 'Chinese (Simplified)';
    protected $words = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    protected $scales = ['wan' => '万', 'yi' => '亿'];
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
        return new ScaleSystem(4, ['wan' => 4, 'yi' => null]);
    }

    /**
     * Chinese counts millions natively (一百万 = one million), so both systems are the same.
     *
     * {@inheritdoc}
     */
    public function internationalSystem(): ScaleSystem
    {
        return $this->nativeSystem();
    }

    /**
     * 0 - 9999, inner zeros read once as 零: 1010 => 一千零一十.
     *
     * {@inheritdoc}
     */
    public function group($number): string
    {
        $digits = str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        $words = '';
        $zero = false;
        foreach ($this->places as $index => $place) {
            $digit = (int) $digits[$index];
            if ($digit === 0) {
                $zero = $words !== '';
                continue;
            }
            $words .= ($zero ? '零' : '') . $this->words[$digit] . $place;
            $zero = false;
        }

        return $words;
    }

    /**
     * Adds 零 for skipped places between groups (一亿零一万) and reads a leading 一十 as 十.
     *
     * {@inheritdoc}
     */
    public function combine($segments): string
    {
        $words = '';
        $gap = false;
        foreach ($segments as $segment) {
            if ($segment->isZero()) {
                $gap = $words !== '';
                continue;
            }
            if ($words !== '' && ($gap || $segment->value() < 1000)) {
                $words .= '零';
            }
            $words .= $segment->scale() === null ? $segment->words() : $this->scale($segment);
            $gap = false;
        }

        return strpos($words, '一十') === 0 ? substr($words, strlen('一')) : $words;
    }
}
