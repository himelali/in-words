<?php

declare(strict_types=1);

namespace Himelali\Tests\Fixtures;

use Himelali\Languages\English;

/**
 * A custom language whose group() returns null for 5, used to test that a TypeError
 * becomes a ConversionException.
 */
class WrongTypeLanguage extends English
{
    protected $code = 'wrong';
    protected $name = 'Wrong type';

    /**
     * @param int $number
     */
    public function group($number): string
    {
        return $number === 5 ? null : parent::group($number);
    }
}
