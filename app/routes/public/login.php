<?php

use App\Model\Usuario;
use \Firebase\JWT\JWT;
use App\Lib\Respuesta;

$app->group('/login', function () {
    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            if (!isset($body["email"]) || !isset($body["password"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $email = $body["email"];
            $password = $body["password"];

            try {
                $posible_usuario = Usuario::where("email", $email)->orWhere("username", $email)->get();
                if (count($posible_usuario) < 1) {
                    return $res->withJson(Respuesta::set(false, 'Nombre de usuario o contrasenya incorrectos.'));
                }
                $usuario = $posible_usuario[0];
                if (!password_verify($password, $usuario->password)) {
                    return $res->withJson(Respuesta::set(false, 'Nombre de usuario o contrasenya incorrectos'));
                }
                unset($usuario->password);
                if ($usuario->activationCode !== 'Active') {
                    return $res->withJson(Respuesta::set(false, 'Esta cuenta no esta activada. Revisa tu email.'));
                }
                $settings = $this->get('settings');
                $secret = $settings['jwt']['secret'];
                $now = new DateTime();
                $future = new DateTime("+1 week");
                $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
                $token = JWT::encode($payload, $secret, "HS256");
                return $res->withJson(Respuesta::set(true, 'Bienvenido.', ["usuario" => $usuario, "token" => $token]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
