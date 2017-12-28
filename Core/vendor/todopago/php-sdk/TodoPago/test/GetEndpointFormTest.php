<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class GetEndpointFormTest extends TestCase {

    public function testEndpointFormFixedDevelopers() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->getEndpointForm("test");

        $this->assertEquals("https://developers.todopago.com.ar/resources/TPHybridForm-v0.1.js",$response);
    }

    public function testEndpointFormFixedProduccion() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"prod");

        $response = $sdk->getEndpointForm("prod");

        $this->assertEquals("https://forms.todopago.com.ar/resources/TPHybridForm-v0.1.js",$response);
    }


    public function testEndpointFormInstanceDevelopers() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->getEndpointForm();

        $this->assertEquals("https://developers.todopago.com.ar/resources/TPHybridForm-v0.1.js",$response);
    }

    public function testEndpointFormInstanceProduccion() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"prod");

        $response = $sdk->getEndpointForm();

        $this->assertEquals("https://forms.todopago.com.ar/resources/TPHybridForm-v0.1.js",$response);


    }

}