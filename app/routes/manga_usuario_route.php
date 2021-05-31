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
            Respuesta::set(false, $error);
            return $res->withJson(Respuesta::toString());
        }
    });

    $this->get('{idManga}/{idUsuario}', function ($req, $res, $args) {
        if (!isset($args['idManga']) || !isset($args['idUsuario'])) {
            Respuesta::set(false, 'Faltan campos.');
            return $res->withJson(Respuesta::toString());
        }
        $idManga = $args['idManga'];
        $idUsuario = $args['idUsuario'];
        $decodetToken = $req->getAttribute('decoded_token_data');
        if ($decodetToken['usuario']->id != $idUsuario) {
            Respuesta::set(false, 'No puedes solicitar el estado de un manga de otras personas! ಠ_ಠ');
            return $res->withJson(Respuesta::toString());
        }
        try {
            $arrayInfo = MangaUsuario::where('idManga', $idManga)->where('idUsuario', $idUsuario)->get();
            $info = isset($arrayInfo[0]) ? $arrayInfo[0] : [];
            $info['estados'] = EstadosMangaUsuario::all();
            $manga = Manga::find($idManga);
            $info['capitulosManga'] = $manga->capitulos;
            $info['volumenesManga'] = $manga->volumenes;
            Respuesta::set(true, '', $info);
            return $res->withJson(Respuesta::toString());
        } catch (Exception $error) {
            Respuesta::set(false, $error);
            return $res->withJson(Respuesta::toString());
        }
    });

    $this->post(
        '',
        function ($req, $res, $args) {
            $body = $req->getParsedBody();
            $decodetToken = $req->getAttribute('decoded_token_data');

            if (isset($body['id'])) {
                $possibleMangaUsuario = MangaUsuario::find($body['id']);
                if ($possibleMangaUsuario) {
                    if ($decodetToken['usuario']->id != $possibleMangaUsuario->idUsuario) {
                        return $res->withJson(Respuesta::set(false, 'No puedes enviar comentarios en nombre de otras personas! ಠ_ಠ'));
                    }

                    if (isset($body['favorito'])) {
                        $possibleMangaUsuario->favorito = $body['favorito'];
                    }
                    if (isset($body['nota'])) {
                        $possibleMangaUsuario->nota = $body['nota'];
                    }
                    if (isset($body['idEstado'])) {
                        $possibleMangaUsuario->idEstado = $body['idEstado'];
                    }
                    if (isset($body['volumenes'])) {
                        $possibleMangaUsuario->volumenes = $body['volumenes'];
                    }
                    if (isset($body['capitulos'])) {
                        $possibleMangaUsuario->capitulos = $body['capitulos'];
                    }
                    $possibleMangaUsuario->save();
                    return $res->withJson(Respuesta::set(true, 'Se ha actualizado la información', $body));
                }
            }

            if (!isset($body['idManga']) || !isset($body['idUsuario'])) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.'));
            }

            $idManga = $body['idManga'];
            $idUsuario = $body['idUsuario'];
            if ($decodetToken['usuario']->id != $idUsuario) {
                Respuesta::set(false, 'No puedes enviar comentarios en nombre de otras personas! ಠ_ಠ');
                return $res->withJson(Respuesta::toString());
            }
            try {
                $mangaUsuario = new MangaUsuario();
                $mangaUsuario->idManga = $idManga;
                $mangaUsuario->idUsuario = $idUsuario;
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
                Respuesta::set(true, 'Se ha actualizado la información', $body);
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }

    );
});
