<?php

/*
 * Dependency free test runner, works on PHP 7.0 - 8.5.
 * Usage: php tests/run.php   (or: composer test)
 */

declare(strict_types=1);

use Himelali\Exception;
use Himelali\Exceptions\ConversionException;
use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Exceptions\InvalidNumberException;
use Himelali\Exceptions\InvalidOptionException;
use Himelali\Exceptions\NumberNotSetException;
use Himelali\Exceptions\NumberOutOfRangeException;
use Himelali\Exceptions\UnsupportedLanguageException;
use Himelali\InWord;
use Himelali\LanguageRegistry;
use Himelali\Languages\English;
use Himelali\Support\ScaleSystem;
use Himelali\Tests\Fixtures\BrokenLanguage;
use Himelali\Tests\Fixtures\WrongTypeLanguage;

require __DIR__ . '/bootstrap.php';

$passed = 0;
$failures = [];

// Any PHP notice, warning or deprecation fails the run.
set_error_handler(function ($severity, $message, $file, $line) use (&$failures) {
    $failures[] = "PHP error: $message ($file:$line)";
    return true;
});

$check = function ($label, $expected, $actual) use (&$passed, &$failures) {
    if ($expected === $actual) {
        $passed++;
        return;
    }
    $failures[] = "$label\n      expected: " . var_export($expected, true)
        . "\n      actual:   " . var_export($actual, true);
};

$describe = function ($value): string {
    return is_object($value) ? get_class($value) : str_replace("\n", '', var_export($value, true));
};

$throws = function ($label, $class, $callback) use (&$passed, &$failures) {
    try {
        $callback();
        $failures[] = "$label: expected $class, nothing thrown";
    } catch (Throwable $error) {
        if ($error instanceof $class && $error instanceof Exception) {
            $passed++;
        } else {
            $failures[] = "$label: expected $class, got " . get_class($error) . ': ' . $error->getMessage();
        }
    }
};

// ---------------------------------------------------------------- languages
$languages = require __DIR__ . '/Fixtures/languages.php';
$inWord = new InWord();
$supported = array_keys($inWord->getSupportedLanguages());
$tested = array_keys($languages);
sort($supported);
sort($tested);
$check('every language has fixtures', $supported, $tested);

foreach ($languages as $code => $cases) {
    $inWord->setLanguage($code);
    foreach ($cases['native'] as list($number, $words)) {
        $label = "$code " . $describe($number);
        $check("$label native", $words, $inWord->setInternational(false)->toWords($number));
        if (!isset($cases['international'])) {
            $intl = $inWord->setInternational(true)->toWords($number);
            $check("$label international (same as native)", $words, $intl);
        }
    }
    foreach ($cases['international'] ?? [] as list($number, $words)) {
        $intl = $inWord->setInternational(true)->toWords($number);
        $check("$code " . $describe($number) . ' international', $words, $intl);
    }
    foreach ($cases['digits'] ?? [] as list($number, $digits)) {
        $check("$code " . $describe($number) . ' getNumber', $digits, $inWord->setNumber($number)->getNumber());
    }
    // Words must be NFC normalized so they compare equal to user / database text.
    if (class_exists(Normalizer::class)) {
        foreach ($cases['native'] as list($number)) {
            $words = $inWord->setInternational(false)->toWords($number);
            $normalized = Normalizer::normalize($words, Normalizer::FORM_C);
            $check("$code " . $describe($number) . ' is NFC', $normalized, $words);
        }
    }
}

// ---------------------------------------------------------------- input formats
$english = new InWord('en');
$inputs = [
    [0.1 + 0.2, 'zero point three'],
    [5.0, 'five'],
    [0.00001, 'zero point zero zero zero zero one'],
    [-0.0, 'zero'],
    [150000000000000.0, 'one hundred fifty trillion'],
    [' 42 ', 'forty-two'],
    ['007', 'seven'],
    [new SplFileInfo('123'), 'one hundred twenty-three'],
];
foreach ($inputs as list($number, $words)) {
    $check('input ' . $describe($number), $words, $english->toWords($number));
}
$check('getNumber keeps trailing zeros', '527.50', $english->setNumber('527.50')->getNumber());

// Float input must not depend on the locale (PHP 7 used it when casting floats).
if (setlocale(LC_ALL, 'de_DE.UTF-8', 'de_DE', 'German_Germany.1252') !== false) {
    $check('float under de_DE locale', 'five hundred twenty-seven point five six', $english->toWords(527.56));
    setlocale(LC_ALL, 'C');
}

// ---------------------------------------------------------------- toggle & API
$bengali = new InWord('bn', true);
$check('constructor international flag', 'এক মিলিয়ন', $bengali->toWords(1000000));
$check('isInternational', true, $bengali->isInternational());
$bengali->setInternational(false)->setInternational();
$check('setInternational() defaults to true', true, $bengali->isInternational());
$check('setConfig alias', 'five', $bengali->setConfig('en')->toWords(5));
$check('language code is case / dash insensitive', 'en_IN', $bengali->setLanguage('EN-in')->getLanguage()->code());
$check('language instance', 'five', $bengali->setLanguage(new English())->toWords(5));
$check('version', InWord::VERSION, $bengali->getVersion());
$check('supported languages', 14, count($bengali->getSupportedLanguages()));

$registry = new LanguageRegistry();
$registry->register('en_GB', English::class);
$check('custom registry', 'five', (new InWord('en-GB', false, $registry))->toWords(5));

// ---------------------------------------------------------------- exceptions
$invalidNumbers = [
    'abc', '1e5', '1E5', '', '5.', '.5', '+5', '1,000', '../x',
    INF, NAN, null, true, [5], new stdClass(),
];
foreach ($invalidNumbers as $value) {
    $throws('invalid ' . $describe($value), InvalidNumberException::class, function () use ($english, $value) {
        $english->setNumber($value);
    });
}
foreach ([-5, -0.5, '-12', '1000000000000000', 1.0E+15] as $value) {
    $throws('out of range ' . $describe($value), NumberOutOfRangeException::class, function () use ($english, $value) {
        $english->setNumber($value);
    });
}
foreach (['fr_XX', '../../etc/passwd', '', null, ['en'], 5] as $value) {
    $throws('unsupported language ' . $describe($value), UnsupportedLanguageException::class, function () use ($value) {
        new InWord($value);
    });
}
foreach ([1, 'yes', null] as $value) {
    $throws('international ' . $describe($value), InvalidOptionException::class, function () use ($english, $value) {
        $english->setInternational($value);
    });
}
$throws('invalid registry', InvalidOptionException::class, function () {
    new InWord('en', false, new stdClass());
});
$throws('invalid converter', InvalidOptionException::class, function () {
    new InWord('en', false, null, 'converter');
});
$throws('register missing class', InvalidLanguageException::class, function () use ($registry) {
    $registry->register('xx', 'Missing\Language');
});
$throws('register class without interface', InvalidLanguageException::class, function () use ($registry) {
    $registry->register('xx', stdClass::class);
});
$throws('register empty code', InvalidLanguageException::class, function () use ($registry) {
    $registry->register('', English::class);
});
$throws('register non-string code', InvalidLanguageException::class, function () use ($registry) {
    $registry->register(5, English::class);
});
$invalidSystems = [['3', ['a' => null]], [0, ['a' => null]], [3, 'scales'], [3, []], [3, ['a' => 3]]];
foreach ($invalidSystems as list($size, $scales)) {
    $label = 'scale system ' . $describe($size) . ' ' . $describe($scales);
    $throws($label, InvalidLanguageException::class, function () use ($size, $scales) {
        new ScaleSystem($size, $scales);
    });
}
$throws('getWord without number', NumberNotSetException::class, function () {
    (new InWord())->getWord();
});
$throws('getNumber without number', NumberNotSetException::class, function () {
    (new InWord())->getNumber();
});

// A broken custom language: PHP warnings, TypeErrors and errors become a ConversionException.
$broken = new InWord(new BrokenLanguage());
$throws('broken language warning', ConversionException::class, function () use ($broken) {
    $broken->toWords(15);
});
$throws('broken language missing scale', ConversionException::class, function () use ($broken) {
    $broken->toWords(1000);
});
try {
    $broken->toWords(15);
} catch (ConversionException $exception) {
    $check('conversion keeps previous error', true, $exception->getPrevious() instanceof ErrorException);
}
$throws('language returning a wrong type', ConversionException::class, function () {
    (new InWord(new WrongTypeLanguage()))->toWords(5);
});
$check('error handler restored', 'five', $english->toWords(5));

restore_error_handler();

$environment = PHP_VERSION . ' (' . (PHP_INT_SIZE * 8) . '-bit int, intl '
    . (class_exists(Normalizer::class) ? 'on' : 'off') . ')';
echo "PHP $environment: $passed passed, " . count($failures) . " failed\n";
foreach ($failures as $failure) {
    echo "  FAIL: $failure\n";
}
exit($failures ? 1 : 0);
