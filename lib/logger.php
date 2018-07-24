<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class TodoPagoLogger {

	public $log;

	protected $config;

	protected $php_version = null;
	protected $commerce_version = null;
	protected $plugin_version = null;
	protected $end_point = null;
	protected $customer = null;
	protected $order = null;

	public function __construct() {
		$this->config = array(
			"appenders" => array(
				"PaymentAppender" => array(
					"class"   => "LoggerAppenderFile",
					"layout"  => array(
						"class"  => "LoggerLayoutPattern",
						"params" => array(
							"conversionPattern" => "[%d{ISO8601}] %-5p [%C{1}(%M:%L) | %F] PAYMENT (PHPv.%X{php_version} - eCv.%X{commerce_version} - Pv.%X{plugin_version} - EP.%X{end_point} - Cus.%X{customer} - Ord.%X{order}) %m%n%ex",
						),
					),
					"params"  => array(
						"file" => dirname( __FILE__ ) . "/todopago.log",
					),
					"filters" => array(
						array(
							"class"  => "LoggerFilterLevelRange",
							"params" => array(
								"levelMin" => "trace",
								"levelMax" => "fatal",
							)
						)
					),
				),
				"AdminAppender"   => array(
					"class"   => "LoggerAppenderFile",
					"layout"  => array(
						"class"  => "LoggerLayoutPattern",
						"params" => array(
							"conversionPattern" => "[%d{ISO8601}] %-5p [%C{1}(%M:%L) | %F] ADMIN (PHPv.%X{php_version} - eCv.%X{commerce_version} - Pv.%X{plugin_version}) %m%n%ex",
						),
					),
					"params"  => array(
						"file" => dirname( __FILE__ ) . "/todopago.log",
					),
					"filters" => array(
						array(
							"class"  => "LoggerFilterLevelRange",
							"params" => array(
								"levelMin" => "trace",
								"levelMax" => "fatal",
							)
						)
					),
				),
			),
			"loggers"   => array(
				"todopagopayment" => array(
					"appenders" => array(
						"PaymentAppender"
					)
				),
				"todopagoadmin"   => array(
					"appenders" => array(
						"AdminAppender"
					)
				),
			),
		);
	}

	public function getLogger( $payment ) {
		Logger::configure( $this->config );

		if ( $this->php_version != null ) {
			LoggerMDC::put( 'php_version', $this->php_version );
		} else {
			throw new Exception( "Logger Configuracion incompleta Falta php_version" );
		}
		if ( $this->commerce_version != null ) {
			LoggerMDC::put( 'commerce_version', $this->commerce_version );
		} else {
			throw new Exception( "Logger Configuracion incompleta Falta commerce_version" );
		}
		if ( $this->plugin_version != null ) {
			LoggerMDC::put( 'plugin_version', $this->plugin_version );
		} else {
			throw new Exception( "Logger Configuracion incompleta Falta plugin_version" );
		}
		if ( $payment ) {
			if ( $this->end_point != null ) {
				LoggerMDC::put( 'end_point', $this->end_point );
			} else {
				throw new Exception( "Logger Configuracion incompleta Falta endpoint" );
			}
			if ( $this->customer != null ) {
				LoggerMDC::put( 'customer', $this->customer );
			} else {
				throw new Exception( "Logger Configuracion incompleta Falta Cliente/Merchant ID" );
			}
			if ( $this->order != null ) {
				LoggerMDC::put( 'order', $this->order );
			} else {
				throw new Exception( "Logger Configuracion incompleta Falta Orden" );
			}
		}

		if ( $payment ) {
			return Logger::getLogger( "todopagopayment" );
		}

		return Logger::getLogger( "todopagoadmin" );
	}

	public function setPhpVersion( $php_version ) {
		$this->php_version = $php_version;
	}

	public function setCommerceVersion( $commerce_version ) {
		$this->commerce_version = $commerce_version;
	}

	public function setPluginVersion( $plugin_version ) {
		$this->plugin_version = $plugin_version;
	}

	public function setEndPoint( $end_point ) {
		$this->end_point = $end_point;
	}

	public function setCustomer( $customer ) {
		$this->customer = $customer;
	}

	public function setOrder( $order ) {
		$this->order = $order;
	}

	public function setLevels( $min = "trace", $max = "fatal" ) {
		$this->config["appenders"]["PaymentAppender"]["filters"][0]["params"]["levelMin"] = $min;
		$this->config["appenders"]["PaymentAppender"]["filters"][0]["params"]["levelMax"] = $max;
		$this->config["appenders"]["AdminAppender"]["filters"][0]["params"]["levelMin"]   = $min;
		$this->config["appenders"]["AdminAppender"]["filters"][0]["params"]["levelMax"]   = $max;
	}

	public function setFile( $file ) {
		$this->config["appenders"]["PaymentAppender"]["params"]["file"] = $file;
		$this->config["appenders"]["AdminAppender"]["params"]["file"]   = $file;
	}
}
