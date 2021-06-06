<?php

use App\Lib\Respuesta;
use App\Model\Comentario;

$app->group('/public-comentario/', function () {

    $this->get('manga/{id}', function ($req, $res, $args) {
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
                $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
                $respuestas = [];
                if (count($possibles_respuestas) > 0) {
                    foreach ($possibles_respuestas as $respuesta) {
                        array_push($respuestas, Comentario::getComentarioPublico($respuesta->id));
                    }
                    $comentario['respuestas'] = $respuestas;
                }
            }
            return $res->withJson(Respuesta::set(true, '', $comentarios));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error->getMessage()));
        }
    });

    $this->get('usuario/{id}', function ($req, $res, $args) {
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
                $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
                $respuestas = [];
                if (count($possibles_respuestas) > 0) {
                    foreach ($possibles_respuestas as $respuesta) {
                        array_push($respuestas, Comentario::getComentarioPublico($respuesta->id));
                    }
                    $comentario['respuestas'] = $respuestas;
                }
            }
            return $res->withJson(Respuesta::set(true, '', $comentarios));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error->getMessage()));
        }
    });
});
