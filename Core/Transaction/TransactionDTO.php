<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 12/09/17
 * Time: 12:21
 */

namespace TodoPago\Core\Transaction;

use TodoPago\Core\AbstractClass\AbstractDTO as AbstractDAO;

class TransactionDTO extends AbstractDAO
{
    protected $response;
    protected $params;
    protected $orderID;

    public function __construct($orderID = NULL)
    {
        $this->setOrderID($orderID);
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response_SAR
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params_SAR
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @param mixed $orderID
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;
    }
}
