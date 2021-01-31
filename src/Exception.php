<?php


namespace Himel\Web;


class Exception extends \Exception {
    protected $details;

    public function __construct($details) {
        $this->details = $details;
        parent::__construct();
    }

    public function __toString() {
        return 'Invalid format: ' . $this->details;
    }
}