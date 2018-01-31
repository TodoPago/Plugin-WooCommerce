<?php
/*
    Plugin Name: TodoPago para WooCommerce
    Description: TodoPago para Woocommerce.
    Version: 1.11.1
    Author: Todo Pago
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('TODOPAGO_PLUGIN_VERSION', '1.11.1');
define('TP_FORM_EXTERNO', 'externo');
define('TP_FORM_HIBRIDO', 'hibrido');

//use TodoPago\Sdk as Sdk;

require_once(dirname(__FILE__) . '/Core/vendor/autoload.php');
require_once(dirname(__FILE__) . '/Core/ControlFraude/ControlFraudeFactory.php');
require_once(dirname(__FILE__) . '/lib/logger.php');


use TodoPago\Core;
use TodoPago\Core\Address\AddressDTO;
use TodoPago\Core\Config\ConfigDTO;
use TodoPago\Core\Customer\CustomerDTO;
use TodoPago\Core\Exception\ExceptionBase;
use TodoPago\Utils\Constantes;

//Llama a la función woocommerce_todopago_init cuando se cargan los plugins. 0 es la prioridad.
add_action('plugins_loaded', 'woocommerce_todopago_init', 0);


function woocommerce_todopago_init()
{

    if (!class_exists('WC_Payment_Gateway')) return;

    if (isset($_GET["TodoPago_redirect"]) && $_GET["TodoPago_redirect"] == "true" && isset($_GET["order"])) {
        $row = get_post_meta($_GET["order"], 'response_SAR', true);
        $response_SAR = unserialize($row);
        if ($_GET["form"] == "ext") {
            header('Location: ' . $response_SAR["URL_Request"]);
            exit;
        } else {
            $res = array("prk" => $response_SAR["PublicRequestKey"]);
        }
        echo json_encode($res);
        exit;
    }

    class WC_TodoPago_Gateway extends WC_Payment_Gateway
    {

        public $tplogger;
        public $core;
        protected $coreConfig;
        public $tipo_formulario;
        const TP_PLUGIN_MADRE = 'WooCommerce';
        const TP_PLUGIN_GITHUB_API = 'https://api.github.com/repos/TodoPago/Plugin-WooCommerce/releases/latest';
        const TP_PLUGIN_GITHUB_REPO = 'https://github.com/TodoPago/Plugin-WooCommerce';

        public function __construct()
        {

            add_action('woocommerce_admin_order_data_after_order_details', array($this, 'todopago_meta_box'));
            $this->id = 'todopago';
            $this->icon = apply_filters('woocommerce_todopago_icon', "http://www.todopago.com.ar/sites/todopago.com.ar/files/pluginstarjeta.jpg");
            $this->medthod_title = 'Todo Pago';
            $this->has_fields = false;
            $this->supports = array(
                'products',
                'refunds'
            );

            $this->init_form_fields();
            $this->init_settings(); //Carga en el array settings los valores de los campos persistidos de la base de datos

            //Datos generales
            $this->version = $this->todopago_getValueOfArray($this->settings, 'version');
            $this->title = "Todo Pago";
            $this->description = $this->todopago_getValueOfArray($this->settings, 'description');
            $this->ambiente = $this->todopago_getValueOfArray($this->settings, 'ambiente');
            $this->clean_carrito = $this->todopago_getValueOfArray($this->settings, 'clean_carrito');
            $this->tipo_segmento = $this->todopago_getValueOfArray($this->settings, 'tipo_segmento');

            //$this -> canal_ingreso  = $this -> settings['canal_ingreso'];
            $this->deadline = $this->todopago_getValueOfArray($this->settings, 'deadline');
            $this->tipo_formulario = $this->todopago_getValueOfArray($this->settings, 'tipo_formulario');
            $this->max_cuotas = $this->todopago_getValueOfArray($this->settings, 'max_cuotas');
            $this->enabledCuotas = $this->todopago_getValueOfArray($this->settings, 'enabledCuotas');

            //Datos credentials;
            $this->credentials = $this->todopago_getValueOfArray($this->settings, 'credentials');
            $this->user = $this->todopago_getValueOfArray($this->settings, 'user');
            $this->password = $this->todopago_getValueOfArray($this->settings, 'password');
            $this->btnCredentials = $this->todopago_getValueOfArray($this->settings, 'btnCredentials');

            //Datos ambiente de test
            $this->http_header_test = $this->todopago_getValueOfArray($this->settings, 'http_header_test');
            $this->security_test = $this->todopago_getValueOfArray($this->settings, 'security_test');
            $this->merchant_id_test = $this->todopago_getValueOfArray($this->settings, 'merchant_id_test');

            //Datos ambiente de producción
            $this->http_header_prod = $this->todopago_getValueOfArray($this->settings, 'http_header_prod');
            $this->security_prod = $this->todopago_getValueOfArray($this->settings, 'security_prod');
            $this->merchant_id_prod = $this->todopago_getValueOfArray($this->settings, 'merchant_id_prod');

            //Datos estado de pedidos
            $this->estado_inicio = $this->todopago_getValueOfArray($this->settings, 'estado_inicio');
            $this->estado_aprobacion = $this->todopago_getValueOfArray($this->settings, 'estado_aprobacion');
            $this->estado_rechazo = $this->todopago_getValueOfArray($this->settings, 'estado_rechazo');
            $this->estado_offline = $this->todopago_getValueOfArray($this->settings, 'estado_offline');

            //Timeout 
            $this->expiracion_formulario_personalizado = $this->todopago_getValueOfArray($this->settings, 'expiracion_formulario_personalizado');
            $this->timeout_limite = $this->todopago_getValueOfArray($this->settings, 'timeout_limite');

            $this->gmaps_validacion = $this->todopago_getValueOfArray($this->settings, 'gmaps_validacion');


            $this->wpnonce_credentials = $this->todopago_getValueOfArray($this->settings, 'wpnonce');

            $this->msg['message'] = "";
            $this->msg['class'] = "";

            //creo la base que administra las direcciones formateadas por Google Maps
            //$this->adressbook = new AdressBook();
            //$this->adressbook->createTable();

            //Llama a la función admin_options definida más abajo
            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            } else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
            }

            //Llamado al first step
            add_action('before_woocommerce_pay', array($this, 'first_step_todopago'));

            //Llamado al second step
            add_action('woocommerce_thankyou', array($this, 'second_step_todopago'));

            $tp_plugin_base_dir = $this->get_tp_name();

            $urlPath = "wp-content/plugins/{$tp_plugin_base_dir}/";

            $this->tplogger = new TodoPagoLogger();

            /*
            $this->expiracion_formulario_personalizado = ($this->expiracion_formulario_personalizado==true)?true:false;
            $this->gmaps_validacion = ($this->gmaps_validacion ==true)?true:false;
            $this->clean_carrito = ($this->clean_carrito ==true)?true:false;
            */

            global $wp_version;
            if ($this->tipo_formulario !== Constantes::TODOPAGO_EXT && $this->tipo_formulario !== Constantes::TODOPAGO_HIBRIDO) {
                $this->setCoreConfig(new ConfigDTO(Constantes::TODOPAGO_TEST, Constantes::TODOPAGO_EXT, false, false, false, $urlPath, self::TP_PLUGIN_MADRE, WC_VERSION, $wp_version, TODOPAGO_PLUGIN_VERSION));
            } else {
                $this->setCoreConfig(new ConfigDTO($this->ambiente, $this->tipo_formulario, $this->expiracion_formulario_personalizado, $this->clean_carrito, false, $urlPath, self::TP_PLUGIN_MADRE, WC_VERSION, $wp_version, TODOPAGO_PLUGIN_VERSION));
            }

            $opcionales = $this->buildOpcionales();
            $this->getCoreConfig()->setArrayOpcionales($opcionales);
            $merchant = $this->buildMerchantDTO();
            $this->core = new Core($this->getCoreConfig(), $merchant);
            $this->core->setTpLogger($this->tplogger);

        }//End __construct

        public function get_tp_name()
        {
            list($tp_plugin_base_dir, $filename) = explode('/', plugin_basename(__FILE__));
            return $tp_plugin_base_dir;
        }


        public function todopago_meta_box(WC_Order $order)
        {
            $neto = (float)$order->get_total();
            $orderTax = (float)$order->get_tax_totals();
            $bruto = $neto + $orderTax;
            $id = $this->getOrderLegacy($order, 'id');
            $paymentMethod = get_post_meta($id, '_payment_method', true);
            $this->core->build_todopago_meta_box($neto, $bruto, $paymentMethod, $id);
        }

        function todopago_getValueOfArray($array, $key)
        {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            } else {
                return FALSE;
            }
        }

        protected function buildMerchantDTO()
        {
            $http_header = $this->getHttpHeader();
            $esProductivo = $this->ambiente == Constantes::TODOPAGO_PROD;
            $apikey = $esProductivo ? $this->security_prod : $this->security_test;
            $merchantId = strval($esProductivo ? $this->merchant_id_prod : $this->merchant_id_test);
            $merchant = new TodoPago\Core\Merchant\MerchantDTO();
            $merchant->setMerchantId($merchantId);
            $merchant->setApiKey($apikey);
            $merchant->setHttpHeader($http_header);
            return $merchant;
        }

        function init_form_fields()
        {

            global $woocommerce;
            require_once $woocommerce->plugin_path() . '/includes/wc-order-functions.php';

            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Habilitar/Deshabilitar',
                    'type' => 'checkbox',
                    'label' => 'Habilitar modulo de pago Todo Pago',
                    'default' => 'no'),
                # 'title' => array(
                #     'title' => 'Título',
                #     'type' => 'text',
                #     'description' => 'Título que el usuario ve durante el checkout',
                #     'default' => 'TodoPago'),
                'description' => array(
                    'title' => 'Descripción',
                    'type' => 'textarea',
                    'description' => 'Descripción que el usuario ve durante el checkout',
                    'default' => 'Paga de manera segura mediante Todo Pago. Solo para la república argentina'),
                'ambiente' => array(
                    'title' => 'Ambiente',
                    'type' => 'select',
                    'description' => 'Seleccione el ambiente con el que desea trabajar',
                    'options' => array(
                        Constantes::TODOPAGO_TEST => 'developers',
                        Constantes::TODOPAGO_PROD => 'produccion')),
                'clean_carrito' => array(
                    'title' => 'Vaciar carrito',
                    'type' => 'select',
                    'description' => 'Vaciar carrito en caso de fallo',
                    'options' => array(
                        true => 'SÍ',
                        false => 'NO')),
                'tipo_segmento' => array(
                    'title' => 'Tipo de Segmento',
                    'type' => 'select',
                    'description' => 'Seleccione el tipo de segmento con el que desea trabajar',
                    'options' => array(
                        /*'retail' => 'Retail',
                'servicios' => 'Servicios',
                'digital_goods' => 'Digital Goods',
                'ticketing' => 'Ticketing')),*/
                        'retail' => 'Retail')),
                /*'canal_ingreso' => array(
            'title' => 'Canal de ingreso del pedido',
            'type' => 'select',
            'options' => array(
                'Web' => 'Web',
                'Mobile' => 'Mobile',
                'Telefonica' => 'Telefonica')),*/
                'deadline' => array(
                    'title' => 'Deadline',
                    'type' => 'text',
                    'description' => 'Dias maximos para la entrega'),

                'tipo_formulario' => array(
                    'title' => 'Elija el fromulario que desea utilizar',
                    'type' => 'select',
                    'description' => 'Puede escojer entre un formulario integrado al comercio o redireccionar al formulario externo',
                    'options' => array(
                        Constantes::TODOPAGO_EXT => 'Externo',
                        Constantes::TODOPAGO_HIBRIDO => 'Integrado'
                    )
                ),
                'enabledCuotas' => array(
                    'title' => 'Habilitar/Deshabilitar cantidad de cuotas',
                    'type' => 'select',
                    'label' => 'Habilitar cuotas maximas',
                    'options' => array(
                        true => 'SÍ',
                        false => 'NO')),
                'max_cuotas' => array(
                    'title' => 'Numero máximo de cuotas',
                    'type' => 'select',
                    'description' => 'Puede escojer entre 1 a 12 cuotas',
                    'options' => array(
                        '12' => 12,
                        '11' => 11,
                        '10' => 10,
                        '9' => 9,
                        '8' => 8,
                        '7' => 7,
                        '6' => 6,
                        '5' => 5,
                        '4' => 4,
                        '3' => 3,
                        '2' => 2,
                        '1' => 1
                    )
                ),

                'credentials_dev' => array(
                    'title' => 'Credenciales Desarrollo',
                    'type' => 'title'),

                'user_dev' => array(
                    'title' => 'User',
                    'type' => 'text',
                    'description' => 'User Todo Pago'),

                'password_dev' => array(
                    'title' => 'Password',
                    'type' => 'text',
                    'description' => 'Password Todo Pago'),

                'btnCredentials_dev' => array(
                    'type' => 'button',
                    'value' => 'Obtener Credenciales',
                    'class' => 'button-primary'),

                'titulo_testing' => array(
                    'title' => 'Ambiente de Developers',
                    'type' => 'title',
                    'description' => 'Datos correspondientes al ambiente de developers',
                    'id' => 'testing_options'),

                'http_header_test' => array(
                    'title' => 'HTTP Header',
                    'type' => 'text',
                    'description' => "API Keys que se obtiene en el portal de Todo Pago. Ejemplo: <b>TODOPAGO 912EC803B2CE49E4A541068D12345678</b>"),
                'security_test' => array(
                    'title' => 'Security',
                    'type' => 'text',
                    'description' => 'API Keys sin TODOPAGO. Ejemplo: <b>912EC803B2CE49E4A541068D12345678</b>'),
                'merchant_id_test' => array(
                    'title' => 'Merchant ID',
                    'type' => 'text',
                    'description' => 'N&uacute;mero de comercio (MerchantId) provisto por el portal de Todo Pago'),

                'credentials_prod' => array(
                    'title' => 'Credenciales Producción',
                    'type' => 'title'),

                'user_prod' => array(
                    'title' => 'User',
                    'type' => 'text',
                    'description' => 'User Todo Pago'),

                'password_prod' => array(
                    'title' => 'Password',
                    'type' => 'text',
                    'description' => 'Password Todo Pago'),

                'btnCredentials_prod' => array(
                    'type' => 'button',
                    'value' => 'Obtener Credenciales',
                    'class' => 'button-primary'),

                'titulo_produccion' => array('title' => 'Ambiente de Producción', 'type' => 'title', 'description' => 'Datos correspondientes al ambiente de producción', 'id' => 'produccion_options'),

                'http_header_prod' => array(
                    'title' => 'HTTP Header',
                    'type' => 'text',
                    'description' => 'API Keys que se obtiene en el portal de Todo Pago. Ejemplo: <b>TODOPAGO 912EC803B2CE49E4A541068D12345678</b>'),
                'security_prod' => array(
                    'title' => 'Security',
                    'type' => 'text',
                    'description' => 'API Keys sin TODOPAGO. Ejemplo: <b>912EC803B2CE49E4A541068D12345678</b>'),
                'merchant_id_prod' => array(
                    'title' => 'Merchant ID',
                    'type' => 'text',
                    'description' => 'N&uacute;mero de comercio (MerchantId) provisto por el portal de Todo Pago'),

                'titulo_estados_pedidos' => array('title' => 'Estados del Pedido', 'type' => 'title', 'description' => 'Datos correspondientes al estado de los pedidos', 'id' => 'estados_pedido_options'),

                'estado_inicio' => array(
                    'title' => 'Estado cuando la transacción ha<br>sido iniciada',
                    'type' => 'select',
                    'options' => wc_get_order_statuses(),
                    'default' => 'wc-pending',
                    'description' => 'Valor por defecto: Pendiente de pago'),
                'estado_aprobacion' => array(
                    'title' => 'Estado cuando la transacción ha<br>sido aprobada',
                    'type' => 'select',
                    'options' => wc_get_order_statuses(),
                    'default' => 'wc-completed',
                    'description' => 'Valor por defecto: Completado'),
                'estado_rechazo' => array(
                    'title' => 'Estado cuando la transacción ha<br>sido rechazada',
                    'type' => 'select',
                    'options' => wc_get_order_statuses(),
                    'default' => 'wc-failed',
                    'description' => 'Valor por defecto: Falló'),
                'estado_offline' => array(
                    'title' => 'Estado cuando la transacción ha<br>sido offline',
                    'type' => 'select',
                    'options' => wc_get_order_statuses()),

                'expiracion_formulario_personalizado' => array(
                    'title' => 'Expiracion formulario personalizado',
                    'type' => 'select',
                    'description' => 'Configurar tiempo de expiración del formulario de pago personalizado',
                    'options' => array(
                        true => 'SÍ',
                        false => 'NO'
                    )
                ),
                'timeout_limite' => array(
                    'title' => 'Tiempo de expiración del formulario de pago',
                    'type' => 'number',
                    'id' => 'timeout_limite',
                    'description' => 'Tiempo maximo en el que se puede realizar el pago en el formulario en milisegundos. Por defecto si no se envia el valor es de 1800000 (30 minutos)',
                    'default' => 1800000
                ),
                /*
                                'gmaps_validacion' => array(
                                    'title' => 'Utilizar Google Maps',
                                    'type' => 'select',
                                    'description' => '¿Desea validar la dirección de compra con Google Maps?',
                                    'options' => array(
                                        true => 'SÍ',
                                        false => 'NO'
                                    )
                                ),
                */
                'wpnonce' => array(
                    'type' => 'hidden',
                    'placeholder' => wp_create_nonce('getCredentials')
                )
            );
        }

        function build_todopago_save_warning()
        {
            ?>
            <style>
                .tp-update-box {
                    text-align: center;
                    font-size: 1.2em;
                    width: 100%;
                    height: 30px;
                    background-color: rgb(255, 231, 62);
                    border: 1px solid rgb(255, 81, 0);
                    color: darkred;
                    border-radius: 2px;
                    line-height: 30px;
                }
            </style>
            <div class="tp-update-box">
                Para completar este update, es necesario guardar la configuración del plugin.
            </div>
            <?php
        }

        //Muestra el título e imprime el formulario de configuración del plugin en la página de ajustes
        public function admin_options()
        {
            echo '<h3> TodoPago </h3>';
            echo '<p> Medio de pago Todo Pago </p>';
            echo '<table class="form-table">';
            do_action('todopago_warning');
            $this->generate_settings_html(); //Generate the HTML For the settings form.
            echo '</table><br>';
            $urlCredentials = plugins_url('js/credentials.js', __FILE__);
            echo '<script type="text/javascript" src="' . $urlCredentials . '"></script>';

            $plugin_config = plugins_url('js/plugin_config.js', __FILE__);
            echo '<script type="text/javascript" src="' . $plugin_config . '"></script>';

            $urlCredentialsPhp = wp_nonce_url(plugins_url('view/credentials.php', __FILE__), "todopago_getcredentials_config_form");
            echo '<script type="text/javascript">var BASE_URL_CREDENTIAL = "' . $urlCredentialsPhp . '";</script>';
            echo '<style>.wp-admin select{height: 35px !important}</style>';
        }

        //Se ejecuta luego de Finalizar compra -> Realizar el pago
        function first_step_todopago()
        {
            if (isset($_GET["second_step"])) {
                //Second Step
                return $this->second_step_todopago();
            } else {

                global $woocommerce;
                $order_id = $woocommerce->session->__get('order_awaiting_payment');

                if ($order_id === null) {
                    $order_key = $_GET['key'];
                    $order_id = wc_get_order_id_by_order_key($order_key);
                }

                $tpLog = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $this->getMerchant(), $order_id, true);
                $this->core->setTpLogger($tpLog);
                $order = new WC_Order($order_id);
                $paymentMethod = $this->getOrderLegacy($order, 'payment_method') == Constantes::TODOPAGO_TODOPAGO;
                if ($paymentMethod == Constantes::TODOPAGO_TODOPAGO) {
                    update_post_meta($order_id, 'payment_method', $paymentMethod);
                    $merchant = $this->getCoreMerchant();
                    $todoPagoConfig = $this->getCoreConfig();
                    $return_URL_ERROR = $order->get_checkout_order_received_url() . "&second_step=true&tp_order=" . $order_id;
                    $return_URL_OK = $order->get_checkout_order_received_url() . "&second_step=true&tp_order=" . $order_id;

                    //TODO BORRAR SI REDIRIGE BIEN
                    #$todoPagoConfig->setUrlSuccess($return_URL_OK);
                    #$todoPagoConfig->setUrlError($return_URL_ERROR);
                    #$todoPagoConfig->setUrlCancelOrder($order->get_cancel_order_url());


                    // con estos parametros el core iniciara el sdk
                    //$this->core = new \TodoPago\Core($todoPagoConfig, $merchant);
                    $config = $this->core->getConfigDTO();
                    $config->setUrlSuccess($return_URL_OK);
                    $config->setUrlError($return_URL_ERROR);
                    $config->setUrlCancelOrder($order->get_cancel_order_url());
                    try {
                        $this->core->setConfigModel($config);
                    } catch (ExceptionBase $e) {
                        echo $e->getMessage();
                    }
                    $orderDTO = new \TodoPago\Core\Order\OrderDTO();
                    $orderDTO->setOrderId($order_id);
                    if (isset($_GET['key'])) {
                        $orderDTO->setOrderKey($_GET['key']);
                    }
                    $customerUser = method_exists($order, 'get_customer_user_agent') ? $order->get_customer_user_agent() : $order->customer_user;
                    $logger = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $customerUser != null ? $customerUser : "guest", $order_id, true);
                    $user = $order->get_user();
                    $customerBillingDTO = $this->buildCustomerDTO(Constantes::TODOPAGO_BILLING, $order, $user);
                    $customerShippingDTO = $this->buildCustomerDTO(Constantes::TODOPAGO_SHIPPING, $order);
                    $this->prepare_order($order, $logger);
                    $this->core->setTpLogger($logger);
                    $addressBilling = $this->buildAddressDTO(Constantes::TODOPAGO_BILLING, $order);
                    $addressShipping = $this->buildAddressDTO(Constantes::TODOPAGO_SHIPPING, $order);
                    $products = $this->load_products();
                    $orderDTO = $this->buildOrderDTO($addressBilling, $addressShipping, $products, $order, $customerBillingDTO, $customerShippingDTO);
                    try {
                        $this->core->setOrderModel($orderDTO);
                    } catch (Exception $e) {
                        echo "Error al setear Orden en Core.\nLINEA: " . $e->getLine() . " " . $e->getMessage();
                        $tpLog->error("LINEA: " . $e->getLine() . " " . $e->getMessage());
                        $this->_printErrorMsg('Error al validar datos.');
                    }
                    try {
                        $transactionModel = $this->core->call_sar();
                    } catch (Exception $e) {
                        $tpLog->error("LINEA: " . $e->getLine() . " " . $e->getMessage());
                        $this->_printErrorMsg("Error al validar datos.\n" . $e->getMessage());
                    }


                    // TODO: Usar TransactionResponseDTO
                    if (isset($transactionModel)) {
                        $response = $transactionModel->getResponse();
                        if (isset($response)) {
                            if ($response->StatusCode == -1)
                                $this->core->initFormulario($transactionModel);
                            else
                                if ($response->StatusCode >= 98000 && $response->StatusCode >= 99000)
                                    $this->_printErrorMsg($response->StatusMessage);
                                else
                                    $this->_printErrorMsg($response->StatusMessage);
                        } else {
                            $this->_printErrorMsg();
                        }
                    } else {
                        $this->_printErrorMsg();
                    }
                }
            }
            return true;
        }

        protected function getCoreMerchant()
        {
            $http_header = $this->getHttpHeader();
            $esProductivo = $this->ambiente == "prod";

            $apikey = $esProductivo ? $this->security_prod : $this->security_test;
            $merchantId = strval($esProductivo ? $this->merchant_id_prod : $this->merchant_id_test);
            $merchant = new TodoPago\Core\Merchant\MerchantDTO();
            $merchant->setMerchantId($merchantId);
            $merchant->setApiKey($apikey);
            $merchant->setHttpHeader($http_header);

            return $merchant;
        }


        protected function buildOpcionales()
        {
            $enabledTimeoutForm = $this->settings['expiracion_formulario_personalizado'];
            if ($enabledTimeoutForm === 'SI') {
                add_action('todopago_warning', array($this, 'build_todopago_save_warning'));
                $enabledTimeoutForm = true;
            } else if ($enabledTimeoutForm === 'NO') {
                add_action('todopago_warning', array($this, 'build_todopago_save_warning'));
                $enabledTimeoutForm = false;
            }


            $opcionales = Array();
            $opcionalesBenchmark = array(
                'deadLine' => $this->settings['deadline'],
                'timeoutValor' => $this->settings['timeout_limite'],
                'maxCuotas' => $this->settings['max_cuotas'],
                'enabledTimeoutForm' => $enabledTimeoutForm,
                'enabledCuotas' => $this->settings['enabledCuotas']
            );
            foreach ($opcionalesBenchmark as $parametro => $valor) {
                if (!empty($valor))
                    $opcionales[$parametro] = $valor;
            }
            return $opcionales;
        }


        protected function buildOrderDTO(AddressDTO $addressBillingDTO, AddressDTO $addressShippingDTO, $products, WC_Order $order, CustomerDTO $customerBillingDTO, CustomerDTO $customerShippingDTO)
        {
            $orderDTO = new \TodoPago\Core\Order\OrderDTO();
            $orderDTO->setOrderId($this->getOrderLegacy($order, 'id'));
            $orderDTO->setAddressBilling($addressBillingDTO);
            $orderDTO->setAddressShipping($addressShippingDTO);
            $orderDTO->setProducts($products);
            if (method_exists($order, 'get_total'))
                $orderDTO->setTotalAmount($order->get_total());
            else
                $orderDTO->setTotalAmount($order->order_total);
            $orderDTO->setTotalAmount($this->getOrderLegacy($order, 'total'));
            $orderDTO->setCustomerBilling($customerBillingDTO);
            $orderDTO->setCustomerShipping($customerShippingDTO);
            return $orderDTO;
        }

        protected function buildCustomerDTO($tipo, WC_Order $order, $user = NULL)
        {
            $customerDTO = new CustomerDTO();
            $customerDTO->setFirstName($this->getOrderLegacy($order, $tipo . '_first_name'));
            $customerDTO->setLastName($this->getOrderLegacy($order, $tipo . '_last_name'));
            $customerDTO->setUserEmail($this->getOrderLegacy($order, 'billing_email'));
            $customerDTO->setId(0); // si es guest seteo ID=0
            if (!is_null($user) && $user != false) {
                $customerDTO->setId($user->data->ID);
                $customerDTO->setUserName($user->data->user_login);
                $customerDTO->setUserPass($user->data->user_pass);
                $customerDTO->setUserRegistered($user->data->user_registered);
            }
            return $customerDTO;
        }

        protected function buildAddressDTO($tipo, WC_Order $order)
        {
            $addressDTO = new AddressDTO();
            $addressDTO->setCity($this->getOrderLegacy($order, $tipo . '_city'));
            $addressDTO->setCountry($this->getOrderLegacy($order, $tipo . '_country'));
            $addressDTO->setPostalCode($this->getOrderLegacy($order, $tipo . '_postcode'));
            $addressDTO->setPhoneNumber($this->getOrderLegacy($order, 'billing_phone')); // WC Order no tiene phone para Shipping
            $addressDTO->setState($this->getOrderLegacy($order, $tipo . '_state'));
            $addressDTO->setStreet($this->getOrderLegacy($order, $tipo . '_address_1'));
            return $addressDTO;
        }

        private function load_products()
        {
            global $woocommerce;
            $order_id = $woocommerce->session->__get('order_awaiting_payment');

            if ($order_id === null) {
                $order_key = $_GET['key'];
                $order_id = wc_get_order_id_by_order_key($order_key);
            }

            $order = new WC_Order($order_id);

            $products = array();

            if ($woocommerce->cart->cart_contents != NULL || count($woocommerce->cart->cart_contents) > 0) {
                foreach ($woocommerce->cart->cart_contents as $cart_key => $cart_item_array) {
                    $ProductDTO = new \TodoPago\Core\Product\ProductDTO();
                    if (method_exists($cart_item_array['data'], 'get_title'))
                        $product = new WC_Product($cart_item_array['product_id']);
                    $product_code = (get_the_terms($cart_item_array['product_id'], 'product_cat') && is_string(get_the_terms($cart_item_array['product_id'], 'product_cat'))) ? get_the_terms($cart_item_array['product_id'], 'product_cat') : 'default';
                    if (method_exists($cart_item_array['data'], 'get_title'))
                        $ProductDTO->setProductName($cart_item_array['data']->get_title());
                    else
                        $ProductDTO->setProductName($cart_item_array['data']->post->post_title);
                    if (method_exists($cart_item_array['data'], 'get_short_description'))
                        $description = $cart_item_array['data']->get_short_description();
                    else
                        $description = $cart_item_array['data']->post->post_excerpt;
                    if (empty($description)) {
                        if (method_exists($cart_item_array['data'], 'get_description'))
                            $description = $cart_item_array['data']->get_description();
                        else
                            $description = $cart_item_array['data']->post->post_content;
                        if (empty($description))
                            $description = $ProductDTO->getProductName();
                    }
                    $ProductDTO->setProductCode($product_code);
                    $ProductDTO->setProductDescription($description);
                    if (method_exists($product, 'get_sku'))
                        $sku = $product->get_sku();
                    else
                        $sku = $product->sku;
                    $product_sku = (!empty($sku)) ? $sku : 'default';
                    $ProductDTO->setProductSKU($product_sku);
                    $ProductDTO->setTotalAmount($cart_item_array['line_total']);
                    $ProductDTO->setQuantity($cart_item_array['quantity']);
                    if (method_exists($cart_item_array['data'], 'get_price'))
                        $ProductDTO->setPrice($cart_item_array['data']->get_price());
                    else
                        $ProductDTO->setPrice($cart_item_array['data']->price);
                    $products[] = $ProductDTO;
                }
            } else {
                $items = $order->get_items();
                foreach ($items as $key => $value) {
                    $ProductDTO = new \TodoPago\Core\Product\ProductDTO();
                    // if (is_array($value)) {
                    $product = new WC_Product($value['product_id']);
                    $product_code = (get_the_terms($value['product_id'], 'product_cat')) ? get_the_terms($value['product_id'], 'product_cat') : 'default';
                    $ProductDTO->setProductCode($product_code);
                    $ProductDTO->setProductDescription($value['name']);
                    $ProductDTO->setProductName($value['name']);
                    $ProductDTO->setProductSKU($product->get_sku());
                    $ProductDTO->setTotalAmount($value['line_total']);
                    $ProductDTO->setQuantity($value['qty']);
                    $ProductDTO->setPrice($value['line_subtotal']);
                    $products[] = $ProductDTO;
                    //  }
                }
            }

            return $products;
        }

        private function _obtain_logger($php_version, $woocommerce_version, $todopago_plugin_version, $endpoint, $customer_id, $order_id, $is_payment)
        {
            $this->tplogger->setPhpVersion($php_version);
            global $woocommerce;
            $this->tplogger->setCommerceVersion($woocommerce_version);
            $this->tplogger->setPluginVersion($todopago_plugin_version);
            $this->tplogger->setEndPoint($endpoint);
            $this->tplogger->setCustomer($customer_id);
            $this->tplogger->setOrder($order_id);

            return $this->tplogger->getLogger(true);
        }

        function prepare_order($order, $logger)
        {
            $logger->info('first step');
            $this->setOrderStatus($order, 'estado_inicio');
        }

        //Se ejecuta luego de pagar con el formulario
        function second_step_todopago()
        {
            global $woocommerce;
            if (isset($_GET['tp_order']) || isset($_GET['key'])) {
                $order_id = intval($_GET['tp_order']);

                if (!isset($_GET['tp_order'])) {
                    $order_id = wc_get_order_id_by_order_key($_GET['key']);
                }

                $order = new WC_Order($order_id);
                $customerUser = method_exists($order, 'get_customer_user_agent') ? $order->get_customer_user_agent() : $order->customer_user;
                $logger = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $customerUser != null ? $customerUser : "guest", $order_id, true);
                $this->core->setTpLogger($logger);
                $data_GAA = $this->core->call_gaa($order_id);

                if (isset($_GET['timeout']) && $_GET['timeout'] == "expired") {
                    $this->setOrderStatus($order, 'estado_rechazo');
                    //$this -> _printErrorMsg();
                    $redirect_url = add_query_arg('wc_error', urlencode($_GET['error_message']), $order->get_cancel_order_url_raw());
                    $this->clean_cart($woocommerce, $this->clean_carrito);
                    wp_redirect($redirect_url);
                    exit;
                }

                if (isset($_GET['Error'])) {
                    $this->setOrderStatus($order, 'estado_rechazo');
                    //$this -> _printErrorMsg();
                    $redirect_url = add_query_arg('wc_error', urlencode($_GET['Error']), $order->get_cancel_order_url_raw());
                    $this->clean_cart($woocommerce, $this->clean_carrito);
                    wp_redirect($redirect_url);
                    exit;
                }

                if ($this->getOrderLegacy($order, 'payment_method') == Constantes::TODOPAGO_TODOPAGO) {
                    if ($data_GAA["response_GAA"]['StatusCode'] != -1) {
                        $this->clean_cart($woocommerce, $this->clean_carrito);
                    }
                    ////////////////////////////////////////////////////////////////////
                    $key = $_GET['key'];
                    $post_id = get_post_id_by_key($key);
                    $costo_subtotal = $order->get_total();
                    $costo_total = $data_GAA["response_GAA"]["Payload"]["Request"]["AMOUNTBUYER"];
                    $otros_cargos = $costo_total - $costo_subtotal;
                    update_post_meta($post_id, "_order_total", $costo_total);
                    add_post_meta($post_id, "_otros_cargos", $otros_cargos);
                    ///////////////////////////////////////////////////////////////////

                    return $this->take_action($order, $data_GAA);
                }
            }
        }


        public function take_action(WC_Order $order, $data_GAA)
        {
            if ($data_GAA['response_GAA']['StatusCode'] == -1) {
                global $woocommerce;
                $this->setOrderStatus($order, 'estado_aprobacion');

                //Reducir stock

                if ($woocommerce->version >= 3)
                    wc_reduce_stock_levels($order->get_id());
                else
                    $order->reduce_order_stock();

                //Vaciar carrito

                $woocommerce->cart->empty_cart();
                $id = $this->getOrderLegacy($order, 'id');
                echo "<h2>Operación " . $id . " exitosa</h2>";
                echo "<script>jQuery('.entry-title').html('Compra finalizada');</script>";
                return;
            } else {
                global $woocommerce;
                $this->clean_cart($woocommerce, $this->clean_carrito);
                $this->setOrderStatus($order, 'estado_rechazo');
                $redirect_url = add_query_arg('wc_error', urlencode("Su pago no ha sido procesado. Mensaje de TodoPago:" . $data_GAA['response_GAA']['StatusMessage']), $order->get_cancel_order_url());
                wp_redirect($redirect_url);
            }
        }

        public function _printErrorMsg($msg = null)
        {
            if ($msg != null) {
                echo '<div class="woocommerce-error">Lo sentimos, ha ocurrido un error. ' . $msg . ' <a href="' . home_url() . '" class="wc-backward">Volver a la página de inicio</a></div>';
            } else {
                echo '<div class="woocommerce-error">Lo sentimos, ha ocurrido un error. <a href="' . home_url() . '" class="wc-backward">Volver a la página de inicio</a></div>';
            }
        }

        protected function log_gaa($logger, $response_GAA)
        {
            $logger->info('Mensaje Respuesta ' . json_encode($response_GAA));
        }

        function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);

            if (isset($_GET["pay_for_order"]) && $_GET["pay_for_order"] == true) {

                $result = array(
                    'result' => 'success',
                    'redirect' => get_site_url() . '/?TodoPago_redirect=true&form=ext&tp_order=' . $order_id
                );

            } else {
                $result = array(
                    'result' => 'success',
                    'redirect' => add_query_arg('tp_order', $this->getOrderLegacy($order, 'id'), add_query_arg('key', $this->getOrderLegacy($order, 'order_key'), $this->exists_woocommerce_get_page_id($order)))
                );

            }


            return $result;
        }

        // Devuelve lo que se desa de la orden acorde a la versión del plugin.
        private function getOrderLegacy(WC_Order $order_object, $property_name)
        {
            if (method_exists($order_object, 'get_' . $property_name)) {
                $result = $order_object->{'get_' . $property_name}();
            } else {
                $result = $order_object->{$property_name};
            }
            return $result;
        }

        private function setOrderStatus($order, $statusName)
        {
            global $wpdb;
            $row = $wpdb->get_row(
                "SELECT option_value FROM " . $wpdb->options . " WHERE option_name = 'woocommerce_todopago_settings'"
            );

            $arrayOptions = unserialize($row->option_value);
            //var_dump($a rrayOptions);

            $estado = substr($arrayOptions[$statusName], 3);
            $order->update_status($estado, "Cambio a estado: " . $estado);
            //var_dump($order);
        }

        private function getWsdl($esProductivo)
        {
            $wsdl = $esProductivo ? $this->wsdls_prod : $this->wsdls_test;
            return json_decode(html_entity_decode($wsdl, TRUE), TRUE);
        }

        private function getHttpHeader()
        {
            $esProductivo = $this->ambiente == "prod";
            $http_header = $esProductivo ? $this->http_header_prod : $this->http_header_test;
            $header_decoded = json_decode(html_entity_decode($http_header, TRUE));
            return (!empty($header_decoded)) ? $header_decoded : array("authorization" => $http_header);
        }

        private function getSecurity()
        {

            return $this->ambiente == "prod" ? $this->security_prod : $this->security_test;
        }

        private function getMerchant()
        {

            return $this->ambiente == "prod" ? $this->merchant_id_prod : $this->merchant_id_test;
        }

        private function getOptionsSARComercio($esProductivo, $return_URL_OK, $return_URL_ERROR)
        {
            return array(
                'Security' => $esProductivo ? $this->security_prod : $this->security_test,
                'EncodingMethod' => 'XML',
                'Merchant' => strval($esProductivo ? $this->merchant_id_prod : $this->merchant_id_test),
                'URL_OK' => $return_URL_OK,
                'URL_ERROR' => $return_URL_ERROR
            );
        }

        private function generate_form()
        {
            //$order_key  = $_GET['key'];
            //$order_id =  wc_get_order_id_by_order_key($order_key);
            return '<a class="button" href="' . get_site_url() . '/?TodoPago_redirect=true&form=ext&tp_order=' . $this->core->getOrderModel()->getOrderId() . '" id="submit_todopago_payment_form" style="top:121px;"> Pagar con TodoPago </a>  
              <a class="button cancel" href="' . $this->core->getConfigDTO()->getUrlCancelOrder() . '" style="top:121px;">' . ' Cancelar orden ' . '</a>';
        }


        private function clean_cart($woocommerce, $condition)
        {
            $woocommerce->cart->empty_cart();

            if ($condition != 1) {

                if (isset($_GET['tp_order']) || isset($_GET['key'])) {
                    $order_id = intval($_GET['tp_order']);
                    if (!isset($_GET['tp_order'])) {
                        $order_id = wc_get_order_id_by_order_key($_GET['key']);
                    }
                }
                $order = new WC_Order($order_id);
                $items = $order->get_items();

                foreach ($items as $key => $value) {
                    if (is_array($value)) {
                        $woocommerce->cart->add_to_cart($value['product_id'], $value['qty']);
                    } else {
                        $data = $value->get_data();
                        $woocommerce->cart->add_to_cart($data['product_id'], $data['quantity']);
                    }
                }
            }


        }

        public function process_refund($order_id, $amount = null, $reason = '')
        {
            global $woocommerce;
            //IMPORTANTE EXCEPTIONS: WooCommerce las capturará y las mostrará en un alert
            $tpLog = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $this->getMerchant(), $order_id, true);
            $this->core->setTpLogger($tpLog);


            $order = new WC_Order($order_id);

            //sí la transacción no se completó , no permito reembolsar
            if ($order->get_status() != "completed") {
                throw new exception("No se puede reembolsar una transacción incompleta");
            }

            $orderDTO = new \TodoPago\Core\Order\OrderDTO();
            $orderDTO->setOrderId($order_id);
            $orderDTO->setRefundAmount($amount);

            //$this->core->setOrder($orderDTO);
            $return_response = $this->core->process_refund($orderDTO);

            //Si el servicio no responde según lo esperado, se interrumpe la devolución
            if (!is_array($return_response) || !array_key_exists('StatusCode', $return_response) || !array_key_exists('StatusMessage', $return_response)) {
                throw new Exception("El servicio no responde correctamente");
            }
            if ($return_response['StatusCode'] == Constantes::TODOPAGO_DEVOLUCION_OK) {
                //retorno true para que Woo tome la devolución
                return true;
            } else {
                throw new Exception($return_response["StatusMessage"]);
                //return false;
            }

        }

        private function getGoogleMapsValidator($md5Billing, $md5Shipping) //Instancia Google en caso de no encontrar la ubicación a cargar en la tabla
        {
            $md5Billing = $this->adressbook->findMd5($md5Billing);
            $md5Shipping = $this->adressbook->findMd5($md5Shipping);
            if (empty($md5Billing) || empty($md5Shipping)) {
                return new TodoPago\Client\Google();
            } else
                return null;
        }

        private function SAR_hasher($paramsSAR, $tipoDeCompra)
        {
            if ($tipoDeCompra === 'billing')
                $arrayCompra = array('CSBTSTREET1' => 1, 'CSBTSTATE' => 2, 'CSBTCITY' => 3, 'CSBTCOUNTRY' => 3, 'CSBTPOSTALCODE' => 5);
            elseif ($tipoDeCompra === 'shipping')
                $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
            else {
                $this->tplogger->error("No se recibió un input válido en el array de SAR_hasher()");
                $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
            }
            return md5(implode(",", array_intersect_key($paramsSAR, $arrayCompra)));//convierte un array en string separados por comas y lo pasa a md5
        }

        private function setAddressBookData($originalData, $gResponse, $md5Billing, $md5Shipping)
        {
            $opBilling = $gResponse['billing'];
            $opShipping = $gResponse['shipping'];

            $this->recordAdressValidator($originalData, $opBilling, $md5Billing, "B");

            if ($md5Billing !== $md5Shipping) {
                $this->recordAdressValidator($originalData, $opShipping, $md5Shipping, "S");
            }
        }

        private function getAddressbookData($operationData, $md5Billing, $md5Shipping) //rellena los datos de la operación con la info almacenada en nuestra agenda
        {
            $arrayBilling = $this->adressbook->getData($md5Billing);
            $arrayShipping = $this->adressbook->getData($md5Shipping);

            if (!empty($arrayBilling)) {
                $operationData['CSBTSTREET1'] = $arrayBilling->street;
                $operationData['CSBTSTATE'] = $arrayBilling->state;
                $operationData['CSBTCITY'] = $arrayBilling->city;
                $operationData['CSBTCOUNTRY'] = $arrayBilling->country;
                $operationData['CSBTPOSTALCODE'] = $arrayBilling->postal;
            }
            if (!empty($arrayBilling)) {
                $operationData['CSSTSTREET1'] = $arrayShipping->street;
                $operationData['CSSTSTATE'] = $arrayShipping->state;
                $operationData['CSSTCITY'] = $arrayShipping->city;
                $operationData['CSSTCOUNTRY'] = $arrayShipping->country;
                $operationData['CSSTPOSTALCODE'] = $arrayShipping->postal;
            }
            return $operationData;
        }

        private function recordAdressValidator($originalData, $gResponse, $md5, $type)
        {
            if (!empty($gResponse)) {//sí la respuesta de Google no es vacía
                $arrayDif = $this->compareArray($this->formArray($type), $gResponse);//array que muestra la diferencia de
                //las llaves que no están en la respuesta de Google
                $arrayDifNumber = sizeof($arrayDif);
                $postalCodeKey = 'CS' . $type . 'TPOSTALCODE';
                $postalCode = $originalData[$postalCodeKey];//seteo como default el codigo postal ingresado por el usuario
                $isRecordable = true;

                switch ($arrayDifNumber) {
                    case 0:
                        $postalCode = $gResponse[$postalCodeKey];
                        break;
                    case 1:
                        $isRecordable = array_key_exists($postalCodeKey, $arrayDif);
                        break;
                    default:
                        $isRecordable = false;
                        break;
                }

                if ($isRecordable) {
                    $this->adressbook->recordAddress($md5, $gResponse['CS' . $type . 'TSTREET1'], $gResponse['CS' . $type . 'TSTATE'], $gResponse['CS' . $type . 'TCITY'], $gResponse['CS' . $type . 'TCOUNTRY'], $postalCode);
                }
            }
        }

        private function compareArray($arrayExpected, $arrayActual)
        {//compara dos arrays,si son iguales , devuelve un array vacio
            $result = array_diff_key($arrayExpected, $arrayActual);
            return $result;
        }

        private function formArray($letter)
        {//define un array con las llaves a traer , pasandole la letra correspondiente(shiiping o billing)

            return array('CS' . $letter . 'TSTREET1' => 1, 'CS' . $letter . 'TSTATE' => 2, 'CS' . $letter . 'TCITY' => 3, 'CS' . $letter . 'TCOUNTRY' => 4, 'CS' . $letter . 'TPOSTALCODE' => 5);
        }


        private function exists_woocommerce_get_page_id($order_object)
        {

            $result = $order_object->get_checkout_payment_url(true);

            if ($result == NULL) {
                $result = woocommerce_get_page_id("pay");
            }

            return $result;
        }

        /**
         * @return mixed
         */
        public function getCoreConfig()
        {
            return $this->coreConfig;
        }

        /**
         * @param mixed $coreConfig
         */
        public function setCoreConfig($coreConfig)
        {
            $this->coreConfig = $coreConfig;
        }


    }//End WC_TodoPago_Gateway

    class_alias("WC_TodoPago_Gateway", "todopago");

    //Agrego el campo teléfono de envío para cybersource
    function todopago_custom_override_checkout_fields($fields)
    {
        $fields['shipping']['shipping_phone'] = array(
            'label' => 'Teléfono',
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true
        );

        return $fields;
    }

    add_filter('woocommerce_checkout_fields', 'todopago_custom_override_checkout_fields');

    //Añado el medio de pago TodoPago a WooCommerce
    function woocommerce_add_todopago_gateway($methods)
    {
        $methods[] = 'WC_TodoPago_Gateway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_todopago_gateway');


}//End woocommerce_todopago_init


//Actualización de versión

global $todopago_db_version;
$todopago_db_version = '1.11.1';

function todopago_install()
{
    global $wpdb;

    $table_name = $wpdb->prefix . "todopago_transaccion";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT  EXISTS $table_name (
    id INT NOT NULL AUTO_INCREMENT,
    id_orden INT NULL,
    first_step TEXT NULL,
    params_SAR TEXT NULL,
    response_SAR TEXT NULL,
    second_step TEXT NULL,
    params_GAA TEXT NULL,
    response_GAA TEXT NULL,
    request_key TEXT NULL,
    public_request_key TEXT NULL,
    answer_key TEXT NULL,
    PRIMARY KEY (id)
  ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    global $todopago_db_version;
    add_option('todopago_db_version', $todopago_db_version);
    $core = new Core();
    $core->todopago_core_install();
}

function todopago_update_db_check()
{
    global $todopago_db_version;
    $installed_ver = get_option('todopago_db_version');

    if ($installed_ver == null || $installed_ver != $todopago_db_version) {
        todopago_install();
        update_option('todopago_db_version', $todopago_db_version);
    }

}


add_action('plugins_loaded', 'todopago_update_db_check');


function my_init()
{

    // comment out the next two lines to load the local copy of jQuery
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', false, '1.3.2');
    wp_enqueue_script('jquery');
}

// No eliminar esta linea, en el Readme se indica que esta linea debe  ser descomentada en el caso de tener conflictos con Jquery
//add_action('init', 'my_init');

add_action('wp_ajax_getCredentials', 'getCredentials'); // executed when logged in
add_action('wp_ajax_nopriv_getCredentials', 'getCredentials');

function getCredentials()
{
    $core = new Core();
    $core->get_credentials();
}



function get_post_id_by_key($key)
{

    global $wpdb;

    $data = $wpdb->get_row("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '" . $key . "'");

    return $data->post_id;
}
