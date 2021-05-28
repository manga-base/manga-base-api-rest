<?php

use App\Lib\Respuesta;
use App\Model\Comentario;
use App\Model\Usuario;
use App\Model\MangaUsuario;


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

    $this->post(
        'avatar',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            $usuario = $decodetToken['usuario'];
            $files = $req->getUploadedFiles();
            if (isset($files['avatar'])) {
                $avatar = $files['avatar']->file;
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

            // is_uploaded_file()
            // move_uploaded_file()
        }
    );
});
