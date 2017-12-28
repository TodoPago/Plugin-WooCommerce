<?php
namespace TodoPago\Core\ControlFraude;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once dirname(__FILE__).'/ControlFraude.php';

class ControlFraude_Retail extends ControlFraude{

    protected function completeCFVertical(){

        $payDataOperacion = array();
        $payDataOperacion['CSSTCITY'] = $this->getField($this->order->getAddressShipping()->getCity());
        $payDataOperacion['CSSTCOUNTRY'] = $this->getField($this->order->getAddressShipping()->getCountry());
        $payDataOperacion['CSSTPHONENUMBER'] = $this->getField(phone::clean($this->order->getAddressShipping()->getPhoneNumber()));
        $payDataOperacion['CSSTPOSTALCODE'] = $this->getField($this->order->getAddressShipping()->getPostalCode());
        $payDataOperacion['CSSTEMAIL'] = $this->getField($this->order->getCustomerShipping()->getUserEmail()); //Woo con contempla mail de envío
        $payDataOperacion['CSSTFIRSTNAME'] = $this->getField($this->order->getCustomerShipping()->getFirstName());
        $payDataOperacion['CSSTLASTNAME'] = $this->getField($this->order->getCustomerShipping()->getLastName());
        $payDataOperacion['CSSTSTATE'] = $this->_getStateCode($this->order->getAddressShipping()->getState());
        $payDataOperacion['CSSTSTREET1'] =$this->getField($this->order->getAddressShipping()->getStreet());

        if(empty($payDataOperacion['CSSTCITY'])){
        	$payDataOperacion['CSSTCITY'] = $this->getField($this->order->getAddressBilling()->getCity());
        }
        if(empty($payDataOperacion['CSSTCOUNTRY'])){
        	$payDataOperacion['CSSTCOUNTRY'] = $this->getField($this->order->getAddressBilling()->getCountry());
        }
        if(empty($payDataOperacion['CSSTPOSTALCODE'])){
        	$payDataOperacion['CSSTPOSTALCODE'] = $this->getField($this->order->getAddressBilling()->getPostalCode());
        }
        if(empty($payDataOperacion['CSSTFIRSTNAME'])){           
            $payDataOperacion['CSSTFIRSTNAME'] = $this->getField($this->order->getCustomerBilling()->getFirstName());
        }
        if(empty($payDataOperacion['CSSTLASTNAME'])){           
            $payDataOperacion['CSSTLASTNAME'] = $this->getField($this->order->getCustomerBilling()->getLastName());
        }
        if(empty($payDataOperacion['CSSTSTATE'])){
        	$payDataOperacion['CSSTSTATE'] = $this->_getStateCode($this->order->getAddressBilling()->getState());
        }
        if(empty($payDataOperacion['CSSTPHONENUMBER']) || strlen($payDataOperacion['CSSTPHONENUMBER']) < 6){
            $payDataOperacion['CSSTPHONENUMBER'] = $this->getField(phone::clean($this->order->getAddressBilling()->getPhoneNumber()));
        }
        if(empty($payDataOperacion['CSSTSTREET1'])){
            $payDataOperacion['CSSTSTREET1'] = $this->getField($this->order->getAddressBilling()->getStreet());
        }
        if(empty($payDataOperacion['CSSTEMAIL'])){
            $payDataOperacion['CSSTEMAIL'] = $this->getField($this->order->getCustomerBilling()->getUserEmail());
        }

        $payDataOperacion = array_merge($this->getMultipleProductsInfo(), $payDataOperacion);
        return $payDataOperacion;
    }

    protected function getCategoryArray($product_id){
        //return Mage::helper('modulodepago2/data')->getCategoryTodopago($product_id);
    }
}
