<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class GetAuthorizeAnswerTest extends TestCase {

    private $clientMock;

    public function setUp() {
        $this->clientMock = $this->getMockBuilder(\TodoPago\Test\MockClient::class)->setMethods(['__doRequest'])->getMock();
    }

    public function testGetAuthorizeAnswerOk() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $this->assertEquals(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);
    }

    public function testGetAuthorizeAnswerFail() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerFailResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);


        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $this->assertEquals(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testGetAuthorizeAnswer702() {
        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswer702Response()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);


        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $this->assertEquals(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }    
}
