/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function ($) {
    
        $("#woocommerce_todopago_btnCredentials").val("Obtener Credenciales");
        var globalError = false;
        
	$("#woocommerce_todopago_btnCredentials").click(function() {
            
            var user = $("#woocommerce_todopago_user").val();
            var password = $("#woocommerce_todopago_password").val();
            
            getCredentials(user, password, 'test');
            getCredentials(user, password, 'prod');  
                               
         }); 
        
        function getCredentials (user, password, mode){
            
          $.ajax({type: 'POST',
                     url: BASE_URL_CREDENTIAL,
                     data: { 'user' :  user,
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
        
        if( $("#woocommerce_todopago_enabledCuotas").prop('checked') ) {
                $("#woocommerce_todopago_max_cuotas").prop('disabled', false);           
            }else{
                $("#woocommerce_todopago_max_cuotas").prop('disabled', true);        
        } 
        
        $("#woocommerce_todopago_enabledCuotas").click(function() {
        
            if( $("#woocommerce_todopago_enabledCuotas").prop('checked') ) {
                $("#woocommerce_todopago_max_cuotas").prop('disabled', false);           
            }else{
                $("#woocommerce_todopago_max_cuotas").prop('disabled', true);        
            }
        });       
});
