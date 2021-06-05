<?php

use App\Model\ComentarioManga;
use App\Lib\Respuesta;
use App\Model\Comentario;

$app->group('/comentario-manga/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['idComentario']) || !isset($body['idManga'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $idComentario = $body['idComentario'];
            $idManga = $body['idManga'];

            try {
                $comentario = Comentario::find($idComentario);
                if (!$comentario) {
                    return $res->withJson(Respuesta::set(false, 'El comentario que intentas asignar no existe.'));
                }

                $decodetToken = $req->getAttribute('decoded_token_data');
                if ($decodetToken['usuario']->id != $comentario->idUsuario) {
                    return $res->withJson(Respuesta::set(false, 'No puedes asignar comentarios en nombre de otras personas! ಠ_ಠ'));
                }

                $comentarioManga = new ComentarioManga;
                $comentarioManga->idComentario  = $idComentario;
                $comentarioManga->idManga  = $idManga;
                $comentarioManga->save();
                return $res->withJson(Respuesta::set(true, 'Se ha enviado el comentario'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }

    );
});
