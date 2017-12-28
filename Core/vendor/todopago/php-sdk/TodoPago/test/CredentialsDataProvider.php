<?php

namespace TodoPago\Test;

class CredentialsDataProvider {

    public static function getCredentialsOptions() {

        $user = new \TodoPago\Data\User("midireccion@mail.com", "mypassword");
        return $user;   
    }

    public static function getCredentialsOkResponse() {
        return '{
  "Credentials": {
    "codigoResultado": 1,
    "resultado": {
      "codigoResultado": 0,
      "mensajeKey": null,
      "mensajeResultado": "Aceptado"
    },
    "merchantId": 5963,
    "APIKey": "TODOPAGO 1f5a522cb9a349c68f8e9e7ac8d0db11"
  }
}';

    }

    public static function getCredentialsWrongUserResponse() {
        return '{
  "Credentials": {
    "codigoResultado": 1,
    "resultado": {
      "codigoResultado": 1050,
      "mensajeKey": 1050,
      "mensajeResultado": "Este usuario no se encuentra registrado. Revisá la información indicada o registrate."
    },
    "merchantId": null,
    "APIKey": null
  }
}';
    }

    public static function getCredentialsWrongPasswordResponse() {
        return '{
  "Credentials": {
    "codigoResultado": 1,
    "resultado": {
      "codigoResultado": 1055,
      "mensajeKey": 1055,
      "mensajeResultado": "La contraseña ingresada es incorrecta. Revisala."
    },
    "merchantId": null,
    "APIKey": null
  }
}';
    }
}
