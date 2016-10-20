<?php

namespace TodoPago\Exception\Data;

class EmptyFieldException extends \Exception {
	
	protected $field;
	
    public function __construct($field = null, $code = 0, Exception $previous = null) {
		if($field == null) {
			$message = "Falta completar un campo requerido.";
		} else {
			$message = "El campo " . $field . " es requerido.";
		}
		parent::__construct($message, $code, $previous);
    }
	
}