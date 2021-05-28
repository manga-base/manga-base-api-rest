<?php

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
            $body = $req->getParsedBody();
            $files = $req->getUploadedFiles();
            return $res->withJson(array("body" => $body, "files" => $files));
            // if (isset($files['avatar'])) {
            //     # code...
            // }

            // is_uploaded_file()
            // move_uploaded_file()
        }
    );
});
