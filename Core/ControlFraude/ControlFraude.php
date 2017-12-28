<?php

namespace TodoPago\Core\ControlFraude;
if (!defined('ABSPATH')) exit; // Exit if accessed directly

include_once dirname(__FILE__) . '/phone.php';

use TodoPago\Core\Customer\CustomerModel;
use TodoPago\Core\Order\OrderModel;

abstract class ControlFraude
{

    protected $order;
    private $customer;

    public function __construct(OrderModel $order, CustomerModel $customer)
    {
        $this->order = $order;
        $this->customer = $customer;
    }

    public function getDataCF()
    {
        $datosCF = $this->completeCF();

        return array_merge($datosCF, $this->completeCFVertical());
    }

    private function completeCF()
    {

        $payDataOperacion = array();

        $payDataOperacion['AMOUNT'] = $this->order->getTotalAmount();
        $payDataOperacion['EMAILCLIENTE'] = $this->customer->getUserEmail();
        $payDataOperacion['CSBTCITY'] = $this->getField($this->order->getAddressBilling()->getCity());

        $payDataOperacion['CSBTCOUNTRY'] = $this->order->getAddressBilling()->getCountry();
        $payDataOperacion['CSBTCUSTOMERID'] = $this->customer->getId();
        $payDataOperacion['CSBTIPADDRESS'] = ($this->customer->getIpAddress() == '::1' || $this->customer->getIpAddress() == '') ? '127.0.0.1' : $this->customer->getIpAddress();
        $payDataOperacion['CSBTEMAIL'] = $this->order->getCustomerBilling()->getUserEmail();
        $payDataOperacion['CSBTFIRSTNAME'] = $this->order->getCustomerBilling()->getFirstName();
        $payDataOperacion['CSBTLASTNAME'] = $this->order->getCustomerBilling()->getLastName();
        $payDataOperacion['CSBTPOSTALCODE'] = $this->order->getAddressBilling()->getPostalCode();
        $payDataOperacion['CSBTPHONENUMBER'] = phone::clean($this->order->getAddressBilling()->getPhoneNumber());
        $payDataOperacion['CSBTSTATE'] = $this->_getStateCode($this->order->getAddressBilling()->getState());
        $payDataOperacion['CSBTSTREET1'] = $this->order->getAddressBilling()->getStreet();
        //$payDataOperacion['CSBTSTREET2'] = $this->order->billing_address_2;
        $payDataOperacion['CSPTCURRENCY'] = "ARS";
        $payDataOperacion['CSPTGRANDTOTALAMOUNT'] = number_format($this->order->getTotalAmount(), 2, ".", "");
        //$payDataOperacion['CSMDD6'] = $this->config->get('canaldeingresodelpedido');

        if (!empty($this->customer)) {
            //CSMDD7 - Fecha Registro Comprador (num Dias) - ver que pasa si es guest
            $payDataOperacion['CSMDD7'] = $this->_getDateTimeDiff($this->customer->getUserRegistered());
            //CSMDD8 - Usuario Guest? (S/N). En caso de ser Y, el campo CSMDD9 no deber&acute; enviarse
            $payDataOperacion['CSMDD8'] = "S";
            //CSMDD9 - Customer password Hash: criptograma asociado al password del comprador final
            $payDataOperacion['CSMDD9'] = $this->customer->getUserPass();
        } else {
            $payDataOperacion['CSMDD8'] = "N";
        }

        return $payDataOperacion;
    }

    protected function _getDateTimeDiff($fecha)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        if($date){
            $return = date_diff( $date, new \DateTime() )->format('%a');
        }else{
            $return = '';
        }

        return $return;
    }

    protected function _getStateCode($stateName)
    {
        $array = array(
            "caba" => "C",
            "capital" => "C",
            "ciudad autonoma de buenos aires" => "C",
            "buenos aires" => "B",
            "bs as" => "B",
            "catamarca" => "K",
            "chaco" => "H",
            "chubut" => "U",
            "cordoba" => "X",
            "corrientes" => "W",
            "entre rios" => "R",
            "formosa" => "P",
            "jujuy" => "Y",
            "la pampa" => "L",
            "la rioja" => "F",
            "mendoza" => "M",
            "misiones" => "N",
            "neuquen" => "Q",
            "rio negro" => "R",
            "salta" => "A",
            "san juan" => "J",
            "san luis" => "D",
            "santa cruz" => "Z",
            "santa fe" => "S",
            "santiago del estero" => "G",
            "tierra del fuego" => "V",
            "tucuman" => "T"
        );

        $name = strtolower($stateName);

        $no_permitidas = array("á", "é", "í", "ó", "ú");
        $permitidas = array("a", "e", "i", "o", "u");
        $name = str_replace($no_permitidas, $permitidas, $name);

        return isset($array[$name]) ? $array[$name] : 'C';
    }

    private function _sanitize_string($string)
    {
        $string = htmlspecialchars_decode($string);

        $re = "/\\[(.*?)\\]|<(.*?)\\>/i";
        $subst = "";
        $string = preg_replace($re, $subst, $string);

        $replace = array("!", "'", "\'", "\"", "  ", "$", "\\", "\n", "\r",
            '\n', '\r', '\t', "\t", "\n\r", '\n\r', '&nbsp;', '&ntilde;', ".,", ",.", "+", "%", "-", ")", "(", "°");
        $string = str_replace($replace, '', $string);

        $cods = array('\u00c1', '\u00e1', '\u00c9', '\u00e9', '\u00cd', '\u00ed', '\u00d3', '\u00f3', '\u00da', '\u00fa', '\u00dc', '\u00fc', '\u00d1', '\u00f1');
        $susts = array('Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'Ü', 'ü', 'Ṅ', 'ñ');
        $string = str_replace($cods, $susts, $string);

        $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹", "&");
        $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E", "");
        $string = str_replace($no_permitidas, $permitidas, $string);

        $string = str_replace('#', '', $string);

        return $string;
    }

    protected function getMultipleProductsInfo()
    {
        $payDataOperacion = array();

        ///datos de la orden separados con #
        $productcode_array = array();
        $description_array = array();
        $name_array = array();
        $sku_array = array();
        $totalamount_array = array();
        $quantity_array = array();
        $price_array = array();


        foreach ($this->order->getProducts() as $product) {
            $productcode_array[] = $product->getProductCode();
            $productDescription = $product->getProductDescription();
            $description_array[] = (empty($productDescription)) ? $product->getProductName() : $product->getProductDescription();
            $name_array[] = $product->getProductName();
            $sku_array[] = $product->getProductSKU();
            $totalamount_array[] = number_format($product->getTotalAmount(), 2, '.', '');
            $quantity_array[] = $product->getQuantity();
            $price_array[] = number_format($product->getPrice(), 2, '.', '');
        }

        $payDataOperacion['CSITPRODUCTCODE'] = join('#', $productcode_array);
        $payDataOperacion['CSITPRODUCTDESCRIPTION'] = join("#", $description_array);
        $payDataOperacion['CSITPRODUCTNAME'] = join("#", $name_array);
        $payDataOperacion['CSITPRODUCTSKU'] = join("#", $sku_array);
        $payDataOperacion['CSITTOTALAMOUNT'] = join("#", $totalamount_array);
        $payDataOperacion['CSITQUANTITY'] = join("#", $quantity_array);
        $payDataOperacion['CSITUNITPRICE'] = join("#", $price_array);

        return $payDataOperacion;
    }

    public function getField($datasources)
    {
        $return = "";
        //try{
        $return = $this->_sanitize_string($datasources);
        //}catch(Exception $e){
        //}

        return $return;
    }

    protected abstract function getCategoryArray($productId);

    protected abstract function completeCFVertical();

    private function _setDescription($cart_item_array)
    {

        $result = "product";
        $name = $this->_sanitize_string($cart_item_array['data']->post->post_title);
        $description = $this->_sanitize_string($cart_item_array['data']->post->post_content);
        $shortDescription = $this->_sanitize_string($cart_item_array['data']->post->post_excerpt);

        $name = trim($name);
        $description = trim($description);
        $shortDescription = trim($shortDescription);

        if (empty($description)) {

            if (empty($shortDescription)) {
                $result = strip_tags($name);
                $result = substr($result, 0, 50);
            } else {
                $result = strip_tags($shortDescription);
                $result = substr($result, 0, 50);
            }

        } else {
            $result = substr($description, 0, 50);
        }

        return $result;
    }


}
