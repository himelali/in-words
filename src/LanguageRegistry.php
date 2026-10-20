<?php

declare(strict_types=1);

namespace Himelali;

use Himelali\Contracts\LanguageInterface;
use Himelali\Exceptions\InvalidLanguageException;
use Himelali\Exceptions\UnsupportedLanguageException;
use Himelali\Languages\Arabic;
use Himelali\Languages\Bengali;
use Himelali\Languages\Chinese;
use Himelali\Languages\English;
use Himelali\Languages\EnglishIndia;
use Himelali\Languages\French;
use Himelali\Languages\German;
use Himelali\Languages\Hindi;
use Himelali\Languages\Indonesian;
use Himelali\Languages\Japanese;
use Himelali\Languages\Portuguese;
use Himelali\Languages\Russian;
use Himelali\Languages\Spanish;
use Himelali\Languages\Urdu;

/**
 * Maps language codes to language classes. Register your own language to extend the package.
 */
class LanguageRegistry
{
    /**
     * Built-in languages, most spoken first.
     */
    const DEFAULTS = [
        'en' => English::class,
        'en_IN' => EnglishIndia::class,
        'zh' => Chinese::class,
        'hi' => Hindi::class,
        'es' => Spanish::class,
        'ar' => Arabic::class,
        'fr' => French::class,
        'bn' => Bengali::class,
        'pt' => Portuguese::class,
        'ru' => Russian::class,
        'ur' => Urdu::class,
        'id' => Indonesian::class,
        'de' => German::class,
        'ja' => Japanese::class,
    ];

    /**
     * Normalized code => class name.
     *
     * @var string[]
     */
    private $languages = [];

    /**
     * @throws InvalidLanguageException
     */
    public function __construct()
    {
        foreach (self::DEFAULTS as $code => $class) {
            $this->register($code, $class);
        }
    }

    /**
     * Adds or replaces a language.
     *
     * Parameters are validated here (not type-hinted) so any wrong value raises an InvalidLanguageException.
     *
     * @param string $code  e.g. "tr" or "pt_PT", case and "-" / "_" do not matter.
     * @param string $class Class name implementing LanguageInterface.
     * @throws InvalidLanguageException
     */
    public function register($code, $class): self
    {
        if (!is_string($code) || trim($code) === '') {
            throw InvalidLanguageException::invalidCode($code);
        }
        if (!is_string($class) || !class_exists($class) || !is_subclass_of($class, LanguageInterface::class)) {
            throw InvalidLanguageException::invalidClass($class, LanguageInterface::class);
        }
        $this->languages[$this->normalize($code)] = $class;

        return $this;
    }

    /**
     * @param mixed $code
     */
    public function has($code): bool
    {
        return is_string($code) && isset($this->languages[$this->normalize($code)]);
    }

    /**
     * @param mixed $code
     * @throws UnsupportedLanguageException
     */
    public function make($code): LanguageInterface
    {
        if (!$this->has($code)) {
            throw UnsupportedLanguageException::language($code, array_keys($this->all()));
        }
        $class = $this->languages[$this->normalize($code)];

        return new $class();
    }

    /**
     * Supported languages as code => name.
     *
     * @return string[]
     */
    public function all(): array
    {
        $languages = [];
        foreach ($this->languages as $class) {
            $language = new $class();
            $languages[$language->code()] = $language->name();
        }

        return $languages;
    }

    /**
     * @param string $code
     */
    private function normalize($code): string
    {
        return strtolower(str_replace('-', '_', trim($code)));
    }
}
