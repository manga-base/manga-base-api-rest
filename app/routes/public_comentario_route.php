<?php

use App\Model\Comentario;
use App\Lib\Respuesta;


$app->group('/public-comentario/', function () {

    $this->get('hola', function ($req, $res, $args) {
        return $res->withJson("Hola");
    });

    $this->get('manga/{id}', function ($req, $res, $args) {
        return $res->withJson(Comentario::getPublicComentariosManga($args['id']));

    });
});
