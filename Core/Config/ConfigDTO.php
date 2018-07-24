<?php
/**
 * Config DTO
 * TodoPago
 * Date: 14/09/17
 * Time: 14:49
 */

namespace TodoPago\Core\Config;

use TodoPago\Core\AbstractClass\AbstractDTO as AbstractDTO;

class ConfigDTO extends AbstractDTO
{
    protected $modo;
    protected $formularioTipo;
    protected $formularioTimeoutEstado;
    protected $carrito;
    protected $googleMaps;
    protected $arrayOpcionales;
    #protected $maxCuotas;
    #protected $formularioTimeoutValor;
    #protected $deadLine;
    protected $url_success;
    protected $url_error;
    protected $url_cancel_order;
    protected $url_checkout;
    protected $pluginPath;
    protected $ecommerceNombre;
    protected $ecommerceVersion;
    protected $cmsVersion;
    protected $pluginVersion;
    protected $isBilletera;


    public function __construct($modo, $formularioTipo, $formularioTimeoutEstado, $carrito, $googleMaps, $pluginPath, $ecommerceNombre, $ecommerceVersion, $cmsVersion, $pluginVersion)
    {
        $this->setModo($modo);
        $this->setFormularioTipo($formularioTipo);
        $this->setFormularioTimeoutEstado($formularioTimeoutEstado);
        $this->setCarrito($carrito);
        $this->setGoogleMaps($googleMaps);
        $this->setPluginPath($pluginPath);
        $this->setEcommerceNombre($ecommerceNombre);
        $this->setEcommerceVersion($ecommerceVersion);
        $this->setCmsVersion($cmsVersion);
        $this->setPluginVersion($pluginVersion);
    }

    public function fillArrayOpcionales($arrayOpcionales)
    {
        $this->setArrayOpcionales($arrayOpcionales);
    }

    /**
     * @return mixed
     */
    public function getModo()
    {
        return $this->modo;
    }

    /**
     * @param mixed $modo
     */
    public function setModo($modo)
    {
        $this->modo = $modo;
    }

    /**
     * @return mixed
     */
    public function getDeadLine()
    {
        return $this->deadLine;
    }

    /**
     * @param mixed $deadLine
     */
    public function setDeadLine($deadLine)
    {
        $this->deadLine = $deadLine;
    }

    /**
     * @return mixed
     */
    public function getFormularioTipo()
    {
        return $this->formularioTipo;
    }

    /**
     * @param mixed $formularioTipo
     */
    public function setFormularioTipo($formularioTipo)
    {
        $this->formularioTipo = $formularioTipo;
    }

    /**
     * @return mixed
     */
    public function getFormularioTimeoutValor()
    {
        return $this->formularioTimeoutValor;
    }

    /**
     * @param mixed $formularioTimeoutValor
     */
    public function setFormularioTimeoutValor($formularioTimeoutValor)
    {
        $this->formularioTimeoutValor = $formularioTimeoutValor;
    }

    /**
     * @return mixed
     */
    public function getFormularioTimeoutEstado()
    {
        return $this->formularioTimeoutEstado;
    }

    /**
     * @param mixed $formularioTimeoutEstado
     */
    public function setFormularioTimeoutEstado($formularioTimeoutEstado)
    {
        $this->formularioTimeoutEstado = $formularioTimeoutEstado;
    }

    /**
     * @return mixed
     */
    public function getMaxCuotas()
    {
        return $this->maxCuotas;
    }

    /**
     * @param mixed $maxCuotas
     */
    public function setMaxCuotas($maxCuotas)
    {
        $this->maxCuotas = $maxCuotas;
    }

    /**
     * @return mixed
     */
    public function getCarrito()
    {
        return $this->carrito;
    }

    /**
     * @param mixed $carrito
     */
    public function setCarrito($carrito)
    {
        $this->carrito = $carrito;
    }

    /**
     * @return mixed
     */
    public function getGoogleMaps()
    {
        return $this->googleMaps;
    }

    /**
     * @param mixed $googleMaps
     */
    public function setGoogleMaps($googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /**
     * @return mixed
     */
    public function getArrayOpcionales()
    {
        return $this->arrayOpcionales;
    }

    /**
     * @param mixed $arrayOpcionales
     */
    public function setArrayOpcionales($arrayOpcionales)
    {
        $this->arrayOpcionales = $arrayOpcionales;
    }

    /**
     * @return mixed
     */
    public function getUrlSuccess()
    {
        return $this->url_success;
    }

    /**
     * @param mixed $url
     */
    public function setUrlSuccess($url)
    {
        $this->url_success = $url;
    }

    /**
     * @return mixed
     */
    public function getUrlError()
    {
        return $this->url_error;
    }

    /**
     * @param mixed $url
     */
    public function setUrlError($url)
    {
        $this->url_error = $url;
    }

    /**
     * @return mixed
     */
    public function getUrlCancelOrder()
    {
        return $this->url_cancel_order;
    }

    /**
     * @param mixed $url
     */
    public function setUrlCancelOrder($url)
    {
        $this->url_cancel_order = $url;
    }

    /**
     * @return mixed
     */
    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    /**
     * @param mixed $pluginPath
     */
    public function setPluginPath($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    /**
     * @return mixed
     */
    public function getEcommerceNombre()
    {
        return $this->ecommerceNombre;
    }

    /**
     * @param mixed $ecommerceNombre
     */
    public function setEcommerceNombre($ecommerceNombre)
    {
        $this->ecommerceNombre = $ecommerceNombre;
    }

    /**
     * @return mixed
     */
    public function getEcommerceVersion()
    {
        return $this->ecommerceVersion;
    }

    /**
     * @param mixed $ecommerceVersion
     */
    public function setEcommerceVersion($ecommerceVersion)
    {
        $this->ecommerceVersion = $ecommerceVersion;
    }

    /**
     * @return mixed
     */
    public function getCmsVersion()
    {
        return $this->cmsVersion;
    }

    /**
     * @param mixed $cmsVersion
     */
    public function setCmsVersion($cmsVersion)
    {
        $this->cmsVersion = $cmsVersion;
    }

    /**
     * @return mixed
     */
    public function getPluginVersion()
    {
        return $this->pluginVersion;
    }

    /**
     * @param mixed $pluginVersion
     */
    public function setPluginVersion($pluginVersion)
    {
        $this->pluginVersion = $pluginVersion;
    }

	/**
	 * @return mixed
	 */
	public function getisBilletera() {
		return $this->isBilletera;
	}

	/**
	 * @param mixed $isBilletera
	 */
	public function setIsBilletera( $isBilletera ) {
		$this->isBilletera = $isBilletera;
	}

}