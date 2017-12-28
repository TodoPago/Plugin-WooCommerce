<?php

namespace TodoPago\Core\Exception;

use Throwable;

class ExceptionBase extends \Exception
{
    public function __construct($message, $componente, $code = 0, Throwable $previous = null)
    {
        $message = "\nError en: " . $componente . "\n" . $message;
        parent::__construct($message, $code, $previous);
    }

}