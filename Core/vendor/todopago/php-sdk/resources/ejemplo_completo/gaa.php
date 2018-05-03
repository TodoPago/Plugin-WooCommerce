<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";
use TodoPago\Sdk;

include("includes/head.php");


$operationid = strip_tags($_GET['ord']);
$orders_db = new FlatDb();
$orders_db->openTable('ordenes');

$orden = $orders_db->getRecords(array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR",  "response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "mode"),array("id" => $operationid));

if($orden[0]["answer_key"]!="0"){
  //común a todas los métodos
  $http_header = array(
    'Authorization'=>$orden[0]["authorizationhttp"],
    'user_agent' => 'PHPSoapClient');
   
  $client = new TodoPago\Sdk($http_header, $orden[0]["mode"]);
  $paramsGaa=array(
      'Merchant'=>$orden[0]["merchantId"],
      'Security' => $orden[0]["security"],
      'RequestKey' => $orden[0]["request_key"],       
      'AnswerKey' => $orden[0]["answer_key"]
  );

  $responseGaa=$client->getAuthorizeAnswer( $paramsGaa );
  
  if($responseGaa["StatusCode"]==-1){
    echo '<div class="alert alert-success"><p>Confirmación correcta</p></div>';

    $orders_db->updateRecords(array("status" => "GAA Correcto", 
      "response_GAA"=>json_encode($responseGaa, true),
      "params_GAA"=>json_encode($paramsGaa, true),
      "gaa"=>1
      ), array("id" => $operationid));    
  }else{
    echo '<div class="alert alert-danger"><p>Error en confirmación</p></div>';
  }

  echo "<h1>Var_dump de Confirmación de transacción:</h1>";
  var_dump( $responseGaa );

}else{
  echo '<div class="alert alert-warning">No se hizo el pago correctamente o no fue realizado</div>';
}
?><a href="list.php">Volver a listado</a><?php

include("includes/foot.php");
?>