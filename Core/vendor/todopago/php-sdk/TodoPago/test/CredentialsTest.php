<?php

namespace TodoPago\Test;

require_once('../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase {

    use \phpmock\phpunit\PHPMock;

    public function testGetCredentialsOk() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(CredentialsDataProvider::getCredentialsOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = CredentialsDataProvider::getCredentialsOptions();
        $response = $sdk->getCredentials($params);

        $this->assertInstanceOf(\TodoPago\Data\User::class, $response);
        $this->assertNotEmpty($response->getMerchant());
        $this->assertNotEmpty($response->getApikey());
    }

    /**
     * @expectedException \TodoPago\Exception\Data\EmptyFieldException    
     * @expectedException \TodoPago\Exception\Data\EmptyFieldUserException
     * @expectedExceptionMessage El campo Data\User::user es requerido.
     */
    public function testGetCredentialsUserEmpty() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(CredentialsDataProvider::getCredentialsOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $user = new \TodoPago\Data\User();
        $user->setPassword("mypassword");
        $response = $sdk->getCredentials($user);
    }    

    /**
     * @expectedException \TodoPago\Exception\Data\EmptyFieldException
     * @expectedException \TodoPago\Exception\Data\EmptyFieldPasswordException
     * @expectedExceptionMessage El campo Data\User::password es requerido.
     */
    public function testGetCredentialsPasswordEmpty() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(CredentialsDataProvider::getCredentialsOkResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $user = new \TodoPago\Data\User();
        $user->setUser("midireccion@mail.com");
        $response = $sdk->getCredentials($user);
    } 

    /**
     * @expectedException \TodoPago\Exception\ConnectionException
     */
    public function testGetCredentialsFailConnection() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(null);

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = CredentialsDataProvider::getCredentialsOptions();
        $response = $sdk->getCredentials($params);
    }

    /**
     * @expectedException \TodoPago\Exception\ResponseException
     * @expectedExceptionCode 1050
     */
    public function testGetCredentialsFailUser() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(CredentialsDataProvider::getCredentialsWrongUserResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = CredentialsDataProvider::getCredentialsOptions();
        $response = $sdk->getCredentials($params);
    } 

    /**
     * @expectedException \TodoPago\Exception\ResponseException
     * @expectedExceptionCode 1055
     */
    public function testGetCredentialsFailPassword() {
        $curl_exec = $this->getFunctionMock("TodoPago", "curl_exec");
        $curl_exec->expects($this->any())->willReturn(CredentialsDataProvider::getCredentialsWrongPasswordResponse());

        $curl_getinfo = $this->getFunctionMock("TodoPago", "curl_getinfo");
        $curl_getinfo->expects($this->any())->willReturn(200);

        $sdk = new \TodoPago\Sdk(array("Authorization" => "TODOPAGO ABCDEF1234567890"),"test");

        $params = CredentialsDataProvider::getCredentialsOptions();
        $response = $sdk->getCredentials($params);
    }

    /**
     * @expectedException \TodoPago\Exception\Data\EmptyFieldException
     * @expectedExceptionMessage Falta completar un campo requerido.
     */
    public function testGetCredentialsTextEmptyFieldException() {
        throw new \TodoPago\Exception\Data\EmptyFieldException();
        
    }

    /**
     * @expectedException \TodoPago\Exception\ResponseException
     */
    public function testResponseException() {
        throw new \TodoPago\Exception\ResponseException();
        
    }   

    /**
     * @expectedException \TodoPago\Exception\ConnectionException
     */
    public function testConnectionException() {
        throw new \TodoPago\Exception\ConnectionException();
        
    } 

    public function testUserClassInvokeOne() {
        $user = new \TodoPago\Data\User();
        $user->setUser("ejemplo@mail.com");
        $user->setPassword("mypassword");

        $this->assertInstanceOf(\TodoPago\Data\User::class,$user);
        $this->assertEquals($user->getUser(),"ejemplo@mail.com");
        $this->assertEquals($user->getPassword(),"mypassword");
        $this->assertArrayHasKey("USUARIO",$user->getData());
        $this->assertArrayHasKey("CLAVE",$user->getData());
    }  

    public function testUserClassInvokeTwo() {
        $user = new \TodoPago\Data\User("ejemplo@mail.com","mypassword");

        $this->assertInstanceOf(\TodoPago\Data\User::class,$user);
        $this->assertEquals($user->getUser(),"ejemplo@mail.com");
        $this->assertEquals($user->getPassword(),"mypassword");
        $this->assertArrayHasKey("USUARIO",$user->getData());
        $this->assertArrayHasKey("CLAVE",$user->getData());
    }  

    public function testUserClassInvokeThree() {
        $user = new \TodoPago\Data\User(array("user" => "ejemplo@mail.com", "password" => "mypassword"));

        $this->assertInstanceOf(\TodoPago\Data\User::class,$user);
        $this->assertEquals($user->getUser(),"ejemplo@mail.com");
        $this->assertEquals($user->getPassword(),"mypassword");
        $this->assertArrayHasKey("USUARIO",$user->getData());
        $this->assertArrayHasKey("CLAVE",$user->getData());
    }  

}
