<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<!-- TAB STATUS-->
<div id="tab-status">

  <h3 class="wc-settings-sub-title ">Status de las Operaciones</h3>
  <table class="form" border="1">

  <?php 
    global $wpdb;
    $orders_array = $wpdb -> get_results("
      SELECT posts.ID AS order_id, posts.post_date AS date_added, meta1.meta_value AS total, meta2.meta_value AS firstname, meta3.meta_value AS lastname
      FROM wp_posts posts, wp_postmeta meta1, wp_postmeta meta2, wp_postmeta meta3, wp_postmeta meta4
      WHERE posts.ID = meta1.post_id
      AND meta1.meta_key = '_order_total'
      AND posts.ID = meta2.post_id
      AND meta2.meta_key = '_billing_first_name'
      AND posts.ID = meta3.post_id
      AND meta3.meta_key = '_billing_last_name'
      AND posts.ID = meta4.post_id
      AND meta4.meta_key = '_payment_method'
      AND meta4.meta_value = 'todopago'
      GROUP BY posts.ID");
    $orders_array = json_encode($orders_array);

    $row = $wpdb -> get_row(
      "SELECT option_value FROM wp_options WHERE option_name = 'woocommerce_todopago_settings'"
    );
    $arrayOptions = unserialize($row -> option_value);
    $esProductivo = $arrayOptions['ambiente'] == "prod";
    $merchant = $esProductivo ? $arrayOptions['merchant_id_prod'] : $arrayOptions['merchant_id_test'];
    $urlGetStatus = plugins_url('getStatus.php', __FILE__);

  ?>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      var valore = '<?php echo $orders_array ?>';
      var tabla_db = '';
      valore_json = JSON.parse(valore);
      valore_json.forEach(function (value, key){
        tabla_db += "<tr>";
        tabla_db +="<th><a onclick='verstatus("+value.order_id+")' style='text-decoration: underline;'>"+value.order_id+"</a></th>";
        tabla_db +="<th>"+value.date_added+"</th>";
        tabla_db +="<th>"+value.firstname+"</th>";
        tabla_db +="<th>"+value.lastname+"</th>";
        tabla_db +="<th>$"+value.total+"</th>";
        tabla_db +="</tr>";
      });
      jQuery("#tabla_db").prepend(tabla_db);
    });

    function verstatus (order){
      var merchant = <?php echo $merchant ?>;
      var url_get_status = '<?php echo $urlGetStatus ?>';
      jQuery.get(url_get_status, {order_id:order,merchant:merchant}).done(llegadaDatos);
      return false;                                           
    }

    function llegadaDatos(datos){
        alert(datos);
    }

  </script>

  <table id="tabla" class="display" cellspacing="0" width="100%">

    <thead>
      <tr>
        <th>Nro</th>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Total</th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <th>Nro</th>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Total</th>
      </tr>
    </tfoot>

    <tbody id="tabla_db">   
    </tbody>
  </table>
</div>


<!-- END TAB STATUS-->
