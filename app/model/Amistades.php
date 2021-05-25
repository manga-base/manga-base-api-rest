<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;

class Amistades extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'amistades';

    public static function getColumns()
    {
        return ['idUsuario', 'idAmigo', 'estado', 'created_at'];
    }

    public static function getAmigos($idUsuario)
    {
        try {
            //$amigos = Amistades::where('idUsuario', $idUsuario)->orWhere('idAmigo', $idUsuario)->where("estado", "aceptada")->get();
            $amigos1 = Amistades::select('usuario.id', 'usuario.username', 'usuario.avatar')
                ->join('usuario', 'amistades.idUsuario', '=', 'usuario.id')
                ->where('estado', "aceptada")
                ->where('idAmigo', $idUsuario)
                ->get();
            $amigos2 = Amistades::select('usuario.id', 'usuario.username', 'usuario.avatar')
                ->join('usuario', 'amistades.idAmigo', '=', 'usuario.id')
                ->where('estado', "aceptada")
                ->where('idUsuario', $idUsuario)
                ->get();
            $amigos = array();
            foreach ($amigos1 as $amigo) {
                array_push($amigos, $amigo);
            }
            foreach ($amigos2 as $amigo) {
                array_push($amigos, $amigo);
            }
            Respuesta::set(true, '', $amigos);
            return Respuesta::toString();
        } catch (Exception $error) {
            Respuesta::set(false, $error);
            return Respuesta::toString();
        }
    }

    public static function solicitudDeAmistad($idUsuario, $idAmigo)
    {
        try {
            $possible_solicitud = Amistades::where('idUsuario', $idUsuario)->where('idAmigo', $idAmigo)->get();
            if (count($possible_solicitud) > 0) {
                Respuesta::set(false, 'Ya has enviado una solicitud a este usuario.');
                return Respuesta::toString();
            }
            $solicitud = new Amistades;
            $solicitud->idUsuario = $idUsuario;
            $solicitud->idAmigo = $idAmigo;
            $solicitud->save();
            Respuesta::set(true, 'Solicitud de amistad enviada correctamente.');
            return Respuesta::toString();
        } catch (Exception $error) {
            Respuesta::set(false, $error);
            return Respuesta::toString();
        }
    }

    public static function aceptarSolicitud($idSolicitante, $idUsuario)
    {
        try {
            $possible_solicitud = Amistades::where('idUsuario', $idSolicitante)->where('idAmigo', $idUsuario)->get();
            if (count($possible_solicitud) < 1) {
                Respuesta::set(false, 'No tienes una solicitud de este usuario.');
                return Respuesta::toString();
            }
            $possible_solicitud = $possible_solicitud[0];
            if ($possible_solicitud["estado"] == "aceptada") {
                Respuesta::set(false, 'La solicitud ya está aceptada.');
                return Respuesta::toString();
            }
            $solicitud = Amistades::find($possible_solicitud["id"]);
            $solicitud->estado = "aceptada";
            $solicitud->save();
            Respuesta::set(true, 'Solicitud de amistad aceptada correctamente.');
            return Respuesta::toString();
        } catch (Exception $error) {
            Respuesta::set(false, $error);
            return Respuesta::toString();
        }
    }
}
