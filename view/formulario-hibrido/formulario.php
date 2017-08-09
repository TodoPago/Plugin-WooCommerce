<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    add_filter('show_admin_bar', '__return_false');
get_header();
?>
	<html>

	<head>
		<title>Formulario Híbrido</title>
		<meta charset="UTF-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="<?php echo "$env_url/resources/TPHybridForm-v0.1.js"; ?>"></script>
		<link href="<?php echo "$form_dir/todopago-formulario.css " ?>" rel="stylesheet" type="text/css">
		<script>

			$(window).load(function() {
				$("#formaDePagoCbx").change(function () {
					if(this.value == 500 || this.value == 501){
						$(".form-row.tp-no-cupon").each(function(div) {
							$(this).removeClass($(this).attr("data-validate_classes"));
						});
					}else{
						$(".form-row.tp-no-cupon").each(function() {
							$(this).addClass($(this).attr("data-validate_classes"));
						});
					}
				});

				$("#MY_btnConfirmarPago").click(_clean_errors);
                                
                                $("#MY_btnPagarConBilletera").click(_clean_errors);

				$(".form-field").change(_unclean_errors);

			});

			function _clean_errors() { //Se ajecuta al apretar pagar
				//Remueve la clases a todos los fields que los marcan como invalido y les pone las de campo valido
				$("#tp-form-tph").find(".form-row").removeClass("woocommerce-invalid woocommerce-invalid-required-field").addClass("woocommerce-validated");
				//limpia los errores
				$(".woocommerce-error").empty();
				$(".woocommerce-error").hide();
				//$("errors_clean").val("true");
				//Si hay errores pendientes los agrego al div de errores (Los errores pendientes se ponen en el div de errores en validationCollector)
				$("#pending_errors").children().each(function _add_errors() {
					$('.woocommerce-error').append("<li>"+$(this).val()+"</li>");
					$('.woocommerce-error').show();
					$($(this).attr('data-element')).parent().addClass("woocommerce-invalid woocommerce-invalid-required-field").removeClass("woocommerce-validated");
				})
			}

			function _unclean_errors() { //Se ejecuta al hacer cambios en alguno de los campos del formulario
				//Lo marco como "sucio" lo cuál significa que validationCollector pondrá los errores en el div de errores pendientes, para lo cuál lo vacía.
				$("#errors_clean").val("false");
				$("#pending_errors").empty();
			}
		</script>
	</head>

	<body class="contentContainer">
		<form id="tp-form-tph">
			<input id="errors_clean" type="hidden" value="true" />
			<div id="pending_errors" hidden></div>
			<ul class="woocommerce-error" hidden="true">
			</ul>
			<div id="tp-logo"></div>
			<div id="tp-content-form" class="col2-set">
				<div class="col-1">
					<div class="form-row validate-required" data-validate_classes="validate-required">
						<label for="formaDePagoCbx">Forma de pago <abbr class="required" title="obligatorio">*</abbr></label>
						<select id="formaDePagoCbx" class="form-field"></select>
					</div>
					<div class="form-row tp-no-cupon" data-validate_classes="validate-required">
						<select id="bancoCbx" class="form-field"></select>
					</div>
					<div class="form-row tp-no-cupon" data-validate_classes="validate-required">
						<select id="promosCbx" class="form-field"></select>
						<label id="labelPromotionTextId" class="left tp-label"></label>
						<div class="clear"></div>
					</div>
					<!-- Para los casos en el que el comercio opera con PEI -->
					<div class="form-row tp-no-cupon">
						<label id="labelPeiCheckboxId"></label>
						<input id="peiCbx"/>
					</div>

					<div class="form-row tp-no-cupon" data-validate_classes="validate-required">
						<label for="numeroTarjetaTxt">N&uacute;mero <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="numeroTarjetaTxt" class="input-text form-field" />
					</div>
					<div class="form-row form-row-first dateFields tp-no-cupon" data-validate_classes="validate-required">
						<label for="mesTxt">Fecha de Vto. <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="mesTxt" class="left input-text form-field">
						<input id="anioTxt" class="left input-text form-field">
						<div class="clear"></div>
					</div>
					
                                        <div class="form-row form-row-first dateFields tp-no-cupon" data-validate_classes="validate-required">
                                                <label id="labelCodSegTextIdy" for="labelCodSegTextId">Ultimos 3 numeros del dorso. <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="codigoSeguridadTxt" class="left input-text form-field">
						<span id="labelCodSegTextId" class="left tp-label" style="visibility: hidden;"></span>
                                                <div class="clear"></div>
                                        </div>
				<div class="col-2" data-validate_classes="validate-required">
					<div class="form-row">
						<label for="apynTxt">Nombre y Apellido <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="apynTxt" class="input-text form-field" />
						<span id="labelApynTextId" class="left tp-label tp-no-cupon">Tal c&oacute;mo aparece en la tarjeta</span>
					</div>
					<div class="form-row form-row-first tp-no-cupon"  data-validate_classes="validate-required">
						<label for="tipoDocCbx">Tipo de documento<abbr class="required" title="obligatorio">*</abbr></label>
						<select id="tipoDocCbx" class="form-field"></select>
					</div>
					<div class="form-row form-row-last tp-no-cupon" data-validate_classes="validate-required">
						<label for="nroDocTxt">Nro. de documento <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="nroDocTxt" class="input-text form-field" />
					</div>
					<div class="form-row form-row-wide" data-validate_classes="validate-required validate-email">
						<label>E-mail <abbr class="required" title="obligatorio">*</abbr></label>
						<input id="emailTxt" class="input-text form-field" />
						<br/>
					</div>
					<div><!-- Para los casos en el que el comercio opera con PEI -->
				    	<label id="labelPeiTokenTextId"></label>
						<input id="peiTokenTxt"/>
					</div>
				</div>
			</div>
		</form>
        <div id="tp-bt-wrapper" class="tp-right">
			<button id="MY_btnPagarConBilletera" class="tp-button button alt"></button>
		</div>
		<div id="tp-bt-wrapper" class="tp-right">
			<button id="MY_btnConfirmarPago" class="tp-button button alt"></button>
		</div>
	</body>
	<script>
		/************* CONFIGURACION DEL API ************************/

		window.TPFORMAPI.hybridForm.initForm({
			callbackValidationErrorFunction: 'validationCollector',
			callbackBilleteraFunction: 'billeteraPaymentResponse',
			botonPagarConBilleteraId: 'MY_btnPagarConBilletera',
			modalCssClass: 'tp-modal-class',
			modalContentCssClass: 'tp-modal-content',
			beforeRequest: 'initLoading',
			afterRequest: 'stopLoading',
			callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
			callbackCustomErrorFunction: 'customPaymentErrorResponse',
			botonPagarId: 'MY_btnConfirmarPago',
		});


		function validationCollector(parametros) {
			console.log("My validator collector");
			console.log(parametros.field + " ==> " + parametros.error);
			//Si está "limpio" puede ser porque ya se ejecutó el método _clean_errors() o porque no hubo cambios desde la vez anterior en la que se tocó el botón, en ese caso el div pending_errors debería contener los errores previos
			if ($('#errors_clean').val() == "true" && $("#pending_errors").children().length == 0) {
				$('.woocommerce-error').append("<li>"+parametros.error+"</li>");
				$('.woocommerce-error').show();
				$('#'+parametros.field).parent().addClass("woocommerce-invalid woocommerce-invalid-required-field").removeClass("woocommerce-validated");
			}

			//Agrego los errores al div de errores pendientes, siempre y cuando no estén aún (Esto puede ocurrir si el usuario volvió a intentar pagar sin hacer cambios en los campos del formulario)
			if ($("#error_"+parametros.field).length == 0) {
				$("#pending_errors").append('<input type="hidden" id="error_'+parametros.field+'" value="'+parametros.error+'" data-element="#'+parametros.field+'" />');
			}
		}

		function billeteraPaymentResponse(response) {
			console.log("My wallet callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
            editResponse(response,true);
		}

		function customPaymentSuccessResponse(response) {
			console.log("My custom payment success callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			editResponse(response,true);
		}

		function customPaymentErrorResponse(response) {
			console.log("Mi custom payment error callback");
			console.log(response.ResultCode + " : " + response.ResultMessage);
			editResponse(response,false);
		}

		function initLoading() {
			console.log('Cargando');
		}

		function stopLoading() {
			console.log('Stop loading...');
		}

		function editResponse(response,responseOK){

	    	var redirectOK="<?php echo "$return_URL_OK&Answer="; ?>" + response.AuthorizationKey;
			var redirectERROR="<?php echo "$return_URL_ERROR&Answer="; ?>" + response.AuthorizationKey;
			var timeoutOK="&timeout=ok";
			var timeoutERROR="&timeout=expired&error_message="+response.ResultMessage;
			var redirection="";

			if(!response.AuthorizationKey){//si se vence el tiempo del timeout(AuthorizationKey nulo)
				redirection=redirectERROR+timeoutERROR;
			}else{
				if(responseOK){
					redirection=redirectOK+timeoutOK;
				}else{
					redirection=redirectERROR+timeoutOK;
				}
			}

			window.location.href = redirection;
		}

	</script>

    <script>
    	jQuery(document).ready( function() { 
    		jQuery.get( "<?php echo get_site_url() ?>?TodoPago_redirect=true&form=hib&order=<?php echo $order->id ?>",function( data ) { 
				window.TPFORMAPI.hybridForm.setItem({
					publicKey: JSON.parse(data).prk,
					defaultNombreApellido: '<?php echo "$firstname $lastname"; ?>',
					defaultMail: '<?php echo "$email"; ?>'
				});
    		}); 
    	});
    </script>

	</html>
<?php
get_footer();
