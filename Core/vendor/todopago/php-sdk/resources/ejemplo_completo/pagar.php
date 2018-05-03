<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";
use TodoPago\Sdk;
session_start();

$operationid = strip_tags($_GET['ord']);

$orders_db = new FlatDb();
$orders_db->openTable('ordenes');
$orden = $orders_db->getRecords(array('url_request', 'response_SAR'),array('id' => $operationid));

$arrResponseSar=json_decode($orden[0]['response_SAR'], true);

if($arrResponseSar['StatusCode']==-1){
  header("Location: {$orden[0]['url_request']}");
}else{
  echo '<p><div class="alert alert-warning">Faltó hacer el SAR o se hizo y no devolvió un estado correcto</div></p>';
}
?>