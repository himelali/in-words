<p align="center">
  <img src="https://img.shields.io/packagist/v/himelali/in-words" alt="Latest Version"/>
  <img src="https://img.shields.io/packagist/dt/himelali/in-words" alt="Total Downloads"/>
  <img src="https://img.shields.io/packagist/php-v/himelali/in-words" alt="PHP Version"/>
  <img src="https://img.shields.io/github/license/himelali/in-words" alt="License"/>
  <img src="https://img.shields.io/github/stars/himelali/in-words" alt="Stars"/>
</p>

# in-words

Convert numbers to words in **14 languages**, using each language's native numbering system
(lakh / crore, 万 / 亿 ...) or the international one (million / billion / trillion).
It also writes numbers with native digits (`৫২৭.৫৬`, `٥٢٧٫٥٦`).

- PHP **7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5**, 32-bit and 64-bit, no dependencies
- Strict types (`declare(strict_types=1)`) and return type declarations; parameters are untyped and validated
- Follows PSR-1, PSR-4 (autoloading) and PSR-12 (coding style), checked with PHP_CodeSniffer
- Numbers up to **999 trillion** (15 integer digits) with any number of decimals
- Works with plain PHP, Laravel or any framework

## Installation

```bash
composer require himelali/in-words
```

## Quick start

```php
use Himelali\InWord;

$inWord = new InWord('bn');

echo $inWord->setNumber(215245)->getWord();   // দুই লক্ষ পনেরো হাজার দুই শত পঁয়তাল্লিশ
echo $inWord->setNumber(527.56)->getWord();   // পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
echo $inWord->setNumber(527.56)->getNumber(); // ৫২৭.৫৬

echo (new InWord())->toWords(215245);         // two hundred fifteen thousand two hundred forty-five
```

## Languages

| Code | Language | 2,500,000 (native, default) | 2,500,000 (international) |
|---|---|---|---|
| `en` | English | two million five hundred thousand | _same_ |
| `en_IN` | English (India) | twenty-five lakh | two million five hundred thousand |
| `zh` | Chinese (Simplified) | 二百五十万 | _same_ |
| `hi` | Hindi | पच्चीस लाख | दो मिलियन पाँच सौ हज़ार |
| `es` | Spanish | dos millones quinientos mil | _same_ |
| `ar` | Arabic | مليونان وخمسمائة ألف | _same_ |
| `fr` | French | deux millions cinq cent mille | _same_ |
| `bn` | Bengali | পঁচিশ লক্ষ | দুই মিলিয়ন পাঁচ শত হাজার |
| `pt` | Portuguese (Brazil) | dois milhões e quinhentos mil | _same_ |
| `ru` | Russian | два миллиона пятьсот тысяч | _same_ |
| `ur` | Urdu | پچیس لاکھ | دو ملین پانچ سو ہزار |
| `id` | Indonesian | dua juta lima ratus ribu | _same_ |
| `de` | German | zwei Millionen fünfhunderttausend | _same_ |
| `ja` | Japanese | 二百五十万 | _same_ |

Language codes are not case sensitive and accept `-` or `_` (`en_IN`, `en-in`).

Each language follows its own grammar, for example French `quatre-vingts` and `soixante et onze`,
Spanish `veintiún mil`, Russian `две тысячи` / `пять тысяч`, Arabic dual and plural forms
(`ألفان`, `ثلاثة آلاف`), German compound words, and Chinese `零` placement (`一亿零一万`).

## Million, billion and trillion

Pass `true` to `setInternational()` (or as the second constructor argument) to use
million / billion / trillion instead of the language's native system. Pass `false` to switch back.

```php
$inWord = new InWord('bn');

echo $inWord->setNumber(2500000)->getWord();                         // পঁচিশ লক্ষ
echo $inWord->setInternational(true)->setNumber(2500000)->getWord(); // দুই মিলিয়ন পাঁচ শত হাজার
echo $inWord->setInternational(false)->getWord();                    // পঁচিশ লক্ষ

$inWord = new InWord('en_IN', true);
echo $inWord->toWords(1000000000);                                   // one billion
```

The toggle changes the output of `en_IN`, `bn`, `hi` and `ur`, which count in lakh and crore natively.
The other languages already count in millions, so both settings give the same words. Billion and trillion
are translated as each language says them: French `milliard` (10⁹) and `billion` (10¹²), German `Milliarde`,
Spanish `mil millones` and `billón`. Chinese and Japanese express millions in their own system
(`一百万`, `百万` = one million), so they look the same either way.

## API

| Method | Description |
|---|---|
| `new InWord($language = 'en', $international = false)` | Language code or `LanguageInterface` instance |
| `setLanguage($language)` | Change the language (`setConfig()` is a 1.x compatible alias) |
| `setInternational($enabled = true)` | `true`: million / billion / trillion, `false`: native system |
| `isInternational()` | Current setting |
| `setNumber($number)` | int, float, numeric string or stringable object |
| `getWord()` | The number in words |
| `getNumber()` | The number with native digits |
| `toWords($number)` | Shortcut for `setNumber($number)->getWord()` |
| `getLanguage()` | The active `LanguageInterface` |
| `getSupportedLanguages()` | `code => name` of all languages |
| `getVersion()` | Package version |

> Pass decimals as strings (`'527.50'`) to keep trailing zeros. Floats are rounded to 15 significant digits,
> so `0.1 + 0.2` reads `zero point three` on every PHP version.

## Error handling

Every exception extends `Himelali\Exception`, so one `catch` handles them all, or catch a specific one:

| Exception (`Himelali\Exceptions\`) | Thrown when |
|---|---|
| `InvalidNumberException` | The value is not a number: wrong type, `'abc'`, `'1e5'`, `NAN`, `INF` |
| `NumberOutOfRangeException` | Negative, or more than 15 integer digits (extends `InvalidNumberException`) |
| `UnsupportedLanguageException` | The language code is not registered |
| `InvalidOptionException` | `setInternational()` gets a non-boolean, or an invalid dependency is passed to the constructor |
| `InvalidLanguageException` | A registered custom language class is invalid |
| `NumberNotSetException` | `getWord()` / `getNumber()` is called before `setNumber()` |
| `ConversionException` | A language fails while converting (e.g. a custom language missing a word). PHP warnings and errors are converted too, and the original is available via `getPrevious()` |

```php
use Himelali\Exception;
use Himelali\Exceptions\InvalidNumberException;

try {
    echo (new InWord('en'))->toWords($input);
} catch (InvalidNumberException $e) {
    echo 'Please enter a valid amount: ' . $e->getMessage();
} catch (Exception $e) {
    // any other error from the package
}
```

## Adding a language

Extend `Himelali\Languages\Language` and define the words. Override a method only where the grammar differs.
Keep the parameters untyped like the parent methods (adding a parameter type to an override is a fatal error):

```php
use Himelali\Languages\Language;

class Turkish extends Language
{
    protected $code = 'tr';
    protected $name = 'Turkish';
    protected $words = ['sıfır', 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz', 'on'];
    protected $tens = [1 => 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    protected $scales = ['thousand' => 'bin', 'million' => 'milyon', 'billion' => 'milyar', 'trillion' => 'trilyon'];
    protected $point = 'virgül';

    // "yüz" (100), "iki yüz" (200)
    protected function hundreds($hundreds, $rest): string
    {
        return $hundreds === 1 ? 'yüz' : $this->words[$hundreds] . ' yüz';
    }

    // "bin" (1000), "iki bin" (2000)
    protected function scale($segment): string
    {
        if ($segment->scale() === 'thousand' && $segment->value() === 1) {
            return 'bin';
        }
        return parent::scale($segment);
    }
}
```

Use it directly, or register a code for it:

```php
use Himelali\InWord;
use Himelali\LanguageRegistry;

echo (new InWord(new Turkish()))->toWords(42); // kırk iki

$registry = (new LanguageRegistry())->register('tr', Turkish::class);
echo (new InWord('tr', false, $registry))->toWords(42);
```

## Package structure

```
src/
├── InWord.php                 Facade: the public API
├── LanguageRegistry.php       Language code => class
├── Exception.php              Base exception
├── Contracts/                 LanguageInterface, DigitsInterface, ConverterInterface
├── Exceptions/                Specific exceptions
├── Languages/                 Language base class + one class per language
└── Support/                   NumberParser, Number, ScaleSystem, Segment,
                               NumberConverter, DigitTransliterator, ErrorGuard
tests/
├── run.php                    Test runner
├── bootstrap.php              Autoloader (Composer or built-in PSR-4 fallback)
└── Fixtures/                  Expected words per language + test languages (Himelali\Tests\Fixtures)
```

Namespaces map to directories with PSR-4: `Himelali\` => `src/`, `Himelali\Tests\` => `tests/`.

## Upgrading from 1.x

- Requires PHP 7.0 or newer.
- The namespace changed from `Himel\Web` to `Himelali`: use `Himelali\InWord` instead of `Himel\Web\InWord`.
- `en` now uses million / billion / trillion. Use `en_IN` for lakh / crore (spelled `lakh`, previously `lac`).
- English no longer adds a plural "s" (`two hundred`, not `two hundreds`) and hyphenates tens (`forty-five`).
- Invalid input throws exceptions instead of calling `die()`. `getWord()` without a number throws `NumberNotSetException`.
- The unused month and weekday arrays of the old `src/lang` files were removed.

## Testing and coding standard

```bash
composer test   # run the test suite
composer lint   # check PSR-12 with PHP_CodeSniffer
composer fix    # fix PSR-12 violations automatically
```

The test suite has no dependencies and runs on every supported PHP version (`php tests/run.php`).
It is verified on PHP 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5 (64-bit) and on 32-bit builds.

Class constants are declared without a visibility keyword: PSR-12 requires `public const` only when the
minimum PHP version is 7.1 or later, and it is a syntax error on PHP 7.0.

## License

The MIT License (MIT). See [LICENSE](LICENSE).
