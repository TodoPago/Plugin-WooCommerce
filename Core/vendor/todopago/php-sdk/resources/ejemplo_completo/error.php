<?php
include_once dirname(__FILE__)."/FlatDb.php";
include("includes/head.php");
?>
<div class="alert alert-danger"><p>Pago error</p></div>
<h2>Var dump de GET:</h2>
<?php
var_dump($_GET);
$orders_db = new FlatDb();
$orders_db->openTable('ordenes');
$orders_db->updateRecords(array("status" => "Pago error"),array("id" => $_GET["OPERATIONID"]));

?><a href="list.php">Volver a listado</a><?php

include("includes/foot.php");
?>