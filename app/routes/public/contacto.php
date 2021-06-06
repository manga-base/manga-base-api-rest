<?php

use App\Lib\Respuesta;
use App\Model\Contacto;
use App\Model\FotoContacto;

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
            try {
                $contacto = new Contacto();
                $contacto->nombre = $nombre;
                $contacto->email = $email;
                $contacto->mensaje = $mensaje;
                $contacto->save();
                foreach ($files as $key => $file) {
                    $file = $file->file;
                    if (($file <> '') && is_uploaded_file($file)) {
                        $image_name = 'C' . $contacto->id . 'K' . $key . ".jpg";
                        $tmp_name = $file;
                        $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/contacto/' . $image_name;
                        if (!move_uploaded_file($tmp_name, $dest_name)) {
                            Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.');
                            return $res->withJson(Respuesta::toString());
                        }
                        $foto_contacto = new FotoContacto();
                        $foto_contacto->idContacto = $contacto->id;
                        $foto_contacto->foto = $image_name;
                        $foto_contacto->save();
                    }
                }
                return $res->withJson(Respuesta::set(true, 'Mensaje enviado correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
