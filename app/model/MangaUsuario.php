<?php

namespace App\Model;

class MangaUsuario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'manga_usuario';

    public static function getStats(Int $idUsuario)
    {
        $stats = [];
        $stats["totalCapitulosLeidos"] = MangaUsuario::where('idUsuario', $idUsuario)->sum('capitulos');
        $stats["totalVolumenesLeidos"] = MangaUsuario::where('idUsuario', $idUsuario)->sum('volumenes');
        $stats["totalMangas"] = MangaUsuario::where('idUsuario', $idUsuario)->count('idManga');
        $stats["totalMangasPorLeer"] = MangaUsuario::where('idUsuario', $idUsuario)->where('idEstado', 3)->count('idManga');
        $stats["avgNota"] = MangaUsuario::where('idUsuario', $idUsuario)->where('nota', '!=', null)->avg('nota');
        $stats["porEstado"] = MangaUsuario::selectRaw('manga_usuario.idEstado id,estado_manga_usuario.estado label, COUNT(*) value')
            ->join('estado_manga_usuario', 'manga_usuario.idEstado', '=', 'estado_manga_usuario.idEstado')
            ->where('manga_usuario.idUsuario', $idUsuario)
            ->groupBy('manga_usuario.idEstado')
            ->get();
        $stats["lastMangaEntries"] = MangaUsuario::select(
            'manga.id',
            'manga.tituloPreferido',
            'manga.foto',
            'manga.capitulos AS totalCapitulosManga',
            'manga.volumenes AS totalVolumenesManga',
            'estado_manga_usuario.idEstado',
            'estado_manga_usuario.estado',
            'manga_usuario.nota',
            'manga_usuario.volumenes',
            'manga_usuario.capitulos',
            'manga_usuario.updated_at',
        )
            ->join('estado_manga_usuario', 'manga_usuario.idEstado', '=', 'estado_manga_usuario.idEstado')
            ->join('manga', 'manga_usuario.idManga', '=', 'manga.id')
            ->where('manga_usuario.idUsuario', $idUsuario)
            ->orderBy('updated_at', 'DESC')
            ->limit(3)
            ->get();
        $stats['calendar'] = ActividadUsuario::selectRaw('DATE(updated_at) AS day, COUNT(*) AS value')->where('idUsuario', $idUsuario)->groupByRaw('DATE(updated_at)')->get();
        $stats['porNota'] = MangaUsuario::selectRaw('nota, COUNT(*) AS value')->where('idUsuario', $idUsuario)->groupBy('nota')->orderBy('nota')->get();
        $stats['porA??o'] = MangaUsuario::selectRaw('`manga`.`a??oDePublicacion` AS x, COUNT(*) AS y')
            ->where('idUsuario', $idUsuario)
            ->join('manga', 'manga_usuario.idManga', '=', 'manga.id')
            ->groupBy('manga.a??oDePublicacion')
            ->orderBy('manga.a??oDePublicacion')
            ->get();
        $stats['porDemografia'] = MangaUsuario::selectRaw('`manga`.`idDemografia` AS id, `demografia`.`demografia` AS label, COUNT(*) AS value')
            ->where('idUsuario', $idUsuario)
            ->join('manga', 'manga_usuario.idManga', '=', 'manga.id')
            ->join('demografia', 'manga.idDemografia', '=', 'demografia.idDemografia')
            ->groupBy('manga.idDemografia')
            ->orderBy('manga.idDemografia')
            ->get();
        return $stats;
    }
}
