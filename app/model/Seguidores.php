<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;


class Seguidores extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'seguidores';

    public static function seguir($idUsuario, $idSeguidor )
    {
        try {
            $siguiendo = Seguidores::where('idUsuario', $idUsuario)->where('idSeguidor ', $idSeguidor)->get();
            if (count($siguiendo) > 0) {
                Respuesta::set(false, 'Ya has enviado una solicitud a este usuario.');
                return Respuesta::toString();
            }
            $seguidor = new Seguidores;
            $seguidor->idUsuario = $idUsuario;
            $seguidor->idSeguidor = $idSeguidor;
            $seguidor->save();
            Respuesta::set(true, 'Solicitud de amistad enviada correctamente.');
            return Respuesta::toString();
        } catch (Exception $error) {
            Respuesta::set(false, $error);
            return Respuesta::toString();
        }
    }
}
