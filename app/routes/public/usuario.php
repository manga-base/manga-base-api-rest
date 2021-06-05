<?php

use App\Lib\Respuesta;
use App\Model\ActividadUsuario;
use App\Model\Usuario;


$app->group('/public-usuario/', function () {

    $this->get(
        '{idUsuario}/profile',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $idUsuario = $args['idUsuario'];
            return $res->withJson(Usuario::getProfile($idUsuario));
        }
    );

    $this->get(
        'destacados',
        function ($req, $res, $args) {
            try {
                $usuariosDestacados = ActividadUsuario::select(
                    'idUsuario',
                    'usuario.username',
                    'usuario.avatar'
                )
                    ->join('usuario', 'actividad_usuario.idUsuario', '=', 'usuario.id')
                    ->groupBy('idUsuario')
                    ->orderByDesc('COUNT(*)')
                    ->limit(3)
                    ->get();
                return $res->withJson(Respuesta::set(true, '', $usuariosDestacados));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
