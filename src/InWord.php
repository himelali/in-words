<?php

namespace Himel\Web;

use Exception;

class InWord
{

    const VERSION = '1.0.0';
    private $languages = array('bn');
    private $configs = [];

    public function __construct($language = 'bn') {
        if(!in_array($language, $this->languages))
            throw new Exception("$language is not supported language");
        $this->configs = $this->getConfigs($language);
    }

    private function getConfigs($language) {
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

    public function getNumber($num) {
        if(!$this->isValid($num))
            return false;
        return strtr($num,$this->configs['numbers']);
    }

}