/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function ($) {
    
        $("#woocommerce_todopago_btnCredentials_dev").val("Obtener Credenciales");
        $("#woocommerce_todopago_btnCredentials_prod").val("Obtener Credenciales");

        $("#woocommerce_todopago_password_dev").attr("type","password");
        $("#woocommerce_todopago_password_prod").attr("type","password");
        var globalError = false;
        
	$("#woocommerce_todopago_btnCredentials_dev").click(function() {
            
            var user = $("#woocommerce_todopago_user_dev").val();
            var password = $("#woocommerce_todopago_password_dev").val();
            var wpnonce = $("#woocommerce_todopago_wpnonce").attr('placeholder');

            getCredentials(user, password, 'test', wpnonce);
                               
         }); 

        $("#woocommerce_todopago_btnCredentials_prod").click(function() {
            
            var user = $("#woocommerce_todopago_user_prod").val();
            var password = $("#woocommerce_todopago_password_prod").val();
            var wpnonce = $("#woocommerce_todopago_wpnonce").attr('placeholder');

            getCredentials(user, password, 'prod', wpnonce);
                               
         }); 

        function getCredentials (user, password, mode, nonce){
            $.ajax({type: 'POST',
                     url: 'admin-ajax.php',
                     data: { 
                             'action' : 'getCredentials',
                             '_wpnonce' : nonce,
                             'user' :  user,
                             'password' :  password,
                             'mode' :  mode
                           },
                     success: function(data) {  
                         setCredentials(data, mode);  
                     },
                     error: function(xhr, ajaxOptions, thrownError) {  
                         console.log(xhr);
                         
                         switch (xhr.status) {
                                 case 404: alert("Verifique la correcta instalación del plugin");
                                           break;
                                 default: alert("Verifique la conexion a internet y su proxy");
                                          break;               
                         }
                     },
            });     
        }
        
        
        function setCredentials (data, ambiente){
            
           var response = $.parseJSON(data);
           
           if(globalError === false && response.codigoResultado === undefined){ 
               globalError = true;
               alert(response.mensajeResultado);     
           }else{
               globalError = false;
                if(ambiente === 'prod'){         
                    $("#woocommerce_todopago_http_header_prod").val(response.apikey);
                    $("#woocommerce_todopago_security_prod").val(response.security);
                    $("#woocommerce_todopago_merchant_id_prod").val(response.merchandid);
                } else{ 
                    $("#woocommerce_todopago_http_header_test").val(response.apikey);
                    $("#woocommerce_todopago_security_test").val(response.security);
                    $("#woocommerce_todopago_merchant_id_test").val(response.merchandid);
                }
                
           }
        }
});
