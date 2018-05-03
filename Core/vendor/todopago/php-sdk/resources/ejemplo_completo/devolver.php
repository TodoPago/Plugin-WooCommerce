<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";
use TodoPago\Sdk;

include("includes/head.php");

$operationid = strip_tags($_GET['ord']);

$orders_db = new FlatDb();
$orders_db->openTable('ordenes');

$orden = $orders_db->getRecords(array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR", "response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "mode"),array("id" => $operationid));
$responseGaa=json_decode($orden[0]["response_GAA"], true);
$arrOrdenData=json_decode($orden[0]["data"], true);



if($responseGaa["StatusCode"]==-1){
	if(isset($_GET['refound'])){
			//común a todas los métodos
			$http_header = array(
			'Authorization'=>$orden[0]["authorizationhttp"],
			'user_agent' => 'PHPSoapClient');
			 
			//creo instancia de la clase TodoPago
			$connector = new Sdk($http_header, $orden[0]["mode"]);

			if(($_POST['amount']>$arrOrdenData["operacion"]["AMOUNT"] AND $_POST['amount']>0) AND isset($_POST["parcial"]) ):
				echo '<div class="alert alert-danger"><p>Monto a devolver inválido</p></div>';
			elseif(isset($_POST["parcial"])): //Devolución parcial
				echo "<p>Devolución parcial</p>";
				$options = array(
				    "Security" => $orden[0]["security"],
				    "Merchant" => $orden[0]["merchantId"],
				    "RequestKey" => $orden[0]["request_key"],
				    "AMOUNT" => $_POST['amount'],
				    "refound" => 0
				);

				$resp = $connector->returnRequest($options);			
			else: //Devolución total
				echo "<p>Devolución total</p>";

				$options = array(
				    "Security" => $orden[0]["security"],
				    "Merchant" => $orden[0]["merchantId"],
				    "RequestKey" => $orden[0]["request_key"]
				);
				$resp = $connector->voidRequest($options);
			endif;

			if(isset($resp)){
				if($resp['StatusCode']==2011){
					echo '<div class="alert alert-success"><p>Devolución correcta</p></div>';
					$orders_db->updateRecords(array("status" => "Devolución correcta", "refound"=>1), array("id" => $operationid));				
				}else{
					echo '<div class="alert alert-danger"><p>Error al hacer la devolución</p></div>';
					$orders_db->updateRecords(array("status" => "Devolución error"), array("id" => $operationid));
				}

				echo "<h1>Var_dump de la respuesta</h1>";
				var_dump($resp);
			}	
	}else{
		?>
		<div style="width: 50%">
			<form style="float: left" id="activeform" method="POST" action="devolver.php?ord=<?php echo $operationid; ?>&amp;refound">
				<h1>Devolución parcial</h1>
				<p>Monto: <strong>$<?php echo $arrOrdenData["operacion"]["AMOUNT"]; ?></strong></p>
				<label for="amount">Monto a devolver:</label>
				<input id="amount" name="amount" type="text" required /><div class="line"></div>
			
				<input name="parcial" class="btn-primary btn-sm" type="submit" value="Devolver" />
			</form>





			<form style="float: right" id="activeform" method="POST" action="devolver.php?ord=<?php echo $operationid; ?>&amp;refound">
				<h1>Devolución total</h1>
				<p>Monto: <strong>$<?php echo $arrOrdenData["operacion"]["AMOUNT"]; ?></strong></p>		
				<input name="total" class="btn-primary btn-sm" type="submit" value="Devolver" />
			</form>
		</div>
		<?php
	}
}else{
	echo '<div class="alert alert-warning">No fue hecho el GAA o hubo un error al realizarlo</div>';
}

?>
<div style="float: left; width: 100%">
<br />
<a href="list.php">Volver a listado</a>
</div>
<?php

include("includes/foot.php");
?>