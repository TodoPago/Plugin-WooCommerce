<?php

namespace TodoPago\Core\AbstractClass;

use TodoPago\Core\Utils\CustomValidator as CustomValidator;

abstract class AbstractModel
{
    abstract function setCustomValidator(CustomValidator $customValidator);
}


?>