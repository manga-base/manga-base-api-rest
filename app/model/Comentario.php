<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;

class Comentario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'comentario';

    public static function getComentarioPublico($idComentario)
    {
        $comentario = Comentario::select(
            'comentario.id',
            'comentario.idPadre',
            'comentario.idUsuario',
            'comentario.texto',
            'comentario.created_at',
            'comentario.updated_at',
            'usuario.username',
            'usuario.avatar',
        )
            ->where('comentario.id', $idComentario)
            ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
            ->get()
            ->first();
        $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
        $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
        $respuestas = [];
        if (count($possibles_respuestas) > 0) {
            foreach ($possibles_respuestas as $respuesta) {
                array_push($respuestas, Comentario::getComentarioPublico($respuesta->id));
            }
            $comentario['respuestas'] = $respuestas;
        }
        return $comentario;
    }

    public static function getComentario($idComentario, $idUsuarioPeticion)
    {
        $comentario = Comentario::select(
            'comentario.id',
            'comentario.idPadre',
            'comentario.idUsuario',
            'comentario.texto',
            'comentario.created_at',
            'comentario.updated_at',
            'usuario.username',
            'usuario.avatar',
        )
            ->where('comentario.id', $idComentario)
            ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
            ->get()
            ->first();
        $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
        $comentario['estadoUsuario'] = PuntuacionComentario::estadoUsuario($comentario->id, $idUsuarioPeticion);
        $possibles_respuestas = Comentario::where('idPadre', $comentario->id)->get('id');
        $respuestas = [];
        if (count($possibles_respuestas) > 0) {
            foreach ($possibles_respuestas as $respuesta) {
                array_push($respuestas, Comentario::getComentario($respuesta->id, $idUsuarioPeticion));
            }
            $comentario['respuestas'] = $respuestas;
        }
        return $comentario;
    }

    public static function getOrigenComentario($comentario)
    {
        $origen = null;
        $posible_comentario_en_manga = ComentarioManga::select('comentario_manga.idManga', 'manga.tituloPreferido', 'manga.foto')
            ->where('idComentario', $comentario->id)
            ->join('manga', 'comentario_manga.idManga', '=', 'manga.id')
            ->get();
        if (count($posible_comentario_en_manga) > 0) {
            $origen = $posible_comentario_en_manga[0];
            $origen->from = "manga";
        }
        $posible_comentario_en_usuario = ComentarioUsuario::select('comentario_usuario.idUsuario', 'usuario.username', 'usuario.avatar')
            ->where('idComentario', $comentario->id)
            ->join('usuario', 'comentario_usuario.idUsuario', '=', 'usuario.id')
            ->get();
        if (count($posible_comentario_en_usuario) > 0) {
            $origen = $posible_comentario_en_usuario[0];
            $origen->from = "usuario";
        }
        if (!$origen) {
            if ($comentario->idPadre) {
                $comentarioPadre = Comentario::find($comentario->idPadre);
                $origen = self::getOrigenComentario($comentarioPadre);
            }
        }
        return $origen;
    }

    public static function getPublicComentariosDeUsuario($idUsuario)
    {
        $comentarios = Comentario::select(
            'comentario.id',
            'comentario.idPadre',
            'comentario.idUsuario',
            'comentario.texto',
            'comentario.created_at',
            'comentario.updated_at',
            'usuario.username',
            'usuario.avatar',
        )
            ->where('comentario.idUsuario', $idUsuario)
            ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
            ->get();
        foreach ($comentarios as $comentario) {
            $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
            $comentario['origen'] = self::getOrigenComentario($comentario);
        }
        return $comentarios;
    }

    public static function getComentariosDeUsuario($idUsuario, $idUsuarioPeticion)
    {
        $comentarios = Comentario::select(
            'comentario.id',
            'comentario.idPadre',
            'comentario.idUsuario',
            'comentario.texto',
            'comentario.created_at',
            'comentario.updated_at',
            'usuario.username',
            'usuario.avatar',
        )
            ->where('comentario.idUsuario', $idUsuario)
            ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
            ->get();
        foreach ($comentarios as $comentario) {
            $comentario['puntosPositivos'] = PuntuacionComentario::puntosPositivos($comentario->id);
            $comentario['estadoUsuario'] = PuntuacionComentario::estadoUsuario($comentario->id, $idUsuarioPeticion);
            $comentario['origen'] = self::getOrigenComentario($comentario);
        }
        return $comentarios;
    }
}
