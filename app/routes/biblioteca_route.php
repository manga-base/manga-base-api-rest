<?php

use App\Lib\Respuesta;
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
                Respuesta::set(true, '', $filtros);
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
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
                    $manga['autores'] = $info->autores;
                    $manga['revistas'] = $info->revistas;
                    $manga['generos'] = $info->generos;
                }
                Respuesta::set(true, '', $mangas);
                return $res->withJson(Respuesta::toString());
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }
    );
});
