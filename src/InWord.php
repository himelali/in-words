<?php


namespace Himel\Web;


class InWord {

    const VERSION = '1.0.0';
    protected $languages = array('bn','en');
    protected $configs = [];
    protected $number = null;
    protected $date = null;

    /**
     * InWord constructor.
     * @param string $language
     */
    public function __construct($language = 'en') {
        if (in_array($language, $this->languages))
            $this->configs = $this->getConfigs($language);
    }

    /**
     * @return string
     */
    public function getVersion() {
        return self::VERSION;
    }

    /**
     * @param $language
     * @return mixed
     */
    protected function getConfigs($language) {
        return include "lang/$language.php";
    }

    /**
     * @param $num
     * @return bool
     * @throws Exception
     */
    private function isValid($num)
    {
        if(!is_numeric($num) || $num>999999999999999 || strpos($num, 'E') !== false)
            throw new Exception('Number is not correct');
        return true;
    }

    /**
     * @param $language
     */
    public function setConfig($language) {
        $this->configs = $this->getConfigs($language);
    }

    /**
     * @param $num
     */
    public function setNumber($num) {
        try {
            if ($this->isValid($num))
                $this->number = $num;
        } catch (Exception $exception) {
            die("$num is not supported");
        }
    }

    /**
     * @return string
     */
    public function getNumber() {
        return strtr($this->number,$this->configs['numbers']);
    }

    /**
     * @param $number
     * @return string
     */
    protected function makeWord($number) {
        if($number == 0) { return $this->configs['zero']; }
        if(is_float($number)) {
            $numbers = explode('.',$number);
            $word = rtrim($this->numberToWord($numbers[0]));
            if(isset($numbers[1]))
                $word .= ($this->configs['divider'].$this->numberToDecimalWord((string)$numbers[1]));
            return $word;
        } return $this->numberToWord($number);
    }

    /**
     * @return string
     */
    public function getWord() {
        return $this->makeWord($this->number);
    }

    /**
     * @param $num
     * @return string
     */
    protected function numberToDecimalWord($num) {
        $decimals = str_split($num);
        $word = '';
        foreach ($decimals as $key => $decimal) {
            $word .= $this->configs['words'][$decimal].' ';
        } return rtrim($word);
    }

    /**
     * @param $num
     * @return string
     */
    protected function numberToWord($num) {
        $word = '';
        $million_10 = (int) ($num / 10000000);
        if($million_10 != 0){
            if($million_10 > 99) {
                $word .= $this->makePlural($this->makeWord($million_10).$this->configs['crore']);
            } else{
                $word .= $this->makePlural($this->configs['words'][$million_10].$this->configs['crore']);
            }
        }

        $million_10_div = $num % 10000000;
        $lac = (int) ($million_10_div/100000);
        if($lac > 0){
            $word .= $this->makePlural($this->configs['words'][$lac].$this->configs['lac']);
        }

        $lac_div = $million_10_div % 100000;
        $thousand = (int) ($lac_div / 1000);
        if($thousand > 0){
            $word .= $this->makePlural($this->configs['words'][$thousand].$this->configs['thousand']);
        }

        $thousand_div = $lac_div % 1000;
        $hundred = (int) ($thousand_div / 100);
        if($hundred > 0){
            $word .= ($this->configs['words'][$hundred].$this->configs['hundred']);
        }
        $hundred_div = (int) ($thousand_div%100);
        if($hundred_div > 0){
            $word .= $this->configs['words'][$hundred_div];
        } return rtrim($word);
    }

    private function makePlural($text) {
        $words = explode(' ',$text);
        if($words[0] == 'one') {
            return $text;
        } return rtrim($text).'s ';
    }

}