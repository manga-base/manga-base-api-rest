<?php

use App\Lib\Respuesta;
use App\Model\Seguidor;
use App\Model\Usuario;

$app->group('/seguidor/', function () {

    $this->post(
        '{idUsuario}',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $decodetToken = $req->getAttribute('decoded_token_data');
            $decodetToken['usuario']->id;

            try {
                $seguidor = new Seguidor();
                $seguidor->idUsuario = $decodetToken['usuario']->id;
                $seguidor->idSeguido = $args['idUsuario'];
                $usuario = Usuario::find($args['idUsuario']);
                return $res->withJson(Respuesta::set(true, 'Ahora sigues a ' . $usuario->username . '.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }

            return $res->withJson();
        }
    );
});
