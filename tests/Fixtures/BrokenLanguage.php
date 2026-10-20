<?php

declare(strict_types=1);

namespace Himelali\Tests\Fixtures;

use Himelali\Languages\Language;

/**
 * A custom language without tens and scale words, used to test error handling.
 */
class BrokenLanguage extends Language
{
    protected $code = 'broken';
    protected $name = 'Broken';
    protected $words = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
}
