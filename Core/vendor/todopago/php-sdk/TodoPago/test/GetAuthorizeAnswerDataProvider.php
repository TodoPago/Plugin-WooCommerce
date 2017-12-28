<?php

namespace TodoPago\Test;

class GetAuthorizeAnswerDataProvider {

    public static function getAuthorizeAnswerOptions() {

       $optionsGAA = array( 
            'Security' => '8A891C0676A25FBF052D1C2FFBC82DEE', 
            'Merchant' => "41702",
            'RequestKey' => '83765ffb-39c8-2cce-b0bf-a9b50f405ee3',
            'AnswerKey' => '9c2ddf78-1088-b3ac-ae5a-ddd45976f77d'
        );

        return $optionsGAA;    
    
    }

    public static function getAuthorizeAnswerOkResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:GetAuthorizeAnswerResponse><api:StatusCode>-1</api:StatusCode><api:StatusMessage>APROBADA</api:StatusMessage><api:AuthorizationKey>a61de00b-c118-2688-77b0-16dbe5799913</api:AuthorizationKey><api:EncodingMethod>XML</api:EncodingMethod><api:Payload><Answer xmlns="http://api.todopago.com.ar"><DATETIME>2017-05-02T11:37:48Z</DATETIME><CURRENCYNAME>Peso Argentino</CURRENCYNAME><PAYMENTMETHODNAME>VISA</PAYMENTMETHODNAME><TICKETNUMBER>12</TICKETNUMBER><AUTHORIZATIONCODE>654402</AUTHORIZATIONCODE><CARDNUMBERVISIBLE>4507XXXXXXXX0010</CARDNUMBERVISIBLE><BARCODE></BARCODE><OPERATIONID>551</OPERATIONID><COUPONEXPDATE></COUPONEXPDATE><COUPONSECEXPDATE></COUPONSECEXPDATE><COUPONSUBSCRIBER></COUPONSUBSCRIBER><BARCODETYPE></BARCODETYPE><ASSOCIATEDDOCUMENTATION></ASSOCIATEDDOCUMENTATION><INSTALLMENTPAYMENTS>7</INSTALLMENTPAYMENTS></Answer><Request xmlns="http://api.todopago.com.ar"><MERCHANT>2658</MERCHANT><OPERATIONID>551</OPERATIONID><AMOUNT>12.00</AMOUNT><CURRENCYCODE>32</CURRENCYCODE><AMOUNTBUYER>12.00</AMOUNTBUYER><BANKID>4</BANKID><PROMOTIONID>2712</PROMOTIONID></Request></api:Payload></api:GetAuthorizeAnswerResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function getAuthorizeAnswerFailResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:GetAuthorizeAnswerResponse><api:StatusCode>404</api:StatusCode><api:StatusMessage>ERROR: Transaccion Enexistente</api:StatusMessage></api:GetAuthorizeAnswerResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function getAuthorizeAnswer702Response() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:GetAuthorizeAnswerResponse><api:StatusCode>702</api:StatusCode><api:StatusMessage>Cuenta de Vendedor Invalida</api:StatusMessage></api:GetAuthorizeAnswerResponse></soapenv:Body></soapenv:Envelope>';
    }     

    public static function getAuthorizeAnswerSoapRequest() {
        return '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://api.todopago.com.ar"><SOAP-ENV:Body><ns1:GetAuthorizeAnswer><ns1:Security>8A891C0676A25FBF052D1C2FFBC82DEE</ns1:Security><ns1:Merchant>41702</ns1:Merchant><ns1:RequestKey>83765ffb-39c8-2cce-b0bf-a9b50f405ee3</ns1:RequestKey><ns1:AnswerKey>9c2ddf78-1088-b3ac-ae5a-ddd45976f77d</ns1:AnswerKey></ns1:GetAuthorizeAnswer></SOAP-ENV:Body></SOAP-ENV:Envelope>
';
    }
}