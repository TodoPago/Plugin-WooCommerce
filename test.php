<?php

require_once("Core/vendor/autoload.php");
require_once("Core/Core.php");
//use TodoPago;

echo "##########";
echo "\n## TEST ##";
echo "\n##########\n";


//////////////////////////////////////////
///************************************///
///*************LLAMADORES*************///
///************************************///
//////////////////////////////////////////


//first_step();
//second_step();
//get_credentials();
//set_config();
//set_address();


transaction();


//////////////////////////////////////////
function first_step()
{
    echo 'first step ... ';
    $http_header = array('Authorization' => 'TODOPAGO aa3b95b8733d4209a61ce5db3ab615c9',
        'user_agent' => 'PHPSoapClient');

    $merchant = new TodoPago\Core\Merchant\MerchantDTO();
    $merchant->setMerchantId('3150');
    $merchant->setApiKey('aa3b95b8733d4209a61ce5db3ab615c9');
    $merchant->setHttpHeader($http_header);


    $opcionales = [
        'deadLine' => "5",
        'timeoutValor' => 10000000
    ];

    $todoPagoConfig = new \TodoPago\Core\Config\ConfigDTO('test', 'ext', true, '1', '0');
    $todoPagoConfig->setArrayOpcionales($opcionales);
    // $config = new \TodoPago\Core\Config\ConfigModel($todoPagoConfig);

    // con estos parametros el core iniciara el sdk
    $core = new \TodoPago\Core($merchant, $todoPagoConfig);


   // $core->call_sar();



    $OrderDTO = new Todopago\Core\Order\OrderDTO();

    //$OrderDTO->setOpBilling($opbilling);

    return 0;
}

function transaction()
{
    global $wpdb;
    var_dump($wpdb);
    echo "Transaction...";
    $transactionDTO = new \TodoPago\Core\Transaction\TransactionDTO();
    $responseSARMock = new stdClass();
    $responseSARMock->statusCode = '15151';
    $responseSARMock->statusMessage = 'PITO';
    $responseSARMock->urlRequest = "https:\/\/developers.todopago.com.ar\/formulario\/commands?command=formulario&m=t1611f996-51b2-bc78-8b6f-6f4f5f59839b";
    $responseSARMock->requestKey = "b5cd36f4-dbe8-3ef1-221a-760678169246";
    $responseSARMock->publicRequestKey = "t1611f996-51b2-bc78-8b6f-6f4f5f59839b";
    $transactionDTO->setResponse($responseSARMock);
    $transactionDTO->setParams(getParamsSARMock());
    echo "\nparams sar seteado";
    $transactionModel = new \TodoPago\Core\Transaction\TransactionModel('5');
    $transactionModel->setResponseSAR($transactionDTO);
    $transactionModel->setParamsSAR($transactionDTO);
    $transactionDAO = new \TodoPago\Core\Transaction\TransactionDAO();
    $transactionDAO->saveSAR($transactionModel);
    var_dump($transactionModel);
}

function second_step()
{
    return 0;
}

function set_config()
{
    echo "\nSet config...";
    $opcionales = [
        'deadLine' => "5",
        'timeoutValor' => 10000000
    ];
    $todoPagoConfig = new \TodoPago\Core\Config\ConfigDTO('prod', 'hibrido', true, '1', '0');
    $todoPagoConfig->setArrayOpcionales($opcionales);
    $model = new \TodoPago\Core\Config\ConfigModel($todoPagoConfig);
    echo "\nConfig seteada sin ningún error.";
}

function set_address()
{
    echo "\nSet address...";
    $todoPagoAddressDTO = new \TodoPago\Core\Address\AddressDTO('Villa Sarmiento');
    $todoPagoAddressDTO->setPostalCode(1707);
    $todoPagoAddressDTO->setPhoneNumber('41414141');
    $todoPagoAddressDTO->setState('Buenos Aires');
    $todoPagoAddressDTO->setStreet('');
    $todoPagoAddressDTO->setCountry('Argentina');
    $todoPagoAddressModel = new \TodoPago\Core\Address\AddressModel($todoPagoAddressDTO);
    echo "\nAddress seteadas sin ningún error.";
}

function getParamsSARMock()
{
    $paramsSARMock = new stdClass();
    $paramsSARMock->AMOUNT = '125.38';
    $paramsSARMock->EMAILCLIENTE = 'decidir@hotmail.com';
    $paramsSARMock->CSBTCITY = 'Villa General Belgrano'; //Ciudad de facturación, REQUERIDO.
    $paramsSARMock->CSBTCOUNTRY = 'AR'; //País de facturación. REQUERIDO. Código ISO.
    $paramsSARMock->CSBTCUSTOMERID = '453458'; //Identificador del usuario al que se le emite la factura. REQUERIDO. No puede contener un correo electrónico.
    $paramsSARMock->CSBTIPADDRESS = '192.0.0.4'; //IP de la PC del comprador. REQUERIDO.
    $paramsSARMock->CSBTEMAIL = 'decidir@hotmail.com'; //Mail del usuario al que se le emite la factura. REQUERIDO.
    $paramsSARMock->CSBTFIRSTNAME = 'Juan';//Nombre del usuario al que se le emite la factura. REQUERIDO.
    $paramsSARMock->CSBTLASTNAME = 'Perez'; //Apellido del usuario al que se le emite la factura. REQUERIDO.
    $paramsSARMock->CSBTPHONENUMBER = '541160913988'; //Teléfono del usuario al que se le emite la factura. No utilizar guiones, puntos o espacios. Incluir código de país. REQUERIDO.
    $paramsSARMock->CSBTPOSTALCODE = ' C1010AAP'; //Código Postal de la dirección de facturación. REQUERIDO.
    $paramsSARMock->CSBTSTATE = 'B'; //Provincia de la dirección de facturación. REQUERIDO. Ver tabla anexa de provincias.
    $paramsSARMock->CSBTSTREET1 = 'Cerrito 740'; //Domicilio de facturación (calle y nro). REQUERIDO.
//        $paramsSARMock->CSBTSTREET2 = 'Piso 8'; //Complemento del domicilio. (piso, departamento). OPCIONAL.
    $paramsSARMock->CSPTCURRENCY = 'ARS'; //Moneda. REQUERIDO FIXED.
    $paramsSARMock->CSPTGRANDTOTALAMOUNT = '125.38'; //Con decimales opcional usando el punto como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales. REQUERIDO. (Ejemplos:$125,38-> 125.38 $12-> 12 o 12.00)
    $paramsSARMock->CSMDD7 = ''; // Fecha registro comprador(num Dias). OPCIONAL.
    #$paramsSARMock->CSMDD8 = 'Y'; //Usuario Guest? (Y/N). En caso de ser Y, el campo CSMDD9 no deberá enviarse. OPCIONAL.
    #$paramsSARMock->CSMDD9 = ''; //Customer password Hash: criptograma asociado al password del comprador final. OPCIONAL.
    #$paramsSARMock->CSMDD10 = ''; //Histórica de compras del comprador (Num transacciones). OPCIONAL.
    #$paramsSARMock->CSMDD11 = ''; //Customer Cell Phone. OPCIONAL.
    $paramsSARMock->CSSTCITY = 'rosario'; //Ciudad de envío de la orden. REQUERIDO.
    $paramsSARMock->CSSTCOUNTRY = ''; //País de envío de la orden. REQUERIDO.
    $paramsSARMock->CSSTEMAIL = 'jose@gmail.com'; //Mail del destinatario, REQUERIDO.
    $paramsSARMock->CSSTFIRSTNAME = 'Jose'; //Nombre del destinatario. REQUERIDO.
    $paramsSARMock->CSSTLASTNAME = 'Perez'; //Apellido del destinatario. REQUERIDO.
    $paramsSARMock->CSSTPHONENUMBER = '541155893737'; //Número de teléfono del destinatario. REQUERIDO.
    $paramsSARMock->CSSTPOSTALCODE = '1414'; //Código postal del domicilio de envío. REQUERIDO.
    $paramsSARMock->CSSTSTATE = 'D'; //Provincia de envío. REQUERIDO. Son de 1 caracter
    $paramsSARMock->CSSTSTREET1 = 'San Martín 123'; //Domicilio de envío. REQUERIDO.
    #$paramsSARMock->CSMDD12 = '';//Shipping DeadLine (Num Dias). NO REQUERIDO.
    #$paramsSARMock->CSMDD13 = '';//Método de Despacho. NO REQUERIDO.
    #$paramsSARMock->CSMDD14 = '';//Customer requires Tax Bill ? (Y/N). NO REQUERIDO.
    #$paramsSARMock->CSMDD15 = '';//Customer Loyality Number. NO REQUERIDO.
    #$paramsSARMock->CSMDD16 = '';//Promotional / Coupon Code. NO REQUERIDO.
    $paramsSARMock->CSITPRODUCTCODE = 'electronic_good'; //Código de producto. REQUERIDO. Valores posibles(adult_content;coupon;default;electronic_good;electronic_software;gift_certificate;handling_only;service;shipping_and_handling;shipping_only;subscription)
    $paramsSARMock->CSITPRODUCTDESCRIPTION = 'NOTEBOOK L845 SP4304LA DF TOSHIBA'; //Descripción del producto. REQUERIDO.
    $paramsSARMock->CSITPRODUCTNAME = 'NOTEBOOK L845 SP4304LA DF TOSHIBA'; //Nombre del producto. REQUERIDO.
    $paramsSARMock->CSITPRODUCTSKU = 'LEVJNSL36GN'; //Código identificador del producto. REQUERIDO.
    $paramsSARMock->CSITTOTALAMOUNT = '1254.40'; //CSITTOTALAMOUNT=CSITUNITPRICE*CSITQUANTITY "999999[.CC]" Con decimales opcional usando el punto como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales. REQUERIDO.
    $paramsSARMock->CSITQUANTITY = '1'; //Cantidad del producto. REQUERIDO.
    $paramsSARMock->CSITUNITPRICE = '1254.40'; //Formato IDEM CSTITOTALAMOUNT
    return $paramsSARMock;
}

function set_client()
{

}

function get_credentials()
{

}

?>
