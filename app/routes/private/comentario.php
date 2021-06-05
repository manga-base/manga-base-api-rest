<?php

use App\Model\Comentario;
use App\Lib\Respuesta;
use App\Model\PuntuacionComentario;

$app->group('/comentario/', function () {

    $this->get('manga/{id}', function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        try {
            $comentarios = Comentario::select(
                'comentario.id',
                'comentario.idPadre',
                'comentario.idUsuario',
                'comentario.texto',
                'comentario.created_at',
                'comentario.updated_at',
                'usuario.username',
                'usuario.avatar'
            )
                ->where('comentario_manga.idManga', $args['id'])
                ->where('comentario.idPadre', null)
                ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
                ->join('comentario_manga', 'comentario.id', '=', 'comentario_manga.idComentario')
                ->get();
            foreach ($comentarios as $comentario) {
                $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
                $comentario['estadoUsuario'] = PuntuacionComentario::estadoUsuario($comentario->id, $decodetToken['usuario']->id);
                $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
                $respuestas = [];
                if (count($possibles_respuestas) > 0) {
                    foreach ($possibles_respuestas as $respuesta) {
                        array_push($respuestas, Comentario::getComentario($respuesta->id, $decodetToken['usuario']->id));
                    }
                    $comentario['respuestas'] = $respuestas;
                }
            }
            return $res->withJson(Respuesta::set(true, '', $comentarios));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error));
        }
    });

    $this->get('usuario/{id}', function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        try {
            $comentarios = Comentario::select(
                'comentario.id',
                'comentario.idPadre',
                'comentario.idUsuario',
                'comentario.texto',
                'comentario.created_at',
                'comentario.updated_at',
                'usuario.username',
                'usuario.avatar'
            )
                ->where('comentario_usuario.idUsuario', $args['id'])
                ->where('comentario.idPadre', null)
                ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
                ->join('comentario_usuario', 'comentario.id', '=', 'comentario_usuario.idComentario')
                ->get();
            foreach ($comentarios as $comentario) {
                $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
                $comentario['estadoUsuario'] = PuntuacionComentario::estadoUsuario($comentario->id, $decodetToken['usuario']->id);
                $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
                $respuestas = [];
                if (count($possibles_respuestas) > 0) {
                    foreach ($possibles_respuestas as $respuesta) {
                        array_push($respuestas, Comentario::getComentario($respuesta->id, $decodetToken['usuario']->id));
                    }
                    $comentario['respuestas'] = $respuestas;
                }
            }
            return $res->withJson(Respuesta::set(true, '', $comentarios));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error));
        }
    });

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['texto'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            try {
                $decodetToken = $req->getAttribute('decoded_token_data');

                $comentario = new Comentario;
                $comentario->idUsuario = $decodetToken['usuario']->id;
                $comentario->texto = $body['texto'];
                if (isset($body['idPadre'])) {
                    $comentario->idPadre = $body['idPadre'];
                }
                $comentario->save();
                return $res->withJson(Respuesta::set(true, 'Se ha enviado el comentario', Comentario::getComentario($comentario->id, $decodetToken['usuario']->id)));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }

    );

    $this->put(
        '{idComentario}',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body['texto'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $posibleComentario = Comentario::find($args['idComentario']);
            if (!$posibleComentario) {
                return $res->withJson(Respuesta::set(false, 'No se ha encontrado ningun comentario con el identidicador ' . $args['idComentario'] . '.'));
            }
            $comentario = $posibleComentario;
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $comentario["idUsuario"]) {
                return $res->withJson(Respuesta::set(false, 'No puedes editar comentarios de otras personas! ಠ_ಠ'));
            }
            try {
                $comentario->texto = $body['texto'];
                $comentario->save();
                return $res->withJson(Respuesta::set(true, 'Se ha editado el comentario', $comentario));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );

    $this->delete(
        '{idComentario}',
        function ($req, $res, $args) {
            $idComentario = $args['idComentario'];
            $posibleComentario = Comentario::find($idComentario);
            if (!$posibleComentario) {
                return $res->withJson(Respuesta::set(false, 'No se ha encontrado ningun comentario con el identidicador ' . $idComentario . '.'));
            }
            $comentario = $posibleComentario;
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $comentario["idUsuario"]) {
                return $res->withJson(Respuesta::set(false, 'No puedes eliminar comentarios de otras personas! ಠ_ಠ'));
            }
            try {
                $comentario->delete();
                return $res->withJson(Respuesta::set(true, 'Se ha eliminado el comentario'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
