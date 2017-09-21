<?php
namespace TodoPago\Client;

class Google 
{
    private $key = null;
    
    private $response;
    
    private $address;
    private $gAddress;

    private $provinciaCode = array(
        "CABA" => "C",
        "Buenos Aires" => "B",
        "Córdoba" => "X",
        "Santa Fe" => "S",
        "Santa Cruz" => "Z",
        "San Juan" => "J",
        "La Rioja" => "F",
        "La Pampa" => "L",
        "Entre Ríos" => "E",
        "Catamarca" => "K",
        "Chaco" => "H",
        "Chubut" => "U",
        "Corrientes" => "W",
        "Formosa" => "P",
        "Jujuy" => "Y",
        "Mendoza" => "M",
        "Misiónes" => "N",
        "Neuquén" => "Q",
        "Río Negro" => "R",
        "Salta" => "A",
        "San Luis" => "D",
        "Santiago del Estero" => "G",
        "Tierra del Fuego" => "V",
        "Tucumán" => "T"
    );

    public function setKey($mode) {
        if($mode == TODOPAGO_ENDPOINT_PROD) {
            include(dirname(__FILE__) . "/key.php");
            $this->key = $key;
        }
    }

    public function obtainInfoAddress($data) {
        $this->address["billing"] = array(
            "CSBTSTREET1" => $data["CSBTSTREET1"],
            "CSBTCITY" => $data["CSBTCITY"],
            "CSBTSTATE" => $data["CSBTSTATE"],
            "CSBTCOUNTRY" => $data["CSBTCOUNTRY"]
        );
        $address = $data["CSBTSTREET1"] ." ". $data["CSBTCITY"] ." ". $data["CSBTSTATE"] ." ". $data["CSBTCOUNTRY"];
        $this->response["billing"] = $this->_doRequest($address);
        $this->gAddress["billing"] = $this->processData($this->response["billing"],true);
        
        $this->address["shipping"] = array(
            "CSBTSTREET1" => $data["CSSTSTREET1"],
            "CSBTCITY" => $data["CSSTCITY"],
            "CSBTSTATE" => $data["CSSTSTATE"],
            "CSBTCOUNTRY" => $data["CSSTCOUNTRY"]
        );            
        $address = $data["CSSTSTREET1"] ." ". $data["CSSTCITY"] ." ". $data["CSSTSTATE"] ." ". $data["CSSTCOUNTRY"];
        $this->response["shipping"] = $this->_doRequest($address);
        $this->gAddress["shipping"] = $this->processData($this->response["shipping"],false);

        return array_merge($this->gAddress["billing"],$this->gAddress["shipping"]);
    }

    public function processData($address, $billing) {
        $tpAddress = array();

        if(isset($address["status"]) && $address["status"] == "OK") {
            $components = $address["results"][0]["address_components"];
            foreach($components as $comp) {
                if(in_array("country", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTCOUNTRY":"CSSTCOUNTRY"] = $comp["short_name"];
                }
                if(in_array("postal_code", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTPOSTALCODE":"CSSTPOSTALCODE"] = $comp["long_name"];
                }
                if(in_array("postal_code_suffix", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTPOSTALCODE":"CSSTPOSTALCODE"] .= $comp["long_name"];
                }                
                if(in_array("locality", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTCITY":"CSSTCITY"] = $comp["long_name"];
                }
                if(in_array("administrative_area_level_1", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTSTATE":"CSSTSTATE"] = $this->_getProvincia($comp["short_name"]);
                    if($this->_getProvincia($comp["short_name"]) == "C") {
                        $tpAddress[($billing)?"CSBTCITY":"CSSTCITY"] = $comp["short_name"];
                    }
                }
                if(in_array("route", $comp["types"])) {
                    $tpAddress[($billing)?"CSBTSTREET1":"CSSTSTREET1"] = $comp["long_name"];
                }
            }
            if(in_array("street_number",$components[0]["types"])) {
                $tpAddress[($billing)?"CSBTSTREET1":"CSSTSTREET1"] .= " " . $components[0]["long_name"];
            }
            return $tpAddress;
        } 
    
        throw new \TodoPago\Exception\ResponseException($address["status"]);
    }

    public function getGoogleResponse() {
        return $this->response;
    }

    public function getOriginalAddress() {
        return $this->address;
    }

    public function getFinalAddress() {
        return $this->gAddress;
    }

    private function _getProvincia($prov) {
        return $this->provinciaCode[$prov];
    }

    private function _doRequest($address){
        $address = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address;

        if($this->key != null) {
            $url = $url . "&key=" . $this->key;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        $result = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if($http_status != 200) {
            throw new \TodoPago\Exception\ConnectionException("Error al consultar API de Google Maps");    
        }

        if( json_decode($result) == null ) {
            throw new \TodoPago\Exception\ResponseException("Error al consultar API de Google Maps");
        }

        return json_decode($result,true);
    }
}