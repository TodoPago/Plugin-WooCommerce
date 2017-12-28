<?php

namespace TodoPago\Test;

class SendAuthorizeRequestDataProvider {

    public static function sendAuthorizeRequestOptions() {

        $optionsSAR_comercio = array (
            'Security'=>'d064744d44cf4985851e460e893e1b15',
            'EncodingMethod'=>'XML',
            'Merchant'=>2658,
            'URL_OK'=>"http://exito.php",
            'URL_ERROR'=>"http://error.php"
        );

        $optionsSAR_operacion = array (
            'MERCHANT'=> "2658",
            'OPERATIONID'=>"50",
            'CURRENCYCODE'=> 032,
            'AMOUNT'=>"54",
            'MININSTALLMENTS' => 3,
            'MAXINSTALLMENTS' => 8,
            
            'CSBTCITY'=> "Villa General Belgrano",
            'CSSTCITY'=> "Villa General Belgrano",
            
            'CSBTCOUNTRY'=> "AR",
            'CSSTCOUNTRY'=> "AR",
            
            'CSBTEMAIL'=> "todopago@hotmail.com",
            'CSSTEMAIL'=> "todopago@hotmail.com",
            
            'CSBTFIRSTNAME'=> "Juan",
            'CSSTFIRSTNAME'=> "LAURA",      
            
            'CSBTLASTNAME'=> "Perez",
            'CSSTLASTNAME'=> "GONZALEZ",
            
            'CSBTPHONENUMBER'=> "541160913988",     
            'CSSTPHONENUMBER'=> "541160913988",     
            
            'CSBTPOSTALCODE'=> " 1010",
            'CSSTPOSTALCODE'=> " 1010",
            
            'CSBTSTATE'=> "B",
            'CSSTSTATE'=> "B",
            
            'CSBTSTREET1'=> "Cerrito 740",
            'CSSTSTREET1'=> "Cerrito 740",
            
            'CSBTCUSTOMERID'=> "453458",
            'CSBTIPADDRESS'=> "192.0.0.4",       
            'CSPTCURRENCY'=> "ARS",
            'CSPTGRANDTOTALAMOUNT'=> "125.38",          

            'CSITPRODUCTCODE'=> "electronic_good",
            'CSITPRODUCTDESCRIPTION'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA",     
            'CSITPRODUCTNAME'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA",  
            'CSITPRODUCTSKU'=> "LEVJNSL36GN",
            'CSITTOTALAMOUNT'=> "1254.40",
            'CSITQUANTITY'=> "1",
            'CSITUNITPRICE'=> "1254.40",

        );

        return array($optionsSAR_comercio, $optionsSAR_operacion);
    }

    public static function sendAuthorizeRequestOkResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:SendAuthorizeRequestResponse><api:StatusCode>-1</api:StatusCode><api:StatusMessage>Solicitud de Autorizacion Registrada</api:StatusMessage><api:URL_Request>https://developers.todopago.com.ar/formulario/commands?command=formulario&amp;m=tdbda56ab-1b64-d470-efca-5817c6216429</api:URL_Request><api:RequestKey>5b26f546-e831-1551-d801-f426f1adfede</api:RequestKey><api:PublicRequestKey>tdbda56ab-1b64-d470-efca-5817c6216429</api:PublicRequestKey></api:SendAuthorizeRequestResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function sendAuthorizeRequestFailResponse() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:SendAuthorizeRequestResponse><api:StatusCode>98001</api:StatusCode><api:StatusMessage>El campo CSBTCITY es obligatorio. (Min Length 2)</api:StatusMessage></api:SendAuthorizeRequestResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function sendAuthorizeRequest702Response() {
        return '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.todopago.com.ar"><soapenv:Body><api:SendAuthorizeRequestResponse><api:StatusCode>702</api:StatusCode><api:StatusMessage>Cuanta de Vendedor Invalida</api:StatusMessage></api:SendAuthorizeRequestResponse></soapenv:Body></soapenv:Envelope>';
    }

    public static function sendAuthorizeRequestSoapRequest() {
        return '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://api.todopago.com.ar"><SOAP-ENV:Body><ns1:SendAuthorizeRequest><ns1:Security>d064744d44cf4985851e460e893e1b15</ns1:Security><ns1:Merchant>2658</ns1:Merchant><ns1:URL_OK>http://exito.php</ns1:URL_OK><ns1:URL_ERROR>http://error.php</ns1:URL_ERROR><ns1:EncodingMethod>XML</ns1:EncodingMethod><ns1:Payload>&lt;Request&gt;&lt;MERCHANT&gt;2658&lt;/MERCHANT&gt;&lt;OPERATIONID&gt;50&lt;/OPERATIONID&gt;&lt;CURRENCYCODE&gt;26&lt;/CURRENCYCODE&gt;&lt;AMOUNT&gt;54&lt;/AMOUNT&gt;&lt;MININSTALLMENTS&gt;3&lt;/MININSTALLMENTS&gt;&lt;MAXINSTALLMENTS&gt;8&lt;/MAXINSTALLMENTS&gt;&lt;CSBTCITY&gt;Villa General Belgrano&lt;/CSBTCITY&gt;&lt;CSSTCITY&gt;Villa General Belgrano&lt;/CSSTCITY&gt;&lt;CSBTCOUNTRY&gt;AR&lt;/CSBTCOUNTRY&gt;&lt;CSSTCOUNTRY&gt;AR&lt;/CSSTCOUNTRY&gt;&lt;CSBTEMAIL&gt;todopago@hotmail.com&lt;/CSBTEMAIL&gt;&lt;CSSTEMAIL&gt;todopago@hotmail.com&lt;/CSSTEMAIL&gt;&lt;CSBTFIRSTNAME&gt;Juan&lt;/CSBTFIRSTNAME&gt;&lt;CSSTFIRSTNAME&gt;LAURA&lt;/CSSTFIRSTNAME&gt;&lt;CSBTLASTNAME&gt;Perez&lt;/CSBTLASTNAME&gt;&lt;CSSTLASTNAME&gt;GONZALEZ&lt;/CSSTLASTNAME&gt;&lt;CSBTPHONENUMBER&gt;541160913988&lt;/CSBTPHONENUMBER&gt;&lt;CSSTPHONENUMBER&gt;541160913988&lt;/CSSTPHONENUMBER&gt;&lt;CSBTPOSTALCODE&gt; 1010&lt;/CSBTPOSTALCODE&gt;&lt;CSSTPOSTALCODE&gt; 1010&lt;/CSSTPOSTALCODE&gt;&lt;CSBTSTATE&gt;B&lt;/CSBTSTATE&gt;&lt;CSSTSTATE&gt;B&lt;/CSSTSTATE&gt;&lt;CSBTSTREET1&gt;Cerrito 740&lt;/CSBTSTREET1&gt;&lt;CSSTSTREET1&gt;Cerrito 740&lt;/CSSTSTREET1&gt;&lt;CSBTCUSTOMERID&gt;453458&lt;/CSBTCUSTOMERID&gt;&lt;CSBTIPADDRESS&gt;192.0.0.4&lt;/CSBTIPADDRESS&gt;&lt;CSPTCURRENCY&gt;ARS&lt;/CSPTCURRENCY&gt;&lt;CSPTGRANDTOTALAMOUNT&gt;125.38&lt;/CSPTGRANDTOTALAMOUNT&gt;&lt;CSITPRODUCTCODE&gt;electronic_good&lt;/CSITPRODUCTCODE&gt;&lt;CSITPRODUCTDESCRIPTION&gt;NOTEBOOK L845 SP4304LA DF TOSHIBA&lt;/CSITPRODUCTDESCRIPTION&gt;&lt;CSITPRODUCTNAME&gt;NOTEBOOK L845 SP4304LA DF TOSHIBA&lt;/CSITPRODUCTNAME&gt;&lt;CSITPRODUCTSKU&gt;LEVJNSL36GN&lt;/CSITPRODUCTSKU&gt;&lt;CSITTOTALAMOUNT&gt;1254.40&lt;/CSITTOTALAMOUNT&gt;&lt;CSITQUANTITY&gt;1&lt;/CSITQUANTITY&gt;&lt;CSITUNITPRICE&gt;1254.40&lt;/CSITUNITPRICE&gt;&lt;SDK&gt;PHP&lt;/SDK&gt;&lt;SDKVERSION&gt;1.9.0&lt;/SDKVERSION&gt;&lt;LENGUAGEVERSION&gt;5.6.11-1ubuntu3.4&lt;/LENGUAGEVERSION&gt;&lt;/Request&gt;</ns1:Payload></ns1:SendAuthorizeRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>
';
    }
}