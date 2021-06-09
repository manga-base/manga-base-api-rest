<?php

use App\Model\EstadosMangaUsuario;
use App\Lib\Respuesta;
use App\Model\Manga;
use App\Model\MangaUsuario;

$app->group('/manga-usuario/', function () {

    $this->get('', function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        try {
            $mangas = MangaUsuario::select(
                'manga_usuario.idEstado',
                'manga_usuario.favorito',
                'manga.id',
                'manga.tituloPreferido',
                'manga.foto'
            )
                ->join('manga', 'manga_usuario.idManga', '=', 'manga.id')
                ->where('idUsuario', $decodetToken['usuario']->id)
                ->get();
            return $res->withJson(Respuesta::set(true, '', $mangas));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error->getMessage()));
        }
    });

    $this->get('{idManga}', function ($req, $res, $args) {
        if (!isset($args['idManga'])) {
            return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
        }

        $idManga = $args['idManga'];
        $decodetToken = $req->getAttribute('decoded_token_data');

        try {
            $manga = Manga::find($idManga);
            if (!$manga) {
                return $res->withJson(Respuesta::set(false, 'El manga que intentas buscar no existe.'));
            }
            $arrayInfo = MangaUsuario::where('idManga', $idManga)->where('idUsuario', $decodetToken['usuario']->id)->get();
            $info = isset($arrayInfo[0]) ? $arrayInfo[0] : [];
            $info['titulo'] = $manga->tituloPreferido;
            $info['estados'] = EstadosMangaUsuario::all();
            $info['capitulosManga'] = $manga->capitulos;
            $info['volumenesManga'] = $manga->volumenes;
            return $res->withJson(Respuesta::set(true, '', $info));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error->getMessage()));
        }
    });

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $decodetToken = $req->getAttribute('decoded_token_data');
            $mangaUsuario = null;
            if (isset($body['id'])) {
                $id = $body['id'];
                $possibleMangaUsuario = MangaUsuario::find($body['id']);
                if (!$possibleMangaUsuario) {
                    return $res->withJson(Respuesta::set(false, 'No se ha encontrado ningun resultado con el identificador ' . $id . '.'));
                }
                if ($decodetToken['usuario']->id != $possibleMangaUsuario->idUsuario) {
                    return $res->withJson(Respuesta::set(false, '¡No puedes editar información de mangas de otras personas! ಠ_ಠ'));
                }
                $mangaUsuario = $possibleMangaUsuario;
            } else {
                if (!isset($body["idManga"])) {
                    return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
                }
                $mangaUsuario = new MangaUsuario();
                $mangaUsuario->idManga = $body["idManga"];
                $mangaUsuario->idUsuario = $decodetToken['usuario']->id;
            }

            if (!isset($body["idEstado"]) || $body["idEstado"] < 1) {
                return $res->withJson(Respuesta::set(false, 'Necesitas poner un estado al manga para poder guardarlo en tu lista.'));
            }

            try {
                if (isset($body['favorito'])) {
                    $mangaUsuario->favorito = $body['favorito'];
                }
                if (isset($body['nota'])) {
                    $mangaUsuario->nota = $body['nota'];
                }
                if (isset($body['idEstado'])) {
                    $mangaUsuario->idEstado = $body['idEstado'];
                }
                if (isset($body['volumenes'])) {
                    $mangaUsuario->volumenes = $body['volumenes'];
                }
                if (isset($body['capitulos'])) {
                    $mangaUsuario->capitulos = $body['capitulos'];
                }
                $mangaUsuario->save();
                return $res->withJson(Respuesta::set(true, 'Se ha actualizado la información'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }

    );

    $this->delete('{id}', function ($req, $res, $args) {
        if (!isset($args['id'])) {
            return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
        }

        $id = $args['id'];
        $decodetToken = $req->getAttribute('decoded_token_data');

        try {
            $mangaUsuario = MangaUsuario::find($id);
            if (!$mangaUsuario) {
                return $res->withJson(Respuesta::set(false, 'La información que intentas eliminar no existe.'));
            }
            if ($mangaUsuario->idUsuario != $decodetToken['usuario']->id) {
                return $res->withJson(Respuesta::set(false, '¡No puedes eliminar información de mangas de otras personas! ಠ_ಠ'));
            }
            $mangaUsuario->delete();
            return $res->withJson(Respuesta::set(true, 'Información eliminada correctamente'));
        } catch (Exception $error) {
            return $res->withJson(Respuesta::set(false, $error->getMessage()));
        }
    });
});
