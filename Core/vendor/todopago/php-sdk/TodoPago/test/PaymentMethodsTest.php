<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class PaymentMethodsTest extends TestCase {

    use \phpmock\phpunit\PHPMock;

    public function testDiscoverOk() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(PaymentMethodsDataProvider::getDiscoverOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->discoverPaymentMethods();

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey("ID",$response["PaymentMethod"][0]);
    }

    public function testDiscoverFail() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(PaymentMethodsDataProvider::getDiscoverFailResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->discoverPaymentMethods();

        $this->assertEmpty($response);
    } 

    
    public function testGetAllPaymentMethodsOk() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(PaymentMethodsDataProvider::getAllPaymentMethodsOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->getAllPaymentMethods(array("MERCHANT" => 35));

        $this->assertNotEmpty($response);
        $this->assertArrayHasKey("PaymentMethodsCollection",$response);
        $this->assertArrayHasKey("PaymentMethodBanksCollection",$response);
        $this->assertArrayHasKey("BanksCollection",$response);
    }

    public function testGetAllPaymentMethodsFail() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(null);

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $response = $sdk->getAllPaymentMethods(array("MERCHANT" => 35));
        $this->assertFalse($response);
    }
}
