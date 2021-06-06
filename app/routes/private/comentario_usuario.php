<?php

use App\Model\ComentarioUsuario;
use App\Model\Comentario;
use App\Lib\Respuesta;

$app->group('/comentario-usuario/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['idComentario']) || !isset($body['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $idComentario = $body['idComentario'];
            $idUsuario = $body['idUsuario'];

            try {
                $comentario = Comentario::find($idComentario);
                if (!$comentario) {
                    return $res->withJson(Respuesta::set(false, 'El comentario que intentas asignar no existe.'));
                }

                $decodetToken = $req->getAttribute('decoded_token_data');
                if ($decodetToken['usuario']->id != $comentario->idUsuario) {
                    return $res->withJson(Respuesta::set(false, 'No puedes asignar comentarios en nombre de otras personas! ಠ_ಠ'));
                }

                $comentarioUsuario = new ComentarioUsuario;
                $comentarioUsuario->idComentario  = $idComentario;
                $comentarioUsuario->idUsuario = $idUsuario;
                $comentarioUsuario->save();
                return $res->withJson(Respuesta::set(true, 'Se ha enviado el comentario'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }

    );
});
