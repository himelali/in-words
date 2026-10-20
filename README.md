<p align="center">
  <img src="https://img.shields.io/packagist/v/himelali/in-words" alt="Latest Version"/>
  <img src="https://img.shields.io/packagist/dt/himelali/in-words" alt="Total Downloads"/>
  <img src="https://img.shields.io/packagist/php-v/himelali/in-words" alt="PHP Version"/>
  <img src="https://img.shields.io/github/license/himelali/in-words" alt="License"/>
  <img src="https://img.shields.io/github/stars/himelali/in-words" alt="Stars"/>
</p>

# in-words

Convert numbers to words in **14 languages**. Each language uses its own numbering system
(lakh / crore, 万 / 亿 ...) or, with one switch, the international one (million / billion / trillion).
It can also write numbers with native digits (`৫২৭.৫৬`, `५२७.५६`, `٥٢٧٫٥٦`).

```php
use Himelali\InWord;

echo (new InWord('bn'))->toWords(215245); // দুই লক্ষ পনেরো হাজার দুই শত পঁয়তাল্লিশ
echo (new InWord('en'))->toWords(215245); // two hundred fifteen thousand two hundred forty-five
echo (new InWord('fr'))->toWords(80);     // quatre-vingts
```

## Contents

- [What's new in 2.0.0](#whats-new-in-200)
- [Features](#features)
- [Installation](#installation)
- [How to use](#how-to-use)
- [Supported languages](#supported-languages)
- [API reference](#api-reference)
- [Error handling](#error-handling)
- [Adding a language](#adding-a-language)
- [Upgrading from 1.x](#upgrading-from-1x)
- [Testing and coding standard](#testing-and-coding-standard)

## What's new in 2.0.0

- **14 languages** instead of 2: English, English (India), Chinese, Hindi, Spanish, Arabic, French, Bengali,
  Portuguese, Russian, Urdu, Indonesian, German and Japanese, each following its own grammar.
- **Million / billion / trillion switch**: `setInternational(true)` uses the international system,
  `setInternational(false)` (the default) the language's native one (lakh / crore, 万 / 亿 ...).
- **Native digits** for Bengali, Hindi, Urdu and Arabic with `getNumber()`.
- **Exceptions instead of `die()`**: every error extends `Himelali\Exception`, and PHP warnings or errors
  during a conversion are turned into a `ConversionException`.
- **Accurate on every PHP version**: numbers are processed as text, so there is no 32-bit integer
  overflow, and floats read the same on every version and locale (`0.1 + 0.2` is `zero point three`).
- **Correct English**: `two hundred` instead of `two hundreds`, `forty-five` with a hyphen, and a
  separate `en_IN` (lakh / crore) next to the international `en`.
- **Extensible**: add your own language with one small class, or register it under a code.
- **Modern codebase**: new `Himelali\` namespace, SOLID structure, PHP 7.0 - 8.5, strict types,
  PSR-1 / PSR-4 / PSR-12, and a test suite for every language.

Version 2.0.0 has breaking changes, see [Upgrading from 1.x](#upgrading-from-1x).

## Features

- 14 languages with real grammar: plural and dual forms, gender, irregular numbers, compound words
- Native numbering systems and the international million / billion / trillion system
- Numbers up to **999 trillion** (15 integer digits) with any number of decimals
- Native digits and decimal separators (`৫২৭.৫৬`, `٥٢٧٫٥٦`)
- Accepts int, float, numeric string or any object with `__toString()`
- Clear exceptions for every error, one base class to catch them all
- PHP **7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5**, 32-bit and 64-bit
- No dependencies, works with plain PHP, Laravel or any framework

## Installation

```bash
composer require himelali/in-words
```

Requires PHP 7.0 or newer.

## How to use

### 1. Convert a number

```php
use Himelali\InWord;

$inWord = new InWord('en');

echo $inWord->setNumber(215245)->getWord(); // two hundred fifteen thousand two hundred forty-five
echo $inWord->toWords(1250);                // one thousand two hundred fifty (shortcut)
```

`setNumber()` stores the number, so you can read it in several ways:

```php
$inWord = new InWord('bn');
$inWord->setNumber(527.56);

echo $inWord->getWord();   // পাঁচ শত সাতাশ দশমিক পাঁচ ছয়
echo $inWord->getNumber(); // ৫২৭.৫৬
```

### 2. Choose a language

Pass the language code to the constructor, or change it later with `setLanguage()`.
Codes are not case sensitive and accept `-` or `_` (`en_IN`, `en-in`).

```php
$inWord = new InWord('hi');
echo $inWord->toWords(45);                      // पैंतालीस

echo $inWord->setLanguage('ar')->toWords(2000); // ألفان
echo $inWord->setLanguage('ja')->toWords(2000); // 二千

print_r($inWord->getSupportedLanguages());     // ['en' => 'English', 'en_IN' => 'English (India)', ...]
```

### 3. Switch to million / billion / trillion

`setInternational(true)` (or `true` as the second constructor argument) uses the international system,
`setInternational(false)` goes back to the language's native one:

```php
$inWord = new InWord('bn');

echo $inWord->toWords(2500000);                           // পঁচিশ লক্ষ
echo $inWord->setInternational(true)->toWords(2500000);   // দুই মিলিয়ন পাঁচ শত হাজার
echo $inWord->setInternational(false)->toWords(2500000);  // পঁচিশ লক্ষ

echo (new InWord('hi', true))->toWords(1000000);          // एक मिलियन
echo (new InWord('en_IN', true))->toWords(1000000000);    // one billion
```

The switch changes the words of `bn`, `hi`, `ur` and `en_IN`, which count in lakh and crore natively.
The other languages already count in millions, so both settings give the same words.

### 4. Decimals

The decimal part is read digit by digit. Pass decimals as **strings** to keep trailing zeros:

```php
$inWord = new InWord('en');

echo $inWord->toWords('527.50');   // five hundred twenty-seven point five zero
echo $inWord->toWords(527.50);     // five hundred twenty-seven point five (a float has no trailing zero)
echo $inWord->toWords(0.1 + 0.2);  // zero point three
```

Floats are rounded to 15 significant digits, so they read the same on every PHP version and locale.

### 5. Native digits

`getNumber()` writes the number with the language's own digits and decimal separator:

```php
echo (new InWord('bn'))->setNumber(527.56)->getNumber(); // ৫২৭.৫৬
echo (new InWord('hi'))->setNumber(527.56)->getNumber(); // ५२७.५६
echo (new InWord('ur'))->setNumber(527.56)->getNumber(); // ۵۲۷.۵۶
echo (new InWord('ar'))->setNumber(527.56)->getNumber(); // ٥٢٧٫٥٦
```

Languages that use the digits 0 - 9 return the number unchanged.

### 6. Accepted input

| Input | Example | Notes |
|---|---|---|
| int | `215245` | |
| float | `527.56` | Rounded to 15 significant digits |
| numeric string | `'527.50'`, `' 007 '` | Spaces are trimmed, leading zeros ignored, trailing zeros kept |
| stringable object | any object with `__toString()` | |

Negative numbers, scientific notation (`'1e5'`), `NAN`, `INF` and numbers with more than 15 integer digits
are rejected with an exception, see [Error handling](#error-handling).

### 7. Amount in words (invoices, cheques, receipts)

Read the whole amount and the cents separately and add your currency:

```php
use Himelali\InWord;

function amountInWords(InWord $inWord, $amount, $currency, $subunit, $and)
{
    list($whole, $cents) = explode('.', number_format((float) $amount, 2, '.', ''));

    $words = $inWord->toWords($whole) . ' ' . $currency;
    if ((int) $cents > 0) {
        $words .= ' ' . $and . ' ' . $inWord->toWords($cents) . ' ' . $subunit;
    }

    return $words;
}

echo amountInWords(new InWord('en'), 1250.50, 'taka', 'paisa', 'and'); // one thousand two hundred fifty taka and fifty paisa
echo amountInWords(new InWord('bn'), 1250.50, 'টাকা', 'পয়সা', 'ও');      // এক হাজার দুই শত পঞ্চাশ টাকা ও পঞ্চাশ পয়সা
```

Use `ucfirst()` for a capital first letter: `One thousand two hundred fifty`.

### 8. Laravel

The package needs no service provider. Use it anywhere, or let Laravel inject it:

```php
use Himelali\InWord;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice, InWord $inWord)
    {
        return view('invoice', [
            'invoice' => $invoice,
            'totalInWords' => $inWord->setLanguage('bn')->toWords($invoice->total),
        ]);
    }
}
```

Or directly in a Blade view:

```blade
{{ (new \Himelali\InWord('bn'))->toWords($invoice->total) }}
```

To use one configuration everywhere, bind it in `AppServiceProvider::register()`:

```php
$this->app->bind(\Himelali\InWord::class, function () {
    return new \Himelali\InWord('bn', false);
});
```

## Supported languages

| Code | Language | Native name | Native system | Switch changes words | Native digits | Decimal word |
|---|---|---|---|---|---|---|
| `en` | English | English | million / billion / trillion | no | - | point |
| `en_IN` | English (India) | English | lakh / crore | **yes** | - | point |
| `zh` | Chinese (Simplified) | 中文 | 万 / 亿 | no | - | 点 |
| `hi` | Hindi | हिन्दी | lakh / crore (लाख / करोड़) | **yes** | ०-९ | दशमलव |
| `es` | Spanish | Español | mil millones / billón | no | - | coma |
| `ar` | Arabic | العربية | million / milliard (مليون / مليار) | no | ٠-٩ and ٫ | فاصلة |
| `fr` | French | Français | million / milliard / billion | no | - | virgule |
| `bn` | Bengali | বাংলা | lakh / crore (লক্ষ / কোটি) | **yes** | ০-৯ | দশমিক |
| `pt` | Portuguese (Brazil) | Português | milhão / bilhão / trilhão | no | - | vírgula |
| `ru` | Russian | Русский | миллион / миллиард / триллион | no | - | запятая |
| `ur` | Urdu | اردو | lakh / crore (لاکھ / کروڑ) | **yes** | ۰-۹ | اعشاریہ |
| `id` | Indonesian | Bahasa Indonesia | juta / miliar / triliun | no | - | koma |
| `de` | German | Deutsch | Million / Milliarde / Billion | no | - | Komma |
| `ja` | Japanese | 日本語 | 万 / 億 / 兆 | no | - | 点 |

Billion and trillion are translated the way each language says them: French and German use the long scale
(`milliard` / `Milliarde` = 10⁹, `billion` / `Billion` = 10¹²) and Spanish says `mil millones` (10⁹) and `billón` (10¹²).
Chinese and Japanese express millions in their own system (`一百万`, `百万` = one million).

Click a language to see its features and examples.

<details>
<summary><b>English</b> (<code>en</code>) and <b>English (India)</b> (<code>en_IN</code>)</summary>

- `en` uses million / billion / trillion, `en_IN` uses lakh / crore (and million / billion with the switch)
- Hyphenated tens (`forty-five`), no plural "s" on scale words

| Number | `en` | `en_IN` |
|---|---|---|
| 21 | twenty-one | twenty-one |
| 101 | one hundred one | one hundred one |
| 215,245 | two hundred fifteen thousand two hundred forty-five | two lakh fifteen thousand two hundred forty-five |
| 2,500,000 | two million five hundred thousand | twenty-five lakh |
| 10,000,000 | ten million | one crore |
| 1,000,000,000 | one billion | one hundred crore |
| 527.56 | five hundred twenty-seven point five six | five hundred twenty-seven point five six |

</details>

<details>
<summary><b>Bengali</b> (<code>bn</code>)</summary>

- Lakh / crore system, all 100 words from 0 to 99 (`পঁয়তাল্লিশ`, `নিরানব্বই`)
- Bengali digits with `getNumber()`: `৫২৭.৫৬`
- Unicode NFC normalized text, so it matches text from users and databases

| Number | Native | International |
|---|---|---|
| 45 | পঁয়তাল্লিশ | পঁয়তাল্লিশ |
| 101 | এক শত এক | এক শত এক |
| 215,245 | দুই লক্ষ পনেরো হাজার দুই শত পঁয়তাল্লিশ | দুই শত পনেরো হাজার দুই শত পঁয়তাল্লিশ |
| 2,500,000 | পঁচিশ লক্ষ | দুই মিলিয়ন পাঁচ শত হাজার |
| 10,000,000 | এক কোটি | দশ মিলিয়ন |
| 527.56 | পাঁচ শত সাতাশ দশমিক পাঁচ ছয় | পাঁচ শত সাতাশ দশমিক পাঁচ ছয় |

</details>

<details>
<summary><b>Hindi</b> (<code>hi</code>)</summary>

- Lakh / crore system, all 100 words from 0 to 99 (`पैंतालीस`, `निन्यानबे`)
- Devanagari digits with `getNumber()`: `५२७.५६`

| Number | Native | International |
|---|---|---|
| 45 | पैंतालीस | पैंतालीस |
| 101 | एक सौ एक | एक सौ एक |
| 215,245 | दो लाख पंद्रह हज़ार दो सौ पैंतालीस | दो सौ पंद्रह हज़ार दो सौ पैंतालीस |
| 2,500,000 | पच्चीस लाख | दो मिलियन पाँच सौ हज़ार |
| 10,000,000 | एक करोड़ | दस मिलियन |
| 527.56 | पाँच सौ सत्ताईस दशमलव पाँच छह | पाँच सौ सत्ताईस दशमलव पाँच छह |

</details>

<details>
<summary><b>Urdu</b> (<code>ur</code>)</summary>

- Lakh / crore system, all 100 words from 0 to 99
- Urdu digits with `getNumber()`: `۵۲۷.۵۶`

| Number | Native | International |
|---|---|---|
| 45 | پینتالیس | پینتالیس |
| 101 | ایک سو ایک | ایک سو ایک |
| 215,245 | دو لاکھ پندرہ ہزار دو سو پینتالیس | دو سو پندرہ ہزار دو سو پینتالیس |
| 2,500,000 | پچیس لاکھ | دو ملین پانچ سو ہزار |
| 10,000,000 | ایک کروڑ | دس ملین |
| 527.56 | پانچ سو ستائیس اعشاریہ پانچ چھ | پانچ سو ستائیس اعشاریہ پانچ چھ |

</details>

<details>
<summary><b>Arabic</b> (<code>ar</code>)</summary>

- Unit before tens joined with `و` (`واحد وعشرون`)
- Singular, dual, plural (3 - 10) and accusative (11 - 99) forms of thousand, million, billion and trillion
- Construct form `مائتا ألف` (200,000)
- Arabic-Indic digits and decimal separator with `getNumber()`: `٥٢٧٫٥٦`

| Number | Words |
|---|---|
| 21 | واحد وعشرون |
| 245 | مائتان وخمسة وأربعون |
| 2,000 | ألفان |
| 3,000 | ثلاثة آلاف |
| 11,000 | أحد عشر ألفاً |
| 200,000 | مائتا ألف |
| 215,245 | مائتان وخمسة عشر ألفاً ومائتان وخمسة وأربعون |
| 527.56 | خمسمائة وسبعة وعشرون فاصلة خمسة ستة |

</details>

<details>
<summary><b>Chinese</b> (<code>zh</code>)</summary>

- Myriad system: 万 (10⁴) and 亿 (10⁸)
- `零` for skipped places, read once (`一千零一十`, `一亿零一万`)
- Leading `一十` read as `十` (`十五`, `十五亿`)

| Number | Words |
|---|---|
| 15 | 十五 |
| 101 | 一百零一 |
| 1,010 | 一千零一十 |
| 215,245 | 二十一万五千二百四十五 |
| 100,010,000 | 一亿零一万 |
| 1,500,000,000 | 十五亿 |
| 527.56 | 五百二十七点五六 |

</details>

<details>
<summary><b>Japanese</b> (<code>ja</code>)</summary>

- Myriad system: 万 (10⁴), 億 (10⁸) and 兆 (10¹²)
- No `一` before 十, 百 and 千 (`十`, `百一`, `千`), but `一千` inside larger numbers (`二万一千`, `一千万`)

| Number | Words |
|---|---|
| 10 | 十 |
| 101 | 百一 |
| 1,000 | 千 |
| 21,000 | 二万一千 |
| 10,000,000 | 一千万 |
| 100,000,000 | 一億 |
| 1,000,000,000,000 | 一兆 |

</details>

<details>
<summary><b>Spanish</b> (<code>es</code>)</summary>

- `cien` / `ciento`, irregular `veintiuno` ... `veintinueve`
- Shortened forms before nouns: `veintiún mil`, `un millón`, `veintiún millones`
- Long scale: `mil millones` (10⁹) and `billón` (10¹²)

| Number | Words |
|---|---|
| 21 | veintiuno |
| 100 | cien |
| 101 | ciento uno |
| 21,000 | veintiún mil |
| 1,000,000 | un millón |
| 21,000,000 | veintiún millones |
| 2,500,000,000 | dos mil quinientos millones |
| 1,000,000,000,000 | un billón |

</details>

<details>
<summary><b>French</b> (<code>fr</code>)</summary>

- `vingt et un`, `soixante et onze`, `soixante-dix`, `quatre-vingts`, `quatre-vingt-dix`
- Plural `cents` / `vingts` only at the end or before million (`deux cents`, `deux cent un`, `quatre-vingt mille`)
- Invariable `mille`, plural `millions` / `milliards`

| Number | Words |
|---|---|
| 21 | vingt et un |
| 71 | soixante et onze |
| 80 | quatre-vingts |
| 81 | quatre-vingt-un |
| 99 | quatre-vingt-dix-neuf |
| 200 | deux cents |
| 201 | deux cent un |
| 80,000 | quatre-vingt mille |
| 200,000,000 | deux cents millions |
| 1,000,000,000 | un milliard |

</details>

<details>
<summary><b>German</b> (<code>de</code>)</summary>

- Numbers below a million are one word (`zweihundertfünfzehntausendzweihundertfünfundvierzig`)
- Unit before tens (`einundzwanzig`), `eins` / `ein` / `eine` forms
- `eine Million`, `zwei Millionen`, `Milliarde` (10⁹), `Billion` (10¹²)

| Number | Words |
|---|---|
| 21 | einundzwanzig |
| 101 | einhunderteins |
| 1,001 | eintausendeins |
| 215,245 | zweihundertfünfzehntausendzweihundertfünfundvierzig |
| 1,000,000 | eine Million |
| 2,500,000 | zwei Millionen fünfhunderttausend |
| 1,000,000,000 | eine Milliarde |

</details>

<details>
<summary><b>Portuguese</b> (<code>pt</code>, Brazil)</summary>

- `cem` / `cento`, `e` between hundreds, tens and units (`duzentos e quarenta e cinco`)
- `e` before the last group when it is below 100 or a round hundred (`mil e cem`, `um milhão e duzentos mil`)
- Short scale: `bilhão` (10⁹), `trilhão` (10¹²)

| Number | Words |
|---|---|
| 21 | vinte e um |
| 100 | cem |
| 101 | cento e um |
| 1,100 | mil e cem |
| 1,250 | mil duzentos e cinquenta |
| 1,200,000 | um milhão e duzentos mil |
| 1,000,000,000 | um bilhão |

</details>

<details>
<summary><b>Russian</b> (<code>ru</code>)</summary>

- Feminine thousand: `одна тысяча`, `две тысячи`
- Three plural forms: `тысяча` / `тысячи` / `тысяч`, `миллион` / `миллиона` / `миллионов`

| Number | Words |
|---|---|
| 21 | двадцать один |
| 1,000 | одна тысяча |
| 2,000 | две тысячи |
| 5,000 | пять тысяч |
| 21,000 | двадцать одна тысяча |
| 2,000,000 | два миллиона |
| 5,000,000 | пять миллионов |

</details>

<details>
<summary><b>Indonesian</b> (<code>id</code>)</summary>

- `se-` for one ten, hundred and thousand (`sepuluh`, `seratus`, `seribu`)
- `belas` for 11 - 19, `puluh` for tens

| Number | Words |
|---|---|
| 11 | sebelas |
| 12 | dua belas |
| 21 | dua puluh satu |
| 100 | seratus |
| 1,000 | seribu |
| 2,000 | dua ribu |
| 1,000,000 | satu juta |

</details>

## API reference

| Method | Returns | Description |
|---|---|---|
| `new InWord($language = 'en', $international = false)` | | Language code or `LanguageInterface` instance |
| `setLanguage($language)` | `InWord` | Change the language (`setConfig()` is a 1.x compatible alias) |
| `getLanguage()` | `LanguageInterface` | The active language |
| `getSupportedLanguages()` | `array` | `code => name` of all languages |
| `setInternational($enabled = true)` | `InWord` | `true`: million / billion / trillion, `false`: native system |
| `isInternational()` | `bool` | Current setting |
| `setNumber($number)` | `InWord` | int, float, numeric string or stringable object |
| `getWord()` | `string` | The number in words |
| `getNumber()` | `string` | The number with native digits |
| `toWords($number)` | `string` | Shortcut for `setNumber($number)->getWord()` |
| `getVersion()` | `string` | Package version |

All setters return the same instance, so calls can be chained.

## Error handling

Every exception extends `Himelali\Exception`, so one `catch` handles them all, or catch a specific one:

| Exception (`Himelali\Exceptions\`) | Thrown when |
|---|---|
| `InvalidNumberException` | The value is not a number: wrong type, `'abc'`, `'1e5'`, `NAN`, `INF` |
| `NumberOutOfRangeException` | Negative, or more than 15 integer digits (extends `InvalidNumberException`) |
| `UnsupportedLanguageException` | The language code is not registered |
| `InvalidOptionException` | `setInternational()` gets a non-boolean, or an invalid dependency is passed to the constructor |
| `InvalidLanguageException` | A custom language or its numbering system is invalid |
| `NumberNotSetException` | `getWord()` / `getNumber()` is called before `setNumber()` |
| `ConversionException` | A language fails while converting (e.g. a custom language missing a word). PHP warnings and errors are converted too, and the original is available via `getPrevious()` |

```php
use Himelali\Exception;
use Himelali\Exceptions\InvalidNumberException;
use Himelali\InWord;

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

Useful properties: `$words` (0 - n), `$tens`, `$hundred`, `$scales` (a word or `[singular, plural]`), `$point`,
`$digits` (native digits), `$separator`. Useful methods to override: `belowHundred()`, `hundreds()`, `scale()`,
`pluralForm()`, `nativeSystem()` (e.g. `ScaleSystem::indian()` for lakh / crore).

## Upgrading from 1.x

- Requires PHP 7.0 or newer.
- The namespace changed from `Himel\Web` to `Himelali`: use `Himelali\InWord` instead of `Himel\Web\InWord`.
- `en` now uses million / billion / trillion. Use `en_IN` for lakh / crore (spelled `lakh`, previously `lac`).
- English no longer adds a plural "s" (`two hundred`, not `two hundreds`) and hyphenates tens (`forty-five`).
- Invalid input throws exceptions instead of calling `die()`. `getWord()` without a number throws `NumberNotSetException`.
- The unused month and weekday arrays of the old `src/lang` files were removed.

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

## Testing and coding standard

```bash
composer test   # run the test suite
composer lint   # check PSR-12 with PHP_CodeSniffer
composer fix    # fix PSR-12 violations automatically
```

The test suite has no dependencies and runs on every supported PHP version (`php tests/run.php`).
It is verified on PHP 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5 (64-bit) and on 32-bit builds.

The code follows PSR-1, PSR-4 and PSR-12. Class constants are declared without a visibility keyword:
PSR-12 requires `public const` only when the minimum PHP version is 7.1 or later, and it is a syntax error on PHP 7.0.

## License

The MIT License (MIT). See [LICENSE](LICENSE).
