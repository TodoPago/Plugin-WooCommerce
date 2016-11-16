<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once dirname(__FILE__).'/ControlFraude.php';

class ControlFraude_Retail extends ControlFraude{

    protected function completeCFVertical(){
        $payDataOperacion = array();
        $payDataOperacion['CSSTCITY'] = $this->getField($this->order->shipping_city);
        $payDataOperacion['CSSTCOUNTRY'] = $this->getField($this->order->shipping_country);
        $payDataOperacion['CSSTEMAIL'] = $this->getField($this->order->billing_email); //Woo con contempla mail de envío
        
        $payDataOperacion['CSSTFIRSTNAME'] = $this->getField($this->order->shipping_first_name);
        if(empty($payDataOperacion['CSSTFIRSTNAME'])){           
            $payDataOperacion['CSSTFIRSTNAME'] = $this->getField($this->order->billing_first_name);
        }
        
        $payDataOperacion['CSSTLASTNAME'] = $this->getField($this->order->shipping_last_name);
        if(empty($payDataOperacion['CSSTLASTNAME'])){           
            $payDataOperacion['CSSTLASTNAME'] = $this->getField($this->order->billing_last_name);
        }

        $payDataOperacion['CSSTPHONENUMBER'] = $this->getField(phone::clean($this->order->shipping_phone));
        $payDataOperacion['CSSTPOSTALCODE'] = $this->getField($this->order->shipping_postcode);
        $payDataOperacion['CSSTSTATE'] = $this->_getStateCode($this->order->shipping_state);
        $payDataOperacion['CSSTSTREET1'] =$this->getField($this->order->billing_address_1);

        //$payDataOperacion['CSMDD12'] = Mage::getStoreConfig('payment/modulodepago2/cs_deadline');
        //$payDataOperacion['CSMDD13'] = $this->getField($this->order->getShippingDescription());
        //$payData ['CSMDD14'] = "";
        //$payData ['CSMDD15'] = "";
        //$payDataOperacion ['CSMDD16'] = $this->getField($this->order->getCuponCode());
        $payDataOperacion = array_merge($this->getMultipleProductsInfo(), $payDataOperacion);
        return $payDataOperacion;
    }

    protected function getCategoryArray($product_id){
        //return Mage::helper('modulodepago2/data')->getCategoryTodopago($product_id);
    }
}
