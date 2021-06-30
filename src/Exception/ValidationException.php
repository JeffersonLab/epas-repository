<?php


namespace Jlab\EpasRepository\Exception;


class ValidationException extends \RuntimeException {

    public $errors;

    public function __construct($message = "", array $errors)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}

