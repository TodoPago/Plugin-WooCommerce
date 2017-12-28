<!-- TAB STATUS-->
<script type="text/javascript">
   	function credentials(mode){
   		if (mode == 'test'){
       		user = document.getElementById("mail_dev").value;
       		password = document.getElementById("pass_dev").value;
   		}else{
   			user = document.getElementById("mail_prod").value;
       		password = document.getElementById("pass_prod").value;
   		}

   		if (user == null || user ==''){  alert('El user esta vacio'); return 0;     }
   		if (password == null || password ==''){  alert('El passwordword esta vacio'); return 0; }

   		var wpnonce = document.getElementById("wpnonce").value;

   		jQuery.ajax({type: 'POST',
            url: 'admin-ajax.php',
            data: {
                    'action' : 'get_credentials',
                    '_wpnonce' : wpnonce,
                    'user' :  user,
                    'password' :  user,
                    'mode' :  mode
                },
            success: function(data) {
            	var response = jQuery.parseJSON(data);

				if(response.codigoResultado === undefined) {
					alert(response.mensajeResultado);
				} else {
					if(mode=='test'){
						document.getElementById("todopago_merchant_id_dev").value = response.merchandid;
						document.getElementById("todopago_authorization_header_dev").value = response.apikey;
						document.getElementById("todopago_security_dev").value = response.security;
					}else{
						document.getElementById("todopago_merchant_id_prod").value = response.merchandid;
						document.getElementById("todopago_authorization_header_prod").value = response.apikey;
						document.getElementById("todopago_security_prod").value = response.security;
					}
				}
			},
            error: function(xhr, ajaxOptions, thrownError) {
                 console.log(xhr);

                switch (xhr.status) {
                        case 404: alert("Verifique la correcta instalación del plugin");
                                break;
                        default: alert("Verifique la conexion a internet y su proxy");
                                break;
                }
			}
        });
   	}
</script>
