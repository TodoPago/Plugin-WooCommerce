<?php

namespace TodoPago;

require_once( "vendor/autoload.php" );
require_once( 'vendor/apache/log4php/src/main/php/Logger.php' );

use TodoPago\Client\Google;
use TodoPago\Core\Address\GoogleAddressDAO;
use TodoPago\Core\Config\ConfigDTO;
use TodoPago\Core\Config\ConfigModel;
use TodoPago\Core\Exception\ExceptionBase;
use TodoPago\Core\Merchant\MerchantDTO;
use TodoPago\Core\Order\OrderDTO;
use TodoPago\Core\Transaction\TransactionDAO;
use TodoPago\Core\Transaction\TransactionDTO;
use TodoPago\Core\Transaction\TransactionModel;
use TodoPago\Core\Order\OrderModel;
use TodoPago\Utils\Constantes;
use TodoPago\Utils\GithubApi;

class Core {
	protected $sdk;
	protected $tpLogger;
	protected $mode;
	protected $http_header;
	protected $wpdb;
	protected $merchant;
	protected $orderModel;
	protected $controlFraude;
	protected $config;
	protected $urlPath;
	protected $googleAddressDAO;
	protected $transactionDAO;
	protected $md5Billing;
	protected $md5Shipping;

	//TODO VALIDAR MERCHANT DE TE O
	public function __construct( ConfigDTO $config = null, MerchantDTO $merchant = null ) {
		global $wpdb;
		if ( $merchant ) {
			$this->setHttpHeader( $merchant->getHttpHeader() );
			$this->setMerchant( $merchant );
		}
		if ( $config ) {
			try {
				$this->setConfig( new ConfigModel( $config ) );
			} catch ( ExceptionBase $e ) {
				echo $e->getLine();
				echo $e->getMessage();
			}
		}
		if ( $config && $merchant ) {
			$this->sdk = new Sdk( $merchant->getHttpHeader(), $config->getModo() );
		}

		$this->setWpdb( $wpdb );
		$this->setGoogleAddressDAO( new GoogleAddressDAO( $wpdb, $this->getTpLogger() ) );
		$this->setTransactionDAO( new TransactionDAO( $wpdb ) );
		do_action( 'todopago_core_get_status', array( $this, 'build_todopago_meta_box' ) );
		add_action( 'todopago_core_install', array( $this, 'todopago_core_install' ) );
		do_action( 'todopago_core_get_sdk', $this->getSdk() );
		add_filter( 'todopago_github_update', array( $this, 'todopago_github_api' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'todopago_admin_style' ) );
	}

	public function todopago_core_install() {
		$this->getTransactionDAO()->createTable();
		$this->getGoogleAddressDAO()->createTable();
	}

	public function todopago_admin_style() {
		?>
        <style>
            .wp-admin select {
                height: 38px !important;
        </style>
		<?php
	}

	public function todopago_github_api( $url, $download, $logger ) {
		$github       = new GithubApi( $url );
		$githubAnswer = $github->github_get();
		if ( is_object( $githubAnswer ) && isset( $githubAnswer->tag_name ) ) {
			$githubVersion = str_replace( 'V', '', $githubAnswer->tag_name );
		} else if ( is_object( $githubAnswer ) ) {
			$githubVersion = $this->getConfig()->getPluginVersion();
			$logger->error( "Error al comunicarse con github: " . $githubAnswer->message );
		} else {
			$logger->error( "Error al comunicarse con github: " . $githubAnswer );
			$githubVersion = 0;
		}
		if ( version_compare( $this->getConfig()->getPluginVersion(), $githubVersion ) == - 1 ) {
			$this->build_todopago_update_box( $download );
			$logger->info( "Hay versión nueva para actualizar:" . $githubVersion );
			add_action( 'admin_notices', array( $this, 'tp_update_notice' ) );
		}
	}

	public function tp_update_notice() {

	}

	protected function build_todopago_update_box( $download ) {
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
            Se encuentra disponible una versión más reciente del plugin de Todo Pago, puede consultarla desde <a
                    href="<?php echo $download ?>" target="_blank">aquí</a>
        </div>
		<?php
	}

	public function build_todopago_meta_box( $neto, $bruto, $method, $id ) {
		if ( $method === Constantes::TODOPAGO_TODOPAGO || $method === Constantes::TODOPAGO_BILLETERA ) {
			$financial_cost  = $bruto - $neto;
			$auth            = $this->merchant->getHttpHeader();
			$auth            = $auth['authorization'];
			$location        = "../" . $this->getConfig()->getPluginUrlPath();
			$urlGetStatus    = $location . "Core/view/getStatus.php";
			$urlGetStatusCss = $location . "Core/view/popup_getStatus.css";

			$urlJquery = $location . "Core/Utils/jquery-3.2.1.min.js";

			?>
            <style type="text/css">
                .container {
                    display: none;
                }

                .row {
                    display: flex;
                    flex-direction: row;
                    margin-bottom: 5px;
                }

                .col-6 {
                    flex: 1;
                }

                .separador {
                    height: 5px;
                    margin-bottom: 10px;
                    border-color: dimgray dimgray dimgray dimgray;
                    border-style: solid;
                    border-width: 0 0 1px 0;
                }

                .tp-button:hover {
                    color: #e3f400
                }

                .tp-button {
                    text-align: right;
                    background-color: #E6007E;
                    color: white;
                    border-radius: 3px;
                    padding: 5px;
                }

                .tp-modal {
                    position: fixed;
                    left: 0;
                    right: 0;
                    top: 0;
                    width: 100vw;
                    height: 100vh;
                    display: none;
                    background-color: rgba(10, 10, 10, 0.8);
                    z-index: -10;
                }

            </style>
            <script src="<?php echo $urlJquery ?>"></script>
            <script type="application/javascript">
                var merchant = {
                    merchantId: "<?php echo $this->merchant->getMerchantId() ?>",
                    apiKey: "<?php echo $this->merchant->getApiKey() ?>",
                    httpHeader: "<?php echo $auth ?>"
                };
                var data = {
                    merchant: merchant,
                    modo: "<?php echo $this->getConfig()->getModo(); ?>",
                    id: "<?php echo $id ?>",
                    css: "<?php echo $urlGetStatusCss ?>"
                };

                function openTodoPago() {
                    $(".container").toggle('slow');

                }
            </script>
            <button type="button" onClick="openTodoPago()" class="tp-button">Todo Pago Tools</button>
            <div class="tp-modal">
                <div class="tp-get-status"></div>
            </div>
            <div class="container">
                <div class="row">
                    Todo Pago
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Otros Cargos:</strong> <?php echo "$" . $financial_cost; ?>
                    </div>
                    <div class="col-6">
                        <strong>Costo Total:</strong> <?php echo "$" . $bruto; ?>
                    </div>
                </div>
                <div class="separador"></div>
                <div class="row">
                    <div class="col-6">
                        <strong>Estado de la orden</strong>
                    </div>
                    <div class="col-6">
                        <button class="button" type="button"
                                onclick="verstatus('<?php echo $urlGetStatus ?>', data)">
                            Ver Estado
                        </button>
                    </div>
                </div>
            </div>

			<?php
			include_once dirname( __FILE__ ) . "/view/status.php";
		}
	}


	protected function todopago_refunded_amount( $order_id ) {
		$refunds_json = get_post_meta( $order_id, 'refund', true );
		$refunds_arr  = json_decode( $refunds_json, true );

		$total_refund = 0;
		if ( ! empty( $refunds_arr ) ) {
			foreach ( $refunds_arr as $refund ) {
				if ( $refund['result'] == Constantes::TODOPAGO_DEVOLUCION_OK ) {
					$total_refund += $refund['amount'];
				}
			}
		}

		return $total_refund;
	}


	protected function wp_get_request_uri() {
		return $_SERVER["REQUEST_URI"];
	}


	function get_refund_url( $ref_total = 0 ) {
		$arr_url               = explode( '?', $this->wp_get_request_uri() );
		$_REQUEST['ref_total'] = $ref_total;

		return add_query_arg( $_REQUEST, $arr_url[0] );
	}

	/* FIRST STEP */

	public function call_sar() {
		do_action( "todopago_pre_call_sar" );
		$transactionModel = null;
		$this->getTpLogger()->info( __METHOD__ );
		$tpConfig  = $this->getConfig();
		$paramsSAR = $this->getCommerceData();
		$this->_persistParams_SAR( $this->getOrderModel()->getOrderId(), $paramsSAR );
		if ( $tpConfig->getGoogleMaps() ) {
			$paramsSAR->operacion = $this->callGoogleAddress( $paramsSAR );
		}
		$responseSAR = $this->getSdk()->sendAuthorizeRequest( $paramsSAR->comercio, $paramsSAR->operacion );

		if ( $responseSAR["StatusCode"] == 702 && ! empty( $paramsSAR->commerce_data["Merchant"] ) && ! empty( $dataSecurity ) ) {
			$responseSAR = $this->sdk->sendAuthorizeRequest( $paramsSAR->commerce_data, $paramsSAR->operation_data );
		}
		$this->_persistResponse_SAR( $this->getOrderModel()->getOrderId(), $responseSAR );
		try {
			$transactionModel = $this->saveTransaction( Constantes::TODOPAGO_SAR, $paramsSAR, $responseSAR );  // guardo la transac en la base
		} catch ( ExceptionBase $e ) {
			var_dump( $e->getMessage() );
			$this->getTpLogger()->error( $e->getMessage() . "\nVer línea: " . $e->getLine() );
		}
		$this->getTpLogger()->info( "resultado SAR: " . json_encode( $responseSAR ) );
		$this->_persistResponse_SAR( $this->getOrderModel()->getOrderId(), $responseSAR );
		if ( ! is_null( $this->getSdk()->getGoogleClient() ) && ! is_null( $this->getSdk()->getGoogleClient()->getFinalAddress() ) ) {
			$googleAddressDAO = $this->getGoogleAddressDAO();
			$googleAddressDAO->recordAddress( $this->getMd5Billing(), $this->getMd5Shipping(), $this->getSdk()->getGoogleClient()->getFinalAddress(), $paramsSAR->operacion );
		}

		do_action( "todopago_post_call_sar" );

		return $transactionModel;
	}


	/* GOOGLE ADDRESS */

	protected function callGoogleAddress( $paramsSAR ) {
		$this->setMd5Billing( $this->SAR_hasher( $paramsSAR, 'B' ) );
		$this->setMd5Shipping( $this->SAR_hasher( $paramsSAR, 'S' ) );
		$validacionPorGoogle = $this->getGoogleMapsValidator( $this->getMd5Billing(), $this->getMd5Billing() );
		if ( $validacionPorGoogle ) {
			return $paramsSAR->operacion;
		} else {
			return $this->getAddressbookData( $paramsSAR->operacion );
		}
	}

	private function getGoogleMapsValidator( $md5Billing, $md5Shipping ) //Instancia Google en caso de no encontrar la ubicación a cargar en la tabla
	{
		$googleDAO       = $this->getGoogleAddressDAO();
		$hashMd5Billing  = $googleDAO->searchHash( $md5Billing );
		$hashMd5Shipping = $googleDAO->searchHash( $md5Shipping );
		if ( empty( $hashMd5Billing ) || empty( $hashMd5Shipping ) ) {
			$this->getSdk()->setGoogleClient( new Google() );

			return true;
		} else {
			return false;
		}

	}

	private function SAR_hasher( $paramsSAR, $tipoDeCompra ) {
		$operacion                                           = $paramsSAR->operacion;
		$arrayCompra[ 'CS' . $tipoDeCompra . 'TSTREET1' ]    = $operacion[ 'CS' . $tipoDeCompra . 'TSTREET1' ];
		$arrayCompra[ 'CS' . $tipoDeCompra . 'TSTATE' ]      = $operacion[ 'CS' . $tipoDeCompra . 'TSTATE' ];
		$arrayCompra[ 'CS' . $tipoDeCompra . 'TCITY' ]       = $operacion[ 'CS' . $tipoDeCompra . 'TCITY' ];
		$arrayCompra[ 'CS' . $tipoDeCompra . 'TCOUNTRY' ]    = $operacion[ 'CS' . $tipoDeCompra . 'TCOUNTRY' ];
		$arrayCompra[ 'CS' . $tipoDeCompra . 'TPOSTALCODE' ] = $operacion[ 'CS' . $tipoDeCompra . 'TPOSTALCODE' ];

		return md5( implode( ",", $arrayCompra ) );//convierte un array en string separados por comas y lo pasa a md5
	}

	private function setAddressBookData( $originalData, $gResponse, $md5Billing, $md5Shipping ) {
		$opBilling  = $gResponse['billing'];
		$opShipping = $gResponse['shipping'];

		$this->recordAdressValidator( $originalData, $opBilling, $md5Billing, "B" );

		if ( $md5Billing !== $md5Shipping ) {
			$this->recordAdressValidator( $originalData, $opShipping, $md5Shipping, "S" );
		}
	}

	private function getAddressbookData( $operationData ) //rellena los datos de la operación con la info almacenada en nuestra agenda
	{
		$md5Billing       = $this->getMd5Billing();
		$md5Shipping      = $this->getMd5Shipping();
		$googleAddressDAO = $this->getGoogleAddressDAO();
		$arrayBilling     = $googleAddressDAO->selectFullAddressByHash( $md5Billing );
		$arrayShipping    = $googleAddressDAO->selectFullAddressByHash( $md5Shipping );
		if ( ! empty( $arrayBilling ) ) {
			$operationData['CSBTSTREET1']    = $arrayBilling->street;
			$operationData['CSBTSTATE']      = $arrayBilling->state;
			$operationData['CSBTCITY']       = $arrayBilling->city;
			$operationData['CSBTCOUNTRY']    = $arrayBilling->country;
			$operationData['CSBTPOSTALCODE'] = $arrayBilling->postal;
		}
		if ( ! empty( $arrayShipping ) ) {
			$operationData['CSSTSTREET1']    = $arrayShipping->street;
			$operationData['CSSTSTATE']      = $arrayShipping->state;
			$operationData['CSSTCITY']       = $arrayShipping->city;
			$operationData['CSSTCOUNTRY']    = $arrayShipping->country;
			$operationData['CSSTPOSTALCODE'] = $arrayShipping->postal;
		}

		return $operationData;
	}

	private function recordAdressValidator( $originalData, $gResponse, $md5, $type ) {
		if ( ! empty( $gResponse ) ) {//sí la respuesta de Google no es vacía
			$arrayDif = $this->compareArray( $this->formArray( $type ), $gResponse );//array que muestra la diferencia de
			//las llaves que no están en la respuesta de Google
			$arrayDifNumber = sizeof( $arrayDif );
			$postalCodeKey  = 'CS' . $type . 'TPOSTALCODE';
			$postalCode     = $originalData[ $postalCodeKey ];//seteo como default el codigo postal ingresado por el usuario
			$isRecordable   = true;

			switch ( $arrayDifNumber ) {
				case 0:
					$postalCode = $gResponse[ $postalCodeKey ];
					break;
				case 1:
					$isRecordable = array_key_exists( $postalCodeKey, $arrayDif );
					break;
				default:
					$isRecordable = false;
					break;
			}

			if ( $isRecordable ) {
				$this->adressbook->recordAddress( $md5, $gResponse[ 'CS' . $type . 'TSTREET1' ], $gResponse[ 'CS' . $type . 'TSTATE' ], $gResponse[ 'CS' . $type . 'TCITY' ], $gResponse[ 'CS' . $type . 'TCOUNTRY' ], $postalCode );
			}
		}
	}

	protected function compareArray( $arrayExpected, $arrayActual ) {//compara dos arrays,si son iguales , devuelve un array vacio
		$result = array_diff_key( $arrayExpected, $arrayActual );

		return $result;
	}

	protected function formArray( $letter ) {//define un array con las llaves a traer , pasandole la letra correspondiente(shiiping o billing)

		return array(
			'CS' . $letter . 'TSTREET1'    => 1,
			'CS' . $letter . 'TSTATE'      => 2,
			'CS' . $letter . 'TCITY'       => 3,
			'CS' . $letter . 'TCOUNTRY'    => 4,
			'CS' . $letter . 'TPOSTALCODE' => 5
		);
	}


	protected function saveGoogleAddress() {

	}

	public function getCommerceData() {
		$controlFraude = \TodoPago\Core\ControlFraude\ControlFraudeFactory::get_ControlFraude_extractor( 'Retail', $this->getOrderModel(), $this->getOrderModel()->getCustomerBilling() );

		$datosCs = $controlFraude->getDataCF();

		$return_URL_ERROR = $this->getConfig()->getUrlError();

		$return_URL_OK = $this->getConfig()->getUrlSuccess();

		$optionsSAR_comercio = $this->getOptionsSARComercio();
		$optionsSAR_operacion = $this->getOptionsSAROperacion();

		$optionsSAR_operacion = array_merge_recursive( $optionsSAR_operacion, $datosCs );
		$paramsSAR            = new \stdClass();
		$paramsSAR->comercio  = $optionsSAR_comercio;
		$paramsSAR->operacion = $optionsSAR_operacion;

		$this->getTpLogger()->info( 'params SAR ' . json_encode( $paramsSAR ) );

		return $paramsSAR;
	}

	private function getOptionsSARComercio() {
		return array(
			'Security'       => $this->getMerchant()->getApiKey(),
			'EncodingMethod' => 'XML',
			'Merchant'       => $this->getMerchant()->getMerchantId(),
			'URL_OK'         => $this->getConfig()->getUrlSuccess(),
			'URL_ERROR'      => $this->getConfig()->getUrlError()
		);
	}

	private function getOptionsSAROperacion() {
		$form        = ( $this->getConfig()->getFormularioTipo() == Constantes::TODOPAGO_EXT ? '-E' : '-H' );
		$arrayResult = array(
			'MERCHANT'         => $this->getMerchant()->getMerchantId(),
			'OPERATIONID'      => strval( $this->getOrderModel()->getOrderId() ),
			'CURRENCYCODE'     => '032', //Por el momento es el único tipo de moneda aceptada
			'ECOMMERCENAME'    => $this->getConfig()->getEcommerceNombre(),
			'ECOMMERCEVERSION' => $this->getConfig()->getEcommerceVersion(),
			'CMSVERSION'       => $this->getConfig()->getCmsVersion(),
			'PLUGINVERSION'    => $this->getConfig()->getPluginVersion() . $form
		);

		$arraOpcionales = $this->getConfig()->getArrayOpcionales();

		if ( array_key_exists( 'enabledCuotas', $this->getConfig()->getArrayOpcionales() ) ) {
			$arrayResult['MAXINSTALLMENTS'] = strval( $arraOpcionales['maxCuotas'] );
		}

		if ( array_key_exists( 'enabledTimeoutForm', $this->getConfig()->getArrayOpcionales() ) ) {
			$arrayResult["TIMEOUT"] = $arraOpcionales['timeoutValor'];
		}

		return $arrayResult;
	}


	private function _persistResponse_SAR( $order_id, $response_SAR ) {

		update_post_meta( $order_id, 'response_SAR', serialize( $response_SAR ) );
	}

	private function _persistParams_SAR( $order_id, $params_SAR ) {

		update_post_meta( $order_id, 'params_SAR', serialize( $params_SAR ) );
	}

	/* SECOND STEP */

	public function call_gaa( $order_id ) {
		//  $esProductivo = $this->ambiente == "prod";
		$logger = $this->getTpLogger();


		$data_GAA = array();

		$row          = get_post_meta( $order_id, 'response_SAR', true );
		$response_SAR = unserialize( $row );
		$params_GAA   = array(
			'Security'   => $this->getMerchant()->getApiKey(),
			'Merchant'   => $this->getMerchant()->getMerchantId(),
			'RequestKey' => $response_SAR["RequestKey"],
			'AnswerKey'  => $_GET['Answer']
		);

		$response_GAA = $this->getSdk()->getAuthorizeAnswer( $params_GAA );


		$data_GAA['params_GAA']   = $params_GAA;
		$data_GAA['response_GAA'] = $response_GAA;

		if ( $logger !== null ) {
			$logger->info( 'second step _ ORDER ID: ' . $order_id );
			$logger->info( 'params GAA ' . json_encode( $params_GAA ) );
			$logger->info( "HTTP_HEADER: " . json_encode( $this->getHttpHeader() ) );
			$logger->info( 'response GAA ' . json_encode( $response_GAA ) );
		}

		try {
			$transactionModel = $this->saveTransaction( Constantes::TODOPAGO_GAA, $params_GAA, $response_GAA, $order_id );  // guardo la transac en la base
		} catch ( ExceptionBase $e ) {
			$this->getTpLogger()->error( $e->getMessage() . "\nVer línea: " . $e->getLine() );
		}

		do_action( "todopago_post_call_gaa" );

		return $data_GAA;
	}


	/* SAVE TRANSACTION */

	public function saveTransaction( $tipo, $params, $response = null, $order_id = 0 ) {
		if ( $tipo == Constantes::TODOPAGO_SAR ) {
			$transactionDTO = $this->buildTransactionDTO( $params, $response, $this->getOrderModel()->getOrderId() );
		} else {
			$transactionDTO = $this->buildTransactionDTO( $params, $response, $order_id );
		}

		$transactionModel = $this->buildTransactionModel( $transactionDTO );
		$transactionDAO   = $this->buildTransactionDAO();
		try {
			$transactionDAO->save( $tipo, $transactionModel );
		} catch ( ExceptionBase $e ) {
			echo $e->getMessage();
		}


		return $transactionModel;
	}

	protected function buildTransactionDAO() {
		global $wpdb;
		$transactionDAO = new TransactionDAO( $wpdb );

		return $transactionDAO;
	}

	protected function buildTransactionDTO( $params, $response, $orderID ) {
		$transactionDTO = new TransactionDTO( $orderID );
		$transactionDTO->setParams( $params );
		$transactionDTO->setResponse( $response );

		return $transactionDTO;
	}

	protected function buildTransactionModel( TransactionDTO $transactionDTO ) {
		$transactionModel = new TransactionModel( $transactionDTO->getOrderID() );
		$transactionModel->setResponse( $transactionDTO->getResponse() );
		$transactionModel->setParams( ( $transactionDTO->getParams() ) );
		$transactionModel->setFirstStep( date( "Y-m-d H:i:s" ) );

		return $transactionModel;
	}

	/* GET STATUS */
	public function get_status() {
		$row          = $this->getWpdb()->get_row(
			"SELECT option_value FROM wp_options WHERE option_name = 'woocommerce_todopago_settings'"
		);
		$arrayOptions = unserialize( $row->option_value );

		$esProductivo = $arrayOptions['ambiente'] == "prod";

		$http_header    = $esProductivo ? $arrayOptions['http_header_prod'] : $arrayOptions['http_header_test'];
		$header_decoded = json_decode( html_entity_decode( $http_header, true ) );
		$http_header    = ( ! empty( $header_decoded ) ) ? $header_decoded : array( "authorization" => $http_header );

		$connector = new \TodoPago\Sdk( $http_header, $arrayOptions['ambiente'] );

		//opciones para el método getStatus
		$optionsGS = array( 'MERCHANT' => $_GET['merchant'], 'OPERATIONID' => $_GET['order_id'] );
		$status    = $connector->getStatus( $optionsGS );

		$rta      = '';
		$refunds  = $status['Operations']['REFUNDS'];
		$refounds = $status['Operations']['refounds'];

		$auxArray = array(
			"refound" => $refounds,
			"REFUND"  => $refunds
		);

		if ( $refunds != null ) {
			$aux          = 'REFUND';
			$auxColection = 'REFUNDS';
		} else {
			$aux          = 'refound';
			$auxColection = 'refounds';
		}


		if ( isset( $status['Operations'] ) && is_array( $status['Operations'] ) ) {

			foreach ( $status['Operations'] as $key => $value ) {
				if ( is_array( $value ) && $key == $auxColection ) {
					$rta .= "$key: <br/>";
					foreach ( $auxArray[ $aux ] as $key2 => $value2 ) {
						$rta .= $aux . " <br/>";
						if ( is_array( $value2 ) ) {
							foreach ( $value2 as $key3 => $value3 ) {
								if ( is_array( $value3 ) ) {
									foreach ( $value3 as $key4 => $value4 ) {
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
				} else {
					if ( is_array( $value ) ) {
						$rta .= "$key: <br/>";
					} else {
						$rta .= "$key: $value <br/>";
					}
				}
			}
		} else {
			$rta = 'No hay operaciones para esta orden.';
		}
		echo( $rta );
		exit;
	}

	public function get_credentials() {
		if ( ( isset( $_POST['user'] ) && ! empty( $_POST['user'] ) ) && ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) ) {

			if ( wp_verify_nonce( $_REQUEST['_wpnonce'], "getCredentials" ) == false ) {
				$response = array(
					"mensajeResultado" => "Error de autorizacion"
				);
				echo json_encode( $response );
				exit;
			}

			$userArray = array(
				"user"     => trim( $_POST['user'] ),
				"password" => trim( $_POST['password'] )
			);

			$http_header = array();

			//ambiente developer por defecto
			$mode = "test";
			if ( $_POST['mode'] == "prod" ) {
				$mode = "prod";
			}

			try {
				$connector    = new Sdk( $http_header, $mode );
				$userInstance = new \TodoPago\Data\User( $userArray );
				$rta          = $connector->getCredentials( $userInstance );

				$security = explode( " ", $rta->getApikey() );
				$response = array(
					"codigoResultado" => 1,
					"merchandid"      => $rta->getMerchant(),
					"apikey"          => $rta->getApikey(),
					"security"        => $security[1]
				);
			} catch ( \TodoPago\Exception\ResponseException $e ) {
				$response = array(
					"mensajeResultado" => $e->getMessage()
				);
			} catch ( \TodoPago\Exception\ConnectionException $e ) {
				$response = array(
					"mensajeResultado" => $e->getMessage()
				);
			} catch ( \TodoPago\Exception\Data\EmptyFieldException $e ) {
				$response = array(
					"mensajeResultado" => $e->getMessage()
				);
			}
			echo json_encode( $response );
		} else {
			$response = array(
				"mensajeResultado" => "Ingrese usuario y contraseña de Todo Pago"
			);
			echo json_encode( $response );
		}
		exit;
	}

	public function process_refund( OrderDTO $OrderDTO ) {
		$row          = get_post_meta( $OrderDTO->getOrderId(), 'response_SAR', true );
		$response_SAR = unserialize( $row );

		//throw new \exception(var_export($this->getSdk(),true));
		$getRefundAmount = $OrderDTO->getRefundAmount();
		if ( empty( $getRefundAmount ) ) {
			//Si el amount vieniera vacío hace la devolución total
			$this->getTpLogger()->info( "Pedido de devolución total pesos de la orden {$OrderDTO->getOrderId()}" );
			$return_response = $this->void_request( $response_SAR["RequestKey"] );

		} else {
			$return_response = $this->return_request( $response_SAR["RequestKey"], $OrderDTO->getRefundAmount() );
		}

		$this->getTpLogger()->info( "Response devolucion: " . json_encode( $return_response ) );

		return $return_response;
	}

	public function void_request( $requestKey ) {
		$return_response = null;
		$options_return  = array(
			"Security"   => $this->getMerchant()->getApiKey(),
			"Merchant"   => $this->getMerchant()->getMerchantId(),
			"RequestKey" => $requestKey
		);
		$this->getTpLogger()->info( "Params devolución: " . json_encode( $options_return ) );

		//Intento realizar la devolución total
		try {
			$return_response = $this->getSdk()->voidRequest( $options_return );
		} catch ( \Exception $e ) {
			$this->getTpLogger()->error( "Falló al consultar el servicio: ", $e );
			throw new \Exception( "Falló al consultar el servicio" );
		}

		return $return_response;

	}

	public function return_request( $requestKey, $amount ) {
		$return_response = null;
		$options_return  = array(
			"Security"   => $this->getMerchant()->getApiKey(),
			"Merchant"   => $this->getMerchant()->getMerchantId(),
			"RequestKey" => $requestKey,
			"AMOUNT"     => $amount
		);

		$this->getTpLogger()->info( "Params devolución: " . json_encode( $options_return ) );

		//Intento realizar la devolución parcial
		try {
			$return_response = $this->getSdk()->returnRequest( $options_return );
		} catch ( \Exception $e ) {
			$this->getTpLogger()->error( "Falló al consultar el servicio: ", $e );
			throw new \Exception( "Falló al consultar el servicio" );
		}

		return $return_response;
	}

	/* FORMULARIO */

	public function initFormulario( TransactionModel $transactionModel ) {
		$config = $this->getConfig();
		//$id = $this->method_exists_orderkey_id($order, "get_id");
		$id              = $transactionModel->getIdOrden();
		$responseSAR     = $transactionModel->getResponse();
		$operacion       = $transactionModel->getParams()->operacion;
		$nombre_completo = $operacion->CSBTFIRSTNAME . " " . $operacion->CSBTLASTNAME;
		$email           = $operacion->CSBTEMAIL;
		if ( $responseSAR->StatusCode == - 1 ) {
			if ( isset( $config ) && $config->getFormularioTipo() == Constantes::TODOPAGO_EXT ) {
				// echo '<script>window.location.href = "' . get_site_url() . '/?TodoPago_redirect=true&form=ext&order=' . $id . '"</script>';
				echo '<p> Gracias por su orden. </p>';
				echo $this->generate_form();
				/*
									echo '<p> Gracias por su órden, click en el botón de abajo para pagar con TodoPago </p>';
									echo $this->generate_form($order, $response_sar["URL_Request"]);
				*/
			} else {
				$basename = plugin_basename( dirname( __FILE__ ) );
				$baseurl  = plugins_url();
				$form_dir = "$baseurl/$basename/view/formulario-hibrido";
				$merchant = $operacion->MERCHANT;
				$amount   = $operacion->CSPTGRANDTOTALAMOUNT;
				//$returnURL = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}".'&second_step=true';
				$return_URL_SUCCESS = $config->getUrlSuccess();
				$home               = home_url( "/" );
				$arrayHome          = explode( "/", $home );
				$return_URL_ERROR   = $config->getUrlError();
				$paymentMethod      = $config->getPaymentMethod();
				/*
				if ($this->url_after_redirection == "order_received") {
					$return_URL_OK = $orderCheckoutURL;
				} else {
					$return_URL_OK = $orderCheckoutURL;
					//$return_URL_OK = $arrayHome[0].'//'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}".'&second_step=true';

				}
				*/

				$env_url = ( $config->getModo() == Constantes::TODOPAGO_PROD ? Constantes::TODOPAGO_JS_PROD : Constantes::TODOPAGO_JS_TEST );

				require 'view/formulario-hibrido/formulario.php';
			}
		} else {
			$this->_printErrorMsg();
		}
	}


	private function generate_form() {
		//$order_key  = $_GET['key'];
		//$order_id =  wc_get_order_id_by_order_key($order_key);
		return '
        <style>
            .order_details{
                margin-top: 30px
            }
        </style>
        <a class="button" href="' . get_site_url() . '/?TodoPago_redirect=true&form=ext&order=' . $this->getOrderModel()->getOrderId() . '" id="submit_todopago_payment_form"><button class="input-text regular-input button-primary"> Pagar con Todo Pago </button></a>  
          <a class="button cancel" href="' . $this->getConfigDTO()->getUrlCancelOrder() . '"><button> Cancelar orden </button></a>';
	}


	/**
	 * @return Sdk
	 */
	public function getSdk() {
		return $this->sdk;
	}

	/**
	 * @param mixed $sdk
	 */
	public function setSdk( $sdk ) {
		$this->sdk = $sdk;
	}

	/**
	 * @return mixed
	 */
	public function getTpLogger() {
		return $this->tpLogger;
	}

	/**
	 * @param mixed $tpLogger
	 */
	public function setTpLogger( $tpLogger ) {
		$this->tpLogger = $tpLogger;
	}

	/**
	 * @return mixed
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * @param mixed $mode
	 */
	public function setMode( $mode ) {
		$this->mode = $mode;
	}

	/**
	 * @return mixed
	 */
	public function getHttpHeader() {
		return $this->http_header;
	}

	/**
	 * @param mixed $http_header
	 */
	public function setHttpHeader( $http_header ) {
		$this->http_header = $http_header;
	}

	/**
	 * @return mixed
	 */
	public function getWpdb() {
		return $this->wpdb;
	}

	/**
	 * @param mixed $wpdb
	 */
	public function setWpdb( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @return mixed
	 */
	public function getMerchant() {
		return $this->merchant;
	}

	/**
	 * @param mixed $merchant
	 */
	public function setMerchant( $merchant ) {
		$this->merchant = $merchant;
	}

	/**
	 * @return ConfigModel
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param ConfigModel $config
	 */
	protected function setConfig( ConfigModel $config ) {
		$this->config = $config;
	}

	public function setConfigModel( ConfigDTO $config ) {
		$this->setConfig( new ConfigModel( $config ) );
	}

	public function getConfigDTO() {
		$configModel             = $this->getConfig();
		$modo                    = $configModel->getModo();
		$formularioTipo          = $configModel->getFormularioTipo();
		$formularioTimeOutEstado = $configModel->getFormularioTimeoutEstado();
		$carrito                 = $configModel->getCarrito();
		$googleMaps              = $configModel->getGoogleMaps();
		$pluginPath              = $configModel->getPluginUrlPath();
		$arrayOpcionales         = $configModel->getArrayOpcionales();
		$getEcommerceNombre      = $configModel->getEcommerceNombre();
		$getEcommerceVersion     = $configModel->getEcommerceVersion();
		$getCmsVersion           = $configModel->getCmsVersion();
		$getPluginVersion        = $configModel->getPluginVersion();
		$configDTO               = new ConfigDTO( $modo, $formularioTipo, $formularioTimeOutEstado, $carrito, $googleMaps, $pluginPath, $getEcommerceNombre, $getEcommerceVersion, $getCmsVersion, $getPluginVersion );
		if ( ! is_null( $arrayOpcionales ) ) {
			$configDTO->setArrayOpcionales( $arrayOpcionales );
		}

		return $configDTO;
	}

	/**
	 * @return GoogleAddressDAO
	 */
	public function getGoogleAddressDAO() {
		return $this->googleAddressDAO;
	}

	/**
	 * @param GoogleAddressDAO $googleAddressDAO
	 */
	public function setGoogleAddressDAO( GoogleAddressDAO $googleAddressDAO ) {
		$this->googleAddressDAO = $googleAddressDAO;
	}


	/**
	 * @return mixed
	 */
	public function getOrderModel() {
		return $this->orderModel;
	}

	/**
	 * @param OrderDTO
	 */
	public function setOrderModel( OrderDTO $orderDTO ) {
		$orderModel = new OrderModel();
		$orderDTO->getCustomerBilling()->setIpAddress( $this->get_the_user_ip() );
		$orderModel->setOrderId( $orderDTO->getOrderId() );
		$orderModel->setAddressBilling( $orderDTO->getAddressBilling() );
		$orderModel->setAddressShipping( $orderDTO->getAddressShipping() );
		$orderModel->setProducts( $orderDTO->getProducts() );
		$orderModel->setTotalAmount( $orderDTO->getTotalAmount() );
		$orderModel->setCustomerBilling( $orderDTO->getCustomerBilling() );
		$orderModel->setCustomerShipping( $orderDTO->getCustomerShipping() );
		$orderModel->getCustomerBilling()->setIpAddress( $this->get_the_user_ip() );
		$this->orderModel = $orderModel;
	}

	/**
	 * @return mixed
	 */
	public function getTransactionDAO() {
		return $this->transactionDAO;
	}

	/**
	 * @param mixed $transactionDAO
	 */
	public function setTransactionDAO( $transactionDAO ) {
		$this->transactionDAO = $transactionDAO;
	}

	/**
	 * @return mixed
	 */
	public function getMd5Billing() {
		return $this->md5Billing;
	}

	/**
	 * @param mixed $md5Billing
	 */
	public function setMd5Billing( $md5Billing ) {
		$this->md5Billing = $md5Billing;
	}

	/**
	 * @return mixed
	 */
	public function getMd5Shipping() {
		return $this->md5Shipping;
	}

	/**
	 * @param mixed $md5Shipping
	 */
	public function setMd5Shipping( $md5Shipping ) {
		$this->md5Shipping = $md5Shipping;
	}


	public function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}


}
