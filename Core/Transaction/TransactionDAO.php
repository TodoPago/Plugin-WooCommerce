<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 12/09/17
 * Time: 12:44
 */

namespace TodoPago\Core\Transaction;

use TodoPago\Core\AbstractClass\AbstractDAO as AbstractDAO;
use TodoPago\Core\Exception\ErrorValue as ErrorValue;
use TodoPago\Core\Exception\ExceptionBase;
use TodoPago\Core\Transaction\TransactionModel as TransactionModel;
use TodoPago\Utils\Constantes;

class TransactionDAO extends AbstractDAO
{
    protected $nombreTabla;
    protected $wpdb;

    public function __construct($wpdb)
    {
        $this->setWpdb($wpdb);
    }

    /* Acciones */

    /* SAVE */

    /* CREATE TABLE */

    public function createTable()
    {
        $wpdb = $this->getWpdb();
        $table_name = $wpdb->prefix . "todopago_transaccion2";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id INT NOT NULL AUTO_INCREMENT,
          id_orden INT NOT NULL,
          tipo TEXT NOT NULL,
          step DATETIME NOT NULL,
          params TEXT NOT NULL,
          response TEXT NOT NULL,
          returned_key TEXT NULL,
          public_request_key TEXT NULL,
          PRIMARY KEY (id)
          ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    /* SAVE REQUEST KEY */

    public function saveRequestKey(TransactionModel $transactionModel)
    {
        $orderId = $transactionModel->getIdOrden();
        $responseSAR = $transactionModel->getResponseSAR();
        update_post_meta($orderId, 'response_SAR', serialize($responseSAR));
    }


    /* SAVE SAR */

    /* public function saveSAR(TransactionModel $transactionModel)
     {
         global $wpdb;
         $responseSAR = $transactionModel->getResponse();
         $values = array(
             'id_orden' => $transactionModel->getIdOrden(),
             'tipo' => 'SAR',
             'step' => date("Y-m-d H:i:s"),
             'params' => json_encode($transactionModel->getParams()),
             'response' => json_encode($transactionModel->getResponse()),
             'returned_key' => $responseSAR->RequestKey,
             'public_request_key' => $responseSAR->PublicRequestKey
         );
         foreach ($values as $key => $value) {
             if (empty($value) && ($key != "returned_key" && $key != "public_request_key"))
                 throw new ErrorValue($key, 'Transaction Table', 'TRANSACTION DAO', 'Que no est� vacio.');
         }
         $wpdb->insert(
             $wpdb->prefix . 'todopago_transaccion2',
             $values,
             array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
         );
     }*/

    /*
     * $wpdb->insert(
            $wpdb->prefix . 'todopago_transaccion',
            array('id_orden' => $order->getOrderId(),
                'params_SAR' => json_encode($paramsSAR),
                'first_step' => date("Y-m-d H:i:s"),
                'response_SAR' => json_encode($response_sar),
                'request_key' => $response_sar["RequestKey"],
                'public_request_key' => $response_sar['PublicRequestKey']
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
     */


    /* SAVE GAA */

    /*public function saveGAA(TransactionModel $transactionModel)
    {
        global $wpdb;
        $dataGAA = $transactionModel->getResponseGAA();
        $wpdb->insert(
            $wpdb->prefix . 'todopago_transaccion',
            array(
                'id_orden' => $transactionModel->getIdOrden(), // int
                'tipo' => 'GAA', // string
                'step' => date("Y-m-d H:i:s"), // string
                'params' => json_encode($dataGAA->params_GAA), // string
                'response' => json_encode($dataGAA->response_GAA), // string
                'returned_key' => $dataGAA->response_GAA->AuthorizationKey //string
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );
    }*/

    public function save($tipo, TransactionModel $transactionModel)
    {
        global $wpdb;
        $response = $transactionModel->getResponse();
        $values = array(
            'id_orden' => $transactionModel->getIdOrden(),
            'tipo' => $tipo,
            'step' => date("Y-m-d H:i:s"),
            'params' => json_encode($transactionModel->getParams()),
            'response' => json_encode($transactionModel->getResponse())
        );

        try {
            if ($tipo == Constantes::TODOPAGO_SAR) {
                if ($response->StatusCode == '-1') {
                    $values["returned_key"] = $response->RequestKey;
                    $values["public_request_key"] = $response->PublicRequestKey;
                }
                else {
                    throw new ExceptionBase($response->StatusMessage, 'Autorización');
                }
            } else { //Constantes::TODOPAGO_GAA
                $values["returned_key"] = $response->AuthorizationKey;
            }
        } catch (Exception $e) {
            echo "Error al guardar en la BBDD" . $e->getMessage();
        }


        foreach ($values as $key => $value) {
            if (empty($value) && ($key != "returned_key" && $key != "public_request_key"))
                throw new ErrorValue($key, 'Transaction Table', 'TRANSACTION DAO', 'Que no esté vacio.');
        }
        $wpdb->insert(
            $wpdb->prefix . 'todopago_transaccion2',
            $values,
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * @return mixed
     */
    public function getNombreTabla()
    {
        return $this->nombreTabla;
    }

    /**
     * @param mixed $nombreTabla
     */
    public function setNombreTabla($nombreTabla)
    {
        $this->nombreTabla = $nombreTabla;
    }

    /**
     * @return mixed
     */
    public function getWpdb()
    {
        return $this->wpdb;
    }

    /**
     * @param mixed $wpdb
     */
    public function setWpdb($wpdb)
    {
        $this->wpdb = $wpdb;
    }


}
