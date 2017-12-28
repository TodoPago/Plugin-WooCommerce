<?php

namespace TodoPago\Core\Utils;

use TodoPago\Core\Exception\ErrorType\TypeBool;
use TodoPago\Core\Exception\ErrorValue;

class CustomValidator
{
    protected $componente;

    public function __construct($componente)
    {
        $this->setComponente($componente);
    }

    public function validateRegexQuiero($elemento, $segmento, array $regexs)
    {
        foreach ($regexs as $regex => $queSeEsperaba) {
            if (!preg_match($regex, $elemento)) {
                throw new ErrorValue($elemento, $segmento, $this->getComponente(), $queSeEsperaba);
            }
        }
        return $elemento;
    }

    public function validateRegexNoQuiero($elemento, $segmento, array $regexs)
    {
        foreach ($regexs as $regex => $queSeEsperaba) {
            if (preg_match($regex, $elemento)) {
                throw new ErrorValue($elemento, $segmento, $this->getComponente(), $queSeEsperaba);
            }
        }
        return $elemento;
    }

    public function validateValue($elemento, $segmento, $elementosEsperados)
    {
        if (gettype($elementosEsperados) == 'array') {
            if (in_array($elemento, $elementosEsperados))
                $respuesta = $elemento;
            else
                throw new ErrorValue($elemento, $segmento, $this->getComponente(), $elementosEsperados);
        } else {
            if ($elemento = $elementosEsperados)
                $respuesta = $elemento;
            else
                throw new ErrorValue($elemento, $segmento, $this->getComponente(), $elementosEsperados);
        }
        return $respuesta;
    }


    public function validateNumber($elemento, $componente)
    {
        $respuesta = NULL;
        if (is_null($elemento) || (gettype($elemento) === 'integer' || gettype($elemento) === 'double'))
            $respuesta = $elemento;
        else
            throw new \TodoPago\Core\Exception\ErrorType\TypeNumber($elemento, $componente);
        return $respuesta;
    }

    public function validateString($elemento, $segmento)
    {
        if (is_string($elemento) || empty($elemento) || is_null($elemento))
            $reply = $elemento;
        else
            throw new \TodoPago\Core\Exception\ErrorType\TypeString($elemento, $segmento, $this->getComponente());
        return $reply;
    }


    public function validateEmpty($elemento, $segmento)
    {
        if (!empty($elemento))
            $reply = $elemento;
        else
            throw new ErrorValue($elemento, $segmento, $this->getComponente(), 'Que no esté vacío');
        return $reply;

    }

    public function validateBinary($elemento, $segmento)
    {
        if (is_string($elemento) && !preg_match('/([01])|(true)\b|(false)\b|(si)\b|(no)\b/', $elemento))
            throw new TypeBool($elemento, $segmento);
        
        $elemento= strtolower($elemento);
        if ($elemento == 'true' || $elemento=='si')
            return true;
        elseif
        ($elemento == 'false' || $elemento=='no')
            return false;
        else
            $elementoLocal = (bool)$elemento;
        if ($elementoLocal)
            return true;
        else
            return false;
    }

    /**
     * @return mixed
     */
    public function getComponente()
    {
        return $this->componente;
    }

    /**
     * @param mixed $componente
     */
    public function setComponente($componente)
    {
        $this->componente = $componente;
    }

}
