<?php

use App\Lib\Respuesta;
use App\Model\BaseMangas;
use Illuminate\Database\Capsule\Manager as DB;


$app->group('/manga/', function () {

    $this->get(
        'info/{id}',
        function ($req, $res, $args) {
            try {
                $manga = BaseMangas::find($args['id']);
                if (!$manga) {
                    Respuesta::set(false, 'Manga no encontrado.');
                    return $res->withJson(Respuesta::toString());
                }
                DB::statement("call info_manga(:idManga, @info)", ["idManga" => $manga->id]);
                $resultado = DB::select("select @info AS info");
                $info = json_decode($resultado[0]->info);
                $manga['autores'] = $info->autores;
                $manga['revistas'] = $info->revistas;
                $manga['generos'] = $info->generos;
                return $res->withJson(Respuesta::set(true, '', $manga));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );

    $this->get(
        'recomendados',
        function ($req, $res, $args) {
            try {
                $mangas = BaseMangas::orderBy('nota')->limit(5)->get();
                return $res->withJson(Respuesta::set(true, '', $mangas));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error));
            }
        }
    );
});
