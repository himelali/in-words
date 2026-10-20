<?php

declare(strict_types=1);

namespace Himelali\Languages;

use Himelali\Support\Segment;

/**
 * Portuguese (Português, Brazil). Short scale: bilhão = 10^9, trilhão = 10^12.
 */
class Portuguese extends Language
{
    protected $code = 'pt';
    protected $name = 'Portuguese';
    protected $words = [
        'zero', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove',
        'dez', 'onze', 'doze', 'treze', 'catorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove',
    ];
    protected $tens = [2 => 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
    protected $tensSeparator = ' e ';
    protected $hundredSeparator = ' e ';
    protected $hundredWords = [
        1 => 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos',
        'seiscentos', 'setecentos', 'oitocentos', 'novecentos',
    ];
    protected $scales = [
        'thousand' => 'mil',
        'million' => ['milhão', 'milhões'],
        'billion' => ['bilhão', 'bilhões'],
        'trillion' => ['trilhão', 'trilhões'],
    ];
    protected $point = 'vírgula';

    /**
     * {@inheritdoc}
     */
    protected function hundreds($hundreds, $rest): string
    {
        return $hundreds === 1 && $rest === 0 ? 'cem' : $this->hundredWords[$hundreds];
    }

    /**
     * {@inheritdoc}
     */
    protected function scale($segment): string
    {
        if ($segment->scale() === 'thousand' && $segment->value() === 1) {
            return 'mil';
        }

        return parent::scale($segment);
    }

    /**
     * "e" joins the last part when it is below 100 or a round hundred:
     * "mil e cem", "um milhão e duzentos mil", but "mil duzentos e cinquenta".
     *
     * {@inheritdoc}
     */
    public function combine($segments): string
    {
        $words = [];
        $last = null;
        foreach ($segments as $segment) {
            if (!$segment->isZero()) {
                $words[] = $segment->scale() === null ? $segment->words() : $this->scale($segment);
                $last = $segment->value();
            }
        }

        if (count($words) > 1 && ($last < 100 || $last % 100 === 0)) {
            $tail = array_pop($words);
            return implode(' ', $words) . ' e ' . $tail;
        }

        return implode(' ', $words);
    }
}
