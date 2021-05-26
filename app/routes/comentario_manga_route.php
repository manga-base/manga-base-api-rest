<?php

use App\Model\ComentarioManga;
use App\Lib\Respuesta;


$app->group('/comentario-manga/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['idComentario']) || !isset($body['idManga']) || !isset($body['idUsuario'])) {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
            $idComentario = $body['idComentario'];
            $idManga = $body['idManga'];
            $idUsuario = $body['idUsuario'];
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $idUsuario) {
                Respuesta::set(false, 'No puedes enviar comentarios en nombre de otras personas! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            try {
                $comentario = new ComentarioManga;
                $comentario->idComentario  = $idComentario;
                $comentario->idManga  = $idManga;
                $comentario->save();
                Respuesta::set(true, 'Se ha enviado el comentario');
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }

    );
});
