<?php

use App\Lib\Respuesta;
use App\Model\Autor;
use App\Model\BaseMangas;
use App\Model\Genero;
use App\Model\Revista;
use Illuminate\Database\Capsule\Manager as DB;


$app->group('/biblioteca/', function () {

    $this->get(
        'filtros',
        function ($req, $res, $args) {
            try {
                DB::statement("call filtros_biblioteca(@filtros)");
                $resultado = DB::select("select @filtros AS filtros");
                $filtros = json_decode($resultado[0]->filtros);
                return $res->withJson(Respuesta::set(true, '', $filtros));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->get(
        'mangas',
        function ($req, $res, $args) {
            try {
                $mangas = BaseMangas::all();
                foreach ($mangas as $manga) {
                    $manga['autores'] = Autor::getAutoresMangaArray($manga->id);
                    $manga['revistas'] = Revista::getRevistasEditorialManga($manga->id);
                    $manga['generos'] = Genero::getGenerosMangaArray($manga->id);
                }
                return $res->withJson(Respuesta::set(true, '', $mangas));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
