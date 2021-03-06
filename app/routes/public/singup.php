<?php

use App\Lib\Mail;
use App\Lib\Respuesta;
use App\Model\Message;
use App\Model\Usuario;

$app->group('/signup/', function () {
    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();

            if (!isset($body["password"]) || !isset($body["passwordConfirmation"]) || !isset($body["username"]) || !isset($body["email"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $userneme = $body["username"];
            $email = $body["email"];
            $password = $body["password"];
            $passwordConfirmation = $body["passwordConfirmation"];

            if ($password != $passwordConfirmation) {
                return $res->withJson(Respuesta::set(false, ["field" => "password", "msg" => "Las contraseñas no coinciden."]));
            }

            if (strlen($userneme) < 3 || strlen($userneme) > 50) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Formato de nombre de usuario incorrecto (mín. 3, máx. 50)."]));
            }

            if (strlen($email) < 3 || strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $res->withJson(Respuesta::set(false, ["field" => "email", "msg" => "Formato de email invalido (mín. 3, máx. 100)."]));
            }

            if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,255}$/", $password)) {
                return $res->withJson(Respuesta::set(false, ["field" => "password", "msg" => "Formato de contraseña incorrecto (mín. 8, máx. 255, al menos una letra mayúscula, una minúscula y 1 número)."]));
            }

            $nombre_usuario_existente = Usuario::where('username', $userneme)->get();
            if (count($nombre_usuario_existente) > 0) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Este nombre de ususario ya está en uso."]));
            }

            $email_existente = Usuario::where('email', $email)->get();
            if (count($email_existente) > 0) {
                return $res->withJson(Respuesta::set(false, ["field" => "email", "msg" => "Este email ya está en uso."]));
            }


            try {
                $activationCode = md5(rand(0, 1000));

                $usuario = new Usuario();
                $usuario->username = $userneme;
                $usuario->password = password_hash($password, PASSWORD_DEFAULT);
                $usuario->email = $email;
                $usuario->activationCode = $activationCode;
                $usuario->save();
                unset($usuario->password);

                if (Message::checkSpace() > 100) {
                    return $res->withJson(Respuesta::set(false, ["field" => "default", "msg" => "Lo sentimos ahora no es posible crear una cuenta, ya que el servidor de correo ha llegado a su límite."]));
                }

                if (!Mail::sendEmailVerification($usuario->email, $usuario->username, $activationCode)) {
                    return $res->withJson(Respuesta::set(false, ["field" => "default", "msg" => "Ha ocurrido un problema enviando el correo de confirmación."]));
                }

                return $res->withJson(Respuesta::set(true, 'Usuario creado correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
