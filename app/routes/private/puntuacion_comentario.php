<?php

use App\Model\PuntuacionComentario;
use App\Lib\Respuesta;


$app->group('/puntuacion-comentario/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['idComentario']) || !isset($body['tipo'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $idComentario = $body['idComentario'];
            $tipo = $body['tipo'];

            if (!in_array($tipo, ['positivo', 'negativo'])) {
                return $res->withJson(Respuesta::set(false, 'Tipo de puntuación no valida.'));
            }

            $decodetToken = $req->getAttribute('decoded_token_data');
            $idUsuario = $decodetToken['usuario']->id;
            try {
                $posiblePuntuacion = PuntuacionComentario::where('idComentario', $idComentario)->where('idUsuario', $idUsuario)->first();

                if ($posiblePuntuacion) {
                    $posiblePuntuacion->tipo = $tipo;
                    $posiblePuntuacion->save();
                    Respuesta::setDatos($posiblePuntuacion);
                } else {
                    $puntuacion = new PuntuacionComentario;
                    $puntuacion->tipo = $tipo;
                    $puntuacion->idComentario = $idComentario;
                    $puntuacion->idUsuario = $idUsuario;
                    $puntuacion->save();
                    Respuesta::setDatos($puntuacion);
                }
                return $res->withJson(Respuesta::set(true, 'Puntuación actualizada correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->delete(
        '{idComentario}',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            $idUsuario = $decodetToken['usuario']->id;
            try {
                $posiblePuntuacion = PuntuacionComentario::where('idComentario', $args['idComentario'])->where('idUsuario', $idUsuario)->first();

                if (!$posiblePuntuacion) {
                    return $res->withJson(Respuesta::set(false, 'Puntuación no encontrada.'));
                }

                $posiblePuntuacion->delete();
                return $res->withJson(Respuesta::set(true, 'Puntuación eliminada correctamente.', null));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
