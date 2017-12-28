<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class SdkUtilsTest extends TestCase {

    use \phpmock\phpunit\PHPMock;

    private $clientMock;

    public function setUp() {
        $this->clientMock = $this->getMockBuilder(\TodoPago\Test\MockClient::class)->setMethods(['__doRequest'])->getMock();
    }

    public function testProxy() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);
        $sdk->setProxyParameters("proxyhost",8080,"proxyuser","proxypass");

        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse()));
        
        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $parameters = $this->clientMock->getParameters();

        $this->assertArrayHasKey("proxy_host",$parameters);
        $this->assertEquals($parameters["proxy_host"],"proxyhost");

        $this->assertArrayHasKey("proxy_port",$parameters);
        $this->assertEquals($parameters["proxy_port"],8080);

        $this->assertArrayHasKey("proxy_login",$parameters);
        $this->assertEquals($parameters["proxy_login"],"proxyuser");

        $this->assertArrayHasKey("proxy_password",$parameters);
        $this->assertEquals($parameters["proxy_password"],"proxypass");        
    }

    public function testConnection() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);
        $sdk->setConnectionTimeout(1000);

        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse()));
        
        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $parameters = $this->clientMock->getParameters();

        $this->assertArrayHasKey("connection_timeout",$parameters);
        $this->assertEquals($parameters["connection_timeout"],1000);        
    }

    public function testLocalcert() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");
        $sdk->setSoapClient($this->clientMock);
        $sdk->setLocalCert("../lib/Authorize.wsdl");

        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse()));
        
        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $parameters = $this->clientMock->getParameters();

        $this->assertArrayHasKey("local_cert",$parameters);
        $this->assertEquals($parameters["local_cert"],file_get_contents("../lib/Authorize.wsdl"));        
    } 

    public function testSanitizeValue() {

        $value = \TodoPago\Sdk::sanitizeValue("&&\n\r");

        $this->assertEmpty($value);
    }

    public function testProd() {
        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"prod");
        $sdk->setSoapClient($this->clientMock);

        $this->clientMock->expects($this->any())->method('__doRequest')->will($this->returnValue(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse()));
        
        $params = GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();
        $response = $sdk->getAuthorizeAnswer($params);

        $params = $this->clientMock->getParameters();
        $params = $params["stream_context"];
        $params = stream_context_get_options($params);
        $this->assertContains("Authorization: TODOPAGO ABCDEF1234567890", $params["http"]["header"]);
    }    

    public function testCurlSoapClient() {
        $client = new \TodoPago\Client("../lib/Authorize.wsdl");

        $client->setCustomHeaders(array("http" => array("header" => "Authorization: TODOPAGO ABCDEF1234567890\r\n")));

        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $params = (object) GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();

        $response = $client->GetAuthorizeAnswer($params);

        $this->assertEquals(-1, $response->StatusCode);

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Curl error: test_error
     */
    public function testCurlSoapClientFail() {
        $client = new \TodoPago\Client("../lib/Authorize.wsdl");

        $client->setCustomHeaders(array("http" => array("header" => "Authorization: TODOPAGO ABCDEF1234567890\r\n")));

        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(false);

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_error");
        $curl_getinfo->expects($this->any())->willReturn("test_error");

        $params = (object) GetAuthorizeAnswerDataProvider::getAuthorizeAnswerOptions();

        $response = $client->GetAuthorizeAnswer($params);                
    }    
}