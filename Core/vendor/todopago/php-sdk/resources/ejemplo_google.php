<?php

//importo archivo con SDK
include_once dirname(__FILE__)."/../vendor/autoload.php";
use TodoPago\Sdk;

//común a todas los métodos
$http_header = array('Authorization'=>'TODOPAGO d064744d44cf4985851e460e893e1b15',
 'user_agent' => 'PHPSoapClient');
 
//creo instancia de la clase TodoPago
$connector = new Sdk($http_header, "test");

$operationid = rand(1,10000000); 
 
//opciones para el método sendAuthorizeRequest
$optionsSAR_comercio = array (
    'Security'=>'d064744d44cf4985851e460e893e1b15',
    'EncodingMethod'=>'XML',
    'Merchant'=>2658,
    'URL_OK'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/exito.php?operationid=$operationid",
    'URL_ERROR'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/error.php?operationid=$operationid"
);

$optionsSAR_operacion = array (
    'MERCHANT'=> "2658",
    'OPERATIONID'=>"50",
    'CURRENCYCODE'=> 032,
    'AMOUNT'=>"54",
    'MININSTALLMENTS' => 3, //Nro minimo de cuotas a mostrar en el formulario, OPCIONAL.
    'MAXINSTALLMENTS' => 8, //Nro maximo de cuotas a mostrar en el formulario, OPCIONAL.
    //Datos ejemplos CS
    'CSBTCITY'=> "CABA",
    'CSSTCITY'=> "CABA",
    
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
    
    'CSBTPOSTALCODE'=> "1426",
    'CSSTPOSTALCODE'=> "1426",
    
    'CSBTSTATE'=> "B",
    'CSSTSTATE'=> "B",
    
    'CSBTSTREET1'=> "Cespedes 2971",
    'CSSTSTREET1'=> "Cespedes 2971",
    
    'CSBTCUSTOMERID'=> "453458",
    'CSBTIPADDRESS'=> "192.0.0.4",       
    'CSPTCURRENCY'=> "ARS",
    'CSPTGRANDTOTALAMOUNT'=> "125.38",
    'CSMDD7'=> "",     
    'CSMDD8'=> "Y",       
    'CSMDD9'=> "",       
    'CSMDD10'=> "",      
    'CSMDD11'=> "",
    'CSMDD12'=> "",     
    'CSMDD13'=> "",
    'CSMDD14'=> "",
    'CSMDD15'=> "",        
    'CSMDD16'=> "",
    'CSITPRODUCTCODE'=> "electronic_good#chocho",
    'CSITPRODUCTDESCRIPTION'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA#chocho",     
    'CSITPRODUCTNAME'=> "NOTEBOOK L845 SP4304LA DF TOSHIBA#chocho",  
    'CSITPRODUCTSKU'=> "LEVJNSL36GN#chocho",
    'CSITTOTALAMOUNT'=> "1254.40#10.00",
    'CSITQUANTITY'=> "1#1",
    'CSITUNITPRICE'=> "1254.40#15.00",

    );

$g = new \TodoPago\Client\Google();
$g->setProxyParameters("proxy.visa2.com.ar",8080,"user","password");

$connector->setGoogleClient($g);

$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);

var_dump($rta);

print_r($connector->getGoogleClient()->getGoogleResponse());
print_r($connector->getGoogleClient()->getOriginalAddress());
print_r($connector->getGoogleClient()->getFinalAddress());
