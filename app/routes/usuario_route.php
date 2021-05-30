<?php

use App\Lib\Respuesta;
use App\Model\Comentario;
use App\Model\Usuario;
use App\Model\MangaUsuario;
use \Firebase\JWT\JWT;


$app->group('/usuario/', function () {
    $this->get(
        '',
        function ($req, $res, $args) {
            $usuarios = Usuario::all(Usuario::getColumns());
            return $res->withJson($usuarios);
        }
    );

    $this->get(
        '{id}',
        function ($req, $res, $args) {
            $usuario = Usuario::where('id', $args["id"])->get(Usuario::getColumns())->first();
            unset($usuario->password);
            return $res->withJson($usuario);
        }
    );

    $this->get(
        '{id}/profile',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = Usuario::where('id', $args["id"])->get(Usuario::getColumns())->first();
            unset($usuario->password);
            $usuario['favoritos'] = MangaUsuario::getFav($usuario->id);
            $usuario['stats'] = MangaUsuario::getStats($usuario->id);
            $usuario['comentarios'] = Comentario::getComentariosUsuario($usuario->id, $decodetToken['usuario']->id);
            return $res->withJson($usuario);
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
            if (!isset($body["username"]) || !isset($body["email"]) || !isset($body["password"]) || !isset($body["biografia"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $userneme = $body["username"];
            $email = $body["email"];
            $password = $body["password"];
            $biografia = $body["biografia"];

            if (strlen($userneme) < 3 || strlen($userneme) > 50) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Formato de nombre de usuario incorrecto (mín. 3, máx. 50)."]));
            }

            if (strlen($email) < 3 || strlen($email) > 100) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Formato de email incorrecto (mín. 3, máx. 100)."]));
            }

            if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,255}$/", $password)) {
                return $res->withJson(Respuesta::set(false, ["field" => "password", "msg" => "Formato de contraseña incorrecto (mín. 8, máx. 255, al menos una letra mayúscula, una minúscula y 1 número)."]));
            }

            if (strlen($biografia) > 100) {
                return $res->withJson(Respuesta::set(false, ["field" => "biografia", "msg" => "Formato de biografia incorrecto (máx. 160)."]));
            }

            // // // REVISAR 'where('id', '!=', $usuarioToken->id)' NO VA BE
            $nombre_usuario_existente = Usuario::where('id', '!=', $usuarioToken->id)->where('username', 'like', $userneme)->get();
            if (count($nombre_usuario_existente) > 0) {
                return $res->withJson(Respuesta::set(false, ["field" => "username", "msg" => "Este nombre de ususario ya está en uso."]));
            }

            $email_existente = Usuario::where('id', '!=', $usuarioToken->id)->where('email', 'like', $email)->get();
            if (count($email_existente) > 0) {
                return $res->withJson(Respuesta::set(false, ["field" => "email", "msg" => "Este email ya está en uso."]));
            }
            try {
                $usuario = Usuario::find($usuarioToken->id);
                $usuario->username = $userneme;
                $usuario->email = $email;
                $usuario->password = password_hash($body["password"], PASSWORD_DEFAULT);
                $usuario->biografia = $biografia;
                $usuario->biografia = $biografia;
                if (isset($body['birthdayDate'])) $usuario->birthdayDate = $body['birthdayDate'];
                $usuario->save();
                unset($usuario->password);
                $now = new DateTime();
                $future = new DateTime("+1 week");
                $payload = ["iat" => $now->getTimeStamp(), "exp" => $future->getTimeStamp(), "usuario" => $usuario];
                $token = JWT::encode($payload, $secret, "HS256");
                return $res->withJson(Respuesta::set(true, 'Información del usuario modificada correctamente.', ["usuario" => $usuario, "token" => $token]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );

    $this->post(
        'avatar',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = $decodetToken['usuario'];
            $files = $req->getUploadedFiles();
            if (isset($files['avatar'])) {
                $avatar = $files['avatar']->file;
                Respuesta::set(false, 'Hey', ["files" => $files, "avatar" => $avatar]);
                return $res->withJson(Respuesta::toString());
                if (($avatar <> '') && is_uploaded_file($avatar)) {
                    try {
                        $image_name = $usuario->id . "-avatar-" . $usuario->username . ".jpg";
                        $tmp_name = $avatar;
                        $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/avatars/' . $image_name;
                        if (!move_uploaded_file($tmp_name, $dest_name)) {
                            Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.');
                            return $res->withJson(Respuesta::toString());
                        }
                        $us = Usuario::find($usuario->id);
                        $us->avatar = $image_name;
                        $us->save();
                        Respuesta::set(true, 'Avatar modificado correctamente.');
                        return $res->withJson(Respuesta::toString());
                    } catch (Exception $error) {
                        Respuesta::set(false, $error);
                        return $res->withJson(Respuesta::toString());
                    }
                }
            } else {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
        }
    );

    $this->post(
        'banner',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = $decodetToken['usuario'];
            $files = $req->getUploadedFiles();
            if (isset($files['banner'])) {
                $banner = $files['banner']->file;
                if (($banner <> '') && is_uploaded_file($banner)) {
                    try {
                        $image_name = $usuario->id . "-banner-" . $usuario->username . ".jpg";
                        $tmp_name = $banner;
                        $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/banners/' . $image_name;
                        if (!move_uploaded_file($tmp_name, $dest_name)) {
                            Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.');
                            return $res->withJson(Respuesta::toString());
                        }
                        $us = Usuario::find($usuario->id);
                        $us->banner = $image_name;
                        $us->save();
                        Respuesta::set(true, 'Banner modificado correctamente.');
                        return $res->withJson(Respuesta::toString());
                    } catch (Exception $error) {
                        Respuesta::set(false, $error);
                        return $res->withJson(Respuesta::toString());
                    }
                }
            } else {
                Respuesta::set(false, 'Faltan campos.');
                return $res->withJson(Respuesta::toString());
            }
        }
    );
});
