/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function ($) {
    $('#woocommerce_todopago_timeout_limite').attr({
        "min": 180000,
        "max": 21600000
    });

    var todoPagoEnabledCuotas = $("#woocommerce_todopago_enabledCuotas");
    disabler(todoPagoEnabledCuotas.val());

    todoPagoEnabledCuotas.change(function () {
        disabler(todoPagoEnabledCuotas.val());
    });

    function disabler(val) {
        if (val === "1") {
            $("#woocommerce_todopago_max_cuotas").prop('disabled', false);
        } else {
            $("#woocommerce_todopago_max_cuotas").prop('disabled', true);
        }
    }
});
