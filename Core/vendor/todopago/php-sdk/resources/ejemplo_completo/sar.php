<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";
use TodoPago\Sdk;
session_start();
include("includes/head.php");

$operationid = strip_tags($_GET['ord']);

$orders_db = new FlatDb();
$orders_db->openTable('ordenes');

$orden = $orders_db->getRecords(array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR", 	"response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "mode"),array("id" => $operationid));

//común a todas los métodos
$http_header = array(
	'Authorization'=>$orden[0]["authorizationhttp"],
	'user_agent' => 'PHPSoapClient');
 
//creo instancia de la clase TodoPago
$connector = new Sdk($http_header, $orden[0]["mode"]);

$dataComercio=json_decode($orden[0]["data"], true);
$arrParamsSar=json_decode($orden[0]['data'], true);

//opciones para el método sendAuthorizeRequest
$optionsSAR_comercio = array (
	'Security'=>$orden[0]["security"],
	'EncodingMethod'=>'XML',
	'Merchant'=>$dataComercio["comercio"]["URL_OK"],


	'URL_OK'=>$dataComercio["operacion"]["URL_OK"],
    'URL_ERROR'=>$dataComercio["operacion"]["URL_ERROR"]


);

$optionsSAR_operacion = array (
	'MERCHANT'=> $dataComercio["comercio"]["Merchant"],
	'OPERATIONID'=>$dataComercio["operacion"]["OPERATIONID"],
	'CURRENCYCODE'=> $dataComercio["operacion"]["CURRENCYCODE"],
	'AMOUNT'=>$dataComercio["operacion"]["AMOUNT"],
	//'MININSTALLMENTS' => 3, //Nro minimo de cuotas a mostrar en el formulario, OPCIONAL.	
	//'MAXINSTALLMENTS' => 8, //Nro maximo de cuotas a mostrar en el formulario, OPCIONAL.
	'MININSTALLMENTS' => $dataComercio["operacion"]["MININSTALLMENTS"],
	'MAXINSTALLMENTS' => $dataComercio["operacion"]["MAXINSTALLMENTS"],

	'CSBTCITY'=> $arrParamsSar["operacion"]["CSBTCITY"],
	'CSSTCITY'=> $arrParamsSar["operacion"]["CSSTCITY"],
	
	'CSBTCOUNTRY'=> $arrParamsSar["operacion"]["CSBTCOUNTRY"],
	'CSSTCOUNTRY'=> $arrParamsSar["operacion"]["CSSTCOUNTRY"],
	
	'CSBTEMAIL'=> $arrParamsSar["operacion"]["CSBTEMAIL"],
	'CSSTEMAIL'=> $arrParamsSar["operacion"]["CSSTEMAIL"],
	
	'CSBTFIRSTNAME'=> $arrParamsSar["operacion"]["CSBTFIRSTNAME"],
	'CSSTFIRSTNAME'=> $arrParamsSar["operacion"]["CSSTFIRSTNAME"],      
	
	'CSBTLASTNAME'=> $arrParamsSar["operacion"]["CSBTLASTNAME"],
	'CSSTLASTNAME'=> $arrParamsSar["operacion"]["CSSTLASTNAME"],
	
	'CSBTPHONENUMBER'=> $arrParamsSar["operacion"]["CSBTPHONENUMBER"],     
	'CSSTPHONENUMBER'=> $arrParamsSar["operacion"]["CSSTPHONENUMBER"],     
	
	'CSBTPOSTALCODE'=> $arrParamsSar["operacion"]["CSBTPOSTALCODE"],
	'CSSTPOSTALCODE'=> $arrParamsSar["operacion"]["CSSTPOSTALCODE"],
	
	'CSBTSTATE'=> $arrParamsSar["operacion"]["CSBTSTATE"],
	'CSSTSTATE'=> $arrParamsSar["operacion"]["CSSTSTATE"],
	
	'CSBTSTREET1'=> $arrParamsSar["operacion"]["CSBTSTREET1"],
	'CSSTSTREET1'=> $arrParamsSar["operacion"]["CSSTSTREET1"],
	
	'CSBTCUSTOMERID'=> $arrParamsSar["operacion"]["CSBTCUSTOMERID"],
	'CSBTIPADDRESS'=> $arrParamsSar["operacion"]["CSBTIPADDRESS"],       
	'CSPTCURRENCY'=> $arrParamsSar["operacion"]["CSPTCURRENCY"],
	'CSPTGRANDTOTALAMOUNT'=> $arrParamsSar["operacion"]["CSPTGRANDTOTALAMOUNT"],
	'CSMDD7'=> $arrParamsSar["operacion"]["CSMDD7"],     
	'CSMDD8'=> $arrParamsSar["operacion"]["CSMDD8"],       
	'CSMDD9'=> $arrParamsSar["operacion"]["CSMDD9"],       
	'CSMDD10'=> $arrParamsSar["operacion"]["CSMDD10"],      
	'CSMDD11'=> $arrParamsSar["operacion"]["CSMDD11"],
	'CSMDD12'=> $arrParamsSar["operacion"]["CSMDD12"],     
	'CSMDD13'=> $arrParamsSar["operacion"]["CSMDD13"],
	'CSMDD14'=> $arrParamsSar["operacion"]["CSMDD14"],
	'CSMDD15'=> $arrParamsSar["operacion"]["CSMDD15"],        
	'CSMDD16'=> $arrParamsSar["operacion"]["CSMDD16"],
	'CSITPRODUCTCODE'=> $arrParamsSar["operacion"]["CSITPRODUCTCODE"],
	'CSITPRODUCTDESCRIPTION'=> $arrParamsSar["operacion"]["CSITPRODUCTDESCRIPTION"],     
	'CSITPRODUCTNAME'=> $arrParamsSar["operacion"]["CSITPRODUCTNAME"],  
	'CSITPRODUCTSKU'=> $arrParamsSar["operacion"]["CSITPRODUCTSKU"],
	'CSITTOTALAMOUNT'=> $arrParamsSar["operacion"]["CSITTOTALAMOUNT"],
	'CSITQUANTITY'=> $arrParamsSar["operacion"]["CSITQUANTITY"],
	'CSITUNITPRICE'=> $arrParamsSar["operacion"]["CSITUNITPRICE"],

	'MININSTALLMENTS'=> $arrParamsSar["operacion"]["MININSTALLMENTS"],
	'MAXINSTALLMENTS'=> $arrParamsSar["operacion"]["MAXINSTALLMENTS"],
	);

$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);	

if($rta["StatusCode"]==-1){
	echo '<div class="alert alert-success"><p>Send Authorize Request Correcto</p></div>';

	$arrSAR=array("comercio"=>$optionsSAR_comercio, "operacion"=>$optionsSAR_operacion);
	$orders_db->updateRecords(array(
		"params_SAR" => json_encode($arrSAR),
		"response_SAR"=>json_encode($rta),
		"url_request"=>$rta["URL_Request"],
		"request_key"=>$rta["RequestKey"],
		"public_request_key"=>$rta["PublicRequestKey"],
		"status" => "SAR ENVIADO",
		"sar"=>1
	),array("id" => $operationid));
}else{
	echo '<div class="alert alert-warning">Send Authorize Request Error</div>';
	$orders_db->updateRecords(array("status" => "SAR ERROR"),array("id" => $operationid));
}

echo "<h3>var_dump de la respuesta de Send Authorize Request</h3>";
var_dump($rta);

?><a href="list.php">Volver a listado</a><?php

include("includes/foot.php");
?>