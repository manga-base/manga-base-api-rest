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
            if ($args['idUsuario'] === $decodetToken['usuario']->id) {
                return $res->withJson(Respuesta::set(false, 'No puedes seguirte a ti mismo. (｡◕‿◕｡)'));
            }
            try {
                $usuario = Usuario::find($args['idUsuario']);

                $posible_seguidor = Seguidor::where('idUsuario', $decodetToken['usuario']->id)->where('idSeguido', $args['idUsuario'])->get();
                if (count($posible_seguidor) > 0) {
                    return $res->withJson(Respuesta::set(false, 'Ya sigues a ' . $usuario->username . '.'));
                }

                $seguidor = new Seguidor();
                $seguidor->idUsuario = $decodetToken['usuario']->id;
                $seguidor->idSeguido = $args['idUsuario'];
                $seguidor->save();
                return $res->withJson(Respuesta::set(true, 'Ahora sigues a ' . $usuario->username . '.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }

            return $res->withJson();
        }
    );
});
