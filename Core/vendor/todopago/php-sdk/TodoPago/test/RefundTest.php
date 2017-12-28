<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase {

    private $clientMock;

    public function setUp() {
        $this->clientMock = $this->getMockBuilder(\TodoPago\Test\MockClient::class)->setMethods(['__doRequest'])->getMock();
    }

    public function testRefundRequestOk() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(RefundDataProvider::returnRequestOkResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);


        $params = RefundDataProvider::returnOptions();
        $response = $sdk->returnRequest($params);
        $this->assertEquals(RefundDataProvider::returnRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertEquals(2011, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testRefundRequestFail() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(RefundDataProvider::returnRequestFailResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = RefundDataProvider::returnOptions();
        $response = $sdk->returnRequest($params);

        $this->assertEquals(RefundDataProvider::returnRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(2011, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testRefundRequest702() {
        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(RefundDataProvider::returnRequest702Response()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = RefundDataProvider::returnOptions();
        $response = $sdk->returnRequest($params);

        $this->assertEquals(RefundDataProvider::returnRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(2011, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testVoidRequestOk() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(RefundDataProvider::voidRequestOkResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = RefundDataProvider::voidOptions();
        $response = $sdk->voidRequest($params);
        $this->assertEquals(RefundDataProvider::voidRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertEquals(2011, $response['StatusCode']);
        
        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testVoidRequestFail() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(RefundDataProvider::voidRequestFailResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = RefundDataProvider::voidOptions();
        $response = $sdk->voidRequest($params);

        $this->assertEquals(RefundDataProvider::voidRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(2011, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testVoidRequest702() {
        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(RefundDataProvider::voidRequest702Response()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = RefundDataProvider::voidOptions();
        $response = $sdk->voidRequest($params);

        $this->assertEquals(RefundDataProvider::voidRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(2011, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }      
}
