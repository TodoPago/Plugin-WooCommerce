<?php

namespace TodoPago\Core\Address;

use TodoPago\Core\AbstractClass\AbstractDAO;

class GoogleAddressDAO extends AbstractDAO
{
    protected $wpdb;
    protected $logger;
    protected $tabla;

    public function __construct($wpdb, $logger)
    {
        $this->setWpdb($wpdb);
        $this->setLogger($logger);
        $this->setTabla($wpdb->prefix . 'todopago_google_address');
    }

    public function createTable()
    {
        $wpdb = $this->getWpdb();
        $table_name = $this->getTabla();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name ( 
            `id` INT NOT NULL AUTO_INCREMENT,
            `md5_hash` VARCHAR(33),
            `street` VARCHAR(100),
            `state` VARCHAR(3),
            `city` VARCHAR(100),
            `country` VARCHAR(3),
            `postal` VARCHAR(50),
            PRIMARY KEY (id)
           ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function recordAddress($hashBilling, $hashShipping, $googleResponse, $originalData)
    {
        $opBilling = $this->googleResponseValidator($googleResponse['billing'], 'B', $originalData);
        $opShipping = $this->googleResponseValidator($googleResponse['shipping'], 'S', $originalData);
        if (!is_null($opBilling))
            $this->insertAddress($hashBilling, $opBilling['CSBTSTREET1'], $opBilling['CSBTSTATE'], $opBilling['CSBTCITY'], $opBilling['CSBTCOUNTRY'], $opBilling['CSBTPOSTALCODE']);
        if ($hashBilling !== $hashShipping && is_null($opShipping))
            $this->insertAddress($hashShipping, $opShipping['CSSTSTREET1'], $opShipping['CSSTSTATE'], $opShipping['CSSTCITY'], $opShipping['CSSTCOUNTRY'], $opShipping['CSSTPOSTALCODE']);
    }

    protected function insertAddress($hash, $street, $state, $city, $country, $postalCode)
    {
        $wpdb = $this->getWpdb();
        $table_name = $this->getTabla();
        try {
            $wpdb->query("
            INSERT INTO `{$table_name}` (md5_hash,street,state,city,country,postal)
            VALUES ('{$hash}','{$street}','{$state}','{$city}','{$country}','{$postalCode}');");
        } catch (\Exception $e) {
            $this->getLogger()->error("Error al guardar en la base de datos: " . $e->getMessage());
        }
    }

    //Comprueba que Google haya devuelo la información correcta
    private function googleResponseValidator($gFinalResponse, $tipoDeCompra, $originalData)
    {
        $comparacion = array_diff_key($this->arrayBenchmarkBuilder($tipoDeCompra), $gFinalResponse);
        if (empty($comparacion))
            return $gFinalResponse;
        else if (array_key_exists('CS' . $tipoDeCompra . 'TPOSTALCODE', $comparacion)) {
            $gFinalResponse['CS' . $tipoDeCompra . 'TPOSTALCODE'] = $originalData->{'CS' . $tipoDeCompra . 'TPOSTALCODE'};
            return $gFinalResponse;
        } else
            return null;
    }

    // Mockea el array con todos los datos deseados
    protected function arrayBenchmarkBuilder($tipoDeCompra)
    {
        $benchmark = array(
            'CS' . $tipoDeCompra . 'TSTREET1' => 1,
            'CS' . $tipoDeCompra . 'TSTATE' => 1,
            'CS' . $tipoDeCompra . 'TCITY' => 1,
            'CS' . $tipoDeCompra . 'TCOUNTRY' => 1,
            'CS' . $tipoDeCompra . 'TPOSTALCODE' => 1
        );
        return $benchmark;
    }

    public function searchHash($hash)
    {
        $wpdb = $this->getWpdb();
        $tabla = $this->getTabla();
        try {
            $md5Encontrado = $wpdb->get_row(
                "SELECT md5_hash
                FROM `$tabla` 
                WHERE md5_hash='{$hash}' LIMIT 1;");
        } catch (\Exception $e) {
            $this->getLogger()->error("Error al leer la Base de Datos $e");
            return null;
        }
        return $md5Encontrado;
    }

    public function selectFullAddressByHash($md5)
    {
        $tabla = $this->getTabla();
        $wpdb = $this->getWpdb();
        try {
            $data = $wpdb->get_row(
                "SELECT street,state,city,country,postal
            FROM `{$tabla}` WHERE md5_hash='{$md5}';");
        } catch (\Exception $e) {
            $this->getLogger()->error("Error al leer la Base de Datos $e");
            return null;
        }
        return $data;

    }

    /**
     * @return mixedara
     */
    public function getTabla()
    {
        return $this->tabla;
    }

    /**
     * @param mixed $tabla
     */
    public function setTabla($tabla)
    {
        $this->tabla = $tabla;
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

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }


}


?>