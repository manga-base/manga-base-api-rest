<?php

use App\Lib\Respuesta;
use App\Model\Amistades;


$app->group('/amistades/', function () {

    $this->get(
        '{idUsuario}',
        function ($req, $res, $args) {
            return $res->withJson(Amistades::getAmigos($args["idUsuario"]));
        }
    );

    $this->post(
        '{idUsuario}/{idAmigo}/solicitar',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $args["idUsuario"]) {
                Respuesta::set(false, 'No puedes enviar solicitudes de amistad en nombre de otros! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            return $res->withJson(Amistades::solicitudDeAmistad($args["idUsuario"], $args["idAmigo"]));
        }
    );

    $this->post(
        '{idUsuario}/{idSolicitante}/aceptar',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->id != $args["idUsuario"]) {
                Respuesta::set(false, 'No aceptar solicitudes de amistad en nombre de otros! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            return $res->withJson(Amistades::aceptarSolicitud($args["idSolicitante"], $args["idUsuario"]));
        }
    );
});
