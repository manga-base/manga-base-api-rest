<?php

use App\Lib\Respuesta;
use App\Model\Usuario;
use \Firebase\JWT\JWT;


$app->group('/usuario/', function () {

    $this->get(
        '{idUsuario}/profile',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $idUsuario = $args['idUsuario'];
            $decodetToken = $req->getAttribute('decoded_token_data');
            return $res->withJson(Usuario::getProfile($idUsuario, $decodetToken['usuario']->id));
        }
    );

    $this->put(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $decodetToken = $req->getAttribute('decoded_token_data');
            $settings = $this->get('settings');
            $secret = $settings['jwt']['secret'];

            $usuarioToken = $decodetToken['usuario'];
            if (!isset($body["username"]) || !isset($body["password"]) || !isset($body["biografia"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $userneme = $body["username"];
            $password = $body["password"];
            $biografia = $body["biografia"];

            if (strlen($userneme) < 3 || strlen($userneme) > 50) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Formato de nombre de usuario incorrecto (mín. 3, máx. 50)."]));
            }

            if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,255}$/", $password)) {
                return $res->withJson(Respuesta::set(false, ["field" => "password", "msg" => "Formato de contraseña incorrecto (mín. 8, máx. 255, al menos una letra mayúscula, una minúscula y 1 número)."]));
            }

            if (strlen($biografia) > 160) {
                return $res->withJson(Respuesta::set(false, ["field" => "biografia", "msg" => "Formato de biografia incorrecto (máx. 160)."]));
            }

            try {
                $nombre_usuario_existente = Usuario::where('id', '!=', $usuarioToken->id)->where('username', 'like', $userneme)->get();
                if (count($nombre_usuario_existente) > 0) {
                    return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Este nombre de ususario ya está en uso."]));
                }

                $usuario = Usuario::find($usuarioToken->id);
                $usuario->username = $userneme;
                $usuario->password = password_hash($body["password"], PASSWORD_DEFAULT);
                $usuario->biografia = $biografia;
                if (isset($body['birthdayDate'])) {
                    $usuario->birthdayDate = $body['birthdayDate'];
                } else {
                    $usuario->birthdayDate = null;
                }
                $usuario->save();
                unset($usuario->password);
                $now = new DateTime();
                $future = new DateTime("+1 week");
                $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
                $token = JWT::encode($payload, $secret, "HS256");
                return $res->withJson(Respuesta::set(true, 'Información del usuario modificada correctamente.', ["usuario" => $usuario, "token" => $token]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->post(
        'avatar',
        function ($req, $res, $args) {
            $files = $req->getUploadedFiles();

            if (!isset($files['avatar'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $avatar = $files['avatar']->file;
            if ($avatar == '' || !is_uploaded_file($avatar)) {
                return $res->withJson(Respuesta::set(false, 'El formato del archivo enviado no es correcto.'));
            }

            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = $decodetToken['usuario'];
            try {
                $image_name = $usuario->id . "-avatar-" . $usuario->username . ".jpg";
                $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/avatars/' . $image_name;
                if (!move_uploaded_file($avatar, $dest_name)) {
                    return $res->withJson(Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.'));
                }
                $us = Usuario::find($usuario->id);
                $us->avatar = $image_name;
                $us->save();
                return $res->withJson(Respuesta::set(true, 'Avatar modificado correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->post(
        'banner',
        function ($req, $res, $args) {
            $files = $req->getUploadedFiles();

            if (!isset($files['banner'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $banner = $files['banner']->file;
            if ($banner == '' || !is_uploaded_file($banner)) {
                return $res->withJson(Respuesta::set(false, 'El formato del archivo enviado no es correcto.'));
            }

            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = $decodetToken['usuario'];
            try {
                $image_name = $usuario->id . "-banner-" . $usuario->username . ".jpg";
                $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/banners/' . $image_name;
                if (!move_uploaded_file($banner, $dest_name)) {
                    return $res->withJson(Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.'));
                }
                $us = Usuario::find($usuario->id);
                $us->banner = $image_name;
                $us->save();
                return $res->withJson(Respuesta::set(true, 'Banner modificado correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
