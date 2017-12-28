<?php
/**
 * Transaction Model
 * TodoPago
 * Date: 12/09/17
 * Time: 12:25
 */

namespace TodoPago\Core\Transaction;

use TodoPago\Core\AbstractClass\AbstractModel;
use TodoPago\Core\Exception\ErrorValue;
use TodoPago\Core\Utils\CustomValidator;


class TransactionModel extends AbstractModel
{
    protected $id;
    protected $id_orden;
    protected $transactionDTO;
    protected $customValidator;
    protected $orderDTO;
    protected $first_step;
    protected $params;
    protected $response;

    public function __construct($id_orden)
    {
        $this->setCustomValidator(new CustomValidator('Transaction'));
        $this->setIdOrden($id_orden);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdOrden()
    {
        return $this->id_orden;
    }

    /**
     * @param mixed $id_orden
     */
    public function setIdOrden($id_orden)
    {
        $this->id_orden = $this->getCustomValidator()->validateNumber($id_orden, 'Order Id');
    }

    /**
     * @return \TodoPago\Core\Utils\CustomValidator
     */
    public function getCustomValidator()
    {
        return $this->customValidator;
    }

    /**
     * @param CustomValidator $customValidator
     */
    public function setCustomValidator(CustomValidator $customValidator)
    {
        $this->customValidator = $customValidator;
    }

    /**
     * @return mixed
     */
    public function getFirstStep()
    {
        return $this->first_step;
    }

    /**
     * @param mixed $first_step
     */
    public function setFirstStep($first_step)
    {
        $this->first_step = $first_step;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param TransactionDTO $params
     */
    public function setParams($params)
    {
        foreach ($params as $param => $valor) {
            if (strtolower($param) === 'comercio')
                $params->{$param} = $this->validateComercio($valor);

            if (strtolower($param) === 'operacion')
                $params->{$param} = $this->validateOperacion($valor);
        }

        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $responseStd = new \stdClass();
        foreach ($response as $param => $valor) {
            if (strtolower($param) === 'statuscode')
                $responseStd->{$param} = $this->getCustomValidator()->validateNumber($valor, $param);
            elseif (strtolower($param) !== 'payload')
                $responseStd->{$param} = $this->getCustomValidator()->validateString($valor, $param);
        }
        $this->response = $responseStd;
    }

    /**
     * @return mixed
     */
    public function getRequestKey()
    {
        return $this->request_key;
    }

    /**
     * @param mixed $request_key
     */
    public function setRequestKey($request_key)
    {
        $this->request_key = $request_key;
    }

    /**
     * @return mixed
     */
    public function getTransactionDTO()
    {
        return $this->transactionDTO;
    }

    /**
     * @param mixed $transactionDTO
     */
    public function setTransactionDTO($transactionDTO)
    {
        $this->transactionDTO = $transactionDTO;
    }

    /**
     * @return mixed
     */
    public function getOrderDTO()
    {
        return $this->orderDTO;
    }

    /**
     * @param mixed $orderDTO
     */
    public function setOrderDTO($orderDTO)
    {
        $this->orderDTO = $orderDTO;
    }

    protected function validateComercio($comercio)
    {
        $comercio = (object)$comercio;
        $paramsBenchmark = new \stdClass();
        $paramsBenchmark->security = "SECURITY";
        $paramsBenchmark->encodingMethod = "ENCODING";
        $paramsBenchmark->merchant = "MERCH";
        $paramsBenchmark->URL_OK = "UNAURL";
        $paramsBenchmark->URL_ERROR = "OTRAURL";
        $paramsBenchmark->AVAILABLEPAYMENTMETHODSIDS = "UNALGO";
        foreach ($paramsBenchmark as $propiedad => $valor) {
            if (!method_exists($comercio, $propiedad))
                $comercio->{$propiedad} = $this->getCustomValidator()->validateEmpty($propiedad, 'COMERCIO');
        }
        return $comercio;
    }

    protected function validateOperacion($operacion)
    {
        $operacion = (object)$operacion;
        $paramsBenchmark = new \stdClass();
        $paramsBenchmark->AMOUNT = '125.38';
        $paramsBenchmark->EMAILCLIENTE = 'decidir@hotmail.com';
        $paramsBenchmark->CSBTCITY = 'Villa General Belgrano'; //Ciudad de facturación, REQUERIDO.
        $paramsBenchmark->CSBTCOUNTRY = 'AR'; //País de facturación. REQUERIDO. Código ISO.
        $paramsBenchmark->CSBTCUSTOMERID = '453458'; //Identificador del usuario al que se le emite la factura. REQUERIDO. No puede contener un correo electrónico.
        $paramsBenchmark->CSBTIPADDRESS = '192.0.0.4'; //IP de la PC del comprador. REQUERIDO.
        $paramsBenchmark->CSBTEMAIL = 'decidir@hotmail.com'; //Mail del usuario al que se le emite la factura. REQUERIDO.
        $paramsBenchmark->CSBTFIRSTNAME = 'Juan';//Nombre del usuario al que se le emite la factura. REQUERIDO.
        $paramsBenchmark->CSBTLASTNAME = 'Perez'; //Apellido del usuario al que se le emite la factura. REQUERIDO.
        $paramsBenchmark->CSBTPHONENUMBER = '541160913988'; //Teléfono del usuario al que se le emite la factura. No utilizar guiones, puntos o espacios. Incluir código de país. REQUERIDO.
        $paramsBenchmark->CSBTPOSTALCODE = ' C1010AAP'; //Código Postal de la dirección de facturación. REQUERIDO.
        $paramsBenchmark->CSBTSTATE = 'B'; //Provincia de la dirección de facturación. REQUERIDO. Ver tabla anexa de provincias.
        $paramsBenchmark->CSBTSTREET1 = 'Cerrito 740'; //Domicilio de facturación (calle y nro). REQUERIDO.
//        $paramsBenchmark->CSBTSTREET2 = 'Piso 8'; //Complemento del domicilio. (piso, departamento). OPCIONAL.
        $paramsBenchmark->CSPTCURRENCY = 'ARS'; //Moneda. REQUERIDO FIXED.
        $paramsBenchmark->CSPTGRANDTOTALAMOUNT = '125.38'; //Con decimales opcional usando el punto como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales. REQUERIDO. (Ejemplos:$125,38-> 125.38 $12-> 12 o 12.00)
        $paramsBenchmark->CSMDD7 = ''; // Fecha registro comprador(num Dias). OPCIONAL.
        #$paramsBenchmark->CSMDD8 = 'Y'; //Usuario Guest? (Y/N). En caso de ser Y, el campo CSMDD9 no deberá enviarse. OPCIONAL.
        #$paramsBenchmark->CSMDD9 = ''; //Customer password Hash: criptograma asociado al password del comprador final. OPCIONAL.
        #$paramsBenchmark->CSMDD10 = ''; //Histórica de compras del comprador (Num transacciones). OPCIONAL.
        #$paramsBenchmark->CSMDD11 = ''; //Customer Cell Phone. OPCIONAL.
        $paramsBenchmark->CSSTCITY = 'rosario'; //Ciudad de envío de la orden. REQUERIDO.
        $paramsBenchmark->CSSTCOUNTRY = ''; //País de envío de la orden. REQUERIDO.
        $paramsBenchmark->CSSTEMAIL = 'jose@gmail.com'; //Mail del destinatario, REQUERIDO.
        $paramsBenchmark->CSSTFIRSTNAME = 'Jose'; //Nombre del destinatario. REQUERIDO.
        $paramsBenchmark->CSSTLASTNAME = 'Perez'; //Apellido del destinatario. REQUERIDO.
        $paramsBenchmark->CSSTPHONENUMBER = '541155893737'; //Número de teléfono del destinatario. REQUERIDO.
        $paramsBenchmark->CSSTPOSTALCODE = '1414'; //Código postal del domicilio de envío. REQUERIDO.
        $paramsBenchmark->CSSTSTATE = 'D'; //Provincia de envío. REQUERIDO. Son de 1 caracter
        $paramsBenchmark->CSSTSTREET1 = 'San Martín 123'; //Domicilio de envío. REQUERIDO.
        #$paramsBenchmark->CSMDD12 = '';//Shipping DeadLine (Num Dias). NO REQUERIDO.
        #$paramsBenchmark->CSMDD13 = '';//Método de Despacho. NO REQUERIDO.
        #$paramsBenchmark->CSMDD14 = '';//Customer requires Tax Bill ? (Y/N). NO REQUERIDO.
        #$paramsBenchmark->CSMDD15 = '';//Customer Loyality Number. NO REQUERIDO.
        #$paramsBenchmark->CSMDD16 = '';//Promotional / Coupon Code. NO REQUERIDO.
        $paramsBenchmark->CSITPRODUCTCODE = 'electronic_good'; //Código de producto. REQUERIDO. Valores posibles(adult_content;coupon;default;electronic_good;electronic_software;gift_certificate;handling_only;service;shipping_and_handling;shipping_only;subscription)
        $paramsBenchmark->CSITPRODUCTDESCRIPTION = 'NOTEBOOK L845 SP4304LA DF TOSHIBA'; //Descripción del producto. REQUERIDO.
        $paramsBenchmark->CSITPRODUCTNAME = 'NOTEBOOK L845 SP4304LA DF TOSHIBA'; //Nombre del producto. REQUERIDO.
        $paramsBenchmark->CSITPRODUCTSKU = 'LEVJNSL36GN'; //Código identificador del producto. REQUERIDO.
        $paramsBenchmark->CSITTOTALAMOUNT = '1254.40'; //CSITTOTALAMOUNT=CSITUNITPRICE*CSITQUANTITY "999999[.CC]" Con decimales opcional usando el punto como separador de decimales. No se permiten comas, ni como separador de miles ni como separador de decimales. REQUERIDO.
        $paramsBenchmark->CSITQUANTITY = '1'; //Cantidad del producto. REQUERIDO.
        $paramsBenchmark->CSITUNITPRICE = '1254.40'; //Formato IDEM CSTITOTALAMOUNT
        foreach ($paramsBenchmark as $propiedad => $valor) {
            if (!property_exists($operacion, $propiedad)) {
                echo "\nPropo: $propiedad";
                $operacion->{$propiedad} = $this->getCustomValidator()->validateEmpty($propiedad, 'OPERACION');
            }
        }
        return $operacion;
    }
}
