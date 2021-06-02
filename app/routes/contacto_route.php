<?php

use App\Lib\Respuesta;
use App\Model\Contacto;

$app->group('/contacto/', function () {

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $files = $req->getUploadedFiles();
            if (!isset($body["nombre"]) || !isset($body["email"]) || !isset($body["mensaje"])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $nombre = $body["nombre"];
            $email = $body["email"];
            $mensaje = $body["mensaje"];
            $imagenes = isset($files['imagenes']) ? $files['imagenes'] : "No hay imagenes";
            try {

                $contacto = new Contacto();
                $contacto->nombre = $nombre;
                $contacto->email = $email;
                $contacto->mensaje = $mensaje;
                $contacto->save();

                return $res->withJson(Respuesta::set(true, 'Mensaje enviado correctamente.', ["contacto" => $contacto, "files" => $files, "imagenes" => $imagenes]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
