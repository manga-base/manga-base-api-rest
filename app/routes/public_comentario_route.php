<?php

use App\Model\Comentario;

$app->group('/public-comentario/', function () {

    $this->get('manga/{id}', function ($req, $res, $args) {
        return $res->withJson(Comentario::getPublicComentariosManga($args['id']));
    });

    $this->get('usuario/{id}', function ($req, $res, $args) {
        return $res->withJson(Comentario::getPublicComentariosUsuario($args['id']));
    });
});
