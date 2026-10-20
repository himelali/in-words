<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Exceptions\ConversionException;
use Himelali\Support\ScaleSystem;
use Himelali\Support\Segment;

/**
 * Spanish (Español). Uses the long scale: 10^9 is "mil millones", 10^12 is "un billón".
 */
class Spanish extends Language
{
    protected $code = 'es';
    protected $name = 'Spanish';
    protected $words = [
        'cero', 'uno', 'dos', 'tres', 'cuatro',
        'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce',
        'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve',
        'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro',
        'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve',
    ];
    protected $tens = [3 => 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    protected $tensSeparator = ' y ';
    protected $hundredWords = [
        1 => 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
        'seiscientos', 'setecientos', 'ochocientos', 'novecientos',
    ];
    protected $scales = [
        'thousand' => 'mil',
        'million' => ['millón', 'millones'],
        'billion' => ['billón', 'billones'],
    ];
    protected $point = 'coma';

    /**
     * Millions hold six digits ("dos mil quotients mill ones"), "billón" is 10^12.
     *
     * {@inheritdoc}
     */
    public function nativeSystem(): ScaleSystem
    {
        return new ScaleSystem(3, ['thousand' => 3, 'million' => 6, 'billion' => null]);
    }

    /**
     * Spanish already counts in millions, so both systems are the same.
     *
     * {@inheritdoc}
     */
    public function internationalSystem(): ScaleSystem
    {
        return $this->nativeSystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $hundreds === 1 && $rest === 0 ? 'cien' : $this->hundredWords[$hundreds];
    }

    /**
     * "mil", "veintiún mil", "un millón", "dos millones".
     *
     * {@inheritdoc}
     * @throws ConversionException
     */
    protected function scale($segment): string
    {
        if ($segment->scale() === 'thousand' && $segment->value() === 1) {
            return 'mil';
        }
        $words = preg_replace(['/veintiuno$/u', '/uno$/u'], ['veintiún', 'un'], $segment->words());

        return $words . ' ' . $this->scaleWord($segment->scale(), $segment->value());
    }
}
