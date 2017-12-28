<?php
namespace TodoPago\Core\ControlFraude;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class phone{

    private static function is_zero_based($number){
        return substr($number,0,1)=="0";
    }
    private static function is_local($number){
        return strlen($number)==8;
    }
    private static function is_cel($number){
        return substr($number,0,2)=="15";
    }
    private static function is_complete($number){
        return strlen($number)==10;
    }
    private static function is_country_based($number){
        return substr($number,0,2)=="54";
    }

    public static function clean($number, $logger=null){
        $return = "";
        if($logger != null){
            $logger->writeLog("numero cliente", $number);
        }

        $number = str_replace(array(" ","(",")","-","+"),"",$number);

        if(self::is_country_based($number)) $return = $number;

        if(self::is_cel($number)){
            $number = substr($number,2,strlen($number));
        }
        if(self::is_local($number)) $return = "5411".$number;

        if(self::is_zero_based($number)) $return = "54".substr($number,1,strlen($number));

        if($return == null){
            $return = '54'.$number;
        }

        if($logger != null){
            $logger->writeLog("numero procesado", $number);
        }

        return $return;

    }

}
