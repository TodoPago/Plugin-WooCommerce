<?php
include_once dirname(__FILE__)."/../../vendor/autoload.php";
include_once dirname(__FILE__)."/FlatDb.php";
include("includes/head.php");

$orders_db = new FlatDb();
if(!file_exists("ordenes.tsv"))
	$orders_db->createTable('ordenes',array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR", 	"response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "url_request", "mode", "sar", "pago", "gaa", "refound"));
	$orders_db->openTable('ordenes');

	$ord = $orders_db->getRecords(array("id", "data", "merchantId","security","authorizationhttp","status",  "params_SAR", 	"response_SAR", "second_step", "params_GAA", "response_GAA", "request_key", "public_request_key", "answer_key", "sar", "gaa", "pago", "refound"));
?>
<div class="w-section"></div>
<div id="m-status" style="margin-bottom: 300px">
	<div class="block">
	<a href="./?login" class="btn info">Cambiar credenciales</a><div class="line"></div>

	<?php if(empty($ord)){
		echo '<p>Sin transacciones</p>
		<a href="create.php" class="btn info">Nuevo</a>';
	}else{
	?>	
		<table id="tablelist" class="full tablesorter">
			<thead>
				<tr>
			    	<th class="header">Operacion</th>
			    	<th class="header">Estado</th>
			    	<th class="header">SAR</th>
					<th class="header">Pagar</th>
					<th class="header">GAA</th>
					<th class="header">GetStatus</th>
					<th class="header">Devolver</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($ord as $o):?>
			    	<tr>
			      		<td><?php echo $o['id'];?></td>
			      		<td><?php echo $o['status'];?></td>
						<td>
							<?php
							if($o['sar']==0){
								?><div class="clearfix"></div>
								<a href="sar.php?ord=<?php echo $o['id'];?>" class="btn site btn-sm">Send Authorize Request</a>
							<?php }else{
								echo "OK";
							} ?>
						</td>
						<td>
							<?php
							if($o['pago']==0){
								?><div class="clearfix"></div>
								<a href="pagar.php?ord=<?php echo $o['id'];?>" class="btn site btn-sm">Pagar</a>
							<?php }else{
								echo "OK";
							} ?>
						</td>
						<td>
							<?php
							if($o['gaa']==0){ ?>
								<div class="clearfix"></div>
								<a href="gaa.php?ord=<?php echo $o['id'];?>" class="btn site btn-sm">Get Authorize Answer</a>
							<?php }else{
								echo "OK";
							} ?>
						</td>
			       		<td>
			       			<?php
			       			if($o['gaa']==1){ ?>
			       				<div class="clearfix"></div>
			       				<a href="getstatus.php?ord=<?php echo $o['id'];?>" class="btn success btn-sm">Consultar estado</a>
			       			<?php }else{
								echo "-";
							} ?>
			       		</td>
			       		<td>
			       			<?php
			       			if($o['gaa']==1){ ?>
			       				<div class="clearfix"></div>
			       				<a href="devolver.php?ord=<?php echo $o['id'];?>" class="btn warning site btn-sm">Devolver</a>
			       			<?php }else{
								echo "-";
							} ?>
			       		</td>

			      	</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
			  <tr>
			    <td colspan="7">		    	
					<a href="create.php" class="btn info">Nuevo</a>
			    </td>
			  </tr>
			</tfoot>
		</table>
	<?php } ?>

	</div>
</div>

<?php include("includes/foot.php"); ?>