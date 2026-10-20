<?php

declare(strict_types=1);

namespace Himelali\Languages;

/**
 * English, international system: "two hundred fifteen thousand two hundred forty-five".
 */
class English extends Language
{
    protected $code = 'en';
    protected $name = 'English';
    protected $words = [
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen',
    ];
    protected $tens = [2 => 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    protected $tensSeparator = '-';
    protected $hundred = 'hundred';
    protected $scales = [
        'thousand' => 'thousand',
        'lakh' => 'lakh',
        'crore' => 'crore',
        'million' => 'million',
        'billion' => 'billion',
        'trillion' => 'trillion',
    ];
    protected $point = 'point';
}
