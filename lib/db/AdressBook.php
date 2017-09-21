<?php

    class AdressBook
    {
        private $tableName="todopago_addressbook";
        
        public function createTable()
        {
            global $wpdb;
            
            $wpdb->query("CREATE TABLE IF NOT  EXISTS `" . $wpdb->prefix . "{$this->tableName}` (`id` INT NOT NULL AUTO_INCREMENT, `md5_hash` VARCHAR(33), `street` VARCHAR(100), `state` VARCHAR(3), `city` VARCHAR(100), `country` VARCHAR(3), `postal` VARCHAR(50), PRIMARY KEY (id));");
        }

        public function dropTable()
        {
            global $wpdb;
            
            $wpdb->query("DROP TABLE IF EXISTS `" . $wpdb->prefix . "{$this->tableName}`;");
        }

        public function recordAddress($md5, $street, $state, $city, $country, $postalCode)
        {
            global $wpdb;
            
            if (empty($this->findMd5($md5))) {
                try {
                    $wpdb->query("INSERT INTO `" . $wpdb->prefix . "todopago_addressbook` (md5_hash,street,state,city,country,postal) VALUES ('{$md5}','{$street}','{$state}','{$city}','{$country}','{$postalCode}');");
                    
                    //$wpdb->insert($wpdb->prefix . "{$this->tableName}",array("id" => 1, "md5_hash" => "066f0dffdbcb7bb26607c8d8ecbdcc47", "street" => "Chile 970","state"=>"B","city"=>"Villa Sarmiento","country"=>"AR","postal"=>"B1707BUS"),array('%d', '%s','%s','%s','%s','%s','%s'));
                } catch (Exception $e) {
                    $this->logger->warn("Error al cargar datos a la agenda de direcciones validadas. " . $e);
                }
            }
        }
        
        public function findMd5($md5)
        {
            global $wpdb;
            
            $md5Encontrado = $md5;
            if (isset($md5)) {
                try {
                    $md5Encontrado = $wpdb->get_row("SELECT md5_hash FROM `" . $wpdb->prefix . "{$this->tableName}` WHERE md5_hash='{$md5}' LIMIT 1;");
                } catch (Exception $e) {
                    $this->logger->warn("Error al buscar el hash: " . $md5 . "en la agenda de direcciones. " . $e);
                    $md5Encontrado = null;
                }
            }
            return $md5Encontrado;
        }

        public function getData($md5)
        {
            global $wpdb;

            try {
                $data = $wpdb->get_row("SELECT street,state,city,country,postal FROM `" . $wpdb->prefix . "{$this->tableName}` WHERE md5_hash='{$md5}';");
            } catch (Exception $e) {
                $this->logger->warn("Error al realizar la query. Error devuelto: " . $e);
                $data = null;
            }
            return $data;
        }
    }