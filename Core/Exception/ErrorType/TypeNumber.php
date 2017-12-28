<?php

namespace TodoPago\Core\Exception\ErrorType;

class TypeNumber extends \TodoPago\Core\Exception\ErrorType
{
    public function __construct($elemento = "", $segmento = "", $message = "", $code = 0, Throwable $previous = null)
    {
        $message = "El valor tiene que ser un número. Se recibió: $elemento de tipo " . gettype($elemento);
        parent::__construct($message, $segmento , $code, $previous);
    }
}
