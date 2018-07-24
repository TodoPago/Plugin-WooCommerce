<?php
/*
    Plugin Name: TodoPago para WooCommerce
    Description: TodoPago para Woocommerce.
    Author: Todo Pago
*/

namespace TodoPago\lib;
require_once( realpath( __DIR__ . "/.." ) . '/Core/vendor/autoload.php' );

use TodoPago\Utils\Constantes;

class TodopagoSettings {


	static function build_settings() {
		$settings = apply_filters( 'woocommerce_settings-todopago',
			array(
				'enabled'       => array(
					'title'   => 'Habilitar/Deshabilitar',
					'type'    => 'checkbox',
					'label'   => 'Habilitar modulo de pago Todo Pago',
					'default' => 'no'
				),
				'ambiente'      => array(
					'title'       => 'Ambiente',
					'type'        => 'select',
					'description' => 'Seleccione el ambiente con el que desea trabajar',
					'options'     => array(
						Constantes::TODOPAGO_TEST => 'developers',
						Constantes::TODOPAGO_PROD => 'produccion'
					)
				),
				'clean_carrito' => array(
					'title'       => 'Vaciar carrito',
					'type'        => 'select',
					'description' => 'Vaciar carrito en caso de fallo',
					'options'     => array(
						true  => 'SÍ',
						false => 'NO'
					)
				),

				'billetera_banner' => array(
					'title'       => 'Billetera en checkout',
					'type'        => 'multiselect',
					'class'       => 'billetera-banner',
					'description' => _( 'Seleccione el banner que desea mostrar para Billetera' ),
					'options'     => array(
						Constantes::TODOPAGO_BANNER_1 => __( 'Opción #1' ),
						Constantes::TODOPAGO_BANNER_2 => __( 'Opción #2' ),
						Constantes::TODOPAGO_BANNER_3 => __( 'Opción #3' )
					)
				),
				'tipo_segmento'    => array(
					'title'       => 'Tipo de segmento',
					'type'        => 'select',
					'description' => 'Seleccione el tipo de segmento con el que desea trabajar',
					'options'     => array(
						/*'retail' => 'Retail',
				'servicios' => 'Servicios',
				'digital_goods' => 'Digital Goods',
				'ticketing' => 'Ticketing')),*/
						'retail' => 'Retail'
					)
				),
				/*'canal_ingreso' => array(
			'title' => 'Canal de ingreso del pedido',
			'type' => 'select',
			'options' => array(
				'Web' => 'Web',
				'Mobile' => 'Mobile',
				'Telefonica' => 'Telefonica')),*/
				'deadline'         => array(
					'title'       => 'Deadline',
					'type'        => 'text',
					'description' => 'Dias maximos para la entrega'
				),

				'tipo_formulario' => array(
					'title'       => 'Elija el fromulario que desea utilizar',
					'type'        => 'select',
					'description' => 'Puede escojer entre un formulario integrado al comercio o redireccionar al formulario externo',
					'options'     => array(
						Constantes::TODOPAGO_EXT     => 'Externo',
						Constantes::TODOPAGO_HIBRIDO => 'Integrado'
					)
				),
				'enabledCuotas'   => array(
					'title'   => 'Habilitar/Deshabilitar cantidad de cuotas',
					'type'    => 'select',
					'label'   => 'Habilitar cuotas maximas',
					'options' => array(
						true  => 'SÍ',
						false => 'NO'
					)
				),
				'max_cuotas'      => array(
					'title'       => 'Numero máximo de cuotas',
					'type'        => 'select',
					'description' => 'Puede escojer entre 1 a 12 cuotas',
					'options'     => array(
						'12' => 12,
						'11' => 11,
						'10' => 10,
						'9'  => 9,
						'8'  => 8,
						'7'  => 7,
						'6'  => 6,
						'5'  => 5,
						'4'  => 4,
						'3'  => 3,
						'2'  => 2,
						'1'  => 1
					)
				),
				'credentials_dev' => array(
					'title' => 'Credenciales Desarrollo',
					'type'  => 'title'
				),

				'user_dev' => array(
					'title'       => 'User',
					'type'        => 'text',
					'description' => 'User Todo Pago'
				),

				'password_dev' => array(
					'title'       => 'Password',
					'type'        => 'text',
					'description' => 'Password Todo Pago'
				),

				'btnCredentials_dev' => array(
					'type'  => 'button',
					'value' => 'Obtener Credenciales',
					'class' => 'button-primary'
				),

				'titulo_testing' => array(
					'title'       => 'Ambiente de Developers',
					'type'        => 'title',
					'description' => 'Datos correspondientes al ambiente de developers',
					'id'          => 'testing_options'
				),

				'http_header_test' => array(
					'title'       => 'HTTP Header',
					'type'        => 'text',
					'description' => "API Keys que se obtiene en el portal de Todo Pago. Ejemplo: <b>TODOPAGO 912EC803B2CE49E4A541068D12345678</b>"
				),
				'security_test'    => array(
					'title'       => 'Security',
					'type'        => 'text',
					'description' => 'API Keys sin TODOPAGO. Ejemplo: <b>912EC803B2CE49E4A541068D12345678</b>'
				),
				'merchant_id_test' => array(
					'title'       => 'Merchant ID',
					'type'        => 'text',
					'description' => 'N&uacute;mero de comercio (MerchantId) provisto por el portal de Todo Pago'
				),

				'credentials_prod' => array(
					'title' => 'Credenciales Producción',
					'type'  => 'title'
				),

				'user_prod' => array(
					'title'       => 'User',
					'type'        => 'text',
					'description' => 'User Todo Pago'
				),

				'password_prod' => array(
					'title'       => 'Password',
					'type'        => 'text',
					'description' => 'Password Todo Pago'
				),

				'btnCredentials_prod' => array(
					'type'  => 'button',
					'value' => 'Obtener Credenciales',
					'class' => 'button-primary'
				),

				'titulo_produccion' => array(
					'title'       => 'Ambiente de Producción',
					'type'        => 'title',
					'description' => 'Datos correspondientes al ambiente de producción',
					'id'          => 'produccion_options'
				),

				'http_header_prod' => array(
					'title'       => 'HTTP Header',
					'type'        => 'text',
					'description' => 'API Keys que se obtiene en el portal de Todo Pago. Ejemplo: <b>TODOPAGO 912EC803B2CE49E4A541068D12345678</b>'
				),
				'security_prod'    => array(
					'title'       => 'Security',
					'type'        => 'text',
					'description' => 'API Keys sin TODOPAGO. Ejemplo: <b>912EC803B2CE49E4A541068D12345678</b>'
				),
				'merchant_id_prod' => array(
					'title'       => 'Merchant ID',
					'type'        => 'text',
					'description' => 'N&uacute;mero de comercio (MerchantId) provisto por el portal de Todo Pago'
				),

				'titulo_estados_pedidos' => array(
					'title'       => 'Estados del Pedido',
					'type'        => 'title',
					'description' => 'Datos correspondientes al estado de los pedidos',
					'id'          => 'estados_pedido_options'
				),

				'estado_inicio'     => array(
					'title'       => 'Estado cuando la transacción ha<br>sido iniciada',
					'type'        => 'select',
					'options'     => wc_get_order_statuses(),
					'default'     => 'wc-pending',
					'description' => 'Valor por defecto: Pendiente de pago'
				),
				'estado_aprobacion' => array(
					'title'       => 'Estado cuando la transacción ha<br>sido aprobada',
					'type'        => 'select',
					'options'     => wc_get_order_statuses(),
					'default'     => 'wc-completed',
					'description' => 'Valor por defecto: Completado'
				),
				'estado_rechazo'    => array(
					'title'       => 'Estado cuando la transacción ha<br>sido rechazada',
					'type'        => 'select',
					'options'     => wc_get_order_statuses(),
					'default'     => 'wc-failed',
					'description' => 'Valor por defecto: Falló'
				),
				'estado_offline'    => array(
					'title'   => 'Estado cuando la transacción ha<br>sido offline',
					'type'    => 'select',
					'options' => wc_get_order_statuses()
				),

				'expiracion_formulario_personalizado' => array(
					'title'       => 'Expiracion formulario personalizado',
					'type'        => 'select',
					'description' => 'Configurar tiempo de expiración del formulario de pago personalizado',
					'options'     => array(
						true  => 'SÍ',
						false => 'NO'
					)
				),
				'timeout_limite'                      => array(
					'title'       => 'Tiempo de expiración del formulario de pago',
					'type'        => 'number',
					'id'          => 'timeout_limite',
					'description' => 'Tiempo maximo en el que se puede realizar el pago en el formulario en milisegundos. Por defecto si no se envia el valor es de 1800000 (30 minutos)',
					'default'     => 1800000
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
				'wpnonce'                             => array(
					'type'        => 'hidden',
					'placeholder' => wp_create_nonce( 'getCredentials' )
				)
			) );

		return apply_filters( 'woocommerce_get_settings_todopago', $settings, 'Todopago' );
	}

}