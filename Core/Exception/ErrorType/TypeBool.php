<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 03/10/17
 * Time: 15:34
 */

namespace TodoPago\Core\Exception\ErrorType;


class TypeBool extends \TodoPago\Core\Exception\ErrorType
{
    public function __construct($elemento, $segmento, $code = 0, Throwable $previous = null)
    {
        $message = "El valor tiene que ser un booleano. Se recibió $elemento de tipo " . gettype($elemento);
        parent::__construct($message, $segmento, $code, $previous);
    }
}