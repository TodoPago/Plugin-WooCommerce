<?php

namespace TodoPago\Core\Exception;

class ErrorType extends \TodoPago\Core\Exception\ExceptionBase
{

    public function __construct($message, $segmento, $code = 0, Throwable $previous = null)
    {
        $message = "Error de tipo de dato. " . $message;
        parent::__construct($message, $segmento, $code, $previous);
    }

}