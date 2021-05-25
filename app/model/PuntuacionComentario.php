<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;

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

    public static function actualizarPuntuacion($body)
    {
        try {
            $idComentario = $body['idComentario'];
            $idUsuario =  $body['idUsuario'];
            $tipo = $body['tipo'];

            if (!in_array($tipo, ['positivo', 'negativo'])) {
                Respuesta::set(false, 'Tipo de puntuación no valida.');
                return Respuesta::toString();
            }

            $posiblePuntuacion = PuntuacionComentario::where('idComentario', $idComentario)->where('idUsuario', $idUsuario)->first();

            if ($posiblePuntuacion) {
                $posiblePuntuacion->tipo = $tipo;
                $posiblePuntuacion->save();
                Respuesta::setDatos($posiblePuntuacion);
            } else {
                $puntuacion = new PuntuacionComentario;
                $puntuacion->tipo = $tipo;
                $puntuacion->idComentario = $idComentario;
                $puntuacion->idUsuario = $idUsuario;
                $puntuacion->save();
                Respuesta::setDatos($puntuacion);
            }
            Respuesta::set(true, 'Puntuación actualizada correctamente.');
            return Respuesta::toString();
        } catch (Exception $e) {
            Respuesta::set(false, $e);
            return Respuesta::toString();
        }
    }

    public static function eliminarPuntuacion($idComentario, $idUsuario)
    {
        try {
            $posiblePuntuacion = PuntuacionComentario::where('idComentario', $idComentario)->where('idUsuario', $idUsuario)->first();

            if ($posiblePuntuacion) {
                $posiblePuntuacion->delete();
                Respuesta::set(true, 'Puntuación eliminada correctamente.', null);
            } else {
                Respuesta::set(false, 'Puntuación no encontrada.');
            }
            return Respuesta::toString();
        } catch (Exception $e) {
            Respuesta::set(false, $e);
            return Respuesta::toString();
        }
    }
}
