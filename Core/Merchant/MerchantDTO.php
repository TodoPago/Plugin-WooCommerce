<?php
namespace TodoPago\Core\Merchant;
use \TodoPago\Core\AbstractClass\AbstractDTO as AbstractDTO;

class MerchantDTO extends AbstractDTO
{
    protected $merchantId;
    protected $apiKey;
    protected $httpHeader;

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
    public function gethttpHeader()
    {
        return $this->httpHeader;
    }
    public function setHttpHeader($httpHeader)
    {
        $this->httpHeader = $httpHeader;
    }
}
