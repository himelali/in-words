<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Support\ScaleSystem;

/**
 * Indian English, lakh / crore system: "two lakh fifteen thousand two hundred forty-five".
 */
class EnglishIndia extends English
{
    protected $code = 'en_IN';
    protected $name = 'English (India)';

    /**
     * {@inheritdoc}
     * @throws InvalidLanguageException
     */
    public function nativeSystem(): ScaleSystem
    {
        return ScaleSystem::indian();
    }
}
