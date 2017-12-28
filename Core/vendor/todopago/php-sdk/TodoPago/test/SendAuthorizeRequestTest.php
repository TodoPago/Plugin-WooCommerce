<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class SendAuthorizeRequestTest extends TestCase {

    private $clientMock;

    use \phpmock\phpunit\PHPMock;

    public function setUp() {
        $this->clientMock = $this->getMockBuilder(\TodoPago\Test\MockClient::class)->setMethods(['__doRequest'])->getMock();
    }

    public function testSendAuthorizeRequestOk() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(SendAuthorizeRequestDataProvider::sendAuthorizeRequestOkResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = SendAuthorizeRequestDataProvider::sendAuthorizeRequestOptions();
        $response = $sdk->sendAuthorizeRequest($params[0], $params[1]);

        $this->assertEquals(SendAuthorizeRequestDataProvider::sendAuthorizeRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testSendAuthorizeRequestFail() {
        $this->clientMock->expects($this->once())->method('__doRequest')->will($this->returnValue(SendAuthorizeRequestDataProvider::sendAuthorizeRequestFailResponse()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = SendAuthorizeRequestDataProvider::sendAuthorizeRequestOptions();
        $response = $sdk->sendAuthorizeRequest($params[0], $params[1]);

        $this->assertEquals(SendAuthorizeRequestDataProvider::sendAuthorizeRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }

    public function testSendAuthorizeRequest702() {
        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(SendAuthorizeRequestDataProvider::sendAuthorizeRequest702Response()));
        
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);

        $params = SendAuthorizeRequestDataProvider::sendAuthorizeRequestOptions();
        $response = $sdk->sendAuthorizeRequest($params[0], $params[1]);

        $this->assertEquals(SendAuthorizeRequestDataProvider::sendAuthorizeRequestSoapRequest(),$this->clientMock->__getLastRequest());
        $this->assertNotEquals(-1, $response['StatusCode']);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);

    }
}
