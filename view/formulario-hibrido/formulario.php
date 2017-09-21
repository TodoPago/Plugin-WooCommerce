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
		<script src="<?php echo $env_url; ?>"></script>
		<link href="<?php echo "$form_dir/todopago-formulario.css "; ?>" rel="stylesheet" type="text/css">
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

			function _clean_errors(e) { //Se ajecuta al apretar pagar
				//Remueve la clases a todos los fields que los marcan como invalido y les pone las de campo valido
				e.preventDefault();
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
                                    <div class="form-row validate-required" data-validate_classes="validate-required">
                                        <select id="formaPagoCbx"></select> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="tp-content-form hidden-div" class="col2-set hidden-element">
                            
                            <div class="col-1">
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <input id="numeroTarjetaTxt" class="input-text form-field tp-no-cupon tp-cupon"/>
                                    <label id="numeroTarjetaLbl"></label>
                                </div>
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <input id="codigoSeguridadTxt"/>
                                    
                                    <label id="codigoSeguridadLbl"></label>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">                                    
                                    
                                    
                                </div>
           
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <select id="medioPagoCbx"></select>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <select id="bancoCbx"></select>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <select id="promosCbx"></select>
                                    <label id="promosLbl"></label>
                                </div>

                                <div>
                                    <select id="mesCbx" class="tp-no-cupon"></select>
                                    <select id="anioCbx" class="tp-no-cupon"></select>             
                                </div>        
                                                                
                                <div class="form-row form-row-first dateFields tp-no-cupon">
                                    <label id="fechaLbl"></label>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                     <label id="peiLbl"></label>    
                                     <input id="peiCbx"/>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <input id="tokenPeiTxt"/>
                                    <label id="tokenPeiLbl"></label>
                                </div>
                                
                            </div>
                            
                            <div class="col-2" data-validate_classes="validate-required">
                                <div class="form-row">
                                    <input id="nombreTxt" class="input-text form-field tp-no-cupon tp-cupon" value="<?php echo $nombre_completo; ?>"/>
                                </div>
                                
                                <div class="form-row form-row-first tp-no-cupon"  data-validate_classes="validate-required">
                                    <select id="tipoDocCbx"></select>
                                </div>
                                
                                <div class="form-row form-row-last tp-no-cupon" data-validate_classes="validate-required">
                                    <input id="nroDocTxt" class="input-text form-field tp-no-cupon tp-cupon" maxlength="10"/>
                                    <label id="nroDocLbl" class="label_error"></label>
                                </div>
                                
                                <div class="form-row tp-no-cupon" data-validate_classes="validate-required">
                                    <input id="emailTxt" class="input-text form-field tp-no-cupon" value="<?php echo $email; ?>" />
                                    <label id="emailLbl" class="label_error"></label>
                                </div> 
                            </div>    
                            
                        </div>    
                        
                        <div id="tp-bt-wrapper" class="tp-right">
                            <button id="MY_btnPagarConBilletera" class="tp-button button alt"></button>
                        </div>
                        <div id="tp-bt-wrapper" class="tp-right">
                            <button id="MY_btnConfirmarPago" class="tp-button button alt"></button>
                        </div>
		</form>
 
	</body>
	<script>
                                    
		/************* CONFIGURACION DEL API ************************/
                $(document).ready(function(){
                    $("#tp-form-tph").submit(function(event){
                        event.preventDefault();
                    });
                });

		/************* CONFIGURACION DEL API ***********************/
                window.TPFORMAPI.hybridForm.initForm({
                    callbackValidationErrorFunction: 'validationCollector',
                    callbackBilleteraFunction: 'billeteraPaymentResponse',
                    callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
                    callbackCustomErrorFunction: 'customPaymentErrorResponse',
                    botonPagarId: 'MY_btnConfirmarPago',
                    botonPagarConBilleteraId: 'MY_btnPagarConBilletera',
                    modalCssClass: 'modal-class',
                    modalContentCssClass: 'modal-content',
                    beforeRequest: 'initLoading',
                    afterRequest: 'stopLoading'
                });

                /************* FUNCIONES CALLBACKS *************************/
                function validationCollector(parametros) {
                    console.log("My validator collector");
                    console.log(parametros.field + " ==> " + parametros.error); 
                    
                    var input = parametros.field;
                    
                    console.log(input);
                    
                    if (input.search("Txt") !== -1) {
                        label = input.replace("Txt", "Lbl");

                    } else {
                        label = input.replace("Cbx", "Lbl");
                    }

                    if (document.getElementById(label) != null) {
                        $(".label_error").text("");
                        $("#"+label).text(parametros.error);
                        //document.getElementById(label).innerHTML = parametros.error;
                    }
/*
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
*/
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
                    $(".hidden-element").css("opacity","1");
		}
		function editResponse(response,responseOK){

                    var redirectOK="<?php echo $return_URL_OK."&Answer="; ?>" + response.AuthorizationKey;
                    var redirectERROR="<?php echo $return_URL_ERROR."&Answer="; ?>" + response.AuthorizationKey;
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

                    document.location=redirection;
		}

	</script>

    <script>
    	jQuery(document).ready( function() { 
            
                setTimeout(function(){
                    $("#hidden-div").removeClass("hidden-element");
                },3000);
                
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
