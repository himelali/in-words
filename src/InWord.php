<?php

namespace Himel\Web;

use Exception;

class InWord {

    const VERSION = '1.0.0';
    protected $languages = array('bn');
    protected $configs = [];
    protected $number = null;
    protected $date = null;

    public function __construct($language = 'bn') {
        if(!in_array($language, $this->languages))
            throw new Exception("$language is not supported language");
        $this->configs = $this->getConfigs($language);
    }

    protected function getConfigs($language) {
        return include "lang/$language.php";
    }

    protected  function isValid($num)
    {
        if(!is_numeric($num) || $num>999999999999999 || strpos($num, 'E') !== false)
            return false;
        return true;
    }

    public function setConfig($language) {
        $this->configs = $this->getConfigs($language);
    }

    public function setNumber($num) {
        if(!$this->isValid($num))
            throw new Exception("$num is not supported");
        $this->number = $num;
    }

    public function getNumber() {
        return strtr($this->number,$this->configs['numbers']);
    }

    protected function makeWord($number) {
        if($number == 0) { return 'শূন্য'; }
        if(is_float($number)){
            $numbers = explode(".", $number);
            $word = $this->numberToWord($numbers[0]);
            if(isset($decimal[1])){
                $word .= ' দশমিক '.$this->numberToDecimalWord((string)$decimal[1]);
            } return $word;
        } return $this->numberToWord($number);
    }

    public function getWord() {
        return $this->makeWord($this->number);
    }

    protected function numberToDecimalWord($num) {
        $decimals = str_split($num);
        $word = '';
        foreach ($decimals as $key => $decimal) {
            $word .= $this->configs['words'][$decimal].' ';
        } return rtrim($word);
    }

    protected function numberToWord($num) {
        $word = '';
        $million_10 = (int) ($num / 10000000);
        if($million_10 != 0){
            if($million_10 > 99) {
                $word .= $this->makeWord($million_10).' কোটি ';
            } else{
                $word .= $this->configs['words'][$million_10].' কোটি ';
            }
        }

        $million_10_div = $num % 10000000;
        $lac = (int) ($million_10_div/100000);
        if($lac > 0){
            $word .= $this->configs['words'][$lac].' লক্ষ ';
        }

        $lac_div = $million_10_div%100000;
        $thousand = (int) ($lac_div/1000);
        if($thousand > 0){
            $word .= $this->configs['words'][$thousand].' হাজার ';
        }

        $thousand_div = $lac_div%1000;
        $hundred = (int) ($thousand_div/100);
        if($hundred > 0){
            $word .= $this->configs['words'][$hundred].' শত ';
        }

        $hundred_div = (int) ($thousand_div%100);
        if($hundred_div > 0){
            $word .= $this->configs['words'][$hundred_div];
        } return rtrim($word);
    }

}