<?php

namespace TodoPago;
{
    function function_exists($function)
    {
        if ($function === 'mb_convert_encoding') {
            return false;
        }
        return \function_exists($function);
    }
}


namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class SpecialConditionsTest extends TestCase {

    private $clientMock;

    use \phpmock\phpunit\PHPMock;

    public function setUp() {
        $this->clientMock = $this->getMockBuilder(\TodoPago\Test\MockClient::class)->setMethods(['__doRequest'])->getMock();
    }

    public function testSendAuthorizeRequestPayloadEncoding() {
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

    public function testSoapClient() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $class = new \ReflectionClass ($sdk);
        $method = $class->getMethod ('getClientSoap');
        $method->setAccessible(true);
        $output = $method->invoke ($sdk, "Authorize", "native");

        $this->assertInstanceOf(\SoapClient::class,$output);

    }    

    public function testCurlClient() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $class = new \ReflectionClass ($sdk);
        $method = $class->getMethod ('getClientSoap');
        $method->setAccessible(true);
        $output = $method->invoke ($sdk, "Authorize", "curl");

        $this->assertInstanceOf(\TodoPago\Client::class,$output);

    }    

}