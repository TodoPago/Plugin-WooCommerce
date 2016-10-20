<?php

//use TodoPago\Sdk as Sdk;

require_once(dirname(__FILE__).'/../../../../wp-blog-header.php');
//require_once(dirname(__FILE__).'/../lib/Sdk.php');
require_once(dirname(__FILE__).'/../lib/vendor/autoload.php');

http_response_code(200);

global $wpdb;
$row = $wpdb -> get_row(
    "SELECT option_value FROM wp_options WHERE option_name = 'woocommerce_todopago_settings'"
);
$arrayOptions = unserialize($row -> option_value);

$esProductivo = $arrayOptions['ambiente'] == "prod";

$http_header = $esProductivo ? $arrayOptions['http_header_prod'] : $arrayOptions['http_header_test'];
$header_decoded = json_decode(html_entity_decode($http_header,TRUE));
$http_header = (!empty($header_decoded)) ? $header_decoded : array("authorization" => $http_header);

$connector = new \TodoPago\Sdk($http_header, $arrayOptions['ambiente']);

//opciones para el método getStatus 
$optionsGS = array('MERCHANT'=>$_GET['merchant'],'OPERATIONID'=>$_GET['order_id']);
$status = $connector->getStatus($optionsGS);

$rta = '';
$refunds = $status['Operations']['REFUNDS'];
$refounds = $status['Operations']['refounds'];

$auxArray = array(
         "refound" => $refounds, 
         "REFUND" => $refunds
         );

    if($refunds != null){  
        $aux = 'REFUND'; 
        $auxColection = 'REFUNDS'; 
    }else{
        $aux = 'refound';
        $auxColection = 'refounds'; 
    }

    
  if (isset($status['Operations']) && is_array($status['Operations']) ) {
      
        foreach ($status['Operations'] as $key => $value) {   
            if(is_array($value) && $key == $auxColection){
                $rta .= "$key: \n";
                foreach ($auxArray[$aux] as $key2 => $value2) {              
                    $rta .= $aux." \n";                
                    if(is_array($value2)){                    
                        foreach ($value2 as $key3 => $value3) {
                            if(is_array($value3)){                    
                                 foreach ($value3 as $key4 => $value4) {
                                    $rta .= "   - $key4: $value4 \n";
                                }
                            }                     
                        }
                    }
                }            
            }else{             
                if(is_array($value)){
                    $rta .= "$key: \n";
                }else{
                    $rta .= "$key: $value \n";
                }
            }
        }
   }else{
       $rta = 'No hay operaciones para esta orden.';
   }
 
echo($rta);
