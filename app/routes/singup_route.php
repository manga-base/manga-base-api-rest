<?php

use App\Model\Usuario;
use \Firebase\JWT\JWT;

$app->group('/signup/', function () {
    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $settings = $this->get('settings');
            $secret = $settings['jwt']['secret'];

            if (!isset($body["password"]) || !isset($body["passwordConfirmation"]) || !isset($body["username"]) || !isset($body["email"])) {
                return $res->withJson(array("error" => "Faltan campos."));
            }
            if ($body["password"] != $body["passwordConfirmation"]) {
                return $res->withJson(array("error" => "Las contraseñas no coinciden."));
            }
            $email_existente = Usuario::where('email', 'like', $body["email"])->get();
            if (count($email_existente) > 0) {
                return $res->withJson(array("error" => "Este email ya está en uso."));
            }
            $nombre_usuario_existente = Usuario::where('username', 'like', $body["username"])->get();
            if (count($nombre_usuario_existente) > 0) {
                return $res->withJson(array("error" => "Este nombre de ususario ya está en uso."));
            }

            $usuario = new Usuario();
            $usuario->username = $body["username"];
            $usuario->password = password_hash($body["password"], PASSWORD_DEFAULT);
            $usuario->email = $body["email"];
            $usuario->save();
            unset($usuario->password);

            $now = new DateTime();
            $future = new DateTime("+1 week");
            $settings = $secret; // get settings array.
            $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
            $token = JWT::encode($payload, $secret, "HS256");
            return $res->withJson(array("usuario" => $usuario, "token" => $token));
        }
    );
});
