<?php

namespace TodoPago\Core\Product;

use TodoPago\Core\AbstractClass\AbstractModel as AbstractModel;
use TodoPago\Core\Utils\CustomValidator;

class ProductModel extends AbstractModel
{
    protected $productCode;
    protected $productDescription;
    protected $productName;
    protected $productSKU;
    protected $totalAmount;
    protected $quantity;
    protected $price;
    protected $customValidator;

    public function __construct(ProductDTO $productDTO = NULL)
    {
        $this->setCustomValidator(new CustomValidator('Productos'));
    }

    public function setCustomValidator(CustomValidator $customValidator)
    {
        $this->customValidator = $customValidator;
    }

    public function getCustomValidator()
    {
        return $this->customValidator;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }

    public function setProductCode($productCode)
    {
        $productCode = $this->getCustomValidator()->validateEmpty($productCode, 'Product Code');
        $productCode = $this->getCustomValidator()->validateString($productCode, 'Product Code');
        $this->productCode = $productCode;
    }

    public function getProductDescription()
    {
        return $this->productDescription;
    }

    public function setProductDescription($productDescription)
    {
        $productDescription = $this->getCustomValidator()->validateEmpty($productDescription, 'Product Description');
        $productDescription = $this->getCustomValidator()->validateString($productDescription, 'Product Description');
        $this->productDescription = $productDescription;
    }

    public function getProductName()
    {
        return $this->productName;
    }

    public function setProductName($productName)
    {
        $productName = $this->getCustomValidator()->validateEmpty($productName, 'Product Name');
        $productName = $this->getCustomValidator()->validateString($productName, 'Product Name');
        $this->productName = $productName;
    }

    public function getProductSKU()
    {
        return $this->productSKU;
    }

    public function setProductSKU($productSKU)
    {
        $productSKU = $this->getCustomValidator()->validateString($productSKU, 'SKU');
        $this->productSKU = $productSKU;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setTotalAmount($totalAmount)
    {
        $totalAmount = $this->getCustomValidator()->validateString($totalAmount, 'Amount');
        $this->totalAmount = $totalAmount == '0' ? '0.00' : $totalAmount;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $quantity = $this->getCustomValidator()->validateEmpty($quantity, 'Quantity');
        $quantity = $this->getCustomValidator()->validateString($quantity, 'Quantity');
        $this->quantity = $quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $price = $this->getCustomValidator()->validateString($price, 'Price');
        $this->price = $price == '0' ? '0.00' : $price;
    }

}
