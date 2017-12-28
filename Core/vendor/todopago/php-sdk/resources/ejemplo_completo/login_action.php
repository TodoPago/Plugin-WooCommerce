<?php
	include_once dirname(__FILE__)."/../../vendor/autoload.php";
	use TodoPago\Sdk;
	session_start();
	
	if((isset($_POST['user']) && !empty($_POST['user'])) &&  (isset($_POST['pass']) && !empty($_POST['pass']))){

		$userArray = array(
						"user" => trim($_POST['user']), 
						"password" => trim($_POST['pass'])
						);

		$http_header = array();
		
		//ambiente developer por defecto	
		$mode = "test";
		if($_POST['mode'] == "production"){
			$mode = "prod";
		}



		try {
			$connector = new Sdk($http_header, $mode);

			$userInstance = new TodoPago\Data\User($userArray);

		    $rta = $connector->getCredentials($userInstance);
		    
		    $security = explode(" ", $rta->getApikey());	

			$response = array(	
							"codigoResultado" => 1,
							"merchandid" => $rta->getMerchant(),
							"apikey" => $rta->getApikey(),
							"security" => $security[1],
							"mode" => $mode,
						);
			$_SESSION=$response;


			header("Location: list.php");
		}catch(Exception $e){
			$mensajeResultado = $e->getMessage();
		}
		echo $mensajeResultado;



	}else{
		echo "Ingrese usuario y contraseña de Todo Pago";
	}

?>