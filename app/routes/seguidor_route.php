<?php

use App\Lib\Respuesta;
use App\Model\Seguidor;
use App\Model\Usuario;

$app->group('/seguidor/', function () {

    $this->get(
        '{idUsuario}',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            #$decodetToken = $req->getAttribute('decoded_token_data');
            try {
                $seguidores = Seguidor::select('usuario.id', 'usuario.username', 'ususario.avatar')
                    ->where('idSeguido', $args['idUsuario'])
                    ->join('usuario', 'seguidor.idUsuario', '=', 'usuario.id')
                    ->get();
                $siguiendo = Seguidor::select('usuario.id', 'usuario.username', 'ususario.avatar')
                    ->where('idUsuario', $args['idUsuario'])
                    ->join('usuario', 'seguidor.idSeguido', '=', 'usuario.id')
                    ->get();
                return $res->withJson(Respuesta::set(true, '', ["seguidores" => $seguidores, "siguiendo" => $siguiendo]));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );

    $this->post(
        '{idUsuario}',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($args['idUsuario'] == $decodetToken['usuario']->id) {
                return $res->withJson(Respuesta::set(false, 'No puedes seguirte a ti mismo. (｡◕‿◕｡)'));
            }
            try {
                $usuario = Usuario::find($args['idUsuario']);
                if (!$usuario) {
                    return $res->withJson(Respuesta::set(false, 'No existe ningun usuario con el id \'' . $args['idUsuario'] . '\'.'));
                }

                $posible_seguidor = Seguidor::where('idUsuario', $decodetToken['usuario']->id)->where('idSeguido', $args['idUsuario'])->get();
                if (count($posible_seguidor) > 0) {
                    return $res->withJson(Respuesta::set(false, 'Ya sigues a ' . $usuario->username . '.'));
                }

                $seguidor = new Seguidor();
                $seguidor->idUsuario = $decodetToken['usuario']->id;
                $seguidor->idSeguido = $args['idUsuario'];
                $seguidor->save();
                return $res->withJson(Respuesta::set(true, 'Ahora sigues a ' . $usuario->username . '.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );

    $this->delete(
        '{idUsuario}',
        function ($req, $res, $args) {
            if (!isset($args['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }
            $decodetToken = $req->getAttribute('decoded_token_data');
            try {
                $usuario = Usuario::find($args['idUsuario']);
                if (!$usuario) {
                    return $res->withJson(Respuesta::set(false, 'No existe ningun usuario con el id \'' . $args['idUsuario'] . '\'.'));
                }

                $posible_seguidor = Seguidor::where('idUsuario', $decodetToken['usuario']->id)->where('idSeguido', $args['idUsuario'])->get();
                if (count($posible_seguidor) < 1) {
                    return $res->withJson(Respuesta::set(false, 'No sigues a ' . $usuario->username . '.'));
                }

                $seguidor = $posible_seguidor[0];
                $seguidor->delete();
                return $res->withJson(Respuesta::set(true, 'Ya no sigues a ' . $usuario->username . '.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
