<?php

use \Tuupola\Middleware\JwtAuthentication;
use App\Lib\Respuesta;

$app->add(new JwtAuthentication([
    "path" => ["/whoami", "/usuario/", "/amistades/", "/comentario/", "/puntuacion-comentario/", "/comentario-manga/", "/comentario-usuario/", "/manga-usuario/"],
    "attribute" => "decoded_token_data",
    "secret" => "g4165gf1fdfsd6fgdsg65fd6fsdfsd1v654dfsd15",
    "algorithm" => ["HS256"],
    "error" => function ($res, $args) {
        Respuesta::set(false, $args["message"]);
        return $res->withJson(Respuesta::toString());
    }
]));
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
