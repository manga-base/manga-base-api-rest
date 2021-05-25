<?php

namespace App\Model;

use Illuminate\Database\Capsule\Manager as DB;

class MangaUsuario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'manga_usuario';

    public static function getColumns()
    {
        return ['id', 'favorito', 'nota', 'idEstado', 'volumenes', 'capitulos', 'idManga', 'idUsuario', 'updated_at'];
    }

    public static function getStats(Int $idUsuario)
    {
        // $tuplas = MangaUsuario::select("
        //     SELECT SUM(capitulos) totalCapitulos, SUM(volumenes) totalVolumenes, COUNT(idManga) totalMangas 
        //     FROM `manga_usuario` 
        //     WHERE `idUsuari` = " . $idUsuario . "
        // ");

        // $tuplas = [];

        // $tuplas["totalCapitulosLeidos"] = MangaUsuario::where('idUsuario', $idUsuario)->sum('capitulos');
        // $tuplas["totalVolumenesLeidos"] = MangaUsuario::where('idUsuario', $idUsuario)->sum('volumenes');
        // $tuplas["totalMangas"] = MangaUsuario::where('idUsuario', $idUsuario)->count('idManga');
        // $tuplas["totalMangasPorLeer"] = MangaUsuario::where('idUsuario', $idUsuario)->where('idEstado', 3)->count('idManga');
        // $tuplas["avgNota"] = MangaUsuario::where('idUsuario', $idUsuario)->where('nota', '!=', null)->avg('nota');
        // $tuplas["porEstado"] = MangaUsuario::selectRaw('manga_usuario.idEstado id,estado_manga_usuario.estado label, COUNT(*) value')
        //     ->join('estado_manga_usuario', 'manga_usuario.idEstado', '=', 'estado_manga_usuario.idEstado')
        //     ->where('manga_usuario.idUsuario', $idUsuario)
        //     ->groupBy('manga_usuario.idEstado')
        //     ->get();

        // $tuplas["lastMangaEntries"] = MangaUsuario::select(
        //     'manga.id',
        //     'manga.tituloPreferido',
        //     'manga.foto',
        //     'manga.capitulos AS totalCapitulosManga',
        //     'manga.volumenes AS totalVolumenesManga',
        //     'estado_manga_usuario.idEstado',
        //     'estado_manga_usuario.estado',
        //     'manga_usuario.nota',
        //     'manga_usuario.volumenes',
        //     'manga_usuario.capitulos',
        //     'manga_usuario.updated_at',
        // )
        //     ->join('estado_manga_usuario', 'manga_usuario.idEstado', '=', 'estado_manga_usuario.idEstado')
        //     ->join('manga', 'manga_usuario.idManga', '=', 'manga.id')
        //     ->where('manga_usuario.idUsuario', $idUsuario)
        //     ->orderBy('updated_at', 'DESC')
        //     ->limit(3)
        //     ->get();

        // $tuplas['calendar'] = ActividadUsuario::selectRaw('DATE(updated_at) AS day, COUNT(*) AS value')->where('idUsuario', $idUsuario)->groupByRaw('DATE(updated_at)')->get();

        // return $tuplas;

        DB::statement("call estadisticas_usuario(:idUsuario, @estadisticas)", [ "idUsuario" => $idUsuario ]);
        $resultado = DB::select("select @estadisticas AS stats");

        return json_decode($resultado[0]->stats);
    }

    public static function getFav(Int $idUsuario)
    {
        $mangas_favoritos = [];
        $tuplas = MangaUsuario::where('idUsuario', '=', $idUsuario)->where('favorito', '=', 1)->get(MangaUsuario::getColumns());

        foreach ($tuplas as $tupla) {
            array_push($mangas_favoritos, Manga::where('id', $tupla->idManga)->get(Manga::getSmallColumns())->first());
        }

        return $mangas_favoritos;
    }

    // public function getFav(Int $idManga, Int $idUsuario)
    // {
    //     return $this::select($this::getColumns())
    //         ->where('idManga', '=', $idManga)
    //         ->where('idUsuario', '=', $idUsuario)
    //         ->get();
    // }
}
