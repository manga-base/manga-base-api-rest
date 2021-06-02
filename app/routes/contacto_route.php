<?php

use App\Lib\Respuesta;
use App\Model\Contacto;

$app->group('/contacto/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $files = $req->getUploadedFiles();
            if (!isset($body["name"]) || !isset($body["email"]) || !isset($body["mensaje"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $name = $body["name"];
            $email = $body["email"];
            $mensaje = $body["mensaje"];
            try {

                $contacto = new Contacto();
                $contacto->name = $name;
                $contacto->email = $email;
                $contacto->mensaje = $mensaje;
                $contacto->save();

                return $res->withJson(Respuesta::set(true, 'Mensaje enviado correctamente.', ["contacto" => $contacto, "files" => $files]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
