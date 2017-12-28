<?php

namespace TodoPago\Core\Exception;


use Throwable;

class ErrorValue extends \TodoPago\Core\Exception\ExceptionBase
{
    public function __construct($elemento, $segmento, $componente, $elementosEsperados = "", $code = 0, Throwable $previous = null)
    {
        $esperado = $elementosEsperados;
        if (is_array($elementosEsperados))
            foreach ($elementosEsperados as $esperado) {
                $esperado = $esperado . "o ";
            }
        $message = "Error de valor en $segmento . Se ingresó: " . $elemento . " Se esperaba: " . $esperado . ".";
        parent::__construct($message, $componente, $code, $previous);
    }
}