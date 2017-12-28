<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

//var_dump("Url del formulario: ".$url_form);

?>


<script src="<?php echo $env_url; ?>"></script>
<link href="<?php echo "$form_dir/todopago-formulario.css"; ?>" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<div class="progress">
    <div class="progress-bar progress-bar-striped active" id="loading-hibrid">
    </div>
</div>
<div class="formuHibrido container-fluid" id="tpForm">
    <!-- row 0 -->
    <div class="static-row">
        <div class="bloque bloque-medium">
            <img src="https://portal.todopago.com.ar/app/images/logo.png" alt="todopago" id="todopago_logo">
        </div>
    </div>
    <!-- TARJETA MEDIO DE PAGO -->
    <div class="row">
        <div class="bloque bloque-medium float-left">
            <select id="formaPagoCbx" class="input button-medium"></select>
        </div>
        <div class="bloque bloque-big float-left loaded-form">
            <input id="numeroTarjetaTxt" class="input">
            <label id="numeroTarjetaLbl" for="numeroTarjetaTxt" class="advertencia"></label>
        </div>
    </div>
    <!-- /TARJETA MEDIO DE PAGO -->

    <!-- TARJETAS -->
    <div class="loaded-form">
        <!-- PEI -->
        <div class="row" id="row-pei">
            <div class="col">
                <div class="bloque bloque-small">
                    <input id="peiCbx" class="input ">
                </div>
                <div class="bloque bloque-medium">
                    <label id="peiLbl" for="peiCbx"></label>
                </div>
            </div>
        </div>
        <!-- /PEI -->
        <!-- MEDIO DE PAGO -->
        <div class="row">
            <div class="bloque bloque-medium float-left">
                <select id="medioPagoCbx" class="input"></select>
            </div>
            <div class="bloque bloque-medium float-left">
                <select id="bancoCbx" class="input "></select>
            </div>
        </div>
        <!-- /MEDIO DE PAGO -->
        <!-- MES AÑO CÓDIGO -->
        <div class="row">
            <div class="bloque bloque-small float-left">
                <select id="mesCbx" class="input "></select>
                <label id="mesLbl" class="advertencia" for="mesCbx"></label>
            </div>
            <div class="bloque bloque-small float-left">
                <select id="anioCbx" class="input"></select>
                <label id="fechaLbl" class="advertencia" for="anioCbx"></label>
            </div>
            <div class="bloque bloque-small float-left">
                <input id="codigoSeguridadTxt" class="input">
                <label id="codigoSeguridadLbl" for="codigoSeguridadTxt" class="advertencia"></label>
            </div>
        </div>
        <!-- /MES AÑO CÓDIGO -->
        <!-- NOMBRE -->
        <div class="row">
            <div class="bloque bloque-full float-right">
                <input id="nombreTxt" class="input button-big">
                <label id="nombreLbl" for="nombreTxt" class="advertencia"></label>
            </div>
        </div>
        <!-- /NOMBRE -->
        <!-- DNI -->
        <div class="row">
            <div class="bloque bloque-small float-right">
                <select id="tipoDocCbx" class="input "></select>
                <label id="tipoDocLbl" for="tipoDocCbx" class="advertencia"></label>
            </div>
            <div class="bloque bloque-big float-right">
                <input id="nroDocTxt" class="input button-big">
                <label id="nroDocLbl" for="nroDocTxt" class="advertencia"></label>
            </div>
        </div>
        <!-- /DNI -->
        <!-- MAIL -->
        <div class="bloque bloque-full float-right">
            <input id="emailTxt" class="input">
            <label id="emailLbl" class="advertencia" for="emailTxt"></label>
        </div>
        <!-- /MAIL-->
        <!-- PROMOS PEI -->
        <div class="row">
            <div class="bloque bloque-big float-left">
                <select id="promosCbx" class="input "></select>
            </div>
            <div class="bloque bloque-small float-right" id="tokenpei-bloque">
                <input id="tokenPeiTxt" class="input ">
                <label id="tokenPeiLbl" for="tokenPeiTxt" class="advertencia"></label>
            </div>
        </div>
        <!-- /PROMOS PEI-->
        <!-- PROMOS LBL -->
        <div class="static-row" id="promos-row">
            <div class="bloque bloque-medium float-left">
                <label id="promosLbl" for="promosCbx button-medium"></label>
            </div>
        </div>
        <!-- /PROMOS LBL -->
    </div>
    <!-- BUTTONS -->
    <div class="static-row" id="botonera-row">
        <button id="MY_buttonConfirmarPago"
                class="button button-payment-method button-primary float-right"></button>
        <button id="MY_buttonPagarConBilletera"
                class="button button-payment-method button-primary float-right"></button>
    </div>
    <!-- /BUTTONS -->
</div>


<script type="text/javascript">

    var tpformJquery = $.noConflict();
    var urlSuccess = "<?php echo $return_URL_SUCCESS ?>";
    var urlError = "<?php echo $return_URL_ERROR?>";
    var peiCbx = tpformJquery("#peiCbx");
    var peiRow = tpformJquery("#row-pei");
    var tokenPeiRow = tpformJquery("#tokenpei-row");
    var tokenPeiTxt = tpformJquery("#tokenPeiTxt");
    var tokenPeiBloque = tpformJquery("#tokenpei-bloque");
    var numeroDeTarjeta = tpformJquery("#numeroTarjetaTxt");
    var formaDePago = document.getElementById("formaPagoCbx");

    /************* SMALL SCREENS DETECTOR (?) *************/


    function detector() {
        console.log("Width: " + tpformJquery("#tpForm").width());
        if (tpformJquery("#tpForm").width() < 500) {
            console.log("inside");
            tpformJquery(".left-col").width('100%');
            tpformJquery(".right-col").width('100%');
            tpformJquery(".advertencia").css("height", "50px");
            tpformJquery(".row").css({
                "height": "120px",
                "width": "95%",
                "margin-bottom": "30px"
            });
            tpformJquery("#codigo-col").css("margin-bottom", "10px");
            tpformJquery("#row-pei").css("height", "100px");
        }
    }


    /************* CONFIGURACION DEL API *********************/
    function loadScript(url, callback) {
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

    loader();

    function loader() {
        tpformJquery("#loading-hibrid").css("width", "50%");
        setTimeout(function () {
            ignite();
        }, 100);
        setTimeout(function () {
            tpformJquery("#loading-hibrid").css("width", "100%");
        }, 1000);
        setTimeout(function () {
            tpformJquery(".progress").hide('fast');
            detectFormaDePagoDesplegado();
        }, 2000);
        setTimeout(function () {
            tpformJquery("#tpForm").fadeTo('fast', 1);
        }, 2200);
    }

    function ignite() {

        window.TPFORMAPI.hybridForm.initForm({
            callbackValidationErrorFunction: 'validationCollector',
            callbackBilleteraFunction: 'billeteraPaymentResponse',
            callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
            callbackCustomErrorFunction: 'customPaymentErrorResponse',
            botonPagarId: 'MY_buttonConfirmarPago',
            botonPagarConBilleteraId: 'MY_buttonPagarConBilletera',
            modalCssClass: 'modal-class',
            modalContentCssClass: 'modal-content',
            beforeRequest: 'initLoading',
            afterRequest: 'stopLoading'
        });

        /************* SETEO UN ITEM PARA COMPRAR ******************/
        window.TPFORMAPI.hybridForm.setItem({
            publicKey: '<?php echo $responseSAR->PublicRequestKey; ?>',
            defaultNombreApellido: '<?php echo $nombre_completo; ?>',
            defaultNumeroDoc: '',
            defaultMail: '<?php echo $email; ?>',
            defaultTipoDoc: 'DNI'
        });
    }

    /************ FUNCIONES CALLBACKS ************/

    function validationCollector(parametros) {
        console.log("Validando los datos");
        console.log(parametros.field + " -> " + parametros.error);
        var input = parametros.field;
        if (input.search("Txt") !== -1) {
            label = input.replace("Txt", "Lbl");
        } else {
            label = input.replace("Cbx", "Lbl");
        }
        if (document.getElementById(label) !== null)
            document.getElementById(label).innerHTML = parametros.error;
    }

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
            window.location.href = urlError + "&Answer=" + response.AuthorizationKey + "&Error=" + response.ResultMessage;
        } else {
            window.location.href = urlError + "&Error=" + response.ResultMessage;
        }
    }

    formaDePago.addEventListener("click", function () {
        if (formaDePago.value === "1") {
            desplegar();
        } else {
            tpformJquery(".loaded-form").hide('fast');
        }
    });

    function detectFormaDePagoDesplegado(){
        if (formaDePago.value === "1"){
            desplegar();
        }
    }

    function desplegar(){
        detector();
        peiRowHaven();
        tpformJquery(".loaded-form").show('fast');
    }

    function initLoading() {
        console.log('Loading...');
    }

    numeroDeTarjeta.change(function () {
        peiRowHaven()
    });

    function peiRowHaven() {
        if (peiCbx.css('display') !== 'none') {
            peiRow.show('fast');
        }
        if (peiCbx.css('display') === 'none') {
            peiRow.hide('fast');
        }
        if (tokenPeiTxt.css('display') !== 'none') {
            tokenPeiBloque.css('height', "%100");
            tokenPeiRow.css('height', "%100");
        } else {
            tokenPeiBloque.css('height', "%0");
            tokenPeiRow.css('height', "%0");
        }
    }

    function stopLoading() {
        console.log('Stop loading...');
        peiRowHaven();
    }

</script>
