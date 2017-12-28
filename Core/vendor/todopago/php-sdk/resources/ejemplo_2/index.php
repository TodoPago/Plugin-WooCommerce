<?php
use TodoPago\Sdk;

//importo archivo con SDK
include_once '../../vendor/autoload.php';

//común a todas los métodos
$http_header = array('Authorization'=>'TODOPAGO 0129b065cfb744718166913eba827a2f',
 'user_agent' => 'PHPSoapClient');

$operationid = rand(1,100000000);

//opciones para el método sendAuthorizeRequest
$optionsSAR_comercio = array (
	'Security'=>'0129b065cfb744718166913eba827a2f',
	'EncodingMethod'=>'XML',
	'Merchant'=>35,
	'URL_OK'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/exito.php?operationid=$operationid",
	'URL_ERROR'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/error.php?operationid=$operationid"
);

$optionsSAR_operacion = array (
	'MERCHANT'=> "35",
	'OPERATIONID'=>"01",
	'CURRENCYCODE'=> 032,
	'AMOUNT'=>"54"
	);
 	
//opciones para el método getAuthorizeAnswer
$optionsGAA = array(	
	'Security' => '0129b065cfb744718166913eba827a2f', 
	'Merchant' => "35",
	'RequestKey' => '8496472a-8c87-e35b-dcf2-94d5e31eb12f',
	'AnswerKey' => '8496472a-8c87-e35b-dcf2-94d5e31eb12f'
	);
	
//opciones para el método getAllPaymentMethods
$optionsGAMP = array("MERCHANT"=>35);
	
//opciones para el método getStatus 
$optionsGS = array('MERCHANT'=>'35', 'OPERATIONID'=>'141120084707');
	
//creo instancia de la clase TodoPago
$connector = new Sdk($http_header,"test");
	
//ejecuto los métodos
$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
$rta2 = $connector->getAuthorizeAnswer($optionsGAA);
$rta3 = $connector->getAllPaymentMethods($optionsGAMP);
$rta4 = $connector->getStatus($optionsGS);    
   	 
//imprimo respuestas
echo "<h3>var_dump de la respuesta de Send Authorize Request</h3>";
var_dump($rta);
echo "<h3>var_dump de la respuesta de Get Authorize Answer</h3>";
var_dump($rta2);
echo "<h3>var_dump de la respuesta de Get All Payment Methods</h3>";
var_dump($rta3);
echo "<h3>var_dump de la respuesta de Get Status</h3>";
var_dump($rta4);