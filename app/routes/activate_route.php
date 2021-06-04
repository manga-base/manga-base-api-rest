<?php

use App\Lib\Respuesta;
use App\Model\Message;
use App\Model\Usuario;

$app->group('/activate/', function () {

    $this->get(
        '{activationCode}',
        function ($req, $res, $args) {
            try {
                $posible_usuario = Usuario::where('activationCode', $args['activationCode'])->get();
                if (count($posible_usuario) < 1) {
                    return $res->withJson(Respuesta::set(false, 'No existe ningún usuario con este código de activación'));
                }
                $usuario = $posible_usuario[0];
                $usuario->activationCode = 'Active';
                $usuario->save();
                return $res->withRedirect('https://mangabase.tk/email-verificado');
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(true, $error));
            }
        }
    );

    $this->get(
        'test',
        function ($req, $res, $args) {
            return $res->withJson(Respuesta::set(true, '', Message::checkSpace()));
        }
    );
});
