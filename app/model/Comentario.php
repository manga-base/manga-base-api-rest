<?php

namespace App\Model;

class Comentario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'comentario';

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

    public static function getComentariosUsuario($idUsuario, $idUsuarioPeticion)
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
            ->where('comentario.idPadre', null)
            ->join('usuario', 'comentario.idUsuario', '=', 'usuario.id')
            ->get();
        foreach ($comentarios as $comentario) {
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
        }
        return $comentarios;
    }
}
