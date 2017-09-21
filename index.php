<?php
/*
    Plugin Name: TodoPago para WooCommerce
    Description: TodoPago para Woocommerce.
    Version: 1.10.0
    Author: Todo Pago
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('TODOPAGO_PLUGIN_VERSION','1.10.0');
define('TP_FORM_EXTERNO', 'ext');
define('TP_FORM_HIBRIDO', 'hib');
define('TODOPAGO_DEVOLUCION_OK', 2011);
define('TODOPAGO_FORMS_PROD','https://forms.todopago.com.ar/resources/v2/TPBSAForm.min.js');
define('TODOPAGO_FORMS_TEST','https://developers.todopago.com.ar/resources/v2/TPBSAForm.min.js');

//use TodoPago\Sdk as Sdk;

require_once(dirname(__FILE__).'/lib/vendor/autoload.php');
require_once(dirname(__FILE__).'/lib/logger.php');
require_once(dirname(__FILE__).'/lib/ControlFraude/ControlFraudeFactory.php');
require_once(dirname(__FILE__).'/lib/db/AdressBook.php');

//Llama a la función woocommerce_todopago_init cuando se cargan los plugins. 0 es la prioridad.
add_action('plugins_loaded', 'woocommerce_todopago_init', 0);

function woocommerce_todopago_init(){
    
    if(!class_exists('WC_Payment_Gateway')) return;

      if (isset($_GET["TodoPago_redirect"]) && $_GET["TodoPago_redirect"]=="true" && isset($_GET["order"])) {
        $row = get_post_meta($_GET["order"], 'response_SAR', true);
        $response_SAR = unserialize($row);
        if ($_GET["form"]=="ext") {
            header('Location: '.$response_SAR["URL_Request"]);
            exit;
        } else {
            $res = array("prk" => $response_SAR["PublicRequestKey"]);
        }
        echo json_encode($res);
        exit;
    }
    
    class WC_TodoPago_Gateway extends WC_Payment_Gateway{

        public $tplogger;

        public function __construct(){
	
            $this -> id             = 'todopago';
            $this -> icon           = apply_filters('woocommerce_todopago_icon', "http://www.todopago.com.ar/sites/todopago.com.ar/files/pluginstarjeta.jpg");
            $this -> medthod_title  = 'Todo Pago';
            $this -> has_fields     = false;
            $this -> supports = array(
                'products',
                'refunds'
            );

            $this -> init_form_fields();
            $this -> init_settings(); //Carga en el array settings los valores de los campos persistidos de la base de datos

            //Datos generales
            $this -> version          = $this -> todopago_getValueOfArray($this -> settings,'version');
            $this -> title            = "Todo Pago";
            $this -> description      = $this -> todopago_getValueOfArray($this -> settings,'description');
            $this -> ambiente         = $this -> todopago_getValueOfArray($this -> settings,'ambiente');
            $this -> clean_carrito    = $this -> todopago_getValueOfArray($this -> settings,'clean_carrito');
            $this -> tipo_segmento    = $this -> todopago_getValueOfArray($this -> settings,'tipo_segmento');
            
            //$this -> canal_ingreso  = $this -> settings['canal_ingreso'];
            $this -> deadline         = $this -> todopago_getValueOfArray($this -> settings,'deadline');
            $this -> tipo_formulario  = $this -> todopago_getValueOfArray($this -> settings,'tipo_formulario');
            $this -> max_cuotas       = $this -> todopago_getValueOfArray($this -> settings,'max_cuotas');
            $this -> enabledCuotas    = $this -> todopago_getValueOfArray($this -> settings,'enabledCuotas');

            //Datos credentials;
            $this -> credentials      = $this -> todopago_getValueOfArray($this -> settings,'credentials');
            $this -> user             = $this -> todopago_getValueOfArray($this -> settings,'user');
            $this -> password         = $this -> todopago_getValueOfArray($this -> settings,'password');
            $this -> btnCredentials   = $this -> todopago_getValueOfArray($this -> settings,'btnCredentials');

            //Datos ambiente de test
            $this -> http_header_test = $this -> todopago_getValueOfArray($this -> settings,'http_header_test');
            $this -> security_test    = $this -> todopago_getValueOfArray($this -> settings,'security_test');
            $this -> merchant_id_test = $this -> todopago_getValueOfArray($this -> settings,'merchant_id_test');

            //Datos ambiente de producción
            $this -> http_header_prod = $this -> todopago_getValueOfArray($this -> settings,'http_header_prod');
            $this -> security_prod    = $this -> todopago_getValueOfArray($this -> settings,'security_prod');
            $this -> merchant_id_prod = $this -> todopago_getValueOfArray($this -> settings,'merchant_id_prod');

            //Datos estado de pedidos
            $this -> estado_inicio    = $this -> todopago_getValueOfArray($this -> settings,'estado_inicio');
            $this -> estado_aprobacion= $this -> todopago_getValueOfArray($this -> settings,'estado_aprobacion');
            $this -> estado_rechazo   = $this -> todopago_getValueOfArray($this -> settings,'estado_rechazo');
            $this -> estado_offline   = $this -> todopago_getValueOfArray($this -> settings,'estado_offline');
			
            //Timeout 
            $this -> expiracion_formulario_personalizado= $this -> todopago_getValueOfArray($this -> settings,'expiracion_formulario_personalizado');
            $this -> timeout_limite = $this -> todopago_getValueOfArray($this -> settings,'timeout_limite');
            
            $this -> gmaps_validacion = $this -> todopago_getValueOfArray($this -> settings,'gmaps_validacion');

            
            $this -> wpnonce_credentials = $this -> todopago_getValueOfArray($this -> settings,'wpnonce');

            $this -> msg['message'] = "";
            $this -> msg['class'] = "";
            
            //creo la base que administra las direcciones formateadas por Google Maps
            $this -> adressbook = new AdressBook();
            $this -> adressbook -> createTable();

            //Llama a la función admin_options definida más abajo
            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')){
                add_action('woocommerce_update_options_payment_gateways_' . $this -> id, array(&$this, 'process_admin_options'));
            } else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
            }

            //Llamado al first step
            add_action('before_woocommerce_pay', array($this, 'first_step_todopago'));

            //Llamado al second step
            add_action('woocommerce_thankyou', array($this, 'second_step_todopago'));

            $this->tplogger = new TodoPagoLogger();

        }//End __construct

        function todopago_getValueOfArray($array , $key) {     
            if(array_key_exists($key, $array)){  
		return $array[$key];
            }  else {
                return FALSE;
            }
        }

        function init_form_fields(){

            global $woocommerce;
            require_once $woocommerce -> plugin_path() . '/includes/wc-order-functions.php';

            $this -> form_fields = array(
                'version' => array(
                    'title' => 'Version del plugin '.TODOPAGO_PLUGIN_VERSION,
                    'type'=> 'title'),  
                'enabled' => array(
                    'title' => 'Habilitar/Deshabilitar',
                    'type' => 'checkbox',
                    'label' => 'Habilitar modulo de pago TodoPago',
                    'default' => 'no'),
                'description' => array(
                    'title' => 'Descripción',
                    'type' => 'textarea',
                    'description' => 'Descripción que el usuario ve durante el checkout',
                    'default' => 'Paga de manera segura mediante TodoPago<br>Solo para la república argentina'),
                'ambiente' => array(
                    'title' => 'Ambiente',
                    'type' => 'select',
                    'description' => 'Seleccione el ambiente con el que desea trabajar',
                    'options' => array(
                        'test' => 'developers',
                        'prod' => 'produccion')),
            	'clean_carrito' => array(
            		'title' => 'Vaciar carrito',
            		'type' => 'select',
            		'description' => 'Vaciar carrito en caso de fallo',
            		    'options' => array(
            				'si' => 'si',
            				'no' => 'no')),
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
                    'type'=> 'text',
                    'description' => 'Dias maximos para la entrega'),
                
                'tipo_formulario' => array(
                    'title' => 'Elija el fromulario que desea utilizar',
                    'type' => 'select',
                    'description' => 'Puede escojer entre un formulario integrado al comercio o redireccionar al formulario externo',
                    'options' => array(
                        TP_FORM_EXTERNO => 'Externo',
                        TP_FORM_HIBRIDO => 'Integrado'
                    )
                ),             
                'enabledCuotas' => array(
                    'title' => 'Habilitar/Deshabilitar cantidad de cuotas',
                    'type' => 'checkbox',
                    'label' => 'Habilitar cuotas maximas',
                    'default' => 'no'),                
                'max_cuotas' => array(
                    'title' => 'Numero maximo de cuotas',
                    'type'=> 'select',
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
                    'type'=> 'title'),
                
                'user_dev' => array(
                    'title' => 'User',
                    'type'=> 'text',
                    'description' => 'User Todo Pago'),
                
                'password_dev' => array(
                    'title' => 'Password',
                    'type'=> 'text',
                    'description' => 'Password Todo Pago'),
          
                'btnCredentials_dev' => array(
                    'type'=> 'button',
                    'value' => 'Obtener Credenciales',
                    'class' => 'button-primary'),
                
                'titulo_testing' => array( 
                    'title' => 'Ambiente de Developers', 
                    'type' => 'title', 
                    'description' => 'Datos correspondientes al ambiente de developers', 
                    'id' => 'testing_options' ),

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
                    'type'=> 'title'),
                
                'user_prod' => array(
                    'title' => 'User',
                    'type'=> 'text',
                    'description' => 'User Todo Pago'),
                
                'password_prod' => array(
                    'title' => 'Password',
                    'type'=> 'text',
                    'description' => 'Password Todo Pago'),
          
                'btnCredentials_prod' => array(
                    'type'=> 'button',
                    'value' => 'Obtener Credenciales',
                    'class' => 'button-primary'),

                'titulo_produccion' => array( 'title' => 'Ambiente de Producción', 'type' => 'title', 'description' => 'Datos correspondientes al ambiente de producción', 'id' => 'produccion_options' ),

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

                'titulo_estados_pedidos' => array( 'title' => 'Estados del Pedido', 'type' => 'title', 'description' => 'Datos correspondientes al estado de los pedidos', 'id' => 'estados_pedido_options' ),

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
            			'type'  => 'select',
            			'description' => 'Configurar tiempo de expiración del formulario de pago personalizado',
            			'options' => array(
            					'SI' => 'SI',
            					'NO' => 'NO'
            			)
            	),
            	'timeout_limite' => array(
            			'title' => 'Tiempo de expiración del formulario de pago',
            			'type' => 'number',
            			'id'    => 'timeout_limite',
            			'description' => 'Tiempo maximo en el que se puede realizar el pago en el formulario en milisegundos. Por defecto si no se envia el valor es de 1800000 (30 minutos)'),
                
                'gmaps_validacion' => array(
            			'title' => 'Utilizar Google Maps',	
            			'type'  => 'select',
            			'description' => '¿Desea validar la dirección de compra con Google Maps?',
            			'options' => array(
            					'SI' => 'SI',
            					'NO' => 'NO'
            			)
            	),  
                
                'wpnonce' => array(
                        'type'  => 'hidden',
                        'placeholder' => wp_create_nonce( 'getCredentials')
                    )
            );
        }

        //Muestra el título e imprime el formulario de configuración del plugin en la página de ajustes
        public function admin_options(){
            echo '<h3> TodoPago </h3>';
            echo '<p> Medio de pago TodoPago </p>';
            echo '<table class="form-table">';
            $this -> generate_settings_html(); //Generate the HTML For the settings form.
            echo '</table><br>';
           
            $urlCredentials = plugins_url('js/credentials.js', __FILE__);            
            echo '<script type="text/javascript" src="' . $urlCredentials . '"></script>';
            
            $plugin_config = plugins_url('js/plugin_config.js', __FILE__);
            echo '<script type="text/javascript" src="' . $plugin_config . '"></script>';
            
            $urlCredentialsPhp = wp_nonce_url(plugins_url('view/credentials.php', __FILE__),"todopago_getcredentials_config_form"); 
            echo '<script type="text/javascript">var BASE_URL_CREDENTIAL = "'.$urlCredentialsPhp.'";</script>';

            include_once dirname(__FILE__)."/view/status.php";
        }

        //Se ejecuta luego de Finalizar compra -> Realizar el pago
        function first_step_todopago($order_id){
            global $wpdb;
            
            if(isset($_GET["second_step"])){
                //Second Step
                return $this -> second_step_todopago();
            }else{
                global $woocommerce;
                $order_id = $woocommerce->session->__get('order_awaiting_payment');
                
                if($order_id === null){
                 $order_key  = $_GET['key'];
                 $order_id =  wc_get_order_id_by_order_key($order_key);
                }

                $order = new WC_Order($order_id);
                
                if($order->payment_method == 'todopago'){
                    global $woocommerce;
                    $logger = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $order->customer_user!=null?$order->customer_user:"guest", $order_id, true);
                    $this->prepare_order($order, $logger);
                    $paramsSAR = $this->get_paydata($order, $logger);
                    $response_sar = $this->call_sar($paramsSAR, $logger);
                    $this->custom_commerce($wpdb, $order, $paramsSAR, $response_sar);
                }
            }

        }

        //Persiste el RequestKey en la DB
        private function _persistResponse_SAR($order_id, $response_SAR){
            update_post_meta( $order_id, 'response_SAR', serialize($response_SAR));
        }

        private function _obtain_logger($php_version, $woocommerce_version, $todopago_plugin_version, $endpoint, $customer_id, $order_id, $is_payment){
            $this->tplogger->setPhpVersion($php_version);
            global $woocommerce;
            $this->tplogger->setCommerceVersion($woocommerce_version);
            $this->tplogger->setPluginVersion($todopago_plugin_version);
            $this->tplogger->setEndPoint($endpoint);
            $this->tplogger->setCustomer($customer_id);
            $this->tplogger->setOrder($order_id);

            return  $this->tplogger->getLogger(true);
        }

        function prepare_order($order, $logger){
            $logger->info('first step');
            $this->setOrderStatus($order,'estado_inicio');
        }

        function get_paydata($order, $logger){
            $controlFraude = ControlFraudeFactory::get_ControlFraude_extractor('Retail', $order, $order->get_user());
            $datosCs = $controlFraude->getDataCF();

            $home = home_url();

            $arrayHome = explode ("/", $home);
            $return_URL_ERROR = $order->get_checkout_order_received_url()."&second_step=true";

            $return_URL_OK = $order->get_checkout_order_received_url();

            $esProductivo = $this->ambiente == "prod";
            $optionsSAR_comercio = $this->getOptionsSARComercio($esProductivo, $return_URL_OK,$return_URL_ERROR);


            $optionsSAR_operacion = $this->getOptionsSAROperacion($esProductivo, $order);
            $optionsSAR_operacion = array_merge_recursive($optionsSAR_operacion, $datosCs);

            $paramsSAR['comercio'] = $optionsSAR_comercio;
            $paramsSAR['operacion'] = $optionsSAR_operacion;

            $logger->info('params SAR '.json_encode($paramsSAR));

            return $paramsSAR;
        }

        function call_sar($paramsSAR, $logger){
            $logger->debug("call_sar");
            
            $esProductivo = $this->ambiente == "prod";
            $md5Billing = null;
            $md5Shipping = null;
            $paydata_comercial = $paramsSAR['comercio'];
            $paydata_operation = $paramsSAR['operacion'];//acá estan los datos de control de fraude
            
/*            if($this->gmaps_validacion=="SI"){//si uso gmaps,valido los datos de paydata
    		$md5Billing = $this->SAR_hasher($paydata_operation, 'billing');
    		$md5Shipping = $this->SAR_hasher($paydata_operation, 'shipping');
    		$gMapsValidator = $this->getGoogleMapsValidator($md5Billing, $md5Shipping);
            }
           */
            $http_header = $this->getHttpHeader();
            $logger->info("http header: ".json_encode($http_header));
            $connector = new \TodoPago\Sdk($http_header, $this->ambiente);
            $logger->info("Connector: ".json_encode($connector));
            
/*            if($this->gmaps_validacion=='SI'){
                if(isset($gMapsValidator)){
                    $connector->setGoogleClient($gMapsValidator);
                }else{
                    $paydata_operation = $this->getAddressbookData($paydata_operation,$md5Billing,$md5Shipping);
                }
            }*/
            
            $response_sar = $connector->sendAuthorizeRequest($paydata_comercial,$paydata_operation);
            $logger->info('response SAR '.json_encode($response_sar));

            
            if($response_sar["StatusCode"] == 702 && !empty($http_header) && !empty($paydata_comercial['Merchant']) && !empty($paydata_comercial['Security'])){
                $response_sar = $connector->sendAuthorizeRequest($paydata_comercial,$paydata_operation);
                $logger->info('reintento');
                $logger->info('response SAR '.json_encode($response_sar));
            }
            
/*            if(isset($gMapsValidator)){
                $this->setAddressBookData($paydata_operation,$connector->getGoogleClient()->getFinalAddress(), $md5Billing, $md5Shipping);
            }*/
            
            return $response_sar;
        }
        
        function custom_commerce($wpdb, $order, $paramsSAR, $response_sar){
            
        	$id=$this->method_exists_orderkey_id($order,"get_id");
        	$nombre_completo=$paramsSAR["operacion"]["CSBTFIRSTNAME"]." ".$paramsSAR["operacion"]["CSBTLASTNAME"];
                $email=$paramsSAR["operacion"]["CSBTEMAIL"];
                
            $this->_persistResponse_SAR($id, $response_sar, $paramsSAR);

            $wpdb->insert(
                $wpdb->prefix.'todopago_transaccion', 
                array('id_orden'=>$id,
                      'params_SAR'=>json_encode($paramsSAR),
                      'first_step'=>date("Y-m-d H:i:s"),
                      'response_SAR'=>json_encode($response_sar),
                      'request_key'=>$response_sar["RequestKey"],
                      'public_request_key'=>$response_sar['PublicRequestKey']
                     ),
                array('%d','%s','%s','%s','%s')
            );


            if($response_sar["StatusCode"] == -1){
                if ($this->tipo_formulario == TP_FORM_EXTERNO) {
			echo '<script>window.location.href = "'.get_site_url().'/?TodoPago_redirect=true&form=ext&order='.$id.'"</script>';
/*
                    echo '<p> Gracias por su órden, click en el botón de abajo para pagar con TodoPago </p>';
                    echo $this->generate_form($order, $response_sar["URL_Request"]);
*/
                }
                else {
                    $basename = plugin_basename(dirname(__FILE__));
                    $baseurl = plugins_url();
                    $form_dir = "$baseurl/$basename/view/formulario-hibrido";
                    $firstname = $paramsSAR['operacion']['CSSTFIRSTNAME'];
                    $lastname = $paramsSAR['operacion']['CSSTLASTNAME'];
                    $email = $paramsSAR['operacion']['CSSTEMAIL'];
                    $merchant = $paramsSAR['operacion']['MERCHANT'];
                    $amount = $paramsSAR['operacion']['CSPTGRANDTOTALAMOUNT'];


	            //$returnURL = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}".'&second_step=true';
                    
                    $home = home_url("/");

                    $arrayHome = explode ("/", $home); 
	            $return_URL_ERROR = $order->get_checkout_order_received_url()."&second_step=true";

                    if($this->url_after_redirection == "order_received"){
                        $return_URL_OK = $order->get_checkout_order_received_url();
                    }else{
                        $return_URL_OK = $order->get_checkout_order_received_url();
                        //$return_URL_OK = $arrayHome[0].'//'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}".'&second_step=true';  
                        
                    }

                    $env_url = ($this->ambiente == "prod" ? TODOPAGO_FORMS_PROD : TODOPAGO_FORMS_TEST);

                    require 'view/formulario-hibrido/formulario.php';
                }
            }else{
                $this->_printErrorMsg();
            }
        }

        //Se ejecuta luego de pagar con el formulario
        function second_step_todopago(){

            if(isset($_GET['order']) || isset($_GET['key'])){
                $order_id = intval($_GET['order']);
 
                if(!isset($_GET['order'])) {
                    $order_id = wc_get_order_id_by_order_key($_GET['key']);
                }

                $order = new WC_Order($order_id);

if(isset($_GET['timeout']) && $_GET['timeout']=="expired"){
        		
        			$this -> setOrderStatus($order,'estado_rechazo');
        			//$this -> _printErrorMsg();
        			$redirect_url = add_query_arg( 'wc_error', urlencode($_GET['error_message']), $order->get_cancel_order_url() );
        			
        			global $woocommerce;
        			$this->clean_cart($woocommerce, $this->clean_carrito);
        			
        			wp_redirect($redirect_url);
        			return;
        		}

                if($order->payment_method == 'todopago'){
                    global $woocommerce;
                    $logger = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $order->customer_user!=null?$order->customer_user:"guest", $order_id, true);
                    $data_GAA = $this->call_GAA($order_id, $logger);

		    ////////////////////////////////////////////////////////////////////
		    $key=$_GET['key'];
		    $post_id=get_post_id_by_key($key);

		    $costo_subtotal=$order->get_total();
		    $costo_total=$data_GAA["response_GAA"]["Payload"]["Request"]["AMOUNTBUYER"];
		    $otros_cargos=$costo_total-$costo_subtotal;


		    update_post_meta($post_id,"_order_total",$costo_total);
		    add_post_meta($post_id,"_otros_cargos",$otros_cargos);
		    ///////////////////////////////////////////////////////////////////

                    return $this->take_action($order, $data_GAA, $logger);
                }
            }
        }

        function call_GAA($order_id, $logger){
        	global $data_GAA;
        	
        	$row = get_post_meta($order_id, 'response_SAR', true);
        	$esProductivo = $this->ambiente == "prod";
        	$response_SAR = unserialize($row);
        
        	$params_GAA = array (
        			'Security'   => $esProductivo ? $this -> security_prod : $this -> security_test,
        			'Merchant'   => strval($esProductivo ? $this -> merchant_id_prod : $this -> merchant_id_test),
        			'RequestKey' => $response_SAR["RequestKey"],
        			'AnswerKey'  => $_GET['Answer']
        	);
        		
        
        	$esProductivo = $this->ambiente == "prod";
        	$http_header = $this->getHttpHeader();
        	
        	
        	$connector = new \TodoPago\Sdk($http_header, $this -> ambiente);
        
        	
        	$response_GAA = $connector->getAuthorizeAnswer($params_GAA);
        	
        
        	$data_GAA['params_GAA'] = $params_GAA;
        	$data_GAA['response_GAA'] = $response_GAA;
        	
        	if($logger!=null){
        		
        		$logger->info('second step _ ORDER ID: '.$order_id);
        		$logger->info('params GAA '.json_encode($params_GAA));
        		$logger->info("HTTP_HEADER: ".json_encode($http_header));
        		$logger->info('response GAA '.json_encode($response_GAA));
        	}
        	
        	global $woocommerce;
        	
        	if($response_GAA['StatusCode']!=-1){
        		$this->clean_cart($woocommerce,$this->clean_carrito);
        	}
        
        	return $data_GAA;
        }

        public function take_action($order, $data_GAA, $logger){
            global $wpdb;
	    $id = $this->method_exists_orderkey_id($order,"get_id");
            $wpdb->update(
                $wpdb->prefix.'todopago_transaccion',
                array(
                    'second_step'=>date("Y-m-d H:i:s"), // string
                    'params_GAA'=>json_encode($data_GAA['params_GAA']), // string
                    'response_GAA'=>json_encode($data_GAA['response_GAA']), // string
                    'answer_key'=>$_GET['Answer'] //string
                ),
                array('id_orden'=>$id), // int
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%d')
            );

            if ($data_GAA['response_GAA']['StatusCode'] == -1){

                $this -> setOrderStatus($order,'estado_aprobacion');
                $logger->info('estado de orden '.$order->post_status);

                //Reducir stock
                $order->reduce_order_stock();

                //Vaciar carrito
                global $woocommerce;
                $woocommerce->cart->empty_cart();
		$id = $this->method_exists_orderkey_id($order,"get_id");
                echo "<h2>Operación " . $id . " exitosa</h2>";
                echo "<script>jQuery('.entry-title').html('Compra finalizada');</script>";
		return;
            }else{
                global $woocommerce;
 		$this->clean_cart($woocommerce, $this->clean_carrito);

                $this -> setOrderStatus($order,'estado_rechazo');
		$redirect_url = add_query_arg( 'wc_error', urlencode("Su pago no ha sido procesado. Mensaje de TodoPago:" . $data_GAA['response_GAA']['StatusMessage']), $order->get_cancel_order_url() );
		wp_redirect($redirect_url);
            }

        }

        public function _printErrorMsg($msg = null){
		if($msg != null) {
            echo '<div class="woocommerce-error">Lo sentimos, ha ocurrido un error. '.$msg.' <a href="' . home_url() . '" class="wc-backward">Volver a la página de inicio</a></div>';
		} else {
            echo '<div class="woocommerce-error">Lo sentimos, ha ocurrido un error. <a href="' . home_url() . '" class="wc-backward">Volver a la página de inicio</a></div>';
		}
        }

        function process_payment($order_id){
            global $woocommerce;
            $order = new WC_Order( $order_id );

            if(isset($_GET["pay_for_order"])  && $_GET["pay_for_order"] == true) {

                $result = array (     
                    'result' => 'success', 
                    'redirect' => get_site_url().'/?TodoPago_redirect=true&form=ext&order='.$order_id
                );
                
            } else {
                $result = array (     
                     'result' => 'success', 
                     'redirect' => add_query_arg('order', $this->method_exists_orderkey_id($order,"get_id"), add_query_arg('key',$this->method_exists_orderkey_id($order,"get_order_key"),$this->exists_woocommerce_get_page_id($order)))
                );

            }

   
            return $result;
        }
        
        private function method_exists_orderkey_id($order_object,$method_name){
        	
        	$gok="get_order_key";
        	$gi="get_id";
        	$result="";
        	
        	if($method_name==$gok){
        		
        		if(method_exists($order_object,$gok)) {
        			$result = $order_object->get_order_key();
        		} else {
        			$result = $order_object->order_key;
        		}
        		
        	}else{
        		
        		if(method_exists($order_object,$gi)) {
        			$result = $order_object->get_id();
        		} else {
        			$result = $order_object->id;
        		}
        	}
        	
        	return $result;
        	
        }

        private function setOrderStatus($order, $statusName){
            global $wpdb;
            $row = $wpdb -> get_row(
                "SELECT option_value FROM " . $wpdb-> options . " WHERE option_name = 'woocommerce_todopago_settings'"
            );

            $arrayOptions = unserialize($row -> option_value);
            //var_dump($a rrayOptions);

            $estado = substr($arrayOptions[$statusName], 3);
            $order -> update_status($estado, "Cambio a estado: " . $estado);
            //var_dump($order);
        }

        private function getWsdl($esProductivo){
            $wsdl = $esProductivo ? $this -> wsdls_prod : $this -> wsdls_test;
            return json_decode(html_entity_decode($wsdl,TRUE),TRUE);
        }

        public function getHttpHeader(){
            $esProductivo = $this->ambiente == "prod";
            $http_header = $esProductivo ? $this -> http_header_prod : $this -> http_header_test;
            $header_decoded = json_decode(html_entity_decode($http_header,TRUE));
            return (!empty($header_decoded)) ? $header_decoded : array("authorization" => $http_header);
        }

        private function getSecurity() {
            return $this->ambiente == "prod"? $this->security_prod : $this->security_test;
        }

        private function getMerchant() {
            return $this->ambiente == "prod"? $this->merchant_id_prod : $this->merchant_id_test;
        }

        private function getOptionsSARComercio($esProductivo, $return_URL_OK, $return_URL_ERROR){
            return array (
                'Security'      => $esProductivo ? $this -> security_prod : $this -> security_test,
                'EncodingMethod'=> 'XML',
                'Merchant'      => strval($esProductivo ? $this -> merchant_id_prod : $this -> merchant_id_test),
                'URL_OK'        => $return_URL_OK,
                'URL_ERROR'     => $return_URL_ERROR
            ); 
        }

        private function getOptionsSAROperacion($esProductivo, $order){
            
            $arrayResult = array ( 
                'MERCHANT'    => strval($esProductivo ? $this -> merchant_id_prod : $this -> merchant_id_test),
                'OPERATIONID' => strval($order -> id),
                'CURRENCYCODE'=> '032', //Por el momento es el único tipo de moneda aceptada
            ); 
            
            if($this -> enabledCuotas === "yes"){
                $arrayResult['MAXINSTALLMENTS']  = strval( $this -> max_cuotas);
            }
            
            if($this->expiracion_formulario_personalizado=='SI'){
            	$arrayResult["TIMEOUT"]=$this->timeout_limite;
            }
            
            return $arrayResult;
        }

        private function generate_form($order, $URL_Request){
            $order_key  = $_GET['key'];
                 $order_id =  wc_get_order_id_by_order_key($order_key);
            return '<a class="button" href="'.get_site_url().'/?TodoPago_redirect=true&form=ext&order='.$order_id.'" id="submit_todopago_payment_form"> Pagar con TodoPago </a>  
              <a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . ' Cancelar orden ' . '</a>';
        }

        private function clean_cart($woocommerce,$condition){
        	if($condition=="si"){
        		$woocommerce->cart->empty_cart();
        	} else {
        		$woocommerce->cart->empty_cart();

		            if(isset($_GET['order']) || isset($_GET['key'])){
                		$order_id = intval($_GET['order']);
		                if(!isset($_GET['order'])) {
                	            $order_id = wc_get_order_id_by_order_key($_GET['key']);
		                }
                            }
                	    $order = new WC_Order($order_id);
			    $items =  $order->get_items();

                            foreach($items as $key => $value) {
                                if(is_array($value)) {
                                    $woocommerce->cart->add_to_cart($value['product_id'],$value['qty']);
                                }
                            }
		}
    	}
        
        public function process_refund( $order_id, $amount = null, $reason = '' ) {
            global $woocommerce;
            //IMPORTANTE EXCEPTIONS: WooCommerce las capturará y las mostrará en un alert, esta es la herramienta que se dipone para comunicarse con el usuario, he probado con echo y no lo he logrado.
			
            $order = new WC_Order( $order_id );
            
            //sí la transacción no se completó , no permito reembolsar
            if($order->get_status()!="completed"){
            	throw new exception("No se puede reembolsar una transacción incompleta");
            }
            
            $logger = $this->_obtain_logger(phpversion(), $woocommerce->version, TODOPAGO_PLUGIN_VERSION, $this->ambiente, $this->getMerchant(), $order_id, true);
            //configuración común a ambos servicios.
            $row = get_post_meta($order_id, 'response_SAR', true);
            $response_SAR = unserialize($row);
            
            $options_return = array(
                    "Security" => $this->getSecurity(),
                    "Merchant" => $this->getMerchant(),
                    "RequestKey" => $response_SAR["RequestKey"]
            );

            //Intento instanciar la Sdk, si la configuración está mal, le avisará al usuario.
            try {
                $connector = new \TodoPago\Sdk($this->getHttpHeader(), $this->ambiente);
            }
            catch (Exception $e) {
                $logger->warn("Error al crear el connector, ", $e);
                throw new Exception("Revise la configuarción de TodoPago");
            }

            //throw new exception("Conector creado");

            if(empty($amount)) { //Si el amount vieniera vacío hace la devolución total
                $logger->info("Pedido de devolución total pesos de la orden $order_id");
                $logger->info("Params devolución: ".json_encode($options_return));

                //Intento realizar la devolución total
                try {
                    $return_response = $connector->voidRequest($options_return);
                }
                catch (Exception $e) {
                    $logger->error("Falló al consultar el servicio: ", $e);
                    throw new Exception("Falló al consultar el servicio");
                }
            }
            else {
//            	if($amount < $options_return['AMOUNT']){

                	$logger->info("Pedido de devolución por $amount pesos de la orden $order_id");
                	$options_return['AMOUNT'] = $amount;
                	$logger->info("Params devolución: ".json_encode($options_return));

                	//Intento realizar la devolución parcial
                	try {
                		$return_response = $connector->returnRequest($options_return);
                	}
                	catch (Exception $e) {
                		$logger->error("Falló al consultar el servicio: ", $e);
                		throw new Exception("Falló al consultar el servicio");
                	}
//                }else{
//                	throw new Exception("Debe Ingresar un monto menor o igual al total de la compra sin interes");
//                }
            }
            $logger->info("Response devolucion: ".json_encode($return_response));

            //Si el servicio no responde según lo esperado, se interrumpe la devolución
            if (!is_array($return_response) || !array_key_exists('StatusCode', $return_response) || !array_key_exists('StatusMessage', $return_response)) {
                throw new Exception("El servicio no responde correctamente");
            }
            if ($return_response['StatusCode'] == TODOPAGO_DEVOLUCION_OK) {
                //retorno true para que Woo tome la devolución
                return true;
            }
            else {
                throw new Exception($return_response["StatusMessage"]);
                //return false;
            }
        }
        
        private function getGoogleMapsValidator($md5Billing, $md5Shipping) //Instancia Google en caso de no encontrar la ubicación a cargar en la tabla
        {   
            if (empty($this->adressbook->findMd5($md5Billing)) || empty($this->adressbook->findMd5($md5Shipping))){        
                return new TodoPago\Client\Google();
            }
            else
                return null;
        }
        
        private function SAR_hasher($paramsSAR, $tipoDeCompra)
        {
            if($tipoDeCompra === 'billing')
                $arrayCompra = array('CSBTSTREET1' => 1, 'CSBTSTATE' => 2, 'CSBTCITY' => 3, 'CSBTCOUNTRY' => 3, 'CSBTPOSTALCODE' => 5);
                elseif ($tipoDeCompra === 'shipping')
                $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
                else {
                        $this->tplogger->error("No se recibió un input válido en el array de SAR_hasher()");
                        $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
                }
                return md5(implode(",", array_intersect_key($paramsSAR, $arrayCompra)));//convierte un array en string separados por comas y lo pasa a md5
        }
        
        private function setAddressBookData($originalData,$gResponse,$md5Billing,$md5Shipping)
        {   
            $opBilling = $gResponse['billing'];
            $opShipping = $gResponse['shipping'];

            $this->recordAdressValidator($originalData,$opBilling,$md5Billing,"B");

            if($md5Billing !== $md5Shipping){
                $this->recordAdressValidator($originalData,$opShipping,$md5Shipping,"S");
            }
        }
        
        private function getAddressbookData($operationData, $md5Billing, $md5Shipping) //rellena los datos de la operación con la info almacenada en nuestra agenda
        {
            $arrayBilling = $this -> adressbook->getData($md5Billing);
            $arrayShipping = $this -> adressbook->getData($md5Shipping);

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
        
        private function recordAdressValidator($originalData,$gResponse,$md5,$type){
            if(!empty($gResponse)){//sí la respuesta de Google no es vacía
                $arrayDif=$this->compareArray($this->formArray($type),$gResponse);//array que muestra la diferencia de
                //las llaves que no están en la respuesta de Google
                $arrayDifNumber=sizeof($arrayDif);
                $postalCodeKey='CS'.$type.'TPOSTALCODE';
                $postalCode=$originalData[$postalCodeKey];//seteo como default el codigo postal ingresado por el usuario
                $isRecordable=true;

                switch($arrayDifNumber){
                        case 0:$postalCode=$gResponse[$postalCodeKey];break;
                        case 1:$isRecordable=array_key_exists($postalCodeKey,$arrayDif);break;
                        default:$isRecordable=false;break;
                }

                if($isRecordable){
                        $this->adressbook->recordAddress($md5,$gResponse['CS'.$type.'TSTREET1'], $gResponse['CS'.$type.'TSTATE'], $gResponse['CS'.$type.'TCITY'], $gResponse['CS'.$type.'TCOUNTRY'], $postalCode);
                }    		
            }
        }
        
        private function compareArray($arrayExpected,$arrayActual){//compara dos arrays,si son iguales , devuelve un array vacio
            $result=array_diff_key($arrayExpected,$arrayActual);    	
            return $result;	
        }
    
        private function formArray($letter){//define un array con las llaves a traer , pasandole la letra correspondiente(shiiping o billing)
            return array('CS'.$letter.'TSTREET1'=>1,'CS'.$letter.'TSTATE'=>2,'CS'.$letter.'TCITY'=>3,'CS'.$letter.'TCOUNTRY'=>4,'CS'.$letter.'TPOSTALCODE'=>5);
        }
        
        
        
        private function exists_woocommerce_get_page_id($order_object){
            
            $result=$order_object->get_checkout_payment_url(true);
            
            if($result==NULL){
                $result=woocommerce_get_page_id("pay");
            }
            
            return $result;
        }
        
    }//End WC_TodoPago_Gateway

    class_alias("WC_TodoPago_Gateway","todopago");

    //Agrego el campo teléfono de envío para cybersource
    function todopago_custom_override_checkout_fields($fields) {
        $fields['shipping']['shipping_phone'] = array(
            'label'     => 'Teléfono',
            'required'  => true,
            'class'     => array('form-row-wide'),
            'clear'     => true
        );

        return $fields;
    }

    add_filter( 'woocommerce_checkout_fields' , 'todopago_custom_override_checkout_fields' );

    //Añado el medio de pago TodoPago a WooCommerce
    function woocommerce_add_todopago_gateway($methods) {
        $methods[] = 'WC_TodoPago_Gateway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_todopago_gateway' );

}//End woocommerce_todopago_init


//Actualización de versión

global $todopago_db_version;
$todopago_db_version = '1.0';

function todopago_install(){
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

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    global $todopago_db_version;
    add_option('todopago_db_version', $todopago_db_version);

}

function todopago_update_db_check() {
    global $todopago_db_version;
    $installed_ver = get_option('todopago_db_version');

    if ($installed_ver == null || $installed_ver != $todopago_db_version) {
        todopago_install();
        update_option('todopago_db_version', $todopago_db_version);
    }

}

add_action('plugins_loaded', 'todopago_update_db_check');

function my_init() {
	
    // comment out the next two lines to load the local copy of jQuery
        wp_deregister_script('jquery'); 
        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', false, '1.3.2'); 
        wp_enqueue_script('jquery');
}
// No eliminar esta linea, en el Readme se indica que esta linea debe  ser descomentada en el caso de tener conflictos con Jquery
//add_action('init', 'my_init');


add_action('wp_ajax_getCredentials', 'getCredentials' ); // executed when logged in
add_action('wp_ajax_nopriv_getCredentials', 'getCredentials' ); 

function getCredentials(){
    if((isset($_POST['user']) && !empty($_POST['user'])) &&  (isset($_POST['password']) && !empty($_POST['password']))){

        if(wp_verify_nonce( $_REQUEST['_wpnonce'], "getCredentials" ) == false) {
            $response = array( 
                "mensajeResultado" => "Error de autorizacion"
            );  
            echo json_encode($response);
            exit;
        }

        $userArray = array(
            "user" => trim($_POST['user']), 
            "password" => trim($_POST['password'])
        );

        $http_header = array();

        //ambiente developer por defecto 
        $mode = "test";
        if($_POST['mode'] == "prod"){
            $mode = "prod";
        }

        try {
            $connector = new \TodoPago\Sdk($http_header, $mode);
            $userInstance = new \TodoPago\Data\User($userArray);
            $rta = $connector->getCredentials($userInstance);

            $security = explode(" ", $rta->getApikey()); 
            $response = array( 
                "codigoResultado" => 1,
                "merchandid" => $rta->getMerchant(),
                "apikey" => $rta->getApikey(),
                "security" => $security[1]
            );
        }catch(\TodoPago\Exception\ResponseException $e){
            $response = array(
                "mensajeResultado" => $e->getMessage()
            );
        }catch(\TodoPago\Exception\ConnectionException $e){
            $response = array(
                "mensajeResultado" => $e->getMessage()
            );
        }catch(\TodoPago\Exception\Data\EmptyFieldException $e){
            $response = array(
                "mensajeResultado" => $e->getMessage()
            );
        }
        echo json_encode($response);
    }else{
        $response = array( 
            "mensajeResultado" => "Ingrese usuario y contraseña de Todo Pago"
        );  
        echo json_encode($response);
    }
    exit;
}

add_action('wp_ajax_getStatus', 'getStatus' ); // executed when logged in
add_action('wp_ajax_nopriv_getStatus', 'getStatus' ); 

function getStatus(){
    global $wpdb;

    $row = $wpdb -> get_row(
    "SELECT option_value FROM wp_options WHERE option_name = 'woocommerce_todopago_settings'"
    );
    $arrayOptions = unserialize($row -> option_value);

    $esProductivo = $arrayOptions['ambiente'] == "prod";

    $http_header = $esProductivo ? $arrayOptions['http_header_prod'] : $arrayOptions['http_header_test'];
    $header_decoded = json_decode(html_entity_decode($http_header,TRUE));
    $http_header = (!empty($header_decoded)) ? $header_decoded : array("authorization" => $http_header);

    $connector = new \TodoPago\Sdk($http_header, $arrayOptions['ambiente']);

    //opciones para el método getStatus 
    $optionsGS = array('MERCHANT'=>$_GET['merchant'],'OPERATIONID'=>$_GET['order_id']);
    $status = $connector->getStatus($optionsGS);

    $rta = '';
    $refunds = $status['Operations']['REFUNDS'];
    $refounds = $status['Operations']['refounds'];

    $auxArray = array(
         "refound" => $refounds, 
         "REFUND" => $refunds
         );

    if($refunds != null){  
        $aux = 'REFUND'; 
        $auxColection = 'REFUNDS'; 
    }else{
        $aux = 'refound';
        $auxColection = 'refounds'; 
    }


    if (isset($status['Operations']) && is_array($status['Operations']) ) {
      
        foreach ($status['Operations'] as $key => $value) {   
            if(is_array($value) && $key == $auxColection){
                $rta .= "$key: <br/>";
                foreach ($auxArray[$aux] as $key2 => $value2) {              
                    $rta .= $aux." <br/>";                
                    if(is_array($value2)){                    
                        foreach ($value2 as $key3 => $value3) {
                            if(is_array($value3)){                    
                                 foreach ($value3 as $key4 => $value4) {
                                    $rta .= "   - $key4: $value4 <br/>";
                                }
                            } else {
				$rta .= "   - $key3: $value3 <br/>";
			    }
                        }
                    } else {
			$rta .= "$key2: $value2 <br/>";
		    }
                }
            }else{
                if(is_array($value)){
                    $rta .= "$key: <br/>";
                }else{
                    $rta .= "$key: $value <br/>";
                }
            }
        }
    }else{
       $rta = 'No hay operaciones para esta orden.';
    }
 
    echo($rta);

    exit;
}


function modifyOrder($total_rows){
	
	global $wpdb;
	
	$tp_gateway=new WC_TodoPago_Gateway();

	if(!isset($_GET["order-received"])) return $total_rows;

	$order_id=$_GET["order-received"];
	

	$key=$_GET["key"];
	
	$params_GAA=$tp_gateway->call_GAA($order_id,null);
	
	$payload_request=$params_GAA["response_GAA"]["Payload"]["Request"];
	
	
	$order = $wpdb->get_row("select * from wp_todopago_transaccion where id_orden=".$order_id.";");
	
	$decoded_order=json_decode($order->params_SAR);
	
	$costo_subtotal=$decoded_order->operacion->AMOUNT;
	
	$costo_total=$payload_request["AMOUNTBUYER"];
	
	$otros_cargos=$costo_total - $costo_subtotal;
	
	
	$order_total_array=$total_rows["order_total"];
	
	$order_total_array["value"]=price_amount_tag($costo_total);
	
	
	array_pop($total_rows);
	
	array_push($total_rows,array("label"=>"Otros cargos","value"=>price_amount_tag($otros_cargos)));
	
	array_push($total_rows, $order_total_array);
	
	
	return $total_rows;
}

add_action('woocommerce_get_order_item_totals', 'modifyOrder' );

function price_amount_tag($value){
	return '<span class="woocommerce-Price-amount amount">'.$value.'<span class="woocommerce-Price-currencySymbol">&#36;</span></span>';
}

function get_post_id_by_key($key){
	
	global $wpdb;
	
	$data = $wpdb->get_row("SELECT post_id FROM wp_postmeta WHERE meta_value = '".$key."'" );
	
	return $data->post_id;
}

add_action('woocommerce_admin_order_totals_after_shipping', 'agregarOtrosCargos');

function agregarOtrosCargos($order_id) {
	
	$post_id=$_GET["post"];
	
	$otros_cargos = get_post_meta($post_id,"_otros_cargos",true);
	
	echo '<tr>
            <td class="label">Otros cargos:</td>
            <td width="1%"></td>
            <td class="total woocommerce-Price-amount amount">'.$otros_cargos.'$</td>
        </tr>';
}
