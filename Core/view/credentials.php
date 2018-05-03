<?php
require_once (dirname(__FILE__) . '/../vendor/autoload.php');
//require_once(dirname(__FILE__).'/../TodoPago/lib/Data/User.php');
//require_once(dirname(__FILE__).'/../TodoPago/lib/Sdk.php');

//require_once(dirname(__FILE__).'/../TodoPago/lib/Exception/ConnectionException.php');
//require_once(dirname(__FILE__).'/../TodoPago/lib/Exception/ResponseException.php');
//require_once(dirname(__FILE__).'/../TodoPago/lib/Exception/Data/EmptyFieldException.php');

use TodoPago\Data\User;
use TodoPago\Sdk;
use TodoPago\Exception\ConnectionException;
use TodoPago\Exception\ResponseException;
use TodoPago\Exception\Data\EmptyFieldException; 


//print_r($_POST);
$mail = filter_var( $_REQUEST['mail'], FILTER_SANITIZE_EMAIL);
$pass = filter_var( $_REQUEST['pass'], FILTER_SANITIZE_STRING); 
$mode = filter_var( $_REQUEST['mode'], FILTER_SANITIZE_STRING);

$userArray = array(
    "user" => trim($mail), 
    "password" => trim($pass)
);


$http_header = array();

try {
    $connector = new Sdk($http_header, $mode);
    $userInstance = new TodoPago\Data\User($userArray);
    $rta = $connector->getCredentials($userInstance);

    $security = explode(" ", $rta->getApikey());
    $response = array(
        "codigoResultado" => 1,
        "merchandid" => $rta->getMerchant(),
        "apikey" => $rta->getApikey(),
        "security" => $security[1]
    );


} catch (TodoPago\Exception\ResponseException $e) {
    $response = array(
        "mensajeResultado" => $e->getMessage()
    );

} catch (TodoPago\Exception\ConnectionException $e) {
    $response = array(
        "mensajeResultado" => $e->getMessage()
    );
} catch (TodoPago\Exception\Data\EmptyFieldException $e) {
    $response = array(
        "mensajeResultado" => $e->getMessage()
    );
}

echo json_encode($response);
exit;

?>




 




