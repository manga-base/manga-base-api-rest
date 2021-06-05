<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;

class Usuario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'usuario';

    public static function getProfile($idUsuario, $idUsuarioPeticion = false)
    {
        try {
            $usuario = Usuario::find($idUsuario);
            if (!$usuario) {
                return Respuesta::set(false, 'No existe ningun usuario con el identificador ' . $idUsuario . ',');
            }
            unset($usuario->password);
            $usuario['favoritos'] = MangaUsuario::getFav($idUsuario);
            $usuario['stats'] = MangaUsuario::getStats($idUsuario);
            if ($idUsuarioPeticion) {
                $usuario['comentarios'] = Comentario::getComentariosDeUsuario($idUsuario, $idUsuarioPeticion);
            } else {
                $usuario['comentarios'] = Comentario::getPublicComentariosDeUsuario($idUsuario);
            }
            $usuario['seguidores'] = Seguidor::select('usuario.id', 'usuario.username', 'usuario.avatar', 'usuario.biografia')
                ->where('idSeguido', $idUsuario)
                ->join('usuario', 'seguidor.idUsuario', '=', 'usuario.id')
                ->get();
            $usuario['siguiendo'] = Seguidor::select('usuario.id', 'usuario.username', 'usuario.avatar', 'usuario.biografia')
                ->where('idUsuario', $idUsuario)
                ->join('usuario', 'seguidor.idSeguido', '=', 'usuario.id')
                ->get();
            return Respuesta::set(true, '', $usuario);
        } catch (Exception $error) {
            return Respuesta::set(false, $error);
        }
    }
}
