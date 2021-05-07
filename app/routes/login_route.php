<?php

use App\Model\Usuario;
use \Firebase\JWT\JWT;

$app->group('/login', function () {
    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $settings = $this->get('settings');
            $secret = $settings['jwt']['secret'];

            if (!isset($body["email"]) || !isset($body["password"])) {
                return $res->withJson(array("error" => "Faltan campos."));
            }

            $email = $body["email"];
            $password = $body["password"];

            $usuario_existente = Usuario::where("email", $email)->orWhere("username", $email)->get(Usuario::getColumns());
            if (count($usuario_existente) < 1) {
                return $res->withJson(array("error" => "Nombre de usuario o contrasenya incorrectos."));
            }

            $usuario = $usuario_existente[0];

            if (!password_verify($password, $usuario->password)) {
                return $res->withJson(array("error" => "Nombre de usuario o contrasenya incorrectos."));
            }

            unset($usuario->password);

            $now = new DateTime();
            $future = new DateTime("+1 week");
            $settings = $secret;
            $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
            $token = JWT::encode($payload, $secret, "HS256");
            return $res->withJson(array("usuario" => $usuario, "token" => $token));
        }
    );
});
