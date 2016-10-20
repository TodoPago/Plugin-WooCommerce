<?php
use TodoPago\Sdk;

//importo archivo con SDK
include_once '../../vendor/autoload.php';


//común a todas los métodos
$http_header = array('Authorization'=>'TODOPAGO 0129b065cfb744718166913eba827a2f',
 'user_agent' => 'PHPSoapClient');

//datos constantes
define('CURRENCYCODE', 032);
define('MERCHANT', 35);
define('ENCODINGMETHOD', 'XML');
define('SECURITY', '0129b065cfb744718166913eba827a2f');

//id de la operacion
$operationid = rand(0, 99999999);

//opciones para el método sendAuthorizeRequest (datos propios del comercio)
$optionsSAR_comercio = array (
	'Security'=> SECURITY,
	'EncodingMethod'=>ENCODINGMETHOD,
	'Merchant'=>MERCHANT,
	'URL_OK'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/exito.php?operationid=$operationid",
	'URL_ERROR'=>"http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].str_replace ($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))."/error.php?operationid=$operationid"
);

// + opciones para el método sendAuthorizeRequest (datos propios de la operación) 
$optionsSAR_operacion = $_POST;
$optionsSAR_operacion['MERCHANT'] = MERCHANT;
/*$optionsSAR_operacion = array (
	'MERCHANT'=> MERCHANT, //dato fijo (número identificador del comercio)
	'OPERATIONID'=>'13333456', //número único que identifica la operación
	'CURRENCYCODE'=> CURRENCYCODE, //por el momento es el único tipo de moneda aceptada
	'AMOUNT'=>$_POST['amount'],
	);
*/

//creo instancia de la clase TodoPago
$connector = new Sdk($http_header, "test");

$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
if($rta['StatusCode'] != -1) {
	var_dump($rta);
} else {
	setcookie('RequestKey',$rta["RequestKey"],  time() + (86400 * 30), "/");
	header("Location: ".$rta["URL_Request"]);		
}	