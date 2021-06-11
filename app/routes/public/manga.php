<?php

use App\Lib\Respuesta;
use App\Model\Autor;
use App\Model\BaseMangas;
use App\Model\Demografia;
use App\Model\Estado;
use App\Model\Genero;
use App\Model\Manga;
use App\Model\Revista;

$app->group('/manga/', function () {

    $this->get(
        'info/{id}',
        function ($req, $res, $args) {
            try {
                $manga = Manga::find($args['id']);
                if (!$manga) {
                    Respuesta::set(false, 'Manga no encontrado.');
                    return $res->withJson(Respuesta::toString());
                }
                $manga['estado'] = Estado::where('idEstado', $manga->idEstado)->pluck('estado');
                $manga['demografia'] = Demografia::where('idDemografia', $manga->idDemografia)->pluck('demografia');
                $manga['autores'] = Autor::getAutoresManga($manga->id);
                $manga['revistas'] = Revista::getRevistasManga($manga->id);
                $manga['generos'] = Genero::getGenerosManga($manga->id);
                return $res->withJson(Respuesta::set(true, '', $manga));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->get(
        'recomendados',
        function ($req, $res, $args) {
            try {
                $mangas = BaseMangas::orderByDesc('nota')->limit(5)->get();
                return $res->withJson(Respuesta::set(true, '', $mangas));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
