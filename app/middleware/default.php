<?php

use \Tuupola\Middleware\JwtAuthentication;

$app->add(new JwtAuthentication([
    "path" => ["/whoami", "/usuario/"],
    "attribute" => "decoded_token_data",
    "secret" => "g4165gf1fdfsd6fgdsg65fd6fsdfsd1v654dfsd15",
    "algorithm" => ["HS256"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data));
    }
]));

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
