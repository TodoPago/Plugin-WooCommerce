<?php

namespace TodoPago\Test;

class RefundDataProvider {

    public static function returnOptions() {

        $devol = array(
            "Security" => "108fc2b7c8a640f2bdd3ed505817ffde",
            "Merchant" => "2669",
            "RequestKey" => "0d801e1c-e6b1-672c-b717-5ddbe5ab97d6",
            "AMOUNT" => 1.00
        );

        return $devol;    
    }

    public static function voidOptions() {
        $devol = array(
            "Security" => "108fc2b7c8a640f2bdd3ed505817ffde",
            "Merchant" => "2669",
            "RequestKey" => "0d801e1c-e6b1-672c-b717-5ddbe5ab97d6"
        );

        return $devol;    
    }

    public static function returnRequestOkResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:ReturnResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>2011</api:StatusCode><api:StatusMessage>Devolucion OK</api:StatusMessage><api:AuthorizationKey>a61de00b-c118-2688-77b0-16dbe5799913</api:AuthorizationKey><api:AUTHORIZATIONCODE>654402</api:AUTHORIZATIONCODE></api:ReturnResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function returnRequestFailResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:ReturnResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>2013</api:StatusCode><api:StatusMessage>No es posible obtener los importes de las comisiones para realizar la devolucion</api:StatusMessage><api:AuthorizationKey></api:AuthorizationKey><api:AUTHORIZATIONCODE></api:AUTHORIZATIONCODE></api:ReturnResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function returnRequest702Response() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:ReturnResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>702</api:StatusCode><api:StatusMessage>Cuenta de Vendedor Invalida</api:StatusMessage><api:AuthorizationKey></api:AuthorizationKey><api:AUTHORIZATIONCODE></api:AUTHORIZATIONCODE></api:ReturnResponse></soapenv:Body></soapenv:Envelope>';
    }        

    public static function returnRequestSoapRequest() {
        return '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://api.todopago.com.ar"><SOAP-ENV:Body><ns1:ReturnRequest><ns1:Security>108fc2b7c8a640f2bdd3ed505817ffde</ns1:Security><ns1:Merchant>2669</ns1:Merchant><ns1:RequestKey>0d801e1c-e6b1-672c-b717-5ddbe5ab97d6</ns1:RequestKey><ns1:AMOUNT>1</ns1:AMOUNT></ns1:ReturnRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>
';
    }

    public static function voidRequestOkResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:VoidResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>2011</api:StatusCode><api:StatusMessage>Devolucion OK</api:StatusMessage><api:AuthorizationKey>a61de00b-c118-2688-77b0-16dbe5799913</api:AuthorizationKey><api:AUTHORIZATIONCODE>654402</api:AUTHORIZATIONCODE></api:VoidResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function voidRequestFailResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:VoidResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>2013</api:StatusCode><api:StatusMessage>No es posible obtener los importes de las comisiones para realizar la devolucion</api:StatusMessage><api:AuthorizationKey></api:AuthorizationKey><api:AUTHORIZATIONCODE></api:AUTHORIZATIONCODE></api:VoidResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function voidRequest702Response() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><api:VoidResponse xmlns:api="http://api.todopago.com.ar"><api:StatusCode>702</api:StatusCode><api:StatusMessage>Cuenta de Vendedor Invalida</api:StatusMessage><api:AuthorizationKey></api:AuthorizationKey><api:AUTHORIZATIONCODE></api:AUTHORIZATIONCODE></api:VoidResponse></soapenv:Body></soapenv:Envelope>';
    }    

    public static function voidRequestSoapRequest() {
        return '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://api.todopago.com.ar"><SOAP-ENV:Body><ns1:VoidRequest><ns1:Security>108fc2b7c8a640f2bdd3ed505817ffde</ns1:Security><ns1:Merchant>2669</ns1:Merchant><ns1:RequestKey>0d801e1c-e6b1-672c-b717-5ddbe5ab97d6</ns1:RequestKey></ns1:VoidRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>
';
    }
}