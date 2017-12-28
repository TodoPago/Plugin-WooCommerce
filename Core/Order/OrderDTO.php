<?php
namespace TodoPago\Core\Order;
#require_once("/vendor/autoload.php");

use TodoPago\Core\Address\AddressDTO;
use TodoPago\Core\Customer\CustomerDTO as CustomerDTO;
use TodoPago\Core\Product\ProductDTO;

class OrderDTO extends \TodoPago\Core\AbstractClass\AbstractDTO
{
    protected $order_id;
    protected $addressBilling; // ADDRESS()
    protected $addressShipping; // ADDRESS()
    protected $products; //Array<products>
    protected $total_amount;
    protected $customer_billing; //CUSTOMER()
    protected $customer_shipping; //CUSTOPMER()
    protected $refund_amount;

    public function __construct()
    {
        $this->setAddressShipping(new AddressDTO());
        $this->setAddressBilling(new AddressDTO());
    }

    public function getAddressBilling()
    {
        return $this->addressBilling;
    }

    public function setAddressBilling(AddressDTO $addressBilling)
    {
        $this->addressBilling = $addressBilling;
    }
    public function getAddressShipping()
    {
        return $this->addressShipping;
    }

    public function setAddressShipping(AddressDTO $addressShipping)
    {
        $this->addressShipping = $addressShipping;
    }
    public function getProducts()
    {
        return $this->products;
    }
    public function setProducts($products)
    {
        $this->products = $products;
    }
    public function getTotalAmount()
    {
        return $this->total_amount;
    }
    public function setTotalAmount($amount)
    {
        $this->total_amount = $amount;
    }
    public function getCustomerBilling()
    {
        return $this->customer_billing;
    }
    public function setCustomerBilling(CustomerDTO $customer)
    {
        $this->customer_billing = $customer;
    }
    public function getCustomerShipping()
    {
        return $this->customer_shipping;
    }
    public function setCustomerShipping(CustomerDTO $customer)
    {
        $this->customer_shipping = $customer;
    }
    public function getOrderId()
    {
        return $this->order_id;
    }
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }
    public function getOrderKey()
    {
        return $this->order_key;
    }
    public function setOrderKey($order_key)
    {
        $this->order_key = $order_key;
    }
    // Obtiene el monto a devolver
    public function getRefundAmount()
    {
        return $this->refund_amount;
    }
    // Se aplica monto a devolver
    public function setRefundAmount($refund_amount)
    {
        $this->refund_amount = $refund_amount;
    }
}
