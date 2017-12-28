<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class OperationsTest extends TestCase {

    use \phpmock\phpunit\PHPMock;

    public function testGetStatusOk() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(OperationsDataProvider::getStatusOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = OperationsDataProvider::getStatusOptions();
        $response = $sdk->getStatus($params);

        $this->assertEquals(-1, $response["Operations"]['RESULTCODE']);
    }

    public function testGetStatusFail() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(OperationsDataProvider::getStatusFailResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = OperationsDataProvider::getStatusOptions();
        $response = $sdk->getStatus($params);

        $this->assertEmpty($response);
    }


    public function testGetStatusEmpty() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(OperationsDataProvider::getStatusFailResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(502);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = OperationsDataProvider::getStatusOptions();
        $response = $sdk->getStatus($params);

        $this->assertEmpty($response);
    }

    public function testGetByRangeDateTimeOk() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(OperationsDataProvider::getByRangeDateTimeOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = OperationsDataProvider::getByRangeDateTimeOptions();
        $response = $sdk->getByRangeDateTime($params);

        $this->assertArrayHasKey("Operations",$response);
    }

}
