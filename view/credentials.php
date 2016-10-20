<?php
//require_once(dirname(__FILE__).'/../lib/Data/User.php');
//require_once(dirname(__FILE__).'/../lib/Sdk.php');
//require_once(dirname(__FILE__).'/../lib/Exception/ConnectionException.php');
//require_once(dirname(__FILE__).'/../lib/Exception/ResponseException.php');
//require_once(dirname(__FILE__).'/../lib/Exception/Data/EmptyFieldException.php');

require_once(dirname(__FILE__).'/../lib/vendor/autoload.php');

/*use TodoPago\Data\User;
use TodoPago\Sdk;
use TodoPago\Exception\ConnectionException;
use TodoPago\Exception\ResponseException;
use TodoPago\Exception\Data\EmptyFieldException;*/ 
 

 if((isset($_POST['user']) && !empty($_POST['user'])) &&  (isset($_POST['password']) && !empty($_POST['password']))){

     $userArray = array(
        "user" => trim($_POST['user']), 
        "password" => trim($_POST['password'])
      );

    $http_header = array();
  
    //ambiente developer por defecto 
    $mode = "test";
     if($_POST['mode'] == "prod"){
         $mode = "prod";
     }
    
    try {
        $connector = new \TodoPago\Sdk($http_header, $mode);
        $userInstance = new \TodoPago\Data\User($userArray);
        $rta = $connector->getCredentials($userInstance);
      
        $security = explode(" ", $rta->getApikey()); 
        $response = array( 
                "codigoResultado" => 1,
                "merchandid" => $rta->getMerchant(),
                "apikey" => $rta->getApikey(),
                "security" => $security[1]
        );
        
        
    }catch(\TodoPago\Exception\ResponseException $e){
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );  
        
    }catch(\TodoPago\Exception\ConnectionException $e){
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );
    }catch(\TodoPago\Exception\Data\EmptyFieldException $e){
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );
    }
    echo json_encode($response);

 }else{

    $response = array( 
        "mensajeResultado" => "Ingrese usuario y contraseña de Todo Pago"
    );  
    echo json_encode($response);
 }
    





 




