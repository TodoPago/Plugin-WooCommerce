<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

//var_dump("Url del formulario: ".$url_form);

?>
<link href="<?php echo "$form_dir/flexbox.css"; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo "$form_dir/form_todopago.css"; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo "$form_dir/queries.css"; ?>" rel="stylesheet" type="text/css">
<script src="<?php echo "$form_dir/jquery-3.2.1.min.js"; ?>"></script>
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

<div class="progress">
    <div class="progress-bar progress-bar-striped active" id="loading-hibrid">
    </div>
</div>

<div class="tp_wrapper" id="tpForm">
    <section class="tp-total tp-flex">
        <div>
            <strong>Total a pagar $<?php echo $amount; ?></strong>
        </div>
        <div>
            Elegí tu forma de pago
        </div>
    </section>

    <section class="billetera_virtual_tp tp-flex tp-flex-responsible">
        <div class="tp-flex-grow-1 tp-bloque-span texto_billetera_virtual text_size_billetera">
            <p>Pagá con tu <strong>Billetera Virtual Todo Pago</strong></p>
            <p>y evitá cargar los datos de tu tarjeta</p>
        </div>
        <div class="tp-flex-grow-1 tp-bloque-span">
            <button id="btn_billetera" title="Iniciar Sesión" class="tp_btn tp_btn_sm text_size_billetera">
                Iniciar Sesión
            </button>
        </div>
    </section>

    <section class="billeterafm_tp">
        <div class="field field-payment-method">
            <label for="formaPagoCbx" class="text_small">Forma de Pago</label>
            <div class="input-box">
                <select id="formaPagoCbx" class="tp_form_control"></select>
                <label class="tp-error" id="formaPagoCbxError"></label>
            </div>
        </div>
    </section>

    <section class="billetera_tp" id="tp-tarjetas">
        <div class="tp-row">
            <p>
                Con tu tarjeta de crédito o débito
            </p>
        </div>
        <!-- Número de tarjeta y banco -->
        <div class="tp-bloque-full tp-flex tp-flex-responsible tp-main-col">
            <!-- Tarjeta -->
            <div class="tp-flex-grow-1">
                <label for="numeroTarjetaTxt" class="text_small">Número de Tarjeta</label>
                <input id="numeroTarjetaTxt" class="tp_form_control" maxlength="19" title="Número de Tarjeta"
                       min-length="14" autocomplete="off">
                <img src="<?php echo $form_dir; ?>/images/empty.png" id="tp-tarjeta-logo"
                     alt=""/>
                <!-- <span class="error" id="numeroTarjetaTxtError"></span> -->
                <label id="numeroTarjetaLbl" class="tp-error"></label>
            </div>
            <!-- Banco -->
            <div class="tp-flex-grow-1">
                <label for="bancoCbx" class="text_small">Banco</label>
                <select id="bancoCbx" class="tp_form_control" placeholder="Selecciona banco"></select>
                <span class="tp-error" id="bancoCbxError"></span>
            </div>
            <div class="tp_col tp-bloque-span payment-method">
                <label for="medioPagoCbx" class="text_small">Medio de Pago</label>
                <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
                <span class="tp-error" id="medioPagoCbxError"></span>
            </div>
        </div>
        <div class="tp-row tp-bloque-full tp-flex tp-flex-responsible tp-main-col" id="pei-block">
            <section class="tp-row" id="peibox">
                <label id="peiLbl" for="peiCbx" class="text_small right">Pago con PEI</label>
                <label class="switch" id="switch-pei">
                    <input type="checkbox" id="peiCbx">
                    <span class="slider round"></span>
                    <span id="slider-text"></span>
                </label>
            </section>
        </div>

        <!--div class="tp_row">
            <div class="tp_col tp-bloque-span">
                <label for="medioPagoCbx" class="text_small">Medio de Pago</label>
                <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
                <span class="error" id="medioPagoCbxError"></span>
            </div>
        </div-->

        <!-- Vencimiento + DNI-->
        <div class="tp-bloque-full tp-flex tp-flex-row tp-flex-responsible tp-flex-space-between tp-main-col">
            <!-- vencimiento -->
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- títulos -->
                <div class="tp-row tp-flex tp-flex-space-between tp-title">
                    <div class="tp-flex-grow-1">
                        <label for="mesCbx" class="text_small">Vencimiento</label>
                    </div>
                    <div class="tp-flex-grow-1 tp-title-right">
                        <label for="codigoSeguridadTxt" class="text_small"></label>
                    </div>
                    <div class="tp-flex-grow-1 tp-title-right">
                        <label id="codigoSeguridadTexto" for="codigoSeguridadTxt" class="text_small">Código de
                            Seguridad</label>
                    </div>
                </div>
                <!-- inputs -->
                <div class="tp-row tp-flex tp-flex-space-between tp-input-row" id="tp-inputs-card">
                    <div class="tp-flex-grow-1">
                        <select id="mesCbx" maxlength="2" class="tp_form_control" placeholder="Mes"></select>
                    </div>
                    <div class="tp-flex-grow-1">
                        <select id="anioCbx" maxlength="2" class="tp_form_control"></select>
                    </div>
                    <div class="tp-flex-grow-1">
                        <input id="codigoSeguridadTxt" class="tp_form_control" maxlength="4"
                               autocomplete="off"/>
                    </div>
                    <div class="tp-cvv-helper-container">
                        <div class="tp-anexo clave-ico" id="tp-cvv-caller"></div>
                        <div id="tp-cvv-helper">
                            <p>
                                Para Visa, Master, Cabal y Diners, los 3 dígitos se encuentran en el
                                <strong>dorso</strong>
                                de
                                tu tarjeta. (izq)
                            </p>
                            <p>
                                Para Amex, los 4 dígitos se encuentran en el frente de tu tarjeta. (der)
                            </p>
                            <img id="tp-cvv-helper-img" alt="ilustración tarjetas"
                                 src="<?php echo $form_dir; ?>/images/clave-ej.png">
                        </div>
                    </div>
                </div>
                <!-- warnings -->
                <div class="tp-row tp-flex tp-error-title">
                    <label id="fechaLbl" class="left tp-error"></label>
                    <label class="left tp-error"></label>
                    <label id="codigoSeguridadLbl" class="left tp-label spacer tp-error"></label>
                </div>
            </div>
            <!-- DNI -->
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- títulos -->
                <div class="tp-row tp-flex tp-flex-space-between tp-title">
                    <div class="tp-flex-1">
                        <label for="tipoDocCbx" class="text_small">Tipo</label>
                    </div>
                    <div class="tp-flex-3 tp-title-right">
                        <label id="tp-dni-numero-title" for="NumeroDocCbx" class="text_small">Número</label>
                    </div>
                </div>
                <!-- inputs -->
                <div class="tp-row tp-flex tp-input-row">
                    <div class="tp-flex-1">
                        <select id="tipoDocCbx" class="tp_form_control"></select>
                    </div>
                    <div class="tp-flex-3" id="tp-dni-numero">
                        <input id="nroDocTxt" maxlength="10" type="text" class="tp_form_control"
                               autocomplete="off"/>
                    </div>
                </div>
                <!-- warnings -->
                <div class="tp-row tp-flex tp-error-title">
                    <label class="tp-error tp-flex-1"></label>
                    <label class="tp-error tp-flex-3" id="nroDocLbl"></label>
                </div>
            </div>
        </div>


        <!-- Nombre y Apellido, y Mail -->
        <div class="tp-bloque-full tp-flex tp-flex-responsible tp-main-col">
            <!-- Nombre y Apellido -->
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- títulos -->
                <div class="tp-row tp-flex tp-flex-space-between tp-title">
                    <label for="nombreTxt" class="text_small">Nombre y Apellido</label>
                </div>
                <!-- inputs -->
                <div class="tp-row tp-flex tp-input-row">
                    <input id="nombreTxt" class="tp_form_control" autocomplete="off" placeholder="" maxlength="50">
                </div>
                <!-- warnings -->
                <div class="tp-row tp-flex tp-error-title">
                    <label id="nombreLbl" class="tp-error"></label>
                </div>
            </div>
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- títulos -->
                <div class="tp-row tp-flex tp-title">
                    <label for="emailTxt" class="text_small">Email</label>
                </div>
                <!-- inputs -->
                <div class="tp-flex-grow-1">
                    <input id="emailTxt" type="email" class="tp_form_control tp-input-row"
                           placeholder="nombre@mail.com"
                           data-mail=""
                           autocomplete="off"/>
                </div>
                <!-- warnings -->
                <div class="tp-row tp-flex tp-error-title">
                    <label id="emailLbl" class="left tp-label spacer tp-error"></label>
                </div>
            </div>
        </div>

        <!-- Cantidad de cuotas y CFT -->
        <div class="tp-bloque-full tp-flex tp-flex-responsible tp-main-col">
            <!-- Cantidad de cuotas -->
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- titulos -->
                <div class="tp-row tp-flex tp-flex-space-between tp-title">
                    <label for="promosCbx" class="text_small">Cantidad de cuotas</label>
                </div>
                <!-- inputs -->
                <div class="tp-row tp-flex tp-input-row">
                    <select id="promosCbx" class="tp_form_control"></select>
                </div>
                <!-- errores -->
                <div class="tp-row">
                    <div class="tp-flex-grow-1">
                        <label class="tp-error" id="promosCbxError"></label>
                    </div>
                </div>
            </div>
            <!--  CFT -->
            <div class="tp-flex-grow-1 tp-flex tp-flex-col">
                <!-- títulos -->
                <div class="tp-row tp-flex tp-flex-space-between tp-title">
                    <label></label>
                </div>
                <!-- select -->
                <div class="tp-row tp-input-row">
                    <div class="promos-lbl-container tp-flex">
                        <label id="promosLbl" class="left"></label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token de PEI -->
        <div class="tp-bloque-full tp-flex tp-flex-responsible tp-main-col">
            <div class="tp-flex-grow-1">
                <label id="tokenPeiLbl" for="tokenPeiTxt" class="text_small"></label>
                <input id="tokenPeiTxt" class="tp_form_control tp-input-row"/>
            </div>
            <div class="tp-flex-grow-1">
            </div>
        </div>

        <!-- Pagar -->
        <div class="tp_row">
            <div class="tp_col tp_span_2_of_2">
                <button id="btn_ConfirmarPago" class="tp_btn" title="Pagar" class="button"><span>Pagar</span>
                </button>
            </div>
            <div class="tp_col tp_span_2_of_2">
                <div class="confirmacion">
                    Al confirmar el pago acepto los <a
                            href="https://www.todopago.com.ar/terminos-y-condiciones-comprador" target="_blank"
                            title="Términos y Condiciones" id="tycId"
                            class="tp_color_text">Términos
                        y Condiciones</a> de Todo Pago.
                </div>
            </div>
        </div>
    </section>
    <div class="tp_row">
        <div id="tp-powered">
            Powered by <img id="tp-powered-img" src="<?php echo $form_dir; ?>/images/tp_logo_prod.png"/>
        </div>
    </div>
</div>


<script language="javascript">
    var tpformJquery = $.noConflict();
    var urlScript = "<?php echo $env_url; ?>";
    //securityRequesKey, esta se obtiene de la respuesta del SAR
    var urlSuccess = "<?php echo $return_URL_SUCCESS ?>";
    var urlError = "<?php echo $return_URL_ERROR?>";
    var security = "<?php echo $responseSAR->PublicRequestKey; ?>";
    var mail = "<?php echo $email; ?>";
    var completeName = "<?php echo $nombre_completo; ?>";
    var defDniType = 'DNI';
    var medioDePago = document.getElementById('medioPagoCbx');
    var tarjetaLogo = document.getElementById('tp-tarjeta-logo');
    var poweredLogo = document.getElementById('tp-powered-img');
    var numeroTarjetaTxt = document.getElementById('numeroTarjetaTxt')
    var btnBilletera = document.getElementById('btn_billetera');
    var todoPagoSection = document.getElementById('tp-tarjetas');
    var poweredLogoUrl = "<?php echo $form_dir;?>/images/";
    var emptyImg = "<?php echo $form_dir;?>/images/empty.png";
    var peiCbx = tpformJquery("#peiCbx");
    var switchPei = tpformJquery("#switch-pei");
    var sliderText = tpformJquery("#slider-text");
    var helperCaller = tpformJquery("#tp-cvv-caller");
    var helperPopover = tpformJquery("#tp-cvv-helper");
    var tipoDePago = "<?php echo $paymentMethod; ?>"


    var idTarjetas = {
        42: 'VISA',
        43: 'VISAD',
        1: 'AMEX',
        2: 'DINERS',
        6: 'CABAL',
        7: 'CABALD',
        14: 'MC',
        15: 'MCD'
    };

    var diccionarioTarjetas = {
        'VISA': 'VISA',
        'VISA DEBITO': 'VISAD',
        'AMEX': 'AMEX',
        'DINERS': 'DINERS',
        'CABAL': 'CABAL',
        'CABAL DEBITO': 'CABALD',
        'MASTER CARD': 'MC',
        'MASTER CARD DEBITO': 'MCD',
        'NARANJA': 'NARANJA'
    };

    /************* HELPERS *************/

    numeroTarjetaTxt.onblur = clearImage;

    function clearImage() {
        tarjetaLogo.src = emptyImg;
    }

    function cardImage(select) {
        var tarjeta = idTarjetas[select.value];
        if (tarjeta === undefined) {
            tarjeta = diccionarioTarjetas[select.textContent];
        }
        if (tarjeta !== undefined) {
            tarjetaLogo.src = 'https://forms.todopago.com.ar/formulario/resources/images/' + tarjeta + '.png';
            tarjetaLogo.style.display = 'block';
        }
    }


    /************* SMALL SCREENS DETECTOR (?) *************/
    function detector() {
        console.log(tpformJquery("#tp-form").width());
        var tpFormWidth = tpformJquery("#tp-form").width();
        var input = tpformJquery("input");
        var select = tpformJquery("select");
        if (tpFormWidth < 500) {
            input.css("font-size", 9);
            select.css("font-size", 9);
        }
    }

    loadScript(urlScript, function () {
        loader();
    });

    function loadScript(url, callback) {
        var entorno = (url.indexOf('developers') === -1) ? 'prod' : 'developers';
        poweredLogo.src = poweredLogoUrl + 'tp_logo_' + entorno + '.png';
        var script = document.createElement("script");
        script.type = "text/javascript";
        if (script.readyState) {  //IE
            script.onreadystatechange = function () {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {  //et al.
            script.onload = function () {
                callback();
            };
        }
        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    function loader() {
        tpformJquery("#loading-hibrid").css("width", "50%");
        setTimeout(function () {
            ignite();
            tpformJquery(".payment-method").hide();
            tpformJquery(".billeterafm_tp").hide();
        }, 100);

        setTimeout(function () {
            tpformJquery("#loading-hibrid").css("width", "100%");
        }, 1000);

        setTimeout(function () {
            tpformJquery(".progress").hide('fast');
            if (tipoDePago === "<?php echo \TodoPago\Utils\Constantes::TODOPAGO_BILLETERA; ?>") {
                btnBilletera.click();
                todoPagoSection.style.opacity = '0';
                todoPagoSection.style.height = '0px';
                todoPagoSection.style.display = 'none';
            }
        }, 2000);

        setTimeout(function () {
            btnBilletera.innerText = "Iniciar Sesión";
            tpformJquery("#tpForm").fadeTo('fast', 1);
        }, 2200);
    }

    //callbacks de respuesta del pago
    window.validationCollector = function (parametros) {
        console.log("My validator collector");
        console.log(parametros.field + " -> " + parametros.error);
        tpformJquery("#peibox").hide();
        var input = parametros.field;
        if (input.search("Txt") !== -1) {
            label = input.replace("Txt", "Lbl");
        } else {
            label = input.replace("Cbx", "Lbl");
        }
        if (document.getElementById(label) !== null) {
            document.getElementById(label).innerText = parametros.error;
        }
    };

    function billeteraPaymentResponse(response) {
        console.log("Iniciando billetera");
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        if (response.AuthorizationKey) {
            window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
        } else {
            window.location.href = urlError + "&Error=" + response.ResultMessage;
        }
    }

    function customPaymentSuccessResponse(response) {
        console.log("Success");
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
    }

    function customPaymentErrorResponse(response) {
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        if (response.AuthorizationKey) {
            window.location.href = urlError + "&Answer=" + response.AuthorizationKey;
        } else {
            window.location.href = urlError + "&Error=" + response.ResultMessage;
        }
    }

    window.initLoading = function () {
        console.log("init");
        cardImage(medioDePago);
        //tpformJquery("#codigoSeguridadLbl").html("");
        tpformJquery("#peibox").hide();
    };

    window.stopLoading = function () {
        console.log('Stop loading...');
        tpformJquery("#peibox").hide();
        if (document.getElementById('peiLbl').style.display === "inline-block") {
            tpformJquery("#peibox").css('display', 'table-cell');
        } else {
            tpformJquery("#peibox").hide("fast");
            peiCbx.prop("checked", false);
        }
        var rowPei = tpformJquery("#row-pei");
        //tpformJquery.uniform.restore();
        if (peiCbx.css('display') !== 'none') {
            activateSwitch(getPEIState());
        } else {
            rowPei.css("display", "none");
            peiCbx.prop("checked", false);
        }
    };

    // Verifica que el usuario no haya puesto para solo pagar con PEI y actúa en consecuencia
    function activateSwitch(soloPEI) {
        readPeiCbx();
        if (!soloPEI) {
            switchPei.click(function () {
                console.log("CHECKED", peiCbx.prop("checked"));
                if (peiCbx.prop("checked")) {
                    switchPei.prop("checked", true);
                    peiCbx.prop("checked", true);
                    sliderText.text("SÍ");
                    sliderText.css('transform', 'translateX(0)');
                } else {
                    switchPei.prop("checked", false);
                    peiCbx.prop("checked", false);
                    sliderText.text("NO");
                    sliderText.css('transform', 'translateX(26px)');
                }
            });
        }
    }

    function readPeiCbx() {
        if (peiCbx.prop("checked", true)) {
            switchPei.prop("checked", true);
            sliderText.text("SÍ");
            sliderText.css('transform', 'translateX(0)');
        } else {
            switchPei.prop("checked", true);
            sliderText.text("NO");
            sliderText.css('transform', 'translateX(26px)');
        }
    }

    function getPEIState() {
        return (peiCbx.prop("disabled"));
    }

    tpformJquery('#peiLbl').bind("DOMSubtreeModified", function () {
        tpformJquery("#peibox").hide();
    });

    helperCaller.click(function () {
        helperPopover.toggle(500);
    });

    helperPopover.click(function () {
        helperPopover.toggle(500);
    });

    function ignite() {
        /************* CONFIGURACION DEL API ************************/
        window.TPFORMAPI.hybridForm.initForm({
            callbackValidationErrorFunction: 'validationCollector',
            callbackBilleteraFunction: 'billeteraPaymentResponse',
            callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
            callbackCustomErrorFunction: 'customPaymentErrorResponse',
            botonPagarId: 'btn_ConfirmarPago',
            botonPagarConBilleteraId: 'btn_billetera',
            modalCssClass: 'modal-class',
            modalContentCssClass: 'modal-content',
            beforeRequest: 'initLoading',
            afterRequest: 'stopLoading'
        });
        /************* SETEO UN ITEM PARA COMPRAR ************************/
        window.TPFORMAPI.hybridForm.setItem({
            publicKey: security,
            defaultNombreApellido: completeName,
            defaultMail: mail,
            defaultTipoDoc: defDniType
        });
    }

</script>
