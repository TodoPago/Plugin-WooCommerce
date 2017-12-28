<?php
namespace TodoPago\Core\Merchant;
use TodoPago\Core\AbstractClass\AbstractModel;
use TodoPago\Core\Utils\CustomValidator;

class MerchantModel extends AbstractModel
{
    protected $merchantId;
    protected $apiKey;

    public function __construct()
    {
        $this->validateData();
    }

    protected function validateData()
    {

    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setCustomValidator(CustomValidator $customValidator)
    {
        // TODO: Implement setCustomValidator() method.
    }

}
