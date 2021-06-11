<?php

use App\Lib\Respuesta;
use App\Model\Autor;
use App\Model\BaseMangas;
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
                    DB::statement("call info_manga(:idManga, @info)", ["idManga" => $manga->id]);
                    $resultado = DB::select("select @info AS info");
                    $info = json_decode($resultado[0]->info);
                    $manga['autores'] = Autor::getAutoresManga($manga->id);
                    $manga['revistas'] = $info->revistas;
                    $manga['generos'] = $info->generos;
                }
                return $res->withJson(Respuesta::set(true, '', $mangas));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
