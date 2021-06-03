<?php

use App\Lib\Respuesta;
use App\Model\Contacto;
use App\Model\FotoContacto;

$app->group('/private-contacto/', function () {

    $this->get(
        '',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->admin === 0) {
                return $res->withJson(Respuesta::set(false, 'No eres administrador! ಠ_ಠ'));
            }
            try {
                $mensajes = Contacto::where('leido', 0)->get();
                foreach ($mensajes as $mensaje) {
                    $mensaje['fotos'] = FotoContacto::where('idContacto', $mensaje->id)->get();
                }
                return $res->withJson(Respuesta::set(true, '', $mensajes));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
