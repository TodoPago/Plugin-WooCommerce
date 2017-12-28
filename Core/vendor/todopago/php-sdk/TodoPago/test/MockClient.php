<?php

namespace TodoPago\Test;

class MockClient extends \SoapClient
{
    private $parameters = array();

    public function setParameters($params) {
        $this->parameters = $params;
        $params = array_merge($params,array("trace" => 1));
        parent::__construct("../lib/Authorize.wsdl",$params);
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function __construct() {
      
    }
}
