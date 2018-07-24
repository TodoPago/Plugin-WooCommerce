<?php
/**
 * Config Model
 * TodoPago
 * Date: 14/09/17
 * Time: 14:48
 */

namespace TodoPago\Core\Config;

use TodoPago\Core\Exception\ExceptionBase;
use TodoPago\Core\Utils\CustomValidator;
use TodoPago\Utils\Constantes as Constantes;

require_once dirname(__DIR__) . "/vendor/autoload.php";

class ConfigModel extends \TodoPago\Core\AbstractClass\AbstractModel
{
    protected $modo;
    protected $deadLine;
    protected $formularioTipo;
    protected $formularioTimeoutEstado;
    protected $timeoutValor;
    protected $enabledTimeoutForm;
    protected $maxCuotas;
    protected $enabledCuotas;
    protected $cleanCart;
    protected $carrito;
    protected $googleMaps;
    protected $arrayOpcionales = Array();
    protected $url_success;
    protected $url_error;
    protected $url_cancel_order;
    protected $url_checkout;
    protected $plugin_url_path;
    protected $customValidator;
    protected $ecommerceNombre;
    protected $ecommerceVersion;
    protected $cmsVersion;
    protected $pluginVersion;
    protected $paymentMethod;

    public function __construct(ConfigDTO $todoPagoConfigDTO)
    {
        $this->setCustomValidator(new CustomValidator('CONFIG'));
        $this->setModo($todoPagoConfigDTO->getModo());
        $this->setFormularioTipo($todoPagoConfigDTO->getFormularioTipo());
        $this->setFormularioTimeoutEstado($todoPagoConfigDTO->getFormularioTimeoutEstado());
        $this->setCarrito($todoPagoConfigDTO->getCarrito());
        $this->setGoogleMaps($todoPagoConfigDTO->getGoogleMaps());
        $this->setPluginUrlPath($todoPagoConfigDTO->getPluginPath());
        $this->setEcommerceNombre($todoPagoConfigDTO->getEcommerceNombre());
        $this->setEcommerceVersion($todoPagoConfigDTO->getEcommerceVersion());
        $this->setCmsVersion($todoPagoConfigDTO->getCmsVersion());
        $this->setPluginVersion($todoPagoConfigDTO->getPluginVersion());
        if ($todoPagoConfigDTO->getUrlError())
            $this->setUrlError($todoPagoConfigDTO->getUrlError());
        if ($todoPagoConfigDTO->getUrlSuccess())
            $this->setUrlSuccess($todoPagoConfigDTO->getUrlSuccess());
        if ($todoPagoConfigDTO->getUrlCancelOrder())
            $this->setUrlCancelOrder($todoPagoConfigDTO->getUrlCancelOrder());
        if (is_array($todoPagoConfigDTO->getArrayOpcionales()))
            $this->setArrayOpcionales($todoPagoConfigDTO->getArrayOpcionales());
        if($todoPagoConfigDTO->getisBilletera())
        	$this->setPaymentMethod($todoPagoConfigDTO->getisBilletera());
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

    /**
     * @return mixed
     */
    public function getModo()
    {
        return $this->modo;
    }

    /**
     * @param mixed $modo
     * @throws
     */
    public function setModo($modo)
    {
        $modo = strtolower($modo);
        $esperado = array(Constantes::TODOPAGO_PROD, Constantes::TODOPAGO_TEST);
        $modo = $this->getCustomValidator()->validateValue($modo, 'modo', $esperado);
        if ($modo === Constantes::TODOPAGO_TEST) {
            $this->modo = Constantes::TODOPAGO_TEST;
        } elseif ($modo === Constantes::TODOPAGO_PROD) {
            $this->modo = Constantes::TODOPAGO_PROD;
        } else {
            throw new ExceptionBase('Error al setear modo', 'ConfigMode');
        }
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
        $deadLineLocal = (int)$deadLine;
        $deadLineLocal = $this->getCustomValidator()->validateNumber($deadLineLocal, 'DEAD LINE');
        $this->deadLine = (string)$deadLineLocal;
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
     * @throws
     */
    public function setFormularioTipo($formularioTipo)
    {
        $formularioTipoLocal = strtolower($formularioTipo);
        $tiposDeFormularioValidos = array(Constantes::TODOPAGO_HIBRIDO, Constantes::TODOPAGO_EXT);
        $formularioTipoLocal = $this->getCustomValidator()->validateValue($formularioTipoLocal, 'TIPO FORMULARIO', $tiposDeFormularioValidos);
        if ($formularioTipoLocal === Constantes::TODOPAGO_HIBRIDO) {
            $this->formularioTipo = Constantes::TODOPAGO_HIBRIDO;
        } elseif ($formularioTipoLocal === Constantes::TODOPAGO_EXT) {
            $this->formularioTipo = Constantes::TODOPAGO_EXT;
        } else
            throw new ExceptionBase('Error al setear TIPO FORMULARIO', 'ConfigMode');

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
        $formularioTimeoutEstado = $this->getCustomValidator()->validateBinary($formularioTimeoutEstado, 'FORMULARIO TIMEOUT ESTADO');
        $this->formularioTimeoutEstado = $formularioTimeoutEstado;
    }

    /**
     * @return mixed
     */
    public function getTimeoutValor()
    {
        return $this->timeoutValor;
    }

    /**
     * @param mixed $timeoutValor
     */
    public function setTimeoutValor($timeoutValor)
    {
        $timeoutValorLocal = $this->getCustomValidator()->validateNumber((int)$timeoutValor, 'VALOR TIMEOUT');
        $this->timeoutValor = $timeoutValorLocal;
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
        $maxCuotasLocal = (int)$maxCuotas;
        if ($maxCuotasLocal < 1)
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
        $this->carrito = $this->getCustomValidator()->validateBinary($carrito, 'Carrito');
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
        $this->googleMaps = $this->getCustomValidator()->validateBinary($googleMaps, 'GoogleMaps');
    }

    /**
     * @return array
     */
    public function getArrayOpcionales()
    {
        return $this->arrayOpcionales;
    }

    /**
     * @param array $arrayOpcionales
     * Esto valida que el array opcional del DTO contenga ciertas keys.
     * Va al set correspondiente en caso de ser válido y
     * chilla en caso de no serlo.
     */
    public function setArrayOpcionales($arrayOpcionales)
    {
        $this->arrayOpcionales = $arrayOpcionales;
        $arrayBenchmark = array(
            'deadLine' => 0,
            'timeoutValor' => 0,
            'maxCuotas' => 0,
            'enabledTimeoutForm' => false,
            'enabledCuotas' => false
        );
        $arrayLocal = array_intersect_key($arrayOpcionales, $arrayBenchmark);
        if (count($arrayOpcionales) > count($arrayLocal)) {
            echo "\nLas key del array recibido tienen que ser 'deadLine, 'timeoutValor', 'maxCuotas'";
        }
        foreach ($arrayLocal as $propiedad => $valor) {
            $propiedad = 'set' . ucfirst($propiedad);
            $this->{$propiedad}($valor);
        }
    }

    /**
     * @return mixed
     */
    public function getPluginUrlPath()
    {
        return $this->plugin_url_path;
    }

    /**
     * @param mixed $plugin_url_path
     */
    public function setPluginUrlPath($plugin_url_path)
    {
        $this->getCustomValidator()->validateEmpty($plugin_url_path, 'Path del plugin');
        $this->getCustomValidator()->validateString($plugin_url_path, 'Path del plugin');
        $this->plugin_url_path = $plugin_url_path;
    }


    /**
     * @return mixed
     */
    public function getCleanCart()
    {
        return $this->cleanCart;
    }

    /**
     * @param mixed $timeoutForm
     */
    public function setCleanCart($cleanCart)
    {
        $this->cleanCart = $this->getCustomValidator()->validateBinary($cleanCart, 'Limpieza del carrito');
    }

    /**
     * @return mixed
     */
    public function getEnabledTimeoutForm()
    {
        return $this->enabledTimeoutForm;
    }

    /**
     * @param mixed $enabledTimeoutForm
     */
    public function setEnabledTimeoutForm($enabledTimeoutForm)
    {
        $this->enabledTimeoutForm = $this->getCustomValidator()->validateBinary($enabledTimeoutForm, 'Timeout Activado');
    }

    /**
     * @return mixed
     */
    public function getEnabledCuotas()
    {
        return $this->enabledCuotas;
    }

    /**
     * @param mixed $arrayOpcionales
     */
    public function setEnabledCuotas($enabledCuotas)
    {
        $this->enabledCuotas = $this->getCustomValidator()->validateBinary($enabledCuotas, 'Cuotas Activadas');
    }

    /**
     * @return mixed
     */
    public function getUrlSuccess()
    {
        return $this->url_success;
    }

    /**
     * @param mixed $arrayOpcionales
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
     * @param mixed $arrayOpcionales
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
     * @param mixed $arrayOpcionales
     */
    public function setUrlCancelOrder($url)
    {
        $this->url_cancel_order = $url;
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
        $this->ecommerceNombre = $this->getCustomValidator()->validateString(strtoupper($ecommerceNombre), 'eCommerce Nombre');
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
        $this->ecommerceVersion = $this->getCustomValidator()->validateString((string)$ecommerceVersion, 'eCommerce Versión');
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
        $this->cmsVersion = $this->getCustomValidator()->validateString((string)$cmsVersion, 'CSM Versión');
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
        $this->pluginVersion = $this->getCustomValidator()->validateString((string)$pluginVersion, 'Versión Plugin');
    }

	/**
	 * @return mixed
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @param mixed $paymentMethod
	 */
	public function setPaymentMethod( $paymentMethod ) {
		$this->paymentMethod = $this->getCustomValidator()->validateString($paymentMethod, "Tipo de pago");
	}


}
