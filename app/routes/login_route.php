<?php

use App\Model\Usuario;
use \Firebase\JWT\JWT;
use App\Lib\Respuesta;

$app->group('/login', function () {
    $this->get('', function ($req, $res, $args) {
        Respuesta::set(true, 'Hola', ['Kpassa?']);
        return $res->withJson(Respuesta::toString());
    });

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $settings = $this->get('settings');
            $secret = $settings['jwt']['secret'];

            if (!isset($body["email"]) || !isset($body["password"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $email = $body["email"];
            $password = $body["password"];

            $usuario_existente = Usuario::where("email", $email)->orWhere("username", $email)->get(Usuario::getColumns());
            if (count($usuario_existente) < 1) {
                return $res->withJson(Respuesta::set(false, 'Nombre de usuario o contrasenya incorrectos.'));
            }

            $usuario = $usuario_existente[0];

            if (!password_verify($password, $usuario->password)) {
                return $res->withJson(Respuesta::set(false, 'Nombre de usuario o contrasenya incorrectos.'));
            }

            unset($usuario->password);

            $now = new DateTime();
            $future = new DateTime("+1 week");
            $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
            $token = JWT::encode($payload, $secret, "HS256");
            return $res->withJson(Respuesta::set(true, 'Login correcto.', ["usuario" => $usuario, "token" => $token]));
        }
    );
});
