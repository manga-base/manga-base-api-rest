<?php

namespace App\Model;

class ComentarioUsuario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'comentario_usuario';

    public static function getComentariosUsuario($idUsuario)
    {
        return ComentarioUsuario::where('idUsuario');
    }
}
