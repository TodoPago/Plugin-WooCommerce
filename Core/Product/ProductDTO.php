<?php
namespace TodoPago\Core\Product;
use \TodoPago\Core\AbstractClass\AbstractDTO as AbstractDTO;

class ProductDTO extends AbstractDTO
{
    protected $productCode;
    protected $productDescription;
    protected $productName;
    protected $productSKU;
    protected $totalAmount;
    protected $quantity;
    protected $price;

    public function getProductCode()
    {
        return $this->productCode;
    }
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }
    public function getProductDescription()
    {
        return $this->productDescription;
    }
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;
    }
    public function getProductName()
    {
        return $this->productName;
    }
    public function setProductName($productName)
    {
        $this->productName = $productName;
    }
    public function getProductSKU()
    {
        return $this->productSKU;
    }
    public function setProductSKU($productSKU)
    {
        $this->productSKU = $productSKU;
    }
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }

}
