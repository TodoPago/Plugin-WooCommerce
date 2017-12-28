<?php

namespace TodoPago\Core\Order;
require_once dirname(__DIR__) . "/vendor/autoload.php";

use TodoPago\Core\Address\AddressDTO as AddressDTO;
use TodoPago\Core\Address\AddressModel;
use TodoPago\Core\Customer\CustomerDTO as CustomerDTO;
use TodoPago\Core\Product\ProductDTO;
use TodoPago\Core\Product\ProductModel as ProductModel;
use TodoPago\Core\Utils\CustomValidator;

class OrderModel extends \TodoPago\Core\AbstractClass\AbstractModel
{
    protected $order_id;
    protected $addressBilling;
    protected $addressShipping;
    protected $products;
    protected $total_amount;
    protected $customerBilling;
    protected $customerShipping;
    protected $customValidator;

    public function __construct()
    {
        $this->validateData();
    }

    protected function validateData()
    {

    }

    public function getAddressBilling()
    {
        return $this->addressBilling;
    }

    /**
     * @return mixed
     */
    public function getCustomValidator()
    {
        return $this->customValidator;
    }

    /**
     * @param mixed $customValidator
     */
    public function setCustomValidator(CustomValidator $customValidator)
    {
        $this->customValidator = $customValidator;
    }


    public function setAddressBilling(AddressDTO $addressBilling)
    {
        $addressModel = new AddressModel($addressBilling);
        $this->addressBilling = $addressModel;
    }

    public function getAddressShipping()
    {
        return $this->addressShipping;
    }

    public function setAddressShipping(AddressDTO $addressShipping)
    {
        $addressModel = new AddressModel($addressShipping);
        $this->addressShipping = $addressModel;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products)
    {
        $productModels = array();
        foreach ($products as $Product) {
            $productModel = new ProductModel();
            $productModel->setProductCode($Product->getProductCode());
            $productModel->setProductDescription($Product->getProductDescription());
            $productModel->setProductName($Product->getProductName());
            $productModel->setProductSKU($Product->getProductSKU());
            if (gettype($Product->getTotalAmount()) != 'string')
                $productModel->setTotalAmount((string)$Product->getTotalAmount());
            else
                $productModel->setTotalAmount($Product->getTotalAmount());
            $productModel->setQuantity((string)$Product->getQuantity());
            $productModel->setPrice($Product->getPrice());
            $productModels[] = $productModel;
        }

        $this->products = $productModels;
    }

    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    public function setTotalAmount($amount)
    {
        $this->total_amount = (float)$amount;
    }

    public function getCustomerBilling()
    {
        return $this->customerBilling;
    }

    public function setCustomerBilling(CustomerDTO $customer)
    {
        $customer_model = new \TodoPago\Core\Customer\CustomerModel();
        $customer_model->setFirstName($customer->getFirstName());
        $customer_model->setLastName($customer->getLastName());
        $customer_model->setId(strval($customer->getId()));
        $customer_model->setUserName($customer->getUserName());
        $customer_model->setUserPass($customer->getUserPass());
        $customer_model->setUserEmail($customer->getUserEmail());
        $customer_model->setUserRegistered($customer->getUserRegistered());
        $customer_model->setIpAddress($customer->getIpAddress());

        $this->customerBilling = $customer_model;
    }

    public function getCustomerShipping()
    {
        return $this->customerShipping;
    }

    public function setCustomerShipping(CustomerDTO $customer)
    {
        $customer_shipping = new \TodoPago\Core\Customer\CustomerModel();
        $customer_shipping->setFirstName($customer->getFirstName());
        $customer_shipping->setLastName($customer->getLastName());
        $customer_shipping->setUserEmail($customer->getUserEmail());
        /*$customer_shipping->setId($customer->getId());
        $customer_shipping->setUserName($customer->getUserName());
        $customer_shipping->setUserPass($customer->getUserPass());
        $customer_shipping->setUserRegistered($customer->getUserRegistered());
        $customer_shipping->setIpAddress($customer->getIpAddress());
        */
        $this->customerShipping = $customer_shipping;
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
}
