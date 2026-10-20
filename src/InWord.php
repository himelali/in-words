<?php

declare(strict_types=1);

namespace Himelali;

use ErrorException;
use Himelali\Contracts\ConverterInterface;
use Himelali\Contracts\DigitsInterface;
use Himelali\Contracts\LanguageInterface;
use Himelali\Exceptions\InvalidNumberException;
use Himelali\Exceptions\InvalidOptionException;
use Himelali\Exceptions\NumberNotSetException;
use Himelali\Exceptions\UnsupportedLanguageException;
use Himelali\Support\DigitTransliterator;
use Himelali\Support\ErrorGuard;
use Himelali\Support\Number;
use Himelali\Support\NumberConverter;
use Himelali\Support\NumberParser;

/**
 * Converts numbers to words and native digits.
 *
 *     $inWord = new InWord('bn');
 *     echo $inWord->setNumber(215245)->getWord(); // দুই লক্ষ পনেরো হাজার দুই শত পঁয়তাল্লিশ
 *
 * Methods that take user input are validated instead of type-hinted, so a wrong value always
 * raises a package exception (never a TypeError or a silent type coercion).
 */
class InWord
{
    const VERSION = '2.0.1';

    /**
     * @var LanguageRegistry
     */
    private $registry;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var NumberParser
     */
    private $parser;

    /**
     * @var DigitTransliterator
     */
    private $transliterator;

    /**
     * @var LanguageInterface
     */
    private $language;

    /**
     * @var bool
     */
    private $international = false;

    /**
     * @var Number|null
     */
    private $number;

    /**
     * @param string|LanguageInterface $language      Language code or instance, default "en".
     * @param bool                     $international Use million / billion / trillion instead of the native system.
     * @param LanguageRegistry|null    $registry      Custom registry with extra languages.
     * @param ConverterInterface|null  $converter     Custom conversion algorithm.
     * @throws Exception
     */
    public function __construct($language = 'en', $international = false, $registry = null, $converter = null)
    {
        if ($registry !== null && !$registry instanceof LanguageRegistry) {
            throw InvalidOptionException::type('registry', 'an instance of ' . LanguageRegistry::class, $registry);
        }
        if ($converter !== null && !$converter instanceof ConverterInterface) {
            throw InvalidOptionException::type('converter', 'an instance of ' . ConverterInterface::class, $converter);
        }

        $this->registry = $registry ?? new LanguageRegistry();
        $this->converter = $converter ?? new NumberConverter();
        $this->parser = new NumberParser();
        $this->transliterator = new DigitTransliterator();

        $this->setLanguage($language);
        $this->setInternational($international);
    }

    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * @param string|LanguageInterface $language Language code (e.g. "bn", "en_IN") or instance.
     * @throws UnsupportedLanguageException
     */
    public function setLanguage($language): self
    {
        $this->language = $language instanceof LanguageInterface ? $language : $this->registry->make($language);

        return $this;
    }

    /**
     * Alias of setLanguage(), kept for version 1.x compatibility.
     *
     * @param string|LanguageInterface $language
     * @throws UnsupportedLanguageException
     */
    public function setConfig($language): self
    {
        return $this->setLanguage($language);
    }

    public function getLanguage(): LanguageInterface
    {
        return $this->language;
    }

    /**
     * Supported languages as code => name.
     *
     * @return string[]
     */
    public function getSupportedLanguages(): array
    {
        return $this->registry->all();
    }

    /**
     * true: million / billion / trillion. false: the language's native system (lakh / crore, 万 / 亿 ...).
     *
     * @param bool $enabled
     * @throws InvalidOptionException
     */
    public function setInternational($enabled = true): self
    {
        if (!is_bool($enabled)) {
            throw InvalidOptionException::type('international', 'a boolean', $enabled);
        }
        $this->international = $enabled;

        return $this;
    }

    public function isInternational(): bool
    {
        return $this->international;
    }

    /**
     * @param int|float|string $number Non-negative number, up to 15 integer digits.
     * @throws InvalidNumberException
     */
    public function setNumber($number): self
    {
        $this->number = $this->parser->parse($number);

        return $this;
    }

    /**
     * The number written with the language's native digits, e.g. "৫২৭.৫৬".
     *
     * @throws Exception|ErrorException
     */
    public function getNumber(): string
    {
        $number = $this->requireNumber();
        if (!$this->language instanceof DigitsInterface) {
            return $number->value();
        }

        return ErrorGuard::run(function () use ($number) {
            return $this->transliterator->transliterate($number, $this->language);
        });
    }

    /**
     * The number in words.
     *
     * @throws Exception|ErrorException
     */
    public function getWord(): string
    {
        $number = $this->requireNumber();

        return ErrorGuard::run(function () use ($number) {
            return $this->converter->convert($number, $this->language, $this->international);
        });
    }

    /**
     * Shortcut for setNumber($number)->getWord().
     *
     * @param int|float|string $number
     * @throws Exception|ErrorException
     */
    public function toWords($number): string
    {
        return $this->setNumber($number)->getWord();
    }

    /**
     * @throws NumberNotSetException
     */
    private function requireNumber(): Number
    {
        if ($this->number === null) {
            throw NumberNotSetException::create();
        }

        return $this->number;
    }
}
