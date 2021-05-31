<?php

use App\Model\Comentario;
use App\Lib\Respuesta;


$app->group('/comentario/', function () {

    $this->get('manga/{id}', function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        return $res->withJson(Comentario::getComentariosManga($args['id'], $decodetToken['usuario']->id));
    });

    $this->get('usuario/{id}', function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        return $res->withJson(Comentario::getComentariosUsuario($args['id'], $decodetToken['usuario']->id));
    });

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['texto']) || !isset($body['idUsuario'])) {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
            $texto = $body['texto'];
            $idUsuario = $body['idUsuario'];
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $idUsuario) {
                Respuesta::set(false, 'No puedes enviar comentarios en nombre de otras personas! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            try {
                $comentario = new Comentario;
                $comentario->idUsuario = $idUsuario;
                $comentario->texto = $texto;
                if (isset($body['idPadre'])) {
                    $comentario->idPadre = $body['idPadre'];
                }
                $comentario->save();
                Respuesta::set(true, 'Se ha enviado el comentario', Comentario::getComentario($comentario->id, $decodetToken['usuario']->id));
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }

    );

    $this->put(
        '{idComentario}',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['texto'])) {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
            $posibleComentario = Comentario::find($args['idComentario']);
            if (!$posibleComentario) {
                Respuesta::set(false, 'No se ha encontrado ningun comentario con el identidicador ' . $args['idComentario'] . '.');
                return $res->withJson(Respuesta::toString());
            }
            $comentario = $posibleComentario;
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $comentario["idUsuario"]) {
                Respuesta::set(false, 'No puedes editar comentarios de otras personas! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            try {
                $comentario->texto = $body['texto'];
                $comentario->save();
                Respuesta::set(true, 'Se ha editado el comentario', $comentario);
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }
    );

    $this->delete(
        '{idComentario}',
        function ($req, $res, $args) {
            $idComentario = $args['idComentario'];
            $posibleComentario = Comentario::find($idComentario);
            if (!$posibleComentario) {
                Respuesta::set(false, 'No se ha encontrado ningun comentario con el identidicador ' . $idComentario . '.');
                return $res->withJson(Respuesta::toString());
            }
            $comentario = $posibleComentario;
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $comentario["idUsuario"]) {
                Respuesta::set(false, 'No puedes eliminar comentarios de otras personas! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            try {
                $comentario->delete();
                Respuesta::set(true, 'Se ha eliminado el comentario');
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }
    );
});
