<?php

use App\Model\PuntuacionComentario;
use App\Lib\Respuesta;


$app->group('/puntuacion-comentario/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['idComentario']) || !isset($body['idUsuario']) || !isset($body['tipo'])) {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $body["idUsuario"]) {
                Respuesta::set(false, 'No puedes puntuar un comentario en nombre de otros! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            return $res->withJson(PuntuacionComentario::actualizarPuntuacion($body));
        }
    );

    $this->delete(
        '{idComentario}/{idUsuario}',
        function ($req, $res, $args) {
            if (!isset($args['idComentario']) || !isset($args['idUsuario'])) {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $args["idUsuario"]) {
                Respuesta::set(false, 'No puedes eliminar la puntuación de un comentario en nombre de otros! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            return $res->withJson(PuntuacionComentario::eliminarPuntuacion($args['idComentario'], $args['idUsuario']));
        }
    );
});
