=== Plugin Name ===
Contributors: todopago
Tags: todopago, payment, woocommerce
Requires at least: 3.5.7
Tested up to: 4.6.1
Stable tag: V1.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin de integración de TodoPago para WooCommerce

== Description ==
WooCommerce- Módulo Todo Pago (v1.12.0)

== Consideraciones Generales ==
El plug in de pagos de <strong>Todo Pago</strong>, provee a las tiendas WooCommerce de un nuevo método de pago, integrando la tienda al gateway de pago.
La versión de este plug in esta testeada en PHP 5.3 en adelante y WordPress 3.7.5 con WooCommerce 2.3.5.


== Instalación ==
1. Descomprimir el archivo woocommerce-plugin-master.zip. 
2. Copiar carpeta woocommerce-plugin-master al directorio de plugins de wordpress ("raíz de wordpress"/wp-content/plugins). 
3. Renombrarla woocommerce-plugin-master por woocommerce-plugin.

Observaciónes:

1. Descomentar: <em>extension=php_soap.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.
Descomentar: <em>extension=php_openssl.dll</em> del php.ini 

2. En caso de tener conflictos con Jquery por los diferentes temas, descomentar la siguiente linea que se encuentra al final del index.php


== Configuración ==

####Activación
La activación se realiza como cualquier plugin de Wordpress: Desde Plugins -> Plugins instalados -> activar el plugin de nombre <strong>TodoPago para WooCommerce</strong>.<br />

####Configuración plug in
Para llegar al menu de configuración del plugin ir a: <em>WooCommerce -> Ajustes</em> y seleccionar Finalizar Compra de la solapa de configuraciones que aparece en la parte superior. Entre los medios de pago aparecerá la opción de nombre <strong>Todopago</strong>.<br />

####Formulario Hibrido
En la configuracion del plugin tambien estara la posibilidad de mostrarle al cliente el formulario de pago de TodoPago integrada en el sitio. 
Para esto , en la configuracion se debe seleccionar la opcion Integrado en el campo de seleccion de formulario

El formulario tiene dos formas de pago, ingresando los datos de una tarjeta ó utilizando la billetera de Todopago. Al ir a "Pagar con Billetera" desplegara una ventana que permitira ingresar a billetera y realizar el pago.

####Obtener datos de configuracion
Se puede obtener los datos de configuracion del plugin con solo loguearte con tus credenciales de Todopago. 
a. Ir a la opcion Obtener credenciales
b. Loguearse con el mail y password de Todopago.
c. Los datos se cargaran automaticamente en los campos Merchant ID y Security code en el ambiente correspondiente y solo hay que hacer click en el boton guardar datos y listo.

####Configuración de Maximo de Cuotas
Se puede configurar la cantidad máxima de cuotas que ofrecerá el formulario de TodoPago con el campo cantidad máxima de cuotas. Para que se tenga en cuenta este valor se debe habilitar el campo Habilitar máximo de cuotas y tomará el valor fijado para máximo de cuotas. En caso que esté habilitado el campo y no haya un valor puesto para las cuotas se tomará el valor 12 por defecto.

== Características ==

#### Consulta de Transacciones
Se puede consultar <strong>on line</strong> las características de la transacción en el sistema de Todo Pago al hacer click en el número de orden en la parte de Status de las Operaciones.<br />

#### Devoluciones
Es posible realizar devoluciones o reembolsos mediante el procedimiento habitual de WooCommerce. Para ello dirigirse en el menú a WooCommerce->Pedidos, "Ver" la orden deseada (Esta debe haber sido realizada con TodoPago) y encontrará una sección con el título **Pedido Productos**, dentro de esta hay un botón *Reembolso* al hacer click ahí nos solicitará el monto a reembolsar y nos dará la opción de *Reembolsar con TodoPago*.
