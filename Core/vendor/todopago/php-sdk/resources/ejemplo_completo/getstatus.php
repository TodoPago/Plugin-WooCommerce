<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";
use TodoPago\Sdk;

include("includes/head.php");


$operationid = strip_tags($_GET['ord']);
$orders_db = new FlatDb();
$orders_db->openTable('ordenes');

$orden = $orders_db->getRecords(array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR",  "response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "mode"),array("id" => $operationid));
$arrResponseSar=json_decode($orden[0]["response_SAR"], true);

if($arrResponseSar["StatusCode"]==-1){
  //común a todas los métodos
  $http_header = array(
    'Authorization'=>$orden[0]["authorizationhttp"],
    'user_agent' => 'PHPSoapClient');
   
  $client = new TodoPago\Sdk($http_header, $orden[0]["mode"]);
  $paramsGaa=array('MERCHANT'=>$orden[0]["merchantId"], 'OPERATIONID'=>$operationid);
  $responseGaa=$client->getStatus( $paramsGaa );

  echo "<h1>Var_dump de getStatus:</h1>";
  var_dump( $responseGaa );

}else{
  echo '<div class="alert alert-warning">No se hizo el pago correctamente o no fue realizado</div>';
}
?><a href="list.php">Volver a listado</a><?php

include("includes/foot.php");
?>