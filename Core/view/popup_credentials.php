<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

$url_referer = $_SERVER['HTTP_REFERER'];
$mode = $_REQUEST['mode']; 
?>

<div id="credentials-login" class="gateway_settings_form" >
	<form method="POST" action="credentials.php">
		<input type="hidden" name="mode" value="<?php echo $mode; ?>">
		<img src="http://www.todopago.com.ar/sites/todopago.com.ar/files/logo.png">
		<p>Ingresa con tus credenciales para Obtener los datos de configuraci&oacute;n</p>    
		<table class="form-table">
			<tr>
				<td><label class="control-label">E-mail</label></td>
				<td><input id="mail" class="form-control" name="mail" type="email" value="" placeholder="E-mail"/></td>		
			</tr>
			<tr>
				<td><label class="control-label">Contrase&ntilde;a</label></td>
				<td><input id="pass" class="form-control" name="pass" type="password" value="" placeholder="Contrase&ntilde;a"/></td>
			</tr>
		</table>
		<button id="btn-form-credentials" style="margin:20%;" class="btn-config-credentials" >Acceder</button>
	</form>
</div>









