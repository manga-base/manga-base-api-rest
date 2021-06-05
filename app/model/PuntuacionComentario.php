<?php

namespace App\Model;

class PuntuacionComentario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'puntuacion_comentario';

    public static function puntosPositivos($idComentario)
    {
        return PuntuacionComentario::where('idComentario', $idComentario)->where('tipo', 'positivo')->orderBy('idComentario')->count();
    }

    public static function estadoUsuario($idComentario, $idUsuario)
    {
        return PuntuacionComentario::select('tipo')->where('idComentario', $idComentario)->where('idUsuario', $idUsuario)->first();
    }
}
