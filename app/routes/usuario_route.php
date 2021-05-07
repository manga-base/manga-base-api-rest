<?php

use App\Model\Usuario;
use App\Model\MangaUsuario;


$app->group('/usuario/', function () {
    $this->get(
        '',
        function ($req, $res, $args) {
            $usuarios = Usuario::all(Usuario::getColumns());
            return $res->withJson($usuarios);
        }
    );
    $this->get(
        '{id}',
        function ($req, $res, $args) {
            $usuario = Usuario::where('id', $args["id"])->get(Usuario::getColumns())->first();
            unset($usuario->password);
            return $res->withJson($usuario);
        }
    );
    $this->get(
        '{id}/profile',
        function ($req, $res, $args) {
            $usuario = Usuario::where('id', $args["id"])->get(Usuario::getColumns())->first();
            unset($usuario->password);
            $usuario['favoritos'] = MangaUsuario::getFav($usuario->id);
            $usuario['stats'] = MangaUsuario::getStats($usuario->id);
            return $res->withJson($usuario);
        }
    );
});
