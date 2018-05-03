<!-- TAB STATUS-->
<script type="text/javascript">
    function credentials(mode) {
        if (mode == 'test') {
            mail = document.getElementById("mail_dev").value;
            pass = document.getElementById("pass_dev").value;
        } else {
            mail = document.getElementById("mail_prod").value;
            pass = document.getElementById("pass_prod").value;
        }

        if (mail == null || mail == '') {
            alert('El mail esta vacio');
            return 0;
        }
        if (pass == null || pass == '') {
            alert('El password esta vacio');
            return 0;
        }

        var wpnonce = document.getElementById("wpnonce").value;

        jQuery.ajax({
            type: 'POST',
            url: 'admin-ajax.php',
            data: {
                'action': 'getCredentials',
                '_wpnonce': wpnonce,
                'user': mail,
                'password': pass,
                'mode': mode
            },
            success: function (data) {
                var response = jQuery.parseJSON(data);

                if (response.codigoResultado === undefined) {
                    alert(response.mensajeResultado);
                } else {
                    if (mode == 'test') {
                        document.getElementById("todopago_merchant_id_dev").value = response.merchandid;
                        document.getElementById("todopago_authorization_header_dev").value = response.apikey;
                        document.getElementById("todopago_security_dev").value = response.security;
                    } else {
                        document.getElementById("todopago_merchant_id_prod").value = response.merchandid;
                        document.getElementById("todopago_authorization_header_prod").value = response.apikey;
                        document.getElementById("todopago_security_prod").value = response.security;
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);

                switch (xhr.status) {
                    case 404:
                        alert("Verifique la correcta instalación del plugin");
                        break;
                    default:
                        alert("Verifique la conexion a internet y su proxy");
                        break;
                }
            }
        });
    }
</script>
