<?php
#if ( ! defined( 'ABSPATH' ) ) exit;

require_once( "../vendor/autoload.php" );

use TodoPago\Sdk;

$merchant = $_POST['merchant'];

$modo = $_POST['modo'];

$operationId = strval( $_POST['id'] );

$stylesheet = $_POST['css'];

$merchantId = $merchant['merchantId'];

http_response_code( 200 );

$http_header    = $merchant['httpHeader'];
$header_decoded = json_decode( html_entity_decode( $http_header, true ) );
$http_header    = ( ! empty( $header_decoded ) ) ? $header_decoded : array( "authorization" => $http_header );

$connector = new Sdk( $http_header, $modo );

//opciones para el método getStatus 
$optionsGS = array( 'MERCHANT' => $merchantId, 'OPERATIONID' => $operationId );

$status = $connector->getStatus( $optionsGS );

if ( array_key_exists( 'Operations', $status ) ) {
	$refunds = $status['Operations']['REFUNDS'];
}

if ( isset( $refunds ) ) {
	$auxArray = array(
		"REFUND" => $refunds
	);
}
$auxColection = '';
if ( isset( $refunds ) && $refunds !== null ) {
	$aux          = 'REFUND';
	$auxColection = 'REFUNDS';
}

$rta = '<table>';
if ( isset( $status['Operations'] ) && is_array( $status['Operations'] ) ) {
	$rta .= printGetStatus( $status['Operations'] );
} else {
	$rta .= '<tr><td>No hay operaciones para esta orden.<td></tr>';
}
$rta .= '</table>';


function printGetStatus( $array, $indent = 0 ) {
	$rta = '';
	foreach ( $array as $key => $value ) {
		if ( $key !== 'nil' && $key !== "@attributes" ) {
			if ( is_array( $value ) ) {
				$rta .= str_repeat( "-", $indent ) . "$key: <br/>";
				$rta .= printGetStatus( $value, $indent + 2 );
			} else {
				$rta .= str_repeat( "-", $indent ) . "$key: $value <br/>";
			}
		}
	}

	return $rta;
}

?>
<div class="get-status-content">
    <div class="tp-close">X</div>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <img src="https://portal.todopago.com.ar/app/images/logo.png" alt="Todopago"/<br>
    <h2>Estado de la operacion</h2>
    <div class="separador"></div>
	<?php echo $rta; ?>
</div>
